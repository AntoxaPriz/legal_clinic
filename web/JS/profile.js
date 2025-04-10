// web/js/profile.js
import { api } from './core.js';

export async function loadProfile() {
    try {
        const data = await api.request('profile.php');
        const profileInfo = document.getElementById('profileInfo');
        if (!profileInfo) return;
        profileInfo.innerHTML = `
      <p><strong>Имя пользователя:</strong> ${data.username}</p>
      <p><strong>Роль:</strong> ${data.role}</p>
    `;
    } catch (error) {
        console.error('Profile load error:', error);
        alert('Ошибка загрузки профиля');
    }
}

export async function updateProfile(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request('profile.php', 'PUT', {
            username: form.username.value.trim(),
            password: form.password.value.trim() || null,
            oldPassword: form.oldPassword.value.trim()
        });

        if (data.success) {
            await loadProfile();
            alert(data.message || 'Профиль успешно обновлён');
            form.password.value = '';
            form.oldPassword.value = '';
        }
    } catch (error) {
        console.error('Update profile error:', error);
        alert(error.message || 'Ошибка обновления профиля');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}

export async function logout() {
    try {
        const data = await api.request('logout.php', 'POST');
        if (data.success) {
            localStorage.removeItem('userRole'); // Очистка роли из localStorage
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        alert('Ошибка при выходе');
    }
}