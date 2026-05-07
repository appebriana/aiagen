import sys
import os

# Tambahkan path ai-agent ke sys.path
sys.path.append(os.path.join(os.getcwd(), "ai-agent"))

from services.db_service import log_unanswered_question

print("Testing log_unanswered_question...")
log_unanswered_question("2", "Test question from script")
print("Done. Check database.")
