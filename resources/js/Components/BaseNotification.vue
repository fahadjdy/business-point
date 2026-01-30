<template>
  <div class="notification-container">
    <TransitionGroup name="list">
      <div 
        v-for="notification in state.notifications" 
        :key="notification.id"
        :class="['notification-item', notification.type]"
      >
        <div class="notification-icon">
          <i v-if="notification.type === 'success'" class="fa-solid fa-circle-check"></i>
          <i v-else-if="notification.type === 'danger'" class="fa-solid fa-circle-exclamation"></i>
          <i v-else class="fa-solid fa-circle-info"></i>
        </div>
        <div class="notification-content">
          {{ notification.message }}
        </div>
        <button class="notification-close" @click="removeNotification(notification.id)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useNotificationStore } from '../store/notification';

const { state, removeNotification } = useNotificationStore();
</script>

<style scoped>
.notification-container {
  position: fixed;
  top: 85px; /* Just below the header (which is 70px) + some spacing */
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none;
}

.notification-item {
  pointer-events: auto;
  min-width: 300px;
  max-width: 450px;
  padding: 15px 20px;
  border-radius: 10px;
  background: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 15px;
  border-left: 5px solid #3699ff;
  animation: slideIn 0.3s ease;
}

.notification-item.success { border-left-color: #1bc5bd; }
.notification-item.danger { border-left-color: #f64e60; }
.notification-item.warning { border-left-color: #ffa800; }

.notification-icon {
  font-size: 1.2rem;
}

.success .notification-icon { color: #1bc5bd; }
.danger .notification-icon { color: #f64e60; }
.warning .notification-icon { color: #ffa800; }

.notification-content {
  flex: 1;
  color: #3f4254;
  font-weight: 500;
  font-size: 0.95rem;
}

.notification-close {
  background: none;
  border: none;
  color: #b5b5c3;
  cursor: pointer;
  padding: 5px;
  transition: color 0.2s;
}

.notification-close:hover {
  color: #7e8299;
}

/* Animations */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(30px); }
  to { opacity: 1; transform: translateX(0); }
}
</style>
