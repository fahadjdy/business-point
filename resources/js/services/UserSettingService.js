import axios from 'axios';

/**
 * UserSettingService handles user-specific preference/settings management.
 */
const UserSettingService = {
    /**
     * Fetch user-specific settings
     */
    async getSettings() {
        try {
            const response = await axios.get('/api/v1/user/settings');
            return response.data;
        } catch (error) {
            console.error('UserSettingService.getSettings failed:', error);
            throw error;
        }
    },

    /**
     * Update user-specific settings
     */
    async updateSettings(settings) {
        try {
            const response = await axios.post('/api/v1/user/settings', { settings });
            return response.data;
        } catch (error) {
            console.error('UserSettingService.updateSettings failed:', error);
            throw error;
        }
    }
};

export default UserSettingService;
