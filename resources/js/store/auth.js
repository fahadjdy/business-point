import { reactive, readonly } from 'vue';
import { useNotificationStore } from './notification';
import AuthService from '../services/AuthService';
import UserService from '../services/UserService';
import swal from '../utils/swal';

const state = reactive({
    user: JSON.parse(localStorage.getItem('user')) || null,
    admin: JSON.parse(localStorage.getItem('admin')) || null,
    user_token: localStorage.getItem('user_token') || null,
    admin_token: localStorage.getItem('admin_token') || null,
    loading: false,
    error: null
});

const setUser = (user, scope = 'user') => {
    if (scope === 'admin') {
        state.admin = user;
        localStorage.setItem('admin', JSON.stringify(user));
    } else {
        state.user = user;
        localStorage.setItem('user', JSON.stringify(user));
    }
};

const setToken = (token, scope = 'user') => {
    if (scope === 'admin') {
        state.admin_token = null; // We use sessions for admin now
        localStorage.removeItem('admin_token');
    } else {
        state.user_token = token;
        if (token) localStorage.setItem('user_token', token);
        else localStorage.removeItem('user_token');
    }
};

const login = async (credentials, scope = 'admin') => {
    state.loading = true;
    state.error = null;
    try {
        const data = await AuthService.login(credentials, scope);

        if (data.success) {
            const { user, access_token, token } = data.data;
            const authToken = access_token || token;

            if (scope === 'user') setToken(authToken, scope);
            setUser(user, scope);
            swal.toast(`Welcome back, ${user.name}!`);
            return true;
        }
    } catch (err) {
        state.error = err.response?.data?.message || 'Login failed';
        swal.toast(state.error, 'error');
        return false;
    } finally {
        state.loading = false;
    }
};

const logout = async (scope = 'admin') => {
    try {
        await AuthService.logout(scope);
    } catch (e) { }

    setToken(null, scope);
    setUser(null, scope);
    if (scope === 'admin') localStorage.removeItem('admin');
    else localStorage.removeItem('user');
};

const updateProfile = async (formData, scope = 'admin') => {
    state.loading = true;
    try {
        const data = await UserService.updateProfile(formData);

        if (data.success) {
            setUser(data.data, scope);
            swal.toast('Profile updated successfully');
            return true;
        }
    } catch (err) {
        swal.toast(err.response?.data?.message || 'Update failed', 'error');
        return false;
    } finally {
        state.loading = false;
    }
};

const fetchProfile = async () => {
    try {
        const data = await AuthService.fetchUser();
        if (data.success) {
            setUser(data.data, 'user');
            return true;
        }
    } catch (err) {
        console.error('Failed to fetch user profile:', err);
        return false;
    }
};

export const useAuthStore = () => {
    return {
        state: readonly(state),
        setUser,
        setToken,
        login,
        logout,
        updateProfile,
        fetchProfile
    };
};
