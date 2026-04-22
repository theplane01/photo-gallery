<template>
  <div class="register-page">
    <!-- Background Animation -->
    <div class="floating-bg">
      <div class="float-shape shape-1"></div>
      <div class="float-shape shape-2"></div>
      <div class="float-shape shape-3"></div>
    </div>

    <!-- Register Container -->
    <div class="register-wrapper">
      <div class="register-container">
        <!-- Header -->
        <div class="form-header">
          <div class="logo-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <h1>Create Account</h1>
          <p>Join PhotoGallery and start sharing</p>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="handleRegister" class="register-form">
          <!-- Username Input -->
          <div class="form-group">
            <label>
              <span class="label-text">Username</span>
              <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input 
                  v-model="form.username" 
                  type="text" 
                  placeholder="Choose your username"
                  required 
                  class="form-input"
                />
              </div>
            </label>
          </div>

          <!-- Email Input -->
          <div class="form-group">
            <label>
              <span class="label-text">Email Address</span>
              <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input 
                  v-model="form.email" 
                  type="email" 
                  placeholder="Enter your email"
                  required 
                  class="form-input"
                />
              </div>
            </label>
          </div>

          <!-- Full Name Input -->
          <div class="form-group">
            <label>
              <span class="label-text">Full Name</span>
              <div class="input-wrapper">
                <i class="fas fa-id-card"></i>
                <input 
                  v-model="form.nama_lengkap" 
                  type="text" 
                  placeholder="Your full name"
                  class="form-input"
                />
              </div>
            </label>
          </div>

          <!-- Password Input -->
          <div class="form-group">
            <label>
              <span class="label-text">Password</span>
              <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input 
                  v-model="form.password" 
                  type="password" 
                  placeholder="Create a strong password"
                  required 
                  class="form-input"
                />
              </div>
            </label>
          </div>

          <!-- Terms Agreement -->
          <div class="form-checkbox">
            <input type="checkbox" id="terms" required />
            <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-submit">
            <i class="fas fa-user-check"></i> Create My Account
          </button>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          {{ error }}
        </div>

        <!-- Success Message -->
        <div v-if="success" class="success-message">
          <i class="fas fa-check-circle"></i>
          {{ success }}
        </div>

        <!-- Login Link -->
        <div class="form-footer">
          <p>Already have an account?</p>
          <router-link to="/login" class="link-login">
            Sign in instead <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>
      </div>

      <!-- Side Illustration -->
      <div class="register-illustration">
        <div class="illustration-content">
          <h2>Welcome to<br/>PhotoGallery</h2>
          <p>Create your account and unlock a world of photography</p>
          <div class="benefits">
            <div class="benefit-item">
              <div class="benefit-icon">
                <i class="fas fa-star"></i>
              </div>
              <h3>Unlimited Uploads</h3>
              <p>Share as many photos as you want</p>
            </div>
            <div class="benefit-item">
              <div class="benefit-icon">
                <i class="fas fa-users"></i>
              </div>
              <h3>Community</h3>
              <p>Connect with photographers worldwide</p>
            </div>
            <div class="benefit-item">
              <div class="benefit-icon">
                <i class="fas fa-folder-plus"></i>
              </div>
              <h3>Organize</h3>
              <p>Create albums and manage your gallery</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '@/services/index.js'

const router = useRouter()
const form = ref({ username: '', email: '', password: '', nama_lengkap: '' })
const error = ref('')
const success = ref('')

const handleRegister = async () => {
  try {
    error.value = ''
    success.value = ''
    await authService.register(form.value)
    success.value = 'Registration successful! Redirecting to login...'
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (err) {
    error.value = err.response?.data?.error || 'Registration failed'
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  position: relative;
  overflow: hidden;
}

/* Floating Background */
.floating-bg {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.float-shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.08;
  animation: float 8s ease-in-out infinite;
}

.shape-1 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, #3b82f6, #8b5cf6);
  top: -100px;
  left: -100px;
}

.shape-2 {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, #06b6d4, #3b82f6);
  bottom: -50px;
  right: -50px;
  animation-delay: 3s;
}

.shape-3 {
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, #8b5cf6, #06b6d4);
  top: 50%;
  right: -100px;
  animation-delay: 5s;
}

/* Register Wrapper */
.register-wrapper {
  position: relative;
  z-index: 10;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  max-width: 1200px;
  width: 100%;
  align-items: center;
}

/* Register Container */
.register-container {
  background: rgba(30, 41, 59, 0.8);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 2rem;
  padding: 3rem;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

/* Form Header */
.form-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #10b981, #06b6d4);
  border-radius: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  margin: 0 auto 1rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
}

.form-header h1 {
  font-size: 1.75rem;
  color: white;
  margin: 0 0 0.5rem 0;
}

.form-header p {
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
  font-size: 0.95rem;
}

/* Form */
.register-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.label-text {
  display: block;
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper i {
  position: absolute;
  left: 1rem;
  color: rgba(59, 130, 246, 0.6);
  font-size: 1.1rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 3rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  color: white;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
}

.form-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.form-input:focus {
  outline: none;
  border-color: rgba(59, 130, 246, 0.6);
  background: rgba(59, 130, 246, 0.05);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Checkbox */
.form-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

.form-checkbox input {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #3b82f6;
}

.form-checkbox label {
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
}

.form-checkbox a {
  color: #3b82f6;
  text-decoration: none;
  transition: color 0.3s;
}

.form-checkbox a:hover {
  color: #8b5cf6;
}

/* Submit Button */
.btn-submit {
  width: 100%;
  padding: 0.9rem;
  background: linear-gradient(135deg, #10b981, #06b6d4);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(6, 182, 212, 0.3);
}

.btn-submit:active {
  transform: translateY(0);
}

/* Messages */
.error-message,
.success-message {
  padding: 1rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.error-message {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.5);
  color: #fca5a5;
}

.success-message {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.5);
  color: #86efac;
}

/* Footer */
.form-footer {
  text-align: center;
}

.form-footer p {
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
}

.link-login {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  transition: all 0.3s;
}

.link-login:hover {
  gap: 0.5rem;
  color: #8b5cf6;
}

/* Illustration */
.register-illustration {
  display: flex;
  align-items: center;
  justify-content: center;
}

.illustration-content {
  text-align: center;
  animation: fadeInUp 0.8s ease-out;
}

.illustration-content h2 {
  font-size: 2.5rem;
  color: white;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.illustration-content p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.benefits {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.benefit-item {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  padding: 1.5rem;
  text-align: center;
}

.benefit-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #10b981, #06b6d4);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  margin: 0 auto 0.75rem;
}

.benefit-item h3 {
  font-size: 1rem;
  color: white;
  margin: 0 0 0.5rem 0;
}

.benefit-item p {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
  margin: 0;
}

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-30px);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive */
@media (max-width: 1024px) {
  .register-wrapper {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .register-illustration {
    display: none;
  }

  .register-container {
    max-width: 450px;
    margin: 0 auto;
  }
}

@media (max-width: 640px) {
  .register-container {
    padding: 2rem;
    border-radius: 1.5rem;
  }

  .form-header h1 {
    font-size: 1.5rem;
  }

  .register-wrapper {
    padding: 0 1rem;
  }

  .illustration-content h2 {
    font-size: 2rem;
  }
}
</style>
