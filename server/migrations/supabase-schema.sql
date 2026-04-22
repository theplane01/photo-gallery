-- PostgreSQL Schema untuk Supabase
-- Import file ini ke Supabase SQL Editor

-- 1. Create gallery_user table
CREATE TABLE IF NOT EXISTS gallery_user (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(255),
    alamat TEXT,
    level VARCHAR(50) NOT NULL DEFAULT 'User' CHECK (level IN ('Admin', 'User')),
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create gallery_album table
CREATE TABLE IF NOT EXISTS gallery_album (
    id SERIAL PRIMARY KEY,
    nama_album VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal_dibuat DATE DEFAULT CURRENT_DATE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Create gallery_foto table
CREATE TABLE IF NOT EXISTS gallery_foto (
    id SERIAL PRIMARY KEY,
    judul_foto VARCHAR(255) NOT NULL,
    deskripsi_foto TEXT,
    tanggal_unggah DATE DEFAULT CURRENT_DATE,
    lokasi_file VARCHAR(255) NOT NULL,
    album_id INTEGER REFERENCES gallery_album(id) ON DELETE SET NULL,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Create gallery_komentarfoto table
CREATE TABLE IF NOT EXISTS gallery_komentarfoto (
    id SERIAL PRIMARY KEY,
    foto_id INTEGER NOT NULL REFERENCES gallery_foto(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    isi_komentar TEXT NOT NULL,
    tanggal_komentar DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Create gallery_likefoto table
CREATE TABLE IF NOT EXISTS gallery_likefoto (
    id SERIAL PRIMARY KEY,
    foto_id INTEGER NOT NULL REFERENCES gallery_foto(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES gallery_user(id) ON DELETE CASCADE,
    tanggal_like DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(foto_id, user_id)
);

-- 6. Create indexes untuk performance
CREATE INDEX IF NOT EXISTS idx_user_username ON gallery_user(username);
CREATE INDEX IF NOT EXISTS idx_user_email ON gallery_user(email);
CREATE INDEX IF NOT EXISTS idx_foto_user ON gallery_foto(user_id);
CREATE INDEX IF NOT EXISTS idx_foto_album ON gallery_foto(album_id);
CREATE INDEX IF NOT EXISTS idx_album_user ON gallery_album(user_id);
CREATE INDEX IF NOT EXISTS idx_comment_foto ON gallery_komentarfoto(foto_id);
CREATE INDEX IF NOT EXISTS idx_comment_user ON gallery_komentarfoto(user_id);
CREATE INDEX IF NOT EXISTS idx_like_foto ON gallery_likefoto(foto_id);
CREATE INDEX IF NOT EXISTS idx_like_user ON gallery_likefoto(user_id);

-- 7. Enable RLS (Row Level Security) - optional tapi recommended
-- ALTER TABLE gallery_user ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE gallery_album ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE gallery_foto ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE gallery_komentarfoto ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE gallery_likefoto ENABLE ROW LEVEL SECURITY;

-- Success!
-- Sekarang update backend untuk gunakan PostgreSQL
