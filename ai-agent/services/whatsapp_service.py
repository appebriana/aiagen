import os
import httpx
from dotenv import load_dotenv

load_dotenv()

# Point to our Node.js WA Gateway Bridge
async def send_whatsapp_message(to_number: str, text: str, department_id: str = "1", gateway_port: int = None, reply_to_msg_id: str = None):
    """
    Kirim perintah balik ke Node.js WA Gateway untuk membalas pesan.
    Menggunakan gateway_port dari payload webhook jika tersedia.
    """
    try:
        # Gunakan port dari webhook jika ada, jika tidak fallback ke perhitungan manual
        port = gateway_port if gateway_port else 3000 + (int(department_id) - 1)
        gateway_url = f"http://127.0.0.1:{port}/send"
        
        payload = {
            "target": to_number,
            "message": text
        }
        if reply_to_msg_id:
            payload["reply_to_msg_id"] = reply_to_msg_id

        async with httpx.AsyncClient() as client:
            response = await client.post(gateway_url, json=payload)
            return response.json()
    except Exception as e:
        error_msg = f"Gagal mengirim balasan ke gateway (Dept {department_id}, Port {port if 'port' in locals() else 'unknown'}): {e}"
        print(error_msg)
        return {"status": "error", "message": error_msg}

async def send_typing_indicator(to_number: str, department_id: str, gateway_port: int = None):
    """
    Kirim perintah ke Node.js WA Gateway untuk memunculkan status 'Mengetik...'.
    """
    try:
        port = gateway_port if gateway_port else 3000 + (int(department_id) - 1)
        gateway_url = f"http://127.0.0.1:{port}/typing"
        
        async with httpx.AsyncClient() as client:
            await client.post(gateway_url, json={"target": to_number}, timeout=5.0)
    except Exception as e:
        print(f"Gagal mengirim status mengetik: {e}")
