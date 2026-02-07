<template>
  <div class="success-page-wrapper">
    <div class="success-card">
      <div class="icon-wrapper">
        <i class="fa-solid fa-check-circle"></i>
      </div>
      
      <h1 class="success-title">Application Submitted!</h1>
      <p class="success-subtitle">
        Thank you for registering your business with Business Point.
      </p>
      
      <div class="status-box">
        <div class="status-item">
          <span class="label">Status:</span>
          <span class="value badge-warning">Pending Approval</span>
        </div>
        <div class="status-item">
            <span class="label">Reference ID:</span>
            <span class="value">#{{ $route.query.id || 'PENDING' }}</span>
        </div>
      </div>

      <div class="info-text">
        <p>Our team will review your details shortly. You will be notified once your business is active.</p>
        <p>In the meantime, you can explore the community directory.</p>
      </div>

      <div class="actions">
        <router-link to="/" class="btn btn-primary d-block mb-3">
          <i class="fa-solid fa-home mr-2"></i> Go to Homepage
        </router-link>
        <router-link to="/announcements" class="btn btn-light d-block">
          <i class="fa-solid fa-bullhorn mr-2"></i> View Announcements
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.success-page-wrapper {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f8fa;
  padding: 20px;
}

.success-card {
  background: white;
  max-width: 500px;
  width: 100%;
  padding: 40px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.icon-wrapper {
  font-size: 4rem;
  color: #1bc5bd;
  margin-bottom: 20px;
}

.success-title {
  font-size: 1.8rem;
  font-weight: 800;
  color: #181c32;
  margin-bottom: 10px;
}

.success-subtitle {
  color: #7e8299;
  font-size: 1.1rem;
  margin-bottom: 30px;
}

.status-box {
  background: #fff4de;
  border: 1px dashed #ffa800;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 30px;
}

.status-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 1rem;
}

.status-item:last-child { margin-bottom: 0; }

.label { color: #7e8299; font-weight: 500; }
.value { font-weight: 700; color: #181c32; }

.badge-warning {
  color: #ffa800;
  background: rgba(255, 168, 0, 0.1);
  padding: 2px 8px;
  border-radius: 4px;
}

.info-text p {
  color: #b5b5c3;
  margin-bottom: 10px;
  font-size: 0.95rem;
}

.actions { margin-top: 30px; }

.btn {
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
  width: 100%;
}

.btn-primary { background: #3699ff; color: white; }
.btn-primary:hover { background: #187de4; }

.btn-light { background: #f3f6f9; color: #7e8299; }
.btn-light:hover { background: #e4e6ef; color: #3699ff; }

.d-block { display: block; }
.mb-3 { margin-bottom: 15px; }
.mr-2 { margin-right: 8px; }
</style>
