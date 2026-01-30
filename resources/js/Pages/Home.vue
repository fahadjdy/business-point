<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container shadow-sm">
        <h1 class="hero-title">Connecting our Village. <br> Finding your needs.</h1>
        <p class="hero-subtitle">Find shops, doctors, and essential services in your community instantly.</p>
        
        <div class="search-container">
          <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" placeholder="Search for doctors, shops, or services..." class="search-input" v-model="searchQuery" @keyup.enter="handleSearch">
            <button class="search-btn" @click="handleSearch">Search</button>
          </div>
        </div>
      </div>
    </section>

    <div class="content-container">
      <!-- Emergency Contacts -->
      <section class="section">
        <h2 class="section-title"><span class="icon-star">✳</span> Emergency Contacts</h2>
        <div class="emergency-grid" v-if="!loadingContacts">
          <div v-for="contact in emergencyContacts" :key="contact.id" class="contact-card" :class="contact.color">
            <div class="card-badge" v-if="contact.badge">{{ contact.badge }}</div>
            <div class="card-icon">
              <i :class="contact.icon || 'fa-solid fa-phone'"></i>
            </div>
            <h3 class="contact-name">{{ contact.name }}</h3>
            <p class="contact-number">{{ contact.contact_number }}</p>
            <button class="call-btn" @click="handleCall(contact)">
              {{ contact.color === 'primary' ? 'Contact Office' : 'Call Now' }}
            </button>
          </div>
        </div>
        <div v-else class="loading-state">
           <i class="fa-solid fa-spinner fa-spin"></i> Loading contacts...
        </div>
      </section>

      <section class="section">
        <div class="section-header">
          <h2 class="section-title"><i class="fa-solid fa-bullhorn icon-blue"></i> Village Announcements</h2>
          <router-link to="/announcements" class="view-all">View All</router-link>
        </div>
        <div class="announcements-container" v-if="!loadingAnnouncements">
          <div v-for="(announcement, index) in announcements" :key="announcement.id" 
               class="announcement-card shadow-sm" 
               :class="{ 'is-expanded': expandedId === announcement.id }">
            
            <div class="announcement-header" @click="toggleAnnouncement(announcement.id)">
               <div class="announcement-meta">
                  <Badge v-if="isNew(announcement)" type="primary" size="sm" class="new-badge">NEW</Badge>
                  <div v-else class="time-ago">{{ formatTimeAgo(announcement.created_at) }}</div>
               </div>
               
               <div class="announcement-title-row">
                  <div class="announcement-img-mini" v-if="announcement.image && expandedId !== announcement.id">
                     <img :src="announcement.image" />
                  </div>
                  <p class="announcement-title">{{ announcement.title }}</p>
                  <i class="fa-solid fa-chevron-down toggle-icon"></i>
               </div>
            </div>

            <Transition name="expand">
               <div v-if="expandedId === announcement.id" class="announcement-body">
                  <p class="announcement-description">{{ announcement.message }}</p>
                  
                  <div v-if="announcement.images && announcement.images.length > 0" class="announcement-gallery">
                    <lightgallery
                        :settings="{ speed: 500, plugins: plugins }"
                        class="gallery-grid"
                    >
                        <a v-for="(img, idx) in announcement.images" 
                           :key="idx" 
                           :href="img" 
                           class="gallery-item"
                           :class="{ 'is-hidden': idx > 0 && announcement.images.length > 1 }"
                        >
                            <img alt="Announcement Image" :src="img" />
                            <div class="zoom-overlay">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                <span v-if="idx === 0 && announcement.images.length > 1" class="more-count">
                                    +{{ announcement.images.length - 1 }} More
                                </span>
                            </div>
                        </a>
                    </lightgallery>
                  </div>
               </div>
            </Transition>
          </div>
          <div v-if="hasMoreAnnouncements" class="view-all-wrapper mt-4">
             <router-link :to="{ name: 'announcements.index' }" class="btn-view-all">
               View All Announcements <i class="fa-solid fa-arrow-right ml-2"></i>
             </router-link>
          </div>
          <div v-if="announcements.length === 0" class="no-data">No active announcements</div>
        </div>
        <div v-else class="loading-state">
           <i class="fa-solid fa-spinner fa-spin"></i> Loading announcements...
        </div>
      </section>

      <!-- Browse by Category -->
      <section class="section mb-10">
        <h2 class="section-title">Browse by Category</h2>
        <div class="category-grid">
          <div class="category-card shadow-sm" v-for="cat in categories" :key="cat.name">
            <div class="cat-icon-wrapper" :class="cat.color">
              <i :class="cat.icon"></i>
            </div>
            <h3 class="cat-name">{{ cat.name }}</h3>
            <p class="cat-subtitle">{{ cat.subtitle }}</p>
          </div>
        </div>
      </section>
    </div>

    <!-- Simple Footer -->
    <footer class="main-footer">
       <div class="content-container">
          <div class="footer-grid">
             <div class="footer-brand">
                <div class="footer-logo">
                   <img v-if="settings.site_logo" :src="settings.site_logo" style="height: 30px;" />
                   <span v-else class="brand-text">BP</span>
                   <span class="brand-name ml-2">{{ settings.brand_name || 'Business Point' }}</span>
                </div>
                <p class="footer-desc">The official business directory of our village community. Connecting local providers with residents.</p>
             </div>
             <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                   <li><a href="#">Add your Shop</a></li>
                   <li><a href="#">Latest News</a></li>
                   <li><a href="#">Privacy Policy</a></li>
                   <li><a href="#">Community Guidelines</a></li>
                </ul>
             </div>
             <div class="footer-contact">
                <h4>Contact Info</h4>
                <p v-if="settings.app_email"><i class="fa-solid fa-envelope mr-2"></i> {{ settings.app_email }}</p>
                <p v-if="settings.app_phone"><i class="fa-solid fa-phone mr-2"></i> {{ settings.app_phone }}</p>
             </div>
          </div>
       </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import axios from 'axios';
import { useConfigStore } from '../store/config';
import Badge from '@/Components/Badge.vue';
import moment from 'moment';

// LightGallery
import Lightgallery from 'lightgallery/vue';
import lgZoom from 'lightgallery/plugins/zoom';
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-zoom.css';

const configStore = useConfigStore();
const { settings } = storeToRefs(configStore);

const searchQuery = ref('');
const emergencyContacts = ref([]);
const announcements = ref([]);
const hasMoreAnnouncements = ref(false);
const loadingContacts = ref(true);
const loadingAnnouncements = ref(true);
const expandedId = ref(null);

const plugins = [lgZoom];

const onInit = () => {
    console.log('lightGallery has been initialized');
};

const toggleAnnouncement = (id) => {
    expandedId.value = expandedId.value === id ? null : id;
};

const categories = [
  { name: 'Shops', subtitle: 'Grocery, Hardware', icon: 'fa-solid fa-bag-shopping', color: 'blue' },
  { name: 'Healthcare', subtitle: 'Doctors, Clinics', icon: 'fa-solid fa-notes-medical', color: 'blue' },
  { name: 'Personal Care', subtitle: 'Barbers, Salons', icon: 'fa-solid fa-scissors', color: 'blue' },
  { name: 'Home Services', subtitle: 'Electricians, Plumbers', icon: 'fa-solid fa-screwdriver-wrench', color: 'blue' },
];

const fetchEmergencyContacts = async () => {
  try {
    const response = await axios.get('/api/v1/emergency-contacts', { params: { is_active: 1, sort_by: 'sort_order', sort_order: 'asc' } });
    if (response.data.success) {
      emergencyContacts.value = response.data.data.data;
    }
  } catch (error) {
    console.error('Error fetching emergency contacts:', error);
  } finally {
    loadingContacts.value = false;
  }
};

const fetchAnnouncements = async () => {
  try {
    const response = await axios.get('/api/v1/notifications', { params: { is_active: 1, sort_by: 'created_at', sort_order: 'desc' } });
    if (response.data.success) {
      const allData = response.data.data.data;
      announcements.value = allData.slice(0, 10);
      hasMoreAnnouncements.value = allData.length > 10;
    }
  } catch (error) {
    console.error('Error fetching announcements:', error);
  } finally {
    loadingAnnouncements.value = false;
  }
};

const handleSearch = () => {
  if (searchQuery.value) {
    console.log('Searching for:', searchQuery.value);
  }
};

const handleCall = (contact) => {
  if (contact.contact_number && contact.contact_number.match(/^\d+$/)) {
    window.location.href = `tel:${contact.contact_number}`;
  } else {
    alert(`Contacting ${contact.name}: ${contact.contact_number}`);
  }
};

const isNew = (notification) => {
  return moment(notification.created_at).isSame(moment(), 'day');
};

const formatTimeAgo = (date) => {
  return moment(date).fromNow().toUpperCase();
};

onMounted(() => {
  configStore.fetchSettings();
  fetchEmergencyContacts();
  fetchAnnouncements();
});
</script>

<style scoped>
.home-page {
  background: #f8fafd;
  min-height: 100vh;
}

.content-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* Hero Section */
.hero-section {
  background: #fff;
  padding: 40px 20px;
}

.hero-container {
  max-width: 1000px;
  margin: 0 auto;
  background: linear-gradient(135deg, #eef5ff 0%, #f6faff 100%);
  border-radius: 20px;
  padding: 60px 40px;
  text-align: center;
}

.hero-title {
  font-size: 2.75rem;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1.2;
  margin-bottom: 20px;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: #666;
  max-width: 500px;
  margin: 0 auto 40px;
}

.search-container {
  max-width: 600px;
  margin: 0 auto;
}

.search-wrapper {
  background: #fff;
  border-radius: 12px;
  display: flex;
  align-items: center;
  padding: 6px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.search-icon {
  padding: 0 15px;
  color: #b5b5c3;
}

.search-input {
  flex: 1;
  border: none;
  padding: 12px 5px;
  font-size: 1rem;
  outline: none;
}

.search-btn {
  background: #3699ff;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
}

/* Sections */
.section {
  margin-bottom: 50px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1a1a1a;
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0;
}

.icon-star { color: #f64e60; font-size: 1.5rem; }
.icon-blue { color: #3699ff; }

/* Emergency Grid */
.emergency-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

@media (max-width: 1024px) {
  .emergency-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .emergency-grid {
    grid-template-columns: 1fr;
  }
}

.contact-card {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  position: relative;
  border-left: 5px solid transparent;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
  transition: all 0.3s ease;
}

.contact-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.contact-card.danger { border-left-color: #f64e60; }
.contact-card.primary { border-left-color: #3699ff; }
.contact-card.success { border-left-color: #1bc5bd; }
.contact-card.warning { border-left-color: #ffa800; }
.contact-card.info { border-left-color: #8950fc; }

.card-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  font-size: 0.7rem;
  font-weight: 800;
  color: #b5b5c3;
}

.card-icon {
  width: 50px;
  height: 50px;
  background: #fff5f8;
  color: #f64e60;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-bottom: 15px;
}

.primary .card-icon { background: #e1f0ff; color: #3699ff; }
.success .card-icon { background: #c9f7f5; color: #1bc5bd; }
.warning .card-icon { background: #fff4de; color: #ffa800; }
.info .card-icon { background: #eee5ff; color: #8950fc; }

.contact-name { font-size: 1.1rem; font-weight: 700; margin-bottom: 5px; }
.contact-number { font-size: 1.8rem; font-weight: 800; color: #f64e60; margin-bottom: 20px; }
.primary .contact-number { color: #1a1a1a; font-size: 1.2rem; min-height: 2.7rem; display: flex; align-items: center; }
.success .contact-number { color: #1bc5bd; }
.warning .contact-number { color: #ffa800; }
.info .contact-number { color: #8950fc; }

.call-btn {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: none;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.danger .call-btn { background: #f64e60; color: #fff; }
.primary .call-btn { background: #3699ff; color: #fff; }
.success .call-btn { background: #1bc5bd; color: #fff; }
.warning .call-btn { background: #ffa800; color: #fff; }
.info .call-btn { background: #8950fc; color: #fff; }

.danger .call-btn:hover { background: #e04455; }
.primary .call-btn:hover { background: #1a7ae4; }

/* Announcements */
.announcements-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.announcement-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid #eff2f5;
}

.announcement-card.is-expanded {
  border-color: #3699ff;
  box-shadow: 0 10px 30px rgba(54, 153, 255, 0.08);
}

.announcement-header {
  padding: 15px 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 20px;
}

.announcement-meta {
  width: 80px;
  flex-shrink: 0;
}

.announcement-title-row {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 15px;
}

.announcement-title {
  font-size: 1rem;
  color: #1a1a1a;
  font-weight: 700;
  margin: 0;
  flex: 1;
}

.toggle-icon {
  color: #b5b5c3;
  transition: transform 0.3s;
  font-size: 0.8rem;
}

.is-expanded .toggle-icon { transform: rotate(180deg); color: #3699ff; }

.announcement-body {
  padding: 0 20px 20px 120px;
  color: #464e5f;
}

.announcement-description {
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 20px;
  white-space: pre-line;
}

.announcement-img-mini {
  width: 35px;
  height: 35px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f3f6f9;
}

.announcement-img-mini img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Gallery */
.view-all-wrapper {
  text-align: center;
}

.btn-view-all {
  display: inline-flex;
  align-items: center;
  padding: 12px 24px;
  background: #fff;
  border: 1px solid #3699ff;
  color: #3699ff;
  border-radius: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-view-all:hover {
  background: #3699ff;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(54, 153, 255, 0.2);
}

.announcement-gallery {
    margin-top: 15px;
    border-radius: 12px;
    overflow: hidden;
    max-width: 500px;
}

.gallery-item {
    display: block;
    position: relative;
    cursor: pointer;
}

.gallery-item img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    display: block;
}

.zoom-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-item.is-hidden { display: none; }
.more-count {
    margin-left: 8px;
    font-size: 0.9rem;
    font-weight: 700;
}

/* Transitions */
.expand-enter-active, .expand-leave-active {
  transition: all 0.3s ease-out;
  max-height: 1000px;
  overflow: hidden;
}
.expand-enter-from, .expand-leave-to {
  max-height: 0;
  opacity: 0;
  padding-bottom: 0;
}

.view-all { color: #3699ff; text-decoration: none; font-weight: 600; font-size: 0.9rem; }

/* Category Grid */
.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.category-card {
  background: #fff;
  border-radius: 16px;
  padding: 30px 20px;
  text-align: center;
  transition: transform 0.2s;
  cursor: pointer;
}

.category-card:hover { transform: translateY(-5px); }

.cat-icon-wrapper {
  width: 60px;
  height: 60px;
  background: #e1f0ff;
  color: #3699ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 0 auto 20px;
}

.cat-name { font-size: 1.1rem; font-weight: 700; margin-bottom: 5px; }
.cat-subtitle { font-size: 0.85rem; color: #b5b5c3; font-weight: 500; }

/* Footer */
.main-footer {
  background: #fff;
  padding: 60px 0;
  border-top: 1px solid #eff2f5;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 40px;
}

.footer-brand .footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.footer-brand .brand-name { font-size: 1.5rem; font-weight: 800; color: #1e1e2d; }
.footer-desc { color: #b5b5c3; line-height: 1.6; max-width: 300px; }

.footer-links h4, .footer-contact h4 { font-weight: 700; margin-bottom: 20px; color: #1e1e2d; }
.footer-links ul { list-style: none; padding: 0; }
.footer-links li { margin-bottom: 12px; }
.footer-links a { color: #b5b5c3; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #3699ff; }

.footer-contact p { color: #b5b5c3; margin-bottom: 12px; }

@media (max-width: 768px) {
  .hero-title { font-size: 1.8rem; }
  .footer-grid { grid-template-columns: 1fr; }
  .announcement-item { flex-direction: column; align-items: flex-start; gap: 10px; }
  .announcement-left { width: auto; }
}

.shadow-sm { box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
.mb-10 { margin-bottom: 80px; }
.loading-state { padding: 40px; text-align: center; color: #b5b5c3; }
.no-data { padding: 20px; text-align: center; color: #b5b5c3; font-style: italic; }
</style>
