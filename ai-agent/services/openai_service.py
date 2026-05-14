import os
import re
import json
import requests
import csv
from io import StringIO
from datetime import datetime
from openai import OpenAI
from services.knowledge_service import get_relevant_info
from services.db_service import get_department_settings, get_manual_answers, log_unanswered_question, get_gsheet_knowledge_urls, get_customer_ai_status, set_customer_ai_status
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

def get_tone_instructions(tone: str) -> str:
    """Mengembalikan instruksi spesifik berdasarkan gaya bicara yang dipilih."""
    if tone == 'formal':
        return (
            "GAYA BICARA: FORMAL & RESMI\n"
            "1. Gunakan panggilan 'Bapak/Ibu' atau 'Anda'.\n"
            "2. Gunakan bahasa Indonesia yang baku sesuai PUEBI.\n"
            "3. Hindari penggunaan emoji sama sekali.\n"
            "4. Kalimat harus lengkap dan sopan (Contoh: 'Baik Bapak, akan kami proses segera')."
        )
    elif tone == 'technical':
        return (
            "GAYA BICARA: TEKNIS & PADAT\n"
            "1. Jawab langsung ke inti (to-the-point) tanpa basa-basi.\n"
            "2. Fokus pada data, fakta, dan instruksi langkah-demi-langkah.\n"
            "3. Gunakan istilah teknis yang akurat.\n"
            "4. Minimalkan kata-kata pemanis, fokus pada efisiensi informasi."
        )
    else: # Default casual
        return (
            "GAYA BICARA: CASUAL & RAMAH\n"
            "1. Gunakan panggilan 'Kak' atau 'Sobat'.\n"
            "2. Gunakan bahasa yang santai, mengalir, dan akrab.\n"
            "3. Gunakan emoji secara wajar untuk menunjukkan keramahan (😊, ✨, 🙏).\n"
            "4. Buat user merasa seperti bicara dengan teman yang membantu."
        )

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

def get_google_sheet_info(urls: list) -> str:
    """Mengambil data dari daftar Google Sheets (Public CSV) untuk dijadikan pengetahuan AI."""
    if not urls:
        return ""
        
    full_context = "BERIKUT ADALAH DATA TERBARU DARI GOOGLE SPREADSHEET (Prioritaskan ini):\n"
    has_data = False
    
    for url in urls:
        if not url or "docs.google.com/spreadsheets" not in url:
            continue
            
        try:
            # Konversi URL Google Sheets biasa ke URL Export CSV
            if "/export" not in url:
                sheet_id = url.split("/d/")[1].split("/")[0]
                export_url = f"https://docs.google.com/spreadsheets/d/{sheet_id}/export?format=csv"
            else:
                export_url = url
                
            print(f"[DEBUG] Fetching Google Sheet from: {export_url}")
            response = requests.get(export_url, timeout=10)
            response.raise_for_status()
            
            # Parse CSV
            f = StringIO(response.text)
            reader = csv.reader(f)
            data = list(reader)
            
            if data:
                has_data = True
                full_context += f"\nSUMBER: {url}\n"
                for i, row in enumerate(data):
                    if any(row): # Skip baris kosong
                        full_context += f"- {' | '.join(row)}\n"
            
        except Exception as e:
            print(f"[ERROR] Gagal mengambil data Google Sheet ({url}): {e}")
            
    return full_context + "\n" if has_data else ""

def check_human_takeover_request(message: str) -> bool:
    """Mendeteksi apakah user ingin berbicara dengan manusia/admin."""
    keywords = [
        "admin", "customer service", "cs", "operator", "manusia", "orang", 
        "hubungi admin", "bicara dengan admin", "panggil admin", "bantuan admin",
        "live chat", "takeover", "manual"
    ]
    message_lower = message.lower()
    for kw in keywords:
        if kw in message_lower:
            return True
    return False

def get_ai_response(customer_id: str, department_id: str, user_message: str, system_prompt: str = "You are a helpful AI assistant.", customer: dict = None, is_csat_enabled: bool = True) -> str:
    """
    Mengambil balasan AI dengan memisahkan riwayat per customer dan pengetahuan per departemen.
    """
    try:
        user_message = user_message.strip()
        # 0. CEK STATUS HUMAN TAKEOVER (Jika AI dimatikan untuk user ini)
        dept_settings = get_department_settings(department_id)
        if not dept_settings:
            return "Department not found.", 0, 0, None
            
        user_id = dept_settings.get('user_id')
        customer_phone = customer.get('phone') if customer else customer_id
        
        is_ai_enabled = get_customer_ai_status(user_id, customer_phone)
        if not is_ai_enabled:
            print(f"[TAKEOVER] AI is disabled for {customer_phone}. Skipping response.")
            return None, 0, 0, None # Return None agar agent tidak mengirim pesan apapun

        # 0.1 CEK APAKAH USER MINTA BICARA DENGAN MANUSIA
        if check_human_takeover_request(user_message):
            print(f"[TAKEOVER] Human takeover request detected from {customer_phone}")
            set_customer_ai_status(user_id, customer_phone, False)
            return "Baik, saya akan menghubungkan Anda dengan tim Admin kami. Mohon tunggu sebentar, Admin akan segera membalas pesan Anda. 🙏", 0, 0, None

        # --- CEK JAM OPERASIONAL ---
        is_open, closed_message = is_within_operational_hours(dept_settings)
        if not is_open:
            print(f"[DEBUG] Dept {department_id} sedang di luar jam operasional. Mengirim pesan penutup.")
            return closed_message, 0, 0, None
        # ----------------------------
        
        # Rakit System Prompt Dinamis
        ai_name = "AI Agent"
        ai_job = "You are a helpful AI assistant."
        
        if dept_settings:
            ai_name = dept_settings.get('ai_name') or "AI Agent"
            ai_job = dept_settings.get('ai_job_description') or "You are a helpful AI assistant."
            tone = dept_settings.get('tone_of_voice') or 'casual'
            tone_instructions = get_tone_instructions(tone)
        else:
            tone_instructions = get_tone_instructions('casual')

        # Ambil identitas penanya
        customer_name = "User"
        if customer:
            customer_name = customer.get('nickname') or customer.get('name') or "User"
        
        full_system_prompt = (
            f"Nama Anda adalah {ai_name}. {ai_job}\n\n"
            f"Anda sedang berbicara dengan: {customer_name}.\n"
            f"{tone_instructions}\n\n"
            "ATURAN PENTING:\n"
            "1. Jawablah pertanyaan HANYA berdasarkan 'Informasi Tambahan' yang disediakan jika ada.\n"
            "2. Jika jawaban tidak ditemukan dalam informasi tersebut, katakan bahwa Anda belum memiliki informasi tersebut dan sarankan untuk menghubungi admin.\n"
            "3. JANGAN mengarang informasi (halusinasi).\n"
            "4. Jika penanya memperkenalkan diri atau ingin dipanggil dengan nama tertentu, balas dengan ramah DAN sertakan tag [[SET_NAME: NamaBaru]] di akhir pesan Anda agar sistem bisa mengingatnya."
        )

        # 2. Load Riwayat Chat Spesifik Sesi
        history = load_session_memory(department_id, customer_id)
        
        # 3. CARI JAWABAN MANUAL (Yang sudah diinput admin)
        manual_answers_context = get_manual_answers(department_id)
        
        # 4. CARI DI KNOWLEDGE BASE (RAG)
        context = get_relevant_info(department_id, user_message)
        
        # 5. CARI DI GOOGLE SHEETS (Daftar link yang tertaut di Knowledge Base)
        gsheet_urls = get_gsheet_knowledge_urls(department_id)
        gsheet_context = get_google_sheet_info(gsheet_urls)
        
        # Gabungkan konteks (Urutan: GSheet > Manual > File Knowledge)
        full_context = gsheet_context + manual_answers_context + "\n" + (context if context else "")
        
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
            "2. PRIORITAS UTAMA: Jika ada 'JAWABAN MANUAL DARI ADMIN' yang relevan dengan pertanyaan user, Anda WAJIB menggunakan jawaban tersebut secara mutlak.\n"
            "3. Jika user bertanya tentang informasi (biaya, syarat, link, dll) dan jawabannya TIDAK ADA di konteks di atas: "
            "Anda WAJIB memberikan jawaban yang sopan namun WAJIB menyertakan tag [[TIDAK_TAHU]] di dalam jawaban Anda.\n"
            "4. Jika user memberikan nama, gunakan tag [[SET_NAME: Nama]] di akhir jawaban Anda.\n"
            "5. Anda WAJIB menganalisis sentimen pesan terakhir user (kategori: positive, neutral, negative) dan menyertakan tag [[SENTIMENT: kategori]] di akhir jawaban.\n"
        )

        if is_csat_enabled:
            smart_instructions += (
                "6. Jika percakapan tampak akan BERAKHIR (user mengucapkan terima kasih, salam penutup, atau masalah sudah selesai), "
                "Anda WAJIB menambahkan kalimat berikut di akhir jawaban Anda: '\n\nApakah jawaban saya sudah membantu dan menjawab pertanyaan Kakak? (Balas YA / TIDAK ya!)'"
            )


        current_messages = [
            {"role": "system", "content": full_system_prompt + "\n\nInformasi Konteks:\n" + full_context + smart_instructions}
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
        
        # Bersihkan tag nama jika ada
        from services.db_service import update_customer_nickname
        name_match = re.search(r'\[\[SET_NAME:\s*(.*?)\]\]', ai_reply)
        if name_match:
            new_name = name_match.group(1).strip()
            update_customer_nickname(customer_id, new_name)
            ai_reply = re.sub(r'\[\[SET_NAME:.*?\]\]', '', ai_reply)

        # Bersihkan tag sentiment jika ada
        sentiment = None
        sentiment_match = re.search(r'\[\[SENTIMENT:\s*(.*?)\]\]', ai_reply)
        if sentiment_match:
            sentiment = sentiment_match.group(1).strip().lower()
            ai_reply = re.sub(r'\[\[SENTIMENT:.*?\]\]', '', ai_reply)

        answer = ai_reply.strip()
        
        # Simpan ke riwayat & return
        history.append({"role": "user", "content": user_message})
        history.append({"role": "assistant", "content": answer})
        save_session_memory(department_id, customer_id, history)
        
        return answer, prompt_tokens, completion_tokens, sentiment
        
    except Exception as e:
        print(f"Error OpenAI: {e}")
        return "Maaf, saya sedang mengalami gangguan koneksi.", 0, 0, None

def clear_memory(customer_id: str, department_id: str):
    path = get_memory_path(department_id, customer_id)
    if os.path.exists(path):
        os.remove(path)
