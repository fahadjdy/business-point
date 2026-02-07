<template>
  <div class="settings-page-wrapper">
    <div class="settings-page pulsate-in">
      <div class="card settings-card">
        <!-- Sticky Save Header -->
        <div class="card-header sticky-header">
          <div class="tabs-nav">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              class="tab-btn" 
              :class="{ 'active': activeTab === tab.id }"
              @click="activeTab = tab.id"
            >
              <i :class="tab.icon"></i>
              <span>{{ tab.label }}</span>
            </button>
          </div>
          <div class="header-actions">
            <BaseButton variant="primary" icon="fa-solid fa-floppy-disk" @click="save" :loading="saving">
              Save Changes
            </BaseButton>
          </div>
        </div>

        <div class="card-body">
          <!-- General Tab -->
          <div v-show="activeTab === 'general'" class="tab-content">
            <div class="form-section">
               <h4 class="section-title"><i class="fa-solid fa-palette text-primary"></i> Branding</h4>
               <div class="grid-2-col">
                  <!-- Logo Upload -->
                  <div class="upload-box" @click="$refs.logoInput.click()">
                    <div class="upload-preview" :style="logoPreview ? `background-image: url(${logoPreview})` : ''">
                      <i v-if="!logoPreview" class="fa-solid fa-image"></i>
                    </div>
                    <div class="upload-info">
                       <span class="upload-title">Site Logo</span>
                       <small>Recommended: 200x60px</small>
                    </div>
                    <input type="file" ref="logoInput" class="d-none" accept="image/*" @change="handleFileChange($event, 'logo')">
                  </div>

                  <!-- Favicon Upload -->
                  <div class="upload-box" @click="$refs.favInput.click()">
                    <div class="upload-preview favicon-preview" :style="favPreview ? `background-image: url(${favPreview})` : ''">
                      <i v-if="!favPreview" class="fa-solid fa-globe"></i>
                    </div>
                    <div class="upload-info">
                       <span class="upload-title">Favicon</span>
                       <small>Recommended: 32x32px</small>
                    </div>
                    <input type="file" ref="favInput" class="d-none" accept="image/*" @change="handleFileChange($event, 'favicon')">
                  </div>
               </div>
               
               <div class="form-grid mt-4">
                 <BaseInput v-model="form.brand_name" label="Brand Name" icon="fa-solid fa-building" />
                 <BaseInput v-model="form.brand_tagline" label="Tagline" icon="fa-solid fa-flag" />
                 <div class="full-width">
                   <BaseEditor v-model="form.about_company" label="About Company" placeholder="Describe your company..." />
                 </div>
               </div>
            </div>

            <div class="divider"></div>

            <div class="form-section">
              <h4 class="section-title"><i class="fa-solid fa-address-card text-primary"></i> Contact Info</h4>
              <div class="form-grid">
                <BaseInput v-model="form.app_email" label="Support Email" icon="fa-solid fa-envelope" />
                <BaseInput v-model="form.app_phone" label="Support Phone" icon="fa-solid fa-phone" />
              </div>
            </div>
          </div>

          <!-- Configuration Tab -->
          <div v-show="activeTab === 'config'" class="tab-content">
             <div class="config-grid">
               <!-- System Configuration -->
               <div class="config-section">
                 <h4 class="section-title">System Policies</h4>
                 <div class="toggles-list">
                    <div class="toggle-item">
                      <div class="toggle-text">
                        <h6>Maintenance Mode</h6>
                        <p>Disable public access to the site.</p>
                      </div>
                      <label class="switch">
                        <input type="checkbox" v-model="form.maintenance_mode">
                        <span class="slider round"></span>
                      </label>
                    </div>
                    
                    <div class="toggle-item">
                      <div class="toggle-text">
                        <h6>Allow Self Registration</h6>
                        <p>Users/Vendors can sign up themselves.</p>
                      </div>
                      <label class="switch">
                        <input type="checkbox" v-model="form.allow_self_registration">
                        <span class="slider round"></span>
                      </label>
                    </div>

                    <div class="toggle-item">
                      <div class="toggle-text">
                        <h6>Auto-Verify Vendors</h6>
                        <p>Approve vendors automatically on signup.</p>
                      </div>
                      <label class="switch">
                        <input type="checkbox" v-model="form.auto_verify_vendors">
                        <span class="slider round"></span>
                      </label>
                    </div>
                 </div>
               </div>

               <div class="config-section">
                 <h4 class="section-title">Notifications</h4>
                 <div class="toggles-list">
                    <div class="toggle-item">
                      <div class="toggle-text">
                        <h6>Signup Notifications</h6>
                        <p>Email admin on new registrations.</p>
                      </div>
                      <label class="switch">
                        <input type="checkbox" v-model="form.notify_signup">
                        <span class="slider round"></span>
                      </label>
                    </div>
                 </div>
               </div>
             </div>
             
             <div class="divider" v-if="form.maintenance_mode"></div>
             
             <div class="maintenance-editor" v-if="form.maintenance_mode">
               <h4 class="section-title text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Maintenance Note</h4>
               <BaseEditor v-model="form.maintenance_note" placeholder="We are undergoing maintenance..." />
             </div>
          </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="card-footer sticky-footer">
           <div class="footer-content">
             <BaseButton variant="primary" icon="fa-solid fa-floppy-disk" @click="save" :loading="saving">
              Save Changes
            </BaseButton>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseAlert from '@/Components/BaseAlert.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseEditor from '@/Components/BaseEditor.vue';
import { useNotificationStore } from '@/store/notification';

const { addNotification } = useNotificationStore();
const activeTab = ref('general');
const saving = ref(false);
const logoPreview = ref('');
const favPreview = ref('');

const logoInput = ref(null);
const favInput = ref(null);

const tabs = [
  { id: 'general', label: 'General', icon: 'fa-solid fa-sliders' },
  { id: 'config', label: 'Configuration', icon: 'fa-solid fa-gears' },
];

const form = reactive({
  brand_name: '',
  brand_tagline: '',
  about_company: '',
  app_email: '',
  app_phone: '',
  maintenance_mode: false,
  maintenance_note: '',
  notify_signup: false,
  auto_verify_vendors: false,
  allow_self_registration: true,
  site_logo: null,
  site_favicon: null
});

const handleFileChange = (event, type) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    if (type === 'logo') {
      logoPreview.value = e.target.result;
      form.site_logo = file;
    } else {
      favPreview.value = e.target.result;
      form.site_favicon = file;
    }
  };
  reader.readAsDataURL(file);
};

const fetchData = async () => {
  try {
    const response = await axios.get('/admin/settings-data');
    if (response.data.success) {
      const data = response.data.data;
      Object.keys(form).forEach(key => {
        if (key !== 'site_logo' && key !== 'site_favicon' && data[key] !== undefined) {
          form[key] = data[key];
        }
      });
      if (data.site_logo && typeof data.site_logo === 'string') logoPreview.value = data.site_logo;
      if (data.site_favicon && typeof data.site_favicon === 'string') favPreview.value = data.site_favicon;
    }
  } catch (error) {
    console.error('Error fetching settings:', error);
  }
};

const save = async () => {
  saving.value = true;
  
  const formData = new FormData();
  Object.keys(form).forEach(key => {
    if (typeof form[key] === 'boolean') {
        formData.append(key, form[key] ? '1' : '0');
    } else if (form[key] !== null) {
        formData.append(key, form[key]);
    }
  });

  try {
    const response = await axios.post('/admin/settings-data', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    if (response.data.success) {
      addNotification('Settings updated successfully!');
    }
  } catch (error) {
    addNotification(error.response?.data?.message || 'Error saving settings', 'danger');
  } finally {
    saving.value = false;
  }
};

onMounted(fetchData);
</script>

<style scoped>
.settings-card {
  border: none;
  background: transparent;
  box-shadow: none;
  overflow: visible; /* Ensure dropdowns aren't clipped */
}

.sticky-header {
  position: sticky;
  top: 0;
  z-index: 50; /* Lower than global header (90) */
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(5px);
  border-bottom: 1px solid #eff2f5;
  padding: 10px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.card-body {
  background: #fff;
  padding: 30px;
  min-height: 400px;
  overflow: visible; /* Important for dropdowns */
}

.sticky-footer {
  position: sticky;
  bottom: 0;
  z-index: 40;
  background: #fff;
  border-top: 1px solid #eff2f5;
  padding: 15px 30px;
  border-radius: 0 0 12px 12px;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.03);
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tabs-nav {
  display: flex;
  gap: 15px;
}

.tab-btn {
  background: none;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  color: #7e8299;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.tab-btn:hover { background: #f5f8fa; color: #3699ff; }
.tab-btn.active { background: #e1f0ff; color: #3699ff; }

.section-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #181c32;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.full-width { grid-column: span 2; }
.grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.upload-box {
  background: #f9f9fc;
  border: 2px dashed #e1e3ea;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 20px;
  cursor: pointer;
  transition: all 0.2s;
}
.upload-box:hover { border-color: #3699ff; background: #f0f7ff; }

.upload-preview {
  width: 60px;
  height: 60px;
  background: #fff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #b5b5c3;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  background-size: cover;
  background-position: center;
}

.upload-title { display: block; font-weight: 600; color: #3f4254; font-size: 1rem; }
.upload-info small { color: #b5b5c3; }

.divider { height: 1px; background: #eff2f5; margin: 40px 0; }

.config-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }

.toggles-list { display: flex; flex-direction: column; gap: 15px; }

.toggle-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #f9f9fc;
  border-radius: 10px;
  border: 1px solid transparent;
}
.toggle-item:hover { border-color: #e1e3ea; }

.toggle-text h6 { margin: 0; font-weight: 600; color: #3f4254; }
.toggle-text p { margin: 0; font-size: 0.85rem; color: #b5b5c3; }

/* Switch Logic */
.switch { position: relative; display: inline-flex; width: 42px; height: 22px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; inset: 0; background-color: #e4e6ef; transition: .4s; border-radius: 34px; cursor: pointer; }
.slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #3699ff; }
input:checked + .slider:before { transform: translateX(20px); }

.text-primary { color: #3699ff !important; }
.text-danger { color: #f64e60 !important; }
.mt-4 { margin-top: 1.5rem; }

@media (max-width: 992px) {
  .sticky-header { flex-direction: column; gap: 15px; align-items: stretch; position: static; }
  .form-grid, .grid-2-col, .config-grid { grid-template-columns: 1fr; }
  .full-width { grid-column: span 1; }
  .header-actions { display: flex; justify-content: flex-end; }
  .sticky-footer { position: fixed; bottom: 0; left: 0; width: 100%; border-radius: 0; }
}
</style>
