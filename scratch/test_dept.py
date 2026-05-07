import sys
import os
sys.path.append(os.path.join(os.getcwd(), "ai-agent"))
from services.db_service import get_department_settings

print("Testing get_department_settings with string '2'...")
settings = get_department_settings("2")
print(f"Result: {settings}")
