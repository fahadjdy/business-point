<template>
  <div class="dashboard-page">
    <div class="dashboard-grid" v-if="!loading">
      <StatCard 
        v-for="stat in data.stats" 
        :key="stat.label"
        :label="stat.label"
        :value="stat.value"
        :icon="stat.icon"
        :color="stat.color"
      />
    </div>

    <div class="dashboard-sections" v-if="!loading">
      <DataTable 
        title="Recent Audit Logs"
        :columns="logColumns"
        :data="data.recent_logs"
        class="recent-activity"
      >
        <template #actions>
          <router-link to="/admin/audit-logs">
            <BaseButton variant="light" size="sm" icon="fa-solid fa-eye">View All</BaseButton>
          </router-link>
        </template>
        <template #cell(module)="{ item }">
          <Badge type="primary" size="sm">{{ item.module }}</Badge>
        </template>
      </DataTable>

      <section class="card quick-actions">
        <div class="card-header">
          <h2>Quick Actions</h2>
        </div>
        <div class="card-body action-list">
          <BaseButton variant="light" icon="fa-solid fa-plus" class="w-100 mb-3 text-left">
            Add New Vendor
          </BaseButton>
          <BaseButton variant="light" icon="fa-solid fa-paper-plane" class="w-100 text-left">
            Send Notification
          </BaseButton>
        </div>
      </section>
    </div>

    <div v-else class="loading-state">
      <i class="fa-solid fa-spinner fa-spin fa-3x text-primary"></i>
      <p>Loading dashboard data...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';

const loading = ref(true);
const data = ref({
  stats: [],
  recent_logs: []
});

const logColumns = [
  { key: 'time', label: 'Time' },
  { key: 'actor_name', label: 'Actor' },
  { key: 'module', label: 'Module' },
  { key: 'action', label: 'Action' },
];

const fetchData = async () => {
  try {
    const isAdmin = window.location.pathname.startsWith('/admin');
    const url = isAdmin ? '/admin/dashboard-data' : '/api/v1/dashboard';
    const response = await axios.get(url);
    if (response.data.success) {
      data.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);
</script>

<style scoped>
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 30px;
  margin-bottom: 30px;
}

.dashboard-sections {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 30px;
  align-items: start;
}

.action-list {
  display: flex;
  flex-direction: column;
}

.w-100 { width: 100%; }
.mb-3 { margin-bottom: 1rem; }
.text-left { text-align: left; }

.loading-state {
  height: 300px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 15px;
  color: #b5b5c3;
}

@media (max-width: 1200px) {
  .dashboard-sections {
    grid-template-columns: 1fr;
  }
}
</style>
