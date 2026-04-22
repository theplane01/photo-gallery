const pool = require('../config/supabase');

// Add comment
exports.addComment = async (req, res) => {
  try {
    const { fotoId } = req.params;
    const { isi_komentar } = req.body;

    if (!isi_komentar || isi_komentar.trim() === '') {
      return res.status(400).json({ error: 'Comment text required' });
    }

    const conn = await pool.getConnection();
    
    // Check if photo exists
    const [photos] = await conn.query('SELECT * FROM gallery_foto WHERE FotoID = ?', [fotoId]);
    if (photos.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Photo not found' });
    }

    const tanggal_komentar = new Date().toISOString().split('T')[0];

    const [result] = await conn.query(
      'INSERT INTO gallery_komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, ?)',
      [fotoId, req.user.userId, isi_komentar, tanggal_komentar]
    );

    conn.release();

    res.status(201).json({
      message: 'Comment added successfully',
      komentar_id: result.insertId
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get comments for photo
exports.getPhotoComments = async (req, res) => {
  try {
    const { fotoId } = req.params;
    const { page = 1, limit = 10 } = req.query;
    const offset = (page - 1) * limit;

    const conn = await pool.getConnection();
    
    // Get total count
    const [countResult] = await conn.query(
      'SELECT COUNT(*) as total FROM gallery_komentarfoto WHERE FotoID = ?',
      [fotoId]
    );

    // Get paginated comments
    const [comments] = await conn.query(
      `SELECT k.*, u.Username, u.UserID
       FROM gallery_komentarfoto k
       LEFT JOIN gallery_user u ON k.UserID = u.UserID
       WHERE k.FotoID = ?
       ORDER BY k.TanggalKomentar DESC
       LIMIT ? OFFSET ?`,
      [fotoId, parseInt(limit), offset]
    );

    conn.release();

    res.json({
      comments,
      pagination: {
        total: countResult[0].total,
        page: parseInt(page),
        limit: parseInt(limit),
        pages: Math.ceil(countResult[0].total / limit)
      }
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Delete comment
exports.deleteComment = async (req, res) => {
  try {
    const { komentarId } = req.params;

    const conn = await pool.getConnection();
    
    // Get comment
    const [comments] = await conn.query(
      'SELECT * FROM gallery_komentarfoto WHERE KomentarID = ?',
      [komentarId]
    );

    if (comments.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Comment not found' });
    }

    // Check ownership
    if (comments[0].UserID !== req.user.userId && req.user.level !== 'Admin') {
      conn.release();
      return res.status(403).json({ error: 'Unauthorized' });
    }

    await conn.query('DELETE FROM gallery_komentarfoto WHERE KomentarID = ?', [komentarId]);
    conn.release();

    res.json({ message: 'Comment deleted successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
