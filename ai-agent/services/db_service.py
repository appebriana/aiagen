import os
import mysql.connector
from dotenv import load_dotenv

load_dotenv()
if not os.getenv("DB_HOST"):
    load_dotenv(os.path.join(os.path.dirname(__file__), "../../.env"))

def get_db_connection():
    return mysql.connector.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database=os.getenv("DB_DATABASE", "aiagen"),
        charset="utf8mb4",
        collation="utf8mb4_unicode_ci"
    )

def get_department_settings(department_id: str):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM departments WHERE id = %s", (department_id,))
        dept = cursor.fetchone()
        cursor.close()
        conn.close()
        return dept
    except Exception as e:
        print(f"Error Database: {e}")
        return None

def get_gsheet_knowledge_urls(department_id: str):
    """Mengambil semua link Google Sheet yang terdaftar sebagai knowledge untuk departemen ini."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT url FROM knowledge_files WHERE department_id = %s AND type = 'google_sheet'", (department_id,))
        rows = cursor.fetchall()
        cursor.close()
        conn.close()
        return [row['url'] for row in rows if row['url']]
    except Exception as e:
        print(f"Error Database (get_gsheet_knowledge_urls): {e}")
        return []

def get_manual_answers(department_id: str):
    """Mengambil daftar pertanyaan yang sudah dijawab untuk referensi AI."""
    try:
        try:
            dept_id = int(department_id)
        except:
            return ""
            
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Ambil 20 jawaban manual terbaru untuk departemen ini, prioritaskan yang ditandai sebagai knowledge
        query = """
            SELECT question, answer 
            FROM unanswered_questions 
            WHERE department_id = %s AND is_answered = 1 
            ORDER BY is_knowledge DESC, updated_at DESC 
            LIMIT 20
        """
        cursor.execute(query, (dept_id,))
        rows = cursor.fetchall()
        cursor.close()
        conn.close()
        
        if not rows:
            return ""
            
        context = "BERIKUT ADALAH JAWABAN MANUAL DARI ADMIN (Gunakan jika relevan):\n"
        for i, row in enumerate(rows, 1):
            context += f"{i}. Pertanyaan: {row['question']}\n   Jawaban: {row['answer']}\n"
            
        return context
    except Exception as e:
        print(f"Error Database (get_manual_answers): {e}")
        return ""

def log_unanswered_question(department_id: str, sender: str, question: str):
    try:
        # Pastikan department_id adalah integer jika memungkinkan
        try:
            dept_id = int(department_id)
        except:
            print(f"[DEBUG] Invalid department_id: {department_id}")
            return

        # Clean strings
        clean_question = question.strip()
        clean_sender = sender.strip() if sender else None

        conn = get_db_connection()
        cursor = conn.cursor()
        # Cek dulu apakah pertanyaan yang sama dari pengirim yang sama sudah ada
        cursor.execute(
            "SELECT id FROM unanswered_questions WHERE department_id = %s AND sender = %s AND question = %s", 
            (dept_id, clean_sender, clean_question)
        )
        if not cursor.fetchone():
            print(f"[DEBUG] Inserting new unanswered question for dept {dept_id}")
            cursor.execute(
                "INSERT INTO unanswered_questions (department_id, sender, question, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())",
                (dept_id, clean_sender, clean_question)
            )
            conn.commit()
            print(f"[DEBUG] Insert successful")
        else:
            print(f"[DEBUG] Question already exists in unanswered_questions")
        cursor.close()
        conn.close()
    except Exception as e:
        print(f"Error Database (log_unanswered_question): {e}")

def get_or_create_customer(user_id: int, phone: str, pushname: str = None):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Cek apakah sudah ada untuk User ID ini
        cursor.execute("SELECT * FROM customers WHERE user_id = %s AND phone = %s", (user_id, phone))
        customer = cursor.fetchone()
        
        if not customer:
            # Jika belum ada, buat baru di bawah User ID ini
            cursor.execute(
                "INSERT INTO customers (user_id, phone, name, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())",
                (user_id, phone, pushname)
            )
            conn.commit()
            # Ambil data yang baru dibuat
            cursor.execute("SELECT * FROM customers WHERE user_id = %s AND phone = %s", (user_id, phone))
            customer = cursor.fetchone()
        elif pushname and not customer['name']:
            # Jika ada tapi nama kosong, update namanya
            cursor.execute("UPDATE customers SET name = %s, updated_at = NOW() WHERE user_id = %s AND phone = %s", (pushname, user_id, phone))
            conn.commit()
            customer['name'] = pushname
            
        cursor.close()
        conn.close()
        return customer
    except Exception as e:
        print(f"Error Database (get_or_create_customer): {e}")
        return None

def get_customer_ai_status(user_id: int, phone: str):
    """Cek apakah AI diaktifkan untuk customer ini."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT is_ai_enabled FROM customers WHERE user_id = %s AND phone = %s", (user_id, phone))
        row = cursor.fetchone()
        cursor.close()
        conn.close()
        return row['is_ai_enabled'] if row else True
    except Exception as e:
        print(f"Error Database (get_customer_ai_status): {e}")
        return True

def set_customer_ai_status(user_id: int, phone: str, status: bool):
    """Aktifkan atau matikan AI untuk customer ini (Human Takeover)."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("UPDATE customers SET is_ai_enabled = %s, updated_at = NOW() WHERE user_id = %s AND phone = %s", (1 if status else 0, user_id, phone))
        conn.commit()
        cursor.close()
        conn.close()
        return True
    except Exception as e:
        print(f"Error Database (set_customer_ai_status): {e}")
        return False

def update_customer_nickname(user_id: int, phone: str, nickname: str):
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("UPDATE customers SET nickname = %s, updated_at = NOW() WHERE user_id = %s AND phone = %s", (nickname, user_id, phone))
        conn.commit()
        cursor.close()
        conn.close()
        return True
    except Exception as e:
        print(f"Error Database (update_customer_nickname): {e}")
        return False

def log_ai_response(department_id, customer_phone, question, answer, model, prompt_tokens, completion_tokens, sentiment=None):
    try:
        # Estimasi biaya GPT-4o-mini (per Mei 2024): 
        # Input: $0.15 / 1M tokens, Output: $0.60 / 1M tokens
        cost = (prompt_tokens * 0.00000015) + (completion_tokens * 0.00000060)
        total_tokens = prompt_tokens + completion_tokens

        conn = get_db_connection()
        cursor = conn.cursor()
        query = """
            INSERT INTO ai_chat_logs 
            (department_id, customer_phone, question, answer, model, prompt_tokens, completion_tokens, total_tokens, cost, sentiment, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
        """
        cursor.execute(query, (
            department_id, customer_phone, question, answer, model, 
            prompt_tokens, completion_tokens, total_tokens, cost, sentiment
        ))
        conn.commit()
        cursor.close()
        conn.close()
        return True
    except Exception as e:
        print(f"Error Database (log_ai_response): {e}")
        return False

def update_last_rating(department_id, customer_phone, rating):
    """Update rating pada interaksi terakhir yang belum diberi rating."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        # Cari log terakhir dari customer ini di departemen ini yang ratingnya masih NULL
        query = """
            UPDATE ai_chat_logs 
            SET rating = %s, updated_at = NOW() 
            WHERE department_id = %s AND customer_phone = %s AND rating IS NULL
            ORDER BY created_at DESC LIMIT 1
        """
        cursor.execute(query, (rating, department_id, customer_phone))
        conn.commit()
        affected = cursor.rowcount
        cursor.close()
        conn.close()
        return affected > 0
    except Exception as e:
        print(f"Error Database (update_last_rating): {e}")
        return False

def update_last_resolved(department_id, customer_phone, is_resolved):
    """Update status apakah pertanyaan terjawab pada interaksi terakhir."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        query = """
            UPDATE ai_chat_logs 
            SET is_resolved = %s, updated_at = NOW() 
            WHERE department_id = %s AND customer_phone = %s AND is_resolved IS NULL
            ORDER BY created_at DESC LIMIT 1
        """
        cursor.execute(query, (1 if is_resolved else 0, department_id, customer_phone))
        conn.commit()
        affected = cursor.rowcount
        cursor.close()
        conn.close()
        return affected > 0
    except Exception as e:
        print(f"Error Database (update_last_resolved): {e}")
        return False
