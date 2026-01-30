<template>
  <div class="settings-page">
    <div class="card">
      <div class="card-header">
        <h3>Account Settings</h3>
        <p>Manage your account preferences and security configuration.</p>
      </div>
      <div class="card-body">
           <div v-if="loading" class="text-center py-5">
               <i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>
           </div>
           
           <div v-else class="settings-list">
               <div class="settings-item">
                   <div class="item-info">
                       <h4>Email Notifications</h4>
                       <p>Receive updates about your account activity via email.</p>
                   </div>
                   <div class="item-action">
                       <label class="switch">
                           <input type="checkbox" v-model="form.email_notifications">
                           <span class="slider round"></span>
                       </label>
                   </div>
               </div>
               <div class="settings-divider"></div>
               <div class="settings-item">
                   <div class="item-info">
                       <h4>Public Profile</h4>
                       <p>Making your profile visible to other members of the platform.</p>
                   </div>
                   <div class="item-action">
                       <label class="switch">
                           <input type="checkbox" v-model="form.public_profile">
                           <span class="slider round"></span>
                       </label>
                   </div>
               </div>
           </div>
      </div>
      <div class="card-footer">
          <BaseButton variant="primary" :loading="saving" @click="saveSettings">Save Settings</BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseButton from '../../Components/BaseButton.vue';
import UserSettingService from '../../services/UserSettingService';
import swal from '../../utils/swal';

const loading = ref(true);
const saving = ref(false);

const form = ref({
    email_notifications: true,
    public_profile: true
});

const fetchSettings = async () => {
    loading.value = true;
    try {
        const response = await UserSettingService.getSettings();
        if (response.success) {
            const settings = response.data;
            if (settings.hasOwnProperty('email_notifications')) {
                form.value.email_notifications = settings.email_notifications === '1' || settings.email_notifications === true || settings.email_notifications === 'true';
            }
            if (settings.hasOwnProperty('public_profile')) {
                form.value.public_profile = settings.public_profile === '1' || settings.public_profile === true || settings.public_profile === 'true';
            }
        }
    } catch (error) {
        swal.toast('Failed to load settings', 'error');
    } finally {
        loading.value = false;
    }
};

const saveSettings = async () => {
    saving.value = true;
    try {
        const response = await UserSettingService.updateSettings(form.value);
        if (response.success) {
            swal.toast('Settings saved successfully');
        }
    } catch (error) {
        swal.toast('Failed to save settings', 'error');
    } finally {
        saving.value = false;
    }
};

onMounted(fetchSettings);
</script>

<style scoped>
.card {
  background: white;
  border-radius: 12px;
  border: 1px solid #eff2f5;
  box-shadow: 0 0 20px rgba(0,0,0,0.02);
}

.card-header {
  padding: 25px 30px;
  border-bottom: 1px solid #eff2f5;
}

.card-header h3 {
  margin: 0 0 5px;
  color: #181c32;
  font-weight: 700;
}

.card-header p {
  margin: 0;
  color: #b5b5c3;
  font-size: 0.9rem;
}

.card-body {
  padding: 30px;
}

.alert-info {
  background: #f1faff;
  color: #009ef7;
  padding: 15px 20px;
  border-radius: 8px;
  font-size: 0.95rem;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
}

.mr-2 { margin-right: 10px; }

.settings-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
}

.item-info h4 {
  margin: 0 0 5px;
  color: #3f4254;
  font-weight: 600;
}

.item-info p {
  margin: 0;
  color: #b5b5c3;
  font-size: 0.85rem;
}

.settings-divider {
  height: 1px;
  background: #eff2f5;
  margin: 5px 0;
}

.card-footer {
  padding: 20px 30px;
  border-top: 1px solid #eff2f5;
  display: flex;
  justify-content: flex-end;
}

/* Toggle Switch Styles */
.switch {
  position: relative;
  display: inline-block;
  width: 45px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #e4e6ef;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #3699ff;
}

input:focus + .slider {
  box-shadow: 0 0 1px #3699ff;
}

input:checked + .slider:before {
  transform: translateX(21px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
