<template>
  <div class="announcement-preview-page">
    <div class="container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <router-link to="/" class="breadcrumb-item">
          <i class="fa-solid fa-home"></i> Home
        </router-link>
        <span class="breadcrumb-separator">/</span>
        <router-link to="/announcements" class="breadcrumb-item">
          Announcements
        </router-link>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ announcement?.title || 'Preview' }}</span>
      </nav>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading announcement...</p>
      </div>

      <div v-else-if="announcement" class="announcement-preview-card">
        <div class="preview-header">
          <h1 class="announcement-title">{{ announcement.title }}</h1>
          <div class="announcement-meta">
            <div class="meta-item">
              <i class="fa-solid fa-calendar"></i>
              <span>{{ formatDate(announcement.created_at) }}</span>
            </div>
            <div v-if="announcement.is_urgent" class="meta-item urgent">
              <i class="fa-solid fa-exclamation-triangle"></i>
              <span>Urgent</span>
            </div>
            <div v-if="announcement.scheduled_at" class="meta-item scheduled">
              <i class="fa-solid fa-clock"></i>
              <span>Scheduled: {{ formatDate(announcement.scheduled_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Image Gallery -->
        <div v-if="announcement.media && announcement.media.length > 0" class="image-gallery">
          <div class="gallery-container" id="lightgallery">
            <div 
              v-for="(image, index) in announcement.media" 
              :key="image.id"
              class="gallery-item"
              :data-src="image.url"
              :data-sub-html="announcement.title"
            >
              <img :src="image.thumbnail_url || image.url" :alt="`Image ${index + 1}`" />
              <div class="gallery-overlay">
                <i class="fa-solid fa-search-plus"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="announcement-content">
          <div class="content-body" v-html="announcement.content || announcement.message"></div>
        </div>

        <!-- Actions -->
        <div class="preview-actions">
          <BaseButton variant="secondary" @click="$router.go(-1)">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back
          </BaseButton>
          <BaseButton variant="primary" @click="shareAnnouncement">
            <i class="fa-solid fa-share mr-2"></i>
            Share
          </BaseButton>
        </div>
      </div>

      <div v-else class="error-state">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <h3>Announcement Not Found</h3>
        <p>The announcement you're looking for doesn't exist or has been removed.</p>
        <router-link to="/announcements" class="back-button">
          <i class="fa-solid fa-arrow-left"></i> Back to Announcements
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import BaseButton from '@/Components/BaseButton.vue';

const route = useRoute();
const announcement = ref(null);
const loading = ref(true);
let lightGallery = null;

const fetchAnnouncement = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/v1/notifications/${route.params.id}`, {
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    if (response.data.success) {
      announcement.value = response.data.data;
      
      // Initialize lightGallery after data is loaded
      setTimeout(() => {
        initLightGallery();
      }, 100);
    }
  } catch (error) {
    console.error('Error fetching announcement:', error);
  } finally {
    loading.value = false;
  }
};

const initLightGallery = () => {
  if (announcement.value?.media?.length > 0) {
    // Simple lightbox implementation without external library
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach((item, index) => {
      item.addEventListener('click', () => {
        openLightbox(index);
      });
    });
  }
};

const openLightbox = (startIndex) => {
  const images = announcement.value.media;
  let currentIndex = startIndex;
  
  // Create lightbox overlay
  const overlay = document.createElement('div');
  overlay.className = 'lightbox-overlay';
  overlay.innerHTML = `
    <div class="lightbox-container">
      <button class="lightbox-close">&times;</button>
      <button class="lightbox-prev" ${images.length <= 1 ? 'style="display:none"' : ''}>&lt;</button>
      <button class="lightbox-next" ${images.length <= 1 ? 'style="display:none"' : ''}>&gt;</button>
      <img class="lightbox-image" src="${images[currentIndex].url}" alt="Image ${currentIndex + 1}">
      <div class="lightbox-counter">${currentIndex + 1} / ${images.length}</div>
    </div>
  `;
  
  document.body.appendChild(overlay);
  document.body.style.overflow = 'hidden';
  
  // Event listeners
  const closeBtn = overlay.querySelector('.lightbox-close');
  const prevBtn = overlay.querySelector('.lightbox-prev');
  const nextBtn = overlay.querySelector('.lightbox-next');
  const image = overlay.querySelector('.lightbox-image');
  const counter = overlay.querySelector('.lightbox-counter');
  
  let closeLightbox = () => {
    document.body.removeChild(overlay);
    document.body.style.overflow = '';
  };
  
  const updateImage = () => {
    image.src = images[currentIndex].url;
    counter.textContent = `${currentIndex + 1} / ${images.length}`;
  };
  
  const showPrev = () => {
    currentIndex = currentIndex > 0 ? currentIndex - 1 : images.length - 1;
    updateImage();
  };
  
  const showNext = () => {
    currentIndex = currentIndex < images.length - 1 ? currentIndex + 1 : 0;
    updateImage();
  };
  
  closeBtn.addEventListener('click', closeLightbox);
  prevBtn.addEventListener('click', showPrev);
  nextBtn.addEventListener('click', showNext);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeLightbox();
  });
  
  // Keyboard navigation
  const handleKeydown = (e) => {
    switch(e.key) {
      case 'Escape':
        closeLightbox();
        break;
      case 'ArrowLeft':
        showPrev();
        break;
      case 'ArrowRight':
        showNext();
        break;
    }
  };
  
  document.addEventListener('keydown', handleKeydown);
  
  // Cleanup function
  const originalClose = closeLightbox;
  closeLightbox = () => {
    document.removeEventListener('keydown', handleKeydown);
    originalClose();
  };
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const shareAnnouncement = () => {
  if (navigator.share) {
    navigator.share({
      title: announcement.value.title,
      text: announcement.value.content || announcement.value.message,
      url: window.location.href
    });
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href).then(() => {
      alert('Link copied to clipboard!');
    });
  }
};

onMounted(() => {
  fetchAnnouncement();
});

onUnmounted(() => {
  if (lightGallery) {
    lightGallery.destroy();
  }
});
</script>

<style scoped>
.announcement-preview-page {
  background: #f8fafd;
  min-height: 100vh;
  padding-top: 20px;
}

.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 30px;
  font-size: 0.9rem;
}

.breadcrumb-item {
  color: #7e8299;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.breadcrumb-item:hover {
  color: #3699ff;
}

.breadcrumb-separator {
  color: #b5b5c3;
}

.breadcrumb-current {
  color: #3f4254;
  font-weight: 600;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #b5b5c3;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e1f0ff;
  border-top-color: #3699ff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* Preview Card */
.announcement-preview-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  overflow: hidden;
  margin-bottom: 40px;
}

.preview-header {
  padding: 40px;
  border-bottom: 1px solid #f1f3f8;
}

.announcement-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1e1e2d;
  margin-bottom: 20px;
  line-height: 1.3;
}

.announcement-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #7e8299;
  font-weight: 500;
}

.meta-item.urgent {
  color: #f64e60;
  font-weight: 600;
}

.meta-item.scheduled {
  color: #3699ff;
}

/* Image Gallery */
.image-gallery {
  padding: 0;
}

.gallery-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0;
}

.gallery-item {
  position: relative;
  aspect-ratio: 16/9;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s;
}

.gallery-item:hover {
  transform: scale(1.02);
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.gallery-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
  color: white;
  font-size: 1.5rem;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

/* Content */
.announcement-content {
  padding: 40px;
}

.content-body {
  font-size: 1.1rem;
  line-height: 1.7;
  color: #5e6278;
}

.content-body h1,
.content-body h2,
.content-body h3 {
  color: #1e1e2d;
  margin-top: 30px;
  margin-bottom: 15px;
}

.content-body p {
  margin-bottom: 20px;
}

.content-body ul,
.content-body ol {
  margin-bottom: 20px;
  padding-left: 30px;
}

/* Actions */
.preview-actions {
  padding: 30px 40px;
  border-top: 1px solid #f1f3f8;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Error State */
.error-state {
  text-align: center;
  padding: 80px 20px;
  color: #b5b5c3;
}

.error-state i {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #f64e60;
}

.error-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #3f4254;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #3699ff;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  margin-top: 20px;
  transition: all 0.2s;
}

.back-button:hover {
  background: #187de4;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .announcement-title {
    font-size: 1.5rem;
  }
  
  .preview-header,
  .announcement-content,
  .preview-actions {
    padding: 20px;
  }
  
  .announcement-meta {
    flex-direction: column;
    gap: 10px;
  }
  
  .preview-actions {
    flex-direction: column;
    gap: 15px;
  }
  
  .gallery-container {
    grid-template-columns: 1fr;
  }
}

.mr-2 { margin-right: 8px; }
</style>

<style>
/* Lightbox Styles */
.lightbox-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.lightbox-container {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
}

.lightbox-image {
  max-width: 100%;
  max-height: 90vh;
  object-fit: contain;
}

.lightbox-close {
  position: absolute;
  top: -50px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  padding: 10px;
  z-index: 10001;
}

.lightbox-prev,
.lightbox-next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 1.5rem;
  padding: 15px 20px;
  cursor: pointer;
  border-radius: 5px;
  transition: background 0.3s;
}

.lightbox-prev {
  left: -60px;
}

.lightbox-next {
  right: -60px;
}

.lightbox-prev:hover,
.lightbox-next:hover {
  background: rgba(255, 255, 255, 0.3);
}

.lightbox-counter {
  position: absolute;
  bottom: -40px;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  font-size: 0.9rem;
  background: rgba(0, 0, 0, 0.5);
  padding: 5px 15px;
  border-radius: 15px;
}

@media (max-width: 768px) {
  .lightbox-prev {
    left: 10px;
  }
  
  .lightbox-next {
    right: 10px;
  }
  
  .lightbox-close {
    top: 10px;
    right: 10px;
  }
}
</style>