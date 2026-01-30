<template>
  <div class="profile-page">
    <div class="row pulsate-in">
      <!-- Left Side: Profile Card -->
      <div class="col-left">
        <div class="card profile-card">
          <div class="card-body text-center">
            <div class="avatar-wrapper" @click="$refs.photoInput.click()">
              <div class="avatar" v-if="!photoPreview">{{ currentUser?.name?.charAt(0).toUpperCase() }}</div>
              <img v-else :src="photoPreview" class="avatar-img" />
              <div class="status-indicator"></div>
              <div class="avatar-edit-overlay">
                <i class="fa-solid fa-camera"></i>
              </div>
            </div>
            <h2 class="user-name">{{ currentUser?.name }}</h2>
            <p class="user-role">{{ currentUser?.roles?.[0]?.name || 'Super Admin' }}</p>
            <div class="profile-actions">
               <input type="file" ref="photoInput" class="d-none" accept="image/*" @change="handlePhotoChange">
               <button class="btn-text" @click="$refs.photoInput.click()">
                 <i class="fa-solid fa-camera"></i> Change Photo
               </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side: Details with Tabs -->
      <div class="col-right">
        <div class="card">
          <div class="card-header tabs-header">
            <div 
              class="tab-item" 
              :class="{ active: activeTab === 'basic' }"
              @click="activeTab = 'basic'"
            >
              <i class="fa-regular fa-id-card"></i> Basic Information
            </div>
            <div 
              class="tab-item" 
              :class="{ active: activeTab === 'scurity' }"
              @click="activeTab = 'scurity'"
            >
              <i class="fa-solid fa-lock"></i> Change Password
            </div>
          </div>

          <div class="card-body">
            <!-- Basic Information Tab -->
            <form v-if="activeTab === 'basic'" @submit.prevent="save">
              <div class="form-grid">
                <BaseInput 
                  v-model="form.name" 
                  label="Full Name" 
                  icon="fa-solid fa-user"
                  required
                />
                <BaseInput 
                  v-model="form.email" 
                  label="Email Address" 
                  type="email"
                  icon="fa-solid fa-envelope"
                  required
                />
                <div class="full-width">
                   <BaseInput 
                    v-model="form.phone" 
                    label="Phone Number" 
                    icon="fa-solid fa-phone"
                  />
                </div>
                <template v-if="scope === 'user'">
                  <BaseSelect
                    v-model="form.blood_group"
                    label="Blood Group"
                    :options="['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']"
                    placeholder="Select blood group"
                  />
                  <BaseSelect
                    v-model="form.gender"
                    label="Gender"
                    :options="[
                      { label: 'Male', id: 'male' },
                      { label: 'Female', id: 'female' },
                      { label: 'Other', id: 'other' }
                    ]"
                    labelField="label"
                    :reduce="opt => opt.id"
                    placeholder="Select gender"
                  />
                  <div class="full-width">
                    <BaseSelect
                      v-model="form.skills"
                      label="Skills"
                      :options="availableSkills"
                      labelField="name"
                      :reduce="tag => tag.id"
                      multiple
                      placeholder="Select your skills"
                    />
                  </div>
                </template>
              </div>
              <div class="form-actions">
                <BaseButton type="submit" variant="primary" :loading="state.loading">
                  Save Changes
                </BaseButton>
              </div>
            </form>

            <!-- Change Password Tab -->
            <form v-if="activeTab === 'scurity'" @submit.prevent="save">
              <div class="form-stack">
                <BaseInput 
                  v-model="form.password" 
                  label="New Password" 
                  type="password"
                  icon="fa-solid fa-key"
                  placeholder="Enter new password"
                />
                <BaseInput 
                  v-model="form.password_confirmation" 
                  label="Confirm Password" 
                  type="password"
                  icon="fa-solid fa-check-double"
                  placeholder="Confirm new password"
                />
              </div>
              
              <div class="form-actions">
                <BaseButton type="submit" variant="primary" :loading="state.loading">
                  Update Password
                </BaseButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import BaseButton from '../../Components/BaseButton.vue';
import BaseInput from '../../Components/BaseInput.vue';
import BaseSelect from '../../Components/BaseSelect.vue';
import BaseAlert from '../../Components/BaseAlert.vue';
import { useAuthStore } from '../../store/auth';
import UserService from '../../services/UserService';
import AppService from '../../services/AppService';
import swal from '../../utils/swal';

const { state, updateProfile } = useAuthStore();
const activeTab = ref('basic');

const scope = computed(() => window.location.pathname.startsWith('/admin') ? 'admin' : 'user');
const currentUser = computed(() => scope.value === 'admin' ? state.admin : state.user);

const photoPreview = ref(currentUser.value?.photo_url || '');
const photoInput = ref(null);

const form = reactive({
  name: currentUser.value?.name || '',
  email: currentUser.value?.email || '',
  phone: currentUser.value?.phone || '',
  blood_group: currentUser.value?.blood_group || '',
  gender: currentUser.value?.gender || '',
  skills: currentUser.value?.skills?.map(s => s.id) || [],
  photo: null,
  password: '',
  password_confirmation: ''
});

const availableSkills = ref([]);

// Update form if currentUser changes (e.g. after refresh/load)
onMounted(async () => {
    if (currentUser.value) {
        form.name = currentUser.value.name;
        form.email = currentUser.value.email;
        form.phone = currentUser.value.phone;
        form.blood_group = currentUser.value.blood_group || '';
        form.gender = currentUser.value.gender || '';
        form.skills = currentUser.value.skills?.map(s => s.id) || [];
        photoPreview.value = currentUser.value.photo_url;
    }

    if (scope.value === 'user') {
      try {
        const response = await AppService.getTags('skill');
        if (response.success) {
          availableSkills.value = response.data;
        }
      } catch (error) {
        console.error('Error fetching skills:', error);
      }
    }
});

const handlePhotoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const save = async () => {
  // Simple validation based on tab
  if (activeTab.value === 'scurity' && !form.password) {
     return; // Don't submit empty password update usually, or show error
  }

  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('email', form.email);
  formData.append('phone', form.phone);
  
  if (scope.value === 'user') {
    formData.append('blood_group', form.blood_group || '');
    formData.append('gender', form.gender || '');
    form.skills.forEach(skillId => {
      formData.append('skills[]', skillId);
    });
  }

  if (form.photo) formData.append('photo', form.photo);
  if (form.password) {
    formData.append('password', form.password);
    formData.append('password_confirmation', form.password_confirmation);
  }

  const success = await updateProfile(formData, scope.value);
  if (success) {
    form.password = '';
    form.password_confirmation = '';
  }
};
</script>

<style scoped>
.row {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 30px;
  align-items: start;
}

.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 0 20px 0 rgba(76, 87, 125, 0.02);
  border: 1px solid #eff2f5;
  overflow: hidden;
}

.profile-card {
  padding: 30px;
}

.text-center { text-align: center; }

.avatar-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 20px;
  cursor: pointer;
}

.avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 15px;
  object-fit: cover;
  border: 1px solid #eff2f5;
}

.avatar-edit-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s;
  border-radius: 15px;
}

.avatar-wrapper:hover .avatar-edit-overlay {
  opacity: 1;
}

.d-none { display: none; }

.avatar {
  width: 100%;
  height: 100%;
  border-radius: 15px;
  background: #e1f0ff;
  color: #3699ff;
  font-size: 3rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-indicator {
  position: absolute;
  bottom: -5px;
  right: -5px;
  width: 25px;
  height: 25px;
  background: #1bc5bd;
  border: 4px solid white;
  border-radius: 50%;
}

.user-name {
  font-size: 1.3rem;
  font-weight: 700;
  color: #181c32;
  margin: 0 0 5px;
}

.user-role {
  color: #b5b5c3;
  margin-bottom: 20px;
  font-weight: 500;
}

.btn-text {
  background: none;
  border: none;
  color: #3699ff;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-text:hover { text-decoration: underline; }

/* Tabs */
.tabs-header {
  display: flex;
  border-bottom: 1px solid #eff2f5;
  padding: 0 20px;
}

.tab-item {
  padding: 20px 25px;
  font-weight: 600;
  color: #7e8299;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
}

.tab-item:hover { color: #3699ff; }

.tab-item.active {
  color: #3699ff;
  border-bottom-color: #3699ff;
}

/* Forms */
.card-body { padding: 30px; }

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.full-width { grid-column: span 2; }

.form-stack {
  display: flex;
  flex-direction: column;
  gap: 0;
  max-width: 500px;
}

.form-actions {
  margin-top: 30px;
  display: flex;
  justify-content: flex-end;
}

@media (max-width: 900px) {
  .row { grid-template-columns: 1fr; }
  .form-grid { grid-template-columns: 1fr; }
  .full-width { grid-column: span 1; }
}
</style>
