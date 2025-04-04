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

        const form = document.getElementById('updateProfileForm');
        if (form) {
            form.username.value = data.username;
            form.password.value = ''; // Пароль остаётся пустым для безопасности
        }
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
            password: form.password.value.trim() || null
        });

        if (data.success) {
            await loadProfile();
            form.password.value = '';
            alert(data.message || 'Профиль успешно обновлён');
        }
    } catch (error) {
        console.error('Profile update error:', error);
        alert(error.message || 'Ошибка обновления профиля');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}