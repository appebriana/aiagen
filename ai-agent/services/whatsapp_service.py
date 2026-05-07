import os
import httpx
from dotenv import load_dotenv

load_dotenv()

# Point to our Node.js WA Gateway Bridge
async def send_whatsapp_message(to_number: str, text: str, department_id: str = "1"):
    """
    Kirim perintah balik ke Node.js WA Gateway untuk membalas pesan.
    Port dihitung dinamis: 3000 + (ID Departemen - 1)
    """
    try:
        # Hitung port sesuai logika manager.js
        port = 3000 + (int(department_id) - 1)
        gateway_url = f"http://127.0.0.1:{port}/send"
        
        payload = {
            "target": to_number,
            "message": text
        }

        async with httpx.AsyncClient() as client:
            response = await client.post(gateway_url, json=payload)
            return response.json()
    except Exception as e:
        error_msg = f"Gagal mengirim balasan ke gateway (Dept {department_id}, Port {port if 'port' in locals() else 'unknown'}): {e}"
        print(error_msg)
        return {"status": "error", "message": error_msg}

async def send_typing_indicator(to_number: str, department_id: str):
    """
    Kirim perintah ke Node.js WA Gateway untuk memunculkan status 'Mengetik...'.
    """
    try:
        port = 3000 + (int(department_id) - 1)
        gateway_url = f"http://127.0.0.1:{port}/typing"
        
        async with httpx.AsyncClient() as client:
            await client.post(gateway_url, json={"target": to_number}, timeout=5.0)
    except Exception as e:
        print(f"Gagal mengirim status mengetik: {e}")
