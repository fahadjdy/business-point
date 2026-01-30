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
      
      <template #cell(status)="{ item }">
        <Badge :type="item.status === 'active' ? 'success' : 'warning'">
          {{ item.status }}
        </Badge>
      </template>

      <template #cell(vendor_type)="{ item }">
        <Badge type="primary">{{ item.vendor_type }}</Badge>
      </template>

      <template #cell(is_verified)="{ item }">
        <Badge :type="item.is_verified ? 'success' : 'danger'">
          <i class="fa-solid mr-1" :class="item.is_verified ? 'fa-circle-check' : 'fa-circle-xmark'"></i>
          {{ item.is_verified ? 'Verified' : 'Unverified' }}
        </Badge>
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
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
  { key: 'status', label: 'Status' },
  { key: 'vendor_type', label: 'Type' },
  { key: 'is_verified', label: 'Verification' },
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

onMounted(() => fetchData());
</script>

<style scoped>
.search-box { width: 300px; }
.flex-actions { display: flex; align-items: center; gap: 10px; }
.mr-1 { margin-right: 5px; }
.mr-2 { margin-right: 10px; }
.text-right { text-align: right; }
.text-danger { color: #f64e60; }
</style>
