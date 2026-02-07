<template>
  <div class="admin-community-preview-page">
    <div class="admin-header">
      <div class="breadcrumb">
        <router-link to="/admin/community-directory" class="breadcrumb-item">
          <i class="fa-solid fa-arrow-left"></i> Community Directory
        </router-link>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ contact?.name || 'Contact Preview' }}</span>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading contact details...</p>
    </div>

    <div v-else-if="contact" class="contact-detail-card">
      <div class="contact-layout">
        <!-- Left Side: Image and Contact Info -->
        <div class="contact-left">
          <div class="contact-image-section">
            <div class="contact-image">
              <img v-if="contact.image" :src="contact.image" :alt="contact.name" />
              <div v-else class="image-placeholder">
                <i class="fa-solid fa-user"></i>
              </div>
            </div>

            <div class="contact-header">
              <h1 class="contact-name">{{ contact.name }}</h1>
              <div class="contact-badges">
                <span class="type-badge" :class="contact.type">{{ contact.type }}</span>
                <span v-if="contact.is_active" class="status-badge active">
                  <i class="fa-solid fa-circle"></i> Active
                </span>
                <span v-else class="status-badge inactive">
                  <i class="fa-solid fa-circle"></i> Inactive
                </span>
              </div>
            </div>

            <div class="contact-actions">
              <a v-if="primaryNumber" :href="'tel:' + primaryNumber" class="call-button">
                <i class="fa-solid fa-phone"></i>
                Call {{ primaryNumber }}
              </a>
              <BaseButton variant="secondary" @click="$router.go(-1)">
                <i class="fa-solid fa-arrow-left"></i> Back to List
              </BaseButton>
            </div>
          </div>
        </div>

        <!-- Right Side: Details -->
        <div class="contact-right">
          <div class="contact-details">
            <!-- Contact Numbers -->
            <div v-if="contact.contact_numbers && contact.contact_numbers.length > 0" class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-phone"></i> Contact Numbers
              </h3>
              <div class="contact-numbers">
                <div v-for="number in contact.contact_numbers" :key="number.id" class="number-item">
                  <span class="number-type">{{ number.type }}</span>
                  <a :href="'tel:' + number.number" class="number">{{ number.number }}</a>
                  <i class="fa-solid fa-phone call-icon"></i>
                </div>
              </div>
            </div>

            <!-- Basic Information -->
            <div class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-user"></i> Basic Information
              </h3>
              <div class="info-grid">
                <div v-if="contact.designation" class="info-item">
                  <span class="info-label">Designation:</span>
                  <span class="info-value">{{ contact.designation }}</span>
                </div>
                <div v-if="contact.department" class="info-item">
                  <span class="info-label">Department:</span>
                  <span class="info-value">{{ contact.department }}</span>
                </div>
                <div v-if="contact.email" class="info-item">
                  <span class="info-label">Email:</span>
                  <a :href="'mailto:' + contact.email" class="info-value email">{{ contact.email }}</a>
                </div>
                <div v-if="contact.type" class="info-item">
                  <span class="info-label">Type:</span>
                  <span class="info-value type-badge" :class="contact.type">{{ contact.type }}</span>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="contact.tags && contact.tags.length > 0" class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-tags"></i> Tags
              </h3>
              <div class="tags-list">
                <span
                  v-for="tag in contact.tags"
                  :key="tag.id"
                  class="tag-badge"
                >
                  {{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Address -->
            <div v-if="contact.address" class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-map-marker-alt"></i> Address
              </h3>
              <p class="address-text">{{ contact.address }}</p>
            </div>

            <!-- Description -->
            <div v-if="contact.description" class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-info-circle"></i> About
              </h3>
              <p class="description-text">{{ contact.description }}</p>
            </div>

            <!-- Images -->
            <div v-if="contact.images && contact.images.length > 0" class="detail-section">
              <h3 class="section-title">
                <i class="fa-solid fa-images"></i> Images
              </h3>
              <div class="images-gallery">
                <div
                  v-for="image in contact.images"
                  :key="image.id"
                  class="gallery-item"
                >
                  <img :src="image.url" :alt="image.file_name" @click="openImageModal(image)" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="selectedImage" class="image-modal" @click="closeImageModal">
      <div class="modal-content">
        <img :src="selectedImage.url" :alt="selectedImage.file_name" />
        <button class="close-modal" @click="closeImageModal">
          <i class="fa-solid fa-times"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import BaseButton from '@/Components/BaseButton.vue';

const route = useRoute();
const loading = ref(true);
const contact = ref(null);
const selectedImage = ref(null);

const primaryNumber = ref(null);

const fetchContact = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/admin/contacts-directory-data/${route.params.id}`);
    if (response.data.success) {
      contact.value = response.data.data;

      // Find primary number
      if (contact.value.contact_numbers && contact.value.contact_numbers.length > 0) {
        const primary = contact.value.contact_numbers.find(n => n.type === 'primary');
        primaryNumber.value = primary ? primary.number : contact.value.contact_numbers[0].number;
      }
    }
  } catch (error) {
    console.error('Error fetching contact:', error);
  } finally {
    loading.value = false;
  }
};

const openImageModal = (image) => {
  selectedImage.value = image;
};

const closeImageModal = () => {
  selectedImage.value = null;
};

onMounted(() => {
  fetchContact();
});
</script>

<style scoped>
.admin-community-preview-page {
  padding: 20px;
}

.admin-header {
  margin-bottom: 20px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #666;
}

.breadcrumb-item {
  color: #007bff;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 5px;
}

.breadcrumb-item:hover {
  text-decoration: underline;
}

.breadcrumb-separator {
  margin: 0 8px;
  color: #ccc;
}

.breadcrumb-current {
  color: #333;
  font-weight: 500;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.contact-detail-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.contact-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  min-height: 500px;
}

.contact-left {
  background: #f8f9fa;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-right: 1px solid #e9ecef;
}

.contact-image {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 20px;
  border: 4px solid white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.contact-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  color: #adb5bd;
}

.contact-header {
  text-align: center;
  margin-bottom: 20px;
}

.contact-name {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin-bottom: 10px;
}

.contact-badges {
  display: flex;
  flex-direction: column;
  gap: 5px;
  align-items: center;
}

.type-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.type-badge.person { background: #007bff; color: white; }
.type-badge.business { background: #28a745; color: white; }
.type-badge.service { background: #fd7e14; color: white; }

.status-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 12px;
}

.status-badge.active {
  background: #d4edda;
  color: #155724;
}

.status-badge.inactive {
  background: #f8d7da;
  color: #721c24;
}

.contact-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.call-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #007bff;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: background 0.2s;
}

.call-button:hover {
  background: #0056b3;
  color: white;
  text-decoration: none;
}

.contact-right {
  padding: 30px;
}

.detail-section {
  margin-bottom: 30px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.contact-numbers {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.number-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
}

.number-type {
  font-size: 12px;
  font-weight: 500;
  color: #666;
  background: #e9ecef;
  padding: 2px 8px;
  border-radius: 12px;
  text-transform: capitalize;
}

.number {
  font-size: 16px;
  font-weight: 500;
  color: #007bff;
  text-decoration: none;
}

.number:hover {
  text-decoration: underline;
}

.call-icon {
  color: #28a745;
  cursor: pointer;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-label {
  font-size: 14px;
  font-weight: 500;
  color: #666;
}

.info-value {
  font-size: 16px;
  color: #333;
}

.info-value.email {
  color: #007bff;
  text-decoration: none;
}

.info-value.email:hover {
  text-decoration: underline;
}

.tags-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag-badge {
  background: #007bff;
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
}

.address-text, .description-text {
  font-size: 16px;
  line-height: 1.6;
  color: #555;
}

.images-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 15px;
}

.gallery-item {
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s;
}

.gallery-item:hover {
  transform: scale(1.05);
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  position: relative;
  max-width: 90%;
  max-height: 90%;
}

.modal-content img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.close-modal {
  position: absolute;
  top: -40px;
  right: 0;
  background: rgba(0,0,0,0.5);
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

@media (max-width: 768px) {
  .contact-layout {
    grid-template-columns: 1fr;
  }

  .contact-left {
    border-right: none;
    border-bottom: 1px solid #e9ecef;
    padding: 20px;
  }

  .contact-right {
    padding: 20px;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>