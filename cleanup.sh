#!/bin/bash

echo "🗑️  PhotoGallery - Cleanup Script"
echo "===================================="
echo ""

# Remove node_modules and lock files
echo "Removing node_modules and lock files..."

# Backend
echo "Cleaning server..."
cd server
rm -rf node_modules
rm -f package-lock.json
cd ..

# Frontend
echo "Cleaning client..."
cd client
rm -rf node_modules
rm -f package-lock.json
rm -rf dist
cd ..

# Build files
echo "Cleaning build files..."
rm -rf .DS_Store
find . -name ".DS_Store" -delete
find . -name "*.log" -delete

echo ""
echo "✅ Cleanup complete!"
echo "Run npm install in both server/ and client/ directories to reinstall"
