const { getPhotoById, updatePhoto, deletePhoto } = require('../../controllers/photoController');
const { authMiddleware, optionalAuth } = require('../../middleware/auth');

module.exports = async (req, res) => {
  const { fotoId } = req.query;

  if (!fotoId) {
    return res.status(400).json({ error: 'Photo ID required' });
  }

  // Add fotoId to req.params for controller compatibility
  req.params = { fotoId };

  switch (req.method) {
    case 'GET':
      // Apply optional auth middleware
      await new Promise((resolve) => {
        optionalAuth(req, res, () => resolve());
      });
      await getPhotoById(req, res);
      break;

    case 'PUT':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await updatePhoto(req, res);
      break;

    case 'DELETE':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await deletePhoto(req, res);
      break;

    default:
      res.status(405).json({ error: 'Method not allowed' });
  }
};
    await new Promise((resolve, reject) => {
      authMiddleware(req, res, (err) => {
        if (err) reject(err);
        else resolve();
      });
    });
    await updatePhoto(req, res);
  } else if (req.method === 'DELETE') {
    // Apply auth middleware
    await new Promise((resolve, reject) => {
      authMiddleware(req, res, (err) => {
        if (err) reject(err);
        else resolve();
      });
    });
    await deletePhoto(req, res);
  } else {
    return res.status(405).json({ error: 'Method not allowed' });
  }
};