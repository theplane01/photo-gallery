const pool = require('../config/supabase');
const path = require('path');
const fs = require('fs');

// Get all photos with pagination
exports.getAllPhotos = async (req, res) => {
  try {
    const { search = '', page = 1, limit = 12 } = req.query;
    const offset = (page - 1) * limit;

    // Get total count
    const countResult = await pool.query(
      'SELECT COUNT(*) as total FROM gallery_foto WHERE judul_foto ILIKE $1',
      [`%${search}%`]
    );

    // Get paginated results
    const photosResult = await pool.query(
      `SELECT f.*, u.username, u.id as user_id, a.nama_album, a.id as album_id,
              COUNT(DISTINCT l.id) as like_count,
              COUNT(DISTINCT k.id) as comment_count
       FROM gallery_foto f
       LEFT JOIN gallery_user u ON f.user_id = u.id
       LEFT JOIN gallery_album a ON f.album_id = a.id
       LEFT JOIN gallery_likefoto l ON f.id = l.foto_id
       LEFT JOIN gallery_komentarfoto k ON f.id = k.foto_id
       WHERE f.judul_foto ILIKE $1
       GROUP BY f.id, u.id, a.id
       ORDER BY f.tanggal_unggah DESC
       LIMIT $2 OFFSET $3`,
      [`%${search}%`, parseInt(limit), offset]
    );

    res.json({
      photos: photosResult.rows,
      pagination: {
        total: parseInt(countResult.rows[0].total),
        page: parseInt(page),
        limit: parseInt(limit),
        pages: Math.ceil(countResult.rows[0].total / limit)
      }
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get photo by ID
exports.getPhotoById = async (req, res) => {
  try {
    const { fotoId } = req.params;

    const photoResult = await pool.query(
      `SELECT f.*, u.username, u.id as user_id, a.nama_album,
              COUNT(DISTINCT l.id) as like_count,
              COUNT(DISTINCT k.id) as comment_count
       FROM gallery_foto f
       LEFT JOIN gallery_user u ON f.user_id = u.id
       LEFT JOIN gallery_album a ON f.album_id = a.id
       LEFT JOIN gallery_likefoto l ON f.id = l.foto_id
       LEFT JOIN gallery_komentarfoto k ON f.id = k.foto_id
       WHERE f.id = $1
       GROUP BY f.id, u.id, a.id`,
      [fotoId]
    );

    if (photoResult.rows.length === 0) {
      return res.status(404).json({ error: 'Photo not found' });
    }

    res.json(photoResult.rows[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Upload photo
exports.uploadPhoto = async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ error: 'No file uploaded' });
    }

    const { judul_foto, deskripsi_foto, album_id } = req.body;

    if (!judul_foto) {
      fs.unlinkSync(req.file.path);
      return res.status(400).json({ error: 'Photo title required' });
    }
    
    // If album_id provided, verify ownership
    if (album_id) {
      const albumResult = await pool.query(
        'SELECT * FROM gallery_album WHERE id = $1 AND user_id = $2',
        [album_id, req.user.userId]
      );
      if (albumResult.rows.length === 0) {
        fs.unlinkSync(req.file.path);
        return res.status(403).json({ error: 'Album not found or unauthorized' });
      }
    }

    const tanggal_unggah = new Date().toISOString().split('T')[0];

    const result = await pool.query(
      `INSERT INTO gallery_foto (judul_foto, deskripsi_foto, tanggal_unggah, lokasi_file, album_id, user_id)
       VALUES ($1, $2, $3, $4, $5, $6) RETURNING id`,
      [judul_foto, deskripsi_foto || '', tanggal_unggah, req.file.filename, album_id || null, req.user.userId]
    );

    res.status(201).json({
      message: 'Photo uploaded successfully',
      fotoId: result.rows[0].id,
      foto: {
        id: result.rows[0].id,
        judul_foto: judul_foto,
        deskripsi_foto: deskripsi_foto,
        tanggal_unggah: tanggal_unggah,
        lokasi_file: req.file.filename,
        album_id: album_id,
        user_id: req.user.userId
      }
    });
  } catch (error) {
    if (req.file) fs.unlinkSync(req.file.path);
    res.status(500).json({ error: error.message });
  }
};

// Update photo
exports.updatePhoto = async (req, res) => {
  try {
    const { fotoId } = req.params;
    const { judul_foto, deskripsi_foto } = req.body;

    const conn = await pool.getConnection();
    
    // Check ownership
    const [photos] = await conn.query(
      'SELECT * FROM gallery_foto WHERE FotoID = ?',
      [fotoId]
    );

    if (photos.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Photo not found' });
    }

    if (photos[0].UserID !== req.user.userId && req.user.level !== 'Admin') {
      conn.release();
      return res.status(403).json({ error: 'Unauthorized' });
    }

    await conn.query(
      'UPDATE gallery_foto SET JudulFoto = ?, DeskripsiFoto = ? WHERE FotoID = ?',
      [judul_foto, deskripsi_foto, fotoId]
    );

    conn.release();

    res.json({ message: 'Photo updated successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Delete photo
exports.deletePhoto = async (req, res) => {
  try {
    const { fotoId } = req.params;

    const conn = await pool.getConnection();
    
    // Get photo info
    const [photos] = await conn.query(
      'SELECT * FROM gallery_foto WHERE FotoID = ?',
      [fotoId]
    );

    if (photos.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Photo not found' });
    }

    if (photos[0].UserID !== req.user.userId && req.user.level !== 'Admin') {
      conn.release();
      return res.status(403).json({ error: 'Unauthorized' });
    }

    // Delete from database
    await conn.query('DELETE FROM gallery_foto WHERE FotoID = ?', [fotoId]);

    conn.release();

    // Delete file
    const filePath = path.join(__dirname, '../uploads', photos[0].LokasiFile);
    if (fs.existsSync(filePath)) {
      fs.unlinkSync(filePath);
    }

    res.json({ message: 'Photo deleted successfully' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Get user photos
exports.getUserPhotos = async (req, res) => {
  try {
    const { userId } = req.params;

    const conn = await pool.getConnection();
    const [photos] = await conn.query(
      `SELECT f.*, u.Username, a.NamaAlbum,
              COUNT(DISTINCT l.LikeID) as like_count,
              COUNT(DISTINCT k.KomentarID) as comment_count
       FROM gallery_foto f
       LEFT JOIN gallery_user u ON f.UserID = u.UserID
       LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID
       LEFT JOIN gallery_likefoto l ON f.FotoID = l.FotoID
       LEFT JOIN gallery_komentarfoto k ON f.FotoID = k.FotoID
       WHERE f.UserID = ?
       GROUP BY f.FotoID
       ORDER BY f.TanggalUnggah DESC`,
      [userId]
    );

    conn.release();

    res.json(photos);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
