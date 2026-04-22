# PostgreSQL (Supabase) Configuration

```env
# Server Config
PORT=3001
NODE_ENV=production

# Supabase PostgreSQL Database
DB_HOST=YOUR_SUPABASE_PROJECT.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=YOUR_SUPABASE_PASSWORD
DB_SSL=true

# JWT Authentication
JWT_SECRET=GENERATE_WITH: openssl rand -base64 32
JWT_EXPIRE=7d

# Frontend URL (untuk CORS)
CLIENT_URL=https://your-frontend.vercel.app

# Supabase Storage (untuk file upload)
SUPABASE_URL=https://YOUR_SUPABASE_PROJECT.supabase.co
SUPABASE_ANON_KEY=YOUR_ANON_KEY
SUPABASE_SERVICE_ROLE_KEY=YOUR_SERVICE_ROLE_KEY

# Optional: Sentry untuk error tracking
SENTRY_DSN=

# Optional: Analytics
ANALYTICS_URL=
```

## 🔑 Cara Mendapatkan Keys

### Supabase Keys
1. Login ke [supabase.com](https://supabase.com)
2. Open Project Settings
3. Go to **API** section
4. Copy:
   - `Project URL` → DB_HOST (tanpa https://)
   - `Database Password` → DB_PASSWORD
   - `anon public` → SUPABASE_ANON_KEY
   - `service_role` → SUPABASE_SERVICE_ROLE_KEY

### JWT Secret
```bash
openssl rand -base64 32
# Output: GUNAKAN INI UNTUK JWT_SECRET
```

### Di Vercel Dashboard
1. Open your project
2. Go to **Settings** → **Environment Variables**
3. Add semua variables di atas
4. Redeploy untuk apply
