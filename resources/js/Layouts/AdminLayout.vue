<template>
  <div class="admin-layout">
    <!-- Mobile Overlay -->
    <div v-if="isMobileOpen" class="mobile-overlay" @click="isMobileOpen = false"></div>

    <!-- Sidebar -->
    <!-- Global Notification -->
    <BaseNotification />

    <aside class="sidebar" :class="{ 'collapsed': isCollapsed, 'mobile-open': isMobileOpen }">
      <div class="sidebar-logo">
        <router-link to="/admin/dashboard" class="brand-link-admin">
            <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.brand_name" class="sidebar-logo-img" />
            <h2 v-if="!isCollapsed || isMobileOpen" class="ml-2">{{ settings.brand_name }}</h2>
            <h2 v-else class="ml-2">BP</h2>
        </router-link>
      </div>
      
      <nav class="sidebar-nav">
        <router-link to="/admin/dashboard" class="nav-item" active-class="active">
          <i class="fa-solid fa-gauge-high"></i>
          <span v-if="!isCollapsed || isMobileOpen">Dashboard</span>
        </router-link>
        <router-link to="/admin/vendors" class="nav-item" :class="{ 'active': $route.path.startsWith('/admin/vendors') }">
          <i class="fa-solid fa-store"></i>
          <span v-if="!isCollapsed || isMobileOpen">Vendors</span>
        </router-link>
        <router-link to="/admin/audit-logs" class="nav-item" active-class="active">
          <i class="fa-solid fa-list-check"></i>
          <span v-if="!isCollapsed || isMobileOpen">Audit Logs</span>
        </router-link>
        <router-link to="/admin/notifications" class="nav-item" active-class="active">
          <i class="fa-solid fa-bullhorn"></i>
          <span v-if="!isCollapsed || isMobileOpen">Announcements</span>
        </router-link>
        <router-link to="/admin/emergency-contacts" class="nav-item" active-class="active">
          <i class="fa-solid fa-phone-flip"></i>
          <span v-if="!isCollapsed || isMobileOpen">Emergency Contacts</span>
        </router-link>
        <router-link to="/admin/community-directory" class="nav-item" active-class="active">
          <i class="fa-solid fa-address-book"></i>
          <span v-if="!isCollapsed || isMobileOpen">Community Directory</span>
        </router-link>
        <router-link to="/admin/settings" class="nav-item" active-class="active">
          <i class="fa-solid fa-sliders"></i>
          <span v-if="!isCollapsed || isMobileOpen">Settings</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <button @click="isCollapsed = !isCollapsed" class="collapse-btn desktop-only">
          <i class="fa-solid" :class="isCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-container">
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <button @click="isMobileOpen = !isMobileOpen" class="mobile-toggle btn-icon mr-3">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div class="header-title">
            <h1 class="page-title">{{ title }}</h1>
            <Breadcrumb :items="breadcrumbs" />
          </div>
        </div>
        <div class="header-right">
        <div class="user-profile" :class="{ 'active': isDropdownOpen }">
            <div class="profile-trigger" @click="toggleDropdown">
              <div class="user-info">
                <span class="user-name">{{ state.admin?.name || 'Administrator' }}</span>
                <span class="user-badge" v-if="state.admin">Super Admin</span>
              </div>
              <div class="user-avatar overflow-hidden">
                <img v-if="state.admin?.photo_url" :src="state.admin.photo_url" class="avatar-thumbnail" />
                <span v-else>{{ state.admin?.name?.charAt(0).toUpperCase() || 'A' }}</span>
              </div>
            </div>
            
            <div v-show="isDropdownOpen" class="user-dropdown">
               <router-link to="/admin/profile" class="dropdown-item" @click="isDropdownOpen = false">
                 <i class="fa-regular fa-user"></i> Profile
               </router-link>
               <router-link to="/admin/settings" class="dropdown-item" @click="isDropdownOpen = false">
                 <i class="fa-solid fa-sliders"></i> Setting
               </router-link>
               <div class="dropdown-divider"></div>
               <button @click="handleLogout" class="dropdown-item text-danger">
                 <i class="fa-solid fa-right-from-bracket"></i> Logout
               </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="content pulsate-in">
        <slot />
      </div>

      <!-- Footer -->
      <footer class="footer">
        <p>&copy; 2026 {{ settings.brand_name }}. All rights reserved.</p>
      </footer>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../store/auth';
import { useConfigStore } from '../store/config';
import BaseNotification from '../Components/BaseNotification.vue';
import Breadcrumb from '../Components/Breadcrumb.vue';

defineProps({
  title: String,
  breadcrumbs: {
    type: Array,
    default: () => []
  }
});

const router = useRouter();
const { state, logout } = useAuthStore();
const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const isCollapsed = ref(false);
const isMobileOpen = ref(false);
const isDropdownOpen = ref(false);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

// Close dropdown when clicking outside (window listener could be added here for robustness)
const closeDropdown = () => {
  isDropdownOpen.value = false;
};

const handleLogout = async () => {
  await logout('admin');
  router.push({ name: 'admin.login' });
};

onMounted(() => {
    configStore.fetchSettings();
});
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f5f8fa;
  font-family: 'Inter', sans-serif;
}

.sidebar {
  width: 265px;
  background-color: #ffffff;
  box-shadow: 0 0 28px 0 rgba(82, 63, 105, 0.05);
  border-right: 1px solid #eff2f5;
  color: #7e8299;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  flex-shrink: 0;
  z-index: 101;
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar-logo {
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 25px;
  color: #181c32;
  font-weight: 700;
  border-bottom: 1px solid #eff2f5;
  white-space: nowrap;
  overflow: hidden;
}

.brand-link-admin {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    gap: 10px;
}

.sidebar-logo-img {
  height: 30px;
  width: auto;
  object-fit: contain;
}


.sidebar-nav {
  padding: 15px 0;
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 25px;
  color: #5e6278;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  white-space: nowrap;
  position: relative;
}

.nav-item i {
  width: 25px;
  font-size: 1.1rem;
  margin-right: 10px;
  color: #a1a5b7;
  transition: transform 0.3s ease, color 0.3s ease;
  display: inline-flex;
  justify-content: center;
}

.sidebar.collapsed .nav-item {
  padding: 12px;
  justify-content: center;
}

.sidebar.collapsed .nav-item i {
  margin-right: 0;
}

/* Hover Effects */
.nav-item:hover {
  background-color: #f5f8fa;
  color: #3699ff;
}

.nav-item:hover i {
  color: #3699ff;
  transform: translateX(4px);
}

.sidebar.collapsed .nav-item:hover i {
  transform: none;
}

/* Active State */
.nav-item.active {
  background-color: #e1f0ff;
  color: #3699ff;
}

.nav-item.active i {
  color: #3699ff;
}

.sidebar-footer {
  padding: 15px;
  border-top: 1px solid #eff2f5;
}

.desktop-only {
  display: block;
}

.collapse-btn {
  width: 100%;
  padding: 8px;
  background: #f5f8fa;
  border: none;
  border-radius: 6px;
  color: #7e8299;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.collapse-btn:hover {
  background: #e1f0ff;
  color: #3699ff;
}

.main-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
  min-width: 0;
  transition: margin-left 0.3s ease;
}

.header {
  height: 70px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.03);
  z-index: 90;
}

.header-left {
  display: flex;
  align-items: center;
}

.header-title {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #7e8299;
  cursor: pointer;
}

.mr-3 { margin-right: 15px; }
.mr-1 { margin-right: 5px; }

.page-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #3f4254;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-link {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  text-decoration: none;
}

.user-name {
  font-weight: 600;
  color: #3f4254;
  font-size: 0.95rem;
}

.user-badge {
  font-size: 0.75rem;
  color: #3699ff;
  font-weight: 500;
  background: #e1f0ff;
  padding: 2px 8px;
  border-radius: 4px;
}

.logout-btn {
  background: transparent;
  border: 1px solid #e1e3ea;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #7e8299;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
}

.logout-btn:hover {
  background: #f3f6f9;
  border-color: #d1d3e0;
}

.content {
  flex: 1;
  padding: 30px;
}

.footer {
  padding: 20px 30px;
  background: #fff;
  color: #b5b5c3;
  font-size: 0.9rem;
  border-top: 1px solid #eff2f5;
  position: sticky;
  bottom: 0;
  z-index: 10;
}

/* Mobile Overlay */
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.3);
  z-index: 90;
  backdrop-filter: blur(2px);
}

/* Animations */
.pulsate-in {
  animation: pulsate-in 0.5s ease;
}

@keyframes pulsate-in {
  0% { opacity: 0; transform: scale(0.98); }
  100% { opacity: 1; transform: scale(1); }
}

@media (max-width: 992px) {
  .sidebar {
    position: fixed;
    height: 100vh;
    z-index: 101;
    left: -265px;
    width: 265px !important;
    transition: left 0.3s ease;
  }
  
  .sidebar.mobile-open {
    left: 0;
  }
  
  .desktop-only {
    display: none;
  }
  
  .mobile-toggle {
    display: block;
  }
  
  .content {
    padding: 15px;
  }
  
  .header {
    padding: 0 15px;
  }
}

/* User Profile Dropdown */
.user-profile {
  position: relative;
  cursor: pointer;
}

.profile-trigger {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background-color: #3699ff;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.2rem;
  box-shadow: 0 3px 6px rgba(54, 153, 255, 0.2);
  position: relative;
}

.avatar-thumbnail {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.overflow-hidden { overflow: hidden; }

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 15px;
  width: 200px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
  padding: 10px 0;
  z-index: 100;
  border: 1px solid #eff2f5;
  animation: slideDown 0.2s ease;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  color: #7e8299;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.2s;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f5f8fa;
  color: #3699ff;
}

.dropdown-item i {
  width: 20px;
  margin-right: 10px;
}

.dropdown-divider {
  height: 1px;
  background-color: #eff2f5;
  margin: 5px 0;
}

.text-danger:hover {
  color: #f64e60;
  background-color: #ffe2e5;
}
</style>
