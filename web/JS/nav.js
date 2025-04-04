// web/js/nav.js
export function initNav(role) {
    const nav = document.getElementById('mainNav');
    if (!nav) return;

    const links = [
        { href: 'index.html', text: 'Главная' },
        { href: 'profile.html', text: 'Профиль' },
        { href: 'tasks.html', text: 'Задачи' },
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
  `;
}