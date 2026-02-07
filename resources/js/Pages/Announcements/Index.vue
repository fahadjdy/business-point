<template>
  <div class="announcement-page">
    <div class="container">
      
      <!-- Header -->
      <div class="page-header">
        <nav class="breadcrumbs">
          <router-link to="/" class="breadcrumb-item">Home</router-link>
          <span class="separator">›</span>
          <span class="breadcrumb-item active">Announcements</span>
        </nav>
        <h1 class="page-title">Village Announcements</h1>
        <p class="page-subtitle">Stay updated with the latest news, maintenance schedules, and community alerts.</p>
      </div>

      <!-- Search and Filter Bar -->
      <div class="search-bar-container">
        <div class="search-input-wrapper">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
          <input 
            type="text" 
            v-model="searchKeyword"
            @input="debounceSearch"
            class="search-input"
            placeholder="Search village announcements..."
          >
        </div>
        
        <div class="filter-controls">
          <span class="filter-label">Filter priority:</span>
          <div class="filter-buttons">
            <button 
                @click="setPriority('')"
                class="filter-btn"
                :class="{ active: selectedPriority === '' }"
            >
                All
            </button>
            <button 
                @click="setPriority('important')"
                class="filter-btn"
                :class="{ active: selectedPriority === 'important' }"
            >
                Important
            </button>
            <button 
                @click="setPriority('normal')"
                class="filter-btn"
                :class="{ active: selectedPriority === 'normal' }"
            >
                Normal
            </button>
          </div>
        </div>
      </div>

      <div class="content-grid">
        
        <!-- Main Content: Announcements List -->
        <div class="main-column">
          
          <div v-if="loading" class="loading-state">
            <div class="spinner"></div>
            <p>Loading announcements...</p>
          </div>

          <template v-else-if="announcements.length > 0">
            <div 
              v-for="announcement in announcements" 
              :key="announcement.id"
              class="announcement-card"
              :class="announcement.priority?.toLowerCase()"
            >
              <div class="card-content">
                <div class="card-header">
                  <span 
                    class="priority-badge"
                    :class="announcement.priority?.toLowerCase()"
                  >
                    {{ announcement.priority || 'Normal' }}
                  </span>
                  <span class="post-date">Posted: {{ formatDate(announcement.created_at) }}</span>
                </div>
                
                <h3 class="card-title">{{ announcement.title }}</h3>
                
                <p class="card-message">
                  {{ announcement.message }}
                </p>
                
                <div class="card-footer">
                    <router-link 
                      :to="{ name: 'announcements.preview', params: { id: announcement.id } }"
                      class="read-more-link"
                    >
                      Read Full Announcement <i class="fa-solid fa-arrow-right arrow-icon"></i>
                    </router-link>
                    
                    <button 
                         @click.prevent="shareAnnouncement(announcement)"
                         class="share-btn"
                         title="Share"
                    >
                         <i class="fa-solid fa-share"></i>
                    </button>
                </div>
              </div>
            </div>

            <!-- Load More Button -->
            <div v-if="pagination.last_page > pagination.current_page" class="load-more-container">
               <button 
                  @click="loadMore" 
                  class="load-more-btn"
                  :disabled="loadingMore"
               >
                  {{ loadingMore ? 'Loading...' : 'Load More Announcements' }}
               </button>
            </div>
          </template>

          <div v-else class="empty-state">
            <i class="fa-regular fa-bell empty-icon"></i>
            <h3>No announcements found</h3>
            <p>Try adjusting your search or filter options.</p>
            <button @click="resetFilters" class="reset-link">
                Clear all filters
            </button>
          </div>

        </div>

        <!-- Sidebar Widgets -->
        <div class="sidebar-column">
          
          <!-- Recent Alerts Widget -->
          <div class="widget">
            <h3 class="widget-title">
               <i class="fa-solid fa-triangle-exclamation icon-alert"></i> Recent Alerts
            </h3>
            <div class="widget-list">
               <div v-for="alert in recentAlerts" :key="alert.id" class="widget-item alert-item">
                  <div class="item-date">{{ alert.date }}</div>
                  <h4 class="item-title">{{ alert.title }}</h4>
               </div>
            </div>
          </div>

          <!-- Village Events Widget -->
          <div class="widget">
             <h3 class="widget-title">
               <i class="fa-solid fa-calendar-day icon-event"></i> Village Events
            </h3>
            <div class="widget-list">
               <div v-for="event in villageEvents" :key="event.id" class="event-item">
                  <div class="event-date-box">
                     <span class="event-month">{{ event.month }}</span>
                     <span class="event-day">{{ event.day }}</span>
                  </div>
                  <div class="event-details">
                     <h4 class="event-title">{{ event.title }}</h4>
                     <p class="event-info">{{ event.time }} • {{ event.location }}</p>
                  </div>
               </div>
            </div>
            <button class="view-calendar-btn">
               View Full Calendar
            </button>
          </div>

          <!-- Quick Resources Widget -->
          <div class="widget resources-widget">
             <h3 class="widget-title">
               Quick Resources
            </h3>
            <div class="resource-list">
               <a href="#" class="resource-link">
                  <span>Contact Village Office</span>
                  <i class="fa-solid fa-arrow-up-right-from-square link-icon"></i>
               </a>
               <div class="divider"></div>
               <a href="#" class="resource-link">
                  <span>Report an Issue</span>
                  <i class="fa-solid fa-arrow-up-right-from-square link-icon"></i>
               </a>
               <div class="divider"></div>
               <a href="#" class="resource-link">
                  <span>Waste Collection PDF</span>
                  <i class="fa-solid fa-download link-icon"></i>
               </a>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';

// State
const announcements = ref([]);
const loading = ref(true);
const loadingMore = ref(false);
const searchKeyword = ref('');
const selectedPriority = ref('');
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

// Sidebar Dummy Data
const recentAlerts = [
   { id: 1, date: 'Oct 24', title: 'Water Main Break - Sector 4 Repairs in Progress' },
   { id: 2, date: 'Oct 18', title: 'Emergency Council Meeting: Budget Finalization' },
   { id: 3, date: 'Oct 15', title: 'Storm Surge Advisory: Secure Outdoor Equipment' },
];

const villageEvents = [
   { id: 1, month: 'OCT', day: '28', title: 'Farmers Market', time: '8:00 AM', location: 'Village Square' },
   { id: 2, month: 'OCT', day: '31', title: 'Halloween Parade', time: '6:30 PM', location: 'Community Center' },
   { id: 3, month: 'NOV', day: '04', title: 'Art in the Park', time: '10:00 AM', location: 'South Green' },
];

const fetchAnnouncements = async (page = 1, append = false) => {
  if (!append) loading.value = true;
  else loadingMore.value = true;

  try {
    const params = {
      page,
      per_page: 10,
      search: searchKeyword.value,
      priority: selectedPriority.value
    };

    const response = await axios.get('/api/v1/notifications', { 
      params,
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    
    if (response.data.success) {
      const result = response.data.data;
      
      if (append) {
         announcements.value = [...announcements.value, ...result.data];
      } else {
         announcements.value = result.data || [];
      }
      
      pagination.value = {
        current_page: result.current_page,
        last_page: result.last_page,
        total: result.total
      };
    }
  } catch (error) {
    console.error('Error fetching announcements:', error);
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

const debounceSearch = debounce(() => {
  fetchAnnouncements(1);
}, 500);

const setPriority = (priority) => {
   selectedPriority.value = priority;
   fetchAnnouncements(1);
};

const loadMore = () => {
   if (pagination.value.current_page < pagination.value.last_page) {
      fetchAnnouncements(pagination.value.current_page + 1, true);
   }
};

const resetFilters = () => {
  searchKeyword.value = '';
  selectedPriority.value = '';
  fetchAnnouncements(1);
};

const formatDate = (dateString) => {
   if (!dateString) return '';
   const options = { year: 'numeric', month: 'short', day: 'numeric' };
   return new Date(dateString).toLocaleDateString('en-US', options);
};

const shareAnnouncement = (item) => {
  const shareUrl = `${window.location.origin}/announcements/${item.id}`;
  const shareText = `Check out this announcement: ${item.title}`;

  if (navigator.share) {
    navigator.share({
      title: item.title,
      text: shareText,
      url: shareUrl,
    });
  } else {
    navigator.clipboard.writeText(`${shareText} ${shareUrl}`).then(() => {
      alert('Announcement link copied to clipboard!');
    });
  }
};

onMounted(() => {
  fetchAnnouncements();
});
</script>

<style scoped>
/* Page Layout */
.announcement-page {
  background-color: #f8f9fa; /* Light gray background like screenshot */
  min-height: 100vh;
  padding: 40px 20px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #333;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Header */
.page-header {
  margin-bottom: 30px;
}

.breadcrumbs {
  font-size: 14px;
  color: #6c757d;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

.breadcrumb-item {
  color: #6c757d;
  text-decoration: none;
}

.breadcrumb-item:hover {
  color: #343a40;
}

.breadcrumb-item.active {
  color: #212529;
  font-weight: 500;
}

.separator {
  margin: 0 8px;
  color: #adb5bd;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #212529;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 16px;
  color: #6c757d;
  margin: 0;
}

/* Search Bar */
.search-bar-container {
  background: white;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.02);
  border: 1px solid #e9ecef;
  flex-wrap: wrap;
  gap: 15px;
}

.search-input-wrapper {
  position: relative;
  flex: 1;
  max-width: 500px;
  min-width: 300px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #adb5bd;
}

.search-input {
  width: 100%;
  padding: 10px 10px 10px 35px;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  background-color: #f8f9fa;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  background-color: white;
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.filter-controls {
  display: flex;
  align-items: center;
  gap: 15px;
}

.filter-label {
  font-size: 14px;
  color: #6c757d;
  font-weight: 500;
}

.filter-buttons {
  display: flex;
  gap: 5px;
}

.filter-btn {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 14px;
  color: #495057;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.filter-btn:hover {
  background: #e2e6ea;
}

.filter-btn.active {
  background: #e7f1ff; /* Light blue active */
  color: #0056b3;
  border-color: #b8daff;
}

.filter-btn:nth-child(2).active { /* Important active */
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Grid Layout */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 350px; /* Main column take rest, Sidebar 350px */
  gap: 30px;
}

/* Announcements Column */
.main-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.announcement-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  overflow: hidden;
  border-left: 4px solid #007bff; /* Default/Normal border color */
  transition: transform 0.2s, box-shadow 0.2s;
}

.announcement-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.announcement-card.important {
  border-left-color: #ff5252; /* Important Red Border */
}

.card-content {
  padding: 25px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.priority-badge {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 20px;
  letter-spacing: 0.5px;
}

.priority-badge.normal {
  background: #e7f1ff;
  color: #0056b3;
}

.priority-badge.important {
  background: #ffcdd2; /* Light red */
  color: #c62828;
}

.post-date {
  font-size: 12px;
  color: #adb5bd;
  font-weight: 600;
}

.card-title {
  font-size: 20px;
  font-weight: 700;
  color: #212529;
  margin: 0 0 10px 0;
  line-height: 1.4;
}

.card-message {
  font-size: 15px;
  color: #6c757d;
  line-height: 1.6;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.read-more-link {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.read-more-link:hover {
  color: #0056b3;
}

.arrow-icon {
  font-size: 12px;
}

.share-btn {
  background: none;
  border: none;
  color: #adb5bd;
  cursor: pointer;
  font-size: 16px;
  transition: color 0.2s;
}

.share-btn:hover {
  color: #007bff;
}

.load-more-btn {
  width: 100%;
  padding: 12px;
  background: white;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  color: #495057;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.load-more-btn:hover {
  background: #f8f9fa;
  border-color: #ced4da;
}

/* Empty State */
.empty-state {
    text-align: center;
    background: white;
    padding: 50px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.empty-icon { font-size: 3rem; color: #e9ecef; margin-bottom: 15px; }
.reset-link { color: #007bff; background: none; border: none; font-weight: 600; cursor: pointer; text-decoration: underline; margin-top: 10px; }


/* Sidebar Column */
.sidebar-column {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.widget {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  border: 1px solid #f1f3f5;
}

.widget-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #adb5bd;
    margin: 0 0 15px 0;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}

.icon-alert { color: #ff5252; margin-right: 8px; }
.icon-event { color: #007bff; margin-right: 8px; }

.widget-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.alert-item {
    cursor: pointer;
}
.alert-item:hover .item-title { color: #007bff; }

.item-date {
    font-size: 11px;
    color: #adb5bd;
    font-weight: 600;
    margin-bottom: 2px;
}

.item-title {
    font-size: 14px;
    font-weight: 600;
    color: #343a40;
    margin: 0;
    line-height: 1.4;
    transition: color 0.2s;
}

/* Event Items */
.event-item {
    display: flex;
    gap: 12px;
}

.event-date-box {
    width: 50px;
    height: 50px;
    background: #e7f1ff;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #b8daff;
    flex-shrink: 0;
}

.event-month {
    font-size: 10px;
    font-weight: 700;
    color: #007bff;
    text-transform: uppercase;
    line-height: 1;
}

.event-day {
    font-size: 18px;
    font-weight: 700;
    color: #0056b3;
    line-height: 1;
}

.event-details {
    flex: 1;
}

.event-title {
    font-size: 14px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 2px 0;
}

.event-info {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
}

.view-calendar-btn {
    width: 100%;
    margin-top: 15px;
    padding: 8px;
    background: white;
    border: 1px solid #e7f1ff;
    color: #007bff;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.view-calendar-btn:hover {
    background: #e7f1ff;
}

/* Resources Widget */
.resources-widget {
    background: #e7f1ff;
    border-color: #b8daff;
}

.resources-widget .widget-title {
    color: #6c757d;
}

.resource-list {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.resource-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: #495057;
    font-size: 14px;
    font-weight: 500;
    padding: 8px 0;
    transition: color 0.2s;
}

.resource-link:hover {
    color: #007bff;
}

.link-icon {
    font-size: 12px;
    color: #adb5bd;
}

.resource-link:hover .link-icon {
    color: #007bff;
}

.divider {
    height: 1px;
    background: rgba(0,123,255, 0.1);
}

/* Responsive */
@media (max-width: 992px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 576px) {
    .search-bar-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input-wrapper {
        max-width: none;
    }
    
    .filter-controls {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>