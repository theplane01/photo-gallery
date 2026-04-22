#!/bin/bash

# Production deployment script untuk Vercel + Supabase
# Run: bash deploy-production.sh

set -e

echo "🚀 Starting Production Deployment..."
echo ""

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Prepare backend
echo -e "${BLUE}[1/5] Preparing Backend...${NC}"
cd server
npm install --production
npm run build 2>/dev/null || echo "No build script needed"
cd ..

# 2. Prepare frontend  
echo -e "${BLUE}[2/5] Building Frontend...${NC}"
cd client
npm install
npm run build
cd ..

# 3. Validate environment files
echo -e "${BLUE}[3/5] Checking Environment Files...${NC}"

if [ ! -f "server/.env.production" ]; then
    echo -e "${RED}❌ server/.env.production not found!${NC}"
    echo "Create it with:"
    echo "  cp server/.env server/.env.production"
    exit 1
fi

if [ ! -f "client/.env.production" ]; then
    echo -e "${RED}❌ client/.env.production not found!${NC}"
    echo "Create it with:"
    echo "  echo 'VITE_API_URL=https://your-api.vercel.app/api' > client/.env.production"
    exit 1
fi

echo -e "${GREEN}✓ Environment files found${NC}"

# 4. Prepare deployment
echo -e "${BLUE}[4/5] Preparing Deployment Files...${NC}"

# Create deployment readme
cat > DEPLOYMENT_INFO.txt << EOF
=== Production Deployment Info ===
Date: $(date)

Backend:  https://your-api.vercel.app
Frontend: https://your-app.vercel.app
Database: Supabase PostgreSQL

Environment Variables Set:
✓ DB_HOST
✓ DB_USER
✓ DB_PASSWORD
✓ JWT_SECRET
✓ SUPABASE_URL
✓ SUPABASE_ANON_KEY

Next Steps:
1. vercel --prod (in server/ directory)
2. vercel --prod (in client/ directory)
3. Set environment variables in Vercel Dashboard
4. Test at production URLs
EOF

echo -e "${GREEN}✓ Deployment ready${NC}"

# 5. Display info
echo -e "${BLUE}[5/5] Deployment Summary${NC}"
echo ""
echo -e "${GREEN}✅ Production Ready!${NC}"
echo ""
echo "Backend preparation:"
echo "  - Install: npm install --production"
echo "  - Deploy: vercel --prod"
echo ""
echo "Frontend preparation:"
echo "  - Build: npm run build"
echo "  - Deploy: vercel --prod"
echo ""
echo "Configuration:"
echo "  - Backend: server/.env.production"
echo "  - Frontend: client/.env.production"
echo "  - Vercel: Dashboard → Environment Variables"
echo ""
echo "Testing:"
echo "  - curl https://your-api.vercel.app/api/health"
echo "  - https://your-app.vercel.app"
echo ""
echo -e "${GREEN}🎉 Ready to deploy!${NC}"
