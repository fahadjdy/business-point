<template>
  <div v-if="visible" :class="['alert', type]">
    <div class="alert-content">
      <i v-if="icon" :class="[icon, 'alert-icon']"></i>
      <div class="alert-text">
        <slot />
      </div>
    </div>
    <button v-if="dismissible" @click="close" class="close-btn">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  type: { type: String, default: 'primary' }, // primary, success, danger, warning
  icon: String,
  dismissible: Boolean
});

const visible = ref(true);

const close = () => {
  visible.value = false;
};
</script>

<style scoped>
.alert {
  padding: 15px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.alert-content {
  display: flex;
  gap: 12px;
}

.alert-icon {
  font-size: 1.1rem;
  margin-top: 2px;
}

.alert-text {
  font-size: 0.95rem;
  font-weight: 500;
}

.close-btn {
  background: none;
  border: none;
  color: inherit;
  opacity: 0.5;
  cursor: pointer;
  padding: 0;
  font-size: 1.1rem;
}

.close-btn:hover {
  opacity: 1;
}

/* Types */
.primary { background: #e1f0ff; color: #3699ff; }
.success { background: #c9f7f5; color: #1bc5bd; }
.danger { background: #ffe2e5; color: #f64e60; }
.warning { background: #fff4de; color: #ffa800; }
</style>
