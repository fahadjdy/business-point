<template>
  <div class="vendor-show-wrapper">
    <div v-if="vendor" class="vendor-details pulsate-in">
      <div class="top-info-card">
        <div class="vendor-main">
          <div class="vendor-avatar">
            {{ vendor.user.name.charAt(0) }}
          </div>
          <div class="vendor-core">
            <h2>{{ vendor.user.name }}</h2>
            <div class="badges">
              <Badge type="primary">{{ vendor.vendor_type }}</Badge>
              <Badge :type="vendor.is_verified ? 'success' : 'danger'">
                {{ vendor.is_verified ? 'Verified' : 'Unverified' }}
              </Badge>
              <Badge :type="vendor.status === 'active' ? 'success' : 'warning'">
                {{ vendor.status }}
              </Badge>
            </div>
          </div>
        </div>
        <div class="vendor-actions">
          <BaseButton :variant="vendor.is_verified ? 'danger' : 'success'" icon="fa-solid fa-shield-halved" @click="toggleVerification" :loading="busy">
            {{ vendor.is_verified ? 'Revoke Verification' : 'Verify Vendor' }}
          </BaseButton>
          <BaseButton variant="light" icon="fa-solid fa-pen-to-square" class="mx-2">Edit</BaseButton>
          <BaseButton variant="danger" icon="fa-solid fa-trash">Delete</BaseButton>
        </div>
      </div>

      <div class="info-grid">
        <div class="card basic-info">
          <div class="card-header"><h3>Business Details</h3></div>
          <div class="card-body">
            <div class="info-row"><label>Email</label><span>{{ vendor.user.email }}</span></div>
            <div class="info-row"><label>Phone</label><span>{{ vendor.user.phone || '-' }}</span></div>
            <div class="info-row"><label>Address</label><span>{{ vendor.address || 'N/A' }}</span></div>
            <div class="info-row"><label>Joined</label><span>{{ formatDate(vendor.created_at) }}</span></div>
          </div>
        </div>

        <div class="card type-info">
          <div class="card-header"><h3>Type Specific Info</h3></div>
          <div class="card-body">
            <template v-if="vendor.vendor_type === 'shop'">
              <div class="info-row"><label>Shop Category</label><span>{{ vendor.shop_info?.category || 'Retail' }}</span></div>
              <div class="info-row"><label>Total Products</label><span>{{ vendor.shop_info?.products_count || 0 }}</span></div>
            </template>
            <template v-else>
              <div class="info-row"><label>Specialization</label><span>{{ vendor.specialization || 'General' }}</span></div>
            </template>
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
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';

const route = useRoute();
const vendor = ref(null);
const busy = ref(false);

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
    // API logic to toggle verification
    vendor.value.is_verified = !vendor.value.is_verified;
  } finally {
    busy.value = false;
  }
};

const formatDate = (date) => new Date(date).toLocaleDateString();

onMounted(fetchData);
</script>

<style scoped>
.top-info-card {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.02);
}

.vendor-main { display: flex; align-items: center; gap: 20px; }

.vendor-avatar {
  width: 80px;
  height: 80px;
  background: #f3f6f9;
  color: #3699ff;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
}

.vendor-core h2 { margin: 0 0 10px; font-size: 1.5rem; color: #1e1e2d; }
.badges { display: flex; gap: 8px; }

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.card-header { padding: 15px 20px; border-bottom: 1px solid #eff2f5; }
.card-header h3 { margin: 0; font-size: 1.1rem; color: #3f4254; }

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px dashed #eff2f5;
}

.info-row:last-child { border-bottom: none; }

.info-row label { color: #b5b5c3; font-weight: 500; }
.info-row span { color: #3f4254; font-weight: 600; }

.loading-state { height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; color: #b5b5c3; }

.mx-2 { margin-left: 10px; margin-right: 10px; }
</style>
