const { deleteComment } = require('../../controllers/commentController');
const { authMiddleware } = require('../../middleware/auth');

module.exports = async (req, res) => {
  const { komentarId } = req.query;

  if (!komentarId) {
    return res.status(400).json({ error: 'Comment ID required' });
  }

  // Add komentarId to req.params for controller compatibility
  req.params = { komentarId };

  if (req.method !== 'DELETE') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  // Apply auth middleware
  await new Promise((resolve, reject) => {
    authMiddleware(req, res, (err) => {
      if (err) reject(err);
      else resolve();
    });
  });

  await deleteComment(req, res);
};