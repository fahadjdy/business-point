<template>
  <div class="home-page">
    <!-- Main Grid Container -->
    <div class="home-container">
      <!-- Main Content Column -->
      <div class="main-content">
        <!-- Featured Banner -->
        <section class="featured-banner">
          <div v-if="loadingBanners" class="banner-loading">
            <i class="fa-solid fa-spinner fa-spin"></i>
          </div>
          <div v-else-if="banners.length > 0" class="banner-carousel">
            <div class="banner-slide" :style="{ backgroundImage: `url(${currentBanner.image})` }">
              <div class="banner-overlay"></div>
              <div class="banner-content">
                <span class="banner-badge">UPCOMING EVENT</span>
                <h1 class="banner-title">{{ currentBanner.title }}</h1>
                <p class="banner-description">{{ currentBanner.description }}</p>
                <button v-if="currentBanner.link" class="banner-btn" @click="window.location.href = currentBanner.link">
                  {{ currentBanner.button_text || 'Learn More' }}
                </button>
              </div>
              <!-- Carousel Indicators -->
              <div class="carousel-indicators" v-if="banners.length > 1">
                <span 
                  v-for="(banner, index) in banners" 
                  :key="banner.id"
                  class="indicator"
                  :class="{ active: currentBannerIndex === index }"
                  @click="currentBannerIndex = index"
                ></span>
              </div>
            </div>
          </div>
          <div v-else class="banner-placeholder">
            <i class="fa-solid fa-image"></i>
            <p>No banners available</p>
          </div>
        </section>

        <!-- Quick Emergency Access -->
        <section class="emergency-access">
          <h2 class="section-title">
            <i class="fa-solid fa-asterisk"></i> Quick Emergency Access
          </h2>
          <div class="emergency-buttons">
            <button class="emergency-btn ambulance" @click="handleEmergencyCall('ambulance')">
              <i class="fa-solid fa-truck-medical"></i>
              <span>Ambulance</span>
            </button>
            <button class="emergency-btn police" @click="handleEmergencyCall('police')">
              <i class="fa-solid fa-shield-halved"></i>
              <span>Police</span>
            </button>
            <button class="emergency-btn fire" @click="handleEmergencyCall('fire')">
              <i class="fa-solid fa-fire-extinguisher"></i>
              <span>Fire</span>
            </button>
          </div>
        </section>

        <!-- Search Bar -->
        <section class="search-section">
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input 
              type="text" 
              v-model="searchQuery"
              placeholder="Search 500+ local businesses, doctors, or services..."
              @keyup.enter="handleSearch"
            />
          </div>
        </section>

        <!-- Explore Categories -->
        <section class="categories-section">
          <div class="section-header">
            <h2 class="section-title">Explore Categories</h2>
            <router-link to="/community-directory" class="view-all-link">View All Directory</router-link>
          </div>
          <div class="category-grid">
            <div class="category-card" v-for="cat in categories" :key="cat.name" @click="navigateToCategory(cat)">
              <div class="category-icon">
                <i :class="cat.icon"></i>
              </div>
              <div class="category-info">
                <h3>{{ cat.name }}</h3>
                <p>{{ cat.count }}</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Featured Members -->
        <section class="featured-members">
          <h2 class="section-title">Featured Members</h2>
          <div class="members-grid">
            <div class="member-card" v-for="member in featuredMembers" :key="member.id">
              <div class="member-image">
                <img :src="member.image" :alt="member.name" />
              </div>
              <div class="member-info">
                <h3>{{ member.name }} <i v-if="member.verified" class="fa-solid fa-circle-check verified"></i></h3>
                <p class="member-status">{{ member.status }}</p>
              </div>
              <button class="member-action" @click="contactMember(member)">
                <i :class="member.actionIcon"></i>
              </button>
            </div>
          </div>
        </section>
      </div>

      <!-- Sidebar -->
      <aside class="sidebar">
        <!-- Village Announcements -->
        <div class="widget announcements-widget">
          <h3 class="widget-title">
            <i class="fa-solid fa-bullhorn"></i> Village Announcements
          </h3>
          <div class="announcement-list">
            <div 
              v-for="announcement in announcements.slice(0, 3)" 
              :key="announcement.id"
              class="announcement-item"
              :class="announcement.priority"
            >
              <span class="announcement-badge">{{ announcement.priority.toUpperCase() }}</span>
              <span class="announcement-time">{{ formatTimeAgo(announcement.created_at) }}</span>
              <h4>{{ announcement.title }}</h4>
              <p>{{ announcement.message.substring(0, 80) }}...</p>
            </div>
          </div>
          <router-link to="/announcements" class="view-all-btn">View All Notifications</router-link>
        </div>

        <!-- Weather Widget -->
        <div class="widget weather-widget">
          <div class="weather-main">
            <div class="weather-temp">
              <span class="temp">24°C</span>
              <i class="fa-solid fa-cloud-sun weather-icon"></i>
            </div>
            <p class="weather-condition">Partly Cloudy</p>
          </div>
          <div class="weather-footer">
            <span>Lakeside Village, 4210</span>
            <span>14:45 PM</span>
          </div>
        </div>

        <!-- Essential Contacts -->
        <div class="widget contacts-widget">
          <h3 class="widget-title">Essential Contacts</h3>
          <div class="contact-list">
            <a href="#" class="contact-item">
              <i class="fa-solid fa-building"></i>
              <span>Village Council Office</span>
            </a>
            <a href="#" class="contact-item">
              <i class="fa-solid fa-bolt"></i>
              <span>Utility Support (Electric)</span>
            </a>
            <a href="#" class="contact-item">
              <i class="fa-solid fa-trash"></i>
              <span>Waste Management Dept</span>
            </a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import moment from 'moment';

const router = useRouter();

const searchQuery = ref('');
const banners = ref([]);
const loadingBanners = ref(true);
const currentBannerIndex = ref(0);
const announcements = ref([]);
const emergencyContacts = ref([]);

const currentBanner = computed(() => banners.value[currentBannerIndex.value] || {});

const categories = [
  { name: 'Shops', count: '124 Listings', icon: 'fa-solid fa-bag-shopping' },
  { name: 'Doctors', count: '42 Clinics', icon: 'fa-solid fa-user-doctor' },
  { name: 'Barbers', count: '18 Salons', icon: 'fa-solid fa-scissors' },
  { name: 'Services', count: '85 Experts', icon: 'fa-solid fa-screwdriver-wrench' },
];

const featuredMembers = [
  {
    id: 1,
    name: 'Central Pharmacy',
    image: '/images/pharmacy-placeholder.jpg',
    status: 'Open 24/7 • 0.5km away',
    verified: true,
    actionIcon: 'fa-solid fa-phone'
  },
  {
    id: 2,
    name: 'Elite Grooming',
    image: '/images/barber-placeholder.jpg',
    status: 'Closes 8 PM • 1.2km away',
    verified: true,
    actionIcon: 'fa-solid fa-calendar'
  }
];

const fetchBanners = async () => {
  loadingBanners.value = true;
  try {
    const response = await axios.get('/api/v1/banners', {
      params: { is_active: 1 }
    });
    if (response.data.success) {
      banners.value = response.data.data.data || response.data.data;
    }
  } catch (error) {
    console.error('Error fetching banners:', error);
  } finally {
    loadingBanners.value = false;
  }
};

const fetchAnnouncements = async () => {
  try {
    const response = await axios.get('/api/v1/notifications', {
      params: { is_active: 1, per_page: 3 }
    });
    if (response.data.success) {
      announcements.value = response.data.data.data || [];
    }
  } catch (error) {
    console.error('Error fetching announcements:', error);
  }
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ 
      name: 'community-directory.index', 
      query: { search: searchQuery.value } 
    });
  }
};

const handleEmergencyCall = (type) => {
  const numbers = {
    ambulance: '108',
    police: '100',
    fire: '101'
  };
  window.location.href = `tel:${numbers[type]}`;
};

const navigateToCategory = (category) => {
  router.push({ name: 'community-directory.index' });
};

const contactMember = (member) => {
  console.log('Contact member:', member);
};

const formatTimeAgo = (date) => {
  return moment(date).fromNow();
};

// Auto-rotate banners
let bannerInterval;
onMounted(() => {
  fetchBanners();
  fetchAnnouncements();
  
  bannerInterval = setInterval(() => {
    if (banners.value.length > 1) {
      currentBannerIndex.value = (currentBannerIndex.value + 1) % banners.value.length;
    }
  }, 5000);
});

onBeforeUnmount(() => {
  if (bannerInterval) clearInterval(bannerInterval);
});
</script>

<style scoped>
.home-page {
  background: #f5f8fa;
  min-height: 100vh;
  padding: 20px 0;
}

.home-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 30px;
}

/* Featured Banner */
.featured-banner {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  margin-bottom: 30px;
}

.banner-carousel {
  position: relative;
  height: 400px;
}

.banner-slide {
  height: 100%;
  background-size: cover;
  background-position: center;
  position: relative;
}

.banner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
}

.banner-content {
  position: relative;
  z-index: 2;
  padding: 60px;
  color: white;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.banner-badge {
  display: inline-block;
  background: #3699ff;
  color: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 20px;
  width: fit-content;
}

.banner-title {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 15px;
  line-height: 1.2;
}

.banner-description {
  font-size: 1.1rem;
  margin-bottom: 30px;
  max-width: 600px;
  line-height: 1.6;
}

.banner-btn {
  background: white;
  color: #1a1a1a;
  border: none;
  padding: 14px 32px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  width: fit-content;
  transition: all 0.3s;
}

.banner-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.carousel-indicators {
  position: absolute;
  bottom: 20px;
  left: 60px;
  display: flex;
  gap: 8px;
  z-index: 3;
}

.indicator {
  width: 30px;
  height: 4px;
  background: rgba(255,255,255,0.4);
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.3s;
}

.indicator.active {
  background: white;
  width: 40px;
}

.banner-loading, .banner-placeholder {
  height: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #b5b5c3;
  font-size: 1.2rem;
}

/* Emergency Access */
.emergency-access {
  margin-bottom: 30px;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-title i {
  color: #f64e60;
  font-size: 0.9rem;
}

.emergency-buttons {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
}

.emergency-btn {
  background: white;
  border: none;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.emergency-btn i {
  font-size: 2rem;
  color: white;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.emergency-btn span {
  font-weight: 700;
  color: #1a1a1a;
  font-size: 1rem;
}

.emergency-btn.ambulance i { background: #f64e60; }
.emergency-btn.police i { background: #3699ff; }
.emergency-btn.fire i { background: #ffa800; }

.emergency-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* Search Section */
.search-section {
  margin-bottom: 30px;
}

.search-box {
  background: white;
  border-radius: 12px;
  padding: 8px 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.search-box i {
  color: #b5b5c3;
  font-size: 1.1rem;
}

.search-box input {
  flex: 1;
  border: none;
  outline: none;
  font-size: 1rem;
  padding: 12px 0;
  color: #1a1a1a;
}

.search-box input::placeholder {
  color: #b5b5c3;
}

/* Categories */
.categories-section {
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.view-all-link {
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
}

.category-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.category-icon {
  width: 50px;
  height: 50px;
  background: #e1f0ff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.category-icon i {
  font-size: 1.5rem;
  color: #3699ff;
}

.category-info h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.category-info p {
  font-size: 0.85rem;
  color: #b5b5c3;
  margin: 0;
}

/* Featured Members */
.featured-members {
  margin-bottom: 30px;
}

.members-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
}

.member-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.member-image {
  width: 60px;
  height: 60px;
  border-radius: 10px;
  overflow: hidden;
  background: #f3f6f9;
  flex-shrink: 0;
}

.member-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.member-info {
  flex: 1;
}

.member-info h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.verified {
  color: #3699ff;
  font-size: 0.9rem;
}

.member-status {
  font-size: 0.85rem;
  color: #b5b5c3;
  margin: 0;
}

.member-action {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #e1f0ff;
  border: none;
  color: #3699ff;
  cursor: pointer;
  transition: all 0.3s;
}

.member-action:hover {
  background: #3699ff;
  color: white;
}

/* Sidebar */
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.widget {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.widget-title {
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 20px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.widget-title i {
  color: #3699ff;
}

/* Announcements Widget */
.announcement-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 20px;
}

.announcement-item {
  padding: 15px;
  background: #f9f9fc;
  border-radius: 8px;
  border-left: 4px solid #3699ff;
}

.announcement-item.important {
  border-left-color: #f64e60;
  background: #fff5f8;
}

.announcement-badge {
  display: inline-block;
  background: #3699ff;
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  margin-right: 8px;
}

.announcement-item.important .announcement-badge {
  background: #f64e60;
}

.announcement-time {
  font-size: 0.75rem;
  color: #b5b5c3;
  text-transform: uppercase;
}

.announcement-item h4 {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 8px 0 4px 0;
}

.announcement-item p {
  font-size: 0.85rem;
  color: #5e6278;
  margin: 0;
  line-height: 1.5;
}

.view-all-btn {
  display: block;
  text-align: center;
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 10px;
  border-radius: 6px;
  transition: all 0.3s;
}

.view-all-btn:hover {
  background: #f0f8ff;
}

/* Weather Widget */
.weather-widget {
  background: linear-gradient(135deg, #3699ff 0%, #1a7ae4 100%);
  color: white;
}

.weather-main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.weather-temp {
  display: flex;
  align-items: center;
  gap: 15px;
}

.temp {
  font-size: 3rem;
  font-weight: 800;
}

.weather-icon {
  font-size: 3rem;
  opacity: 0.9;
}

.weather-condition {
  font-size: 1.1rem;
  margin: 0;
  opacity: 0.95;
}

.weather-footer {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  opacity: 0.9;
  padding-top: 15px;
  border-top: 1px solid rgba(255,255,255,0.2);
}

/* Contacts Widget */
.contact-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f9f9fc;
  border-radius: 8px;
  text-decoration: none;
  color: #1a1a1a;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.contact-item:hover {
  background: #e1f0ff;
  color: #3699ff;
}

.contact-item i {
  color: #3699ff;
  width: 20px;
  text-align: center;
}

/* Responsive */
@media (max-width: 1200px) {
  .home-container {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    grid-row: 2;
  }
  
  .category-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .banner-title {
    font-size: 2rem;
  }
  
  .banner-content {
    padding: 30px;
  }
  
  .emergency-buttons {
    grid-template-columns: 1fr;
  }
  
  .category-grid {
    grid-template-columns: 1fr;
  }
  
  .members-grid {
    grid-template-columns: 1fr;
  }
}
</style>
