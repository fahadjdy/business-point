<template>
  <div class="register-business-wrapper">
    <div class="register-business-card">
      <div class="register-header">
        <div class="header-icon">
          <i class="fa-solid fa-store"></i>
        </div>
        <h1 class="brand">Register Your Business</h1>
        <p class="subtitle">Join our community directory and connect with local customers.</p>
      </div>

      <!-- Status Notice -->
      <div v-if="applicationStatus" class="status-notice-wrapper">
        <div :class="['status-card', applicationStatus.status]">
          <div class="status-icon">
            <i v-if="applicationStatus.status === 'pending'" class="fa-solid fa-clock-rotate-left"></i>
            <i v-else-if="applicationStatus.status === 'rejected'" class="fa-solid fa-circle-exclamation"></i>
          </div>
          <div class="status-content">
            <h4>Application status for "{{ applicationStatus.business_name }}"</h4>
            <div v-if="applicationStatus.status === 'pending'">
              <p>Your application is currently being reviewed by our administrators. We'll notify you once it's approved.</p>
            </div>
            <div v-else-if="applicationStatus.status === 'rejected'">
              <p>Your application was unfortunately rejected.</p>
              <div v-if="applicationStatus.reason" class="rejection-reason">
                <strong>Reason:</strong> {{ applicationStatus.reason }}
              </div>
              <p class="mt-2">You can update your details and resubmit the form below.</p>
            </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <!-- Business Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fa-solid fa-building mr-2"></i>
            Business Information
          </h3>
          
          <div class="form-group">
            <label>Business Name <span class="required">*</span></label>
            <div class="input-with-icon">
              <i class="fa-solid fa-store"></i>
              <input 
                v-model="form.business_name" 
                type="text" 
                placeholder="Your Business Name" 
                required
                :disabled="loading"
              >
            </div>
            <div v-if="errors.business_name" class="error-message">{{ errors.business_name[0] }}</div>
          </div>

          <div class="form-group">
            <label>Business Type <span class="required">*</span></label>
            <div class="input-with-icon">
              <i class="fa-solid fa-briefcase"></i>
              <select 
                v-model="form.business_type" 
                required
                :disabled="loading"
                class="form-select"
              >
                <option value="">Select Business Type</option>
                <option value="shop">Shop / Store</option>
                <option value="doctor">Doctor / Clinic</option>
                <option value="barber">Barber Shop</option>
              </select>
            </div>
            <div v-if="errors.business_type" class="error-message">{{ errors.business_type[0] }}</div>
          </div>

          <!-- Shop Category (Only if Shop is selected) -->
          <div v-if="form.business_type === 'shop'" class="form-group">
            <label>Shop Category <span class="required">*</span></label>
            <div class="input-with-icon">
              <i class="fa-solid fa-tags"></i>
              <select 
                v-model="form.shop_category_id" 
                required
                :disabled="loading"
                class="form-select"
              >
                <option value="">Select Category</option>
                <option v-for="category in shopCategories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
              </select>
            </div>
            <div v-if="errors.shop_category_id" class="error-message">{{ errors.shop_category_id[0] }}</div>
          </div>

          <div class="form-group">
            <label>Business Description <span class="required">*</span></label>
            <textarea 
              v-model="form.description" 
              placeholder="Describe your business, services, and what makes you unique..." 
              required
              :disabled="loading"
              rows="4"
              class="form-textarea"
            ></textarea>
            <div v-if="errors.description" class="error-message">{{ errors.description[0] }}</div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fa-solid fa-phone mr-2"></i>
            Contact Information
          </h3>
          
          <div class="form-row">
            <div class="form-group">
              <label>Phone Number <span class="required">*</span></label>
              <div class="input-with-icon">
                <i class="fa-solid fa-phone"></i>
                <input 
                  v-model="form.phone" 
                  type="tel" 
                  placeholder="+1 234 567 8900" 
                  required
                  :disabled="loading"
                >
              </div>
              <div v-if="errors.phone" class="error-message">{{ errors.phone[0] }}</div>
            </div>

            <div class="form-group">
              <label>Email Address <span class="required">*</span></label>
              <div class="input-with-icon">
                <i class="fa-solid fa-envelope"></i>
                <input 
                  v-model="form.email" 
                  type="email" 
                  placeholder="business@example.com" 
                  required
                  :disabled="loading"
                >
              </div>
              <div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div>
            </div>
          </div>

          <div class="form-group">
            <label>Website (Optional)</label>
            <div class="input-with-icon">
              <i class="fa-solid fa-globe"></i>
              <input 
                v-model="form.website" 
                type="url" 
                placeholder="https://www.yourbusiness.com" 
                :disabled="loading"
              >
            </div>
            <div v-if="errors.website" class="error-message">{{ errors.website[0] }}</div>
          </div>
        </div>

        <!-- Location Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fa-solid fa-location-dot mr-2"></i>
            Location Information
          </h3>
          
          <div class="form-group">
            <label>Business Address <span class="required">*</span></label>
            <div class="input-with-icon">
              <i class="fa-solid fa-map-marker-alt"></i>
              <textarea 
                v-model="form.address" 
                placeholder="Street address, city, state, zip code..." 
                required
                :disabled="loading"
                rows="3"
                class="form-textarea"
              ></textarea>
            </div>
            <div v-if="errors.address" class="error-message">{{ errors.address[0] }}</div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>City <span class="required">*</span></label>
              <div class="input-with-icon">
                <i class="fa-solid fa-city"></i>
                <input 
                  v-model="form.city" 
                  type="text" 
                  placeholder="City" 
                  required
                  :disabled="loading"
                >
              </div>
              <div v-if="errors.city" class="error-message">{{ errors.city[0] }}</div>
            </div>

            <div class="form-group">
              <label>State/Province <span class="required">*</span></label>
              <div class="input-with-icon">
                <i class="fa-solid fa-map"></i>
                <input 
                  v-model="form.state" 
                  type="text" 
                  placeholder="State/Province" 
                  required
                  :disabled="loading"
                >
              </div>
              <div v-if="errors.state" class="error-message">{{ errors.state[0] }}</div>
            </div>
          </div>
        </div>

        <!-- Business Hours -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fa-solid fa-clock mr-2"></i>
            Business Hours (Optional)
          </h3>
          
          <div class="business-hours">
            <div v-for="day in daysOfWeek" :key="day.value" class="day-row">
              <div class="day-checkbox">
                <label class="checkbox-container">
                  <input 
                    type="checkbox" 
                    v-model="form.operating_days" 
                    :value="day.value"
                    :disabled="loading"
                  >
                  <span class="checkmark"></span>
                  {{ day.label }}
                </label>
              </div>
              
              <div v-if="form.operating_days.includes(day.value)" class="time-inputs">
                <input 
                  type="time" 
                  v-model="form.hours[day.value + '_open']"
                  :disabled="loading"
                  class="time-input"
                >
                <span class="time-separator">to</span>
                <input 
                  type="time" 
                  v-model="form.hours[day.value + '_close']"
                  :disabled="loading"
                  class="time-input"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fa-solid fa-info-circle mr-2"></i>
            Additional Information
          </h3>
          
          <div class="form-group">
            <label>Services/Products Offered</label>
            <textarea 
              v-model="form.services" 
              placeholder="List your main services or products..." 
              :disabled="loading"
              rows="3"
              class="form-textarea"
            ></textarea>
            <div class="form-note">Help customers understand what you offer</div>
          </div>

          <div class="form-group">
            <label>{{ imageLabel }} (Optional)</label>
            <div class="image-upload-wrapper">
              <input 
                type="file" 
                @change="handleImageUpload" 
                multiple 
                accept="image/*" 
                class="hidden-input"
                id="business-images"
                :disabled="loading"
              >
              <label for="business-images" :class="['upload-trigger', { disabled: loading }]">
                <i class="fa-solid fa-images mr-2"></i>
                <span>{{ selectedImages.length ? 'Add More Images' : 'Select Business Images' }}</span>
              </label>
              
              <div v-if="imagePreviews.length" class="image-previews">
                <div v-for="(src, index) in imagePreviews" :key="index" class="preview-item">
                  <img :src="src" alt="Preview">
                  <button @click.prevent="removeImage(index)" class="remove-btn" title="Remove image">
                    <i class="fa-solid fa-circle-xmark"></i>
                  </button>
                  <div v-if="index === 0" class="primary-badge">Primary</div>
                </div>
              </div>
            </div>
            <div class="form-note">Upload clear photos to showcase your business. First image will be used as cover.</div>
          </div>

          <div class="form-options">
            <label class="checkbox-container">
              <input type="checkbox" v-model="form.agree_terms" required>
              <span class="checkmark"></span>
              I agree to the <a href="#" class="terms-link">Terms & Conditions</a> and confirm that the information provided is accurate
            </label>
          </div>
        </div>

        <div class="form-actions">
          <BaseButton 
            type="submit" 
            variant="primary" 
            :loading="loading" 
            class="w-100"
            size="lg"
          >
            <i class="fa-solid fa-store mr-2"></i>
            Register Business
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import BaseButton from '../../Components/BaseButton.vue';
import axios from 'axios';
import swal from '../../utils/swal';

const router = useRouter();
const loading = ref(false);
const errors = ref({});
const shopCategories = ref([]);

const form = reactive({
  business_name: '',
  business_type: '', // shop, doctor, barber
  shop_category_id: '', // Updated from shop_category (string) to ID
  description: '',
  phone: '',
  email: '',
  website: '',
  address: '',
  city: '',
  state: '',
  services: '',
  operating_days: [],
  hours: {},
  agree_terms: false
});

const applicationStatus = ref(null);
const selectedImages = ref([]);
const imagePreviews = ref([]);

const imageLabel = computed(() => {
    switch (form.business_type) {
        case 'shop': return 'Shop Images';
        case 'doctor': return 'Clinic Images';
        case 'barber': return 'Barber Shop Images';
        default: return 'Business Images';
    }
});

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        selectedImages.value.push(file);
        const reader = new FileReader();
        reader.onload = (e) => imagePreviews.value.push(e.target.result);
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    selectedImages.value.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const fetchStatus = async () => {
    try {
        const response = await axios.get('/api/v1/register-business/status');
        if (response.data.success && response.data.data) {
            // Only show if pending or rejected
            if (['pending', 'rejected'].includes(response.data.data.status)) {
                applicationStatus.value = response.data.data;
                // Pre-fill some data if rejected
                if (response.data.data.status === 'rejected') {
                    // Pre-filling could go here if we had more data from status
                }
            }
        }
    } catch (error) {
        console.error('Failed to fetch application status', error);
    }
};

const daysOfWeek = [
  { label: 'Monday', value: 'monday' },
  { label: 'Tuesday', value: 'tuesday' },
  { label: 'Wednesday', value: 'wednesday' },
  { label: 'Thursday', value: 'thursday' },
  { label: 'Friday', value: 'friday' },
  { label: 'Saturday', value: 'saturday' },
  { label: 'Sunday', value: 'sunday' }
];

onMounted(async () => {
    fetchStatus();
    try {
        const response = await axios.get('/api/v1/shop-categories');
        if (response.data.success) {
            shopCategories.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to fetch shop categories', error);
    }
});

const handleRegister = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
        // Prepare the data with FormData for file uploads
        const formData = new FormData();
        formData.append('name', form.business_name);
        formData.append('type', form.business_type);
        if (form.business_type === 'shop' && form.shop_category_id) {
            formData.append('shop_category_id', form.shop_category_id);
        }
        formData.append('description', form.description);
        formData.append('phone', form.phone);
        formData.append('email', form.email);
        formData.append('website', form.website || '');
        formData.append('address', form.address);
        formData.append('city', form.city);
        formData.append('state', form.state);
        formData.append('services', form.services || '');
        
        form.operating_days.forEach(day => formData.append('operating_days[]', day));
        
        Object.keys(form.hours).forEach(key => {
            formData.append(`business_hours[${key}]`, form.hours[key]);
        });

        selectedImages.value.forEach(file => {
            formData.append('images[]', file);
        });

        const response = await axios.post('/api/v1/register-business', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            router.push({ 
                name: 'business.register.success', 
                query: { id: response.data.data.id } 
            });
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
        } else {
            swal.fire({
                title: 'Registration Failed',
                text: error.response?.data?.message || 'Something went wrong. Please try again.',
                icon: 'error'
            });
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.register-business-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f5f8fa;
  background-image: radial-gradient(#3699ff 1px, transparent 1px);
  background-size: 40px 40px;
  background-attachment: fixed;
  padding: 40px 20px;
}

.register-business-card {
  width: 100%;
  max-width: 800px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.08);
  overflow: hidden;
}

.register-header {
  text-align: center;
  padding: 40px 40px 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.header-icon {
  width: 80px;
  height: 80px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 2rem;
  backdrop-filter: blur(10px);
}

.brand {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 10px;
}

.subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  font-weight: 500;
}

.register-form {
  padding: 40px;
}

.form-section {
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 1px solid #f1f3f8;
}

.form-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e1e2d;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 0.9rem;
  font-weight: 600;
  color: #3f4254;
  margin-bottom: 8px;
}

.required {
  color: #f64e60;
}

.form-note {
  font-size: 0.8rem;
  color: #7e8299;
  margin-top: 4px;
  font-style: italic;
}

.error-message {
  font-size: 0.8rem;
  color: #f64e60;
  margin-top: 4px;
}

.input-with-icon {
  position: relative;
}

.input-with-icon i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #b5b5c3;
  z-index: 2;
}

.input-with-icon input,
.input-with-icon select {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  background: #f9f9fc;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.input-with-icon textarea {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  background: #f9f9fc;
  transition: all 0.2s;
  font-size: 0.95rem;
  resize: vertical;
  font-family: inherit;
}

.form-select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
}

.form-textarea {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #e1e3ea;
  border-radius: 8px;
  background: #f9f9fc;
  transition: all 0.2s;
  font-size: 0.95rem;
  resize: vertical;
  font-family: inherit;
}

.input-with-icon input:focus,
.input-with-icon select:focus,
.input-with-icon textarea:focus,
.form-textarea:focus {
  border-color: #3699ff;
  background: #fff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
}

/* Business Hours */
.business-hours {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 12px;
}

.day-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #e9ecef;
}

.day-row:last-child {
  border-bottom: none;
}

.day-checkbox {
  min-width: 120px;
}

.time-inputs {
  display: flex;
  align-items: center;
  gap: 10px;
}

.time-input {
  padding: 6px 10px;
  border: 1px solid #e1e3ea;
  border-radius: 6px;
  background: white;
  font-size: 0.85rem;
}

.time-separator {
  color: #7e8299;
  font-weight: 500;
  font-size: 0.9rem;
}

/* Checkbox */
.checkbox-container {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
  color: #7e8299;
  cursor: pointer;
  user-select: none;
}

.checkbox-container input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin: 0;
}

.checkmark {
  /* Custom checkbox styling can be added here */
}

.form-options {
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #f1f3f8;
}

.terms-link {
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
}

.terms-link:hover {
  text-decoration: underline;
}

.form-actions {
  margin-top: 30px;
  padding-top: 25px;
  border-top: 1px solid #f1f3f8;
}

.w-100 { width: 100%; }
.mr-2 { margin-right: 8px; }

/* Responsive */
@media (max-width: 768px) {
  .register-business-wrapper {
    padding: 20px 10px;
  }
  
  .register-header {
    padding: 30px 20px 20px;
  }
  
  .register-form {
    padding: 30px 20px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .day-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .time-inputs {
    width: 100%;
    justify-content: flex-start;
  }
  
  .brand {
    font-size: 1.6rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
}

/* Status Notice Styles */
.status-notice-wrapper {
  padding: 20px 40px 0;
}

.status-card {
  display: flex;
  gap: 20px;
  padding: 20px;
  border-radius: 12px;
  border: 1px solid transparent;
  align-items: flex-start;
}

.status-card.pending {
  background-color: #fff8dd;
  border-color: #ffc700;
  color: #856404;
}

.status-card.rejected {
  background-color: #fff5f8;
  border-color: #f64e60;
  color: #7e3339;
}

.status-icon {
  font-size: 1.8rem;
  padding-top: 2px;
}

.status-content h4 {
  margin: 0 0 5px;
  font-weight: 700;
  font-size: 1.1rem;
}

.status-content p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.4;
}

.rejection-reason {
  margin: 10px 0;
  padding: 10px;
  background: rgba(246, 78, 96, 0.1);
  border-left: 4px solid #f64e60;
  font-style: italic;
}

/* Image Upload Styles */
.image-upload-wrapper {
  margin-top: 10px;
}

.hidden-input {
  display: none;
}

.upload-trigger {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  border: 2px dashed #e1e3ea;
  border-radius: 12px;
  background: #f9f9fc;
  color: #7e8299;
  cursor: pointer;
  transition: all 0.2s;
}

.upload-trigger:hover:not(.disabled) {
  border-color: #3699ff;
  color: #3699ff;
  background: rgba(54, 153, 255, 0.05);
}

.upload-trigger.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.image-previews {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 15px;
  margin-top: 20px;
}

.preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e1e3ea;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background: white;
  border: none;
  color: #f64e60;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  transition: transform 0.2s;
}

.remove-btn:hover {
  transform: scale(1.1);
}

.primary-badge {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(54, 153, 255, 0.9);
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  text-align: center;
  padding: 4px;
  text-transform: uppercase;
}

@media (max-width: 768px) {
  .status-notice-wrapper {
    padding: 20px 20px 0;
  }
}
</style>