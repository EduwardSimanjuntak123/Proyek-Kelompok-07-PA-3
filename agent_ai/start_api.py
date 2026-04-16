#!/usr/bin/env python
"""
Startup script untuk FastAPI Agent Grouping Server
Pastikan sudah run: pip install -r requirements-api.txt
"""

import subprocess
import sys
import os

def start_server():
    """Start FastAPI server"""
    
    print("=" * 60)
    print("Starting Agent Grouping FastAPI Server")
    print("=" * 60)
    print()
    print("Server akan berjalan di: http://127.0.0.1:8001")
    print()
    print("Available endpoints:")
    print("  POST   /generate-kelompok          - Generate kelompok")
    print("  GET    /conversation-history/{id}  - Get conversation history")
    print("  DELETE /conversation-history/{id}  - Clear conversation history")
    print("  GET    /health                     - Health check")
    print()
    print("Stop server dengan: Ctrl+C")
    print()
    print("-" * 60)
    print()
    
    # Run uvicorn
    try:
        subprocess.run([
            sys.executable, "-m", "uvicorn",
            "api:app",
            "--host", "127.0.0.1",
            "--port", "8001",
            "--reload"
        ])
    except KeyboardInterrupt:
        print("\n\nServer stopped.")
        sys.exit(0)
    except Exception as e:
        print(f"\nError starting server: {e}")
        sys.exit(1)

if __name__ == "__main__":
    # Check jika dependencies installed
    try:
        import fastapi
        import uvicorn
    except ImportError:
        print("Error: FastAPI dependencies not installed!")
        print("Run: pip install -r requirements-api.txt")
        sys.exit(1)
    
    start_server()
