const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const {
  getAllPhotos,
  getPhotoById,
  uploadPhoto,
  updatePhoto,
  deletePhoto,
  getUserPhotos
} = require('../controllers/photoController');
const { authMiddleware, optionalAuth } = require('../middleware/auth');

// Configure multer
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, path.join(__dirname, '../uploads'));
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB
  fileFilter: (req, file, cb) => {
    const allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowedMimes.includes(file.mimetype)) {
      cb(null, true);
    } else {
      cb(new Error('Invalid file type'));
    }
  }
});

// Get all photos
router.get('/', optionalAuth, getAllPhotos);

// Get photo by ID
router.get('/:fotoId', optionalAuth, getPhotoById);

// Upload photo
router.post('/', authMiddleware, upload.single('file'), uploadPhoto);

// Update photo
router.put('/:fotoId', authMiddleware, updatePhoto);

// Delete photo
router.delete('/:fotoId', authMiddleware, deletePhoto);

// Get user photos
router.get('/user/:userId', getUserPhotos);

module.exports = router;
