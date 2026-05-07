import requests
import json

url = "http://localhost:8000/webhook"
data = {
    "sender": "628123456789@c.us",
    "message": "halo",
    "department_id": "2"
}

try:
    response = requests.post(url, json=data, timeout=30)
    print(f"Status Code: {response.status_code}")
    print(f"Response Body: {response.text}")
except Exception as e:
    print(f"Error: {e}")
