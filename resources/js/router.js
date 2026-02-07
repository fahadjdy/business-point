import { createRouter, createWebHistory } from 'vue-router';
import { useConfigStore } from './store/config';

// Lazy load components
const Home = () => import('./Pages/Home.vue');
const Maintenance = () => import('./Pages/Maintenance.vue');
const UserDashboard = () => import('./Pages/User/Dashboard.vue');
const UserSetting = () => import('./Pages/User/Setting.vue');
const Dashboard = () => import('./Pages/Admin/Dashboard/Index.vue');
const VendorsIndex = () => import('./Pages/Admin/Vendors/Index.vue');
const VendorsShow = () => import('./Pages/Admin/Vendors/Show.vue');
const AuditLogs = () => import('./Pages/Admin/AuditLogs/Index.vue');
const Settings = () => import('./Pages/Admin/Settings/Index.vue');
const Login = () => import('./Pages/Auth/Login.vue');
const UserLogin = () => import('./Pages/Auth/UserLogin.vue');
const Profile = () => import('./Pages/Admin/Profile/Index.vue');
const NotificationsIndex = () => import('./Pages/Admin/Notifications/Index.vue');
const AdminAnnouncementsIndex = () => import('./Pages/Admin/Announcements/Index.vue');
const FrontendAnnouncementsIndex = () => import('./Pages/Announcements/Index.vue');
const AnnouncementsPreview = () => import('./Pages/Announcements/Preview.vue');
const CommunityDirectoryIndex = () => import('./Pages/CommunityDirectory/Index.vue');
const CommunityDirectoryShow = () => import('./Pages/CommunityDirectory/Show.vue');
const Register = () => import('./Pages/Auth/Register.vue');
const VendorRegister = () => import('./Pages/User/VendorRegister.vue');
const EmergencyContactsIndex = () => import('./Pages/Admin/EmergencyContacts/Index.vue');
const AdminBannersIndex = () => import('./Pages/Admin/Banners/Index.vue');
const AdminBannersPreview = () => import('./Pages/Admin/Banners/Preview.vue');
const ContactBookAdminIndex = () => import('./Pages/Admin/CommunityDirectory/Index.vue');
const CommunityDirectoryPreview = () => import('./Pages/Admin/CommunityDirectory/Preview.vue');
const AnnouncementPreview = () => import('./Pages/Admin/Announcements/Preview.vue');

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
        path: '/user/manage-business',
        name: 'user.manage-business',
        component: () => import('./Pages/User/ManageBusiness.vue'),
        meta: { requiresAuth: true, title: 'Manage Business', layout: 'User', scope: 'user' }
    },
    {
        path: '/user/manage-products',
        name: 'user.manage-products',
        component: () => import('./Pages/User/ManageProducts.vue'),
        meta: { requiresAuth: true, title: 'Manage Products', layout: 'User', scope: 'user' }
    },
    {
        path: '/announcements',
        name: 'announcements.index',
        component: FrontendAnnouncementsIndex,
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
        path: '/register-business/success',
        name: 'business.register.success',
        component: () => import('./Pages/User/VendorRegisterSuccess.vue'),
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
        component: AdminAnnouncementsIndex,
        meta: { requiresAuth: true, title: 'Announcements', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/emergency-contacts',
        name: 'admin.emergency-contacts',
        component: EmergencyContactsIndex,
        meta: { requiresAuth: true, title: 'Emergency Contacts', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/banners',
        name: 'admin.banners',
        component: AdminBannersIndex,
        meta: { requiresAuth: true, title: 'Banners', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/banners/:id/preview',
        name: 'admin.banners.preview',
        component: AdminBannersPreview,
        meta: { requiresAuth: true, title: 'Banner Preview', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/community-directory',
        name: 'admin.community-directory',
        component: ContactBookAdminIndex,
        meta: { requiresAuth: true, title: 'Community Directory', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/community-directory/:id/preview',
        name: 'admin.community-directory.preview',
        component: CommunityDirectoryPreview,
        meta: { requiresAuth: true, title: 'Contact Preview', layout: 'Admin', scope: 'admin' }
    },
    {
        path: '/admin/notifications/:id/preview',
        name: 'admin.announcement.preview',
        component: AnnouncementPreview,
        meta: { requiresAuth: true, title: 'Announcement Preview', layout: 'Admin', scope: 'admin' }
    },
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
