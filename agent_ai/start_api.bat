@echo off
REM FastAPI Agent Grouping Server - Windows Startup Script
REM Jalankan: start_api.bat

echo ============================================================
echo Starting Agent Grouping FastAPI Server
echo ============================================================
echo.
echo Server akan berjalan di: http://127.0.0.1:8001
echo.
echo Available endpoints:
echo   POST   /generate-kelompok          - Generate kelompok
echo   GET    /conversation-history/{id}  - Get conversation history
echo   DELETE /conversation-history/{id}  - Clear conversation history
echo   GET    /health                     - Health check
echo.
echo Stop server: Ctrl+C
echo.
echo ============================================================
echo.

REM Check if Python is installed
python --version >nul 2>&1
if errorlevel 1 (
    echo Error: Python tidak ditemukan!
    echo Pastikan Python sudah di-install dan di PATH
    pause
    exit /b 1
)

REM Check if uvicorn installed
python -c "import uvicorn" >nul 2>&1
if errorlevel 1 (
    echo Error: FastAPI dependencies tidak ter-install!
    echo Run: pip install -r requirements-api.txt
    pause
    exit /b 1
)

REM Start server
python -m uvicorn api:app --host 127.0.0.1 --port 8001 --reload

pause
