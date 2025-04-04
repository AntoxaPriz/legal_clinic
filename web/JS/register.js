// web/js/register.js
import { api } from './core.js';

export async function handleRegister(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Регистрация...';

        const data = await api.request('register.php', 'POST', {
            username: form.username.value.trim(),
            password: form.password.value
        });

        if (data.success) {
            alert(data.message || 'Регистрация успешна! Теперь вы можете войти.');
            window.location.href = 'login.html';
        }
    } catch (error) {
        form.password.value = '';
        console.error('Register error:', error);
        alert(error.message || 'Ошибка регистрации');
    } finally {
        button.disabled = false;
        button.textContent = 'Зарегистрироваться';
    }
}