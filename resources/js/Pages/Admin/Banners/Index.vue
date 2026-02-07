<template>
  <div class="banners-page">
    <DataTable
      title="Front Page Banners"
      :columns="columns"
      :data="banners.data"
      :pagination="banners"
      :loading="loading"
      @paginate="fetchBanners"
    >
      <template #header>
        <div class="search-box">
          <BaseInput
            v-model="filters.search"
            placeholder="Search banners..."
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>

      <template #actions>
        <BaseButton variant="primary" icon="fa-solid fa-plus" @click="openModal()">
          Add Banner
        </BaseButton>
      </template>

      <template #cell(image)="{ item }">
        <div class="banner-img-thumb" v-if="item.image">
          <img :src="item.image" alt="Banner" />
        </div>
        <div v-else class="text-muted italic text-xs">No image</div>
      </template>

      <template #cell(is_active)="{ item }">
        <Badge :type="item.is_active ? 'success' : 'danger'">
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </Badge>
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-eye" class="mr-2" @click="previewBanner(item)" title="Preview" />
          <BaseButton variant="light" size="sm" icon="fa-solid fa-pen-to-square" class="mr-2" @click="openModal(item)" title="Edit" />
          <BaseButton variant="light" size="sm" icon="fa-solid fa-trash" @click="deleteBanner(item.id)" class="text-danger" title="Delete" />
        </div>
      </template>
    </DataTable>

    <BaseModal :show="showModal" :title="editMode ? 'Edit Banner' : 'Add Banner'" :strict="true" size="xl" @close="closeModal">
      <form @submit.prevent="saveBanner">
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 mb-4">
              <label class="form-label">Title</label>
              <BaseInput v-model="form.title" placeholder="Banner Title" required maxlength="150" />
              <small class="text-gray-400">{{ form.title.length }}/150 characters</small>
            </div>

            <div class="col-span-2 mb-4">
              <label class="form-label">Description</label>
              <textarea v-model="form.description" class="form-control" rows="3" placeholder="Banner description..." maxlength="255"></textarea>
              <small class="text-gray-400">{{ form.description.length }}/255 characters</small>
            </div>

            <div class="mb-4">
              <label class="form-label">Button Link (URL)</label>
              <BaseInput v-model="form.link" placeholder="https://..." />
            </div>

            <div class="mb-4">
              <label class="form-label">Button Text</label>
              <BaseInput v-model="form.button_text" placeholder="e.g. Learn More" maxlength="50" />
            </div>

            <div class="mb-4 pt-2">
              <BaseCheckbox v-model="form.is_active" label="Is Active" />
            </div>

            <div class="col-span-2 mb-4">
              <label class="form-label">Banner Image (Required for new)</label>
              <div class="image-upload-wrapper">
                <input type="file" @change="handleImageUpload" accept="image/*" class="hidden" ref="fileInput" />
                
                <div v-if="imagePreview || existingImage" class="preview-container mb-3" @click="$refs.fileInput.click()">
                  <img :src="imagePreview || existingImage" class="banner-preview-img" />
                  <div class="overlay">
                    <i class="fa-solid fa-camera"></i>
                    <span>Change Image</span>
                  </div>
                </div>
                
                <div v-else class="upload-area" @click="$refs.fileInput.click()">
                  <div class="placeholder">
                    <i class="fa-solid fa-image fa-2x text-blue-500"></i>
                    <span class="font-medium">Click to upload banner image</span>
                    <small class="text-muted">Recommended: 1200x400px (Max 2MB)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">Cancel</BaseButton>
        <BaseButton variant="primary" :loading="saving" @click="saveBanner">
          {{ editMode ? 'Update' : 'Save' }}
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseCheckbox from '@/Components/BaseCheckbox.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { debounce } from 'lodash';
import { useNotificationStore } from '@/store/notification';

const { addNotification } = useNotificationStore();
const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const banners = ref({ data: [], current_page: 1, total: 0 });
const showModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const fileInput = ref(null);
const imagePreview = ref(null);
const existingImage = ref(null);
const selectedFile = ref(null);

const filters = reactive({
  search: '',
  sort_by: 'id',
  sort_order: 'desc'
});

const form = reactive({
  title: '',
  description: '',
  link: '',
  button_text: 'Learn More',
  is_active: true
});

const columns = [
  { key: 'image', label: 'Image', class: 'w-100px' },
  { key: 'title', label: 'Title' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const fetchBanners = async (page = 1) => {
  loading.value = true;
  try {
    const params = { ...filters, page, per_page: 15 };
    const response = await axios.get('/admin/banners-data', { params });
    if (response.data.success) {
      banners.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching banners:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => {
  fetchBanners(1);
}, 300);

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      addNotification('Image is too large. Max 2MB allowed.', 'error');
      return;
    }
    selectedFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const openModal = (item = null) => {
  if (item) {
    editMode.value = true;
    selectedId.value = item.id;
    Object.assign(form, {
      title: item.title,
      description: item.description || '',
      link: item.link || '',
      button_text: item.button_text || 'Learn More',
      is_active: !!item.is_active
    });
    existingImage.value = item.image;
    imagePreview.value = null;
    selectedFile.value = null;
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
    title: '',
    description: '',
    link: '',
    button_text: 'Learn More',
    is_active: true
  });
  imagePreview.value = null;
  existingImage.value = null;
  selectedFile.value = null;
  if (fileInput.value) fileInput.value.value = '';
};

const saveBanner = async () => {
  saving.value = true;
  try {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('link', form.link);
    formData.append('button_text', form.button_text);
    formData.append('is_active', form.is_active ? '1' : '0');

    if (selectedFile.value) {
      formData.append('image', selectedFile.value);
    }

    if (editMode.value) {
      formData.append('_method', 'PUT');
    }

    const url = editMode.value
      ? `/admin/banners-data/${selectedId.value}`
      : '/admin/banners-data';

    const response = await axios.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.success) {
      addNotification(`Banner ${editMode.value ? 'updated' : 'created'} successfully`);
      closeModal();
      fetchBanners(banners.value.current_page);
    }
  } catch (error) {
    const message = error.response?.data?.message || 'Failed to save banner';
    addNotification(message, 'error');
  } finally {
    saving.value = false;
  }
};

const previewBanner = (item) => {
  router.push(`/admin/banners/${item.id}/preview`);
};

const deleteBanner = async (id) => {
  if (confirm('Are you sure you want to delete this banner?')) {
    try {
      const response = await axios.delete(`/admin/banners-data/${id}`);
      if (response.data.success) {
        addNotification('Banner deleted successfully');
        fetchBanners(banners.value.current_page);
      }
    } catch (error) {
      addNotification('Failed to delete banner', 'error');
    }
  }
};

onMounted(() => fetchBanners());
</script>

<style scoped>
.search-box { width: 300px; }
.mr-2 { margin-right: 8px; }
.text-right { text-align: right; }
.text-danger { color: #f64e60; }
.form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #3f4254; }
.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e1e3ea;
  border-radius: 0.475rem;
  font-size: 1rem;
}
.banner-img-thumb {
  width: 80px;
  height: 40px;
  border-radius: 4px;
  overflow: hidden;
  background: #f3f6f9;
}
.banner-img-thumb img { width: 100%; height: 100%; object-fit: cover; }
.w-100px { width: 100px; }
.p-6 { padding: 1.5rem; }
.hidden { display: none; }

.upload-area {
  border: 2px dashed #e1e3ea;
  border-radius: 8px;
  height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background: #f8f9fa;
}
.preview-container {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid #e1e3ea;
}
.banner-preview-img {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
}
.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.2s;
}
.preview-container:hover .overlay { opacity: 1; }
.placeholder { text-align: center; color: #a1a5b7; }
</style>
