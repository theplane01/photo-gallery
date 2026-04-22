# Photo Gallery - Node.js & Vue.js Conversion

Panduan lengkap untuk menjalankan Photo Gallery yang sudah dikonversi dari PHP ke Node.js dan Vue.js.

## 📂 Struktur Folder

```
Web-Gallery/
├── server/                 # Backend Node.js + Express
│   ├── config/
│   │   └── database.js     # Database connection
│   ├── controllers/        # Business logic untuk setiap route
│   │   ├── authController.js
│   │   ├── userController.js
│   │   ├── photoController.js
│   │   ├── albumController.js
│   │   ├── likeController.js
│   │   └── commentController.js
│   ├── middleware/
│   │   └── auth.js         # JWT authentication
│   ├── routes/             # API routes
│   │   ├── auth.js
│   │   ├── users.js
│   │   ├── photos.js
│   │   ├── albums.js
│   │   ├── likes.js
│   │   └── comments.js
│   ├── uploads/            # Direktori upload foto
│   ├── package.json
│   ├── server.js           # Entry point
│   └── .env                # Environment variables
│
├── client/                 # Frontend Vue.js 3
│   ├── src/
│   │   ├── components/     # Vue components
│   │   │   ├── Navbar.vue
│   │   │   └── PhotoCard.vue
│   │   ├── pages/          # Page components
│   │   │   ├── Home.vue
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── AddPhoto.vue
│   │   │   ├── PhotoDetail.vue
│   │   │   ├── MyAlbums.vue
│   │   │   ├── AlbumDetail.vue
│   │   │   ├── CreateAlbum.vue
│   │   │   └── Explore.vue
│   │   ├── services/       # API calls
│   │   │   ├── api.js      # Axios instance
│   │   │   └── index.js    # Service functions
│   │   ├── stores/         # Pinia state management
│   │   │   ├── authStore.js
│   │   │   ├── photoStore.js
│   │   │   └── albumStore.js
│   │   ├── App.vue
│   │   ├── main.js
│   │   └── router.js       # Vue Router
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
└── document/
    └── database.txt        # SQL untuk setup database
```

## 🎯 Quick Start

### 1. Setup Database

**MySQL Command:**
```bash
mysql -u root -p < document/database.txt
```

Atau manual:
1. Buka MySQL/phpMyAdmin
2. Import file `document/database.txt`

### 2. Setup Backend

```bash
cd server

# Install dependencies
npm install

# Buat .env file (sudah ada template)
# Edit .env jika perlu menyesuaikan database config
nano .env

# Jalankan server
npm run dev
```

Server akan jalan di `http://localhost:5000`

### 3. Setup Frontend

```bash
cd client

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

App akan buka di `http://localhost:3000`

## 🔑 Environment Variables

### Server (.env)

```env
# Server
PORT=5000
NODE_ENV=development

# Database
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gallery

# JWT
JWT_SECRET=your_super_secret_jwt_key_change_this_in_production
JWT_EXPIRE=7d

# File Upload
MAX_FILE_SIZE=5242880          # 5MB in bytes
UPLOAD_PATH=./uploads

# Client
CLIENT_URL=http://localhost:3000
```

## 📡 API Documentation

### Base URL
```
http://localhost:5000/api
```

### Authentication Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register user baru |
| POST | `/auth/login` | Login (return JWT token) |
| GET | `/auth/me` | Get current user info (protected) |

### Photo Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/photos` | Get all photos (paginated, searchable) |
| GET | `/photos/:fotoId` | Get single photo detail |
| POST | `/photos` | Upload new photo (protected) |
| PUT | `/photos/:fotoId` | Edit photo info (protected) |
| DELETE | `/photos/:fotoId` | Delete photo (protected) |
| GET | `/photos/user/:userId` | Get user's photos |

### Album Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/albums` | Get all albums |
| GET | `/albums/:albumId` | Get album with photos |
| POST | `/albums` | Create new album (protected) |
| PUT | `/albums/:albumId` | Edit album (protected) |
| DELETE | `/albums/:albumId` | Delete album (protected) |
| GET | `/albums/user/:userId` | Get user's albums |

### Like Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/likes/:fotoId` | Toggle like/unlike (protected) |
| GET | `/likes/:fotoId/status` | Get like status (protected) |
| GET | `/likes/:fotoId` | Get all likes for photo |

### Comment Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/comments/:fotoId` | Add comment (protected) |
| GET | `/comments/:fotoId` | Get comments (paginated) |
| DELETE | `/comments/:komentarId` | Delete comment (protected) |

### User Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/users` | Get all users (admin only) |
| GET | `/users/:userId` | Get user profile |
| PUT | `/users/:userId` | Update profile (protected) |
| DELETE | `/users/:userId` | Delete user (admin only) |

## 🔐 Authentication

Menggunakan JWT (JSON Web Tokens):

1. User login → server return token
2. Client simpan token di localStorage
3. Setiap request ke protected endpoint, sertakan header:
   ```
   Authorization: Bearer <token>
   ```

## 🎨 Frontend Features

- ✅ Responsive design
- ✅ Vue Router untuk navigation
- ✅ Pinia untuk state management
- ✅ Axios untuk API calls
- ✅ Modern UI dengan gradient colors
- ✅ Form validation
- ✅ Error handling
- ✅ Loading states
- ✅ Pagination

## 📝 File Upload

- **Format**: JPEG, PNG, GIF, WebP
- **Max Size**: 5MB
- **Direktori**: `server/uploads/`
- **URL Access**: `http://localhost:5000/uploads/filename`

## 🚀 Production Build

### Frontend

```bash
cd client
npm run build
```

Output di `client/dist/` - bisa dideploy ke Vercel, Netlify, GitHub Pages, dll

### Backend

Deploy ke hosting yang support Node.js:
- Heroku
- Railway
- Render
- AWS EC2
- DigitalOcean
- dll

## 🧪 Testing API

Gunakan Postman atau Thunder Client:

1. **Register**
   ```
   POST http://localhost:5000/api/auth/register
   Content-Type: application/json
   
   {
     "username": "user123",
     "email": "user@example.com",
     "password": "password123",
     "nama_lengkap": "John Doe"
   }
   ```

2. **Login**
   ```
   POST http://localhost:5000/api/auth/login
   Content-Type: application/json
   
   {
     "username": "user123",
     "password": "password123"
   }
   ```

3. **Get Photos**
   ```
   GET http://localhost:5000/api/photos?search=mountain&page=1&limit=12
   ```

## ❓ Troubleshooting

### Database Connection Error
- Pastikan MySQL running
- Check DB_HOST, DB_USER, DB_PASSWORD di .env
- Pastikan database `gallery` sudah dicreate

### CORS Error
- Check CLIENT_URL di server .env
- Pastikan frontend running di port yang benar

### File Upload Failed
- Check folder `server/uploads/` writable
- Pastikan file size < 5MB
- Format harus image (jpg, png, gif, webp)

### Token Invalid Error
- Login lagi untuk get token baru
- Check JWT_SECRET di .env (jangan ganti setelah ada token yang issue)

## 📚 Documentation Links

- [Vue 3 Docs](https://vuejs.org)
- [Express.js Docs](https://expressjs.com)
- [MySQL2 Docs](https://github.com/sidorares/node-mysql2)
- [JWT Docs](https://jwt.io)
- [Pinia Docs](https://pinia.vuejs.org)

## 👤 Account Info

Default testing account dapat dibuat melalui register page.

Untuk admin access, edit langsung di database:
```sql
UPDATE gallery_user SET Level='Admin' WHERE Username='username_anda';
```

## 📞 Support

Ada masalah? Check:
1. Terminal output untuk error messages
2. Browser console untuk frontend errors
3. Network tab untuk API responses
4. .env file untuk konfigurasi

---

**Selamat menggunakan PhotoGallery! 📸**
