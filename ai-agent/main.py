import os
from fastapi import FastAPI, Request
from services.openai_service import get_ai_response, clear_memory
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
        from services.db_service import get_department_settings, get_or_create_customer, update_customer_nickname
        dept_settings = get_department_settings(department_id)
        
        if not dept_settings:
            return {"status": "error", "message": "Department not found"}
            
        owner_id = dept_settings.get('user_id')
        
        # 2. Ambil/Buat data customer (Isolasi per owner_id)
        customer = get_or_create_customer(owner_id, customer_id, pushname)
        
        # --- FITUR HOLD / HUMAN TAKEOVER ---
        is_held_by_label = data.get("is_held_by_label", False)
        is_muted_by_dashboard = customer.get('is_muted', False) if customer else False

        if is_held_by_label or is_muted_by_dashboard:
            reason = "Label WA" if is_held_by_label else "Dashboard"
            print(f"[DEBUG] Customer {customer_id} sedang di-HOLD via {reason}. AI tidak menjawab.")
            return {"status": "held"}
        # ------------------------------------------------
        from services.whatsapp_service import send_whatsapp_message, send_typing_indicator

        print(f"[Dept: {department_id}] Pesan dari {customer_id}: {msg_body}")
        
        # Munculkan status "Mengetik" di WA (Hanya jika tidak mute)
        await send_typing_indicator(reply_to, department_id, gateway_port)

        # Perintah khusus
        if msg_body.lower() == "/reset":
            clear_memory(customer_id, department_id)
            await send_whatsapp_message(reply_to, "Ingatan chat departemen ini telah dihapus.", department_id, gateway_port, message_id)
            return {"status": "cleared"}
        
        # Ambil balasan AI (Kirim data customer juga)
        ai_reply = get_ai_response(customer_id, department_id, msg_body, customer=customer)
        
        # 3. Cek apakah AI ingin mengupdate nama user
        if "[[SET_NAME:" in ai_reply:
            import re
            match = re.search(r"\[\[SET_NAME:\s*(.*?)\]\]", ai_reply)
            if match:
                new_name = match.group(1).strip()
                update_customer_nickname(owner_id, customer_id, new_name)
                ai_reply = ai_reply.replace(match.group(0), "").strip()

        # Kirim balik
        result = await send_whatsapp_message(reply_to, ai_reply, department_id, gateway_port, message_id)
        
        if result and result.get("status") == "success":
            return {"status": "success", "ai_reply": ai_reply}
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
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
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

if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8000))
    uvicorn.run(app, host="127.0.0.1", port=port)
