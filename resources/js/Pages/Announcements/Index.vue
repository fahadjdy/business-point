<template>
  <div class="announcements-page">
    <div class="content-container">
      
      <!-- Search Section -->
      <div class="search-section mb-10">
        <div class="search-container shadow-sm">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input 
                    type="text" 
                    v-model="searchKeyword" 
                    placeholder="Search announcements..." 
                    class="search-input"
                    @input="debounceSearch"
                >
            </div>
            
            <div class="filter-row">
               <!-- Date Filter -->
               <div class="date-filters">
                  <input 
                    type="date" 
                    v-model="dateFrom" 
                    class="date-input"
                    @change="fetchAnnouncements(1)"
                  >
                  <span class="date-separator">to</span>
                  <input 
                    type="date" 
                    v-model="dateTo" 
                    class="date-input"
                    @change="fetchAnnouncements(1)"
                  >
                  <button 
                    v-if="dateFrom || dateTo" 
                    @click="clearDateFilter" 
                    class="clear-date-btn"
                  >
                    <i class="fa-solid fa-xmark"></i>
                  </button>
               </div>
            </div>
        </div>
      </div>

      <!-- Loading State -->
       <div v-if="loading" class="loading-state">
         <div class="spinner"></div>
         <p>Loading announcements...</p>
      </div>

      <!-- Listings -->
      <div v-else-if="announcements.length > 0" class="announcement-list">
        <div 
          v-for="announcement in announcements" 
          :key="announcement.id" 
          class="announcement-card shadow-sm"
        >
           <div class="card-left">
              <div class="card-icon-wrapper">
                 <img v-if="announcement.image" :src="announcement.image" class="card-img" />
                 <i v-else class="fa-solid fa-bullhorn"></i>
              </div>
           </div>

           <div class="card-body">
              <div class="card-header">
                 <h3 class="card-title">{{ announcement.title }}</h3>
                 <span class="card-date">{{ formatDate(announcement.created_at) }}</span>
              </div>
              
              <div class="card-content">
                 <p class="card-description">{{ truncateText(announcement.message, 150) }}</p>
              </div>
              
              <!-- Meta Info -->
              <div class="card-meta">
                 <span v-if="announcement.scheduled_at" class="meta-item scheduled">
                    <i class="fa-solid fa-clock mr-1"></i> 
                    Scheduled: {{ formatDate(announcement.scheduled_at) }}
                 </span>
                 <span v-if="announcement.is_urgent" class="meta-item urgent">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i> 
                    Urgent
                 </span>
              </div>
           </div>

           <div class="card-actions">
              <router-link 
                :to="{ name: 'announcements.preview', params: { id: announcement.id } }"
                class="btn-action btn-primary"
              >
                Read More
              </router-link>
           </div>
        </div>
      </div>

       <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="fa-regular fa-bell empty-icon"></i>
        <h3>No announcements found</h3>
        <p>There are no announcements matching your search criteria.</p>
        <button @click="resetFilters" class="btn-link">Reset Filters</button>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination-container">
            <button 
              :disabled="pagination.current_page === 1" 
              @click="fetchAnnouncements(pagination.current_page - 1)"
              class="page-btn prev"
            >
               <i class="fa-solid fa-chevron-left"></i>
            </button>
            
            <template v-for="p in getVisiblePages()" :key="p">
              <button 
                v-if="p !== '...'"
                @click="fetchAnnouncements(p)"
                class="page-btn"
                :class="{ active: pagination.current_page === p }"
              >
                {{ p }}
              </button>
              <span v-else class="page-ellipsis">...</span>
            </template>

            <button 
              :disabled="pagination.current_page === pagination.last_page" 
              @click="fetchAnnouncements(pagination.current_page + 1)"
              class="page-btn next"
            >
               <i class="fa-solid fa-chevron-right"></i>
            </button>
            
            <div class="pagination-info">
              Showing {{ ((pagination.current_page - 1) * 20) + 1 }} to {{ Math.min(pagination.current_page * 20, pagination.total) }} of {{ pagination.total }} announcements
            </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';

const announcements = ref([]);
const loading = ref(true);
const searchKeyword = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const fetchAnnouncements = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      per_page: 20
    };
    
    if (searchKeyword.value) params.search = searchKeyword.value;
    if (dateFrom.value) params.date_from = dateFrom.value;
    if (dateTo.value) params.date_to = dateTo.value;

    const response = await axios.get('/api/v1/notifications', { 
      params,
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    
    if (response.data.success) {
      const result = response.data.data;
      announcements.value = result.data || [];
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
  }
};

const debounceSearch = debounce(() => {
  fetchAnnouncements(1);
}, 500);

const clearDateFilter = () => {
  dateFrom.value = '';
  dateTo.value = '';
  fetchAnnouncements(1);
};

const resetFilters = () => {
  searchKeyword.value = '';
  dateFrom.value = '';
  dateTo.value = '';
  fetchAnnouncements(1);
};

const viewAnnouncement = (announcement) => {
  selectedAnnouncement.value = announcement;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const truncateText = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const getVisiblePages = () => {
  const current = pagination.value.current_page;
  const total = pagination.value.last_page;
  const pages = [];
  
  if (total <= 7) {
    // Show all pages if 7 or fewer
    for (let i = 1; i <= total; i++) {
      pages.push(i);
    }
  } else {
    // Always show first page
    pages.push(1);
    
    if (current > 4) {
      pages.push('...');
    }
    
    // Show pages around current
    const start = Math.max(2, current - 1);
    const end = Math.min(total - 1, current + 1);
    
    for (let i = start; i <= end; i++) {
      if (!pages.includes(i)) {
        pages.push(i);
      }
    }
    
    if (current < total - 3) {
      pages.push('...');
    }
    
    // Always show last page
    if (!pages.includes(total)) {
      pages.push(total);
    }
  }
  
  return pages;
};

onMounted(() => {
  fetchAnnouncements();
});
</script>

<style scoped>
.announcements-page {
  background: #f8fafd;
  min-height: 100vh;
}

.content-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* Search Section */
.search-container {
    background: white;
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 20px;
}

.search-wrapper {
  background: #f3f6f9;
  border-radius: 12px;
  display: flex;
  align-items: center;
  padding: 8px 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  transition: all 0.2s;
}

.search-wrapper:focus-within {
    background: white;
    border-color: #3699ff;
    box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
}

.search-icon {
  color: #b5b5c3;
  font-size: 1.2rem;
  margin-right: 15px;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 10px 0;
  font-size: 1.1rem;
  outline: none;
  font-weight: 500;
  color: #3f4254;
}

.filter-row {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.date-filters {
  display: flex;
  align-items: center;
  gap: 10px;
}

.date-input {
  padding: 8px 12px;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  background: #f9f9fc;
  font-size: 0.9rem;
  color: #3f4254;
}

.date-input:focus {
  border-color: #3699ff;
  background: white;
  outline: none;
}

.date-separator {
  color: #7e8299;
  font-weight: 500;
  font-size: 0.9rem;
}

.clear-date-btn {
  background: #f64e60;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.8rem;
}

/* Listings */
.announcement-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.announcement-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: flex-start;
    gap: 25px;
    transition: all 0.3s;
    border: 1px solid #eff2f5;
}

.announcement-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border-color: #3699ff;
}

.card-left { flex-shrink: 0; }

.card-icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    background: #fff4de;
    color: #ffa800;
    overflow: hidden;
}

.card-img { width: 100%; height: 100%; object-fit: cover; }

.card-body { flex: 1; }

.card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 10px;
}

.card-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: #181c32;
    line-height: 1.3;
}

.card-date {
    font-size: 0.85rem;
    color: #b5b5c3;
    font-weight: 500;
    white-space: nowrap;
}

.card-content {
    margin-bottom: 15px;
}

.card-description {
    margin: 0;
    color: #5e6278;
    font-weight: 500;
    line-height: 1.6;
}

.card-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.meta-item {
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.meta-item.scheduled {
    color: #3699ff;
}

.meta-item.urgent {
    color: #f64e60;
}

/* Actions */
.card-actions {
    display: flex;
    align-items: flex-start;
    padding-top: 5px;
}

.btn-action {
    padding: 10px 24px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s;
    white-space: nowrap;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #3699ff;
    color: white;
    box-shadow: 0 4px 12px rgba(54, 153, 255, 0.2);
}

.btn-primary:hover {
    background: #0084ff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(54, 153, 255, 0.3);
}

/* Utilities */
.shadow-sm { box-shadow: 0 0 20px 0 rgba(76,87,125,0.02); }
.mb-10 { margin-bottom: 40px; }
.mr-1 { margin-right: 5px; }

/* Responsive */
@media (max-width: 768px) {
    .announcement-card { 
      flex-direction: column; 
      text-align: center; 
      padding: 20px; 
    }
    
    .card-header { 
      flex-direction: column; 
      align-items: center; 
      text-align: center;
    }
    
    .card-meta {
      justify-content: center;
    }
    
    .modal-content {
      margin: 10px;
    }
    
    .modal-header,
    .modal-body {
      padding: 20px;
    }
}

/* Loading & Empty */
.loading-state, .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #b5b5c3;
}
.empty-icon { font-size: 4rem; opacity: 0.5; margin-bottom: 15px; }
.btn-link { 
    background: none; border: none; 
    color: #3699ff; font-weight: 700; 
    cursor: pointer; text-decoration: underline; 
    margin-top: 10px;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 50px;
    flex-wrap: wrap;
}

.page-btn {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid #e1e3ea;
    background: white;
    color: #7e8299;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-btn.active {
    background: #3699ff;
    color: white;
    border-color: #3699ff;
    box-shadow: 0 4px 10px rgba(54, 153, 255, 0.3);
}

.page-btn:hover:not(.active):not(:disabled) {
    color: #3699ff;
    border-color: #3699ff;
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-ellipsis {
    padding: 0 8px;
    color: #7e8299;
    font-weight: 600;
}

.pagination-info {
    font-size: 0.9rem;
    color: #7e8299;
    margin-left: 20px;
    white-space: nowrap;
}

.spinner {
   width: 40px; height: 40px;
   border: 4px solid #e1f0ff;
   border-top-color: #3699ff;
   border-radius: 50%;
   animation: spin 1s linear infinite;
   margin: 0 auto 15px;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>