import os
import json
from datetime import datetime
from openai import OpenAI
from services.knowledge_service import get_relevant_info
from services.db_service import get_department_settings, get_manual_answer, log_unanswered_question
from dotenv import load_dotenv

load_dotenv()
if not os.getenv("OPENAI_API_KEY"):
    load_dotenv(os.path.join(os.path.dirname(__file__), "../../.env"))

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

MEMORY_DIR = "memory"

def is_within_operational_hours(dept_settings):
    """Cek apakah saat ini masuk jam operasional departemen."""
    if not dept_settings:
        print("[DEBUG] No dept settings found, defaulting to open.")
        return True, ""
        
    if dept_settings.get('is_24_hours'):
        print(f"[DEBUG] Dept {dept_settings.get('id')} is set to 24 hours.")
        return True, ""
    
    try:
        now = datetime.now().time()
        open_val = dept_settings.get('open_time')
        close_val = dept_settings.get('close_time')
        
        if open_val is None or close_val is None:
            return True, ""
            
        # Konversi ke string dan bersihkan (MySQL time bisa berupa timedelta atau string)
        def format_time_str(val):
            s = str(val).strip()
            if 'day' in s: s = s.split(',')[-1].strip() # Handle timedelta string
            parts = s.split(':')
            if len(parts) >= 2:
                # Pastikan format HH:MM
                return f"{parts[0].zfill(2)}:{parts[1].zfill(2)}"
            return None

        open_str = format_time_str(open_val)
        close_str = format_time_str(close_val)
        
        if not open_str or not close_str:
            return True, ""
            
        open_time = datetime.strptime(open_str, "%H:%M").time()
        close_time = datetime.strptime(close_str, "%H:%M").time()
        
        is_open = False
        if open_time <= close_time:
            is_open = open_time <= now <= close_time
        else: # Shift malam
            is_open = now >= open_time or now <= close_time
            
        if not is_open:
            return False, f"Maaf, saat ini kami sedang di luar jam operasional (Buka: {open_str} - {close_str}). Pesan Anda telah kami terima dan akan dijawab secepatnya saat kami kembali aktif. Terima kasih!"
            
        return True, ""
    except Exception as e:
        print(f"[ERROR] Cek jam operasional gagal: {e}")
        return True, ""

def get_memory_path(department_id: str, customer_id: str):
    dept_dir = os.path.join(MEMORY_DIR, f"dept_{department_id}")
    if not os.path.exists(dept_dir):
        os.makedirs(dept_dir)
    return os.path.join(dept_dir, f"{customer_id}.json")

def load_session_memory(department_id: str, customer_id: str):
    path = get_memory_path(department_id, customer_id)
    if os.path.exists(path):
        try:
            with open(path, "r") as f:
                return json.load(f)
        except:
            return []
    return []

def save_session_memory(department_id: str, customer_id: str, history: list):
    path = get_memory_path(department_id, customer_id)
    # Simpan hanya 20 pesan terakhir untuk menjaga performa
    with open(path, "w") as f:
        json.dump(history[-20:], f)

def get_ai_response(customer_id: str, department_id: str, user_message: str, system_prompt: str = "You are a helpful AI assistant.", customer: dict = None) -> str:
    """
    Mengambil balasan AI dengan memisahkan riwayat per customer dan pengetahuan per departemen.
    """
    try:
        user_message = user_message.strip()
        # 1. Ambil Pengaturan Departemen TERBARU dari DB (Tanpa Cache)
        dept_settings = get_department_settings(department_id)
        
        # --- CEK JAM OPERASIONAL ---
        is_open, closed_message = is_within_operational_hours(dept_settings)
        if not is_open:
            print(f"[DEBUG] Dept {department_id} sedang di luar jam operasional. Mengirim pesan penutup.")
            return closed_message
        # ----------------------------
        
        # Rakit System Prompt Dinamis
        ai_name = "AI Agent"
        ai_job = "You are a helpful AI assistant."
        
        if dept_settings:
            ai_name = dept_settings.get('ai_name') or "AI Agent"
            ai_job = dept_settings.get('ai_job_description') or "You are a helpful AI assistant."

        # Ambil identitas penanya
        customer_name = "User"
        if customer:
            customer_name = customer.get('nickname') or customer.get('name') or "User"
        
        full_system_prompt = (
            f"Nama Anda adalah {ai_name}. {ai_job}\n\n"
            f"Anda sedang berbicara dengan: {customer_name}.\n"
            "ATURAN PENTING:\n"
            "1. Jawablah pertanyaan HANYA berdasarkan 'Informasi Tambahan' yang disediakan jika ada.\n"
            "2. Jika jawaban tidak ditemukan dalam informasi tersebut, katakan bahwa Anda belum memiliki informasi tersebut dan sarankan untuk menghubungi admin.\n"
            "3. JANGAN mengarang informasi (halusinasi).\n"
            "4. Gunakan bahasa yang sopan, profesional, dan ramah.\n"
            "5. Jika penanya memperkenalkan diri atau ingin dipanggil dengan nama tertentu, balas dengan ramah DAN sertakan tag [[SET_NAME: NamaBaru]] di akhir pesan Anda agar sistem bisa mengingatnya."
        )

        # 2. Load Riwayat Chat Spesifik Sesi
        history = load_session_memory(department_id, customer_id)
        
        # 3. CARI JAWABAN MANUAL (Yang sudah diinput admin)
        manual_answer = get_manual_answer(department_id, user_message)
        
        # 4. CARI DI KNOWLEDGE BASE (RAG)
        context = get_relevant_info(department_id, user_message)
        
        # Tambahkan Jawaban Manual ke Konteks jika ada
        manual_context = f"\n\nJAWABAN WAJIB (Gunakan ini!):\n{manual_answer}" if manual_answer else ""
        
        # Template pesan pembuka & fallback
        introduction = f"Halo! Saya {ai_name}." if ai_name else "Halo! Saya asisten AI Anda."
        fallback_reply = (
            f"{introduction}\n\n"
            "Mohon maaf, saat ini saya belum memiliki informasi yang cukup mengenai hal tersebut di pusat data kami. "
            "Saya sedang dalam tahap belajar untuk memberikan informasi yang lebih akurat terkait layanan kami.\n\n"
            "Silakan tanyakan hal lain, atau Anda bisa menghubungi admin kami untuk bantuan lebih lanjut. Terima kasih!"
        )
        
        # 5. PANGGIL OPENAI UNTUK ANALISIS & JAWABAN
        rag_prompt = f"\n\nInformasi Tambahan:\n{context}" if context else ""
        
        smart_instructions = (
            "\n\nKONTROL KONTEKS DAN LOGGING:\n"
            f"1. Jika user menyapa: Balas dengan ramah (Nama Anda: {ai_name}).\n"
            "2. Jika ada 'JAWABAN WAJIB' di atas: Gunakan informasi tersebut secara mutlak.\n"
            "3. Jika user bertanya tentang informasi (biaya, syarat, link, dll) dan jawabannya TIDAK ADA di 'Informasi Tambahan' maupun 'JAWABAN WAJIB': "
            "Anda WAJIB memberikan jawaban yang sopan namun WAJIB menyertakan tag [[TIDAK_TAHU]] di dalam jawaban Anda. "
            "Sangat penting: Jika Anda tidak tahu, Anda HARUS menyertakan [[TIDAK_TAHU]] agar admin bisa membantu menjawab nanti.\n"
            "4. Jika user memberikan nama, gunakan tag [[SET_NAME: Nama]] di akhir jawaban Anda."
        )

        current_messages = [
            {"role": "system", "content": full_system_prompt + manual_context + rag_prompt + smart_instructions}
        ]
        current_messages.extend(history)
        current_messages.append({"role": "user", "content": user_message})

        response = client.chat.completions.create(
            model=os.getenv("OPENAI_MODEL", "gpt-4o"),
            messages=current_messages,
            temperature=0.7
        )
        
        ai_reply = response.choices[0].message.content
        
        # 6. LOG PEMAKAIAN (Untuk Statistik & Biaya)
        try:
            model_used = response.model
            prompt_tokens = response.usage.prompt_tokens
            completion_tokens = response.usage.completion_tokens
            print(f"[DEBUG] Logging AI Response: {prompt_tokens} + {completion_tokens} tokens using {model_used}")
            from services.db_service import log_ai_response
            success = log_ai_response(department_id, customer_id, user_message, ai_reply, model_used, prompt_tokens, completion_tokens)
            if success:
                print("[DEBUG] Log AI berhasil disimpan ke database.")
            else:
                print("[DEBUG] Log AI GAGAL disimpan ke database.")
        except Exception as log_err:
            print(f"[ERROR] Gagal mencatat log pemakaian: {log_err}")

        # 7. EVALUASI JAWABAN AI (Logging jika tidak tahu)
        # Daftar kalimat kunci yang menandakan AI tidak tahu (Safety Net)
        dont_know_keywords = [
            "[[TIDAK_TAHU]]",
            "belum memiliki informasi",
            "tidak memiliki informasi",
            "hubungi admin kami",
            "dalam tahap belajar",
            "pusat data kami",
            "maaf, saya belum bisa membantu"
        ]

        is_unknown = any(kw.lower() in ai_reply.lower() for kw in dont_know_keywords)

        if is_unknown:
            print(f"[DEBUG] Sistem mendeteksi jawaban 'Tidak Tahu'. Log pertanyaan: {user_message}")
            log_unanswered_question(department_id, customer_id, user_message)
            
            # Jika isinya cuma tag, gunakan fallback. Jika ada kalimat lain, cukup hapus tag-nya.
            if ai_reply.strip().upper() == "[[TIDAK_TAHU]]":
                ai_reply = fallback_reply
            else:
                import re
                ai_reply = re.sub(r"\[\[TIDAK_TAHU\]\]", "", ai_reply, flags=re.IGNORECASE).strip()
        
        # Simpan ke riwayat & return
        history.append({"role": "user", "content": user_message})
        history.append({"role": "assistant", "content": ai_reply})
        save_session_memory(department_id, customer_id, history)
        
        return ai_reply
        
    except Exception as e:
        print(f"Error OpenAI: {e}")
        return "Maaf, terjadi kesalahan teknis."

def clear_memory(customer_id: str, department_id: str):
    path = get_memory_path(department_id, customer_id)
    if os.path.exists(path):
        os.remove(path)
