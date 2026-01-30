<template>
  <div class="community-detail-page">
    <div class="container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <router-link to="/" class="breadcrumb-item">
          <i class="fa-solid fa-home"></i> Home
        </router-link>
        <span class="breadcrumb-separator">/</span>
        <router-link to="/community-directory" class="breadcrumb-item">
          Community Directory
        </router-link>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ contact?.name || 'Contact Details' }}</span>
      </nav>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading contact details...</p>
      </div>

      <div v-else-if="contact" class="contact-detail-card">
        <div class="contact-layout">
          <!-- Left Side: Image and Call Button -->
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
                    <i class="fa-solid fa-circle"></i> Available
                  </span>
                </div>
              </div>

              <a :href="'tel:' + contact.phone" class="call-button">
                <i class="fa-solid fa-phone"></i>
                Call Now
              </a>
            </div>
          </div>

          <!-- Right Side: All Details -->
          <div class="contact-right">
            <div class="details-section">
              <h2 class="section-title">Contact Information</h2>
              
              <div class="details-grid">
                <div class="detail-item">
                  <div class="detail-label">Phone Number</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-phone detail-icon"></i>
                    {{ contact.phone }}
                  </div>
                </div>

                <div v-if="contact.email" class="detail-item">
                  <div class="detail-label">Email Address</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-envelope detail-icon"></i>
                    <a :href="'mailto:' + contact.email" class="email-link">{{ contact.email }}</a>
                  </div>
                </div>

                <div v-if="contact.designation" class="detail-item">
                  <div class="detail-label">Designation</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-briefcase detail-icon"></i>
                    {{ contact.designation }}
                  </div>
                </div>

                <div v-if="contact.department" class="detail-item">
                  <div class="detail-label">Department</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-building detail-icon"></i>
                    {{ contact.department }}
                  </div>
                </div>

                <div v-if="contact.blood_group" class="detail-item">
                  <div class="detail-label">Blood Group</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-droplet detail-icon"></i>
                    {{ contact.blood_group }}
                  </div>
                </div>

                <div v-if="contact.address" class="detail-item full-width">
                  <div class="detail-label">Address</div>
                  <div class="detail-value">
                    <i class="fa-solid fa-location-dot detail-icon"></i>
                    {{ contact.address }}
                  </div>
                </div>
              </div>

              <!-- Skills/Tags Section -->
              <div v-if="contact.tags && contact.tags.length > 0" class="skills-section">
                <h3 class="section-subtitle">Skills & Services</h3>
                <div class="skills-grid">
                  <span 
                    v-for="tag in contact.tags" 
                    :key="tag.id"
                    class="skill-tag"
                    :class="tag.category"
                  >
                    {{ tag.name }}
                  </span>
                </div>
              </div>

              <!-- Description Section -->
              <div v-if="contact.description" class="description-section">
                <h3 class="section-subtitle">About</h3>
                <div class="description-content">
                  {{ contact.description }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="error-state">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <h3>Contact Not Found</h3>
        <p>The contact you're looking for doesn't exist or has been removed.</p>
        <router-link to="/community-directory" class="back-button">
          <i class="fa-solid fa-arrow-left"></i> Back to Directory
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const contact = ref(null);
const loading = ref(true);

const fetchContactDetails = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/v1/community-directory/${route.params.id}`, {
      headers: { 'X-Api-Key': 'business-point-secret-key' }
    });
    if (response.data.success) {
      contact.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching contact details:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchContactDetails();
});
</script>

<style scoped>
.community-detail-page {
  background: #f8fafd;
  min-height: 100vh;
  padding-top: 20px;
}

.container {
  max-width: 1200px;
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

/* Contact Detail Card */
.contact-detail-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  overflow: hidden;
  margin-bottom: 40px;
}

.contact-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  min-height: 600px;
}

/* Left Side */
.contact-left {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  color: white;
}

.contact-image-section {
  width: 100%;
}

.contact-image {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto 30px;
  border: 4px solid rgba(255,255,255,0.2);
}

.contact-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: rgba(255,255,255,0.7);
}

.contact-header {
  margin-bottom: 30px;
}

.contact-name {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 15px;
  line-height: 1.2;
}

.contact-badges {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
}

.type-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.type-badge.person { background: rgba(255,168,0,0.2); color: #ffa800; }
.type-badge.business { background: rgba(54,153,255,0.2); color: #3699ff; }
.type-badge.service { background: rgba(27,197,189,0.2); color: #1bc5bd; }

.status-badge {
  font-size: 0.8rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

.status-badge.active {
  color: #1bc5bd;
}

.status-badge i {
  font-size: 0.6rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.call-button {
  background: rgba(255,255,255,0.2);
  color: white;
  padding: 15px 30px;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
}

.call-button:hover {
  background: rgba(255,255,255,0.3);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* Right Side */
.contact-right {
  padding: 40px;
}

.details-section {
  height: 100%;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e1e2d;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f1f3f8;
}

.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
  margin-bottom: 40px;
}

.detail-item {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 12px;
  border-left: 4px solid #3699ff;
}

.detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: #7e8299;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.detail-value {
  font-size: 1rem;
  font-weight: 600;
  color: #3f4254;
  display: flex;
  align-items: center;
  gap: 10px;
}

.detail-icon {
  color: #3699ff;
  width: 16px;
}

.email-link {
  color: #3699ff;
  text-decoration: none;
}

.email-link:hover {
  text-decoration: underline;
}

/* Skills Section */
.skills-section {
  margin-bottom: 40px;
}

.section-subtitle {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e1e2d;
  margin-bottom: 20px;
}

.skills-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.skill-tag {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: capitalize;
}

.skill-tag.profession { background: #fff4de; color: #ffa800; }
.skill-tag.skill { background: #e1f0ff; color: #3699ff; }
.skill-tag.business { background: #c9f7f5; color: #1bc5bd; }
.skill-tag.service { background: #fce4ec; color: #e91e63; }

/* Description Section */
.description-section {
  background: #f8f9fa;
  padding: 25px;
  border-radius: 12px;
  border-left: 4px solid #1bc5bd;
}

.description-content {
  font-size: 1rem;
  line-height: 1.6;
  color: #5e6278;
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
  .contact-layout {
    grid-template-columns: 1fr;
  }
  
  .contact-left {
    padding: 30px 20px;
  }
  
  .contact-right {
    padding: 30px 20px;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .contact-name {
    font-size: 1.5rem;
  }
}
</style>