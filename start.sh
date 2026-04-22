#!/bin/bash

echo "🚀 PhotoGallery - Startup Script"
echo "=================================="
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    exit 1
fi

echo "✓ Node.js version: $(node -v)"
echo "✓ NPM version: $(npm -v)"
echo ""

# Install and start backend
echo "📦 Starting Backend Server..."
cd server
npm install
npm run dev &
SERVER_PID=$!
echo "✓ Backend started (PID: $SERVER_PID)"
echo ""

# Wait a bit for backend to start
sleep 3

# Install and start frontend
echo "📦 Starting Frontend Server..."
cd ../client
npm install
npm run dev &
FRONTEND_PID=$!
echo "✓ Frontend started (PID: $FRONTEND_PID)"
echo ""

echo "=================================="
echo "✅ All servers are running!"
echo ""
echo "Backend:  http://localhost:5000"
echo "Frontend: http://localhost:3000"
echo "API:      http://localhost:5000/api"
echo ""
echo "Press Ctrl+C to stop all servers"
echo "=================================="

# Wait for both processes
wait $SERVER_PID $FRONTEND_PID
