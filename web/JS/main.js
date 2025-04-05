// web/js/main.js
import { initNav } from './nav.js';
import { logout } from './profile.js';

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Legal Clinic CRM frontend loaded');

    let role = localStorage.getItem('userRole') || 'user';
    if (!localStorage.getItem('userRole')) {
        try {
            const profileData = await (await fetch('api/profile.php', {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })).json();
            role = profileData.role || 'user';
            localStorage.setItem('userRole', role);
        } catch (error) {
            console.error('Failed to fetch role:', error);
        }
    }
    initNav(role);
});