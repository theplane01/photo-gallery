const pool = require('../config/supabase');
const bcrypt = require('bcryptjs');

// Get all users (admin only)
exports.getAllUsers = async (req, res) => {
  try {
    if (req.user.level !== 'Admin') {
      return res.status(403).json({ error: 'Unauthorized' });
    }

    const conn = await pool.getConnection();
    const [users] = await conn.query('SELECT UserID, Username, Email, NamaLengkap, Level FROM gallery_user ORDER BY UserID DESC');
    conn.release();

    res.json(users);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get user profile
exports.getUserProfile = async (req, res) => {
  try {
    const { userId } = req.params;
    
    const conn = await pool.getConnection();
    const [users] = await conn.query(
      'SELECT UserID, Username, Email, NamaLengkap, Alamat FROM gallery_user WHERE UserID = ?',
      [userId]
    );
    conn.release();

    if (users.length === 0) {
      return res.status(404).json({ error: 'User not found' });
    }

    // Get user's photo count and album count
    const connStats = await pool.getConnection();
    const [stats] = await connStats.query(
      'SELECT COUNT(DISTINCT FotoID) as photo_count, COUNT(DISTINCT AlbumID) as album_count FROM gallery_foto WHERE UserID = ?',
      [userId]
    );
    connStats.release();

    const user = users[0];
    user.photo_count = stats[0].photo_count;
    user.album_count = stats[0].album_count;

    res.json(user);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Update profile
exports.updateProfile = async (req, res) => {
  try {
    const { userId } = req.params;
    const { nama_lengkap, alamat } = req.body;

    if (req.user.userId != userId && req.user.level !== 'Admin') {
      return res.status(403).json({ error: 'Unauthorized' });
    }

    const conn = await pool.getConnection();
    await conn.query(
      'UPDATE gallery_user SET NamaLengkap = ?, Alamat = ? WHERE UserID = ?',
      [nama_lengkap, alamat, userId]
    );
    conn.release();

    res.json({ message: 'Profile updated successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Delete user (admin only)
exports.deleteUser = async (req, res) => {
  try {
    if (req.user.level !== 'Admin') {
      return res.status(403).json({ error: 'Unauthorized' });
    }

    const { userId } = req.params;

    const conn = await pool.getConnection();
    await conn.query('DELETE FROM gallery_user WHERE UserID = ?', [userId]);
    conn.release();

    res.json({ message: 'User deleted successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
