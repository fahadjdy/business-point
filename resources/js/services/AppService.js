import axios from 'axios';

/**
 * AppService handles general application-wide data fetching (settings, branding, etc.)
 */
const AppService = {
    /**
     * Fetch public/application settings (branding, logos, etc.)
     */
    async getSettings() {
        try {
            const response = await axios.get('/api/v1/company-details');
            return response.data;
        } catch (error) {
            console.error('AppService.getSettings failed:', error);
            throw error;
        }
    },

    /**
     * Fetch common tags/skills
     */
    async getTags(category = '') {
        try {
            const url = category ? `/api/v1/tags?category=${category}` : '/api/v1/tags';
            const response = await axios.get(url);
            return response.data;
        } catch (error) {
            console.error('AppService.getTags failed:', error);
            throw error;
        }
    }
};

export default AppService;
