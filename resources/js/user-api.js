document.addEventListener('DOMContentLoaded', () => {
    const PUBLIC_API_BASE = '/api/customer';

    const fetchPublicApi = async (endpoint, options = {}) => {
        try {
            const res = await fetch(`${PUBLIC_API_BASE}${endpoint}`, {
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                ...options,
            });
            return await res.json();
        } catch (err) {
            console.error('Public API Error:', err);
            return { message: 'Terjadi kesalahan jaringan', data: null };
        }
    };

    const rupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);

    // ── LANDING PAGE LOGIC ──
    const citySelect = document.querySelector('[data-city-select]');
    const cityStatus = document.querySelector('[data-city-status]');

    if (citySelect) {
        // Fetch Cities
        (async () => {
            const res = await fetchPublicApi('/cities');
            if (res.data && res.data.length) {
                citySelect.innerHTML = res.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                
                // Restore saved city or select first
                const savedCityId = localStorage.getItem('pickup_city_id') || res.data[0].id;
                citySelect.value = savedCityId;
                loadCityPrices(savedCityId);
            } else {
                citySelect.innerHTML = `<option value="">Tidak ada kota tersedia</option>`;
            }
        })();

        citySelect.addEventListener('change', (e) => {
            const cityId = e.target.value;
            const cityName = e.target.options[e.target.selectedIndex]?.text || '';
            localStorage.setItem('pickup_city_id', cityId);
            localStorage.setItem('pickup_city_name', cityName);
            if (cityStatus) cityStatus.textContent = `Data harga ${cityName} mengikuti data yang terhubung pada sistem.`;
            loadCityPrices(cityId);
        });
    }

    async function loadCityPrices(cityId) {
        if (!cityId) return;
        const res = await fetchPublicApi(`/cities/${cityId}/accus`);
        if (!res.data || !res.data.accus) return;

        const accus = res.data.accus;
        const productCards = document.querySelectorAll('[data-product-card]');

        productCards.forEach(card => {
            const productName = card.getAttribute('data-product-name')?.toLowerCase();
            const productBrand = card.getAttribute('data-product-brand')?.toLowerCase();

            // Match accu by name or brand
            const match = accus.find(a => 
                (a.name && productName && a.name.toLowerCase().includes(productName)) ||
                (a.brand && productBrand && a.brand.toLowerCase().includes(productBrand))
            ) || accus[0]; // fallback to available price

            const priceLabel = card.querySelector('[data-product-price-label]');
            if (match && match.price) {
                card.setAttribute('data-product-price', match.price);
                card.setAttribute('data-accu-id', match.id);
                if (priceLabel) priceLabel.textContent = rupiah(match.price);
            } else {
                if (priceLabel) priceLabel.textContent = 'Harga belum tersedia';
            }
        });
    }

    // ── IDENTITY FORM LOGIC ──
    const identityForm = document.querySelector('[data-identity-form]');
    const bankSelect = document.querySelector('select[name="bank_type"]');

    if (bankSelect) {
        (async () => {
            const res = await fetchPublicApi('/banks');
            if (res.data && res.data.length) {
                bankSelect.innerHTML = '<option value="" selected disabled>Pilih Bank</option>' + 
                    res.data.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
            }
        })();
    }

    if (identityForm) {
        // Confirmation Modal Logic
        const modal = document.querySelector('[data-identity-modal]');
        const modalConfirmBtn = modal?.querySelector('.user-button--primary');
        const modalCancelBtns = modal?.querySelectorAll('[data-modal-close]');

        modalCancelBtns?.forEach(btn => {
            btn.addEventListener('click', () => {
                if (modal) modal.hidden = true;
            });
        });

        let formDataToSubmit = null;

        identityForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target;
            
            const cityId = localStorage.getItem('pickup_city_id') || 1;
            const addressInput = document.querySelector('textarea[name="address"]') || form.querySelector('input[name="full_name"]');

            formDataToSubmit = {
                name: form.querySelector('input[name="full_name"]')?.value || '',
                phone_number: form.querySelector('input[name="whatsapp"]')?.value || '',
                address: localStorage.getItem('pickup_address') || form.querySelector('input[name="full_name"]')?.value + ' - Surabaya',
                address_note: 'Catatan penjemputan',
                banks_id: bankSelect ? parseInt(bankSelect.value) || 1 : 1,
                account_name: form.querySelector('input[name="account_holder"]')?.value || '',
                account_number: form.querySelector('input[name="account_number"]')?.value || '',
                cities_id: parseInt(cityId) || 1,
                pickup_address: localStorage.getItem('pickup_address') || 'Jl. Raya Utama No. 12',
                pickup_address_note: 'Rumah depan masjid',
            };

            if (modal) {
                modal.hidden = false;
            } else {
                submitOrder(formDataToSubmit);
            }
        });

        if (modalConfirmBtn) {
            modalConfirmBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                modalConfirmBtn.disabled = true;
                modalConfirmBtn.textContent = 'Memproses...';
                if (formDataToSubmit) {
                    await submitOrder(formDataToSubmit);
                }
            });
        }

        async function submitOrder(payload) {
            const res = await fetchPublicApi('/orders', {
                method: 'POST',
                body: JSON.stringify(payload),
            });

            if (res.data && res.data.order_id) {
                if (modal) modal.hidden = true;
                window.location.href = `/receipt?order_id=${res.data.order_id}`;
            } else {
                alert(res.message || 'Gagal mengirim pesanan');
                if (modalConfirmBtn) {
                    modalConfirmBtn.disabled = false;
                    modalConfirmBtn.innerHTML = 'Sudah Benar <span aria-hidden="true">→</span>';
                }
            }
        }
    }

    // ── RECEIPT PAGE LOGIC ──
    const receiptContainer = document.querySelector('[data-receipt]');
    if (receiptContainer) {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');

        if (orderId) {
            (async () => {
                const res = await fetchPublicApi(`/orders/${orderId}`);
                if (res.data) {
                    const o = res.data;
                    const c = o.customer || {};
                    const b = c.bank || {};

                    // Header Meta
                    const metaMeta = receiptContainer.querySelector('.user-receipt__meta strong');
                    const metaDate = receiptContainer.querySelector('.user-receipt__meta small');
                    if (metaMeta) metaMeta.textContent = `#ORDER-${o.id}`;
                    if (metaDate) metaDate.textContent = `Tanggal transaksi: ${new Date(o.created_at).toLocaleDateString('id-ID')}`;

                    // Badge
                    const badge = receiptContainer.querySelector('[data-receipt-badge]');
                    if (badge) {
                        badge.textContent = o.status.toUpperCase();
                        badge.className = `user-receipt__status user-receipt__status--${o.status === 'completed' ? 'paid' : 'unpaid'}`;
                    }

                    // Penjual Info
                    const blockPenjual = receiptContainer.querySelectorAll('.user-receipt__block')[0];
                    if (blockPenjual) {
                        const dds = blockPenjual.querySelectorAll('dd');
                        if (dds[0]) dds[0].textContent = c.name || '-';
                        if (dds[1]) dds[1].textContent = c.phone_number || '-';
                        if (dds[2]) dds[2].textContent = b.name || '-';
                        if (dds[3]) dds[3].textContent = `${c.account_number || '-'} (a.n ${c.account_name || '-'})`;
                        if (dds[4]) dds[4].textContent = c.address || '-';
                    }

                    // Penyerahan Info
                    const blockPenyerahan = receiptContainer.querySelectorAll('.user-receipt__block')[1];
                    if (blockPenyerahan) {
                        const dds = blockPenyerahan.querySelectorAll('dd');
                        if (dds[0]) dds[0].textContent = 'Dijemput Kurir Indoprima';
                        if (dds[1]) dds[1].textContent = o.city ? o.city.name : '-';
                        if (dds[2]) dds[2].textContent = 'Gratis';
                        if (dds[3]) dds[3].textContent = o.pickup_address_note || '-';
                    }
                }
            })();
        }
    }
});
