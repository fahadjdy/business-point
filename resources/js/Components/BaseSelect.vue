<template>
  <div class="form-group">
    <label v-if="label" class="form-label">{{ label }}</label>
    <div class="select-wrapper" :class="{ 'has-error': error }">
      <v-select
        v-model="model"
        :options="options"
        :label="labelField"
        :reduce="reduce"
        :placeholder="placeholder"
        :searchable="searchable"
        :multiple="multiple"
        class="custom-select"
      >
        <template #open-indicator="{ attributes }">
          <i class="fa-solid fa-chevron-down select-arrow" v-bind="attributes"></i>
        </template>
        <template #no-options="{ search, searching }">
             <template v-if="searching">No results found for <em>{{ search }}</em>.</template>
             <em v-else>No options available.</em>
        </template>
      </v-select>
    </div>
    <span v-if="error" class="error-msg">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  modelValue: [String, Number, Array, Object],
  options: { type: Array, default: () => [] },
  label: String,
  labelField: { type: String, default: 'label' },
  // Function or property name to reduce the selected object to a value
  reduce: { type: Function, default: option => option },
  placeholder: { type: String, default: 'Select option...' },
  searchable: { type: Boolean, default: true },
  multiple: { type: Boolean, default: false },
  error: String
});

const emit = defineEmits(['update:modelValue']);

const model = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>

<style>
/* Global overrides for vue-select to match app theme */
:root {
  --vs-controls-color: #b5b5c3;
  --vs-border-color: #e1e3ea;
  --vs-border-radius: 8px;
  --vs-font-size: 0.95rem;
  --vs-line-height: 1.5;
  --vs-state-disabled-bg: #f3f6f9;
  --vs-state-disabled-color: #b5b5c3;
  --vs-state-disabled-cursor: not-allowed;
  --vs-dropdown-bg: #fff;
  --vs-dropdown-color: #3f4254;
  --vs-dropdown-option-color: #3f4254;
  --vs-dropdown-option--active-bg: #f3f6f9;
  --vs-dropdown-option--active-color: #3699ff;
}

.custom-select {
  width: 100%;
}

.custom-select .vs__dropdown-toggle {
  padding: 6px 10px;
  border-color: #e1e3ea;
  transition: all 0.2s;
  width: 100%;
  min-width: 220px; /* prevents shrinking inside flex rows */
  box-sizing: border-box;
}

.custom-select.vs--open .vs__dropdown-toggle {
  border-color: #3699ff;
  border-bottom-color: #3699ff;
  box-shadow: 0 0 0 3px rgba(54, 153, 255, 0.1);
}

.custom-select .vs__selected-options {
  flex-wrap: nowrap;
  min-width: 0;
}

.custom-select .vs__search,
.custom-select .vs__search:focus {
  flex: 1 1 auto;
  min-width: 60px; /* keeps input usable without resizing the control */
}

.custom-select .vs__selected {
  color: #3f4254;
  font-weight: 500;
}

.select-arrow {
  font-size: 0.8rem;
  color: #b5b5c3;
}

.has-error .vs__dropdown-toggle {
  border-color: #f64e60;
}
</style>

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

.error-msg {
  color: #f64e60;
  font-size: 0.8rem;
  margin-top: 5px;
  display: block;
}
</style>
