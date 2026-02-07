<template>
  <div class="manage-products">
    <div class="dashboard-header mb-5">
      <div class="header-info">
        <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
        <p class="text-gray-500">Manage your shop inventory and pricing</p>
      </div>
      <div class="header-actions">
         <router-link to="/user/manage-business" class="btn btn-secondary mr-2">
           <i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard
         </router-link>
         <button class="btn btn-primary" @click="openModal()">
           <i class="fa-solid fa-plus mr-1"></i> Add New Product
         </button>
      </div>
    </div>

    <!-- Stats Bar -->
    <div class="stats-row mb-5">
      <div class="stat-card">
        <div class="stat-icon bg-light-primary">
          <i class="fa-solid fa-box text-primary"></i>
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ products.length }}</div>
          <div class="stat-label">Total Products</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-light-success">
          <i class="fa-solid fa-check text-success"></i>
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ activeCount }}</div>
          <div class="stat-label">Active Items</div>
        </div>
      </div>
    </div>

    <!-- Product Grid -->
    <div v-if="loading" class="loading-state">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Loading products...</p>
    </div>

    <div v-else-if="products.length === 0" class="empty-state card shadow-sm p-10 text-center">
      <i class="fa-solid fa-boxes-stacked fa-3x text-muted mb-4"></i>
      <h3>No Products Found</h3>
      <p class="text-muted">Start adding products to showcase them in your shop profile.</p>
      <button class="btn btn-primary mt-4" @click="openModal()">Add Your First Product</button>
    </div>

    <div v-else class="product-grid">
      <div v-for="product in products" :key="product.id" class="product-card card shadow-sm">
        <div class="product-image">
           <img :src="product.image || '/images/placeholder-product.png'" :alt="product.name" />
           <div class="product-badge" :class="product.is_active ? 'bg-success' : 'bg-danger'">
             {{ product.is_active ? 'Active' : 'Inactive' }}
           </div>
        </div>
        <div class="product-info p-4">
          <div class="product-category text-xs font-bold text-primary uppercase mb-1">
            {{ product.category?.name || 'General' }}
          </div>
          <h3 class="product-name font-bold text-gray-800 mb-2 truncate" :title="product.name">
            {{ product.name }}
          </h3>
          <div class="product-price flex items-center gap-2 mb-4">
             <span class="price font-black text-lg">SAR {{ product.price }}</span>
             <span v-if="product.compare_price" class="compare-price text-gray-400 line-through text-sm">
               SAR {{ product.compare_price }}
             </span>
          </div>
          <div class="product-actions flex gap-2">
            <button class="btn btn-light-primary btn-sm flex-grow" @click="openModal(product)">
              <i class="fa-solid fa-pen mr-1"></i> Edit
            </button>
            <button class="btn btn-light-danger btn-sm" @click="confirmDelete(product.id)">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <BaseModal :show="showModal" :title="editMode ? 'Edit Product' : 'Add New Product'" size="xl" @close="closeModal">
      <form @submit.prevent="saveProduct">
        <div class="p-6">
          <div class="grid-form">
            <div class="form-group full-width">
              <label class="form-label">Product Name</label>
              <input v-model="form.name" type="text" class="form-control" placeholder="e.g. iPhone 15 Pro Max" required />
            </div>

            <div class="form-group full-width">
              <label class="form-label">Category</label>
              <select v-model="form.category_id" class="form-control" required>
                <option value="">Select Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div class="form-group full-width">
              <label class="form-label">Description</label>
              <textarea v-model="form.description" class="form-control" rows="3" placeholder="Tell customers about this product..."></textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Price (SAR)</label>
              <input v-model="form.price" type="number" step="0.01" class="form-control" placeholder="0.00" required />
            </div>

            <div class="form-group">
              <label class="form-label">Compare Price (Optional)</label>
              <input v-model="form.compare_price" type="number" step="0.01" class="form-control" placeholder="Discount price" />
            </div>

            <div class="form-group full-width">
              <div class="flex items-center gap-2">
                <input v-model="form.is_active" type="checkbox" id="is_active" class="form-checkbox" />
                <label for="is_active" class="mb-0 font-bold cursor-pointer">Active and Visible in Shop</label>
              </div>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <button class="btn btn-secondary mr-2" @click="closeModal">Cancel</button>
        <button class="btn btn-primary" :loading="saving" @click="saveProduct">
          {{ editMode ? 'Update Product' : 'Save Product' }}
        </button>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import BaseModal from '../../Components/BaseModal.vue';
import swal from '../../utils/swal';

const loading = ref(true);
const saving = ref(false);
const showModal = ref(false);
const editMode = ref(false);
const products = ref([]);
const categories = ref([]);
const shopId = ref(null);

const form = reactive({
  id: null,
  name: '',
  category_id: '',
  description: '',
  price: '',
  compare_price: '',
  is_active: true
});

const activeCount = computed(() => products.value.filter(p => p.is_active).length);

const fetchData = async () => {
  loading.value = true;
  try {
    // 1. Get current business to get shop_id
    const businessResp = await axios.get('/api/v1/my-business');
    if (businessResp.data.success) {
      shopId.value = businessResp.data.data.shop?.id;
      
      if (shopId.value) {
        // 2. Get products for this shop
        const productsResp = await axios.get(`/api/v1/shop-products?shop_id=${shopId.value}`);
        if (productsResp.data.success) {
          products.value = productsResp.data.data.data || productsResp.data.data;
        }

        // 3. Get product categories (could be public)
        const catsResp = await axios.get('/api/v1/shop-product-categories');
        if (catsResp.data.success) {
          categories.value = catsResp.data.data;
        }
      }
    }
  } catch (error) {
    console.error('Failed to fetch data', error);
  } finally {
    loading.value = false;
  }
};

const openModal = (product = null) => {
  if (product) {
    editMode.value = true;
    Object.assign(form, {
      id: product.id,
      name: product.name,
      category_id: product.category_id,
      description: product.description,
      price: product.price,
      compare_price: product.compare_price,
      is_active: !!product.is_active
    });
  } else {
    editMode.value = false;
    resetForm();
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  Object.assign(form, {
    id: null,
    name: '',
    category_id: '',
    description: '',
    price: '',
    compare_price: '',
    is_active: true
  });
};

const saveProduct = async () => {
  if (!shopId.value) return;
  
  saving.value = true;
  try {
    const data = { ...form, shop_id: shopId.value };
    let response;
    
    if (editMode.value) {
      response = await axios.put(`/api/v1/shop-products/${form.id}`, data);
    } else {
      response = await axios.post('/api/v1/shop-products', data);
    }

    if (response.data.success) {
      swal.toast(`Product ${editMode.value ? 'updated' : 'added'} successfully`);
      closeModal();
      fetchData();
    }
  } catch (error) {
    swal.toast(error.response?.data?.message || 'Failed to save product', 'error');
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (id) => {
  const result = await swal.fire({
    title: 'Are you sure?',
    text: "This product will be removed from your shop permanently!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete it!',
    confirmButtonColor: '#f64e60'
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`/api/v1/shop-products/${id}`);
      if (response.data.success) {
        swal.toast('Product deleted');
        fetchData();
      }
    } catch (error) {
      swal.toast('Failed to delete product', 'error');
    }
  }
};

onMounted(fetchData);
</script>

<style scoped>
.manage-products { padding: 20px 0; }
.dashboard-header { display: flex; justify-content: space-between; align-items: center; }

.stats-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.stat-card { background: white; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; border: 1px solid #eff2f5; }
.stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }

.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
.product-card { border-radius: 12px; overflow: hidden; height: 100%; transition: transform 0.2s; background: white; }
.product-card:hover { transform: translateY(-5px); }

.product-image { height: 200px; position: relative; background: #f8f9fa; }
.product-image img { width: 100%; height: 100%; object-fit: cover; }
.product-badge { position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; color: white; font-size: 11px; font-weight: 700; text-transform: uppercase; }

.price { color: #3699ff; }
.bg-light-primary { background: #e1f0ff; }
.bg-light-success { background: #c9f7f5; }

.loading-state { text-align: center; padding: 100px 0; }

/* Form Styles */
.p-6 { padding: 30px; }
.grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.full-width { grid-column: span 2; }
.form-group { margin-bottom: 15px; }
.form-label { display: block; font-weight: 700; color: #3f4254; margin-bottom: 8px; font-size: 0.9rem; }
.form-control { width: 100%; padding: 12px 15px; border: 1px solid #e1e3ea; border-radius: 8px; font-size: 1rem; color: #3f4254; }

.btn { padding: 10px 20px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-sm { padding: 8px 15px; font-size: 0.85rem; }
.btn-primary { background: #3699ff; color: white; }
.btn-secondary { background: #f3f6f9; color: #7e8299; }
.btn-light-primary { background: #e1f0ff; color: #3699ff; }
.btn-light-danger { background: #fff5f8; color: #f64e60; }
.btn-light-danger:hover { background: #f64e60; color: white; }

.form-checkbox { width: 1.25rem; height: 1.25rem; }
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

@media (max-width: 768px) {
  .dashboard-header { flex-direction: column; align-items: flex-start; gap: 20px; }
  .stats-row { grid-template-columns: 1fr; }
  .grid-form { grid-template-columns: 1fr; }
  .full-width { grid-column: span 1; }
}
</style>
