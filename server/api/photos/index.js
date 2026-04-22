const { getAllPhotos, uploadPhoto } = require('../../controllers/photoController');
const { authMiddleware, optionalAuth } = require('../../middleware/auth');

module.exports = async (req, res) => {
  switch (req.method) {
    case 'GET':
      // Apply optional auth middleware
      await new Promise((resolve) => {
        optionalAuth(req, res, () => resolve());
      });
      await getAllPhotos(req, res);
      break;

    case 'POST':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await uploadPhoto(req, res);
      break;

    default:
      res.status(405).json({ error: 'Method not allowed' });
  }
};