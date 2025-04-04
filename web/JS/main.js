// web/js/main.js
import { handleLogin, handleRegister } from './auth.js';
import { loadDocuments } from './documents.js';
import { loadTasks } from './tasks.js';
import { loadClients, addClient, editClient } from './clients.js'; // Обновлено: добавлен editClient

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

    const editClientForm = document.getElementById('editClientForm'); // Добавлено
    if (editClientForm) editClientForm.addEventListener('submit', editClient);

    const tasksList = document.getElementById('tasksList');
    if (tasksList) loadTasks();
});