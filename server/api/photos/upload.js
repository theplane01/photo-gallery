const { uploadPhoto } = require('../../controllers/photoController');
const { authMiddleware } = require('../../middleware/auth');
const multer = require('multer');
const path = require('path');

// Configure multer for memory storage (for serverless)
const storage = multer.memoryStorage();
const upload = multer({
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB
  fileFilter: (req, file, cb) => {
    const allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowedMimes.includes(file.mimetype)) {
      cb(null, true);
    } else {
      cb(new Error('Invalid file type'));
    }
  }
});

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  // Apply auth middleware
  await new Promise((resolve, reject) => {
    authMiddleware(req, res, (err) => {
      if (err) reject(err);
      else resolve();
    });
  });

  // Handle file upload
  upload.single('file')(req, res, async (err) => {
    if (err) {
      return res.status(400).json({ error: err.message });
    }

    // Save file to disk (for now)
    if (req.file) {
      const fs = require('fs');
      const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
      const filename = uniqueSuffix + path.extname(req.file.originalname);
      const filepath = path.join(__dirname, '../../uploads', filename);

      fs.writeFileSync(filepath, req.file.buffer);
      req.file.filename = filename;
      req.file.path = filepath;
    }

    await uploadPhoto(req, res);
  });
};