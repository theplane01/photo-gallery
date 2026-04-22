# 🚀 Deploy ke Vercel + Supabase

Panduan lengkap deploy Photo Gallery ke Vercel dengan database Supabase PostgreSQL.

## ⚡ Quick Overview

| Komponen | Platform | Link |
|----------|----------|------|
| Backend (API) | Vercel | https://your-api.vercel.app |
| Frontend (UI) | Vercel | https://your-app.vercel.app |
| Database | Supabase (PostgreSQL) | Cloud-hosted |

---

## 📋 Langkah 1: Setup Supabase Database

### 1.1 Buat Akun Supabase

1. Kunjungi [supabase.com](https://supabase.com)
2. Klik **Sign In** → **Create a new account**
3. Login dengan GitHub atau email
4. Verifikasi email

### 1.2 Buat Project Supabase

1. Di dashboard Supabase, klik **New Project**
2. Isi form:
   - **Project Name**: `photo-gallery` (atau nama lain)
   - **Database Password**: Buat password kuat (simpan!)  `ykoRjTpTRbkD1r2x`
   - **Region**: Pilih terdekat (Singapore, Tokyo, etc)
3. Tunggu project dibuat (5-10 menit)

### 1.3 Akses Database Info

Setelah project jadi, buka **Project Settings** → **Database**:

```
Host: project-id.supabase.co
Port: 5432
Database: postgres
User: postgres
Password: (password yang Anda buat)
```

---

## 🔄 Langkah 2: Migrate Database dari MySQL ke PostgreSQL

### 2.1 Install PostgreSQL Tools

```bash
# macOS
brew install postgresql

# Windows
# Download dari https://www.postgresql.org/download/windows/

# Linux
sudo apt-get install postgresql
```

### 2.2 Create SQL Schema untuk Supabase

Buat file `server/migrations/supabase-schema.sql`:

```sql
-- Create tables untuk Supabase PostgreSQL

CREATE TABLE gallery_user (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(255),
    alamat TEXT,
    level VARCHAR(50) NOT NULL DEFAULT 'User' CHECK (level IN ('Admin', 'User')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_album (
    id SERIAL PRIMARY KEY,
    nama_album VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal_dibuat DATE DEFAULT CURRENT_DATE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_foto (
    id SERIAL PRIMARY KEY,
    judul_foto VARCHAR(255) NOT NULL,
    deskripsi_foto TEXT,
    tanggal_unggah DATE DEFAULT CURRENT_DATE,
    lokasi_file VARCHAR(255) NOT NULL,
    album_id INTEGER REFERENCES gallery_album(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_komentarfoto (
    id SERIAL PRIMARY KEY,
    foto_id INTEGER NOT NULL REFERENCES gallery_foto(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    isi_komentar TEXT NOT NULL,
    tanggal_komentar DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gallery_likefoto (
    id SERIAL PRIMARY KEY,
    foto_id INTEGER NOT NULL REFERENCES gallery_foto(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    tanggal_like DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(foto_id, user_id)
);

-- Create indexes
CREATE INDEX idx_user_username ON gallery_user(username);
CREATE INDEX idx_foto_user ON gallery_foto(user_id);
CREATE INDEX idx_foto_album ON gallery_foto(album_id);
CREATE INDEX idx_album_user ON gallery_album(user_id);
CREATE INDEX idx_comment_foto ON gallery_komentarfoto(foto_id);
CREATE INDEX idx_like_foto ON gallery_likefoto(foto_id);

-- Enable RLS (Row Level Security)
ALTER TABLE gallery_user ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery_album ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery_foto ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery_komentarfoto ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery_likefoto ENABLE ROW LEVEL SECURITY;
```

### 2.3 Upload Schema ke Supabase

1. Buka Supabase Dashboard
2. Klik **SQL Editor** → **New Query**
3. Copy-paste schema di atas
4. Klik **Run**

---

## 🔧 Langkah 3: Update Backend untuk Supabase

### 3.1 Install PostgreSQL Driver

```bash
cd server
npm install pg
```

### 3.2 Update `server/config/database.js`

Buat file baru `server/config/supabase.js`:

```javascript
const { Pool } = require('pg');
require('dotenv').config();

const pool = new Pool({
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  host: process.env.DB_HOST,
  port: process.env.DB_PORT || 5432,
  database: process.env.DB_NAME,
  ssl: {
    rejectUnauthorized: false
  }
});

// Test connection
pool.query('SELECT NOW()', (err, res) => {
  if (err) {
    console.error('❌ Database connection error:', err);
  } else {
    console.log('✓ Supabase database connected');
  }
});

module.exports = pool;
```

### 3.3 Update `server/.env`

```env
# Ganti database config
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=your-supabase-password

# API Config
PORT=3001
NODE_ENV=production

# JWT
JWT_SECRET=your-super-secret-key-change-this

# Client
CLIENT_URL=https://your-frontend-url.vercel.app

# File Upload (gunakan Supabase Storage atau S3)
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your-supabase-api-key
```

### 3.4 Update Controllers untuk PostgreSQL

Perubahan utama: dari MySQLi binding ke PostgreSQL parameterized queries.

**Contoh: `server/controllers/authController.js`**

```javascript
const pool = require('../config/supabase');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

// Register
exports.register = async (req, res) => {
  try {
    const { username, email, password, nama_lengkap } = req.body;

    // Validation
    if (!username || !email || !password) {
      return res.status(400).json({ error: 'Username, email, and password required' });
    }

    // Check existing user
    const checkUser = await pool.query(
      'SELECT id FROM gallery_user WHERE username = $1 OR email = $2',
      [username, email]
    );

    if (checkUser.rows.length > 0) {
      return res.status(400).json({ error: 'Username or email already exists' });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Insert user
    const result = await pool.query(
      'INSERT INTO gallery_user (username, email, password, nama_lengkap, level) VALUES ($1, $2, $3, $4, $5) RETURNING id, username, email',
      [username, email, hashedPassword, nama_lengkap || '', 'User']
    );

    res.status(201).json({
      message: 'User registered successfully',
      user: result.rows[0]
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: error.message });
  }
};

// Login
exports.login = async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ error: 'Username and password required' });
    }

    const result = await pool.query(
      'SELECT * FROM gallery_user WHERE username = $1',
      [username]
    );

    if (result.rows.length === 0) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const user = result.rows[0];
    const isPasswordValid = await bcrypt.compare(password, user.password);

    if (!isPasswordValid) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    // Generate JWT
    const token = jwt.sign(
      { userId: user.id, username: user.username, level: user.level },
      process.env.JWT_SECRET || 'secret',
      { expiresIn: '7d' }
    );

    res.json({
      message: 'Login successful',
      token,
      user: {
        userId: user.id,
        username: user.username,
        email: user.email,
        nama_lengkap: user.nama_lengkap,
        level: user.level
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: error.message });
  }
};

// Get current user
exports.getCurrentUser = async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT id, username, email, nama_lengkap, level FROM gallery_user WHERE id = $1',
      [req.user.userId]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'User not found' });
    }

    res.json(result.rows[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
```

---

## 📤 Langkah 4: Setup File Upload ke Supabase Storage

### 4.1 Create Supabase Storage Bucket

1. Di Supabase Dashboard, klik **Storage**
2. Klik **Create a new bucket** → Beri nama `photos`
3. Pilih **Public** untuk akses publik

### 4.2 Install Supabase Client

```bash
cd server
npm install @supabase/supabase-js
```

### 4.3 Update Photo Upload Handler

```javascript
const { createClient } = require('@supabase/supabase-js');
const fs = require('fs');
const path = require('path');

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_KEY
);

exports.uploadPhoto = async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ error: 'No file uploaded' });
    }

    const { judul_foto, deskripsi_foto, album_id } = req.body;

    if (!judul_foto) {
      fs.unlinkSync(req.file.path);
      return res.status(400).json({ error: 'Photo title required' });
    }

    // Upload ke Supabase Storage
    const fileBuffer = fs.readFileSync(req.file.path);
    const fileName = `${Date.now()}-${req.file.originalname}`;
    
    const { data, error: uploadError } = await supabase.storage
      .from('photos')
      .upload(`${req.user.userId}/${fileName}`, fileBuffer, {
        contentType: req.file.mimetype
      });

    if (uploadError) {
      fs.unlinkSync(req.file.path);
      return res.status(500).json({ error: uploadError.message });
    }

    // Get public URL
    const { data: { publicUrl } } = supabase.storage
      .from('photos')
      .getPublicUrl(`${req.user.userId}/${fileName}`);

    // Save to database
    const tanggal_unggah = new Date().toISOString().split('T')[0];

    const result = await pool.query(
      `INSERT INTO gallery_foto (judul_foto, deskripsi_foto, tanggal_unggah, lokasi_file, album_id, user_id)
       VALUES ($1, $2, $3, $4, $5, $6) RETURNING *`,
      [judul_foto, deskripsi_foto || '', tanggal_unggah, publicUrl, album_id || null, req.user.userId]
    );

    // Delete temp file
    fs.unlinkSync(req.file.path);

    res.status(201).json({
      message: 'Photo uploaded successfully',
      foto: result.rows[0]
    });
  } catch (error) {
    if (req.file) fs.unlinkSync(req.file.path);
    res.status(500).json({ error: error.message });
  }
};
```

---

## 🌐 Langkah 5: Deploy Backend ke Vercel

### 5.1 Install Vercel CLI

```bash
npm install -g vercel
```

### 5.2 Create `server/vercel.json`

```json
{
  "version": 2,
  "builds": [
    {
      "src": "server.js",
      "use": "@vercel/node"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "server.js"
    }
  ],
  "env": {
    "NODE_ENV": "production",
    "DB_HOST": "@db_host",
    "DB_PORT": "@db_port",
    "DB_NAME": "@db_name",
    "DB_USER": "@db_user",
    "DB_PASSWORD": "@db_password",
    "JWT_SECRET": "@jwt_secret",
    "CLIENT_URL": "@client_url",
    "SUPABASE_URL": "@supabase_url",
    "SUPABASE_KEY": "@supabase_key"
  }
}
```

### 5.3 Deploy Backend

```bash
cd server

# Login ke Vercel
vercel login

# Deploy
vercel --prod

# Follow prompts:
# - Set project name: photo-gallery-api
# - Set as production: yes
# - Framework: Node.js
```

### 5.4 Add Environment Variables di Vercel

1. Buka [vercel.com](https://vercel.com) → Project Anda
2. Klik **Settings** → **Environment Variables**
3. Add semua variables:

```
DB_HOST = your-project.supabase.co
DB_PORT = 5432
DB_NAME = postgres
DB_USER = postgres
DB_PASSWORD = (supabase password)
JWT_SECRET = (generate kuat: openssl rand -base64 32)
CLIENT_URL = https://your-frontend-url.vercel.app
SUPABASE_URL = https://your-project.supabase.co
SUPABASE_KEY = (dari Supabase API Keys)
```

---

## 🎨 Langkah 6: Deploy Frontend ke Vercel

### 6.1 Update `client/.env.production`

```env
VITE_API_URL=https://your-api.vercel.app/api
```

### 6.2 Deploy Frontend

```bash
cd client

# Login ke Vercel (jika belum)
vercel login

# Deploy
vercel --prod

# Follow prompts:
# - Set project name: photo-gallery
# - Framework: Vue.js (Vite)
```

### 6.3 Configure Environment Variables

Di Vercel Dashboard:
1. Buka project frontend
2. Settings → Environment Variables
3. Add:

```
VITE_API_URL=https://your-api.vercel.app/api
```

4. Redeploy untuk apply changes

---

## ✅ Langkah 7: Testing Production

### Test Backend API

```bash
# Test health check
curl https://your-api.vercel.app/api/health

# Test register
curl -X POST https://your-api.vercel.app/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Test Frontend

1. Buka https://your-frontend.vercel.app
2. Test register & login
3. Test upload foto
4. Test like & comment

---

## 🔒 Security Checklist

- [ ] Change default Supabase password
- [ ] Enable SSL connections
- [ ] Add Vercel domain to CORS whitelist
- [ ] Generate strong JWT secret (min 32 chars)
- [ ] Enable Row Level Security (RLS) di Supabase tables
- [ ] Setup Rate Limiting di Vercel
- [ ] Enable HTTPS redirect
- [ ] Regular database backups

---

## 📊 Environment Variables Summary

### Server (.env)
```
DB_HOST=*.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=***
JWT_SECRET=*** (32+ chars)
CLIENT_URL=https://your-app.vercel.app
SUPABASE_URL=https://*.supabase.co
SUPABASE_KEY=***
```

### Client (.env.production)
```
VITE_API_URL=https://your-api.vercel.app/api
```

---

## 🚀 Deployment URLs

Setelah deploy, URLs Anda:

- **Frontend**: https://your-frontend.vercel.app
- **API**: https://your-api.vercel.app/api
- **Database**: Supabase (managed)
- **Storage**: Supabase Storage (managed)

---

## 📈 Monitor & Logs

### Vercel Logs
```bash
vercel logs https://your-api.vercel.app
```

### Supabase Logs
- Dashboard → Logs

---

## 💡 Tips & Tricks

1. **Auto-redeploy saat push ke GitHub**
   - Connect GitHub repo ke Vercel
   - Enable auto-deploy

2. **Database backups**
   - Supabase auto-backup (gratis)
   - Bisa set custom backup schedule

3. **Performance**
   - Use Supabase Realtime (optional)
   - Enable query caching
   - Use CDN untuk storage

4. **Cost**
   - Vercel: Free tier available
   - Supabase: Free tier available (500MB storage, 2GB bandwidth)
   - Upgrade as needed

---

## ❌ Troubleshooting

### Error: "Cannot connect to database"
- Check HOST, PORT, PASSWORD di .env
- Ensure Supabase project is active
- Check firewall/network access

### Error: "CORS error"
- Verify CLIENT_URL di backend .env
- Check VITE_API_URL di frontend .env
- Ensure API deployed correctly

### Error: "File upload fails"
- Check Supabase Storage bucket exists
- Verify API key has storage permission
- Check file size limits

### Error: "Token invalid"
- Login again
- Check JWT_SECRET matches backend
- Clear localStorage & cookies

---

## 📞 Helpful Resources

- [Supabase Docs](https://supabase.com/docs)
- [Vercel Docs](https://vercel.com/docs)
- [PostgreSQL vs MySQL](https://www.postgresql.org/about/comparison/mysql/)
- [Supabase Storage](https://supabase.com/docs/guides/storage)

---

**Selamat! Aplikasi Anda sudah live di production!** 🎉
