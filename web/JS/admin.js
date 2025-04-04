// web/js/admin.js
import { api } from './core.js';

export async function loadUsers() {
    try {
        const data = await api.request('admin.php?users=1');
        const tableBody = document.querySelector('#usersList tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        data.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${user.id}</td>
        <td>${user.username}</td>
        <td>${user.role}</td>
        <td><!-- Действия добавим позже --></td>
      `;
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Admin users load error:', error);
        alert('Ошибка загрузки пользователей');
    }
}