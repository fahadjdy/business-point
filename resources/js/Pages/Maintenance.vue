<template>
  <div class="maintenance-page">
    <div class="maintenance-container">
      <!-- Brand Logo Section -->
      <div class="brand-section">
        <div class="brand-logo">
          <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.brand_name" />
          <i v-else class="fa-solid fa-cube text-primary"></i>
        </div>
        <h1 class="brand-name">{{ settings.brand_name || 'Business Point' }}</h1>
        <p v-if="settings.brand_tagline" class="brand-tagline">{{ settings.brand_tagline }}</p>
      </div>

      <!-- Maintenance Message -->
      <div class="maintenance-content">
        <div class="maintenance-icon">
          <i class="fa-solid fa-tools"></i>
        </div>
        <h2 class="maintenance-title">We're Under Maintenance</h2>
        <p class="maintenance-message">
          {{ settings.maintenance_note || 'We are currently performing scheduled maintenance. Please check back later.' }}
        </p>
        
        <!-- Contact Information -->
        <div class="contact-info" v-if="settings.app_email || settings.app_phone">
          <p class="contact-text">Need immediate assistance? Contact us:</p>
          <div class="contact-details">
            <a v-if="settings.app_email" :href="`mailto:${settings.app_email}`" class="contact-link">
              <i class="fa-solid fa-envelope"></i>
              {{ settings.app_email }}
            </a>
            <a v-if="settings.app_phone" :href="`tel:${settings.app_phone}`" class="contact-link">
              <i class="fa-solid fa-phone"></i>
              {{ settings.app_phone }}
            </a>
          </div>
        </div>

        <!-- Refresh Button -->
        <button @click="checkMaintenanceStatus" class="refresh-btn" :disabled="checking">
          <i class="fa-solid fa-refresh" :class="{ 'fa-spin': checking }"></i>
          {{ checking ? 'Checking...' : 'Check Again' }}
        </button>
      </div>

      <!-- Footer -->
      <div class="maintenance-footer">
        <p>&copy; {{ new Date().getFullYear() }} {{ settings.brand_name || 'Business Point' }}. All rights reserved.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useConfigStore } from '../store/config';
import AppService from '../services/AppService';

const router = useRouter();
const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const checking = ref(false);

const checkMaintenanceStatus = async () => {
  checking.value = true;
  try {
    // Fetch fresh settings to check maintenance status
    await configStore.fetchSettings(true);
    
    // If maintenance mode is disabled, redirect to home
    if (!settings.value.maintenance_mode) {
      router.push('/');
    }
  } catch (error) {
    console.error('Failed to check maintenance status:', error);
  } finally {
    checking.value = false;
  }
};

onMounted(() => {
  // Fetch settings on component mount
  configStore.fetchSettings();
});
</script>

<style scoped>
.maintenance-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: 'Inter', sans-serif;
}

.maintenance-container {
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
  padding: 60px 40px;
  text-align: center;
  max-width: 600px;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.maintenance-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3699ff, #06d6a0, #ffd23f, #f72585);
}

/* Brand Section */
.brand-section {
  margin-bottom: 40px;
}

.brand-logo {
  width: 80px;
  height: 80px;
  background: #f8f9fa;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.brand-logo img {
  max-width: 60px;
  max-height: 60px;
  object-fit: contain;
}

.brand-logo i {
  font-size: 2.5rem;
  color: #3699ff;
}

.brand-name {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1e1e2d;
  margin: 0 0 10px;
  letter-spacing: -0.5px;
}

.brand-tagline {
  font-size: 1.1rem;
  color: #7e8299;
  margin: 0;
  font-weight: 500;
}

/* Maintenance Content */
.maintenance-content {
  margin-bottom: 40px;
}

.maintenance-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #ffd23f, #ff6b35);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 30px;
  box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
}

.maintenance-icon i {
  font-size: 2.5rem;
  color: white;
}

.maintenance-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1e1e2d;
  margin: 0 0 20px;
}

.maintenance-message {
  font-size: 1.1rem;
  color: #5e6278;
  line-height: 1.6;
  margin: 0 0 30px;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

/* Contact Information */
.contact-info {
  background: #f8f9fa;
  border-radius: 15px;
  padding: 25px;
  margin: 30px 0;
}

.contact-text {
  font-size: 1rem;
  color: #5e6278;
  margin: 0 0 15px;
  font-weight: 600;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

.contact-link {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.2s;
  padding: 8px 15px;
  border-radius: 8px;
}

.contact-link:hover {
  background: #e1f0ff;
  transform: translateY(-1px);
}

.contact-link i {
  width: 20px;
  text-align: center;
}

/* Refresh Button */
.refresh-btn {
  background: linear-gradient(135deg, #3699ff, #06d6a0);
  color: white;
  border: none;
  padding: 15px 30px;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0 auto;
  box-shadow: 0 8px 25px rgba(54, 153, 255, 0.3);
}

.refresh-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(54, 153, 255, 0.4);
}

.refresh-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.refresh-btn i.fa-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Footer */
.maintenance-footer {
  border-top: 1px solid #eff2f5;
  padding-top: 20px;
  margin-top: 20px;
}

.maintenance-footer p {
  color: #b5b5c3;
  font-size: 0.9rem;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .maintenance-container {
    padding: 40px 20px;
    margin: 10px;
  }

  .brand-name {
    font-size: 2rem;
  }

  .maintenance-title {
    font-size: 1.5rem;
  }

  .maintenance-message {
    font-size: 1rem;
  }

  .contact-details {
    flex-direction: column;
  }

  .refresh-btn {
    padding: 12px 25px;
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .maintenance-page {
    padding: 10px;
  }

  .maintenance-container {
    padding: 30px 15px;
  }

  .brand-logo {
    width: 60px;
    height: 60px;
  }

  .brand-logo img {
    max-width: 40px;
    max-height: 40px;
  }

  .brand-logo i {
    font-size: 2rem;
  }

  .maintenance-icon {
    width: 80px;
    height: 80px;
  }

  .maintenance-icon i {
    font-size: 2rem;
  }
}

/* Utility Classes */
.text-primary {
  color: #3699ff;
}
</style>