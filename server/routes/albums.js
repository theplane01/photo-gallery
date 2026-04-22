const express = require('express');
const router = express.Router();
const {
  getAllAlbums,
  getAlbumById,
  createAlbum,
  updateAlbum,
  deleteAlbum,
  getUserAlbums
} = require('../controllers/albumController');
const { authMiddleware } = require('../middleware/auth');

// Get all albums
router.get('/', getAllAlbums);

// Get album by ID
router.get('/:albumId', getAlbumById);

// Create album
router.post('/', authMiddleware, createAlbum);

// Update album
router.put('/:albumId', authMiddleware, updateAlbum);

// Delete album
router.delete('/:albumId', authMiddleware, deleteAlbum);

// Get user albums
router.get('/user/:userId', getUserAlbums);

module.exports = router;
