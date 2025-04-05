// web/js/contact.js
import { api } from './core.js';

export async function loadContact() {
    try {
        const data = await api.cachedRequest('contact.php', 'GET', null, 'contactCache');
        const container = document.querySelector('.container');
        if (!container) return;

        container.innerHTML = `
      <h2>Контакты</h2>
      <ul class="list-group">
        <li class="list-group-item"><strong>Email:</strong> ${data.email}</li>
        <li class="list-group-item"><strong>Телефон:</strong> ${data.phone}</li>
        <li class="list-group-item"><strong>Адрес:</strong> ${data.address}</li>
      </ul>
    `;
    } catch (error) {
        console.error('Contact load error:', error);
        alert('Ошибка загрузки контактов');
    }
}