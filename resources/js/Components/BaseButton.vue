<template>
  <button 
    :type="type" 
    :class="['btn', variant, size, { 'btn-loading': loading }]"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
  >
    <i v-if="icon && !loading" :class="[icon, 'btn-icon']"></i>
    <span v-if="loading" class="spinner"></span>
    <slot v-else />
  </button>
</template>

<script setup>
defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' }, // primary, success, danger, warning, secondary, light
  size: { type: String, default: 'md' }, // sm, md, lg
  icon: String,
  loading: Boolean,
  disabled: Boolean
});

defineEmits(['click']);
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
  border: 1px solid transparent;
  gap: 8px;
  white-space: nowrap;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Variants */
.primary { background: #3699ff; color: #fff; }
.primary:hover:not(:disabled) { background: #187de4; }

.success { background: #1bc5bd; color: #fff; }
.success:hover:not(:disabled) { background: #0bb7af; }

.danger { background: #f64e60; color: #fff; }
.danger:hover:not(:disabled) { background: #ee2d41; }

.secondary { background: #e4e6ef; color: #3f4254; }
.secondary:hover:not(:disabled) { background: #d1d3e0; }

.light { background: #f3f6f9; color: #7e8299; }
.light:hover:not(:disabled) { background: #e1e5ec; }

/* Sizes */
.sm { padding: 4px 10px; font-size: 0.8rem; }
.lg { padding: 12px 24px; font-size: 1rem; }

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
