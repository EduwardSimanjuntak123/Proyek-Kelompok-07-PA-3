#!/bin/bash

# FastAPI Agent Grouping Server - Linux/Mac Startup Script
# Jalankan: chmod +x start_api.sh && ./start_api.sh

echo "============================================================"
echo "Starting Agent Grouping FastAPI Server"
echo "============================================================"
echo ""
echo "Server akan berjalan di: http://127.0.0.1:8001"
echo ""
echo "Available endpoints:"
echo "  POST   /generate-kelompok          - Generate kelompok"
echo "  GET    /conversation-history/{id}  - Get conversation history"
echo "  DELETE /conversation-history/{id}  - Clear conversation history"
echo "  GET    /health                     - Health check"
echo ""
echo "Stop server: Ctrl+C"
echo ""
echo "============================================================"
echo ""

# Check if Python is installed
if ! command -v python3 &> /dev/null; then
    echo "Error: Python3 tidak ditemukan!"
    echo "Pastikan Python3 sudah ter-install"
    exit 1
fi

# Check if uvicorn installed
if ! python3 -c "import uvicorn" 2>/dev/null; then
    echo "Error: FastAPI dependencies tidak ter-install!"
    echo "Run: pip install -r requirements-api.txt"
    exit 1
fi

# Start server
python3 -m uvicorn api:app --host 127.0.0.1 --port 8001 --reload
