import './bootstrap';

// import { supabase, getProducts } from './supabase';

// Helper function to open auth modal
window.openAuthModal = function(tab = 'login') {
    window.dispatchEvent(new CustomEvent('openAuthModal', { detail: { tab } }));
};

window.openLoginModal = function() {
    window.openAuthModal('login');
};

window.openRegisterModal = function() {
    window.openAuthModal('register');
};
