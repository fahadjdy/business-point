<template>
  <div class="audit-logs-page">
    <DataTable 
      title="System Audit Logs"
      :columns="columns"
      :data="logs.data"
      :pagination="logs"
      :loading="loading"
      @paginate="fetchData"
    >
      <template #header>
        <div class="search-box">
          <BaseInput 
            v-model="filters.search" 
            placeholder="Search logs..." 
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>
      
      <template #actions>
        <div class="flex-actions">
          <BaseSelect
            v-model="filters.module"
            :options="moduleOptions"
            labelField="label"
            :reduce="(o) => o.value"
            :searchable="true"
            placeholder="All Modules"
            class="mr-2 audit-filter"
            @update:modelValue="search"
          />
          <BaseSelect
            v-model="filters.action"
            :options="actionOptions"
            labelField="label"
            :reduce="(o) => o.value"
            :searchable="false"
            placeholder="All Actions"
            class="audit-filter"
            @update:modelValue="search"
          />
        </div>
      </template>

      <template #cell(actor_name)="{ item }">
        <div class="actor-info">
          <span class="actor-name">{{ item.actor_name }}</span>
          <span class="actor-ip">{{ item.actor_ip }}</span>
        </div>
      </template>

      <template #cell(module)="{ item }">
        <Badge type="primary">{{ item.module }}</Badge>
      </template>

      <template #cell(action)="{ item }">
        <Badge :type="getActionType(item.action)">{{ item.action }}</Badge>
      </template>

      <template #cell(created_at)="{ item }">
        {{ formatDateTime(item.created_at) }}
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-eye" @click="viewDetails(item)" />
        </div>
      </template>
    </DataTable>

    <!-- Details Modal -->
    <BaseModal :show="!!selectedLog" :title="`Log Details - #${selectedLog?.id}`" size="xl" @close="selectedLog = null">
      <div v-if="selectedLog" class="log-details">
        <div class="details-grid">
          <div class="info-group"><label>Module</label><p>{{ selectedLog.module }}</p></div>
          <div class="info-group"><label>Action</label><p>{{ selectedLog.action }}</p></div>
          <div class="info-group"><label>Actor</label><p>{{ selectedLog.actor_name }} ({{ selectedLog.actor_type }} #{{ selectedLog.actor_id }})</p></div>
          <div class="info-group"><label>IP Address</label><p>{{ selectedLog.actor_ip }}</p></div>
        </div>
        <div class="changes-section" v-if="hasChanges">
          <h4>Changes</h4>
          <div class="changes-table">
            <div class="change-row header"><span>Field</span><span>Old Value</span><span>New Value</span></div>
            <div v-for="(newVal, field) in selectedLog.new_values" :key="field" class="change-row">
              <span class="field-name">{{ field }}</span>
              <span class="old-val">{{ selectedLog.old_values?.[field] || '-' }}</span>
              <span class="new-val">{{ newVal }}</span>
            </div>
          </div>
        </div>
        <div class="meta-section" v-if="selectedLog.metadata">
          <h4>Metadata</h4>
          <pre>{{ selectedLog.metadata }}</pre>
        </div>
      </div>
      <template #footer>
        <BaseButton variant="secondary" @click="selectedLog = null">Close</BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
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
const logs = ref({ data: [], current_page: 1, total: 0 });
const selectedLog = ref(null);
const modules = ['User', 'Vendor', 'Shop', 'Doctor', 'Barber', 'ShopProduct', 'Banner', 'Notification'];

const filters = reactive({
  search: '',
  module: '',
  action: ''
});

const columns = [
  { key: 'created_at', label: 'Time' },
  { key: 'actor_name', label: 'Actor' },
  { key: 'module', label: 'Module' },
  { key: 'action', label: 'Action' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const moduleOptions = [
  { label: 'All Modules', value: '' },
  ...modules.map((m) => ({ label: m, value: m }))
];

const actionOptions = [
  { label: 'All Actions', value: '' },
  { label: 'Created', value: 'created' },
  { label: 'Updated', value: 'updated' },
  { label: 'Deleted', value: 'deleted' }
];

const fetchData = async (urlOrPage = 1) => {
  loading.value = true;
  try {
    const url = typeof urlOrPage === 'string' ? urlOrPage : '/admin/audit-logs-data';
    const params = typeof urlOrPage === 'number' ? { ...filters, page: urlOrPage } : filters;

    const response = await axios.get(url, { params });
    if (response.data.success) {
      logs.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching logs:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => fetchData(1), 300);

const getActionType = (action) => {
  switch (action) {
    case 'created': return 'success';
    case 'updated': return 'primary';
    case 'deleted': return 'danger';
    default: return 'default';
  }
};

const formatDateTime = (date) => new Date(date).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
const viewDetails = (log) => selectedLog.value = log;
const hasChanges = computed(() => selectedLog.value?.new_values && Object.keys(selectedLog.value.new_values).length > 0);

onMounted(() => fetchData());
</script>

<style scoped>
.search-box { width: 300px; }
.flex-actions { display: flex; align-items: center; gap: 10px; }
.mr-2 { margin-right: 8px; }
.text-right { text-align: right; }
.actor-info { display: flex; flex-direction: column; }
.actor-name { font-weight: 600; color: #3f4254; }
.actor-ip { font-size: 0.75rem; color: #b5b5c3; }
.details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
.info-group label { display: block; font-size: 0.8rem; color: #b5b5c3; margin-bottom: 4px; }
.info-group p { font-weight: 600; color: #3f4254; margin: 0; }
.changes-section h4, .meta-section h4 { font-size: 1rem; font-weight: 600; color: #3f4254; margin-bottom: 15px; }
.changes-table { border: 1px solid #eff2f5; border-radius: 8px; }
.change-row { display: grid; grid-template-columns: 1fr 1.5fr 1.5fr; padding: 12px 15px; border-bottom: 1px solid #eff2f5; font-size: 0.9rem; }
.change-row:last-child { border-bottom: none; }
.change-row.header { background: #fcfcfd; font-weight: 600; color: #7e8299; }
.field-name { font-weight: 600; color: #3f4254; }
.old-val { color: #f64e60; text-decoration: line-through; }
.new-val { color: #1bc5bd; }
pre { background: #f3f6f9; padding: 15px; border-radius: 8px; font-size: 0.85rem; color: #3f4254; overflow-x: auto; }
</style>
