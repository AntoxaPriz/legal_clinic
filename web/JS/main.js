// web/js/main.js
import { handleLogin, handleRegister } from './auth.js';
import { loadDocuments } from './documents.js';
import { loadTasks, addTask, editTask } from './tasks.js';
import { loadClients, addClient, editClient } from './clients.js';
import { loadProfile, updateProfile, logout } from './profile.js';
import { loadUsers, addUser, editUser, deleteUser } from './admin.js';
import { initNav } from './nav.js';

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Legal Clinic CRM frontend loaded');

    // Проверяем роль в localStorage, если нет — загружаем из профиля
    let role = localStorage.getItem('userRole') || 'user';
    if (!localStorage.getItem('userRole')) {
        try {
            const profileData = await (await fetch('api/profile.php', {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })).json();
            role = profileData.role || 'user';
            localStorage.setItem('userRole', role); // Сохраняем роль в localStorage
        } catch (error) {
            console.error('Failed to fetch role:', error);
        }
    }
    initNav(role); // Инициализация навигации с ролью

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
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) logoutButton.addEventListener('click', logout);

    const tasksList = document.getElementById('tasksList');
    if (tasksList) loadTasks();
    const addTaskForm = document.getElementById('addTaskForm');
    if (addTaskForm) addTaskForm.addEventListener('submit', addTask);
    const editTaskForm = document.getElementById('editTaskForm');
    if (editTaskForm) editTaskForm.addEventListener('submit', editTask);

    const usersList = document.getElementById('usersList');
    if (usersList) loadUsers();
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) addUserForm.addEventListener('submit', addUser);
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) editUserForm.addEventListener('submit', editUser);
});