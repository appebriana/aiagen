import os
import re
from fastapi import FastAPI, Request
from services.openai_service import get_ai_response, clear_memory, load_session_memory, generate_session_summary
from services.whatsapp_service import send_whatsapp_message
from dotenv import load_dotenv

load_dotenv()
# Jika API Key tidak ada di folder ai-agent, coba ambil dari root folder Laravel
if not os.getenv("OPENAI_API_KEY"):
    load_dotenv(os.path.join(os.path.dirname(__file__), "../.env"))

app = FastAPI(title="AIAGEN - Department-Based AI Agent")

@app.get("/")
def read_root():
    return {"status": "AI Agent Active", "mode": "Department-Based RAG"}

@app.post("/webhook")
async def handle_webhook(request: Request):
    data = await request.json()
    
    # customer_id: nomor WA pelanggan
    # department_id: ID departemen yang menangani chat ini
    sender = data.get("sender") or data.get("from")
    author = data.get("author") or sender
    
    # customer_id adalah identitas unik individu untuk memori
    customer_id = author
    # reply_to adalah tujuan kirim pesan (Grup atau Individu)
    reply_to = sender

    msg_body = data.get("message") or data.get("body")
    department_id = data.get("department_id", "default")
    gateway_port = data.get("gateway_port")
    pushname = data.get("pushname")
    message_id = data.get("message_id")
    
    if customer_id and msg_body:
        # 1. Ambil Pengaturan & Pemilik Departemen
        from services.db_service import get_department_settings, get_or_create_customer, update_customer_nickname, log_ai_response, update_ai_response
        dept_settings = get_department_settings(department_id)
        
        if not dept_settings:
            return {"status": "error", "message": "Department not found"}
            
        owner_id = dept_settings.get('user_id')
        
        # 2. Ambil/Buat data customer (Isolasi per owner_id)
        customer = get_or_create_customer(owner_id, customer_id, pushname)
        
        # --- LOG AWAL AGAR LANGSUNG MUNCUL DI CMS ---
        # Gunakan reply_to/sender agar chat grup ter-grouping dengan benar
        log_id = log_ai_response(department_id, reply_to, msg_body, "", "WAITING", 0, 0)
        
        # --- FITUR LOGGING CMS UNTUK SEMUA PESAN ---
        # Ambil flag is_triggered dari gateway (default True untuk PC, False untuk Grup tanpa trigger)
        is_triggered = data.get("is_triggered", True)

        # --- FITUR HOLD / HUMAN TAKEOVER ---
        is_held_by_label = data.get("is_held_by_label", False)
        # Gunakan field is_ai_enabled (default True jika tidak ada)
        is_ai_enabled = customer.get('is_ai_enabled', True) if customer else True

        if is_held_by_label or not is_ai_enabled or not is_triggered:
            reason = "Not Triggered"
            if is_held_by_label: reason = "Label WA"
            elif not is_ai_enabled: reason = "Dashboard (Takeover)"
            
            print(f"[DEBUG] Message from {customer_id} in {reply_to}: {reason}. AI tidak menjawab.")
            
            # Update log awal tadi menjadi status LOG_ONLY
            update_ai_response(log_id, "", reason, 0, 0)
            
            return {"status": "logged_only", "reason": reason}
        # ------------------------------------------------
        from services.whatsapp_service import send_whatsapp_message, send_typing_indicator, stop_typing_indicator

        # Munculkan status "Mengetik" di WA (Hanya jika tidak mute)
        await send_typing_indicator(reply_to, department_id, gateway_port)

        # ------------------------------------------------
        # FITUR RATING (CSAT) & RESOLUTION CHECK
        # ------------------------------------------------
        is_csat_enabled = dept_settings.get('is_csat_enabled', True)
        clean_msg = msg_body.strip().upper()
        
        if is_csat_enabled:
            # 1. Handle YA / TIDAK (Resolution Check)
            if clean_msg in ["YA", "TIDAK"]:
                from services.db_service import update_last_resolved
                is_resolved = (clean_msg == "YA")
                # Gunakan reply_to agar update pada conversation yang benar
                success = update_last_resolved(department_id, reply_to, is_resolved)
                if success:
                    if is_resolved:
                        next_msg = "Alhamdulillah, senang bisa membantu! 😊\n\nTerakhir, mohon kesediaannya memberikan rating layanan saya dengan membalas angka 1 (Buruk) s/d 5 (Sangat Puas) ya!"
                    else:
                        next_msg = "Mohon maaf jika jawaban saya belum memuaskan. 🙏 Kami akan terus belajar.\n\nTetap mohon bantuannya untuk memberikan rating 1-5 agar kami bisa melakukan evaluasi."
                    await send_whatsapp_message(reply_to, next_msg, department_id, gateway_port, message_id)
                    
                    # Update log awal tadi dengan jawaban CSAT
                    update_ai_response(log_id, next_msg, "SYSTEM_CSAT", 0, 0)
                    return {"status": "resolution_saved"}

            # 2. Handle 1-5 (Rating)
            if msg_body.strip() in ["1", "2", "3", "4", "5"]:
                from services.db_service import update_last_rating
                success = update_last_rating(department_id, reply_to, int(msg_body.strip()))
                if success:
                    thanks_msg = "Terima kasih banyak atas penilaiannya! Masukan Kakak sangat berarti bagi kami. 🙏✨"
                    await send_whatsapp_message(reply_to, thanks_msg, department_id, gateway_port, message_id)
                    
                    # Update log awal tadi dengan jawaban Rating
                    update_ai_response(log_id, thanks_msg, "SYSTEM_RATING", 0, 0)

                    # ------------------------------------------------
                    # Generate Summary Konteks (Summary dari Awal Sesi)
                    try:
                        chat_history = load_session_memory(department_id, customer_id)
                        if chat_history:
                            summary = generate_session_summary(chat_history)
                            if summary:
                                from services.db_service import update_last_summary
                                update_last_summary(department_id, reply_to, summary)
                    except Exception as e:
                        print(f"[ERROR] Gagal proses summary: {e}")
                    # ------------------------------------------------

                    return {"status": "rating_saved"}
        # ------------------------------------------------

        # Perintah khusus
        if msg_body.lower() == "/reset":
            clear_memory(customer_id, department_id)
            answer_reset = "Memori percakapan Anda telah dibersihkan."
            await send_whatsapp_message(reply_to, answer_reset, department_id, gateway_port, message_id)
            update_ai_response(log_id, answer_reset, "SYSTEM_RESET", 0, 0)
            return {"status": "reset"}

        # Ambil respon dari AI
        answer, p_tokens, c_tokens, sentiment = get_ai_response(customer_id, department_id, msg_body, customer=customer, is_csat_enabled=is_csat_enabled)

        # Jika AI di-takeover (answer=None), jangan kirim apa-apa
        if answer is None:
            await stop_typing_indicator(reply_to, department_id, gateway_port)
            update_ai_response(log_id, "", "AI_DISABLED_MID_PROCESS", 0, 0)
            return {"status": "ai_disabled"}

        # 3. Cek apakah AI ingin mengupdate nama user
        if "[[SET_NAME:" in answer:
            match = re.search(r"\[\[SET_NAME:\s*(.*?)\]\]", answer)
            if match:
                new_name = match.group(1).strip()
                update_customer_nickname(owner_id, customer_id, new_name)
                answer = re.sub(r"\[\[SET_NAME:.*?\]\]", "", answer).strip()

        # Kirim Balasan ke WhatsApp
        result = await send_whatsapp_message(reply_to, answer, department_id, gateway_port, message_id)

        # Update Log yang sudah ada (Termasuk Token & Sentiment)
        update_ai_response(log_id, answer, os.getenv("OPENAI_MODEL", "gpt-4o-mini"), p_tokens, c_tokens, sentiment)
        
        if result and result.get("status") == "success":
            return {"status": "success", "ai_reply": answer}
        else:
            return {"status": "failed_to_send_to_gateway", "error": str(result)}
        
    return {"status": "ignored"}

@app.get("/settings/{department_id}")
async def get_settings(department_id: str):
    from services.db_service import get_department_settings
    settings = get_department_settings(department_id)
    return settings

@app.post("/update-status/{department_id}")
async def update_status(department_id: str, data: dict):
    from services.db_service import get_db_connection
    status = data.get("status", "disconnected")
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        # Update status di tabel whatsapp_devices berdasarkan department_id
        cursor.execute(
            "UPDATE whatsapp_devices SET status = %s, updated_at = NOW() WHERE department_id = %s",
            (status, department_id)
        )
        conn.commit()
        cursor.close()
        conn.close()
        return {"status": "success"}
    except Exception as e:
        print(f"Gagal update status di DB: {e}")
        return {"status": "error", "message": str(e)}

@app.post("/update-device-status/{device_id}")
async def update_device_status(device_id: str, data: dict):
    """Update status untuk SATU device spesifik berdasarkan ID perangkat."""
    from services.db_service import get_db_connection
    status = data.get("status", "disconnected")
    phone_number = data.get("phone_number")
    
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Jika sedang mencoba connect, cek apakah nomor ini sudah dipakai device LAIN
        if status == 'connected' and phone_number:
            cursor.execute(
                "SELECT id, name FROM whatsapp_devices WHERE phone_number = %s AND status = 'connected' AND id != %s",
                (phone_number, device_id)
            )
            existing = cursor.fetchone()
            
            if existing:
                print(f"[ERROR] Nomor {phone_number} sudah digunakan oleh device: {existing['name']} (ID: {existing['id']})")
                cursor.close()
                conn.close()
                return {
                    "status": "error", 
                    "message": f"Nomor ini sudah terhubung di perangkat lain ({existing['name']}). Satu nomor hanya boleh untuk satu perangkat.",
                    "code": "duplicate_number"
                }

        # Update status dan nomor HP di tabel whatsapp_devices
        if phone_number:
            cursor.execute(
                "UPDATE whatsapp_devices SET status = %s, phone_number = %s, updated_at = NOW() WHERE id = %s",
                (status, phone_number, device_id)
            )
        else:
            cursor.execute(
                "UPDATE whatsapp_devices SET status = %s, updated_at = NOW() WHERE id = %s",
                (status, device_id)
            )
            
        conn.commit()
        cursor.close()
        conn.close()
        return {"status": "success"}
    except Exception as e:
        print(f"Gagal update device status di DB: {e}")
        return {"status": "error", "message": str(e)}

@app.post("/suggest")
async def suggest_answer(request: Request):
    """Memberikan saran jawaban untuk admin berdasarkan Knowledge Base."""
    data = await request.json()
    question = data.get("question")
    department_id = data.get("department_id")
    
    if not question or not department_id:
        return {"status": "error", "message": "Missing question or department_id"}
        
    try:
        from services.openai_service import client, get_relevant_info, get_department_settings, get_manual_answers
        from services.db_service import log_ai_response
        
        # 1. Ambil Pengaturan Departemen
        dept_settings = get_department_settings(department_id)
        ai_name = dept_settings.get('ai_name') or "AI Agent"
        ai_job = dept_settings.get('ai_job_description') or "You are a helpful AI assistant."
        
        # 2. Ambil Konteks (KB + Manual Answers)
        kb_context = get_relevant_info(department_id, question)
        manual_context = get_manual_answers(department_id)
        full_context = manual_context + "\n" + (kb_context if kb_context else "")
        
        # 3. Panggil OpenAI
        system_prompt = (
            f"Nama Anda adalah {ai_name}. {ai_job}\n"
            "Tugas Anda saat ini adalah memberikan SARAN JAWABAN kepada ADMIN untuk membalas pertanyaan pelanggan.\n"
            "Gunakan informasi berikut sebagai referensi:\n"
            f"{full_context}\n\n"
            "ATURAN:\n"
            "1. Berikan jawaban yang siap kirim, ramah, dan profesional.\n"
            "2. Jika informasi tidak ditemukan, berikan draf: 'Mohon maaf, saya belum memiliki informasi mengenai hal tersebut. Akan saya tanyakan ke tim terkait.'\n"
            "3. JANGAN menyertakan tag internal seperti [[TIDAK_TAHU]] atau [[SET_NAME]]."
        )
        
        response = client.chat.completions.create(
            model=os.getenv("OPENAI_MODEL", "gpt-4o-mini"),
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": question}
            ],
            temperature=0.7
        )
        
        suggestion = response.choices[0].message.content
        
        # 4. Log Pemakaian (Accumulate to stats)
        model_used = response.model
        prompt_tokens = response.usage.prompt_tokens
        completion_tokens = response.usage.completion_tokens
        
        log_ai_response(
            department_id=department_id,
            customer_phone="ADMIN_SUGGESTION", # Penanda bahwa ini adalah penggunaan via fitur saran
            question=f"[SUGGESTION] {question}",
            answer=suggestion,
            model=model_used,
            prompt_tokens=prompt_tokens,
            completion_tokens=completion_tokens
        )
        
        return {
            "status": "success",
            "suggestion": suggestion,
            "usage": {
                "total_tokens": prompt_tokens + completion_tokens
            }
        }
    except Exception as e:
        print(f"Error in /suggest: {e}")
        return {"status": "error", "message": str(e)}

if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8000))
    uvicorn.run(app, host="127.0.0.1", port=port)
