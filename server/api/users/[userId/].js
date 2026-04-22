const { getUserProfile, updateProfile, deleteUser } = require('../../controllers/userController');
const { authMiddleware } = require('../../middleware/auth');

module.exports = async (req, res) => {
  const { userId } = req.query;

  if (!userId) {
    return res.status(400).json({ error: 'User ID required' });
  }

  // Add userId to req.params for controller compatibility
  req.params = { userId };

  switch (req.method) {
    case 'GET':
      await getUserProfile(req, res);
      break;

    case 'PUT':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await updateProfile(req, res);
      break;

    case 'DELETE':
      // Apply auth middleware
      await new Promise((resolve, reject) => {
        authMiddleware(req, res, (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
      await deleteUser(req, res);
      break;

    default:
      res.status(405).json({ error: 'Method not allowed' });
  }
};