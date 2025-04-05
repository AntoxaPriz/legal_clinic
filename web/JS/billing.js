// web/js/billing.js
import { api } from './core.js';

export async function loadBilling() {
    try {
        const data = await api.request('billing.php'); // Предполагаемый API
        const container = document.querySelector('.container');
        if (!container) return;

        container.innerHTML = `
      <h2>Биллинг</h2>
      <p>Информация о платежах будет здесь (заглушка).</p>
    `;
    } catch (error) {
        console.error('Billing load error:', error);
        alert('Ошибка загрузки данных биллинга');
    }
}