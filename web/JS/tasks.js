// web/js/tasks.js
import { api } from './core.js';

export async function loadTasks() {
    try {
        const data = await api.request('tasks.php');
        const table = document.getElementById('tasksList');
        table.innerHTML = ''; // Очистка таблицы перед загрузкой
        data.forEach(task => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${task.id}</td><td>${task.description}</td><td>${task.status}</td><td>${task.responsible || '-'}</td>`;
            table.appendChild(row);
        });
    } catch (error) {
        console.error('Tasks error:', error);
        alert('Ошибка загрузки задач');
    }
}