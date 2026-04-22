const express = require('express');
const router = express.Router();
const { getAllUsers, getUserProfile, updateProfile, deleteUser } = require('../controllers/userController');
const { authMiddleware } = require('../middleware/auth');

// Get all users (admin)
router.get('/', authMiddleware, getAllUsers);

// Get user profile
router.get('/:userId', getUserProfile);

// Update profile
router.put('/:userId', authMiddleware, updateProfile);

// Delete user (admin)
router.delete('/:userId', authMiddleware, deleteUser);

module.exports = router;
