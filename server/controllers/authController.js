const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const pool = require('../config/supabase');

// Register
exports.register = async (req, res) => {
  try {
    const { username, email, password, nama_lengkap } = req.body;

    // Validation
    if (!username || !email || !password) {
      return res.status(400).json({ error: 'Username, email, and password required' });
    }

    // Check existing user
    const { rows: users } = await pool.query('SELECT * FROM gallery_user WHERE username = $1 OR email = $2', [username, email]);
    
    if (users.length > 0) {
      return res.status(400).json({ error: 'Username or email already exists' });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Insert user
    await pool.query(
      'INSERT INTO gallery_user (username, email, password, nama_lengkap, level) VALUES ($1, $2, $3, $4, $5)',
      [username, email, hashedPassword, nama_lengkap || '', 'User']
    );

    res.status(201).json({ 
      message: 'User registered successfully',
      user: { username, email, nama_lengkap }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: error.message });
  }
};

// Login
exports.login = async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ error: 'Username and password required' });
    }

    const { rows: users } = await pool.query('SELECT * FROM gallery_user WHERE username = $1', [username]);
    
    if (users.length === 0) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const user = users[0];
    const isPasswordValid = await bcrypt.compare(password, user.password);

    if (!isPasswordValid) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    // Generate JWT
    const token = jwt.sign(
      { userId: user.id, username: user.username, level: user.level },
      process.env.JWT_SECRET || 'secret',
      { expiresIn: process.env.JWT_EXPIRE || '7d' }
    );

    res.json({
      message: 'Login successful',
      token,
      user: {
        userId: user.id,
        username: user.username,
        email: user.email,
        nama_lengkap: user.nama_lengkap,
        level: user.level
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: error.message });
  }
};

// Get current user
exports.getCurrentUser = async (req, res) => {
  try {
    const conn = await pool.getConnection();
    const [users] = await conn.query('SELECT UserID, Username, Email, NamaLengkap, Level FROM gallery_user WHERE UserID = ?', [req.user.userId]);
    conn.release();

    if (users.length === 0) {
      return res.status(404).json({ error: 'User not found' });
    }

    res.json(users[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};
