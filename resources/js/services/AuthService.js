import axios from 'axios';

/**
 * AuthService handles login, logout and authentication checks.
 */
const AuthService = {
    /**
     * Login user/admin
     */
    async login(credentials, scope = 'user') {
        try {
            const url = scope === 'admin' ? '/admin/login' : '/api/v1/auth/login';
            const response = await axios.post(url, credentials);
            return response.data;
        } catch (error) {
            console.error('AuthService.login failed:', error);
            throw error;
        }
    },

    /**
     * Logout user/admin
     */
    async logout(scope = 'user') {
        try {
            const url = scope === 'admin' ? '/admin/logout' : '/api/v1/auth/logout';
            const response = await axios.post(url);
            return response.data;
        } catch (error) {
            console.error('AuthService.logout failed:', error);
            throw error;
        }
    },

    /**
     * Fetch current user profile
     */
    async fetchUser() {
        try {
            const response = await axios.get('/api/v1/auth/user');
            return response.data;
        } catch (error) {
            console.error('AuthService.fetchUser failed:', error);
            throw error;
        }
    }
};

export default AuthService;
