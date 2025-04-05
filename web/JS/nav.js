// web/js/nav.js
import { logout } from './profile.js'; // Предполагаем, что функция logout уже есть в profile.js

export function initNav(role) {
    const nav = document.getElementById('mainNav');
    if (!nav) return;

    const links = [
        { href: 'index.html', text: 'Главная' },
        { href: 'dashboard.html', text: 'Панель управления' },
        { href: 'profile.html', text: 'Профиль' },
        { href: 'tasks.html', text: 'Задачи' },
        { href: 'clients.html', text: 'Клиенты' },
        { href: 'documents.html', text: 'Документы' },
        { href: 'cases.html', text: 'Дела' },
        { href: 'billing.html', text: 'Биллинг' },
        { href: 'reports.html', text: 'Отчёты' },
        { href: 'history.html', text: 'История' },
        { href: 'settings.html', text: 'Настройки' },
        { href: 'contact.html', text: 'Контакты' },
        { href: 'help.html', text: 'Помощь' },
        ...(role === 'curator' ? [{ href: 'admin.html', text: 'Админ-панель' }] : [])
    ];

    nav.innerHTML = `
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      ${links.map(link => `
        <li class="nav-item">
          <a class="nav-link" href="${link.href}">${link.text}</a>
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
}