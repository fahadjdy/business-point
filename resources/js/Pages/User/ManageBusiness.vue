<template>
  <div class="manage-business-wrapper" v-if="vendor">
    <div class="container container-dashboard py-5">
      <!-- Dashboard Header -->
      <div class="dashboard-header mb-5">
        <div class="header-content">
          <h1 class="h2 font-weight-bold">Manage {{ vendorLabel }}</h1>
          <p class="text-muted">Welcome back! Here's what's happening with your business today.</p>
        </div>
        <div class="header-actions">
           <router-link to="/" class="btn btn-light-primary mr-2">
             <i class="fa-solid fa-house mr-1"></i> Home
           </router-link>
           <button class="btn btn-primary shadow-sm" @click="openEditModal">
             <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Business
           </button>
        </div>
      </div>

      <!-- Edit Business Modal -->
      <BaseModal :show="showEditModal" title="Edit Business Profile" size="xl" @close="showEditModal = false">
        <form @submit.prevent="updateBusiness">
          <div class="p-6">
            <div class="grid-form">
              <div class="form-group full-width">
                <label class="form-label">Business Name</label>
                <input v-model="form.business_name" type="text" class="form-control" placeholder="Business Name" required />
              </div>

              <div class="form-group full-width">
                <label class="form-label">Description</label>
                <textarea v-model="form.description" class="form-control" rows="4" placeholder="Business description..."></textarea>
              </div>

              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input v-model="form.phone" type="text" class="form-control" placeholder="Phone" required />
              </div>

              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input v-model="form.email" type="email" class="form-control" placeholder="Email" required />
              </div>

              <div class="form-group full-width">
                <label class="form-label">Website (Optional)</label>
                <input v-model="form.website" type="url" class="form-control" placeholder="https://..." />
              </div>

              <div class="form-group full-width">
                <label class="form-label">Street Address</label>
                <input v-model="form.address" type="text" class="form-control" placeholder="123 Main St" required />
              </div>

              <div class="form-group">
                <label class="form-label">City</label>
                <input v-model="form.city" type="text" class="form-control" placeholder="City" required />
              </div>

              <div class="form-group">
                <label class="form-label">State/Province</label>
                <input v-model="form.state" type="text" class="form-control" placeholder="State" required />
              </div>
            </div>
          </div>
        </form>
        <template #footer>
          <button class="btn btn-secondary mr-2" @click="showEditModal = false">Cancel</button>
          <button class="btn btn-primary" :loading="updating" @click="updateBusiness">Save Changes</button>
        </template>
      </BaseModal>

      <!-- Quick Stats -->
      <div class="stats-row mb-5">
        <div class="stat-item">
          <div class="stat-card">
            <div class="stat-icon bg-light-primary text-primary">
              <i class="fa-solid fa-box"></i>
            </div>
            <div class="stat-info">
              <h3>{{ productCount }}</h3>
              <p>Products/Services</p>
            </div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-card">
            <div class="stat-icon bg-light-success text-success">
              <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="stat-info">
              <h3>24</h3>
              <p>Monthly Views</p>
            </div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-card">
            <div class="stat-icon bg-light-warning text-warning">
              <i class="fa-solid fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>0.0</h3>
              <p>Rating</p>
            </div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-card">
            <div class="stat-icon bg-light-info text-info">
              <i class="fa-solid fa-shield-check"></i>
            </div>
            <div class="stat-info">
              <h3>Active</h3>
              <p>Status</p>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="dashboard-main">
          <div class="card business-profile-card shadow-sm mb-4">
            <div class="card-header-custom">
               <h5 class="mb-0">Business Profile</h5>
            </div>
            <div class="card-body">
              <div class="business-top-info mb-4">
                 <div class="business-logo shadow-sm">
                    <img v-if="vendor.image" :src="vendor.image" alt="Business Logo" />
                    <div v-else class="logo-placeholder">
                      {{ vendor.business_name.charAt(0) }}
                    </div>
                 </div>
                 <div class="business-title-meta">
                    <h2 class="h4 mb-1">{{ vendor.business_name }}</h2>
                    <p class="text-muted mb-2"><i class="fa-solid fa-location-dot mr-1"></i> {{ vendor.address }}, {{ vendor.city }}, {{ vendor.state }}</p>
                    <div class="badges">
                       <span class="badge badge-primary">{{ vendor.vendor_type }}</span>
                       <span class="badge badge-success ml-2">Verified Business</span>
                    </div>
                 </div>
              </div>

              <div class="section-divider mb-4"></div>

              <div class="business-section mb-4">
                <h6 class="section-label">Business Description</h6>
                <p class="description-text">{{ vendor.description }}</p>
              </div>

              <div class="business-section">
                <h6 class="section-label">Contact Information</h6>
                <div class="contact-grid">
                   <div class="contact-cell">
                      <label><i class="fa-solid fa-envelope mr-1"></i> Email</label>
                      <p>{{ vendor.email }}</p>
                   </div>
                   <div class="contact-cell">
                      <label><i class="fa-solid fa-phone mr-1"></i> Phone</label>
                      <p>{{ vendor.phone }}</p>
                   </div>
                   <div class="contact-cell" v-if="vendor.website">
                      <label><i class="fa-solid fa-globe mr-1"></i> Website</label>
                      <p><a :href="vendor.website" target="_blank">{{ vendor.website }}</a></p>
                   </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Business Images -->
          <div class="card shadow-sm mb-4" v-if="vendor.images && vendor.images.length">
            <div class="card-header-custom">
               <h5 class="mb-0">Business Gallery</h5>
            </div>
            <div class="card-body">
               <div class="gallery-grid">
                  <div v-for="(img, idx) in vendor.images" :key="idx" class="gallery-item">
                     <img :src="img" alt="Business Image" />
                  </div>
               </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="dashboard-sidebar">
          <div class="card hours-card shadow-sm mb-4">
             <div class="card-header-custom">
                <h5 class="mb-0">Business Hours</h5>
             </div>
             <div class="card-body">
                <div v-if="vendor.opening_times && vendor.opening_times.length" class="hours-list">
                   <div v-for="time in vendor.opening_times" :key="time.id" class="hour-row">
                      <span class="day-label">{{ formatDay(time.day_of_week) }}</span>
                      <span class="time-value">{{ time.open_time }} - {{ time.close_time }}</span>
                   </div>
                </div>
                <p v-else class="text-muted italic text-center py-3">Hours not specified</p>
             </div>
          </div>

          <div class="card shadow-sm">
             <div class="card-header-custom">
                <h5 class="mb-0">Quick Management</h5>
             </div>
             <div class="card-body p-0">
                <div class="action-menu">
                   <router-link v-if="vendor.vendor_type === 'shop'" to="/user/manage-products" class="menu-item">
                      <i class="fa-solid fa-box-open"></i> Manage Products
                   </router-link>
                   <button v-else class="menu-item">
                      <i class="fa-solid fa-calendar-check"></i> Manage Appointments
                   </button>
                   <button class="menu-item">
                      <i class="fa-solid fa-bullhorn"></i> My Announcements
                   </button>
                   <div class="menu-divider"></div>
                   <button class="menu-item text-danger">
                      <i class="fa-solid fa-stop-circle"></i> Pause Business
                   </button>
                </div>
             </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else-if="loading" class="loading-state">
     <div class="loader-content">
        <i class="fa-solid fa-circle-notch fa-spin fa-4x text-primary mb-3"></i>
        <p>Fetching your business details...</p>
     </div>
  </div>
  <div v-else class="container py-5 text-center">
     <div class="card shadow-sm p-5">
        <i class="fa-solid fa-store-slash fa-4x text-danger mb-4"></i>
        <h2>Business Not Found</h2>
        <p class="text-muted">We couldn't find an approved business associated with your account.</p>
        <router-link to="/register-business" class="btn btn-primary mt-3">Register Business</router-link>
     </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import BaseModal from '../../Components/BaseModal.vue';
import swal from '../../utils/swal';

const vendor = ref(null);
const loading = ref(true);
const updating = ref(false);
const showEditModal = ref(false);

const form = reactive({
    business_name: '',
    description: '',
    phone: '',
    email: '',
    website: '',
    address: '',
    city: '',
    state: ''
});

const vendorLabel = computed(() => {
    if (!vendor.value) return 'Business';
    const map = {
        'shop': 'Shop',
        'doctor': 'Doctor/Clinic',
        'barber': 'Barber Shop'
    };
    return map[vendor.value.vendor_type] || 'Business';
});

const productCount = computed(() => {
    if (!vendor.value) return 0;
    // Count depends on vendor type, for shop we check products relation
    return vendor.value.shop?.products_count || 0;
});

const dayMap = {
  'mon': 'Monday', 'tue': 'Tuesday', 'wed': 'Wednesday',
  'thu': 'Thursday', 'fri': 'Friday', 'sat': 'Saturday', 'sun': 'Sunday'
};
const formatDay = (day) => dayMap[day?.toLowerCase()] || day;

const openEditModal = () => {
    Object.assign(form, {
        business_name: vendor.value.business_name,
        description: vendor.value.description,
        phone: vendor.value.phone,
        email: vendor.value.email,
        website: vendor.value.website,
        address: vendor.value.address,
        city: vendor.value.city,
        state: vendor.value.state
    });
    showEditModal.value = true;
};

const updateBusiness = async () => {
    updating.value = true;
    try {
        const response = await axios.put('/api/v1/my-business', form);
        if (response.data.success) {
            vendor.value = response.data.data;
            showEditModal.value = false;
            swal.toast('Business profile updated successfully');
        }
    } catch (error) {
        swal.toast(error.response?.data?.message || 'Update failed', 'error');
    } finally {
        updating.value = false;
    }
};

const fetchVendorData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/v1/my-business');
        if (response.data.success) {
            vendor.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to fetch vendor data', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchVendorData);
</script>

<style scoped>
.manage-business-wrapper {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.container-dashboard {
    max-width: 1200px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    color: #181c32;
    margin-bottom: 5px;
}

/* Stats Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 800;
}

.stat-info p {
    margin: 0;
    color: #b5b5c3;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 30px;
}

.card {
    background: white;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header-custom {
    padding: 20px 25px;
    border-bottom: 1px solid #eff2f5;
    background: #fbfbfb;
}

.card-header-custom h5 {
    font-weight: 700;
    color: #3f4254;
}

/* Business Profil Card */
.business-top-info {
    display: flex;
    gap: 25px;
    align-items: center;
}

.business-logo {
    width: 100px;
    height: 100px;
    border-radius: 15px;
    overflow: hidden;
    background: #f3f6f9;
    border: 3px solid white;
}

.business-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 800;
    color: #3699ff;
}

.description-text {
    line-height: 1.6;
    color: #5e6278;
    white-space: pre-line;
}

.section-label {
    font-weight: 700;
    color: #181c32;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.contact-cell label {
    display: block;
    font-size: 0.75rem;
    color: #b5b5c3;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.contact-cell p {
    font-weight: 600;
    color: #3f4254;
    margin: 0;
}

/* Gallery */
.gallery-grid {
   display: grid;
   grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
   gap: 15px;
}

.gallery-item {
   aspect-ratio: 1;
   border-radius: 10px;
   overflow: hidden;
   border: 1px solid #eee;
}

.gallery-item img {
   width: 100%;
   height: 100%;
   object-fit: cover;
}

/* Hours */
.hour-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #eff2f5;
}

.hour-row:last-child {
    border-bottom: none;
}

.day-label {
    font-weight: 600;
    color: #7e8299;
}

.time-value {
    font-weight: 700;
    color: #181c32;
}

/* Action Menu */
.action-menu {
    display: flex;
    flex-direction: column;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 25px;
    background: none;
    border: none;
    text-align: left;
    font-weight: 600;
    color: #3f4254;
    cursor: pointer;
    transition: all 0.2s;
}

.menu-item:hover {
    background: #f9f9fc;
    color: #3699ff;
}

.menu-item i {
    width: 20px;
    color: #b5b5c3;
    transition: color 0.2s;
}

.menu-item:hover i {
    color: #3699ff;
}

.menu-divider {
    height: 1px;
    background: #eff2f5;
    margin: 5px 0;
}

/* Base Classes */
.bg-light-primary { background: #e1f0ff; }
.bg-light-success { background: #c9f7f5; }
.bg-light-warning { background: #fff4de; }
.bg-light-info { background: #e1e9ff; }

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary { background: #3699ff; color: white; }
.btn-primary:hover { background: #187de4; }
.btn-secondary { background: #f3f6f9; color: #7e8299; }
.btn-secondary:hover { background: #e1e3ea; }
.btn-light-primary { background: #e1f0ff; color: #3699ff; }
.btn-light-primary:hover { background: #3699ff; color: white; }

.badge-primary { background: #e1f0ff; color: #3699ff; }
.badge-success { background: #c9f7f5; color: #1bc5bd; }

.section-divider {
    height: 1px;
    background: #eff2f5;
}

.loading-state {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    text-align: center;
}

.loader-content p {
    color: #7e8299;
    font-weight: 600;
}

/* Modal Form Styles */
.p-6 { padding: 30px; }
.grid-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.full-width { grid-column: span 2; }
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-weight: 700; color: #3f4254; margin-bottom: 8px; font-size: 0.9rem; }
.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e1e3ea;
    border-radius: 8px;
    font-size: 1rem;
    color: #3f4254;
    transition: border-color 0.2s;
}
.form-control:focus { border-color: #3699ff; outline: none; }
textarea.form-control { resize: none; }

@media (max-width: 992px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .dashboard-header { flex-direction: column; align-items: flex-start; gap: 20px; }
    .grid-form { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
}

@media (max-width: 576px) {
    .stats-row { grid-template-columns: 1fr; }
    .contact-grid { grid-template-columns: 1fr; }
}
</style>
