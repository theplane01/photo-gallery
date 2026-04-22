# PhotoGallery - Node.js & Vue.js Version

Aplikasi Photo Gallery modern yang dibangun dengan **Node.js** (Express) dan **Vue.js 3**.

## 📋 Struktur Project

```
Web-Gallery/
├── server/                 # Backend Node.js
│   ├── config/            # Database configuration
│   ├── controllers/       # Business logic
│   ├── middleware/        # Authentication middleware
│   ├── models/            # Database models
│   ├── routes/            # API routes
│   ├── uploads/           # User uploaded files
│   ├── package.json
│   ├── server.js          # Main server file
│   └── .env.example       # Environment variables template
│
└── client/                 # Frontend Vue.js
    ├── src/
    │   ├── components/    # Reusable components
    │   ├── pages/         # Page components
    │   ├── services/      # API services
    │   ├── stores/        # Pinia stores (state management)
    │   ├── App.vue
    │   ├── main.js
    │   └── router.js
    ├── index.html
    ├── package.json
    └── vite.config.js
```

## 🚀 Instalasi & Setup

### Backend Setup

1. Masuk ke folder server:
```bash
cd server
```

2. Install dependencies:
```bash
npm install
```

3. Buat file `.env`:
```bash
copy .env.example .env
```

4. Edit `.env` sesuai konfigurasi database Anda:
```
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gallery
JWT_SECRET=your_secret_key_here
```

5. Import database:
```sql
-- Jalankan file SQL dari document/database.txt di MySQL
```

6. Jalankan server:
```bash
npm run dev
```

Server akan berjalan di `http://localhost:5000`

### Frontend Setup

1. Buka terminal baru, masuk ke folder client:
```bash
cd client
```

2. Install dependencies:
```bash
npm install
```

3. Jalankan development server:
```bash
npm run dev
```

Aplikasi akan terbuka di `http://localhost:3000`

## 📚 Fitur

### Authentication
- Register user baru
- Login dengan JWT token
- Manajemen session

### Photo Management
- Upload foto
- Edit data foto
- Hapus foto
- Cari foto

### Albums
- Buat album baru
- Tambah foto ke album
- Edit album
- Hapus album

### Social Features
- Like/Unlike foto
- Comment di foto
- Delete comment
- Like counter & comment counter

### User Profile
- View user profile
- Edit profile
- Admin panel (untuk admin)

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - Daftar user baru
- `POST /api/auth/login` - Login
- `GET /api/auth/me` - Get current user (protected)

### Photos
- `GET /api/photos` - Get semua foto (with pagination & search)
- `GET /api/photos/:fotoId` - Get detail foto
- `POST /api/photos` - Upload foto (protected)
- `PUT /api/photos/:fotoId` - Update foto (protected)
- `DELETE /api/photos/:fotoId` - Delete foto (protected)
- `GET /api/photos/user/:userId` - Get foto dari user tertentu

### Albums
- `GET /api/albums` - Get semua album
- `GET /api/albums/:albumId` - Get detail album dengan foto-nya
- `POST /api/albums` - Create album (protected)
- `PUT /api/albums/:albumId` - Update album (protected)
- `DELETE /api/albums/:albumId` - Delete album (protected)
- `GET /api/albums/user/:userId` - Get album dari user tertentu

### Likes
- `POST /api/likes/:fotoId` - Toggle like (protected)
- `GET /api/likes/:fotoId/status` - Get like status (protected)
- `GET /api/likes/:fotoId` - Get semua like untuk foto

### Comments
- `POST /api/comments/:fotoId` - Add comment (protected)
- `GET /api/comments/:fotoId` - Get comments untuk foto
- `DELETE /api/comments/:komentarId` - Delete comment (protected)

### Users
- `GET /api/users` - Get semua user (admin only)
- `GET /api/users/:userId` - Get user profile
- `PUT /api/users/:userId` - Update profile (protected)
- `DELETE /api/users/:userId` - Delete user (admin only)

## 🛠 Technologies Used

### Backend
- **Express.js** - Web framework
- **MySQL2** - Database driver
- **JWT** - Authentication
- **Bcryptjs** - Password hashing
- **Multer** - File upload handling
- **CORS** - Cross-origin support
- **Dotenv** - Environment variables

### Frontend
- **Vue 3** - UI framework
- **Vue Router** - Routing
- **Pinia** - State management
- **Axios** - HTTP client
- **Vite** - Build tool

## 📝 Database Schema

### gallery_user
```sql
- UserID (PK)
- Username
- Email
- Password (hashed)
- NamaLengkap
- Alamat
- Level (Admin/User)
```

### gallery_album
```sql
- AlbumID (PK)
- NamaAlbum
- Deskripsi
- TanggalDibuat
- UserID (FK)
```

### gallery_foto
```sql
- FotoID (PK)
- JudulFoto
- DeskripsiFoto
- TanggalUnggah
- LokasiFile
- AlbumID (FK)
- UserID (FK)
```

### gallery_komentarfoto
```sql
- KomentarID (PK)
- FotoID (FK)
- UserID (FK)
- IsiKomentar
- TanggalKomentar
```

### gallery_likefoto
```sql
- LikeID (PK)
- FotoID (FK)
- UserID (FK)
- TanggalLike
```

## 🔐 Security Features

- JWT token-based authentication
- Password hashing dengan bcryptjs
- Input validation
- CORS protection
- File upload validation
- Authorization checks untuk protected routes

## 📱 Responsive Design

Aplikasi ini fully responsive dan dapat diakses di:
- Desktop browsers
- Tablet
- Mobile devices

## 🎨 UI/UX

- Modern gradient design
- Smooth animations
- Intuitive navigation
- Clean and organized layout
- User-friendly forms

## 📦 Build & Deployment

### Build frontend:
```bash
cd client
npm run build
```

Output akan berada di `client/dist/`

### Deploy
- Backend: Deploy Node.js server ke hosting (Heroku, Railway, Vercel, dll)
- Frontend: Deploy Vue.js app ke CDN/static hosting (Vercel, Netlify, GitHub Pages, dll)

## 🤝 Contributing

Feel free to fork dan contribute ke project ini!

## 📄 License

MIT License

---

**Happy Coding! 🚀**
