<template>
  <div class="community-directory-page">
    <div class="content-container">
      
      <!-- Search Section -->
      <div class="search-section mb-10">
        <div class="search-container shadow-sm">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input 
                    type="text" 
                    v-model="searchKeyword" 
                    placeholder="Search by name, category, or service..." 
                    class="search-input"
                    @input="debounceSearch"
                >
            </div>
            
            <div class="flex justify-between items-center mb-4 px-1">
               <h2 class="text-lg font-bold text-gray-700">Directory</h2>
               <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                  {{ pagination.total }} Members
               </span>
            </div>

            <div class="filter-row">
               <!-- Tabs -->
               <div class="filter-tabs shadow-sm">
                  <button 
                    v-for="type in typeOptions" 
                    :key="type.value"
                    @click="selectedType = type.value; fetchContacts(1)"
                    class="filter-tab"
                    :class="{ active: selectedType === type.value }"
                  >
                    {{ type.label }}
                  </button>
               </div>

               <!-- Quick Tags with Filter Icon -->
                <div class="quick-tags">
                  <button 
                    v-for="tag in quickTags" 
                    :key="tag.label"
                    @click="applyQuickTag(tag.label)"
                    class="tag-pill"
                    :class="{ active: searchKeyword === tag.label }"
                  >
                     {{ tag.label }} <i :class="tag.icon" class="ml-2 icon-opacity"></i>
                  </button>
                  
                  <!-- Filter Icon for All Tags -->
                  <button 
                    @click="showAllTagsFilter = !showAllTagsFilter"
                    class="tag-pill filter-pill"
                    :class="{ active: selectedTag && searchKeyword === selectedTag.name }"
                  >
                     <i class="fa-solid fa-filter mr-2"></i> {{ selectedTag && searchKeyword === selectedTag.name ? selectedTag.name : 'Filter' }}
                  </button>
               </div>

               <!-- All Tags Filter Dropdown -->
               <div v-if="showAllTagsFilter" class="all-tags-dropdown">
                  <div class="dropdown-header">
                     <span>Filter by Tags</span>
                     <button @click="clearAllTagsFilter" class="clear-btn">Clear</button>
                  </div>
                  <div class="dropdown-content">
                     <button 
                        v-for="tag in availableTags" 
                        :key="tag.id"
                        @click="selectTagFromAll(tag)"
                        class="dropdown-item"
                        :class="{ active: selectedTag?.id === tag.id }"
                     >
                        <span class="tag-name">{{ tag.name }}</span>
                        <span class="tag-category">{{ tag.category }}</span>
                     </button>
                  </div>
               </div>
            </div>
        </div>
      </div>

      <!-- Loading State -->
       <div v-if="loading" class="loading-state">
         <div class="spinner"></div>
         <p>Loading directory...</p>
      </div>

      <!-- Listings -->
      <div v-else-if="contacts.length > 0" class="contact-list">
        <div 
          v-for="contact in contacts" 
          :key="contact.id" 
          class="directory-card shadow-sm"
          :class="contact.type"
        >
           <div class="card-left">
              <div class="card-icon-wrapper">
                 <img v-if="contact.image" :src="contact.image" class="card-img" />
                 <i v-else :class="getContactIcon(contact.type, contact.designation)"></i>
              </div>
           </div>

           <div class="card-body">
              <div class="card-header">
                 <h3 class="card-title">{{ contact.name }}</h3>
                 <span class="card-badge" :class="contact.type">{{ contact.type }}</span>
              </div>
              <p class="card-subtitle">{{ contact.designation || contact.department || 'Village Member' }}</p>
              
              <div class="card-details">
                 <span class="detail-item phone">
                    <i class="fa-solid fa-phone mr-1"></i> {{ contact.phone }}
                 </span>
                 <span v-if="contact.address" class="detail-item address">
                    <i class="fa-solid fa-location-dot mr-1"></i> {{ contact.address }}
                 </span>
              </div>
              
              <!-- Tags -->
              <div v-if="contact.tags && contact.tags.length > 0" class="card-tags">
                 <span 
                    v-for="tag in contact.tags.slice(0, 3)" 
                    :key="tag.id"
                    class="contact-tag"
                    :class="tag.category"
                 >
                    {{ tag.name }}
                 </span>
                 <span v-if="contact.tags.length > 3" class="more-tags">
                    +{{ contact.tags.length - 3 }} more
                 </span>
              </div>
           </div>

           <div class="card-actions">
              <router-link 
                :to="{ name: 'community-directory.show', params: { id: contact.id } }"
                class="btn-action btn-outline"
              >
                Details
              </router-link>
              <a 
                :href="'tel:' + contact.phone"
                class="btn-action btn-primary"
              >
                Call Now
              </a>
           </div>
        </div>
      </div>

       <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="fa-regular fa-folder-open empty-icon"></i>
        <h3>No contacts found</h3>
        <p>Try changing your search terms or filters.</p>
        <button @click="resetFilters" class="btn-link">Reset Filters</button>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination-container">
            <button 
              :disabled="pagination.current_page === 1" 
              @click="fetchContacts(pagination.current_page - 1)"
              class="page-btn prev"
            >
               <i class="fa-solid fa-chevron-left"></i>
            </button>
            
            <template v-for="p in getVisiblePages()" :key="p">
              <button 
                v-if="p !== '...'"
                @click="fetchContacts(p)"
                class="page-btn"
                :class="{ active: pagination.current_page === p }"
              >
                {{ p }}
              </button>
              <span v-else class="page-ellipsis">...</span>
            </template>

            <button 
              :disabled="pagination.current_page === pagination.last_page" 
              @click="fetchContacts(pagination.current_page + 1)"
              class="page-btn next"
            >
               <i class="fa-solid fa-chevron-right"></i>
            </button>
            
            <div class="pagination-info">
              Showing {{ ((pagination.current_page - 1) * 20) + 1 }} to {{ Math.min(pagination.current_page * 20, pagination.total) }} of {{ pagination.total }} contacts
            </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';

const contacts = ref([]);
const loading = ref(true);
const searchKeyword = ref('');
const selectedType = ref('');
const selectedTag = ref(null);
const availableTags = ref([]);
const showAllTagsFilter = ref(false);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const typeOptions = [
  { label: 'All', value: '' },
  { label: 'Person', value: 'person' },
  { label: 'Business', value: 'business' },
  { label: 'Service', value: 'service' }
];

const quickTags = [
  { label: 'Doctor', icon: 'fa-solid fa-briefcase-medical' },
  { label: 'Grocery', icon: 'fa-solid fa-cart-shopping' },
  { label: 'Plumber', icon: 'fa-solid fa-wrench' },
  { label: 'Electrician', icon: 'fa-solid fa-bolt' }
];

const fetchContacts = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      sort_by: 'name',
      sort_order: 'asc',
      is_active: 1
    };
    
    // Use keyword OR tag_id depending on how the user interacted
    if (searchKeyword.value) params.keyword = searchKeyword.value;
    if (selectedTag.value) params.tag_id = selectedTag.value.id;
    if (selectedType.value) params.type = selectedType.value;

    const response = await axios.get('/api/v1/community-directory', { 
      params,
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    if (response.data.success) {
      const result = response.data.data;
      contacts.value = result.data || [];
      pagination.value = {
        current_page: result.current_page,
        last_page: result.last_page,
        total: result.total
      };
    }
  } catch (error) {
    console.error('Error fetching contacts:', error);
  } finally {
    loading.value = false;
  }
};

const fetchTags = async () => {
  try {
    const response = await axios.get('/api/v1/tags/all', {
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    if (response.data.success) {
      availableTags.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching tags:', error);
  }
};

const selectTagFromAll = (tag) => {
  selectedTag.value = tag;
  searchKeyword.value = tag.name; // Auto-fill search box when selecting from dropdown too
  showAllTagsFilter.value = false;
  fetchContacts(1);
};

const clearAllTagsFilter = () => {
  selectedTag.value = null;
  searchKeyword.value = '';
  showAllTagsFilter.value = false;
  fetchContacts(1);
};

const applyQuickTag = (tagName) => {
  searchKeyword.value = tagName; // Auto-fill search box
  
  // Find the tag by name for proper tag-based filtering if it exists
  const tag = availableTags.value.find(t => t.name.toLowerCase() === tagName.toLowerCase());
  if (tag) {
    selectedTag.value = tag;
  } else {
    selectedTag.value = null;
  }
  
  // Keep the type filter if it's already set, or clear it? 
  // User usually wants to search within the current type (e.g. Service -> Plumber)
  // But common behavior for "Quick Filters" is often a fresh search.
  // I'll keep it cumulative unless they switch tabs.
  
  fetchContacts(1);
};

const debounceSearch = debounce(() => {
  if (!searchKeyword.value) {
    selectedTag.value = null;
  } else {
    // Auto-sync tag if typed keyword matches exactly
    const tag = availableTags.value.find(t => t.name.toLowerCase() === searchKeyword.value.trim().toLowerCase());
    if (tag) {
       selectedTag.value = tag;
    }
  }
  fetchContacts(1);
}, 500);

const resetFilters = () => {
  searchKeyword.value = '';
  selectedType.value = '';
  selectedTag.value = null;
  fetchContacts(1);
};

const getContactIcon = (type, designation) => {
  if (type === 'service') return 'fa-solid fa-graduation-cap';
  if (type === 'business') return 'fa-solid fa-store';
  if (designation?.toLowerCase().includes('doctor')) return 'fa-solid fa-user-doctor';
  return 'fa-solid fa-user';
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
  fetchContacts();
  fetchTags();
  
  // Close filter dropdown when clicking outside
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.quick-tags')) {
      showAllTagsFilter.value = false;
    }
  });
});

onUnmounted(() => {
  // Cleanup if needed
});
</script>

<style scoped>
.community-directory-page {
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
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  position: relative; /* Added to fix dropdown positioning */
}

.card-tags {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.contact-tag {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.contact-tag.profession { background: #fff4de; color: #ffa800; }
.contact-tag.skill { background: #e1f0ff; color: #3699ff; }
.contact-tag.business { background: #c9f7f5; color: #1bc5bd; }
.contact-tag.service { background: #fce4ec; color: #e91e63; }

.more-tags {
  font-size: 0.75rem;
  color: #7e8299;
  font-style: italic;
}

.filter-tabs {
    display: inline-flex;
    background: #f3f6f9;
    padding: 5px;
    border-radius: 12px;
}

.filter-tab {
    padding: 10px 20px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-weight: 700;
    color: #7e8299;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-tab.active {
    background: #3699ff;
    color: white;
    box-shadow: 0 4px 12px rgba(54, 153, 255, 0.3);
}

.filter-tab:hover:not(.active) {
    color: #3699ff;
    background: rgba(54, 153, 255, 0.1);
}

.quick-tags {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.tag-pill {
    padding: 8px 16px;
    border: 1px solid #e1e3ea;
    background: white;
    border-radius: 50px;
    color: #5e6278;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
}

.tag-pill:hover {
    border-color: #3699ff;
    color: #3699ff;
    background: #e1f0ff;
}

.tag-pill.active {
    background: #3699ff;
    color: white !important;
    border-color: #3699ff;
}

.tag-pill.active .icon-opacity {
    opacity: 1;
}

.filter-pill {
    background: #f8f9fa;
    border-color: #dee2e6;
}

.filter-pill:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.all-tags-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e1e3ea;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  z-index: 1000;
  max-height: 300px;
  overflow-y: auto;
  margin-top: 5px;
  min-width: 250px;
}

.dropdown-header {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f1f2;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
  color: #3f4254;
}

.clear-btn {
  background: none;
  border: none;
  color: #3699ff;
  font-size: 0.85rem;
  cursor: pointer;
  font-weight: 600;
}

.dropdown-content {
  max-height: 200px;
  overflow-y: auto;
}

.dropdown-item {
  width: 100%;
  padding: 10px 16px;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dropdown-item:hover {
  background: #f8f9fa;
}

.dropdown-item.active {
  background: #e1f0ff;
  color: #3699ff;
}

.tag-name {
  font-weight: 600;
}

.tag-category {
  font-size: 0.8rem;
  color: #7e8299;
  text-transform: capitalize;
}

.icon-opacity { opacity: 0.6; }

/* Listings */
.contact-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.directory-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 25px;
    transition: all 0.3s;
    border: 1px solid #eff2f5;
}

.directory-card:hover {
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
    background: #f3f6f9;
    color: #b5b5c3;
    overflow: hidden;
}

.directory-card.person .card-icon-wrapper { background: #fff4de; color: #ffa800; }
.directory-card.business .card-icon-wrapper { background: #e1f0ff; color: #3699ff; }
.directory-card.service .card-icon-wrapper { background: #c9f7f5; color: #1bc5bd; }

.card-img { width: 100%; height: 100%; object-fit: cover; }

.card-body { flex: 1; }

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.card-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: #181c32;
}

.card-badge {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
}

.card-badge.person { background: #fff4de; color: #ffa800; }
.card-badge.business { background: #e1f0ff; color: #3699ff; }
.card-badge.service { background: #c9f7f5; color: #1bc5bd; }

.card-subtitle {
    margin: 0 0 12px 0;
    color: #b5b5c3;
    font-weight: 500;
}

.card-details {
    display: flex;
    gap: 20px;
    font-size: 0.95rem;
    font-weight: 600;
}

.detail-item.phone { color: #3699ff; }
.detail-item.address { color: #7e8299; }

/* Actions */
.card-actions {
    display: flex;
    gap: 15px;
}

.btn-action {
    padding: 10px 24px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-outline {
    border: 1px solid #e1e3ea;
    color: #7e8299;
}

.btn-outline:hover {
    border-color: #3699ff;
    color: #3699ff;
    background: #f9f9fc;
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
.ml-2 { margin-left: 8px; }
.mr-1 { margin-right: 5px; }

/* Responsive */
@media (max-width: 768px) {
    .filter-row { 
      flex-direction: column; 
      align-items: stretch; 
      gap: 15px;
    }
    .filter-tabs { display: flex; overflow-x: auto; }
    .filter-tab { flex: 1; text-align: center; white-space: nowrap; }
    
    .directory-card { flex-direction: column; text-align: center; padding: 20px; }
    .card-header, .card-details { justify-content: center; flex-wrap: wrap; }
    .card-actions { width: 100%; }
    .btn-action { flex: 1; }
    
    .card-tags {
      justify-content: center;
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
