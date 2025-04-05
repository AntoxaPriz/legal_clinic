// web/js/nav.js
import { logout } from './profile.js';

export function initNav(role) {
    const nav = document.getElementById('mainNav');
    if (!nav) return;

    const currentPath = window.location.pathname.split('/').pop() || 'index.html';

    const links = [
        { href: 'index.html', text: 'Главная', content: '<h2>Добро пожаловать в Legal Clinic CRM</h2><p>Пожалуйста, войдите или зарегистрируйтесь.</p>' },
        { href: 'dashboard.html', text: 'Панель управления', content: '<h2>Панель управления</h2><p>Здесь будет общая информация и статистика.</p>' },
        { href: 'profile.html', text: 'Профиль', js: 'loadProfile' },
        { href: 'tasks.html', text: 'Задачи', js: 'loadTasks' },
        { href: 'clients.html', text: 'Клиенты', js: 'loadClients' },
        { href: 'documents.html', text: 'Документы', js: 'loadDocuments' },
        { href: 'cases.html', text: 'Дела', js: 'loadCases' },
        { href: 'billing.html', text: 'Биллинг', js: 'loadBilling' },
        { href: 'reports.html', text: 'Отчёты', js: 'loadReports' },
        { href: 'history.html', text: 'История', js: 'loadHistory' },
        { href: 'settings.html', text: 'Настройки', js: 'loadSettings' },
        { href: 'contact.html', text: 'Контакты', js: 'loadContact' },
        { href: 'help.html', text: 'Помощь', js: 'loadHelp' },
        ...(role === 'curator' ? [{ href: 'admin.html', text: 'Админ-панель', js: 'loadUsers' }] : [])
    ];

    nav.innerHTML = `
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      ${links.map(link => `
        <li class="nav-item">
          <a class="nav-link ${currentPath === link.href ? 'active' : ''}" href="${link.href}" data-js="${link.js || ''}" data-content="${link.content ? encodeURIComponent(link.content) : ''}">${link.text}</a>
        </li>
      `).join('')}
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <button id="logoutNavButton" class="btn btn-outline-danger">Выйти</button>
      </li>
    </ul>
  `;

    const logoutButton = document.getElementById('logoutNavButton');
    if (logoutButton) logoutButton.addEventListener('click', logout);

    nav.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', async (e) => {
            e.preventDefault();
            const href = link.getAttribute('href');
            const jsFunc = link.getAttribute('data-js');
            const content = decodeURIComponent(link.getAttribute('data-content') || '');
            const container = document.querySelector('.container');

            if (container) {
                try {
                    container.innerHTML = content || '<h2>Загрузка...</h2>';
                    if (jsFunc) {
                        const module = await import(`./${jsFunc.split('load')[1].toLowerCase()}.js`);
                        await module[jsFunc]();
                    } else {
                        container.innerHTML = content || '<h2>Страница загружена</h2>';
                    }
                    window.history.pushState({}, link.textContent, href);
                    updateActiveLink(href);
                } catch (error) {
                    console.error(`Error loading ${href}:`, error);
                    container.innerHTML = `
            <div class="alert alert-danger" role="alert">
              Ошибка загрузки страницы: ${error.message || 'Неизвестная ошибка'}. 
              <a href="${href}" class="alert-link">Попробовать снова</a>
            </div>
          `;
                }
            }
        });
    });
}

function updateActiveLink(currentHref) {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === currentHref);
    });
}