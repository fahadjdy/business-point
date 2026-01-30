<template>
  <div class="contact-logs-page">
    <DataTable 
      title="Contact Audit Logs"
      :columns="columns"
      :data="logs.data"
      :pagination="logs"
      :loading="loading"
      @paginate="fetchLogs"
    >
      <template #header>
        <div class="search-box">
          <BaseInput 
            v-model="filters.keyword" 
            placeholder="Search logs..." 
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>

      <template #cell(user)="{ item }">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2 text-gray-500 font-bold text-xs">
                {{ item.user ? item.user.name.charAt(0) : 'G' }}
            </div>
            <span>{{ item.user ? item.user.name : 'Guest' }}</span>
        </div>
      </template>

      <template #cell(action)="{ item }">
        <Badge :type="getActionColor(item.action)">{{ item.action }}</Badge>
      </template>

      <template #cell(details)="{ item }">
        <div v-if="item.contact_book" class="font-bold text-gray-700">
            <i class="fa-solid fa-address-card mr-1 text-gray-400"></i> {{ item.contact_book.name }}
        </div>
        <div v-else-if="item.search_keyword" class="text-gray-600">
            <i class="fa-solid fa-magnifying-glass mr-1 text-gray-400"></i> "{{ item.search_keyword }}"
        </div>
        <div v-else class="text-gray-400 italic">No details</div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import BaseInput from '@/Components/BaseInput.vue';
import { debounce } from 'lodash';

const loading = ref(true);
const logs = ref({ data: [], current_page: 1, total: 0 });

const filters = reactive({
  keyword: '',
  sort_by: 'created_at',
  sort_order: 'desc'
});

const columns = [
  { key: 'user', label: 'User', class: 'w-200px' },
  { key: 'action', label: 'Action' },
  { key: 'details', label: 'Details' },
  { key: 'ip_address', label: 'IP Address' },
  { key: 'created_at', label: 'Date' },
];

const fetchLogs = async (page = 1) => {
  loading.value = true;
  try {
    const params = { ...filters, page };
    const response = await axios.get('/admin/contact-logs-data', { params });
    if (response.data.success) {
      logs.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching logs:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => {
  fetchLogs(1);
}, 300);

const getActionColor = (action) => {
  if (action === 'create') return 'success';
  if (action === 'update') return 'warning';
  if (action === 'delete') return 'danger';
  if (action === 'view') return 'info';
  if (action === 'search') return 'primary';
  return 'secondary';
};

onMounted(() => fetchLogs());
</script>

<style scoped>
.search-box { width: 300px; }
.w-200px { width: 200px; }
</style>
