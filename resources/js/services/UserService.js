import axios from 'axios';

/**
 * UserService handles user-specific profile and authentication data.
 */
const UserService = {
    /**
     * Update user profile (multi-part form data)
     */
    async updateProfile(formData) {
        try {
            const response = await axios.post('/api/v1/profile', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return response.data;
        } catch (error) {
            console.error('UserService.updateProfile failed:', error);
            throw error;
        }
    },

    /**
     * Get user profile details
     */
    async getProfile() {
        try {
            const response = await axios.get('/api/v1/profile');
            return response.data;
        } catch (error) {
            console.error('UserService.getProfile failed:', error);
            throw error;
        }
    }
};

export default UserService;
