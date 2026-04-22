# ⚡ Quick Deployment Guide - Vercel + Supabase

Langkah-langkah cepat deploy production dalam 30 menit!

## 📋 Checklist Pre-Deployment

- [ ] Supabase account created
- [ ] PostgreSQL database ready
- [ ] Supabase Storage bucket created
- [ ] Vercel account created
- [ ] GitHub repository ready
- [ ] Environment variables prepared

---

## 🚀 Fase 1: Supabase Setup (5 menit)

### 1. Create Supabase Project
```
supabase.com → Sign Up → Create New Project
```

Catat:
- Project URL: `https://xxxxx.supabase.co`
- Database Password: `xxxxxx`
- Region: `Singapore` (untuk Indonesia)

### 2. Create Tables

Copy schema dari `server/migrations/supabase-schema.sql` ke:
```
Supabase Dashboard → SQL Editor → New Query → Paste & Run
```

### 3. Create Storage Bucket

```
Supabase Dashboard → Storage → New Bucket
Bucket name: photos
Access: Public
```

### 4. Get API Keys

```
Settings → API → Copy:
- Project URL
- anon public (SUPABASE_ANON_KEY)
- service_role (SUPABASE_SERVICE_ROLE_KEY)
```

---

## 🛠️ Fase 2: Backend Preparation (10 menit)

### 1. Update Dependencies

```bash
cd server

npm install pg @supabase/supabase-js
npm install --save-dev
```

### 2. Create .env.production

```bash
cp .env .env.production
```

Edit `server/.env.production`:

```env
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=your_supabase_password
DB_SSL=true

JWT_SECRET=run: openssl rand -base64 32

CLIENT_URL=https://your-app.vercel.app

SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

### 3. Verify Files Exist

```
✓ server/vercel.json
✓ server/config/supabase.js
✓ server/migrations/supabase-schema.sql
```

---

## 📱 Fase 3: Frontend Preparation (5 menit)

### 1. Create .env.production

```bash
cd client

echo "VITE_API_URL=https://your-api.vercel.app/api" > .env.production
```

### 2. Build Test

```bash
npm run build

# Check dist/ folder is created
ls dist/
```

---

## 🌐 Fase 4: Deploy ke Vercel (10 menit)

### Option A: Via GitHub (Recommended)

```bash
# 1. Push ke GitHub
cd ../
git add .
git commit -m "Ready for production"
git push origin main

# 2. Di vercel.com:
# - Import project dari GitHub
# - Select your repository
# - Framework: Node.js (backend), Vite (frontend)
```

### Option B: Via Vercel CLI

```bash
# 1. Install Vercel CLI
npm install -g vercel

# 2. Deploy Backend
cd server
vercel --prod

# 3. Deploy Frontend
cd ../client
vercel --prod
```

---

## 🔐 Fase 5: Configure Environment Variables

### Backend (Server Project)

Di Vercel Dashboard → Project Settings → Environment Variables:

```
DB_HOST                      your-project.supabase.co
DB_PORT                      5432
DB_NAME                      postgres
DB_USER                      postgres
DB_PASSWORD                  (your supabase password)
DB_SSL                       true
JWT_SECRET                   (generated key)
CLIENT_URL                   https://your-app.vercel.app
SUPABASE_URL                 https://your-project.supabase.co
SUPABASE_ANON_KEY           (anon public key)
SUPABASE_SERVICE_ROLE_KEY   (service role key)
```

Redeploy: `vercel --prod`

### Frontend (App Project)

Di Vercel Dashboard → Project Settings → Environment Variables:

```
VITE_API_URL    https://your-api.vercel.app/api
```

Redeploy: `vercel --prod`

---

## ✅ Fase 6: Testing (5 menit)

### 1. Test Backend Health

```bash
curl https://your-api.vercel.app/api/health
# Expected: {"status":"OK"}
```

### 2. Test API

```bash
# Register
curl -X POST https://your-api.vercel.app/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "password": "Test123!"
  }'
```

### 3. Test Frontend

Open: https://your-app.vercel.app

- Try register
- Try login
- Try upload photo

---

## 🔗 Final URLs

After deployment:

```
Frontend: https://your-app.vercel.app
API:      https://your-api.vercel.app/api
Database: Supabase (managed)
Storage:  Supabase Storage (managed)
```

---

## 📊 Cost Estimate (Monthly)

| Service | Free Tier | Cost |
|---------|-----------|------|
| Vercel Frontend | 100 GB bandwidth | Free |
| Vercel Backend | 100,000 invocations | Free |
| Supabase DB | 500MB storage | Free |
| Supabase Storage | 1GB storage, 3GB bandwidth | Free |

**Total Cost for Free Tier: $0/month** ✓

Upgrade when needed: Storage limit ↑

---

## ❌ Troubleshooting

### API shows 500 error
```bash
# Check logs
vercel logs https://your-api.vercel.app

# Common issues:
# - DB_HOST wrong
# - DB_PASSWORD wrong
# - JWT_SECRET not set
```

### File upload fails
```bash
# Check Supabase Storage:
# - Bucket 'photos' exists?
# - SUPABASE_ANON_KEY correct?
# - API key has storage permission?
```

### CORS error on frontend
```bash
# Check:
# - VITE_API_URL correct
# - CLIENT_URL in backend matches
# - API deployed correctly
```

### Login token invalid
```bash
# Solution:
# - Logout & login again
# - Check JWT_SECRET same as deployment
# - Clear localStorage
```

---

## 🎓 Next Steps

✅ Production deployed
✅ Database secured
✅ Files uploaded
✅ Monitoring ready

Next:
1. Setup custom domain (optional)
2. Enable HTTPS (automatic)
3. Setup monitoring (Sentry)
4. Configure backups
5. Setup CI/CD pipeline

---

**Selamat! Aplikasi Anda live di production!** 🎉

**Total time: ~30 menit**
