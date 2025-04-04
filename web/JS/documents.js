// web/js/documents.js
import { api } from './core.js';

export async function loadDocuments() {
    try {
        const data = await api.request('documents.php');
        const table = document.getElementById('documentsList');

        data.forEach(doc => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${doc.id}</td>
        <td>${doc.file_path.split('/').pop()}</td>
        <td>${doc.extracted_text.substring(0, 100)}...</td>
        <td><button class="btn btn-sm btn-outline-danger delete-doc" data-id="${doc.id}">Удалить</button></td>
      `;
            table.appendChild(row);
        });

        table.addEventListener('click', async (e) => {
            if (e.target.classList.contains('delete-doc')) {
                if (confirm('Вы уверены?')) {
                    await api.request(`documents.php?id=${e.target.dataset.id}`, 'DELETE');
                    e.target.closest('tr').remove();
                }
            }
        });
    } catch (error) {
        console.error('Documents error:', error);
        alert('Ошибка загрузки документов');
    }
}