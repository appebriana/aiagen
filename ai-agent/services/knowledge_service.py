import os
from langchain_openai import OpenAIEmbeddings
from langchain_community.vectorstores import FAISS
from langchain_community.document_loaders import TextLoader, PyPDFLoader, DirectoryLoader
from langchain_text_splitters import CharacterTextSplitter
from dotenv import load_dotenv

load_dotenv()
if not os.getenv("OPENAI_API_KEY"):
    load_dotenv(os.path.join(os.path.dirname(__file__), "../../.env"))

# Lokasi folder dokumen utama (AI-Agent/knowledge/{department_id})
BASE_KNOWLEDGE_DIR = "knowledge"
# Lokasi folder index utama
BASE_INDEX_PATH = "faiss_indexes"

embeddings = OpenAIEmbeddings(api_key=os.getenv("OPENAI_API_KEY"))

def build_knowledge_base(department_id: str):
    """
    Membangun vector index untuk departemen tertentu.
    """
    dept_knowledge_dir = os.path.join(BASE_KNOWLEDGE_DIR, str(department_id))
    dept_index_path = os.path.join(BASE_INDEX_PATH, str(department_id))

    if not os.path.exists(dept_knowledge_dir):
        print(f"Folder knowledge untuk departemen {department_id} tidak ditemukan.")
        return None

    # 1. Load dokumen khusus departemen ini
    loaders = [
        DirectoryLoader(dept_knowledge_dir, glob="**/*.txt", loader_cls=TextLoader),
        DirectoryLoader(dept_knowledge_dir, glob="**/*.pdf", loader_cls=PyPDFLoader),
    ]
    
    docs = []
    for loader in loaders:
        try:
            docs.extend(loader.load())
        except Exception as e:
            print(f"Error loading docs for dept {department_id}: {e}")

    if not docs:
        print(f"Tidak ada dokumen ditemukan untuk departemen {department_id}.")
        return None

    # 2. Split teks
    text_splitter = CharacterTextSplitter(chunk_size=1000, chunk_overlap=100)
    texts = text_splitter.split_documents(docs)

    # 3. Buat vector store
    vectorstore = FAISS.from_documents(texts, embeddings)
    
    # 4. Simpan index khusus departemen ini
    os.makedirs(BASE_INDEX_PATH, exist_ok=True)
    vectorstore.save_local(dept_index_path)
    print(f"Knowledge base untuk departemen {department_id} berhasil dibangun.")
    return vectorstore

def get_relevant_info(department_id: str, query: str):
    """
    Mencari informasi relevan dalam ruang lingkup data milik departemen tertentu.
    """
    dept_index_path = os.path.join(BASE_INDEX_PATH, str(department_id))
    
    try:
        if os.path.exists(dept_index_path):
            vectorstore = FAISS.load_local(dept_index_path, embeddings, allow_dangerous_deserialization=True)
        else:
            vectorstore = build_knowledge_base(department_id)
        
        if vectorstore:
            docs = vectorstore.similarity_search(query, k=3)
            return "\n".join([doc.page_content for doc in docs])
    except Exception as e:
        print(f"Error searching knowledge for dept {department_id}: {e}")
    
    return ""
