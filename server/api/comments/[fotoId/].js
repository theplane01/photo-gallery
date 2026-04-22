const { addComment, getPhotoComments } = require('../../controllers/commentController');
const { authMiddleware } = require('../../middleware/auth');

module.exports = async (req, res) => {
  const { fotoId } = req.query;

  if (!fotoId) {
    return res.status(400).json({ error: 'Photo ID required' });
  }

  // Add fotoId to req.params for controller compatibility
  req.params = { fotoId };

  switch (req.method) {
    case 'POST':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await addComment(req, res);
      break;

    case 'GET':
      await getPhotoComments(req, res);
      break;

    default:
      res.status(405).json({ error: 'Method not allowed' });
  }
};