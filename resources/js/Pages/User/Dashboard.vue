<template>
  <div class="user-dashboard">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1 class="hero-title">Connecting our Village.<br>Finding your needs.</h1>
        <p class="hero-subtitle">Find shops, doctors, and essential services in your community instantly.</p>
        
        <div class="search-box-wrapper shadow-lg">
          <div class="search-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>
          <input type="text" placeholder="Search for doctors, shops, or services..." class="search-input" />
          <button class="btn-search">Search</button>
        </div>
      </div>
    </section>

    <!-- Emergency Contacts -->
    <section class="section-container mt-5">
      <div class="section-header">
         <h2 class="section-title"><span class="text-danger">*</span> Emergency Contacts</h2>
      </div>
      <div class="grid-layout cols-4">
         <div class="emergency-card" v-for="contact in emergencyContacts" :key="contact.title">
            <div class="card-badge" :class="contact.badgeClass">{{ contact.badge }}</div>
            <div class="emergency-icon">
               <i :class="contact.icon"></i>
            </div>
            <div class="emergency-info">
               <h3 class="emergency-title">{{ contact.title }}</h3>
               <p class="emergency-number">{{ contact.number }}</p>
               <button class="btn-call" :class="contact.btnClass">{{ contact.btnText }}</button>
            </div>
         </div>
      </div>
    </section>

    <!-- Village Announcements -->
    <section class="section-container mt-5">
      <div class="announcements-card shadow-sm">
         <div class="announcement-header">
            <h2 class="section-title"><i class="fa-solid fa-bullhorn text-primary mr-2"></i> Village Announcements</h2>
            <a href="#" class="view-all">View All</a>
         </div>
         <div class="announcement-list">
            <div class="announcement-item" v-for="item in announcements" :key="item.text">
               <div class="announcement-badge" :class="item.badgeClass">{{ item.badge }}</div>
               <p class="announcement-text">{{ item.text }}</p>
            </div>
         </div>
      </div>
    </section>

    <!-- Browse by Category -->
    <section class="section-container mt-5 mb-5">
      <div class="section-header">
         <h2 class="section-title">Browse by Category</h2>
      </div>
      <div class="grid-layout cols-4">
         <div class="category-card shadow-sm" v-for="cat in categories" :key="cat.name">
            <div class="category-icon-box" :class="cat.iconClass">
               <i :class="cat.icon"></i>
            </div>
            <h3 class="category-name">{{ cat.name }}</h3>
            <p class="category-desc">{{ cat.desc }}</p>
         </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../store/auth';

const authStore = useAuthStore();

const emergencyContacts = ref([
  { title: 'Ambulance', number: '108', icon: 'fa-solid fa-truck-medical', badge: '24/7', badgeClass: 'bg-danger-light', btnText: 'Call Now', btnClass: 'btn-danger' },
  { title: 'Police Station', number: '100', icon: 'fa-solid fa-shield-halved', badge: '24/7', badgeClass: 'bg-danger-light', btnText: 'Call Now', btnClass: 'btn-danger' },
  { title: 'Fire Brigade', number: '101', icon: 'fa-solid fa-fire-extinguisher', badge: '24/7', badgeClass: 'bg-danger-light', btnText: 'Call Now', btnClass: 'btn-danger' },
  { title: 'Sarpanch Office', number: 'Local Official Hub', icon: 'fa-solid fa-building-columns', badge: 'OFFICIAL', badgeClass: 'bg-primary-light', btnText: 'Contact Office', btnClass: 'btn-primary' }
]);

const announcements = ref([
  { badge: 'NEW', badgeClass: 'bg-primary', text: 'Gram Panchayat meeting scheduled for Friday at 10:00 AM in the Community Hall.' },
  { badge: '2 DAYS AGO', badgeClass: 'bg-gray', text: 'Free Vaccination drive starts tomorrow at the Primary Health Center.' },
  { badge: '1 WEEK AGO', badgeClass: 'bg-gray', text: 'New digital literacy workshop registration now open for all residents.' }
]);

const categories = ref([
  { name: 'Shops', desc: 'Grocery, Hardware', icon: 'fa-solid fa-bag-shopping', iconClass: 'icon-primary' },
  { name: 'Healthcare', desc: 'Doctors, Clinics', icon: 'fa-solid fa-briefcase-medical', iconClass: 'icon-success' },
  { name: 'Personal Care', desc: 'Barbers, Salons', icon: 'fa-solid fa-scissors', iconClass: 'icon-info' },
  { name: 'Home Services', desc: 'Electricians, Plumbers', icon: 'fa-solid fa-screwdriver-wrench', iconClass: 'icon-warning' }
]);
</script>

<style scoped>
.user-dashboard {
  max-width: 1000px;
  margin: 0 auto;
}

/* Hero Section */
.hero-section {
  background: #eef6ff;
  border-radius: 20px;
  padding: 60px 40px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1e1e2d;
  line-height: 1.2;
  margin-bottom: 15px;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: #7e8299;
  margin-bottom: 40px;
}

.search-box-wrapper {
  background: white;
  max-width: 600px;
  margin: 0 auto;
  border-radius: 12px;
  display: flex;
  align-items: center;
  padding: 6px;
  gap: 10px;
}

.search-icon {
  padding: 0 15px;
  color: #b5b5c3;
  font-size: 1.1rem;
}

.search-input {
  flex: 1;
  border: none;
  background: none;
  padding: 12px 0;
  font-size: 1rem;
  outline: none;
  color: #3f4254;
}

.btn-search {
  background: #3699ff;
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-search:hover { background: #187de4; }

/* Section Styles */
.section-container {
  margin-top: 40px;
}

.section-header {
  margin-bottom: 20px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #181c32;
  display: flex;
  align-items: center;
}

.grid-layout {
  display: grid;
  gap: 20px;
}

.cols-4 { grid-template-columns: repeat(4, 1fr); }

/* Emergency Card */
.emergency-card {
  background: white;
  border-radius: 12px;
  padding: 25px 20px;
  text-align: center;
  position: relative;
  border-left: 3px solid transparent;
  box-shadow: 0 2px 15px rgba(0,0,0,0.03);
  transition: transform 0.2s;
}

.emergency-card:hover { transform: translateY(-5px); }

.emergency-card:nth-child(1), .emergency-card:nth-child(2), .emergency-card:nth-child(3) {
  border-left-color: #f64e60;
}
.emergency-card:nth-child(4) {
  border-left-color: #3699ff;
}

.card-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 3px 8px;
  border-radius: 4px;
}

.emergency-icon {
  font-size: 1.8rem;
  margin-bottom: 15px;
  color: #f64e60;
}

.emergency-card:nth-child(4) .emergency-icon { color: #3699ff; }

.emergency-title {
  font-size: 1rem;
  font-weight: 700;
  color: #3f4254;
  margin-bottom: 5px;
}

.emergency-number {
  font-size: 1.4rem;
  font-weight: 800;
  color: #181c32;
  margin-bottom: 15px;
}

.btn-call {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: none;
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
}

/* Announcements */
.announcements-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
}

.announcement-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.view-all {
  font-size: 0.85rem;
  color: #3699ff;
  font-weight: 700;
  text-decoration: none;
}

.announcement-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  background: #f9f9fc;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 10px;
}

.announcement-badge {
  font-size: 0.65rem;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 4px;
  color: white;
  flex-shrink: 0;
  margin-top: 2px;
}

.announcement-text {
  margin: 0;
  font-size: 0.95rem;
  color: #3f4254;
}

/* Category Cards */
.category-card {
  background: white;
  border-radius: 12px;
  padding: 30px 20px;
  text-align: center;
  transition: all 0.2s;
  cursor: pointer;
}

.category-card:hover { background: #f1faff; }

.category-icon-box {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  font-size: 1.2rem;
}

.category-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #181c32;
  margin-bottom: 5px;
}

.category-desc {
  font-size: 0.85rem;
  color: #b5b5c3;
  margin: 0;
}

/* Colors and Labels */
.bg-danger-light { background: #fff5f8; color: #f64e60; }
.bg-primary-light { background: #e1f0ff; color: #3699ff; }
.bg-primary { background: #3699ff; }
.bg-gray { background: #e4e6ef; color: #7e8299; }
.btn-danger { background: #f64e60; color: white; }
.btn-primary { background: #3699ff; color: white; }
.icon-primary { background: #e1f0ff; color: #3699ff; }
.icon-success { background: #c9f7f5; color: #1bc5bd; }
.icon-info { background: #eee5ff; color: #8950fc; }
.icon-warning { background: #fff4de; color: #ffa800; }

.shadow-lg { box-shadow: 0 10px 40px rgba(0,0,0,0.06); }
.shadow-sm { box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
.mt-5 { margin-top: 50px; }
.mb-5 { margin-bottom: 50px; }
.mr-2 { margin-right: 8px; }

@media (max-width: 900px) {
  .cols-4 { grid-template-columns: repeat(2, 1fr); }
  .hero-title { font-size: 1.8rem; }
}

@media (max-width: 500px) {
  .cols-4 { grid-template-columns: 1fr; }
}
</style>
