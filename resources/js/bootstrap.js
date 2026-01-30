import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Axios interceptor to ensure headers are sent with every request
 */
window.axios.interceptors.request.use(config => {
    // Add API Key for all v1 API requests
    if (config.url.includes('/api/v1/')) {
        config.headers['X-Api-Key'] = import.meta.env.VITE_AUTH_API_KEY;

        // Add Bearer token for User routes if available
        const token = localStorage.getItem('user_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
    }

    // Ensure withCredentials is true for session-based routes (Admin)
    if (config.url.includes('/admin/')) {
        config.withCredentials = true;
    }

    return config;
}, error => {
    return Promise.reject(error);
});

