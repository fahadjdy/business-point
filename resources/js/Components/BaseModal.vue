<template>
  <teleport to="body">
    <div v-if="isVisible" class="modal-overlay" @click="handleOverlayClick">
      <div class="modal-container" :class="[`modal-${size}`]" @click.stop>
        <div class="modal-header">
          <h3 v-if="title" class="modal-title">{{ title }}</h3>
          <slot name="header"></slot>
          <button @click="$emit('close')" class="modal-close-btn">
            <i class="fa-solid fa-times"></i>
          </button>
        </div>
        
        <div class="modal-body">
          <slot></slot>
        </div>
        
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { onMounted, onUnmounted, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md'
  },
  strict: {
    type: Boolean,
    default: false
  },
  closeOnOverlay: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close', 'update:modelValue']);

// Support both v-model and :show patterns
const isVisible = computed(() => props.modelValue || props.show);

const handleOverlayClick = () => {
  if (props.closeOnOverlay) {
    emit('close');
    emit('update:modelValue', false);
  }
};

const handleEscapeKey = (event) => {
  if (event.key === 'Escape') {
    emit('close');
    emit('update:modelValue', false);
  }
};

onMounted(() => {
  if (isVisible.value) {
    document.addEventListener('keydown', handleEscapeKey);
    document.body.style.overflow = 'hidden';
  }
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);
  document.body.style.overflow = '';
});

// Watch for visibility changes
watch(isVisible, (newValue) => {
  if (newValue) {
    document.addEventListener('keydown', handleEscapeKey);
    document.body.style.overflow = 'hidden';
  } else {
    document.removeEventListener('keydown', handleEscapeKey);
    document.body.style.overflow = '';
  }
});
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.modal-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f3f8;
  flex-shrink: 0;
}

.modal-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e1e2d;
}

.modal-container.modal-sm {
  max-width: 400px;
}

.modal-container.modal-md {
  max-width: 600px;
}

.modal-container.modal-lg {
  max-width: 800px;
}

.modal-container.modal-xl {
  max-width: 1200px;
}

.modal-close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #7e8299;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.modal-close-btn:hover {
  background: #f8f9fa;
  color: #3f4254;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}

.modal-footer {
  padding: 20px 24px;
  border-top: 1px solid #f1f3f8;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-container {
    margin: 0;
    border-radius: 16px 16px 0 0;
    max-height: 95vh;
  }
  
  .modal-header,
  .modal-body,
  .modal-footer {
    padding-left: 16px;
    padding-right: 16px;
  }
}
</style>