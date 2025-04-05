// web/js/documents.js
import { api } from './core.js';

export async function loadDocuments() {
    try {
        const data = await api.cachedRequest('documents.php', 'GET', null, 'documentsCache');
        const tableBody = document.querySelector('#documentsList tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        data.forEach(doc => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${doc.id}</td>
        <td>${doc.name}</td>
        <td><a href="${doc.url}" target="_blank">Скачать</a></td>
      `;
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Documents load error:', error);
        alert('Ошибка загрузки документов');
    }
}