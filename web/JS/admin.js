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
        <td>
          <button class="btn btn-primary btn-sm edit-user" data-id="${user.id}">Редактировать</button>
          <button class="btn btn-danger btn-sm delete-user" data-id="${user.id}">Удалить</button>
        </td>
      `;
            row.querySelector('.edit-user').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                const form = document.getElementById('editUserForm');
                form.id.value = user.id;
                form.username.value = user.username;
                form.role.value = user.role;
                form.password.value = '';
                modal.show();
            });
            row.querySelector('.delete-user').addEventListener('click', () => deleteUser(user.id));
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Admin users load error:', error);
        alert('Ошибка загрузки пользователей');
    }
}

export async function addUser(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';

        const data = await api.request('admin.php', 'POST', {
            username: form.username.value.trim(),
            password: form.password.value.trim(),
            role: form.role.value
        });

        if (data.success) {
            form.reset();
            await loadUsers();
            alert(data.message || 'Пользователь успешно добавлен');
        }
    } catch (error) {
        console.error('Add user error:', error);
        alert(error.message || 'Ошибка добавления пользователя');
    } finally {
        button.disabled = false;
        button.textContent = 'Добавить пользователя';
    }
}

export async function editUser(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request(`admin.php?id=${form.id.value}`, 'PUT', {
            username: form.username.value.trim(),
            password: form.password.value.trim() || null,
            role: form.role.value
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            modal.hide();
            await loadUsers();
            alert(data.message || 'Пользователь успешно обновлён');
        }
    } catch (error) {
        console.error('Edit user error:', error);
        alert(error.message || 'Ошибка редактирования пользователя');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}

export async function deleteUser(userId) {
    if (!confirm('Вы уверены, что хотите удалить этого пользователя?')) return;

    try {
        const data = await api.request(`admin.php?id=${userId}`, 'DELETE');
        if (data.success) {
            await loadUsers();
            alert(data.message || 'Пользователь успешно удалён');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        alert(error.message || 'Ошибка удаления пользователя');
    }
}