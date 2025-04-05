// web/js/history.js
import { api } from './core.js';

export async function loadHistory() {
    const tableBody = document.querySelector('#historyList tbody');
    if (!tableBody) return;

    tableBody.innerHTML = '<tr><td colspan="3" class="text-center"><span class="spinner-border spinner-border-sm"></span> Загрузка...</td></tr>';

    try {
        const data = await api.cachedRequest('history.php', 'GET', null, 'historyCache');
        tableBody.innerHTML = '';
        data.forEach(entry => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${entry.id}</td>
        <td>${entry.action}</td>
        <td>${new Date(entry.timestamp).toLocaleString()}</td>
      `;
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('History load error:', error);
        tableBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Ошибка загрузки истории</td></tr>';
    }
}

export async function addHistoryEntry(action) {
    try {
        const data = await api.request('history.php', 'POST', { action });
        if (data.success) {
            localStorage.removeItem('historyCache');
            await loadHistory();
        }
    } catch (error) {
        console.error('Add history error:', error);
    }
}