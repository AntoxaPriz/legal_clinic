// web/js/auth.js
import { api } from './core.js';

export async function handleLogin(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Вход...';

        const data = await api.request('auth.php', 'POST', {
            username: form.username.value.trim(),
            password: form.password.value
        });

        if (data.success) {
            localStorage.setItem('token', data.token);
            window.location.href = data.redirect || 'index.html';
        }
    } catch (error) {
        form.password.value = '';
        console.error('Login error:', error);
        alert(error.message || 'Ошибка авторизации');
    } finally {
        button.disabled = false;
        button.textContent = 'Войти';
    }
}

export { handleRegister } from './register.js';