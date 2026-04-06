@echo off
REM Kill any existing uvicorn processes
taskkill /F /IM python.exe /FI "WINDOWTITLE eq*uvicorn*" 2>nul

echo.
echo ============================================================
echo Starting uvicorn server on port 8001...
echo ============================================================
echo.

cd /d "d:\semester 6\PROYEK AKHIR 3\agent ai"

REM Start uvicorn
.\.venv\Scripts\uvicorn.exe main:app --reload --port=8001

REM Keep window open
pause
