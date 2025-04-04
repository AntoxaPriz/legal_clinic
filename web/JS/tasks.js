// web/js/tasks.js
import { api } from './core.js';

export async function loadTasks() {
    try {
        const data = await api.request('tasks.php');
        const table = document.querySelector('#tasksList tbody');
        if (!table) return;
        table.innerHTML = '';
        data.forEach(task => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${task.id}</td>
        <td>${task.description}</td>
        <td>${task.status}</td>
        <td>${task.responsible || '-'}</td>
        <td><button class="btn btn-primary btn-sm edit-task" data-id="${task.id}">Редактировать</button></td>
      `;
            row.querySelector('.edit-task').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                const form = document.getElementById('editTaskForm');
                form.id.value = task.id;
                form.description.value = task.description;
                form.status.value = task.status;
                form.responsible_id.value = task.responsible_id || ''; // Предполагаем, что API вернёт ID
                modal.show();
            });
            table.appendChild(row);
        });
    } catch (error) {
        console.error('Tasks error:', error);
        alert('Ошибка загрузки задач');
    }
}

export async function addTask(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';

        const data = await api.request('tasks.php', 'POST', {
            description: form.description.value.trim(),
            responsible_id: form.responsible_id.value.trim() || null
        });

        if (data.success) {
            form.reset();
            await loadTasks();
            alert(data.message || 'Задача успешно добавлена');
        }
    } catch (error) {
        console.error('Add task error:', error);
        alert(error.message || 'Ошибка добавления задачи');
    } finally {
        button.disabled = false;
        button.textContent = 'Добавить задачу';
    }
}

export async function editTask(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request(`tasks.php?id=${form.id.value}`, 'PUT', {
            description: form.description.value.trim(),
            status: form.status.value,
            responsible_id: form.responsible_id.value.trim() || null
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
            modal.hide();
            await loadTasks();
            alert(data.message || 'Задача успешно обновлена');
        }
    } catch (error) {
        console.error('Edit task error:', error);
        alert(error.message || 'Ошибка редактирования задачи');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}