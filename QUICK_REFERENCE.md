# 📚 Quick Reference Guide

## 🚀 Start Development

### Option 1: Automated (Windows)
```bash
# Double-click
start.bat
```

### Option 2: Automated (macOS/Linux)
```bash
bash start.sh
```

### Option 3: Manual

**Terminal 1 - Backend:**
```bash
cd server
npm install
npm run dev
```

**Terminal 2 - Frontend:**
```bash
cd client
npm install
npm run dev
```

---

## 📍 URLs

| Service | URL |
|---------|-----|
| Frontend | http://localhost:3000 |
| Backend API | http://localhost:5000/api |
| Health Check | http://localhost:5000/api/health |
| Uploads | http://localhost:5000/uploads |

---

## 🗂️ Project Structure

```
server/                 ← Express.js backend
├── config/            ← Database config
├── controllers/       ← Business logic
├── middleware/        ← Auth middleware
├── routes/            ← API endpoints
├── uploads/           ← Uploaded files
└── server.js

client/                 ← Vue.js frontend
├── src/
│   ├── components/    ← Reusable components
│   ├── pages/         ← Page components
│   ├── services/      ← API calls
│   ├── stores/        ← State management
│   └── main.js
└── index.html
```

---

## 💻 Common Commands

### Backend
```bash
cd server

# Install dependencies
npm install

# Start development server
npm run dev

# View dependencies
npm list

# Update dependencies
npm update
```

### Frontend
```bash
cd client

# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

---

## 🔑 Environment Variables

### Server (.env)
```env
PORT=5000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gallery
JWT_SECRET=your_secret_here
CLIENT_URL=http://localhost:3000
```

### Client (.env.local)
```env
VITE_API_URL=http://localhost:5000/api
```

---

## 📊 Database

### Reset Database
```sql
-- Drop and recreate
DROP DATABASE gallery;
-- Import from document/database.txt
```

### Connect to MySQL
```bash
mysql -u root -p
USE gallery;
SELECT * FROM gallery_user;
```

---

## 🔐 JWT Token

### How it works
1. User logs in → Server returns token
2. Client stores in `localStorage`
3. Client sends in every request: `Authorization: Bearer token`
4. Server validates token

### Token stored in
```javascript
// client/src/stores/authStore.js
localStorage.setItem('token', token)
```

---

## 📝 Adding New Pages

### Step 1: Create Vue component
```bash
touch client/src/pages/MyNewPage.vue
```

### Step 2: Create content
```vue
<template>
  <div class="page">
    <h1>My New Page</h1>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const data = ref([])
</script>

<style scoped>
.page {
  padding: 2rem;
}
</style>
```

### Step 3: Add route
```javascript
// client/src/router.js
import MyNewPage from '../pages/MyNewPage.vue'

const routes = [
  // ...
  { path: '/my-new-page', component: MyNewPage }
]
```

---

## 📡 Adding New API Route

### Step 1: Create controller
```javascript
// server/controllers/myController.js
exports.myFunction = async (req, res) => {
  try {
    // Your logic here
    res.json({ message: 'Success' })
  } catch (error) {
    res.status(500).json({ error: error.message })
  }
}
```

### Step 2: Create route
```javascript
// server/routes/myRoute.js
const express = require('express')
const router = express.Router()
const { myFunction } = require('../controllers/myController')

router.get('/', myFunction)

module.exports = router
```

### Step 3: Add to server
```javascript
// server/server.js
app.use('/api/myroute', require('./routes/myRoute'))
```

---

## 🎨 Component Example

### Simple Component
```vue
<template>
  <div class="card">
    <h3>{{ title }}</h3>
    <p>{{ description }}</p>
    <button @click="handleClick">Click Me</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  title: String,
  description: String
})

const emit = defineEmits(['action'])

const handleClick = () => {
  emit('action')
}
</script>

<style scoped>
.card {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

button {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
</style>
```

---

## 🐛 Debugging

### Backend
```javascript
// Use console.log
console.log('Debug:', variable)

// Check error
console.error('Error:', error.message)

// View terminal output
// Errors appear in terminal where npm run dev is running
```

### Frontend
```javascript
// Browser DevTools Console
console.log('Debug:', variable)

// Vue DevTools extension
// Install: https://devtools.vuejs.org/

// Network tab
// Check API requests and responses
```

---

## ❌ Common Issues & Solutions

### Issue: Port already in use
```bash
# Linux/macOS
lsof -i :5000
kill -9 <PID>

# Windows
netstat -ano | findstr :5000
taskkill /PID <PID> /F
```

### Issue: npm ERR!
```bash
# Clear cache
npm cache clean --force

# Reinstall
rm -rf node_modules
npm install
```

### Issue: CORS error
```bash
# Check CLIENT_URL in server .env
# Ensure both servers running
# Check origin in API response headers
```

### Issue: Token invalid
```bash
# Login again
# Check JWT_SECRET in .env (don't change!)
# Clear localStorage in browser
```

---

## 📦 File Upload

### Supported formats
- JPEG, PNG, GIF, WebP

### Max size
- 5MB (configurable in `server/.env`)

### Location
- Stored in `server/uploads/`
- Accessed via `http://localhost:5000/uploads/filename`

---

## 🔗 API Usage in Vue

### Simple API call
```javascript
import { photoService } from '../services/index'

// Get data
const response = await photoService.getAllPhotos()
const photos = response.data.photos

// Post data
const result = await photoService.uploadPhoto(formData)

// Handle error
try {
  await photoService.deletePhoto(id)
} catch (error) {
  console.error(error.response.data.error)
}
```

---

## 📚 Useful Links

- [Vue 3 Docs](https://vuejs.org)
- [Express.js Docs](https://expressjs.com)
- [MySQL2 Docs](https://github.com/sidorares/node-mysql2)
- [JWT.io](https://jwt.io)
- [Axios Docs](https://axios-http.com)
- [Pinia Docs](https://pinia.vuejs.org)
- [Vue Router Docs](https://router.vuejs.org)

---

## 🎯 Development Tips

1. **Use VS Code extensions**
   - Vetur or Volar (Vue support)
   - REST Client (test API)
   - Thunder Client or REST

2. **Use Postman**
   - Test API endpoints
   - Save request collections
   - Environment variables

3. **Browser DevTools**
   - Network tab for API debugging
   - Console for errors
   - Vue DevTools for state inspection

4. **Keep code organized**
   - One component per file
   - Descriptive function names
   - Comments for complex logic

5. **Test on mobile**
   - Use device emulator
   - Check responsive design
   - Test touch interactions

---

## 🚀 Before Deploying

```bash
# Backend
cd server
npm run build (if applicable)

# Frontend
cd client
npm run build

# Test production build
npm run preview
```

---

**Need more help?** Check README.md, INSTALLATION.md, or API_EXAMPLES.md
