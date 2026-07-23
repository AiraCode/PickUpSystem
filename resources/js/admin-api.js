document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = '/api/admin';
    const token = localStorage.getItem('admin_token');
    const user = JSON.parse(localStorage.getItem('admin_user') || 'null');

    // Cek Akses
    if (!token && window.location.pathname !== '/admin/login') {
        window.location.href = '/admin/login';
        return;
    }
    if (token && window.location.pathname === '/admin/login') {
        window.location.href = '/admin/dashboard';
        return;
    }

    // Set Profil di Header
    if (user && document.getElementById('auth-user-name')) {
        document.getElementById('auth-user-name').innerText = user.name;
        document.getElementById('auth-user-initial').innerText = user.name.charAt(0).toUpperCase();
    }

    // Helper fetch
    const fetchApi = async (endpoint, options = {}) => {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };
        if (token) headers['Authorization'] = `Bearer ${token}`;
        
        const response = await fetch(`${API_BASE}${endpoint}`, { ...options, headers });
        if (response.status === 401) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login';
        }
        return response.json();
    };

    // Logout
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async (e) => {
            e.preventDefault();
            await fetch('/api/logout', { method: 'POST', headers: { 'Authorization': `Bearer ${token}` }});
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login';
        });
    }

    // Toggle menu profil
    const profileBtn = document.getElementById('admin-profile-btn');
    const profileMenu = document.getElementById('admin-profile-menu');
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', () => {
            profileMenu.hidden = !profileMenu.hidden;
        });
    }

    // Login Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-submit');
            const err = document.getElementById('login-error');
            btn.disabled = true;
            btn.innerText = 'Memproses...';
            err.style.display = 'none';

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value
                    })
                });
                const data = await res.json();
                
                if (res.ok && data.token) {
                    localStorage.setItem('admin_token', data.token);
                    localStorage.setItem('admin_user', JSON.stringify(data.user));
                    window.location.href = '/admin/dashboard';
                } else {
                    err.innerText = data.message || 'Login gagal';
                    err.style.display = 'block';
                }
            } catch (error) {
                err.innerText = 'Terjadi kesalahan jaringan';
                err.style.display = 'block';
            }
            btn.disabled = false;
            btn.innerText = 'Masuk';
        });
    }

    // Dashboard Data
    if (window.location.pathname === '/admin/dashboard') {
        const loadDashboard = async () => {
            const res = await fetchApi('/dashboard-stats');
            if (res.data) {
                // Overview
                document.getElementById('stat-total-transactions').innerText = res.data.overview.total_transactions;
                document.getElementById('stat-pending-verifications').innerText = res.data.overview.pending_verifications;
                document.getElementById('stat-total-sales').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(res.data.overview.total_sales);
                document.getElementById('stat-avg-time').innerText = res.data.overview.avg_processing_time;

                // Attention Orders
                const tbody = document.getElementById('attention-orders-tbody');
                if (res.data.attention_orders.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
                } else {
                    tbody.innerHTML = res.data.attention_orders.map(o => `
                        <tr>
                            <td>#${o.id}</td>
                            <td>${o.customer.name}</td>
                            <td>${o.city.name}</td>
                            <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td>
                            <td><span style="padding:4px 8px; border-radius:4px; font-size:11px; background:#fff0f1; color:#ba1b2b; font-weight:bold;">${o.status}</span></td>
                        </tr>
                    `).join('');
                }

                // Recent Activities
                const actList = document.getElementById('activity-list-container');
                const actEmpty = document.getElementById('activity-empty-state');
                const shipments = res.data.recent_activities.shipments || [];
                const receipts = res.data.recent_activities.receipts || [];
                
                if (shipments.length === 0 && receipts.length === 0) {
                    actEmpty.innerHTML = `<strong>Belum ada aktivitas</strong>`;
                } else {
                    actEmpty.style.display = 'none';
                    actList.style.display = 'flex';
                    let html = '';
                    shipments.forEach(s => {
                        html += `<div style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                            <strong style="display:block; font-size:12px;">Pengiriman #${s.id}</strong>
                            <small style="color:#6d727c;">Ke Gudang ${s.warehouse.name} - ${s.status}</small>
                        </div>`;
                    });
                    actList.innerHTML = html;
                }
            }
        };
        loadDashboard();
    }

    // Orders Data
    if (window.location.pathname === '/admin/transaksi') {
        const loadOrders = async () => {
            const res = await fetchApi('/orders');
            const tbody = document.getElementById('orders-tbody');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(o => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.customer.name}</td>
                        <td>${o.city.name}</td>
                        <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td>
                        <td><strong>${o.status}</strong></td>
                        <td>
                            <button onclick="editOrderStatus(${o.id}, '${o.status}')" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Update</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="6"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            }
        };
        loadOrders();

        window.editOrderStatus = (id, currentStatus) => {
            document.getElementById('update-order-id').value = id;
            document.getElementById('update-order-status').value = currentStatus;
            document.getElementById('modal-update-order').style.display = 'flex';
        };

        const form = document.getElementById('form-update-order');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('update-order-id').value;
                const status = document.getElementById('update-order-status').value;
                await fetchApi(`/orders/${id}`, { method: 'PUT', body: JSON.stringify({ status }) });
                document.getElementById('modal-update-order').style.display = 'none';
                loadOrders();
            });
        }
    }

    // Prices Data (Cities & Accus)
    if (window.location.pathname === '/admin/harga') {
        const loadCities = async () => {
            const res = await fetchApi('/cities');
            const tbody = document.getElementById('cities-tbody');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(c => `
                    <tr>
                        <td>${c.id}</td>
                        <td>${c.name}</td>
                        <td><button onclick="deleteCity(${c.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada kota</strong></div></td></tr>`;
            }
        };

        const loadAccus = async () => {
            const res = await fetchApi('/accus');
            const tbody = document.getElementById('accus-tbody');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(a => `
                    <tr>
                        <td>${a.id}</td>
                        <td>${a.name}</td>
                        <td>Rp ${a.base_price}</td>
                        <td><button onclick="deleteAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada aki</strong></div></td></tr>`;
            }
        };

        loadCities();
        loadAccus();

        document.getElementById('form-add-city').addEventListener('submit', async (e) => {
            e.preventDefault();
            await fetchApi('/cities', { method: 'POST', body: JSON.stringify({
                id: document.getElementById('city-id').value,
                name: document.getElementById('city-name').value
            })});
            document.getElementById('modal-add-city').style.display = 'none';
            loadCities();
        });

        document.getElementById('form-add-accu').addEventListener('submit', async (e) => {
            e.preventDefault();
            await fetchApi('/accus', { method: 'POST', body: JSON.stringify({
                id: document.getElementById('accu-id').value,
                name: document.getElementById('accu-name').value,
                base_price: 0
            })});
            document.getElementById('modal-add-accu').style.display = 'none';
            loadAccus();
        });

        window.deleteCity = async (id) => {
            if(confirm('Hapus kota ini?')) {
                await fetchApi(`/cities/${id}`, { method: 'DELETE' });
                loadCities();
            }
        };
        window.deleteAccu = async (id) => {
            if(confirm('Hapus aki ini?')) {
                await fetchApi(`/accus/${id}`, { method: 'DELETE' });
                loadAccus();
            }
        };
    }

    // Storages Data
    if (window.location.pathname === '/admin/gudang') {
        const loadStorages = async () => {
            const res = await fetchApi('/warehouses');
            const tbody = document.getElementById('storages-tbody');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(s => `
                    <tr>
                        <td>${s.id}</td>
                        <td>${s.name}</td>
                        <td>${s.city_id}</td>
                        <td><button onclick="deleteStorage(${s.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada gudang</strong></div></td></tr>`;
            }
        };
        loadStorages();

        document.getElementById('form-add-storage').addEventListener('submit', async (e) => {
            e.preventDefault();
            await fetchApi('/warehouses', { method: 'POST', body: JSON.stringify({
                id: document.getElementById('storage-id').value,
                name: document.getElementById('storage-name').value,
                city_id: document.getElementById('storage-city-id').value
            })});
            document.getElementById('modal-add-storage').style.display = 'none';
            loadStorages();
        });

        window.deleteStorage = async (id) => {
            if(confirm('Hapus gudang ini?')) {
                await fetchApi(`/warehouses/${id}`, { method: 'DELETE' });
                loadStorages();
            }
        };
    }

    // Users Data
    if (window.location.pathname === '/admin/pengguna') {
        const loadUsers = async () => {
            const res = await fetchApi('/users');
            const tbody = document.getElementById('users-tbody');
            if (res.data && res.data.length > 0) {
                tbody.innerHTML = res.data.map(u => `
                    <tr>
                        <td>${u.id}</td>
                        <td>${u.name}</td>
                        <td>${u.email}</td>
                        <td>${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                        <td><button onclick="deleteUser(${u.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button></td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pengguna</strong></div></td></tr>`;
            }
        };
        loadUsers();

        document.getElementById('form-add-user').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                name: document.getElementById('user-name').value,
                email: document.getElementById('user-email').value,
                password: document.getElementById('user-password').value
            };
            const idVal = document.getElementById('user-id').value;
            if(idVal) payload.id = idVal;
            else payload.id = Math.floor(Math.random() * 1000000);

            await fetchApi('/users', { method: 'POST', body: JSON.stringify(payload)});
            document.getElementById('modal-add-user').style.display = 'none';
            loadUsers();
        });

        window.deleteUser = async (id) => {
            if(confirm('Hapus pengguna ini?')) {
                await fetchApi(`/users/${id}`, { method: 'DELETE' });
                loadUsers();
            }
        };
    }
});
