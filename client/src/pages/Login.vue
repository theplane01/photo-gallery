<template>
  <div class="login-page">
    <!-- Background Animation -->
    <div class="floating-bg">
      <div class="float-shape shape-1"></div>
      <div class="float-shape shape-2"></div>
      <div class="float-shape shape-3"></div>
    </div>

    <!-- Login Container -->
    <div class="login-wrapper">
      <div class="login-container">
        <!-- Header -->
        <div class="form-header">
          <div class="logo-icon">
            <i class="fas fa-camera"></i>
          </div>
          <h1>Welcome Back</h1>
          <p>Sign in to your PhotoGallery account</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="login-form">
          <!-- Username Input -->
          <div class="form-group">
            <label>
              <span class="label-text">Username</span>
              <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input 
                  v-model="form.username" 
                  type="text" 
                  placeholder="Enter your username"
                  required 
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
                  placeholder="Enter your password"
                  required 
                  class="form-input"
                />
              </div>
            </label>
          </div>

          <!-- Remember Me -->
          <div class="form-checkbox">
            <input type="checkbox" id="remember" />
            <label for="remember">Remember me</label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-submit">
            <i class="fas fa-sign-in-alt"></i> Sign In
          </button>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          {{ error }}
        </div>

        <!-- Register Link -->
        <div class="form-footer">
          <p>Don't have an account?</p>
          <router-link to="/register" class="link-register">
            Create one now <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>

        <!-- Divider -->
        <div class="divider">
          <span>or continue as guest</span>
        </div>

        <!-- Guest Link -->
        <router-link to="/" class="btn-guest">
          <i class="fas fa-globe"></i> Explore Gallery
        </router-link>
      </div>

      <!-- Side Illustration -->
      <div class="login-illustration">
        <div class="illustration-content">
          <h2>Share Your Moments</h2>
          <p>Join thousands of photographers capturing and sharing beautiful photos</p>
          <ul class="features-list">
            <li><i class="fas fa-check"></i> Easy to upload photos</li>
            <li><i class="fas fa-check"></i> Create albums</li>
            <li><i class="fas fa-check"></i> Connect with creators</li>
            <li><i class="fas fa-check"></i> Discover amazing content</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore.js'
import { authService } from '@/services/index.js'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ username: '', password: '' })
const error = ref('')

const handleLogin = async () => {
  try {
    error.value = ''
    const response = await authService.login(form.value)
    authStore.setToken(response.data.token)
    authStore.setUser(response.data.user)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.error || 'Login failed'
  }
}
</script>

<style scoped>
.login-page {
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

/* Login Wrapper */
.login-wrapper {
  position: relative;
  z-index: 10;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  max-width: 1200px;
  width: 100%;
  align-items: center;
}

/* Login Container */
.login-container {
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
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  margin: 0 auto 1rem;
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
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
.login-form {
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
  font-size: 0.9rem;
}

/* Submit Button */
.btn-submit {
  width: 100%;
  padding: 0.9rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(139, 92, 246, 0.3);
}

.btn-submit:active {
  transform: translateY(0);
}

/* Error Message */
.error-message {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.5);
  color: #fca5a5;
  padding: 1rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

/* Footer */
.form-footer {
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-footer p {
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
}

.link-register {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  transition: all 0.3s;
}

.link-register:hover {
  gap: 0.5rem;
  color: #8b5cf6;
}

/* Divider */
.divider {
  position: relative;
  text-align: center;
  margin: 1.5rem 0;
}

.divider::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
}

.divider span {
  position: relative;
  background: rgba(30, 41, 59, 0.8);
  padding: 0 1rem;
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.85rem;
}

/* Guest Button */
.btn-guest {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-guest:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
}

/* Illustration */
.login-illustration {
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
  background: linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.illustration-content p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 0;
  text-align: left;
  display: inline-block;
}

.features-list li {
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.features-list i {
  color: #10b981;
  font-weight: bold;
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
  .login-wrapper {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .login-illustration {
    display: none;
  }

  .login-container {
    max-width: 450px;
    margin: 0 auto;
  }
}

@media (max-width: 640px) {
  .login-container {
    padding: 2rem;
    border-radius: 1.5rem;
  }

  .form-header h1 {
    font-size: 1.5rem;
  }

  .login-wrapper {
    padding: 0 1rem;
  }

  .illustration-content h2 {
    font-size: 2rem;
  }
}
</style>
