<template>
  <nav v-if="items.length" class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb-list">
      <li v-for="(item, idx) in items" :key="idx" class="breadcrumb-item">
        <template v-if="item.to && idx !== items.length - 1">
          <router-link :to="item.to" class="breadcrumb-link">{{ item.label }}</router-link>
        </template>
        <template v-else>
          <span class="breadcrumb-current">{{ item.label }}</span>
        </template>
        <span v-if="idx !== items.length - 1" class="breadcrumb-sep">/</span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => []
  }
});
</script>

<style scoped>
.breadcrumb {
  margin-top: 4px;
}

.breadcrumb-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.breadcrumb-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
}

.breadcrumb-link {
  color: #3699ff;
  text-decoration: none;
  font-weight: 600;
}

.breadcrumb-link:hover {
  text-decoration: underline;
}

.breadcrumb-current {
  color: #7e8299;
  font-weight: 600;
}

.breadcrumb-sep {
  color: #b5b5c3;
  user-select: none;
}
</style>
