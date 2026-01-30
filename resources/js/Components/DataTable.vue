<template>
  <div class="card">
    <div v-if="$slots.header || title" class="card-header">
      <slot name="header">
        <h2>{{ title }}</h2>
      </slot>
      <div v-if="$slots.actions" class="card-actions">
        <slot name="actions" />
      </div>
    </div>
    
    <div class="card-body">
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th v-if="shouldShowSerial" class="text-center w-70px">Sr. No.</th>
              <th v-for="col in columns" :key="col.key" :class="col.class">
                {{ col.label }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in data" :key="index">
              <td v-if="shouldShowSerial" class="text-center w-70px">
                {{ serialStart + index }}
              </td>
              <td v-for="col in columns" :key="col.key" :class="col.class">
                <slot :name="`cell(${col.key})`" :item="row">
                  {{ row[col.key] }}
                </slot>
              </td>
            </tr>
            <tr v-if="data.length === 0">
              <td :colspan="columns.length + (shouldShowSerial ? 1 : 0)" class="text-center empty-state">
                <slot name="empty">No records found.</slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination && pagination.links" class="pagination-container">
        <div class="pagination-info">
          Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total }} entries
        </div>
        <div class="pagination">
          <button v-for="link in pagination.links" 
                 :key="link.label" 
                 v-html="link.label"
                 class="page-link"
                 :class="{ 'active': link.active, 'disabled': !link.url }"
                 @click="link.url && !link.active ? $emit('paginate', link.url) : null"
          ></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const props = defineProps({
  title: String,
  columns: Array,
  data: Array,
  pagination: Object,
  loading: Boolean,
  showSerial: { type: Boolean, default: undefined }
});

defineEmits(['paginate']);

const shouldShowSerial = computed(() => {
  if (props.showSerial !== undefined) return props.showSerial;
  return route.path.startsWith('/admin');
});

const serialStart = computed(() => {
  const from = props.pagination?.from;
  if (typeof from === 'number') return from;
  return 1;
});
</script>

<style scoped>
.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.card-header {
  padding: 20px 25px;
  border-bottom: 1px solid #eff2f5;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h2 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #3f4254;
}

.card-body {
  padding: 25px;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  color: #b5b5c3;
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  padding-bottom: 15px;
  white-space: nowrap;
}

.data-table td {
  padding: 15px 0;
  border-bottom: 1px solid #f3f6f9;
  color: #3f4254;
  font-size: 0.95rem;
}

.empty-state {
  padding: 40px 0 !important;
  color: #b5b5c3;
  font-style: italic;
}

.pagination-container {
  margin-top: 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pagination-info {
  color: #b5b5c3;
  font-size: 0.9rem;
}

.pagination {
  display: flex;
  gap: 5px;
}

.page-link {
  padding: 6px 12px;
  border-radius: 4px;
  background: #f3f6f9;
  color: #7e8299;
  border: none;
  cursor: pointer;
  font-size: 0.85rem;
  transition: all 0.2s;
}

.page-link.active {
  background: #3699ff;
  color: #fff;
  cursor: default;
}

.page-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.text-center { text-align: center; }
.text-right { text-align: right; }
.w-70px { width: 70px; }
</style>
