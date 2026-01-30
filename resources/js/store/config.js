import { defineStore } from 'pinia';
import AppService from '../services/AppService';

export const useConfigStore = defineStore('config', {
    state: () => ({
        settings: {
            brand_name: 'Business Point',
            site_logo: '',
            site_favicon: '',
            brand_tagline: '',
            about_company: '',
            app_email: '',
            app_phone: '',
            maintenance_mode: false,
            maintenance_note: '',
            allow_registration: true
        },
        loading: false,
        initialized: false
    }),

    actions: {
        async fetchSettings(force = false) {
            if (this.initialized && !force) return;

            this.loading = true;
            try {
                const response = await AppService.getSettings();
                if (response.success) {
                    this.settings = { ...this.settings, ...response.data };
                    this.initialized = true;
                    this.updateFavicon();
                }
            } catch (error) {
                console.error('ConfigStore.fetchSettings failed:', error);
            } finally {
                this.loading = false;
            }
        },

        updateFavicon() {
            if (this.settings.site_favicon) {
                const link = document.querySelector("link[rel~='icon']");
                if (link) {
                    link.href = this.settings.site_favicon;
                } else {
                    const newLink = document.createElement('link');
                    newLink.rel = 'icon';
                    newLink.href = this.settings.site_favicon;
                    document.head.appendChild(newLink);
                }
            }
        },

        // Check if the application is in maintenance mode
        isInMaintenanceMode() {
            return this.settings.maintenance_mode === true || this.settings.maintenance_mode === '1';
        },

        // Check if self registration is allowed
        isRegistrationAllowed() {
            return this.settings.allow_registration === true || this.settings.allow_registration === '1';
        }
    }
});
