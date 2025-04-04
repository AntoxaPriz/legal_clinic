// web/js/main.js
import { handleLogin, handleRegister } from './auth.js';
import { loadDocuments } from './documents.js';
import { loadTasks } from './tasks.js';
import { loadClients, addClient, editClient } from './clients.js';
import { loadProfile, updateProfile, logout } from './profile.js'; // Обновлено

document.addEventListener('DOMContentLoaded', () => {
    console.log('Legal Clinic CRM frontend loaded');

    const loginForm = document.getElementById('loginForm');
    if (loginForm) loginForm.addEventListener('submit', handleLogin);

    const registerForm = document.getElementById('registerForm');
    if (registerForm) registerForm.addEventListener('submit', handleRegister);

    const documentsList = document.getElementById('documentsList');
    if (documentsList) loadDocuments();

    const clientsList = document.getElementById('clientsList');
    if (clientsList) loadClients();

    const addClientForm = document.getElementById('addClientForm');
    if (addClientForm) addClientForm.addEventListener('submit', addClient);

    const editClientForm = document.getElementById('editClientForm');
    if (editClientForm) editClientForm.addEventListener('submit', editClient);

    const profileInfo = document.getElementById('profileInfo');
    if (profileInfo) loadProfile();
    const updateProfileForm = document.getElementById('updateProfileForm');
    if (updateProfileForm) updateProfileForm.addEventListener('submit', updateProfile);
    const logoutButton = document.getElementById('logoutButton'); // Добавлено
    if (logoutButton) logoutButton.addEventListener('click', logout);

    const tasksList = document.getElementById('tasksList');
    if (tasksList) loadTasks();
});