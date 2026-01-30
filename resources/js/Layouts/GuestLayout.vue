<template>
  <div class="guest-layout">
    <!-- Header -->
    <header class="guest-header">
      <div class="container">
        <div class="header-content">
          <router-link to="/" class="brand-link">
            <div class="brand-logo">
               <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.brand_name" style="height: 30px; width: auto;" />
               <i v-else class="fa-solid fa-cube text-primary"></i>
            </div>
            <span class="brand-text">{{ settings.brand_name || 'Business Point' }}</span>
          </router-link>
          
          <nav class="guest-nav">
            <router-link to="/" class="nav-link">Home</router-link>
            <router-link to="/community-directory" class="nav-link">Community Directory</router-link>
            <router-link to="/announcements" class="nav-link">Announcements</router-link>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="guest-main">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="guest-footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-brand">
            <div class="brand-logo">
               <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.brand_name" style="height: 24px; width: auto;" />
               <i v-else class="fa-solid fa-cube text-primary"></i>
            </div>
            <span class="brand-text">{{ settings.brand_name || 'Business Point' }}</span>
          </div>
          
          <div class="footer-links">
            <router-link to="/" class="footer-link">Home</router-link>
            <router-link to="/community-directory" class="footer-link">Community Directory</router-link>
            <router-link to="/announcements" class="footer-link">Announcements</router-link>
          </div>
          
          <div class="footer-bottom">
            <p>&copy; 2026 {{ settings.brand_name }}. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useConfigStore } from '../store/config';

const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

onMounted(() => {
    configStore.fetchSettings();
});
</script>

<style scoped>
.guest-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f8fafd;
}

/* Header */
.guest-header {
  background: white;
  border-bottom: 1px solid #eff2f5;
  padding: 20px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.5rem;
  color: #1e1e2d;
}

.brand-logo {
  width: 40px;
  height: 40px;
  background: #f3f6f9;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.text-primary { 
  color: #3699ff; 
}

.guest-nav {
  display: flex;
  gap: 30px;
}

.nav-link {
  text-decoration: none;
  color: #5e6278;
  font-weight: 600;
  font-size: 1rem;
  transition: color 0.2s;
}

.nav-link:hover {
  color: #3699ff;
}

/* Main Content */
.guest-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
}

/* Footer */
.guest-footer {
  background: white;
  border-top: 1px solid #eff2f5;
  padding: 40px 0 20px;
  margin-top: auto;
}

.footer-content {
  text-align: center;
}

.footer-brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 20px;
  font-weight: 700;
  font-size: 1.2rem;
  color: #1e1e2d;
}

.footer-brand .brand-logo {
  width: 32px;
  height: 32px;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-bottom: 20px;
}

.footer-link {
  text-decoration: none;
  color: #7e8299;
  font-weight: 500;
  transition: color 0.2s;
}

.footer-link:hover {
  color: #3699ff;
}

.footer-bottom {
  padding-top: 20px;
  border-top: 1px solid #f1f3f8;
}

.footer-bottom p {
  margin: 0;
  color: #b5b5c3;
  font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 20px;
  }
  
  .guest-nav {
    gap: 20px;
  }
  
  .footer-links {
    flex-direction: column;
    gap: 15px;
  }
  
  .guest-main {
    padding: 20px 0;
  }
}
</style>