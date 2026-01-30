<template>
  <div class="contact-detail-page bg-gray-50 min-height-screen">
    <div v-if="loading" class="flex items-center justify-center p-20">
      <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
    </div>

    <div v-else-if="contact" class="container mx-auto py-10 px-4">
      <!-- Back Button -->
      <router-link :to="{ name: 'contacts.index' }" class="inline-flex items-center text-gray-500 hover:text-primary mb-6 font-medium transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Directory
      </router-link>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Image & Quick Actions -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="aspect-square bg-gray-100 flex items-center justify-center">
              <img v-if="contact.image" :src="contact.image" :alt="contact.name" class="w-full h-full object-cover" />
              <i v-else class="fa-solid fa-user text-8xl text-gray-200"></i>
            </div>
            <div class="p-6">
              <div class="flex flex-col gap-3">
                <a :href="'tel:' + contact.phone" class="btn btn-primary w-full flex items-center justify-center py-4 text-lg font-bold">
                  <i class="fa-solid fa-phone-flip mr-3"></i> Call Now
                </a>
                <a v-if="contact.email" :href="'mailto:' + contact.email" class="btn btn-light-primary w-full flex items-center justify-center py-4 text-lg font-medium">
                  <i class="fa-solid fa-envelope mr-3"></i> Send Email
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content: Details -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 h-full">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-black uppercase tracking-wider rounded-full">
                    {{ contact.type }}
                  </span>
                  <span v-if="contact.is_active" class="flex items-center text-green-500 text-xs font-bold">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Available
                  </span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 mb-1">{{ contact.name }}</h1>
                <p v-if="contact.designation" class="text-xl text-gray-500 font-medium">
                  {{ contact.designation }} <span v-if="contact.department" class="text-gray-300 mx-2">|</span> {{ contact.department }}
                </p>
              </div>
            </div>

            <hr class="border-gray-50 mb-8" />

            <div class="space-y-8">
              <!-- Contact Methods -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div class="detail-item">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Phone Number</h4>
                    <p class="text-xl font-bold text-primary">{{ contact.phone }}</p>
                 </div>
                 <div v-if="contact.email" class="detail-item">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Email Address</h4>
                    <p class="text-lg font-medium text-gray-700">{{ contact.email }}</p>
                 </div>
                 <div v-if="contact.address" class="detail-item md:col-span-2">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Office/Work Address</h4>
                    <p class="text-lg text-gray-600 leading-relaxed">{{ contact.address }}</p>
                 </div>
              </div>

              <!-- About / Description -->
              <div v-if="contact.description" class="about-section pt-4">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">About / Additional Info</h4>
                <div class="text-gray-600 leading-loose whitespace-pre-line text-lg">
                  {{ contact.description }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center p-32">
       <i class="fa-solid fa-face-frown text-6xl text-gray-200 mb-4"></i>
       <h3 class="text-xl font-bold text-gray-400">Contact Not Found</h3>
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
    const response = await axios.get(`/api/v1/contacts/${route.params.id}`);
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
.min-height-screen { min-height: 100vh; }
.text-primary { color: #3699ff; }
.bg-blue-50 { background-color: #f1faff; }

.btn {
  border-radius: 12px;
  transition: all 0.3s;
}

.btn-primary {
  background: #3699ff;
  color: white;
}

.btn-primary:hover {
  background: #187de4;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(54, 153, 255, 0.3);
}

.btn-light-primary {
  background: #f1faff;
  color: #3699ff;
}

.btn-light-primary:hover {
  background: #e1f0ff;
}

.detail-item {
  background: #fafbfc;
  padding: 15px 20px;
  border-radius: 12px;
}
</style>
