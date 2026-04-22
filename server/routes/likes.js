const express = require('express');
const router = express.Router();
const { toggleLike, getLikeStatus, getPhotosLikes } = require('../controllers/likeController');
const { authMiddleware } = require('../middleware/auth');

// Toggle like
router.post('/:fotoId', authMiddleware, toggleLike);

// Get like status
router.get('/:fotoId/status', authMiddleware, getLikeStatus);

// Get all likes for a photo
router.get('/:fotoId', getPhotosLikes);

module.exports = router;
