const { getAlbumById, updateAlbum, deleteAlbum } = require('../../controllers/albumController');
const { authMiddleware } = require('../../middleware/auth');

module.exports = async (req, res) => {
  const { albumId } = req.query;

  if (!albumId) {
    return res.status(400).json({ error: 'Album ID required' });
  }

  // Add albumId to req.params for controller compatibility
  req.params = { albumId };

  switch (req.method) {
    case 'GET':
      await getAlbumById(req, res);
      break;

    case 'PUT':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await updateAlbum(req, res);
      break;

    case 'DELETE':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await deleteAlbum(req, res);
      break;

    default:
      res.status(405).json({ error: 'Method not allowed' });
  }
};