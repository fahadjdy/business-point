<template>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-header">
        <router-link to="/">
            <h1 class="brand">{{ settings.brand_name }}</h1>
        </router-link>
        <p class="subtitle">Welcome back! Please login to your {{ settings.brand_name }} account.</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>Email Address</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-envelope"></i>
            <input 
              v-model="form.email" 
              type="email" 
              placeholder="name@company.com" 
              required
              :disabled="loading"
            >
          </div>
        </div>

        <div class="form-group">
          <div class="label-row">
            <label>Password</label>
            <a href="#" class="forgot-link">Forgot?</a>
          </div>
          <div class="input-with-icon">
            <i class="fa-solid fa-lock"></i>
            <input 
              v-model="form.password" 
              type="password" 
              placeholder="••••••••" 
              required
              :disabled="loading"
            >
          </div>
        </div>

        <div class="form-options">
          <label class="checkbox-container">
            <input type="checkbox" v-model="form.remember">
            <span class="checkmark"></span>
            Remember me
          </label>
        </div>

        <BaseButton 
          type="submit" 
          variant="primary" 
          :loading="loading" 
          class="w-100"
        >
          Sign In
        </BaseButton>

        <div class="register-prompt" v-if="settings.allow_self_registration">
          Don't have an account? <router-link to="/register">Create one</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../../store/auth';
import { useConfigStore } from '../../store/config';
import BaseButton from '../../Components/BaseButton.vue';
import swal from '../../utils/swal';

const router = useRouter();
const { login } = useAuthStore();
const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const loading = ref(false);

const form = reactive({
  email: '',
  password: '',
  remember: false
});

onMounted(() => {
    configStore.fetchSettings();
});

const handleLogin = async () => {
    loading.value = true;
    const success = await login({
        login: form.email,
        password: form.password
    }, 'user');
    
    if (success) {
        router.push('/');
    }
    loading.value = false;
};
</script>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f8fa;
  background-image: radial-gradient(#3699ff 1px, transparent 1px);
  background-size: 40px 40px;
  background-attachment: fixed;
  padding: 20px;
}

.login-card {
  width: 100%;
  max-width: 450px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.05);
  padding: 40px;
}

.login-header {
  text-align: center;
  margin-bottom: 35px;
}

.brand {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1e1e2d;
  margin-bottom: 10px;
  text-decoration: none;
}

.subtitle {
  color: #b5b5c3;
  font-weight: 500;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group label {
  display: block;
  font-size: 0.9rem;
  font-weight: 600;
  color: #3f4254;
  margin-bottom: 8px;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-link {
  font-size: 0.8rem;
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
}

.input-with-icon {
  position: relative;
}

.input-with-icon i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #b5b5c3;
}

.input-with-icon input {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  background: #f9f9fc;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.input-with-icon input:focus {
  border-color: #3699ff;
  background: #fff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.85rem;
  color: #7e8299;
  cursor: pointer;
  user-select: none;
}

.w-100 { width: 100%; }

.register-prompt {
  text-align: center;
  margin-top: 10px;
  font-size: 0.9rem;
  color: #7e8299;
}

.register-prompt a {
  color: #3699ff;
  font-weight: 600;
  text-decoration: none;
}
</style>
