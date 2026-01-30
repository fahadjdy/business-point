<template>
  <div class="register-wrapper">
    <div class="register-card">
      <div class="register-header">
        <router-link to="/">
            <h1 class="brand">{{ settings.brand_name }}</h1>
        </router-link>
        <p class="subtitle">Create your {{ settings.brand_name }} account to get started.</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label>Full Name <span class="required">*</span></label>
          <div class="input-with-icon">
            <i class="fa-solid fa-user"></i>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="John Doe" 
              required
              :disabled="loading"
            >
          </div>
          <div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div>
        </div>

        <div class="form-group">
          <label>Email Address <span class="required">*</span></label>
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
          <div class="form-note">This will be used for login</div>
          <div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div>
        </div>

        <div class="form-group">
          <label>Mobile Number</label>
          <div class="input-with-icon">
            <i class="fa-solid fa-phone"></i>
            <input 
              v-model="form.phone" 
              type="tel" 
              placeholder="+1 234 567 8900" 
              :disabled="loading"
            >
          </div>
          <div v-if="errors.phone" class="error-message">{{ errors.phone[0] }}</div>
        </div>

        <div class="form-group">
          <label>Password <span class="required">*</span></label>
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
          <div v-if="errors.password" class="error-message">{{ errors.password[0] }}</div>
        </div>

        <div class="form-group">
          <label>Confirm Password <span class="required">*</span></label>
          <div class="input-with-icon">
            <i class="fa-solid fa-lock"></i>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              placeholder="••••••••" 
              required
              :disabled="loading"
            >
          </div>
          <div v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation[0] }}</div>
        </div>

        <div class="form-options">
          <label class="checkbox-container">
            <input type="checkbox" v-model="form.agree_terms" required>
            <span class="checkmark"></span>
            I agree to the <a href="#" class="terms-link">Terms & Conditions</a>
          </label>
        </div>

        <BaseButton 
          type="submit" 
          variant="primary" 
          :loading="loading" 
          class="w-100"
        >
          Create Account
        </BaseButton>

        <div class="login-prompt">
          Already have an account? <router-link to="/login">Sign in</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useConfigStore } from '../../store/config';
import BaseButton from '../../Components/BaseButton.vue';
import axios from 'axios';
import swal from '../../utils/swal';

const router = useRouter();
const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const loading = ref(false);
const errors = ref({});

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  agree_terms: false
});

onMounted(() => {
    configStore.fetchSettings();
});

const handleRegister = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
        const response = await axios.post('/api/v1/auth/register', {
            name: form.name,
            email: form.email,
            phone: form.phone,
            password: form.password,
            password_confirmation: form.password_confirmation
        }, {
            headers: { 'X-Api-Key': 'business-point-secret-key' }
        });
        
        if (response.data.success) {
            swal.fire({
                title: 'Registration Successful!',
                text: 'Your account has been created successfully. Please login to continue.',
                icon: 'success',
                confirmButtonText: 'Go to Login'
            }).then(() => {
                router.push('/login');
            });
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            swal.fire({
                title: 'Registration Failed',
                text: error.response?.data?.message || 'Something went wrong. Please try again.',
                icon: 'error'
            });
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.register-wrapper {
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

.register-card {
  width: 100%;
  max-width: 450px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.05);
  padding: 40px;
}

.register-header {
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

.register-form {
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

.required {
  color: #f64e60;
}

.form-note {
  font-size: 0.8rem;
  color: #7e8299;
  margin-top: 4px;
  font-style: italic;
}

.error-message {
  font-size: 0.8rem;
  color: #f64e60;
  margin-top: 4px;
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

.input-with-icon input.error {
  border-color: #f64e60;
}

.form-options {
  display: flex;
  justify-content: flex-start;
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

.terms-link {
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
}

.w-100 { width: 100%; }

.login-prompt {
  text-align: center;
  margin-top: 10px;
  font-size: 0.9rem;
  color: #7e8299;
}

.login-prompt a {
  color: #3699ff;
  font-weight: 600;
  text-decoration: none;
}
</style>