// web/js/admin.js
import { api } from './core.js';

export async function loadUsers() {
    try {
        const data = await api.cachedRequest('admin.php?users=1', 'GET', null, 'usersCache');
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

function validatePassword(password) {
    const minLength = 8;
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    return password.length >= minLength && hasLetter && hasNumber;
}

export async function addUser(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');
    const password = form.password.value.trim();

    if (!validatePassword(password)) {
        alert('Пароль должен быть минимум 8 символов, содержать буквы и цифры');
        return;
    }

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';

        const data = await api.request('admin.php', 'POST', {
            username: form.username.value.trim(),
            password: password,
            role: form.role.value
        });

        if (data.success) {
            form.reset();
            localStorage.removeItem('usersCache'); // Очистка кэша
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
    const password = form.password.value.trim();

    if (password && !validatePassword(password)) {
        alert('Новый пароль должен быть минимум 8 символов, содержать буквы и цифры');
        return;
    }

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request(`admin.php?id=${form.id.value}`, 'PUT', {
            username: form.username.value.trim(),
            password: password || null,
            role: form.role.value
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            modal.hide();
            localStorage.removeItem('usersCache'); // Очистка кэша
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
            localStorage.removeItem('usersCache'); // Очистка кэша
            await loadUsers();
            alert(data.message || 'Пользователь успешно удалён');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        alert(error.message || 'Ошибка удаления пользователя');
    }
}