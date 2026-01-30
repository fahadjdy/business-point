<template>
  <div class="contact-book-admin-page">
    <DataTable 
      title="Community Directory"
      :columns="columns"
      :data="contacts.data"
      :pagination="contacts"
      :loading="loading"
      @paginate="fetchContacts"
    >
      <template #header>
        <div class="flex items-center gap-4">
          <div class="search-box">
            <BaseInput 
              v-model="filters.keyword" 
              placeholder="Search contacts..." 
              icon="fa-solid fa-magnifying-glass"
              @input="search"
            />
          </div>
          
          <!-- Tag Filter -->
          <div class="tag-filter-box">
            <BaseSelect
              v-model="filters.tag_id"
              :options="availableTags"
              labelField="name"
              :reduce="(o) => o.id"
              placeholder="Filter by tag..."
              :clearable="true"
              @input="search"
            />
          </div>
          
          <!-- Category Filter -->
          <div class="category-filter-box">
            <BaseSelect
              v-model="filters.type"
              :options="typeOptions"
              labelField="label"
              :reduce="(o) => o.value"
              placeholder="Filter by type..."
              :clearable="true"
              @input="search"
            />
          </div>
        </div>
      </template>
      
      <template #actions>
        <div class="flex gap-2 relative">
          <div class="relative inline-block">
            <BaseButton variant="secondary" icon="fa-solid fa-download" @click="showExportOptions = !showExportOptions">
              Export
            </BaseButton>
            
            <!-- Export Options Dropdown -->
            <div v-if="showExportOptions" class="absolute top-full right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 min-w-64 z-50" @click.stop>
              <div class="px-4 py-3 border-b border-gray-100">
                <div class="text-sm font-semibold text-gray-700">Export Community Directory</div>
                <div class="text-xs text-gray-500 mt-1">Download contacts in your preferred format</div>
              </div>
              <div class="py-2">
                <button @click="exportData('csv')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm flex items-center gap-3 transition-colors">
                  <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-csv text-green-600 text-sm"></i>
                  </div>
                  <div class="flex-1">
                    <div class="font-medium text-gray-900">Export as CSV</div>
                    <div class="text-xs text-gray-500">Comma-separated values for Excel</div>
                  </div>
                </button>
                <button @click="exportData('excel')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-sm flex items-center gap-3 transition-colors">
                  <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-excel text-green-600 text-sm"></i>
                  </div>
                  <div class="flex-1">
                    <div class="font-medium text-gray-900">Export as Excel</div>
                    <div class="text-xs text-gray-500">Excel-compatible format</div>
                  </div>
                </button>
              </div>
            </div>
          </div>
          <BaseButton variant="primary" icon="fa-solid fa-plus" @click="openModal()">
            Add Contact
          </BaseButton>
        </div>
      </template>

      <template #cell(image)="{ item }">
        <div class="avatar-sm">
           <img v-if="item.image" :src="item.image" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;" />
           <div v-else class="placeholder-sm bg-gray-100 rounded text-gray-300 flex items-center justify-center" style="width: 40px; height: 40px;">
              <i class="fa-solid fa-user"></i>
           </div>
        </div>
      </template>
      
      <template #cell(tags)="{ item }">
        <div class="flex flex-wrap gap-1">
          <span 
            v-for="tag in item.tags" 
            :key="tag.id"
            class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"
          >
            {{ tag.name }}
          </span>
          <span v-if="!item.tags || item.tags.length === 0" class="text-gray-400 text-xs">No tags</span>
        </div>
      </template>
      
      <template #cell(type)="{ item }">
        <Badge :type="getTypeColor(item.type)">{{ item.type }}</Badge>
      </template>

      <template #cell(is_active)="{ item }">
        <Badge :type="item.is_active ? 'success' : 'danger'">
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </Badge>
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-pen-to-square" class="mr-2" @click="openModal(item)" />
          <BaseButton variant="light" size="sm" icon="fa-solid fa-trash" @click="confirmDelete(item.id)" class="text-danger" />
        </div>
      </template>
    </DataTable>

    <BaseModal :show="showModal" :title="editMode ? 'Edit Contact' : 'Add Contact'" :strict="true" size="xl" @close="closeModal">
      <form @submit.prevent="saveContact">
        <div class="p-6 max-h-96 overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <!-- Image Upload -->
            <div class="col-span-full mb-4">
              <label class="form-label">Contact Photo</label>
              <div class="image-upload-section">
                 <div v-if="imagePreview || form.image_url" class="image-preview-container">
                    <img :src="imagePreview || form.image_url" class="preview-image" />
                    <button type="button" @click="removeImage" class="remove-image-btn">
                       <i class="fa-solid fa-xmark"></i>
                    </button>
                 </div>
                 <div v-else class="upload-placeholder" @click="fileInput.click()">
                    <i class="fa-solid fa-camera upload-icon"></i>
                    <span class="upload-text">Upload Photo</span>
                    <small class="upload-hint">Square image recommended, Max 2MB</small>
                 </div>
                 <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleImageChange" />
              </div>
            </div>

            <!-- Name -->
            <div class="col-span-full mb-4">
              <label class="form-label">Full Name <span class="required">*</span></label>
              <BaseInput v-model="form.name" placeholder="John Doe or Business Name" required />
            </div>
            
            <!-- Type and Phone -->
            <div class="mb-4">
              <BaseSelect
                v-model="form.type"
                label="Type *"
                :options="typeOptions"
                labelField="label"
                :reduce="(o) => o.value"
                :searchable="false"
                placeholder="Select type..."
              />
            </div>

            <div class="mb-4">
              <label class="form-label">Phone <span class="required">*</span></label>
              <BaseInput v-model="form.phone" placeholder="Contact number" required />
            </div>

            <!-- Email and Designation -->
            <div class="mb-4">
              <label class="form-label">Email</label>
              <BaseInput type="email" v-model="form.email" placeholder="email@example.com" />
            </div>

            <div class="mb-4">
              <label class="form-label">Designation</label>
              <BaseInput v-model="form.designation" placeholder="e.g. Manager, Doctor" />
            </div>

            <!-- Department and Sort Order -->
            <div class="mb-4">
              <label class="form-label">Department</label>
              <BaseInput v-model="form.department" placeholder="e.g. Sales, Medicine" />
            </div>

            <div class="mb-4">
              <label class="form-label">Sort Order</label>
              <BaseInput type="number" v-model="form.sort_order" />
            </div>

            <!-- Tags -->
            <div class="col-span-full mb-4">
              <label class="form-label">Tags</label>
              <BaseSelect
                v-model="form.tag_ids"
                :options="availableTags"
                labelField="name"
                :reduce="(o) => o.id"
                :multiple="true"
                :searchable="true"
                placeholder="Select tags (e.g., Doctor, Plumber, Groceries)..."
                :closeOnSelect="false"
              />
              <div class="form-hint">
                Select relevant tags to help categorize this contact
              </div>
            </div>

            <!-- Active Status -->
            <div class="col-span-full mb-4 flex items-center">
              <BaseCheckbox v-model="form.is_active" label="Active in directory" />
            </div>

            <!-- Address -->
            <div class="col-span-full mb-4">
              <label class="form-label">Address</label>
              <textarea v-model="form.address" class="form-control" rows="2" placeholder="Physical location..."></textarea>
            </div>

            <!-- Description -->
            <div class="col-span-full mb-4">
              <label class="form-label">Profile Description (About)</label>
              <textarea v-model="form.description" class="form-control" rows="3" placeholder="Tell more about this contact..."></textarea>
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
import { ref, reactive, onMounted, watch } from 'vue';
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
const fileInput = ref(null);
const imagePreview = ref(null);
const selectedFile = ref(null);
const availableTags = ref([]);
const showExportOptions = ref(false);

const filters = reactive({
  keyword: '',
  tag_id: null,
  type: null,
  sort_by: 'sort_order',
  sort_order: 'asc'
});

const form = reactive({
  name: '',
  phone: '',
  email: '',
  designation: '',
  department: '',
  address: '',
  description: '',
  type: 'person',
  sort_order: 0,
  is_active: true,
  image_url: null,
  tag_ids: []
});

const columns = [
  { key: 'image', label: 'Photo', class: 'w-60px' },
  { key: 'name', label: 'Name' },
  { key: 'tags', label: 'Tags' },
  { key: 'type', label: 'Type' },
  { key: 'phone', label: 'Phone' },
  { key: 'designation', label: 'Designation' },
  { key: 'sort_order', label: 'Order', class: 'text-center' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const typeOptions = [
  { label: 'Person', value: 'person' },
  { label: 'Business', value: 'business' },
  { label: 'Service', value: 'service' }
];

const fetchContacts = async (page = 1) => {
  loading.value = true;
  try {
    const params = { ...filters, page };
    const response = await axios.get('/admin/contacts-directory-data', { params });
    if (response.data.success) {
      contacts.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching contacts:', error);
  } finally {
    loading.value = false;
  }
};

const fetchTags = async () => {
  try {
    const response = await axios.get('/api/v1/tags/all', {
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    if (response.data.success) {
      availableTags.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching tags:', error);
  }
};

const search = debounce(() => {
  fetchContacts(1);
}, 300);

const getTypeColor = (type) => {
  if (type === 'person') return 'primary';
  if (type === 'business') return 'success';
  if (type === 'service') return 'danger';
  return 'info';
};

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  selectedFile.value = null;
  imagePreview.value = null;
  form.image_url = null;
  if (fileInput.value) fileInput.value.value = '';
};

const openModal = (item = null) => {
  removeImage();
  if (item) {
    editMode.value = true;
    selectedId.value = item.id;
    Object.assign(form, {
      name: item.name,
      phone: item.phone,
      email: item.email,
      designation: item.designation,
      department: item.department,
      address: item.address,
      description: item.description,
      type: item.type,
      sort_order: item.sort_order,
      is_active: !!item.is_active,
      image_url: item.image,
      tag_ids: item.tags ? item.tags.map(tag => tag.id) : []
    });
    
    // Debug: Log the tags being loaded
    console.log('Loading contact for edit:', item.name);
    console.log('Tags loaded:', form.tag_ids);
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
    phone: '',
    email: '',
    designation: '',
    department: '',
    address: '',
    description: '',
    type: 'person',
    sort_order: 0,
    is_active: true,
    image_url: null,
    tag_ids: []
  });
  selectedFile.value = null;
  imagePreview.value = null;
};

const saveContact = async () => {
  saving.value = true;
  try {
    const formData = new FormData();
    
    // Debug: Log the form data before processing
    console.log('Form data before processing:', form);
    
    Object.keys(form).forEach(key => {
        if (key !== 'image_url') {
            if (key === 'tag_ids') {
                // Handle tag_ids array properly
                if (Array.isArray(form[key]) && form[key].length > 0) {
                    console.log('Adding tag_ids:', form[key]);
                    form[key].forEach((tagId, index) => {
                        formData.append(`tag_ids[${index}]`, tagId);
                    });
                }
                // If no tags selected, don't append anything (nullable field)
            } else {
                formData.append(key, form[key] === null ? '' : (typeof form[key] === 'boolean' ? (form[key] ? 1 : 0) : form[key]));
            }
        }
    });

    if (selectedFile.value) {
      formData.append('image', selectedFile.value);
    }
    
    // For PUT/PATCH with FormData, Laravel needs _method
    if (editMode.value) {
      formData.append('_method', 'PUT');
    }

    // Debug: Log FormData contents
    console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
      console.log(key, value);
    }

    const url = editMode.value 
      ? `/admin/contacts-directory-data/${selectedId.value}` 
      : '/admin/contacts-directory-data';
    
    const response = await axios.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    if (response.data.success) {
      addNotification(`Contact ${editMode.value ? 'updated' : 'created'} successfully`);
      closeModal();
      fetchContacts(contacts.value.current_page);
    }
  } catch (error) {
    console.error('Save contact error:', error.response?.data);
    const message = error.response?.data?.message || 'Failed to save contact';
    addNotification(message, 'error');
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (id) => {
  if (confirm('Are you sure you want to delete this contact?')) {
    try {
      const response = await axios.delete(`/admin/contacts-directory-data/${id}`);
      if (response.data.success) {
        addNotification('Contact deleted successfully');
        fetchContacts(contacts.value.current_page);
      }
    } catch (error) {
      addNotification('Failed to delete contact', 'error');
    }
  }
};

const exportData = async (format) => {
  showExportOptions.value = false;
  try {
    // Prepare export parameters with current filters
    const exportParams = {
      format: format,
      keyword: filters.keyword || '',
      tag_id: filters.tag_id || '',
      type: filters.type || ''
    };

    // Remove empty parameters
    Object.keys(exportParams).forEach(key => {
      if (exportParams[key] === '' || exportParams[key] === null) {
        delete exportParams[key];
      }
    });

    // For CSV and Excel formats, download as file
    const response = await axios.get('/api/v1/contacts/export', { 
      params: exportParams,
      responseType: 'blob',
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    
    // Set filename based on format
    const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    let extension = format;
    if (format === 'excel') extension = 'xlsx.csv';
    
    link.setAttribute('download', `community-directory-${timestamp}.${extension}`);
    
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
    
    addNotification(`Community directory exported as ${format.toUpperCase()}`);
  } catch (error) {
    console.error('Export error:', error);
    addNotification('Export failed. Please try again.', 'error');
  }
};

onMounted(() => {
  fetchContacts();
  fetchTags();
  
  // Close export dropdown when clicking outside
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.relative')) {
      showExportOptions.value = false;
    }
  });
});

// Debug: Watch for changes to tag_ids
watch(() => form.tag_ids, (newValue, oldValue) => {
  console.log('tag_ids changed:', { from: oldValue, to: newValue, isArray: Array.isArray(newValue) });
}, { deep: true });
</script>

<style scoped>
.search-box { width: 300px; }
.tag-filter-box { width: 200px; }
.category-filter-box { width: 150px; }
.mr-2 { margin-right: 8px; }
.text-right { text-align: right; }
.text-danger { color: #f64e60; }
.form-label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: #3f4254; font-size: 0.9rem; }
.required { color: #f64e60; }
.form-hint { font-size: 0.8rem; color: #7e8299; margin-top: 4px; font-style: italic; }

.image-upload-section {
  display: flex;
  align-items: center;
  gap: 20px;
}

.image-preview-container {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e1e3ea;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #f64e60;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.remove-image-btn:hover {
  background: #e63946;
  transform: scale(1.1);
}

.upload-placeholder {
  width: 100px;
  height: 100px;
  border: 2px dashed #e1e3ea;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #f8f9fa;
}

.upload-placeholder:hover {
  border-color: #3699ff;
  background: #f0f8ff;
}

.upload-icon {
  font-size: 1.5rem;
  color: #b5b5c3;
  margin-bottom: 4px;
}

.upload-text {
  font-size: 0.8rem;
  font-weight: 600;
  color: #5e6278;
}

.upload-hint {
  font-size: 0.7rem;
  color: #a1a5b7;
  text-align: center;
  margin-top: 2px;
}
.form-select, .form-control { 
  width: 100%; 
  padding: 0.75rem 1rem; 
  border: 1px solid #e1e3ea; 
  border-radius: 0.475rem; 
  font-size: 1rem; 
  background: #fdfdfd;
}
.form-control:focus, .form-select:focus { border-color: #3699ff; outline: none; }
.form-checkbox { width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; border: 1px solid #e1e3ea; }
.pt-8 { padding-top: 2rem; }
.col-span-2 { grid-column: span 2 / span 2; }
.col-span-full { grid-column: 1 / -1; }
.w-60px { width: 60px; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
.mb-3 { margin-bottom: 0.75rem; }
.max-h-96 { max-height: 24rem; }
.overflow-y-auto { overflow-y: auto; }
.min-w-64 { min-width: 16rem; }
.flex-1 { flex: 1; }
.relative { position: relative; }
.absolute { position: absolute; }
.top-full { top: 100%; }
.right-0 { right: 0; }
.mt-2 { margin-top: 0.5rem; }
.z-50 { z-index: 50; }
.inline-block { display: inline-block; }
.flex { display: flex; }
.items-center { align-items: center; }
.flex-wrap { flex-wrap: wrap; }
.gap-1 { gap: 0.25rem; }
.inline-block { display: inline-block; }
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.text-xs { font-size: 0.75rem; }
.rounded-full { border-radius: 9999px; }
.bg-blue-100 { background-color: #dbeafe; }
.text-blue-800 { color: #1e40af; }
.text-gray-400 { color: #9ca3af; }
</style>
