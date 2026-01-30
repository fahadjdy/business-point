<template>
  <div class="form-group">
    <label v-if="label" class="form-label">{{ label }}</label>
    <div class="editor-wrapper" :class="{ 'has-error': error }">
      <QuillEditor
        v-model:content="content"
        contentType="html"
        theme="snow"
        :placeholder="placeholder"
        toolbar="essential"
      />
    </div>
    <span v-if="error" class="error-msg">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
  modelValue: String,
  label: String,
  placeholder: String,
  error: String
});

const emit = defineEmits(['update:modelValue']);

const content = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>

<style>
/* Quill Overrides */
.ql-toolbar.ql-snow {
  border-color: #e1e3ea;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  background-color: #f9f9fc;
}

.ql-container.ql-snow {
  border-color: #e1e3ea;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  font-family: inherit;
  font-size: 0.95rem;
  min-height: 150px;
}

.ql-editor { 
  min-height: 150px;
  color: #3f4254;
}

.ql-editor.ql-blank::before {
  color: #b5b5c3;
  font-style: normal;
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

.editor-wrapper.has-error .ql-toolbar,
.editor-wrapper.has-error .ql-container {
  border-color: #f64e60;
}

.error-msg {
  color: #f64e60;
  font-size: 0.8rem;
  margin-top: 5px;
  display: block;
}
</style>
