<template>
  <div class="emergency-contacts-page">
    <DataTable 
      title="Emergency Contacts"
      :columns="columns"
      :data="contacts.data"
      :pagination="contacts"
      :loading="loading"
      @paginate="fetchContacts"
    >
      <template #header>
        <div class="search-box">
          <BaseInput 
            v-model="filters.search" 
            placeholder="Search contacts..." 
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>
      
      <template #actions>
        <BaseButton variant="primary" icon="fa-solid fa-plus" @click="openModal()">
          Add Contact
        </BaseButton>
      </template>

      <template #cell(icon)="{ item }">
        <i :class="item.icon" class="fa-lg"></i>
      </template>
      
      <template #cell(badge)="{ item }">
        <Badge :type="item.color || 'primary'">{{ item.badge }}</Badge>
      </template>

      <template #cell(is_active)="{ item }">
        <Badge :type="item.is_active ? 'success' : 'danger'">
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </Badge>
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-pen-to-square" class="mr-2" @click="openModal(item)" />
          <BaseButton variant="light" size="sm" icon="fa-solid fa-trash" @click="deleteContact(item.id)" class="text-danger" />
        </div>
      </template>
    </DataTable>

    <BaseModal :show="showModal" :title="editMode ? 'Edit Emergency Contact' : 'Add Emergency Contact'" :strict="true" size="xl" @close="closeModal">
      <form @submit.prevent="saveContact">
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 mb-4">
              <label class="form-label">Name</label>
              <BaseInput v-model="form.name" placeholder="Contact Name (e.g. Ambulance)" required />
            </div>
            
            <div class="mb-4">
              <label class="form-label">Contact Number</label>
              <BaseInput v-model="form.contact_number" placeholder="Number or label" required />
            </div>

            <div class="mb-4">
              <label class="form-label">Icon (FontAwesome)</label>
              <BaseInput v-model="form.icon" placeholder="fa-solid fa-phone" />
            </div>

            <div class="mb-4">
              <label class="form-label">Badge Text</label>
              <BaseInput v-model="form.badge" placeholder="e.g. 24/7 or OFFICIAL" />
            </div>

            <div class="mb-4">
              <BaseSelect
                v-model="form.color"
                label="Color Theme"
                :options="colorOptions"
                labelField="label"
                :reduce="(o) => o.value"
                :searchable="false"
                placeholder="Select color..."
              />
            </div>

            <div class="mb-4">
              <label class="form-label">Sort Order</label>
              <BaseInput type="number" v-model="form.sort_order" />
            </div>

            <div class="mb-4 pt-8">
              <BaseCheckbox v-model="form.is_active" label="Active" />
            </div>

            <div class="col-span-2 mb-4">
              <label class="form-label">Description (Optional)</label>
              <textarea v-model="form.description" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">Cancel</BaseButton>
        <BaseButton variant="primary" :loading="saving" @click="saveContact">
          {{ editMode ? 'Update' : 'Save' }}
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseCheckbox from '@/Components/BaseCheckbox.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { debounce } from 'lodash';
import { useNotificationStore } from '@/store/notification';

const { addNotification } = useNotificationStore();

const loading = ref(true);
const saving = ref(false);
const contacts = ref({ data: [], current_page: 1, total: 0 });
const showModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);

const filters = reactive({
  search: '',
  sort_by: 'sort_order',
  sort_order: 'asc'
});

const form = reactive({
  name: '',
  contact_number: '',
  icon: 'fa-solid fa-phone',
  badge: '',
  color: 'primary',
  sort_order: 0,
  description: '',
  is_active: true
});

const columns = [
  { key: 'icon', label: 'Icon', class: 'w-50px' },
  { key: 'name', label: 'Name' },
  { key: 'contact_number', label: 'Contact' },
  { key: 'badge', label: 'Badge' },
  { key: 'sort_order', label: 'Order' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const colorOptions = [
  { label: 'Primary (Blue)', value: 'primary' },
  { label: 'Danger (Red)', value: 'danger' },
  { label: 'Success (Green)', value: 'success' },
  { label: 'Warning (Yellow)', value: 'warning' },
  { label: 'Info (Light Blue)', value: 'info' }
];

const fetchContacts = async (page = 1) => {
  loading.value = true;
  try {
    const params = { ...filters, page };
    const response = await axios.get('/admin/emergency-contacts-data', { params });
    if (response.data.success) {
      contacts.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching contacts:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => {
  fetchContacts(1);
}, 300);

const openModal = (item = null) => {
  if (item) {
    editMode.value = true;
    selectedId.value = item.id;
    Object.assign(form, {
      name: item.name,
      contact_number: item.contact_number,
      icon: item.icon,
      badge: item.badge,
      color: item.color,
      sort_order: item.sort_order,
      description: item.description,
      is_active: !!item.is_active
    });
  } else {
    editMode.value = false;
    selectedId.value = null;
    resetForm();
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  Object.assign(form, {
    name: '',
    contact_number: '',
    icon: 'fa-solid fa-phone',
    badge: '',
    color: 'primary',
    sort_order: 0,
    description: '',
    is_active: true
  });
};

const saveContact = async () => {
  saving.value = true;
  try {
    const url = editMode.value 
      ? `/admin/emergency-contacts-data/${selectedId.value}` 
      : '/admin/emergency-contacts-data';
    
    const method = editMode.value ? 'put' : 'post';
    const response = await axios[method](url, form);
    
    if (response.data.success) {
      addNotification(`Contact ${editMode.value ? 'updated' : 'created'} successfully`);
      closeModal();
      fetchContacts(contacts.value.current_page);
    }
  } catch (error) {
    const message = error.response?.data?.message || 'Failed to save contact';
    addNotification(message, 'error');
  } finally {
    saving.value = false;
  }
};

const deleteContact = async (id) => {
  if (confirm('Are you sure you want to delete this contact?')) {
    try {
      const response = await axios.delete(`/admin/emergency-contacts-data/${id}`);
      if (response.data.success) {
        addNotification('Contact deleted successfully');
        fetchContacts(contacts.value.current_page);
      }
    } catch (error) {
      addNotification('Failed to delete contact', 'error');
    }
  }
};

onMounted(() => fetchContacts());
</script>

<style scoped>
.search-box { width: 300px; }
.mr-2 { margin-right: 8px; }
.text-right { text-align: right; }
.text-danger { color: #f64e60; }
.form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #3f4254; }
.form-select, .form-control { 
  width: 100%; 
  padding: 0.75rem 1rem; 
  border: 1px solid #e1e3ea; 
  border-radius: 0.475rem; 
  font-size: 1rem; 
}
.form-checkbox { width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; }
.pt-8 { padding-top: 2rem; }
.col-span-2 { grid-column: span 2 / span 2; }
.w-50px { width: 50px; }
</style>
