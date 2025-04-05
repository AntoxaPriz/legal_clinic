// web/js/settings.js
import { api } from './core.js';

export async function loadSettings() {
    try {
        const data = await api.cachedRequest('settings.php', 'GET', null, 'settingsCache');
        const form = document.getElementById('settingsForm');
        if (!form) return;

        form.theme.value = data.theme || 'light';
        document.body.className = data.theme === 'dark' ? 'bg-dark text-white' : 'bg-light text-dark';
    } catch (error) {
        console.error('Settings load error:', error);
        alert('Ошибка загрузки настроек');
    }
}

export async function saveSettings(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request('settings.php', 'POST', {
            theme: form.theme.value
        });

        if (data.success) {
            localStorage.removeItem('settingsCache'); // Очистка кэша после изменения
            await loadSettings();
            alert(data.message || 'Настройки успешно сохранены');
        }
    } catch (error) {
        console.error('Save settings error:', error);
        alert(error.message || 'Ошибка сохранения настроек');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}