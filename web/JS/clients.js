// web/js/clients.js
import { api } from './core.js';

export async function loadClients() {
    try {
        const data = await api.cachedRequest('clients.php', 'GET', null, 'clientsCache');
        const tableBody = document.querySelector('#clientsList tbody');
        if (!tableBody) return;

        tableBody.innerHTML = '';
        data.forEach(client => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${client.id}</td>
        <td>${client.name}</td>
        <td>${client.contact}</td>
        <td>
          <button class="btn btn-primary btn-sm edit-client" data-id="${client.id}">Редактировать</button>
          <button class="btn btn-danger btn-sm delete-client" data-id="${client.id}">Удалить</button>
        </td>
      `;
            row.querySelector('.edit-client').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('editClientModal'));
                const form = document.getElementById('editClientForm');
                form.id.value = client.id;
                form.name.value = client.name;
                form.contact.value = client.contact;
                modal.show();
            });
            row.querySelector('.delete-client').addEventListener('click', () => deleteClient(client.id));
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Clients load error:', error);
        alert('Ошибка загрузки клиентов');
    }
}

export async function addClient(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';

        const data = await api.request('clients.php', 'POST', {
            name: form.name.value.trim(),
            contact: form.contact.value.trim()
        });

        if (data.success) {
            form.reset();
            localStorage.removeItem('clientsCache'); // Очистка кэша
            await loadClients();
            alert(data.message || 'Клиент успешно добавлен');
        }
    } catch (error) {
        console.error('Add client error:', error);
        alert(error.message || 'Ошибка добавления клиента');
    } finally {
        button.disabled = false;
        button.textContent = 'Добавить клиента';
    }
}

export async function editClient(e) {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');

    try {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';

        const data = await api.request(`clients.php?id=${form.id.value}`, 'PUT', {
            name: form.name.value.trim(),
            contact: form.contact.value.trim()
        });

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editClientModal'));
            modal.hide();
            localStorage.removeItem('clientsCache'); // Очистка кэша
            await loadClients();
            alert(data.message || 'Клиент успешно обновлён');
        }
    } catch (error) {
        console.error('Edit client error:', error);
        alert(error.message || 'Ошибка редактирования клиента');
    } finally {
        button.disabled = false;
        button.textContent = 'Сохранить';
    }
}

async function deleteClient(clientId) {
    if (!confirm('Вы уверены, что хотите удалить этого клиента?')) return;

    try {
        const data = await api.request(`clients.php?id=${clientId}`, 'DELETE');
        if (data.success) {
            localStorage.removeItem('clientsCache'); // Очистка кэша
            await loadClients();
            alert(data.message || 'Клиент успешно удалён');
        }
    } catch (error) {
        console.error('Delete client error:', error);
        alert(error.message || 'Ошибка удаления клиента');
    }
}