<template>
  <div class="preview-page">
    <div class="preview-header">
      <BaseButton variant="light" icon="fa-solid fa-arrow-left" @click="$router.back()">
        Back to Banners
      </BaseButton>
      <h1 class="page-title">Banner Preview</h1>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fa-solid fa-spinner fa-spin fa-3x text-primary"></i>
      <p>Loading banner...</p>
    </div>

    <div v-else-if="banner" class="preview-container">
      <div class="banner-card">
        <div class="banner-image-wrapper">
          <img v-if="banner.image" :src="banner.image" :alt="banner.title" class="banner-image" />
          <div v-else class="no-image">No Image</div>
        </div>
        
        <div class="banner-content">
          <h2 class="banner-title">{{ banner.title }}</h2>
          <p v-if="banner.description" class="banner-description">{{ banner.description }}</p>
          
          <div class="banner-meta">
            <div class="meta-item">
              <strong>Status:</strong>
              <Badge :type="banner.is_active ? 'success' : 'danger'">
                {{ banner.is_active ? 'Active' : 'Inactive' }}
              </Badge>
            </div>
            <div class="meta-item" v-if="banner.link">
              <strong>Link:</strong>
              <a :href="banner.link" target="_blank" class="banner-link">{{ banner.link }}</a>
            </div>
            <div class="meta-item" v-if="banner.button_text">
              <strong>Button Text:</strong>
              <span>{{ banner.button_text }}</span>
            </div>
          </div>

          <div class="preview-actions">
            <BaseButton variant="primary" icon="fa-solid fa-pen" @click="editBanner">
              Edit Banner
            </BaseButton>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="error-state">
      <i class="fa-solid fa-exclamation-triangle fa-3x text-danger"></i>
      <p>Banner not found</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import BaseButton from '@/Components/BaseButton.vue';
import Badge from '@/Components/Badge.vue';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const banner = ref(null);

const fetchBanner = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/admin/banners-data/${route.params.id}`);
    if (response.data.success) {
      banner.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching banner:', error);
  } finally {
    loading.value = false;
  }
};

const editBanner = () => {
  router.push({ name: 'admin.banners' });
};

onMounted(() => fetchBanner());
</script>

<style scoped>
.preview-page {
  padding: 20px;
}

.preview-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #181c32;
  margin: 0;
}

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 20px;
  gap: 20px;
  color: #b5b5c3;
}

.preview-container {
  max-width: 1200px;
  margin: 0 auto;
}

.banner-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.banner-image-wrapper {
  width: 100%;
  height: 400px;
  background: #f3f6f9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.banner-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  color: #b5b5c3;
  font-size: 1.2rem;
  font-weight: 600;
}

.banner-content {
  padding: 40px;
}

.banner-title {
  font-size: 2rem;
  font-weight: 800;
  color: #181c32;
  margin-bottom: 15px;
}

.banner-description {
  font-size: 1.1rem;
  color: #5e6278;
  line-height: 1.6;
  margin-bottom: 30px;
}

.banner-meta {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 30px;
  padding: 20px;
  background: #f9f9fc;
  border-radius: 8px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.meta-item strong {
  color: #3f4254;
  min-width: 120px;
}

.banner-link {
  color: #3699ff;
  text-decoration: none;
}

.banner-link:hover {
  text-decoration: underline;
}

.preview-actions {
  display: flex;
  gap: 15px;
}

.text-primary { color: #3699ff; }
.text-danger { color: #f64e60; }
</style>
