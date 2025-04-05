// web/js/cases.js
import { api } from './core.js';

export async function loadCases() {
    try {
        const data = await api.cachedRequest('cases.php', 'GET', null, 'casesCache');
        const tableBody = document.querySelector('#casesList tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        data.forEach(caseItem => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${caseItem.id}</td>
        <td>${caseItem.title}</td>
        <td>${caseItem.status}</td>
        <td>
          <button class="btn btn-primary btn-sm edit-case" data-id="${caseItem.id}">Редактировать</button>
          <button class="btn btn-danger btn-sm delete-case" data-id="${caseItem.id}">Удалить</button>
        </td>
      `;
            row.querySelector('.edit-case').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('editCaseModal'));
                const form = document.getElementById('editCaseForm');
                form.id.value = caseItem.id;
                form.title.value = caseItem.title;
                form.status.value = caseItem.status;
                modal.show();
            });
            row.querySelector('.delete-case').addEventListener('click', () => deleteCase(caseItem.id));
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Cases load error:', error);
        alert('Ошибка загрузки дел');
    }
}

export async function addCase(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';

        const data = await api.request('cases.php', 'POST', {
            title: form.title.value.trim(),
            status: form.status.value
        });

        if (data.success) {
            form.reset();
            localStorage.removeItem('casesCache'); // Очистка кэша после добавления
            await loadCases();
            alert(data.message || 'Дело успешно добавлено');
        }
    } catch (error) {
        console.error('Add case error:', error);
        alert(error.message || 'Ошибка добавления дела');
    } finally {
        button.disabled = false;
        button.textContent = 'Добавить дело';
    }
}

export async function editCase(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request(`cases.php?id=${form.id.value}`, 'PUT', {
            title: form.title.value.trim(),
            status: form.status.value
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCaseModal'));
            modal.hide();
            localStorage.removeItem('casesCache'); // Очистка кэша после редактирования
            await loadCases();
            alert(data.message || 'Дело успешно обновлено');
        }
    } catch (error) {
        console.error('Edit case error:', error);
        alert(error.message || 'Ошибка редактирования дела');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}

export async function deleteCase(caseId) {
    if (!confirm('Вы уверены, что хотите удалить это дело?')) return;

    try {
        const data = await api.request(`cases.php?id=${caseId}`, 'DELETE');
        if (data.success) {
            localStorage.removeItem('casesCache'); // Очистка кэша после удаления
            await loadCases();
            alert(data.message || 'Дело успешно удалено');
        }
    } catch (error) {
        console.error('Delete case error:', error);
        alert(error.message || 'Ошибка удаления дела');
    }
}