<template>
  <span class="badge" :class="[type, size]">
    <slot />
  </span>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'default' // primary, success, danger, warning, default
  },
  size: {
    type: String,
    default: 'md' // sm, md, lg
  }
});
</script>

<style scoped>
.badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  text-transform: capitalize;
}

.badge.sm { font-size: 0.7rem; padding: 2px 8px; }
.badge.md { font-size: 0.8rem; }
.badge.lg { font-size: 0.9rem; padding: 6px 14px; }

/* Types */
.badge.default { background: #f3f6f9; color: #7e8299; }
.badge.primary { background: #e1f0ff; color: #3699ff; }
.badge.success { background: #c9f7f5; color: #1bc5bd; }
.badge.danger { background: #ffe2e5; color: #f64e60; }
.badge.warning { background: #fff4de; color: #ffa800; }
</style>
