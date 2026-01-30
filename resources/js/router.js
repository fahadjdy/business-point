import { createRouter, createWebHistory } from 'vue-router';
import { useConfigStore } from './store/config';

// Lazy load components
const Home = () => import('./Pages/Home.vue');
const Maintenance = () => import('./Pages/Maintenance.vue');
const UserDashboard = () => import('./Pages/User/Dashboard.vue');
const UserSetting = () => import('./Pages/User/Setting.vue');
const Dashboard = () => import('./Pages/Dashboard.vue');
const VendorsIndex = () => import('./Pages/Vendors/Index.vue');
const VendorsShow = () => import('./Pages/Vendors/Show.vue');
const AuditLogs = () => import('./Pages/AuditLogs/Index.vue');
const Settings = () => import('./Pages/Settings.vue');
const Login = () => import('./Pages/Auth/Login.vue');
const UserLogin = () => import('./Pages/Auth/UserLogin.vue');
const Profile = () => import('./Pages/Auth/Profile.vue');
const NotificationsIndex = () => import('./Pages/Notifications/Index.vue');
const AnnouncementsIndex = () => import('./Pages/Announcements/Index.vue');
const AnnouncementsPreview = () => import('./Pages/Announcements/Preview.vue');
const CommunityDirectoryIndex = () => import('./Pages/CommunityDirectory/Index.vue');
const CommunityDirectoryShow = () => import('./Pages/CommunityDirectory/Show.vue');
const Register = () => import('./Pages/Auth/Register.vue');
const VendorRegister = () => import('./Pages/Vendors/Register.vue');
const EmergencyContactsIndex = () => import('./Pages/EmergencyContacts/Index.vue');
const ContactBookAdminIndex = () => import('./Pages/ContactBook/AdminIndex.vue');

const routes = [
    {
        path: '/maintenance',
        name: 'maintenance',
        component: Maintenance,
        meta: { layout: 'Blank' }
    },
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: { layout: 'User' }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { layout: 'Guest', guest: true, scope: 'user' }
    },
    {
        path: '/login',
        name: 'user.login',
        component: UserLogin,
        meta: { layout: 'Guest', guest: true, scope: 'user' }
    },
    {
        path: '/user',
        redirect: '/',
        meta: { requiresAuth: true, scope: 'user' }
    },
    {
        path: '/user/profile',
        name: 'user.profile',
        component: Profile,
        meta: { requiresAuth: true, title: 'My Profile', layout: 'User', scope: 'user' }
    },
    {
        path: '/user/setting',
        name: 'user.setting',
        component: UserSetting,
        meta: { requiresAuth: true, title: 'Settings', layout: 'User', scope: 'user' }
    },
    {
        path: '/announcements',
        name: 'announcements.index',
        component: AnnouncementsIndex,
        meta: { layout: 'User' }
    },
    {
        path: '/announcements/:id',
        name: 'announcements.preview',
        component: AnnouncementsPreview,
        meta: { layout: 'User' }
    },
    {
        path: '/community-directory',
        name: 'community-directory.index',
        component: CommunityDirectoryIndex,
        meta: { layout: 'User' }
    },
    {
        path: '/community-directory/:id',
        name: 'community-directory.show',
        component: CommunityDirectoryShow,
        meta: { layout: 'User' }
    },
    {
        path: '/register-business',
        name: 'business.register',
        component: VendorRegister,
        meta: { requiresAuth: true, layout: 'User' }
    },
    {
        path: '/admin/login',
        name: 'admin.login',
        component: Login,
        meta: { layout: 'Blank', guest: true, scope: 'admin' }
    },
    {
        path: '/admin',
        redirect: '/admin/dashboard',
        meta: { requiresAuth: true, scope: 'admin' }
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        meta: { requiresAuth: true, title: 'Dashboard', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/vendors',
        name: 'vendors.index',
        component: VendorsIndex,
        meta: { requiresAuth: true, title: 'Vendors', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/vendors/:id',
        name: 'vendors.show',
        component: VendorsShow,
        meta: { requiresAuth: true, title: 'Vendor Details', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/audit-logs',
        name: 'audit-logs',
        component: AuditLogs,
        meta: { requiresAuth: true, title: 'Audit Logs', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/settings',
        name: 'settings',
        component: Settings,
        meta: { requiresAuth: true, title: 'Settings', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: Profile,
        meta: { requiresAuth: true, title: 'Edit Profile', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/notifications',
        name: 'admin.notifications',
        component: NotificationsIndex,
        meta: { requiresAuth: true, title: 'Announcements', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/emergency-contacts',
        name: 'admin.emergency-contacts',
        component: EmergencyContactsIndex,
        meta: { requiresAuth: true, title: 'Emergency Contacts', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/community-directory',
        name: 'admin.community-directory',
        component: ContactBookAdminIndex,
        meta: { requiresAuth: true, title: 'Community Directory', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/contact-logs',
        name: 'admin.contact-logs',
        component: () => import('./Pages/ContactBook/AuditLogs.vue'),
        meta: { requiresAuth: true, title: 'Contact Logs', layout: 'Admin', scope: 'admin' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation Guard
router.beforeEach(async (to, from, next) => {
    const configStore = useConfigStore();
    
    // Fetch settings if not initialized (except for maintenance page)
    if (!configStore.initialized && to.name !== 'maintenance') {
        await configStore.fetchSettings();
    }
    
    // Check maintenance mode (except for admin routes and maintenance page itself)
    const isAdminPath = to.path.startsWith('/admin');
    const isMaintenancePage = to.name === 'maintenance';
    
    if (!isAdminPath && !isMaintenancePage && configStore.isInMaintenanceMode()) {
        next({ name: 'maintenance' });
        return;
    }
    
    // If on maintenance page but maintenance mode is disabled, redirect to home
    if (isMaintenancePage && !configStore.isInMaintenanceMode()) {
        next({ name: 'home' });
        return;
    }
    
    const scope = to.meta.scope || (isAdminPath ? 'admin' : 'user');
    const isAdmin = scope === 'admin';

    const isLoggedIn = isAdmin
        ? !!localStorage.getItem('admin')
        : !!localStorage.getItem('user_token');

    if (to.meta.requiresAuth && !isLoggedIn) {
        next({ name: isAdmin ? 'admin.login' : 'user.login' });
    } else if (to.meta.guest && isLoggedIn) {
        next({ name: isAdmin ? 'admin.dashboard' : 'home' });
    } else {
        next();
    }
});

export default router;
