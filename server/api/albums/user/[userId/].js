const { getUserAlbums } = require('../../../controllers/albumController');

module.exports = async (req, res) => {
  const { userId } = req.query;

  if (!userId) {
    return res.status(400).json({ error: 'User ID required' });
  }

  // Add userId to req.params for controller compatibility
  req.params = { userId };

  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  await getUserAlbums(req, res);
};