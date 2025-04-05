// web/js/tasks.js
import { api } from './core.js';

export async function loadTasks() {
    try {
        const data = await api.cachedRequest('tasks.php', 'GET', null, 'tasksCache');
        const tableBody = document.querySelector('#tasksList tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        data.forEach(task => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${task.id}</td>
        <td>${task.title}</td>
        <td>${task.status}</td>
        <td>
          <button class="btn btn-primary btn-sm edit-task" data-id="${task.id}">Редактировать</button>
          <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Удалить</button>
        </td>
      `;
            row.querySelector('.edit-task').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                const form = document.getElementById('editTaskForm');
                form.id.value = task.id;
                form.title.value = task.title;
                form.status.value = task.status;
                modal.show();
            });
            row.querySelector('.delete-task').addEventListener('click', () => deleteTask(task.id));
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Tasks load error:', error);
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
            title: form.title.value.trim(),
            status: form.status.value
        });

        if (data.success) {
            form.reset();
            localStorage.removeItem('tasksCache'); // Очистка кэша
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
            title: form.title.value.trim(),
            status: form.status.value
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
            modal.hide();
            localStorage.removeItem('tasksCache'); // Очистка кэша
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

async function deleteTask(taskId) {
    if (!confirm('Вы уверены, что хотите удалить эту задачу?')) return;

    try {
        const data = await api.request(`tasks.php?id=${taskId}`, 'DELETE');
        if (data.success) {
            localStorage.removeItem('tasksCache'); // Очистка кэша
            await loadTasks();
            alert(data.message || 'Задача успешно удалена');
        }
    } catch (error) {
        console.error('Delete task error:', error);
        alert(error.message || 'Ошибка удаления задачи');
    }
}