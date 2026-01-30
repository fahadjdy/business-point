<template>
  <div class="login-page">
    <div class="login-card pulsate-in">
      <div class="login-header">
        <h1 class="logo">Business Point</h1>
        <p>Admin Login</p>
      </div>

      <BaseAlert v-if="state.error" type="danger" icon="fa-solid fa-circle-exclamation">
        {{ state.error }}
      </BaseAlert>

      <form @submit.prevent="handleLogin">
        <BaseInput
          v-model="form.email"
          label="Email Address"
          type="email"
          placeholder="admin@example.com"
          icon="fa-solid fa-envelope"
          required
        />
        <BaseInput
          v-model="form.password"
          label="Password"
          type="password"
          placeholder="••••••••"
          icon="fa-solid fa-lock"
          required
        />

        <div class="form-options">
          <BaseCheckbox v-model="form.remember" label="Remember me" />
          <a href="#" class="forgot-link">Forgot Password?</a>
        </div>

        <BaseButton 
          type="submit" 
          variant="primary" 
          class="w-100 mt-4" 
          :loading="state.loading"
        >
          Sign In
        </BaseButton>
      </form>

      <div class="login-footer">
        <p>&copy; 2026 BP Team. All rights reserved.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../store/auth';
import BaseInput from '../../Components/BaseInput.vue';
import BaseButton from '../../Components/BaseButton.vue';
import BaseAlert from '../../Components/BaseAlert.vue';
import BaseCheckbox from '../../Components/BaseCheckbox.vue';

const router = useRouter();
const { state, login } = useAuthStore();

const form = reactive({
  email: '',
  password: '',
  remember: false
});

const handleLogin = async () => {
  const success = await login({
    email: form.email,
    password: form.password
  }, 'admin');

  if (success) {
    router.push({ name: 'admin.dashboard' });
  }
};
</script>

<style scoped>
.login-page {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f8fa;
  background-image: radial-gradient(#3699ff11 1px, transparent 1px);
  background-size: 20px 20px;
}

.login-card {
  width: 100%;
  max-width: 450px;
  background: #fff;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}

.login-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo {
  font-size: 2rem;
  font-weight: 800;
  color: #1e1e2d;
  margin: 0 0 10px;
}

.login-header p {
  color: #b5b5c3;
  font-weight: 500;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: -10px;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #7e8299;
  cursor: pointer;
}

.forgot-link {
  color: #3699ff;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
}

.login-footer {
  margin-top: 40px;
  text-align: center;
  color: #b5b5c3;
  font-size: 0.85rem;
}

.w-100 { width: 100%; }
.mt-4 { margin-top: 24px; }

.pulsate-in {
  animation: pulsate-in 0.5s ease;
}

@keyframes pulsate-in {
  0% { opacity: 0; transform: scale(0.95); }
  100% { opacity: 1; transform: scale(1); }
}
</style>
