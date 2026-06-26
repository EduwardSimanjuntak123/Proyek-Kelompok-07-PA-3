2. python -m venv .venv
3. .venv\\Scripts\\activate
4. pip install -r requirements-api.txt
5. copy .env.example → .env
6. docker-compose redis up -d
7. MongoDB running  →  python setup\_mongodb.py
8. uvicorn api:app --host 127.0.0.1 --port 8002 --log-level debug

