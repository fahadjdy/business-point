<template>
  <div class="form-group">
    <label v-if="label" class="form-label">{{ label }}</label>
    <div class="input-wrapper">
      <i v-if="icon" :class="[icon, 'input-icon']"></i>
      <input
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="form-input"
        :class="{ 'has-icon': icon, 'is-invalid': error }"
        :placeholder="placeholder"
        v-bind="$attrs"
      >
    </div>
    <span v-if="error" class="error-msg">{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  type: { type: String, default: 'text' },
  placeholder: String,
  icon: String,
  error: String
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 0.9rem;
  font-weight: 500;
  color: #3f4254;
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #b5b5c3;
  font-size: 1rem;
}

.form-input {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e1e3ea;
  font-size: 0.95rem;
  transition: all 0.2s;
  color: #3f4254;
}

.form-input:focus {
  outline: none;
  border-color: #3699ff;
  box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
}

.form-input.has-icon {
  padding-left: 40px;
}

.form-input.is-invalid {
  border-color: #f64e60;
}

.error-msg {
  color: #f64e60;
  font-size: 0.8rem;
  margin-top: 5px;
  display: block;
}
</style>
