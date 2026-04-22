@echo off
REM Production deployment script untuk Windows

echo.
echo   ========== Production Deployment Check ==========
echo.

REM Check Node.js
node --version >nul 2>&1
if errorlevel 1 (
    echo Error: Node.js not installed
    exit /b 1
)

echo Checking environment files...

REM Check backend env
if not exist "server\.env.production" (
    echo Error: server\.env.production not found
    echo Copy: copy server\.env server\.env.production
    exit /b 1
)

REM Check client env
if not exist "client\.env.production" (
    echo Error: client\.env.production not found
    echo Create with Supabase API URL
    exit /b 1
)

echo.
echo Backend Setup:
cd server
call npm install --production
call npm run build 2>nul || echo No build needed
cd ..

echo.
echo Frontend Build:
cd client
call npm install
call npm run build
cd ..

echo.
echo ========== Ready for Deployment ==========
echo.
echo Next steps:
echo   1. cd server
echo   2. vercel --prod
echo   3. cd ../client
echo   4. vercel --prod
echo   5. Add environment variables in Vercel Dashboard
echo.
pause
