<template>
  <div class="stat-card">
    <div class="stat-icon" :style="{ backgroundColor: color + '20', color: color }">
      <i :class="icon"></i>
    </div>
    <div class="stat-info">
      <span class="stat-label">{{ label }}</span>
      <h3 class="stat-value">{{ value }}</h3>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: [String, Number],
  icon: String,
  color: {
    type: String,
    default: '#3699ff'
  }
});
</script>

<style scoped>
.stat-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-label {
  color: #b5b5c3;
  font-size: 0.9rem;
  font-weight: 500;
  display: block;
}

.stat-value {
  color: #3f4254;
  font-size: 1.5rem;
  font-weight: 700;
  margin: 5px 0 0;
}
</style>
