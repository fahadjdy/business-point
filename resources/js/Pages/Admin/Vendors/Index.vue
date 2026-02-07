<template>
  <div class="vendors-page">
    <DataTable 
      title="Vendor Management"
      :columns="columns"
      :data="data.data"
      :pagination="data"
      :loading="loading"
      @paginate="fetchData"
    >
      <template #header>
        <div class="search-box">
          <BaseInput 
            v-model="filters.search" 
            placeholder="Search vendors..." 
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>
      
      <template #actions>
        <div class="flex-actions">
          <BaseSelect
            v-model="filters.vendor_type"
            :options="vendorTypeOptions"
            labelField="label"
            :reduce="(o) => o.value"
            :searchable="false"
            placeholder="All Types"
            class="mr-1 vendor-type-filter"
            @update:modelValue="search"
          />
          <BaseButton variant="primary" icon="fa-solid fa-plus" @click="showAddModal = true">
            Add Vendor
          </BaseButton>
        </div>
      </template>

      <template #cell(id)="{ item }">#{{ item.id }}</template>
      
      <template #cell(business)="{ item }">
        <div class="flex items-center">
          <div class="business-icon-mini mr-3">
            <i v-if="item.vendor_type === 'shop'" class="fa-solid fa-store text-primary"></i>
            <i v-else-if="item.vendor_type === 'doctor'" class="fa-solid fa-user-doctor text-success"></i>
            <i v-else-if="item.vendor_type === 'barber'" class="fa-solid fa-scissors text-warning"></i>
            <i v-else class="fa-solid fa-briefcase text-info"></i>
          </div>
          <div>
            <div class="font-bold text-gray-800">{{ item.business_name || 'Unnamed Business' }}</div>
            <div class="text-xs text-muted">{{ item.user?.name || 'No Owner' }}</div>
          </div>
        </div>
      </template>
      
      <template #cell(location)="{ item }">
        <div class="text-sm font-semibold">{{ item.city }}, {{ item.state }}</div>
        <div class="text-xs text-muted">{{ item.phone }}</div>
      </template>

      <template #cell(is_verified)="{ item }">
        <div class="flex flex-col gap-1">
          <Badge :type="getVerificationBadgeType(item.verification_status)">
            {{ item.verification_status.toUpperCase() }}
          </Badge>
          <span v-if="item.is_verified" class="text-xs text-success font-bold">
            <i class="fa-solid fa-check-double mr-1"></i> Verified
          </span>
        </div>
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <template v-if="item.verification_status === 'pending'">
             <BaseButton 
                variant="success" 
                size="sm" 
                icon="fa-solid fa-check" 
                class="mr-2" 
                title="Approve"
                @click="approveVendor(item.id)" 
             />
             <BaseButton 
                variant="danger" 
                size="sm" 
                icon="fa-solid fa-xmark" 
                class="mr-2" 
                title="Reject"
                @click="rejectVendor(item.id)" 
             />
          </template>
          
          <router-link :to="`/admin/vendors/${item.id}`">
            <BaseButton variant="light" size="sm" icon="fa-solid fa-eye" class="mr-2" />
          </router-link>
          <BaseButton variant="light" size="sm" icon="fa-solid fa-trash" @click="deleteVendor(item.id)" class="text-danger" />
        </div>
      </template>
    </DataTable>

    <BaseModal :show="showAddModal" title="Add New Vendor" size="xl" @close="showAddModal = false">
      <div class="p-4 text-center">
        <i class="fa-solid fa-screwdriver-wrench fa-3x mb-3 text-muted"></i>
        <p>Vendor creation form implementation in progress...</p>
      </div>
      <template #footer>
        <BaseButton variant="secondary" @click="showAddModal = false">Cancel</BaseButton>
        <BaseButton variant="primary">Save Vendor</BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { debounce } from 'lodash';
import swal from '../../../utils/swal';

const loading = ref(true);
const data = ref({ data: [], current_page: 1, total: 0 });
const showAddModal = ref(false);

const filters = reactive({
  search: '',
  vendor_type: '',
  status: ''
});

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'business', label: 'Business Profile' },
  { key: 'location', label: 'Location & Contact' },
  { key: 'is_verified', label: 'Verification Status' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const vendorTypeOptions = [
  { label: 'All Types', value: '' },
  { label: 'Shop', value: 'shop' },
  { label: 'Doctor', value: 'doctor' },
  { label: 'Barber', value: 'barber' }
];

const fetchData = async (urlOrPage = 1) => {
  loading.value = true;
  try {
    const url = typeof urlOrPage === 'string' ? urlOrPage : '/admin/vendors-data';
    const params = typeof urlOrPage === 'number' ? { ...filters, page: urlOrPage } : filters;
    
    const response = await axios.get(url, { params });
    if (response.data.success) {
      data.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching vendors:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => {
  fetchData(1);
}, 300);

const approveVendor = async (id) => {
    const result = await swal.fire({
        title: 'Approve Vendor?',
        text: "This vendor will become active and verified immediately.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        confirmButtonColor: '#1bc5bd' // Success color
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/admin/vendors-data/${id}/approve`);
            if (response.data.success) {
                swal.fire('Approved!', 'Vendor has been approved.', 'success');
                fetchData(data.value.current_page);
            }
        } catch (error) {
            swal.fire('Error', 'Failed to approve vendor.', 'error');
        }
    }
};

const rejectVendor = async (id) => {
    const result = await swal.fire({
        title: 'Reject Vendor?',
        text: "Please provide a reason for rejection:",
        input: 'text',
        inputPlaceholder: 'Reason...',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#f64e60',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to write something!'
            }
        }
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/admin/vendors-data/${id}/reject`, { reason: result.value });
            if (response.data.success) {
                swal.fire('Rejected!', 'Vendor has been rejected.', 'success');
                fetchData(data.value.current_page);
            }
        } catch (error) {
            swal.fire('Error', 'Failed to reject vendor.', 'error');
        }
    }
};

const deleteVendor = async (id) => {
  if (confirm('Are you sure you want to delete this vendor?')) {
    try {
      await axios.delete(`/admin/vendors-data/${id}`);
      fetchData(data.value.current_page);
    } catch (error) {
      alert('Failed to delete vendor');
    }
  }
};

const getVerificationBadgeType = (status) => {
  const map = {
    'pending': 'warning',
    'approved': 'success',
    'rejected': 'danger'
  };
  return map[status] || 'light';
};

onMounted(() => fetchData());
</script>

<style scoped>
.search-box { width: 300px; }
.flex-actions { display: flex; align-items: center; gap: 10px; }
.mr-1 { margin-right: 5px; }
.mr-2 { margin-right: 10px; }
.mr-3 { margin-right: 15px; }
.text-right { text-align: right; }
.text-danger { color: #f64e60; }
.text-success { color: #1bc5bd; }
.text-muted { color: #7e8299; }
.font-bold { font-weight: 700; }
.text-gray-800 { color: #3f4254; }
.text-xs { font-size: 0.75rem; }
.business-icon-mini {
  width: 35px;
  height: 35px;
  background: #f3f6f9;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.items-center { align-items: center; }
.flex-col { flex-direction: column; }
.gap-1 { gap: 4px; }
</style>
