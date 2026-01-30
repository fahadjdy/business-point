import { reactive, readonly } from 'vue';

const state = reactive({
    notifications: []
});

const addNotification = (message, type = 'success', duration = 5000) => {
    const id = Date.now();
    state.notifications.push({ id, message, type });

    if (duration > 0) {
        setTimeout(() => {
            removeNotification(id);
        }, duration);
    }
};

const removeNotification = (id) => {
    const index = state.notifications.findIndex(n => n.id === id);
    if (index !== -1) {
        state.notifications.splice(index, 1);
    }
};

export const useNotificationStore = () => {
    return {
        state: readonly(state),
        addNotification,
        removeNotification
    };
};
