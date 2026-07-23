document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = '/api/admin';
    const token = localStorage.getItem('admin_token');
    const user = JSON.parse(localStorage.getItem('admin_user') || 'null');

    if (!token && window.location.pathname !== '/admin/login') { window.location.href = '/admin/login'; return; }
    if (token && window.location.pathname === '/admin/login') { window.location.href = '/admin/dashboard'; return; }

    if (user && document.getElementById('auth-user-name')) {
        document.getElementById('auth-user-name').innerText = user.name;
        document.getElementById('auth-user-initial').innerText = user.name.charAt(0).toUpperCase();
    }

    const fetchApi = async (endpoint, options = {}) => {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (token) headers['Authorization'] = `Bearer ${token}`;
        const response = await fetch(`${API_BASE}${endpoint}`, { ...options, headers });
        if (response.status === 401) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login';
        }
        return response.json();
    };

    const rupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);

    const statusBadge = (status) => {
        const colors = {
            pending:    { bg: '#fef3c7', color: '#92400e' },
            processing: { bg: '#dbeafe', color: '#1e40af' },
            completed:  { bg: '#d1fae5', color: '#065f46' },
            cancelled:  { bg: '#fee2e2', color: '#991b1b' },
        };
        const c = colors[status] || { bg: '#f3f4f6', color: '#374151' };
        return `<span style="padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; background:${c.bg}; color:${c.color}; text-transform:uppercase;">${status}</span>`;
    };

    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async (e) => {
            e.preventDefault();
            await fetch('/api/logout', { method: 'POST', headers: { 'Authorization': `Bearer ${token}` } });
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login';
        });
    }

    const profileBtn = document.getElementById('admin-profile-btn');
    const profileMenu = document.getElementById('admin-profile-menu');
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', () => { profileMenu.hidden = !profileMenu.hidden; });
    }

    // ── LOGIN ──
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
                    body: JSON.stringify({ name: document.getElementById('name').value, password: document.getElementById('password').value })
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

    // ── DASHBOARD ──
    if (window.location.pathname === '/admin/dashboard') {
        (async () => {
            const res = await fetchApi('/dashboard-stats');
            if (!res.data) return;
            document.getElementById('stat-total-transactions').innerText = res.data.overview.total_transactions;
            document.getElementById('stat-pending-verifications').innerText = res.data.overview.pending_verifications;
            document.getElementById('stat-total-sales').innerText = rupiah(res.data.overview.total_sales);
            document.getElementById('stat-avg-time').innerText = res.data.overview.avg_processing_time;

            const tbody = document.getElementById('attention-orders-tbody');
            if (!res.data.attention_orders.length) {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            } else {
                tbody.innerHTML = res.data.attention_orders.map(o => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.customer.name}</td>
                        <td>${o.city.name}</td>
                        <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td>
                        <td>${statusBadge(o.status)}</td>
                    </tr>`).join('');
            }

            const actList = document.getElementById('activity-list-container');
            const actEmpty = document.getElementById('activity-empty-state');
            const shipments = res.data.recent_activities.shipments || [];
            if (!shipments.length) {
                actEmpty.innerHTML = `<strong>Belum ada aktivitas</strong>`;
            } else {
                actEmpty.style.display = 'none';
                actList.style.display = 'flex';
                actList.innerHTML = shipments.map(s => `
                    <div style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                        <strong style="display:block; font-size:12px;">Pengiriman #${s.id}</strong>
                        <small style="color:#6d727c;">Ke Gudang ${s.warehouse.name} - ${s.status}</small>
                    </div>`).join('');
            }
        })();
    }

    // ── TRANSAKSI MASUK ──
    if (window.location.pathname === '/admin/transaksi') {
        const uploadArea = document.getElementById('upload-area');
        const uploadInput = document.getElementById('upload-proof');
        const uploadPreview = document.getElementById('upload-preview');
        const uploadPlaceholder = document.getElementById('upload-placeholder');

        if (uploadArea && uploadInput) {
            uploadArea.addEventListener('click', () => uploadInput.click());
            uploadInput.addEventListener('change', () => {
                const file = uploadInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        uploadPreview.src = e.target.result;
                        uploadPreview.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const loadOrders = async () => {
            const res = await fetchApi('/orders');
            const tbody = document.getElementById('orders-tbody');
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data.map(o => `
                    <tr>
                        <td style="font-weight:500;">${o.customer ? o.customer.name : '-'}</td>
                        <td>${o.city ? o.city.name : '-'}</td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${o.pickup_address}</td>
                        <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td>
                        <td>${statusBadge(o.status)}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="viewOrderDetail(${o.id})" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Detail</button>
                                <button onclick="editOrderStatus(${o.id}, '${o.status}')" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Update</button>
                            </div>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="6"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            }
        };
        loadOrders();

        window.editOrderStatus = (id, currentStatus) => {
            document.getElementById('update-order-id').value = id;
            const radios = document.querySelectorAll('input[name="order_status"]');
            radios.forEach(r => {
                r.checked = (r.value === currentStatus);
                const card = r.closest('label');
                card.style.borderColor = r.checked ? '#3b82f6' : '#e5e7eb';
            });
            uploadPreview.style.display = 'none';
            uploadPlaceholder.style.display = 'block';
            uploadInput.value = '';
            document.getElementById('modal-update-order').style.display = 'flex';
        };

        document.querySelectorAll('input[name="order_status"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('input[name="order_status"]').forEach(r => {
                    r.closest('label').style.borderColor = r.checked ? '#3b82f6' : '#e5e7eb';
                });
            });
        });

        window.viewOrderDetail = async (id) => {
            const res = await fetchApi(`/orders/${id}`);
            if (!res.data) return;
            const o = res.data;
            const c = o.customer || {};
            const bankName = (c.bank && c.bank.name) ? c.bank.name : '-';
            document.getElementById('detail-customer-name').innerText = c.name || '-';
            document.getElementById('detail-customer-phone').innerText = c.phone_number || '-';
            document.getElementById('detail-customer-address').innerText = c.address || '-';
            document.getElementById('detail-customer-ktp').innerText = c.ktp || '-';
            document.getElementById('detail-customer-bank').innerText = `${bankName} - ${c.account_number || '-'} (a.n. ${c.account_name || '-'})`;
            document.getElementById('detail-order-city').innerText = o.city ? o.city.name : '-';
            document.getElementById('detail-order-status').innerHTML = statusBadge(o.status);
            document.getElementById('detail-order-time').innerText = new Date(o.created_at).toLocaleString('id-ID');
            document.getElementById('detail-order-pickup-address').innerText = o.pickup_address || '-';
            document.getElementById('detail-order-pickup-note').innerText = o.pickup_address_note || '-';
            document.getElementById('modal-detail-order').style.display = 'flex';
        };

        const formUpdate = document.getElementById('form-update-order');
        if (formUpdate) {
            formUpdate.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('update-order-id').value;
                const selected = document.querySelector('input[name="order_status"]:checked');
                if (!selected) { alert('Pilih status terlebih dahulu'); return; }
                await fetchApi(`/orders/${id}/status`, { method: 'PUT', body: JSON.stringify({ status: selected.value }) });
                document.getElementById('modal-update-order').style.display = 'none';
                loadOrders();
            });
        }
    }

    // ── HARGA AKI ──
    if (window.location.pathname === '/admin/harga') {
        let cachedCities = [];
        let cachedAccus = [];
        let activeCityId = null;
        let activeCityName = '';

        const loadCities = async () => {
            const res = await fetchApi('/cities');
            cachedCities = res.data || [];
            const tbody = document.getElementById('cities-tbody');
            if (cachedCities.length) {
                tbody.innerHTML = cachedCities.map(c => `
                    <tr>
                        <td style="font-weight:500;">${c.name}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="viewCityAccus(${c.id}, '${c.name}')" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Lihat Harga</button>
                                <button onclick="deleteCity(${c.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="2"><div class="admin-table-empty"><strong>Belum ada kota</strong></div></td></tr>`;
            }
            const sel = document.getElementById('set-price-city');
            if (sel) sel.innerHTML = cachedCities.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        };

        const loadAccus = async () => {
            const res = await fetchApi('/accus');
            cachedAccus = res.data || [];
            const tbody = document.getElementById('accus-tbody');
            if (cachedAccus.length) {
                tbody.innerHTML = cachedAccus.map(a => `
                    <tr>
                        <td style="font-weight:500;">${a.brand}</td>
                        <td>${a.name}</td>
                        <td>
                            <button onclick="deleteAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada aki</strong></div></td></tr>`;
            }
            const sel = document.getElementById('set-price-accu');
            if (sel) sel.innerHTML = cachedAccus.map(a => `<option value="${a.id}">${a.brand} - ${a.name}</option>`).join('');
        };

        loadCities();
        loadAccus();

        window.viewCityAccus = async (cityId, cityName) => {
            activeCityId = cityId;
            activeCityName = cityName;
            const res = await fetchApi(`/cities/${cityId}/accus`);
            const tbody = document.getElementById('city-accus-tbody');
            if (res.data && res.data.accus && res.data.accus.length) {
                tbody.innerHTML = res.data.accus.map(a => `
                    <tr>
                        <td style="font-weight:500;">${cityName}</td>
                        <td>${a.brand}</td>
                        <td>${a.name}</td>
                        <td>${rupiah(a.price)}</td>
                        <td>
                            <button onclick="deleteCityAccu(${cityId}, ${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada harga untuk kota ${cityName}</strong></div></td></tr>`;
            }
        };

        window.deleteCity = async (id) => {
            if (confirm('Yakin hapus kota ini?')) { await fetchApi(`/cities/${id}`, { method: 'DELETE' }); loadCities(); }
        };
        window.deleteAccu = async (id) => {
            if (confirm('Yakin hapus aki ini?')) { await fetchApi(`/accus/${id}`, { method: 'DELETE' }); loadAccus(); }
        };
        window.deleteCityAccu = async (cityId, accuId) => {
            if (confirm('Yakin hapus harga ini?')) {
                await fetchApi(`/cities/${cityId}/accus/${accuId}`, { method: 'DELETE' });
                viewCityAccus(activeCityId, activeCityName);
            }
        };

        document.getElementById('form-add-city').addEventListener('submit', async (e) => {
            e.preventDefault();
            await fetchApi('/cities', { method: 'POST', body: JSON.stringify({ name: document.getElementById('city-name').value }) });
            document.getElementById('modal-add-city').style.display = 'none';
            document.getElementById('form-add-city').reset();
            loadCities();
        });

        document.getElementById('form-add-accu').addEventListener('submit', async (e) => {
            e.preventDefault();
            await fetchApi('/accus', { method: 'POST', body: JSON.stringify({
                brand: document.getElementById('accu-brand').value,
                name: document.getElementById('accu-name').value
            }) });
            document.getElementById('modal-add-accu').style.display = 'none';
            document.getElementById('form-add-accu').reset();
            loadAccus();
        });

        document.getElementById('form-set-price').addEventListener('submit', async (e) => {
            e.preventDefault();
            const cityId = document.getElementById('set-price-city').value;
            await fetchApi(`/cities/${cityId}/accus`, { method: 'POST', body: JSON.stringify({
                accus_id: document.getElementById('set-price-accu').value,
                price: document.getElementById('set-price-value').value
            }) });
            document.getElementById('modal-set-price').style.display = 'none';
            document.getElementById('form-set-price').reset();
            const cityOpt = cachedCities.find(c => c.id == cityId);
            viewCityAccus(cityId, cityOpt ? cityOpt.name : '');
        });
    }

    // ── GUDANG & LOKASI ──
    if (window.location.pathname === '/admin/gudang') {
        let addMarker = null;

        const addMap = L.map('map-add').setView([-7.2575, 112.7521], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(addMap);
        window.addMap = addMap;

        addMap.on('click', (e) => {
            const { lat, lng } = e.latlng;
            document.getElementById('storage-lat').value = lat.toFixed(8);
            document.getElementById('storage-long').value = lng.toFixed(8);
            document.getElementById('map-coords').innerText = `Lat: ${lat.toFixed(6)}, Long: ${lng.toFixed(6)}`;

            if (addMarker) addMap.removeLayer(addMarker);
            addMarker = L.marker([lat, lng]).addTo(addMap);
        });

        let viewMap = null;

        window.showStorageMap = (name, lat, lng) => {
            document.getElementById('view-map-title').innerText = `Lokasi: ${name}`;
            document.getElementById('modal-view-map').style.display = 'flex';

            setTimeout(() => {
                if (viewMap) { viewMap.remove(); viewMap = null; }
                viewMap = L.map('map-view').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(viewMap);
                L.marker([lat, lng]).addTo(viewMap).bindPopup(`<strong>${name}</strong>`).openPopup();
            }, 200);
        };

        const loadStorages = async () => {
            const res = await fetchApi('/storages');
            const tbody = document.getElementById('storages-tbody');
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data.map(s => `
                    <tr>
                        <td style="font-weight:500;">${s.name}</td>
                        <td>${s.address || '-'}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="showStorageMap('${s.name}', ${s.lat}, ${s.long})" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Lihat Peta</button>
                                <button onclick="deleteStorage(${s.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada gudang</strong></div></td></tr>`;
            }
        };
        loadStorages();

        document.getElementById('form-add-storage').addEventListener('submit', async (e) => {
            e.preventDefault();
            const lat = document.getElementById('storage-lat').value;
            const lng = document.getElementById('storage-long').value;
            if (!lat || !lng) { alert('Pilih lokasi di peta terlebih dahulu'); return; }
            await fetchApi('/storages', { method: 'POST', body: JSON.stringify({
                name: document.getElementById('storage-name').value,
                address: document.getElementById('storage-address').value,
                lat: lat,
                long: lng
            }) });
            document.getElementById('modal-add-storage').style.display = 'none';
            document.getElementById('form-add-storage').reset();
            document.getElementById('map-coords').innerText = 'Belum ada titik dipilih';
            if (addMarker) { addMap.removeLayer(addMarker); addMarker = null; }
            loadStorages();
        });

        window.deleteStorage = async (id) => {
            if (confirm('Yakin hapus gudang ini?')) { await fetchApi(`/storages/${id}`, { method: 'DELETE' }); loadStorages(); }
        };
    }

    // ── PENGGUNA ──
    if (window.location.pathname === '/admin/pengguna') {
        const loadUsers = async () => {
            const res = await fetchApi('/users');
            const tbody = document.getElementById('users-tbody');
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data.map(u => `
                    <tr>
                        <td style="font-weight:500;">${u.name}</td>
                        <td>${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                        <td>
                            <button onclick="deleteUser(${u.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada pengguna</strong></div></td></tr>`;
            }
        };
        loadUsers();

        document.getElementById('form-add-user').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                name: document.getElementById('user-name').value,
                password: document.getElementById('user-password').value
            };
            const idVal = document.getElementById('user-id').value;
            payload.id = idVal ? idVal : Math.floor(Math.random() * 1000000);
            await fetchApi('/users', { method: 'POST', body: JSON.stringify(payload) });
            document.getElementById('modal-add-user').style.display = 'none';
            document.getElementById('form-add-user').reset();
            loadUsers();
        });

        window.deleteUser = async (id) => {
            if (confirm('Yakin hapus pengguna ini?')) { await fetchApi(`/users/${id}`, { method: 'DELETE' }); loadUsers(); }
        };
    }
});
