const pool = require('../config/supabase');

// Get all albums
exports.getAllAlbums = async (req, res) => {
  try {
    const result = await pool.query(
      `SELECT a.*, u.username,
              COUNT(DISTINCT f.id) as photo_count
       FROM gallery_album a
       LEFT JOIN gallery_user u ON a.user_id = u.id
       LEFT JOIN gallery_foto f ON a.id = f.album_id
       GROUP BY a.id, u.id
       ORDER BY a.nama_album`
    );

    res.json(result.rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get album by ID
exports.getAlbumById = async (req, res) => {
  try {
    const { albumId } = req.params;

    // Get album info
    const albumResult = await pool.query(
      `SELECT a.*, u.username,
              COUNT(DISTINCT f.id) as photo_count
       FROM gallery_album a
       LEFT JOIN gallery_user u ON a.user_id = u.id
       LEFT JOIN gallery_foto f ON a.id = f.album_id
       WHERE a.id = $1
       GROUP BY a.id, u.id`,
      [albumId]
    );

    if (albumResult.rows.length === 0) {
      return res.status(404).json({ error: 'Album not found' });
    }

    const album = albumResult.rows[0];

    // Get photos in album
    const photosResult = await pool.query(
      `SELECT f.*, u.username,
              COUNT(DISTINCT l.id) as like_count,
              COUNT(DISTINCT k.id) as comment_count
       FROM gallery_foto f
       LEFT JOIN gallery_user u ON f.user_id = u.id
       LEFT JOIN gallery_likefoto l ON f.id = l.foto_id
       LEFT JOIN gallery_komentarfoto k ON f.id = k.foto_id
       WHERE f.album_id = $1
       GROUP BY f.id, u.id
       ORDER BY f.tanggal_unggah DESC`,
      [albumId]
    );

    res.json({
      ...album,
      photos: photosResult.rows
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Create album
exports.createAlbum = async (req, res) => {
  try {
    const { nama_album, deskripsi } = req.body;

    if (!nama_album) {
      return res.status(400).json({ error: 'Album name required' });
    }

    const tanggal_dibuat = new Date().toISOString().split('T')[0];

    const result = await pool.query(
      `INSERT INTO gallery_album (nama_album, deskripsi, tanggal_dibuat, user_id)
       VALUES ($1, $2, $3, $4) RETURNING id`,
      [nama_album, deskripsi || '', tanggal_dibuat, req.user.userId]
    );

    res.status(201).json({
      message: 'Album created successfully',
      albumId: result.rows[0].id,
      album: {
        id: result.rows[0].id,
        nama_album: nama_album,
        deskripsi: deskripsi,
        tanggal_dibuat: tanggal_dibuat,
        user_id: req.user.userId
      }
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Update album
exports.updateAlbum = async (req, res) => {
  try {
    const { albumId } = req.params;
    const { nama_album, deskripsi } = req.body;

    const conn = await pool.getConnection();
    
    // Check ownership
    const [albums] = await conn.query(
      'SELECT * FROM gallery_album WHERE AlbumID = ?',
      [albumId]
    );

    if (albums.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Album not found' });
    }

    if (albums[0].UserID !== req.user.userId && req.user.level !== 'Admin') {
      conn.release();
      return res.status(403).json({ error: 'Unauthorized' });
    }

    await conn.query(
      'UPDATE gallery_album SET NamaAlbum = ?, Deskripsi = ? WHERE AlbumID = ?',
      [nama_album, deskripsi, albumId]
    );

    conn.release();

    res.json({ message: 'Album updated successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Delete album
exports.deleteAlbum = async (req, res) => {
  try {
    const { albumId } = req.params;

    const conn = await pool.getConnection();
    
    // Check ownership
    const [albums] = await conn.query(
      'SELECT * FROM gallery_album WHERE AlbumID = ?',
      [albumId]
    );

    if (albums.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Album not found' });
    }

    if (albums[0].UserID !== req.user.userId && req.user.level !== 'Admin') {
      conn.release();
      return res.status(403).json({ error: 'Unauthorized' });
    }

    await conn.query('DELETE FROM gallery_album WHERE AlbumID = ?', [albumId]);
    conn.release();

    res.json({ message: 'Album deleted successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get user albums
exports.getUserAlbums = async (req, res) => {
  try {
    const { userId } = req.params;

    const conn = await pool.getConnection();
    const [albums] = await conn.query(
      `SELECT a.*,
              COUNT(DISTINCT f.FotoID) as photo_count
       FROM gallery_album a
       LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID
       WHERE a.UserID = ?
       GROUP BY a.AlbumID
       ORDER BY a.NamaAlbum`,
      [userId]
    );

    conn.release();

    res.json(albums);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
