<template>
  <div class="checkbox-wrap" :class="{ 'is-disabled': disabled }">
    <label class="checkbox-label" :for="id">
      <input
        class="checkbox-input"
        type="checkbox"
        :id="id"
        :checked="!!modelValue"
        :disabled="disabled"
        @change="$emit('update:modelValue', $event.target.checked)"
        v-bind="$attrs"
      />
      <span class="checkbox-box" aria-hidden="true"></span>
      <span v-if="label" class="checkbox-text">{{ label }}</span>
    </label>

    <div v-if="help || error" class="checkbox-meta">
      <div v-if="help && !error" class="checkbox-help">{{ help }}</div>
      <div v-if="error" class="checkbox-error">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [Boolean, Number], default: false },
  id: { type: String, default: undefined },
  label: { type: String, default: '' },
  help: { type: String, default: '' },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.checkbox-wrap {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.checkbox-label {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.checkbox-box {
  width: 20px;
  height: 20px;
  border-radius: 6px;
  border: 1px solid #e1e3ea;
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.checkbox-text {
  color: #3f4254;
  font-weight: 600;
  font-size: 0.95rem;
}

.checkbox-input:focus-visible + .checkbox-box {
  outline: none;
  border-color: #3699ff;
  box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.15);
}

.checkbox-input:checked + .checkbox-box {
  background: #3699ff;
  border-color: #3699ff;
}

.checkbox-input:checked + .checkbox-box::after {
  content: "";
  width: 10px;
  height: 10px;
  background: #fff;
  border-radius: 3px;
}

.checkbox-meta {
  padding-left: 30px;
}

.checkbox-help {
  font-size: 0.8rem;
  color: #b5b5c3;
}

.checkbox-error {
  font-size: 0.8rem;
  color: #f64e60;
  font-weight: 600;
}

.is-disabled .checkbox-label {
  cursor: not-allowed;
  opacity: 0.65;
}
</style>
