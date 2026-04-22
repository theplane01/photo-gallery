const express = require('express');
const router = express.Router();
const { addComment, getPhotoComments, deleteComment } = require('../controllers/commentController');
const { authMiddleware } = require('../middleware/auth');

// Add comment
router.post('/:fotoId', authMiddleware, addComment);

// Get comments for photo
router.get('/:fotoId', getPhotoComments);

// Delete comment
router.delete('/:komentarId', authMiddleware, deleteComment);

module.exports = router;
