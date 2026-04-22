const { getCurrentUser } = require('../../controllers/authController');
const { authMiddleware } = require('../../middleware/auth');

module.exports = async (req, res) => {
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  // Apply auth middleware
  await new Promise((resolve, reject) => {
    authMiddleware(req, res, (err) => {
      if (err) reject(err);
      else resolve();
    });
  });

  await getCurrentUser(req, res);
};