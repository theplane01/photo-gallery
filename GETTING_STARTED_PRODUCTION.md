# 🚀 Mulai Deploy ke Vercel + Supabase

**Panduan 30 menit untuk live ke production!**

---

## 📋 Yang Anda Butuhkan

- ✓ GitHub account (untuk push code)
- ✓ Vercel account (free)
- ✓ Supabase account (free)
- ✓ 30 menit waktu

---

## ⚡ Step-by-Step Deployment

### 🔵 STEP 1: Setup Supabase Database (5 menit)

1. **Pergi ke** [supabase.com](https://supabase.com)

2. **Sign Up** → Choose GitHub

3. **Create New Project**
   ```
   Project Name: photo-gallery
   Database Password: (buat password kuat)
   Region: Singapore (untuk speed)
   ```
   Tunggu ~5 menit...

4. **Simpan credentials:**
   ```
   Project URL:  copy dari dashboard
   DB Password:  yang tadi dibuat
   ```

5. **Create Tables** - Salin ke SQL Editor:
   ```
   File: server/migrations/supabase-schema.sql
   → Paste ke Supabase Dashboard → SQL Editor → Run
   ```

6. **Create Storage Bucket**
   ```
   Supabase Dashboard → Storage → New Bucket
   Name: photos
   Access: Public
   ```

7. **Get API Keys**
   ```
   Settings → API → Copy:
   - Project URL (database)
   - anon public (API key)
   - service_role (backend key)
   ```

---

### 🟢 STEP 2: Configure Backend (5 menit)

1. **Buka folder server**
   ```bash
   cd server
   ```

2. **Create production env file**
   ```bash
   cp .env.production.example .env.production
   ```

3. **Edit `.env.production`** - Gunakan credentials Supabase:
   ```env
   DB_HOST=your-project.supabase.co
   DB_PORT=5432
   DB_NAME=postgres
   DB_USER=postgres
   DB_PASSWORD=your_password_here
   DB_SSL=true
   
   JWT_SECRET=run_this: openssl rand -base64 32
   CLIENT_URL=https://your-app.vercel.app
   
   SUPABASE_URL=https://your-project.supabase.co
   SUPABASE_ANON_KEY=paste_here
   SUPABASE_SERVICE_ROLE_KEY=paste_here
   ```

4. **Install PostgreSQL driver**
   ```bash
   npm install pg @supabase/supabase-js
   ```

5. **Verify konfigurasi**
   ```bash
   npm run dev
   # Test: curl http://localhost:5000/api/health
   ```

6. **Jika OK**, lanjut ke step 3

---

### 🟡 STEP 3: Build Frontend (5 menit)

1. **Buka folder client**
   ```bash
   cd ../client
   ```

2. **Create production env**
   ```bash
   echo "VITE_API_URL=https://your-api.vercel.app/api" > .env.production
   ```

3. **Build untuk production**
   ```bash
   npm run build
   ```

4. **Verify build success**
   ```bash
   ls -la dist/
   # Harus ada file di folder dist/
   ```

---

### 🔴 STEP 4: Push ke GitHub (5 menit)

1. **Initialize git** (jika belum)
   ```bash
   cd ..
   git init
   git add .
   git commit -m "Ready for production deployment"
   ```

2. **Create GitHub repo** → [github.com/new](https://github.com/new)
   - Name: photo-gallery
   - Public/Private: terserah
   - Create

3. **Push code**
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/photo-gallery.git
   git branch -M main
   git push -u origin main
   ```

---

### 🟠 STEP 5: Deploy Backend ke Vercel (5 menit)

1. **Login Vercel** → [vercel.com](https://vercel.com)

2. **New Project** → Import Git Repository

3. **Select** photo-gallery repository

4. **Configure Project**
   ```
   Root Directory: server/
   Framework: Node.js
   ```

5. **Click Deploy** → Wait...

6. **Setelah berhasil**, kopilot URL backend
   ```
   https://your-api.vercel.app
   ```

7. **Add Environment Variables**
   ```
   Settings → Environment Variables → Add:
   
   DB_HOST                    your-project.supabase.co
   DB_PORT                    5432
   DB_NAME                    postgres
   DB_USER                    postgres
   DB_PASSWORD                (your password)
   JWT_SECRET                 (32 char random)
   CLIENT_URL                 https://your-app.vercel.app
   SUPABASE_URL               https://your-project.supabase.co
   SUPABASE_ANON_KEY         (paste here)
   SUPABASE_SERVICE_ROLE_KEY (paste here)
   ```

8. **Redeploy** → Deployments → Trigger → Redeploy

9. **Test backend**
   ```bash
   curl https://your-api.vercel.app/api/health
   # Expected: {"status":"OK"}
   ```

---

### 🔵 STEP 6: Deploy Frontend ke Vercel (5 menit)

1. **Di Vercel** → **New Project** (baru)

2. **Import Repository** → photo-gallery

3. **Configure Project**
   ```
   Root Directory: client/
   Framework: Other (Vite)
   Build Command: npm run build
   Output Directory: dist
   ```

4. **Add Environment Variables**
   ```
   VITE_API_URL    https://your-api.vercel.app/api
   ```

5. **Click Deploy** → Wait...

6. **Setelah berhasil**, buka URL frontend
   ```
   https://your-app.vercel.app
   ```

---

### ✅ STEP 7: Test Production (5 menit)

**Test Backend API:**
```bash
# Health check
curl https://your-api.vercel.app/api/health

# Register
curl -X POST https://your-api.vercel.app/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"test","email":"test@example.com","password":"test123"}'
```

**Test Frontend:**
1. Buka https://your-app.vercel.app
2. Register user baru
3. Login
4. Upload photo
5. Like & comment

---

## 🎉 Selesai!

**Aplikasi Anda sudah LIVE!**

```
Frontend:  https://your-app.vercel.app
API:       https://your-api.vercel.app/api
Database:  Supabase (managed)
Storage:   Supabase Storage (managed)
```

---

## 📊 Status

- [x] Backend deployed
- [x] Frontend deployed
- [x] Database connected
- [x] Storage configured
- [x] Testing passed

**Status: PRODUCTION ✅**

---

## 🔗 Dokumentasi Lengkap

Untuk detail lebih lanjut, baca:

- **[DEPLOYMENT_QUICK_START.md](DEPLOYMENT_QUICK_START.md)** - Panduan detil
- **[DEPLOY_VERCEL_SUPABASE.md](DEPLOY_VERCEL_SUPABASE.md)** - Referensi lengkap
- **[SUPABASE_REFERENCE.md](SUPABASE_REFERENCE.md)** - Database tips
- **[ENV_SUPABASE.md](ENV_SUPABASE.md)** - Environment variables

---

## ❓ Troubleshooting

### Backend tidak bisa connect ke database
```
✓ Check DB_HOST, PASSWORD di Vercel env vars
✓ Ensure Supabase project aktif
✓ Try test connection locally
```

### Frontend shows CORS error
```
✓ Check VITE_API_URL correct
✓ Check CLIENT_URL di backend env
✓ Redeploy backend after env change
```

### File upload fails
```
✓ Check storage bucket "photos" exists di Supabase
✓ Verify SUPABASE_ANON_KEY correct
✓ Check file size < 5MB
```

### Login error
```
✓ Check database seeded dengan user
✓ Check JWT_SECRET sama di backend
✓ Clear browser localStorage
```

---

## 🚀 Next Steps

Sekarang aplikasi sudah live, Anda bisa:

1. **Custom Domain**
   - Vercel: Settings → Domains
   - Add domain Anda sendiri

2. **Monitoring**
   - Vercel: Analytics
   - Supabase: Monitoring

3. **Backups**
   - Supabase: auto-backup daily
   - Manual export jika diperlukan

4. **Improvements**
   - Add error tracking (Sentry)
   - Add analytics (Google Analytics)
   - Optimize performance
   - Add more features

---

## 💡 Pro Tips

1. **Push changes otomatis deploy**
   ```bash
   git push origin main
   # Vercel auto-deploy!
   ```

2. **Monitor logs**
   ```bash
   vercel logs https://your-api.vercel.app
   ```

3. **Scale up later**
   - Upgrade database di Supabase
   - Upgrade Vercel plan
   - Add CDN untuk storage

4. **Security**
   - Rotate API keys regularly
   - Enable 2FA di Supabase
   - Monitor database logs

---

## 📞 Support

Jika ada masalah:

1. Check dokumentasi di folder project
2. Visit [supabase.com/docs](https://supabase.com/docs)
3. Visit [vercel.com/docs](https://vercel.com/docs)
4. Check browser console untuk errors
5. Check Vercel/Supabase logs

---

## 🎓 Apa yang Sudah Dikerjakan

✅ Backend migration dari MySQL ke PostgreSQL
✅ Frontend integration dengan Vue.js
✅ Deployment configuration untuk Vercel
✅ Storage setup dengan Supabase Storage
✅ Environment configuration
✅ Database schema optimization
✅ Security best practices
✅ Production-ready code

---

**Selamat! Aplikasi Anda production-ready!** 🎊

**Total waktu: 30 menit**
**Biaya: GRATIS (free tier)**

🚀 **Happy deploying!**
