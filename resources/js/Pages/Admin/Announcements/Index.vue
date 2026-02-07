<template>
  <div class="notifications-page">
    <DataTable
      title="Village Announcements"
      :columns="columns"
      :data="notifications.data"
      :pagination="notifications"
      :loading="loading"
      @paginate="fetchNotifications"
    >
      <template #header>
        <div class="search-box">
          <BaseInput
            v-model="filters.search"
            placeholder="Search announcements..."
            icon="fa-solid fa-magnifying-glass"
            @input="search"
          />
        </div>
      </template>

      <template #actions>
        <BaseButton variant="primary" icon="fa-solid fa-plus" @click="openModal()">
          Add Announcement
        </BaseButton>
      </template>

      <template #cell(image)="{ item }">
        <div class="announcement-img-thumb" v-if="item.media && item.media.length > 0">
          <img :src="item.media[0].thumbnail_url || item.media[0].url" alt="Announcement" />
          <div v-if="item.media.length > 1" class="image-count">+{{ item.media.length - 1 }}</div>
        </div>
        <div v-else class="text-muted italic text-xs">No image</div>
      </template>

      <template #cell(priority)="{ item }">
        <Badge :type="item.priority === 'important' ? 'danger' : 'info'">{{ item.priority }}</Badge>
      </template>

      <template #cell(is_active)="{ item }">
        <Badge :type="item.is_active ? 'success' : 'danger'">
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </Badge>
      </template>

      <template #cell(created_at)="{ item }">
        {{ formatDate(item.created_at) }}
      </template>

      <template #cell(actions)="{ item }">
        <div class="text-right">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-eye" class="mr-2" @click="previewAnnouncement(item)" />
          <div class="inline-block relative">
          <BaseButton variant="light" size="sm" icon="fa-solid fa-pen-to-square" class="mr-2" @click="openModal(item)" />
          <BaseButton variant="light" size="sm" icon="fa-solid fa-trash" @click="deleteNotification(item.id)" class="text-danger" />
          </div>
        </div>
      </template>
    </DataTable>

    <BaseModal :show="showModal" :title="editMode ? 'Edit Announcement' : 'Add Announcement'" :strict="true" size="xl" @close="closeModal">
      <form @submit.prevent="saveNotification">
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 mb-4">
              <label class="form-label">Title</label>
              <BaseInput v-model="form.title" placeholder="Announcement Title" required />
            </div>

            <div class="col-span-2 mb-4">
              <label class="form-label">Message</label>
              <textarea v-model="form.message" class="form-control" rows="4" required placeholder="Announcement content..."></textarea>
            </div>

            <div class="mb-4">
              <BaseSelect
                v-model="form.priority"
                label="Priority"
                :options="priorityOptions"
                labelField="label"
                :reduce="(o) => o.value"
                :searchable="false"
                placeholder="Select priority..."
              />
            </div>

            <div class="mb-4 pt-2">
              <BaseCheckbox v-model="form.is_active" label="Active" />
            </div>

            <div class="mb-4 pt-2">
              <BaseCheckbox v-model="form.is_scheduled" label="Schedule Post" />
            </div>

            <div v-if="form.is_scheduled" class="col-span-2 mb-4">
               <label class="form-label text-primary font-black">Publish Date & Time</label>
               <input type="datetime-local" v-model="form.scheduled_at" class="form-control" :required="form.is_scheduled" />
               <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">Announcement will be hidden until this time</p>
            </div>

            <div class="col-span-2 mb-4">
              <label class="form-label">Announcement Images (Optional)</label>
              <div class="multiple-image-upload">
                <input type="file" @change="handleImageUpload" accept="image/*" multiple class="hidden" ref="fileInput" />

                <!-- Existing Images -->
                <div v-if="existingImages.length > 0" class="existing-images mb-3">
                  <h6 class="text-sm font-medium text-gray-700 mb-2">Current Images</h6>
                  <div class="image-grid">
                    <div v-for="(image, index) in existingImages" :key="'existing-' + index" class="image-item">
                      <img :src="image.url" class="preview-img" />
                      <button type="button" @click="removeExistingImage(index)" class="remove-btn">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- New Images Preview -->
                <div v-if="newImages.length > 0" class="new-images mb-3">
                  <h6 class="text-sm font-medium text-gray-700 mb-2">New Images</h6>
                  <div class="image-grid">
                    <div v-for="(image, index) in newImages" :key="'new-' + index" class="image-item">
                      <img :src="image.preview" class="preview-img" />
                      <button type="button" @click="removeNewImage(index)" class="remove-btn">
                        <i class="fa-solid fa-xmark"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Upload Area -->
                <div class="upload-area" @click="$refs.fileInput.click()">
                  <div class="placeholder">
                    <i class="fa-solid fa-cloud-arrow-up fa-2x text-blue-500"></i>
                    <span class="font-medium">Click to upload images</span>
                    <small class="text-muted">You can select multiple images (Max 2MB each)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">Cancel</BaseButton>
        <BaseButton variant="primary" :loading="saving" @click="saveNotification">
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
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseCheckbox from '@/Components/BaseCheckbox.vue';
import BaseModal from '@/Components/BaseModal.vue';
import { debounce } from 'lodash';
import { useNotificationStore } from '@/store/notification';
import { formatDate } from '@/utils/formatters.js';

const { addNotification } = useNotificationStore();

const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const notifications = ref({ data: [], current_page: 1, total: 0 });
const showModal = ref(false);
const editMode = ref(false);
const selectedId = ref(null);
const fileInput = ref(null);
const existingImages = ref([]);
const newImages = ref([]);
const imagesToDelete = ref([]);

const filters = reactive({
  search: '',
  sort_by: 'created_at',
  sort_order: 'desc'
});

const form = reactive({
  title: '',
  message: '',
  priority: 'normal',
  is_active: true,
  is_scheduled: false,
  scheduled_at: ''
});

const columns = [
  { key: 'image', label: 'Image', class: 'w-80px' },
  { key: 'title', label: 'Title' },
  { key: 'priority', label: 'Priority' },
  { key: 'is_active', label: 'Status' },
  { key: 'created_at', label: 'Date' },
  { key: 'actions', label: 'Actions', class: 'text-right' },
];

const priorityOptions = [
  { label: 'Normal', value: 'normal' },
  { label: 'Important (Red Badge)', value: 'important' }
];

const fetchNotifications = async (page = 1) => {
  loading.value = true;
  try {
    const params = { ...filters, page, per_page: 20 };
    const response = await axios.get('/admin/notifications-data', { params });
    if (response.data.success) {
      notifications.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching notifications:', error);
  } finally {
    loading.value = false;
  }
};

const search = debounce(() => {
  fetchNotifications(1);
}, 300);

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files);

  files.forEach(file => {
    if (file && file.type.startsWith('image/')) {
      // Check file size (2MB limit)
      if (file.size > 2 * 1024 * 1024) {
        addNotification(`File ${file.name} is too large. Maximum size is 2MB.`, 'error');
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        newImages.value.push({
          file: file,
          preview: e.target.result,
          name: file.name
        });
      };
      reader.readAsDataURL(file);
    } else {
      addNotification(`File ${file.name} is not a valid image.`, 'error');
    }
  });

  // Clear the input so the same file can be selected again
  event.target.value = '';
};

const removeExistingImage = (index) => {
  const image = existingImages.value[index];
  imagesToDelete.value.push(image.id);
  existingImages.value.splice(index, 1);
};

const removeNewImage = (index) => {
  newImages.value.splice(index, 1);
};

const openModal = (item = null) => {
  if (item) {
    editMode.value = true;
    selectedId.value = item.id;
    Object.assign(form, {
      title: item.title,
      message: item.message,
      priority: item.priority || 'normal',
      is_active: !!item.is_active,
      is_scheduled: !!item.is_scheduled,
      scheduled_at: item.scheduled_at ? item.scheduled_at.substring(0, 16) : ''
    });

    // Load existing images
    existingImages.value = item.media ? item.media.map(media => ({
      id: media.id,
      url: media.url,
      name: media.file_name
    })) : [];

    newImages.value = [];
    imagesToDelete.value = [];
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
    message: '',
    priority: 'normal',
    is_active: true,
    is_scheduled: false,
    scheduled_at: ''
  });
  existingImages.value = [];
  newImages.value = [];
  imagesToDelete.value = [];
  if (fileInput.value) fileInput.value.value = '';
};

const saveNotification = async () => {
  saving.value = true;
  try {
    const formData = new FormData();

    // Handle form fields properly
    formData.append('title', form.title);
    formData.append('message', form.message);
    formData.append('priority', form.priority);
    formData.append('is_active', form.is_active ? '1' : '0');
    formData.append('is_scheduled', form.is_scheduled ? '1' : '0');

    if (form.is_scheduled && form.scheduled_at) {
      formData.append('scheduled_at', form.scheduled_at);
    }

    // Add new images
    newImages.value.forEach((image, index) => {
      formData.append(`images[${index}]`, image.file);
    });

    // Add images to delete
    if (imagesToDelete.value.length > 0) {
      imagesToDelete.value.forEach((id, index) => {
        formData.append(`delete_images[${index}]`, id);
      });
    }

    if (editMode.value) {
      formData.append('_method', 'PUT');
    }

    const url = editMode.value
      ? `/admin/notifications-data/${selectedId.value}`
      : '/admin/notifications-data';

    const response = await axios.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.success) {
      addNotification(`Announcement ${editMode.value ? 'updated' : 'created'} successfully`);
      closeModal();
      fetchNotifications(notifications.value.current_page);
    }
  } catch (error) {
    console.error('Save error:', error);
    const message = error.response?.data?.message || 'Failed to save announcement';
    addNotification(message, 'error');
  } finally {
    saving.value = false;
  }
};

const previewAnnouncement = (item) => {
  router.push(`/admin/notifications/${item.id}/preview`);
};

const shareAnnouncement = (item) => {
  const shareUrl = `${window.location.origin}/announcements/${item.id}`;
  const shareText = `Check out this announcement: ${item.title}`;

  if (navigator.share) {
    navigator.share({
      title: item.title,
      text: shareText,
      url: shareUrl,
    });
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(`${shareText} ${shareUrl}`).then(() => {
      addNotification('Announcement link copied to clipboard!');
    });
  }
};

const deleteNotification = async (id) => {
  if (confirm('Are you sure you want to delete this announcement?')) {
    try {
      const response = await axios.delete(`/admin/notifications-data/${id}`);
      if (response.data.success) {
        addNotification('Announcement deleted successfully');
        fetchNotifications(notifications.value.current_page);
      }
    } catch (error) {
      addNotification('Failed to delete announcement', 'error');
    }
  }
};

onMounted(() => fetchNotifications());
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
.col-span-2 { grid-column: span 2 / span 2; }
.announcement-img-thumb {
  width: 60px;
  height: 40px;
  border-radius: 4px;
  overflow: hidden;
  background: #f3f6f9;
  position: relative;
}
.announcement-img-thumb img { width: 100%; height: 100%; object-fit: cover; }
.image-count {
  position: absolute;
  top: 2px;
  right: 2px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  font-size: 0.7rem;
  padding: 1px 4px;
  border-radius: 2px;
  line-height: 1;
}
.w-80px { width: 80px; }

.multiple-image-upload { width: 100%; }

.existing-images, .new-images { margin-bottom: 15px; }

.image-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 10px;
  margin-bottom: 10px;
}

.image-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid #e1e3ea;
}

.image-item .preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background: rgba(246, 78, 96, 0.9);
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

.remove-btn:hover {
  background: #f64e60;
  transform: scale(1.1);
}

.upload-area {
  border: 2px dashed #e1e3ea;
  border-radius: 8px;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #f8f9fa;
}

.upload-area:hover {
  border-color: #3699ff;
  background: #f0f8ff;
}

.placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #a1a5b7;
  text-align: center;
}

.placeholder small {
  font-size: 0.75rem;
  opacity: 0.8;
}

.hidden { display: none; }
</style>