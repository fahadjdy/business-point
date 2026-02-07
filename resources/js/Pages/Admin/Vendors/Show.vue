<template>
  <div class="vendor-show-wrapper">
    <div v-if="vendor" class="vendor-details pulsate-in">
      <!-- Header Section -->
      <div class="header-card">
        <div class="header-left">
          <div class="vendor-avatar">
            {{ vendor.business_name?.charAt(0) || vendor.user?.name?.charAt(0) || 'B' }}
          </div>
          <div class="vendor-identity">
            <h1>{{ vendor.business_name || vendor.user?.name || 'Unknown Business' }}</h1>
            <div class="badges">
              <Badge type="primary">{{ vendor.vendor_type }}</Badge>
              <Badge :type="vendor.is_verified ? 'success' : 'danger'">
                <i class="fa-solid fa-shield-check mr-1" v-if="vendor.is_verified"></i>
                {{ vendor.is_verified ? 'Verified' : 'Unverified' }}
              </Badge>
              <Badge :type="vendor.status === 'active' ? 'success' : 'warning'">
                {{ vendor.status }}
              </Badge>
            </div>
          </div>
        </div>
        <div class="header-actions">
           <BaseButton 
              :variant="vendor.is_verified ? 'danger' : 'success'" 
              :icon="vendor.is_verified ? 'fa-solid fa-shield-slash' : 'fa-solid fa-shield-heart'" 
              @click="toggleVerification" 
              :loading="busy"
           >
            {{ vendor.is_verified ? 'Revoke Verification' : 'Verify Business' }}
          </BaseButton>
          <BaseButton variant="light" icon="fa-solid fa-pen" class="mx-2">Edit</BaseButton>
          <BaseButton variant="danger" icon="fa-solid fa-trash">Delete</BaseButton>
        </div>
      </div>

      <div class="details-grid">
        <!-- Left Column: Business Bio & Description -->
        <div class="grid-left">
          <div class="card basic-info">
            <div class="card-header">
              <h3><i class="fa-solid fa-file-lines mr-2"></i> Business Description</h3>
            </div>
            <div class="card-body">
              <p class="business-description">{{ vendor.description || 'No description provided.' }}</p>
            </div>
          </div>

          <!-- Type Specific Info -->
          <div class="card type-info">
            <div class="card-header">
              <h3><i class="fa-solid fa-briefcase mr-2"></i> {{ vendor.vendor_type }} Information</h3>
            </div>
            <div class="card-body">
              <template v-if="vendor.vendor_type === 'shop'">
                <div class="info-row"><label>Shop Category</label><span>{{ vendor.shop?.category?.name || vendor.shop?.category || 'Retail' }}</span></div>
                <div class="info-row"><label>Total Products</label><span>{{ vendor.shop?.products_count || 0 }}</span></div>
              </template>
              <template v-else-if="vendor.vendor_type === 'doctor'">
                <div class="info-row"><label>Specialization</label><span>{{ vendor.doctor?.specialization || 'General' }}</span></div>
                <div class="info-row"><label>Qualification</label><span>{{ vendor.doctor?.qualification || 'N/A' }}</span></div>
              </template>
              <template v-else-if="vendor.vendor_type === 'barber'">
                <div class="info-row"><label>Services</label><span>{{ vendor.barber?.services || 'Standard' }}</span></div>
              </template>
            </div>
          </div>

          <div class="card hours-info">
            <div class="card-header">
              <h3><i class="fa-solid fa-clock mr-2"></i> Operating Hours</h3>
            </div>
            <div class="card-body">
              <div v-if="vendor.opening_times && vendor.opening_times.length" class="hours-list">
                <div v-for="time in vendor.opening_times" :key="time.id" class="hour-row">
                  <label>{{ formatDay(time.day_of_week) }}</label>
                  <span>{{ time.open_time }} - {{ time.close_time }}</span>
                </div>
              </div>
              <p v-else class="text-muted italic">Hours not specified.</p>
            </div>
          </div>
        </div>

        <!-- Right Column: Contact & Location -->
        <div class="grid-right">
          <!-- Contact Details -->
          <div class="card contact-info">
            <div class="card-header">
              <h3><i class="fa-solid fa-address-book mr-2"></i> Contact Details</h3>
            </div>
            <div class="card-body">
              <div class="contact-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                  <label>Phone Number</label>
                  <p>{{ vendor.phone || vendor.user?.phone || '-' }}</p>
                </div>
              </div>
              <div class="contact-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                  <label>Email Address</label>
                  <p>{{ vendor.email || vendor.user?.email || '-' }}</p>
                </div>
              </div>
              <div class="contact-item">
                <i class="fa-solid fa-globe"></i>
                <div>
                  <label>Website</label>
                  <p><a :href="vendor.website" target="_blank" v-if="vendor.website" class="text-primary">{{ vendor.website }}</a><span v-else>-</span></p>
                </div>
              </div>
              <div class="contact-item">
                <i class="fa-solid fa-user"></i>
                <div>
                  <label>Registered User</label>
                  <p>{{ vendor.user?.name }} (ID: #{{ vendor.user?.id }})</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Location Details -->
          <div class="card location-info">
            <div class="card-header">
              <h3><i class="fa-solid fa-location-dot mr-2"></i> Location Info</h3>
            </div>
            <div class="card-body">
              <div class="contact-item">
                <i class="fa-solid fa-map-marker-alt"></i>
                <div>
                  <label>Full Address</label>
                  <p>{{ vendor.address || 'N/A' }}</p>
                </div>
              </div>
              <div class="contact-item" v-if="vendor.city || vendor.state">
                <i class="fa-solid fa-city"></i>
                <div>
                  <label>City & State</label>
                  <p>{{ vendor.city }}{{ vendor.city && vendor.state ? ', ' : '' }}{{ vendor.state }}</p>
                </div>
              </div>
              <div class="contact-item" v-if="vendor.latitude && vendor.longitude">
                <i class="fa-solid fa-location-crosshairs"></i>
                <div>
                  <label>Coordinates</label>
                  <p>{{ vendor.latitude }}, {{ vendor.longitude }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Business Images -->
          <div class="card images-info" v-if="vendor.images?.length">
            <div class="card-header">
              <h3><i class="fa-solid fa-images mr-2"></i> Business Images</h3>
            </div>
            <div class="card-body p-2">
              <div class="image-grid">
                <div v-for="(img, idx) in vendor.images" :key="idx" class="admin-image-item">
                  <img :src="img" alt="Business Image" @click="openImage(img)">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="loading-state">
      <i class="fa-solid fa-spinner fa-spin fa-3x text-primary"></i>
      <p>Loading vendor details...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { formatDate } from '@/utils/formatters.js';

const route = useRoute();
const vendor = ref(null);
const busy = ref(false);

const dayMap = {
  'mon': 'Monday', 'tue': 'Tuesday', 'wed': 'Wednesday',
  'thu': 'Thursday', 'fri': 'Friday', 'sat': 'Saturday', 'sun': 'Sunday'
};

const formatDay = (day) => dayMap[day?.toLowerCase()] || day;

const openImage = (url) => {
    window.open(url, '_blank');
};

const fetchData = async () => {
  try {
    const response = await axios.get(`/admin/vendors-data/${route.params.id}`);
    if (response.data.success) {
      vendor.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching vendor:', error);
  }
};

const toggleVerification = async () => {
  busy.value = true;
  try {
    const endpoint = vendor.value.is_verified ? 'reject' : 'approve';
    const response = await axios.post(`/admin/vendors-data/${vendor.value.id}/${endpoint}`, {
        reason: vendor.value.is_verified ? 'Admin revoked verification' : 'Verified by admin'
    });
    
    if (response.data.success) {
        vendor.value.is_verified = !vendor.value.is_verified;
        vendor.value.verification_status = vendor.value.is_verified ? 'approved' : 'rejected';
    }
  } catch (error) {
    console.error('Action failed:', error);
  } finally {
    busy.value = false;
  }
};

onMounted(fetchData);
</script>

<style scoped>
.header-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.03);
  border-left: 5px solid #3699ff;
}

.header-left { display: flex; align-items: center; gap: 20px; }

.vendor-avatar {
  width: 90px;
  height: 90px;
  background: #f3f6f9;
  color: #3699ff;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: 800;
  border: 1px solid #e1e3ea;
}

.vendor-identity h1 { 
  margin: 0 0 10px; 
  font-size: 1.8rem; 
  color: #1e1e2d; 
  font-weight: 800;
}

.badges { display: flex; gap: 10px; }

.details-grid {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 30px;
}

.card {
  background: white;
  border-radius: 12px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  border: 1px solid #eff2f5;
  overflow: hidden;
}

.card-header { 
  padding: 18px 25px; 
  background: #fbfbfb; 
  border-bottom: 1px solid #eff2f5; 
}

.card-header h3 { 
  margin: 0; 
  font-size: 1.1rem; 
  font-weight: 700; 
  color: #3f4254; 
}

.card-body { padding: 25px; }

.business-description { 
  font-size: 1.05rem; 
  line-height: 1.6; 
  color: #5e6278; 
  white-space: pre-line;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px dashed #eff2f5;
}

.info-row:last-child { border-bottom: none; }
.info-row label { color: #b5b5c3; font-weight: 600; }
.info-row span { color: #3f4254; font-weight: 700; }

.contact-item {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.contact-item i {
  width: 40px;
  height: 40px;
  background: #f3f6f9;
  color: #3699ff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.contact-item label { 
  display: block; 
  font-size: 0.8rem; 
  color: #b5b5c3; 
  font-weight: 600; 
  margin-bottom: 2px;
}

.contact-item p { 
  margin: 0; 
  font-weight: 700; 
  color: #3f4254; 
  word-break: break-all;
}

.hour-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f8f9fa;
}

.hour-row label { font-weight: 600; color: #7e8299; }
.hour-row span { font-weight: 700; color: #3f4254; }

.image-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.admin-image-item {
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #eee;
}

.admin-image-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.2s;
}

.admin-image-item img:hover {
  transform: scale(1.05);
}

.p-2 { padding: 10px; }
.text-muted { color: #b5b5c3; }
.italic { font-style: italic; }
.mx-2 { margin-left: 10px; margin-right: 10px; }
.mr-2 { margin-right: 8px; }
.mr-1 { margin-right: 4px; }

.loading-state { height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px; color: #b5b5c3; }

@media (max-width: 992px) {
  .details-grid { grid-template-columns: 1fr; }
  .header-card { flex-direction: column; align-items: flex-start; gap: 20px; }
  .header-actions { width: 100%; display: flex; gap: 10px; }
  .header-actions button { flex: 1; justify-content: center; }
}
</style>
