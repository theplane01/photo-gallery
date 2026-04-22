@echo off
REM PhotoGallery - Windows Startup Script

echo.
echo   ========== PhotoGallery Startup ==========
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if errorlevel 1 (
    echo Error: Node.js is not installed
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

echo Server Setup...
cd server

REM Install backend dependencies
if not exist "node_modules\" (
    echo Installing backend dependencies...
    call npm install
)

REM Start backend
echo Starting backend server on http://localhost:5000
start cmd /k "npm run dev"

REM Wait a bit
timeout /t 3 /nobreak

echo.
echo Client Setup...
cd ..\client

REM Install frontend dependencies
if not exist "node_modules\" (
    echo Installing frontend dependencies...
    call npm install
)

REM Start frontend
echo Starting frontend server on http://localhost:3000
start cmd /k "npm run dev"

echo.
echo ==========================================
echo Backend:  http://localhost:5000
echo Frontend: http://localhost:3000
echo ==========================================
echo.
pause
