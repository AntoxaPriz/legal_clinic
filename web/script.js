document.addEventListener('DOMContentLoaded', () => {
    console.log('Legal Clinic CRM frontend loaded');

    // Обработка формы входа
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Получаем элементы формы
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const submitButton = loginForm.querySelector('button[type="submit"]');

            // Проверка наличия элементов
            if (!usernameInput || !passwordInput) {
                console.error('Не найдены поля формы');
                return;
            }

            const username = usernameInput.value.trim();
            const password = passwordInput.value;

            // Валидация
            if (!username || !password) {
                alert('Пожалуйста, заполните все поля');
                return;
            }

            try {
                // Блокируем кнопку во время запроса
                submitButton.disabled = true;
                submitButton.textContent = 'Вход...';

                // Отправка данных
                const response = await fetch('auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        username: username,
                        password: password
                    })
                });

                // Проверка ответа
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Перенаправление после успешного входа
                    window.location.href = data.redirect || 'index.html';
                } else {
                    // Обработка ошибок
                    alert(data.message || 'Ошибка авторизации. Проверьте данные и попробуйте снова.');
                    passwordInput.value = '';
                    passwordInput.focus();
                }
            } catch (error) {
                console.error('Ошибка при авторизации:', error);
                alert('Произошла ошибка при соединении с сервером');
            } finally {
                // Разблокируем кнопку
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Войти';
                }
            }
        });
    }

    // Здесь можно добавить другие обработчики для страницы
});