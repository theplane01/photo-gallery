# 📚 Supabase Quick Reference

Panduan lengkap menggunakan Supabase untuk Photo Gallery.

## 🔑 Supabase Credentials

Setelah create project, simpan info ini:

```
Project Name: photo-gallery
Project URL:  https://xxxxx.supabase.co
API Key:      xxxxx-xxxxx-xxxxx
DB Password:  xxxxxx
```

## 📊 Database Credentials

```
Host:     xxxxx.supabase.co
Port:     5432
Database: postgres
User:     postgres
Password: (from setup)
```

## 🗄️ Tables Structure

### gallery_user
```sql
id (SERIAL PRIMARY KEY)
username (VARCHAR UNIQUE)
email (VARCHAR UNIQUE)
password (VARCHAR hashed)
nama_lengkap (VARCHAR)
alamat (TEXT)
level (VARCHAR: 'Admin' or 'User')
created_at (TIMESTAMP)
```

### gallery_album
```sql
id (SERIAL PRIMARY KEY)
nama_album (VARCHAR)
deskripsi (TEXT)
tanggal_dibuat (DATE)
user_id (FK → gallery_user)
```

### gallery_foto
```sql
id (SERIAL PRIMARY KEY)
judul_foto (VARCHAR)
deskripsi_foto (TEXT)
tanggal_unggah (DATE)
lokasi_file (VARCHAR - URL dari Storage)
album_id (FK → gallery_album)
user_id (FK → gallery_user)
```

### gallery_komentarfoto
```sql
id (SERIAL PRIMARY KEY)
foto_id (FK → gallery_foto)
user_id (FK → gallery_user)
isi_komentar (TEXT)
tanggal_komentar (DATE)
```

### gallery_likefoto
```sql
id (SERIAL PRIMARY KEY)
foto_id (FK → gallery_foto)
user_id (FK → gallery_user)
tanggal_like (DATE)
UNIQUE(foto_id, user_id)
```

## 📁 Storage Structure

```
photos/
├── 1/
│   ├── image-1.jpg
│   ├── image-2.png
├── 2/
│   ├── photo-1.jpg
│   └── photo-2.jpg
...
```

Format: `photos/{user_id}/{filename}`

## 🔐 Row Level Security (RLS)

Optional tapi recommended untuk security:

```sql
-- Allow users to read all photos
CREATE POLICY "Allow read all photos"
ON gallery_foto FOR SELECT
USING (true);

-- Allow users to insert own photos
CREATE POLICY "Allow insert own photos"
ON gallery_foto FOR INSERT
WITH CHECK (auth.uid()::integer = user_id);

-- Sama untuk album, comments, likes
```

## 🚀 API Keys Needed

### Anon Public Key
- Digunakan dari client (browser)
- Read + Insert permissions saja
- **Jangan simpan secret key di frontend!**

### Service Role Key
- Digunakan dari backend
- Full permissions
- Keep secret!

### URLs di .env

```env
# Backend
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=public_key_dari_settings
SUPABASE_SERVICE_ROLE_KEY=secret_key_dari_settings

# Frontend
VITE_SUPABASE_URL=https://your-project.supabase.co
VITE_SUPABASE_ANON_KEY=public_key
```

## 🔗 Connection String

Untuk tools eksternal (DBeaver, MySQL Workbench, etc):

```
postgresql://postgres:PASSWORD@HOST:5432/postgres?sslmode=require
```

Contoh:
```
postgresql://postgres:mypassword@abc123.supabase.co:5432/postgres?sslmode=require
```

## 💾 Backup & Export

### Automated Backups
- Supabase auto-backup setiap hari
- Retention: 7 hari (free tier)
- Akses di: Database → Backups

### Manual Export
```bash
# Via pgdump
pg_dump "postgresql://user:password@host:5432/db" > backup.sql

# Via Supabase CLI
supabase db dump --file backup.sql
```

### Import Data
```bash
# Restore dari backup
psql "postgresql://user:password@host:5432/db" < backup.sql
```

## 🆘 Common Issues

### Connection Timeout
- Check firewall settings
- Ensure SSL is enabled
- Check region is correct

### Permission Denied
- Check API key
- Verify user role
- Check RLS policies

### Storage Upload Failed
- Verify bucket exists
- Check file size (max based on tier)
- Check MIME type allowed

### Query Performance
- Add indexes (already done in schema)
- Use EXPLAIN to analyze
- Consider pagination

## 📈 Monitoring

### Database Health
- Dashboard → Monitoring
- Check slow queries
- Monitor storage usage
- Check bandwidth

### API Usage
- Dashboard → Usage
- Track invocations
- Monitor rate limits

### Logs
- Dashboard → Logs
- Filter by level (info/error/warning)
- Real-time streaming

## 🛡️ Security Best Practices

1. **Never expose Service Role Key**
   - Only use in backend
   - Rotate regularly
   - Use different keys for different apps

2. **Use Row Level Security**
   - Restrict data access by user
   - Prevent data leaks
   - Automatic security at DB level

3. **Validate on Backend**
   - Don't trust client-side validation
   - Check permissions server-side
   - Sanitize inputs

4. **Use HTTPS Only**
   - Enable in browser settings
   - Force redirect from HTTP

5. **Rate Limiting**
   - Implement on API
   - Prevent brute force attacks
   - Protect against DDoS

## 💡 Tips & Tricks

1. **Real-time Subscriptions** (Premium)
   - Listen to database changes
   - Update UI automatically
   - ```javascript
     const subscription = supabase
       .from('gallery_foto')
       .on('*', payload => {
         console.log('New photo:', payload.new)
       })
       .subscribe()
     ```

2. **Vector Search** (Premium)
   - For image search
   - Using pgvector extension
   - Requires setup

3. **Full-Text Search**
   - Search across columns
   - ```sql
     SELECT * FROM gallery_foto 
     WHERE judul_foto @@ plainto_tsquery('query')
     ```

4. **Webhooks**
   - Trigger external events
   - Send emails on photo upload
   - Integrate with other services

## 📞 Support

- [Supabase Docs](https://supabase.com/docs)
- [Community Discord](https://discord.supabase.io)
- [Status Page](https://status.supabase.io)
- [GitHub Issues](https://github.com/supabase/supabase/issues)

---

**Keep your credentials safe!** 🔐
