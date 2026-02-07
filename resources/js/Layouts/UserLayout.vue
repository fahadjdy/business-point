<template>
  <div class="user-panel-layout">
    <!-- Global Notification -->
    <BaseNotification />

    <!-- Top Header / Navbar -->
    <header class="top-nav shadow-sm">
      <div class="container container-header">
        <div class="nav-left">
          <router-link to="/" class="brand-link">
            <div class="brand-logo">
               <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.brand_name" style="height: 30px; width: auto;" />
               <i v-else class="fa-solid fa-cube text-primary"></i>
            </div>
            <span class="brand-text">{{ settings.brand_name || 'Business Point' }}</span>
          </router-link>
        </div>

        <nav class="nav-center">
          <router-link to="/" class="nav-link" active-class="active">Home</router-link>
          <router-link :to="{ name: 'community-directory.index' }" class="nav-link" active-class="active">Community Directory</router-link>
          <router-link :to="{ name: 'announcements.index' }" class="nav-link" active-class="active">Announcements</router-link>
        </nav>

        <div class="nav-right">
          <!-- Conditional Business Menu -->
          <template v-if="state.user">
             <!-- Case 1: No Vendor Profile -> Register -->
             <router-link v-if="!state.user.vendor" :to="{ name: 'business.register' }" class="btn-register-business mr-4">
                <i class="fa-solid fa-store mr-2"></i> Register Business
             </router-link>

             <!-- Case 2: Pending Approval -->
             <div v-else-if="state.user.vendor.verification_status === 'pending'" class="status-badge warning mr-4">
                <i class="fa-solid fa-clock mr-2"></i> Application Pending
             </div>

             <!-- Case 3: Approved -> Manage Business -->
             <router-link v-else-if="state.user.vendor.verification_status === 'approved'" to="/user/manage-business" class="btn-manage-business mr-4">
                <i class="fa-solid fa-briefcase mr-2"></i> Manage {{ getVendorLabel(state.user.vendor.vendor_type) }}
             </router-link>
             
             <!-- Case 4: Rejected -->
             <div v-else-if="state.user.vendor.verification_status === 'rejected'" class="status-badge danger mr-4">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> Application Rejected
             </div>
          </template>

          <!-- Authenticated View -->
          <div v-if="state.user" class="user-profile-wrapper">
             <div class="profile-dropdown-trigger" @click="toggleDropdown">
                <div class="avatar-thumbnail">
                   <img v-if="state.user?.photo_url" :src="state.user.photo_url" alt="User Photo" />
                   <span v-else>{{ state.user?.name?.charAt(0).toUpperCase() || 'U' }}</span>
                </div>
                <i class="fa-solid fa-chevron-down ml-2 arrow-icon"></i>
             </div>

             <div v-if="isDropdownOpen" class="dropdown-menu-custom">
                <div class="dropdown-header">
                   <p class="user-name">{{ state.user?.name }}</p>
                   <p class="user-email">{{ state.user?.email }}</p>
                   <span v-if="state.user.vendor" class="vendor-badge mt-1" :class="state.user.vendor.verification_status">
                       {{ state.user.vendor.vendor_type.toUpperCase() }}
                   </span>
                </div>
                <div class="dropdown-divider"></div>
                <router-link to="/user/profile" class="dropdown-item" @click="isDropdownOpen = false">
                   <i class="fa-regular fa-user"></i> My Profile
                </router-link>
                <router-link to="/user/setting" class="dropdown-item" @click="isDropdownOpen = false">
                   <i class="fa-solid fa-sliders"></i> Settings
                </router-link>
                
                <!-- Manage Link in Dropdown too -->
                <router-link v-if="state.user.vendor?.verification_status === 'approved'" to="/user/manage-business" class="dropdown-item" @click="isDropdownOpen = false">
                   <i class="fa-solid fa-store"></i> Manage My {{ getVendorLabel(state.user.vendor.vendor_type) }}
                </router-link>

                <div class="dropdown-divider"></div>
                <button @click="handleLogout" class="dropdown-item text-danger">
                   <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
             </div>
          </div>
          
          <!-- Guest View -->
          <div v-else class="guest-actions">
             <router-link v-if="settings.allow_self_registration" to="/register" class="btn-register-business mr-3">
                <i class="fa-solid fa-user-plus mr-2"></i> Register
             </router-link>
             <router-link to="/login" class="btn-register-business">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Login
             </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content">
      <slot />
    </main>

    <!-- Simple Footer -->
    <footer class="footer-simple">
      <div class="container text-center py-4">
        <p class="mb-0 text-muted">&copy; 2026 {{ settings.brand_name }}. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../store/auth';
import { useConfigStore } from '../store/config';
import swal from '../utils/swal';

const router = useRouter();
const { state, logout, fetchProfile } = useAuthStore();
const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const isDropdownOpen = ref(false);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const getVendorLabel = (type) => {
    const map = {
        'shop': 'Shop',
        'doctor': 'Doctor/Clinic',
        'barber': 'Barber Shop'
    };
    return map[type] || 'Business';
};

const handleLogout = async () => {
  await logout('user');
  swal.toast('Logged out successfully');
  router.push({ name: 'user.login' });
};

onMounted(() => {
    configStore.fetchSettings();
    if (state.user_token) {
        fetchProfile();
    }
});
</script>

<style scoped>
.user-panel-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #fafbfc;
  color: #181c32;
  font-family: 'Inter', sans-serif;
}

.top-nav {
  background: white;
  height: 80px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid #eff2f5;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.container-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}

/* Brand Section */
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

.text-primary { color: #3699ff; }

/* Navigation Center */
.nav-center {
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

.nav-link:hover, .nav-link.active {
  color: #3699ff;
}

/* Right Profile Section */
.nav-right {
  display: flex;
  align-items: center;
}

.user-profile-wrapper {
  display: flex;
  align-items: center;
  position: relative;
}

.btn-edit-profile {
  background: #3699ff;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-edit-profile:hover {
  background: #187de4;
}

.btn-register-business {
  background: #3699ff;
  color: white;
  padding: 10px 22px;
  border-radius: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.btn-register-business:hover {
  background: #0084ff;
  box-shadow: 0 4px 12px rgba(54, 153, 255, 0.25);
}

.btn-manage-business {
  background: #1bc5bd;
  color: white;
  padding: 10px 22px;
  border-radius: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.btn-manage-business:hover {
  background: #0bb7af;
  box-shadow: 0 4px 12px rgba(27, 197, 189, 0.25);
}

.status-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
}

.status-badge.warning {
    background: #fff4de;
    color: #ffa800;
}

.status-badge.danger {
    background: #ffe2e5;
    color: #f64e60;
}

.vendor-badge {
    display: inline-block;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 800;
}
.vendor-badge.approved { background: #c9f7f5; color: #1bc5bd; }
.vendor-badge.pending { background: #fff4de; color: #ffa800; }
.vendor-badge.rejected { background: #ffe2e5; color: #f64e60; }

.mr-1 { margin-right: 5px; }

.profile-dropdown-trigger {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-left: 20px;
}

.avatar-thumbnail {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  overflow: hidden;
  background: #f3f6f9;
  color: #3699ff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  border: 2px solid #fff;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.avatar-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.arrow-icon {
  font-size: 0.8rem;
  color: #b5b5c3;
  transition: transform 0.2s;
}

/* Custom Dropdown */
.dropdown-menu-custom {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 15px;
  width: 240px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  border: 1px solid #eff2f5;
  padding: 10px 0;
  z-index: 1001;
  animation: slideDown 0.2s ease;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-header {
  padding: 15px 20px;
}

.user-name {
  margin: 0;
  font-weight: 700;
  color: #181c32;
  font-size: 1rem;
}

.user-email {
  margin: 2px 0 0;
  color: #b5b5c3;
  font-size: 0.85rem;
}

.dropdown-divider {
  height: 1px;
  background-color: #eff2f5;
  margin: 10px 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  color: #5e6278;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s;
}

.dropdown-item:hover {
  background-color: #f9f9fc;
  color: #3699ff;
}

.dropdown-item i {
  color: #b5b5c3;
  width: 20px;
  text-align: center;
}

.dropdown-item:hover i { color: #3699ff; }

.text-danger { color: #f64e60 !important; }
.text-danger:hover { background-color: #fff5f8; }

/* Utilities */
.mr-4 { margin-right: 20px; }
.mr-3 { margin-right: 15px; }
.ml-2 { margin-left: 8px; }
.py-5 { padding-top: 50px; padding-bottom: 50px; }
.text-center { text-align: center; }
.text-muted { color: #b5b5c3; }
.mb-0 { margin-bottom: 0; }

.main-content {
  flex: 1;
}

.footer-simple {
  border-top: 1px solid #eff2f5;
  background: white;
}

@media (max-width: 900px) {
  .nav-center { display: none; }
  .btn-edit-profile span { display: none; }
  .btn-edit-profile { padding: 10px; }
}
</style>
