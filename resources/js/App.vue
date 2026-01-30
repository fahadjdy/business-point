<template>
  <component :is="layout" v-bind="layoutProps">
    <router-view></router-view>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AdminLayout from './Layouts/AdminLayout.vue';
import UserLayout from './Layouts/UserLayout.vue';
import GuestLayout from './Layouts/GuestLayout.vue';

const route = useRoute();

const layout = computed(() => {
  const layoutName = route.meta.layout;
  if (layoutName === 'Admin') return AdminLayout;
  if (layoutName === 'User') return UserLayout;
  if (layoutName === 'Guest') return GuestLayout;
  return 'div'; // Default for Blank or unspecified
});

const adminBreadcrumbs = computed(() => {
  const isAdmin = route.path.startsWith('/admin');
  if (!isAdmin) return [];

  const crumbs = [
    { label: 'Dashboard', to: { name: 'admin.dashboard' } }
  ];

  // Vendors
  if (route.name === 'vendors.index') {
    crumbs.push({ label: 'Vendors' });
    return crumbs;
  }
  if (route.name === 'vendors.show') {
    crumbs.push({ label: 'Vendors', to: { name: 'vendors.index' } });
    crumbs.push({ label: route.meta.title || 'Details' });
    return crumbs;
  }

  // Generic: Dashboard > Current Page
  const title = route.meta.title;
  if (title && route.name !== 'admin.dashboard') {
    crumbs.push({ label: title });
  } else {
    crumbs[0] = { label: 'Dashboard' };
  }

  return crumbs;
});

const layoutProps = computed(() => {
  if (route.meta.layout === 'Admin') {
    return {
      title: route.meta.title || '',
      breadcrumbs: adminBreadcrumbs.value
    };
  }

  return {};
});
</script>

<style>
/* Global styles can go here or in app.css */
</style>
