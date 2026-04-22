const pool = require('../config/supabase');

// Toggle like
exports.toggleLike = async (req, res) => {
  try {
    const { fotoId } = req.params;

    const conn = await pool.getConnection();
    
    // Check if photo exists
    const [photos] = await conn.query('SELECT * FROM gallery_foto WHERE FotoID = ?', [fotoId]);
    if (photos.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Photo not found' });
    }

    // Check if user already liked
    const [likes] = await conn.query(
      'SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?',
      [fotoId, req.user.userId]
    );

    const tanggal_like = new Date().toISOString().split('T')[0];

    if (likes.length > 0) {
      // Unlike
      await conn.query(
        'DELETE FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?',
        [fotoId, req.user.userId]
      );
      conn.release();
      res.json({ message: 'Photo unliked', liked: false });
    } else {
      // Like
      await conn.query(
        'INSERT INTO gallery_likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, ?)',
        [fotoId, req.user.userId, tanggal_like]
      );
      conn.release();
      res.json({ message: 'Photo liked', liked: true });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get like status
exports.getLikeStatus = async (req, res) => {
  try {
    const { fotoId } = req.params;

    const conn = await pool.getConnection();
    const [likes] = await conn.query(
      'SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?',
      [fotoId, req.user.userId]
    );

    const [stats] = await conn.query(
      'SELECT COUNT(*) as count FROM gallery_likefoto WHERE FotoID = ?',
      [fotoId]
    );

    conn.release();

    res.json({
      liked: likes.length > 0,
      like_count: stats[0].count
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get likes for photo
exports.getPhotosLikes = async (req, res) => {
  try {
    const { fotoId } = req.params;

    const conn = await pool.getConnection();
    const [likes] = await conn.query(
      `SELECT l.*, u.Username, u.UserID
       FROM gallery_likefoto l
       LEFT JOIN gallery_user u ON l.UserID = u.UserID
       WHERE l.FotoID = ?
       ORDER BY l.TanggalLike DESC`,
      [fotoId]
    );

    conn.release();

    res.json(likes);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
