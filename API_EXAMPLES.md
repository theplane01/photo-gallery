# API Request Examples

## Base URL
```
http://localhost:5000/api
```

## Authentication

### Register
```bash
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john_doe",
    "email": "john@example.com",
    "password": "password123",
    "nama_lengkap": "John Doe"
  }'
```

### Login
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john_doe",
    "password": "password123"
  }'
```

Response:
```json
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "userId": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "nama_lengkap": "John Doe",
    "level": "User"
  }
}
```

### Get Current User
```bash
curl -X GET http://localhost:5000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Photos

### Get All Photos (with pagination and search)
```bash
curl -X GET "http://localhost:5000/api/photos?search=mountain&page=1&limit=12"
```

### Get Photo Detail
```bash
curl -X GET http://localhost:5000/api/photos/1
```

### Upload Photo
```bash
curl -X POST http://localhost:5000/api/photos \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@/path/to/photo.jpg" \
  -F "judul_foto=Mountain View" \
  -F "deskripsi_foto=Beautiful mountain landscape" \
  -F "album_id=1"
```

### Update Photo
```bash
curl -X PUT http://localhost:5000/api/photos/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "judul_foto": "Updated Title",
    "deskripsi_foto": "Updated description"
  }'
```

### Delete Photo
```bash
curl -X DELETE http://localhost:5000/api/photos/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get User Photos
```bash
curl -X GET http://localhost:5000/api/photos/user/1
```

## Albums

### Get All Albums
```bash
curl -X GET http://localhost:5000/api/albums
```

### Get Album Detail
```bash
curl -X GET http://localhost:5000/api/albums/1
```

### Create Album
```bash
curl -X POST http://localhost:5000/api/albums \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_album": "Vacation 2024",
    "deskripsi": "Photos from our summer vacation"
  }'
```

### Update Album
```bash
curl -X PUT http://localhost:5000/api/albums/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_album": "Updated Album Name",
    "deskripsi": "Updated description"
  }'
```

### Delete Album
```bash
curl -X DELETE http://localhost:5000/api/albums/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get User Albums
```bash
curl -X GET http://localhost:5000/api/albums/user/1
```

## Likes

### Toggle Like
```bash
curl -X POST http://localhost:5000/api/likes/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Response:
```json
{
  "message": "Photo liked",
  "liked": true
}
```

### Get Like Status
```bash
curl -X GET http://localhost:5000/api/likes/1/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Response:
```json
{
  "liked": true,
  "like_count": 42
}
```

### Get Photo Likes
```bash
curl -X GET http://localhost:5000/api/likes/1
```

## Comments

### Add Comment
```bash
curl -X POST http://localhost:5000/api/comments/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "isi_komentar": "Beautiful photo!"
  }'
```

### Get Comments
```bash
curl -X GET "http://localhost:5000/api/comments/1?page=1&limit=10"
```

### Delete Comment
```bash
curl -X DELETE http://localhost:5000/api/comments/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Users

### Get All Users (Admin Only)
```bash
curl -X GET http://localhost:5000/api/users \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Get User Profile
```bash
curl -X GET http://localhost:5000/api/users/1
```

### Update Profile
```bash
curl -X PUT http://localhost:5000/api/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama_lengkap": "John Doe Updated",
    "alamat": "123 Main St"
  }'
```

### Delete User (Admin Only)
```bash
curl -X DELETE http://localhost:5000/api/users/1 \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

---

## Tips

1. Ganti `YOUR_TOKEN` dengan token yang didapat dari login
2. Untuk test, bisa gunakan Postman, Thunder Client, atau curl
3. Pastikan server running sebelum test API
4. Check Content-Type header untuk setiap request
5. Untuk file upload, gunakan form-data bukan JSON
