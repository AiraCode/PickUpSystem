document.addEventListener("DOMContentLoaded", () => {
    const PUBLIC_API_BASE = "/api/customer";

    const fetchPublicApi = async (endpoint, options = {}) => {
        try {
            const res = await fetch(`${PUBLIC_API_BASE}${endpoint}`, {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                ...options,
            });
            return await res.json();
        } catch (err) {
            console.error("Public API Error:", err);
            return { message: "Terjadi kesalahan jaringan", data: null };
        }
    };

    const rupiah = (n) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(n);

    const showCustomAlert = (msg) => {
        const alertModal = document.getElementById("modal-user-alert");
        const alertMsg = document.getElementById("user-alert-message");
        if (alertModal && alertMsg) {
            alertMsg.textContent = msg;
            alertModal.style.display = "flex";
        } else {
            alert(msg);
        }
    };

    // Clear selections on fresh load of landing page
    if (window.location.pathname === "/user" || window.location.pathname === "/") {
        localStorage.removeItem("pickup_address");
        localStorage.removeItem("pickup_city");
        localStorage.removeItem("pickup_zip");
        localStorage.removeItem("pickup_lat");
        localStorage.removeItem("pickup_long");
        localStorage.removeItem("pickup_cart");
        localStorage.removeItem("pickup_fee");
        localStorage.removeItem("nearest_warehouse_name");
        localStorage.removeItem("nearest_warehouse_address");
        localStorage.removeItem("nearest_warehouse_distance");
        if (window.userCart) {
            window.userCart.clear();
        }
    }

    // ── LANDING PAGE LOGIC ──
    const citySelect = document.querySelector("[data-city-select]");
    const cityStatus = document.querySelector("[data-city-status]");

    // Search accu names logic
    const searchInput = document.getElementById("accu-search-input");
    searchInput?.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase().trim();
        const productCards = document.querySelectorAll("[data-product-card]");
        productCards.forEach((card) => {
            const name = card.getAttribute("data-product-name")?.toLowerCase() || "";
            const brand = card.getAttribute("data-product-brand")?.toLowerCase() || "";
            if (name.includes(query) || brand.includes(query)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    });

    if (citySelect) {
        // Fetch Cities
        (async () => {
            const res = await fetchPublicApi("/cities");
            if (res.data && res.data.length) {
                citySelect.innerHTML = '<option value="" disabled selected>-- Pilih Kota Penyerahan --</option>' + res.data
                    .map((c) => `<option value="${c.id}">${c.name}</option>`)
                    .join("");
            } else {
                citySelect.innerHTML = `<option value="">Tidak ada kota tersedia</option>`;
            }
        })();

        citySelect.addEventListener("change", (e) => {
            const cityId = e.target.value;
            const cityName =
                e.target.options[e.target.selectedIndex]?.text || "";
            localStorage.setItem("pickup_city_id", cityId);
            localStorage.setItem("pickup_city_name", cityName);

            // Kolom kota dibawah langsung sesuai dengan kota yang dipilih
            const userCityInput = document.getElementById("user-city-input");
            if (userCityInput) {
                userCityInput.value = cityName;
                localStorage.setItem("pickup_city", cityName);
            }

            if (cityStatus)
                cityStatus.textContent = `Data harga ${cityName} mengikuti data yang terhubung pada sistem.`;
            loadCityPrices(cityId);
        });
    }

    async function loadCityPrices(cityId) {
        if (!cityId) return;
        const res = await fetchPublicApi(`/cities/${cityId}/accus`);
        if (!res.data || !res.data.accus) return;

        const accus = res.data.accus;

        // 1. Update product cards in the DOM first
        const productCards = document.querySelectorAll("[data-product-card]");
        productCards.forEach((card) => {
            const productName = card.getAttribute("data-product-name")?.toLowerCase();
            const productBrand = card.getAttribute("data-product-brand")?.toLowerCase();

            const match = accus.find((a) =>
                (a.name && productName && a.name.toLowerCase().includes(productName)) ||
                (a.brand && productBrand && a.brand.toLowerCase().includes(productBrand))
            );

            const priceLabel = card.querySelector("[data-product-price-label]");
            if (match && match.price) {
                card.setAttribute("data-product-price", match.price);
                card.setAttribute("data-accu-id", match.id);
                if (priceLabel) priceLabel.textContent = rupiah(match.price);
            } else {
                if (priceLabel) priceLabel.textContent = "Harga belum tersedia";
            }
        });

        // 2. Update the items in userCart using the updated card prices
        if (window.userCart && window.userCart.size > 0) {
            window.userCart.forEach((item, key) => {
                const matchingCard = document.querySelector(`[data-product-card][data-product-name="${item.name}"]`);
                if (matchingCard) {
                    const newPrice = Number(matchingCard.getAttribute("data-product-price"));
                    if (newPrice) {
                        item.price = newPrice;
                    }
                }
            });
            if (typeof window.renderUserCart === 'function') {
                window.renderUserCart();
            }
        }
    }

    // ── IDENTITY FORM LOGIC ──
    const identityForm = document.querySelector("[data-identity-form]");
    const bankSelect = document.querySelector('select[name="bank_type"]');

    if (bankSelect) {
        (async () => {
            const res = await fetchPublicApi("/banks");
            if (res.data && res.data.length) {
                bankSelect.innerHTML =
                    '<option value="" selected disabled>Pilih Bank</option>' +
                    res.data
                        .map(
                            (b) => `<option value="${b.id}">${b.name}</option>`,
                        )
                        .join("");
            }
        })();
    }

    if (identityForm) {
        // Restrict name inputs to only accept letters and spaces, detect and warn if they try non-letters
        const nameInputs = identityForm.querySelectorAll('input[name="full_name"], input[name="account_holder"]');
        nameInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key && e.key.length === 1 && !/[a-zA-Z\s]/.test(e.key)) {
                    e.preventDefault();
                    showCustomAlert("Kolom nama hanya menerima huruf dan spasi!");
                }
            });
            input.addEventListener('paste', (e) => {
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[a-zA-Z\s]+$/.test(pastedText)) {
                    e.preventDefault();
                    showCustomAlert("Teks yang ditempelkan mengandung karakter non-huruf! Kolom nama hanya menerima huruf.");
                }
            });
            // backup cleaner
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            });
        });

        // Restrict number inputs to only accept digits, detect and warn if they try non-digits
        const numberInputs = identityForm.querySelectorAll('input[name="account_number"], input[name="whatsapp"]');
        numberInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key && e.key.length === 1 && !/[0-9]/.test(e.key)) {
                    e.preventDefault();
                    showCustomAlert("Kolom ini hanya menerima angka! Masukan huruf atau simbol tidak diperbolehkan.");
                }
            });
            input.addEventListener('paste', (e) => {
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(pastedText)) {
                    e.preventDefault();
                    showCustomAlert("Teks yang ditempelkan mengandung karakter non-angka! Kolom ini wajib angka.");
                }
            });
            // backup cleaner
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });

        // Populate Summary sidebar on identity page load
        const flowSummary = document.querySelector(".user-flow-summary");
        if (flowSummary) {
            const address = localStorage.getItem("pickup_address") || "Belum diisi";
            const city = localStorage.getItem("pickup_city") || "";
            const zip = localStorage.getItem("pickup_zip") || "";
            
            const addressSummary = flowSummary.querySelectorAll(".user-flow-summary__item")[2]?.querySelector("strong");
            if (addressSummary) {
                addressSummary.textContent = `${address}, ${city} ${zip}`;
            }

            // Cart Items
            const savedCart = JSON.parse(localStorage.getItem("pickup_cart") || "[]");
            const itemsSummary = flowSummary.querySelectorAll(".user-flow-summary__item")[0]?.querySelector("strong");
            let totalItems = 0;
            let subtotal = 0;
            
            savedCart.forEach((item) => {
                if (item && item.quantity) {
                    totalItems += item.quantity;
                    subtotal += item.price * item.quantity;
                }
            });
            
            if (itemsSummary) {
                itemsSummary.textContent = `${totalItems} unit aki`;
            }

            // Delivery method
            const deliverySummary = flowSummary.querySelectorAll(".user-flow-summary__item")[1]?.querySelector("strong");
            const fee = parseInt(localStorage.getItem("pickup_fee") || "0");
            const deliveryMethod = fee > 0 ? "Dijemput Kurir" : "Antar ke Gudang";
            if (deliverySummary) {
                deliverySummary.textContent = deliveryMethod;
            }

            // Total price
            const totalSummary = flowSummary.querySelector(".user-flow-summary__total strong");
            if (totalSummary) {
                totalSummary.textContent = rupiah(subtotal + fee);
            }
        }

        // Confirmation Modal Logic
        const modal = document.querySelector("[data-identity-modal]");
        const modalConfirmBtn = modal?.querySelector(".user-button--primary");
        const modalCancelBtns = modal?.querySelectorAll("[data-modal-close]");

        modalCancelBtns?.forEach((btn) => {
            btn.addEventListener("click", () => {
                if (modal) modal.hidden = true;
            });
        });

        let formDataToSubmit = null;

        identityForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const form = e.target;

            const nameVal = form.querySelector('input[name="full_name"]')?.value.trim() || "";
            const holderVal = form.querySelector('input[name="account_holder"]')?.value.trim() || "";
            const numberVal = form.querySelector('input[name="account_number"]')?.value.trim() || "";
            const waVal = form.querySelector('input[name="whatsapp"]')?.value.trim() || "";

            // 1. Validation: Name only letters and spaces
            const namePattern = /^[a-zA-Z\s]+$/;
            if (!namePattern.test(nameVal)) {
                showCustomAlert("Nama lengkap hanya boleh berisi huruf dan spasi!");
                return;
            }
            if (!namePattern.test(holderVal)) {
                showCustomAlert("Nama pemilik rekening hanya boleh berisi huruf dan spasi!");
                return;
            }

            // 2. Validation: Number only digits
            const numberPattern = /^[0-9]+$/;
            if (!numberPattern.test(numberVal)) {
                showCustomAlert("Nomor rekening hanya boleh berisi angka!");
                return;
            }
            if (!numberPattern.test(waVal)) {
                showCustomAlert("Nomor WhatsApp hanya boleh berisi angka!");
                return;
            }

            // 3. Validation: Name must match Account Holder name
            if (nameVal.toLowerCase() !== holderVal.toLowerCase()) {
                showCustomAlert("Nama lengkap penjual harus sama dengan nama pemilik rekening bank!");
                return;
            }

            const cityId = localStorage.getItem("pickup_city_id") || 1;
            const addressInput =
                document.querySelector('textarea[name="address"]') ||
                form.querySelector('input[name="full_name"]');

            const savedCartItems = JSON.parse(localStorage.getItem("pickup_cart") || "[]");
            const itemsPayload = savedCartItems.map(item => ({
                id: parseInt(item.id) || 1,
                quantity: parseInt(item.quantity) || 1
            }));
            const deliveryMethodVal = localStorage.getItem("pickup_fee") ? "courier" : "pickup";

            formDataToSubmit = {
                name: nameVal,
                phone_number: waVal,
                address:
                    localStorage.getItem("pickup_address") ||
                    nameVal + " - Surabaya",
                address_note: "Catatan penjemputan",
                banks_id: bankSelect ? parseInt(bankSelect.value) || 1 : 1,
                account_name: holderVal,
                account_number: numberVal,
                cities_id: parseInt(cityId) || 1,
                pickup_address:
                    localStorage.getItem("pickup_address") ||
                    "Jl. Raya Utama No. 12",
                pickup_address_note: "Rumah depan masjid",
                pickup_lat: parseFloat(localStorage.getItem("pickup_lat")) || -7.2575,
                pickup_long: parseFloat(localStorage.getItem("pickup_long")) || 112.7521,
                delivery_method: deliveryMethodVal,
                items: itemsPayload
            };

            if (modal) {
                modal.hidden = false;
                document.body.classList.add("overflow-hidden");
            } else {
                submitOrder(formDataToSubmit);
            }
        });

        if (modalConfirmBtn) {
            modalConfirmBtn.addEventListener("click", async (e) => {
                e.preventDefault();
                modalConfirmBtn.disabled = true;
                modalConfirmBtn.textContent = "Memproses...";
                if (formDataToSubmit) {
                    await submitOrder(formDataToSubmit);
                }
            });
        }

        async function submitOrder(payload) {
            const res = await fetchPublicApi("/orders", {
                method: "POST",
                body: JSON.stringify(payload),
            });

            if (res.data && res.data.order_id) {
                if (modal) modal.hidden = true;
                window.location.href = `/receipt?order_id=${res.data.order_id}`;
            } else {
                alert(res.message || "Gagal mengirim pesanan");
                if (modalConfirmBtn) {
                    modalConfirmBtn.disabled = false;
                    modalConfirmBtn.innerHTML =
                        'Sudah Benar <span aria-hidden="true">→</span>';
                }
            }
        }
    }

    // ── RECEIPT PAGE LOGIC ──
    const receiptContainer = document.querySelector("[data-receipt]");
    if (receiptContainer) {
        // Hide the switch status preview buttons for customer
        const switchToolbar = document.querySelector(".user-receipt-toolbar");
        if (switchToolbar) {
            switchToolbar.style.display = "none";
        }

        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get("order_id");

        if (orderId) {
            (async () => {
                const res = await fetchPublicApi(`/receipts/${orderId}`);
                if (res.data) {
                    const o = res.data;
                    const c = o.customer || {};
                    const b = c.bank || {};
                    const receipt = o.receipt || {};

                    // Header Meta
                    const metaMeta = receiptContainer.querySelector(
                        ".user-receipt__meta strong",
                    );
                    const metaDate = receiptContainer.querySelector(
                        ".user-receipt__meta small",
                    );
                    if (metaMeta) metaMeta.textContent = `#ORDER-${o.order_id}`;
                    if (metaDate)
                        metaDate.textContent = `Tanggal transaksi: ${new Date(o.created_at).toLocaleDateString("id-ID")}`;

                    // Badge
                    const badge = receiptContainer.querySelector(
                        "[data-receipt-badge]",
                    );
                    const orderStatus = receipt.status || "unpaid";
                    const isPaid = orderStatus === "paid";
                    if (badge) {
                        badge.textContent = orderStatus.toUpperCase();
                        badge.className = `user-receipt__status user-receipt__status--${isPaid ? "paid" : "unpaid"}`;
                    }

                    // Penjual Info
                    const blockPenjual = receiptContainer.querySelectorAll(
                        ".user-receipt__block",
                    )[0];
                    if (blockPenjual) {
                        const dds = blockPenjual.querySelectorAll("dd");
                        if (dds[0]) dds[0].textContent = c.name || "-";
                        if (dds[1]) dds[1].textContent = c.phone_number || "-";
                        if (dds[2]) dds[2].textContent = b.name || "-";
                        if (dds[3])
                            dds[3].textContent = `${c.account_number || "-"} (a.n ${c.account_name || "-"})`;
                        if (dds[4]) dds[4].textContent = c.address || "-";
                    }

                    // Penyerahan Info
                    const blockPenyerahan = receiptContainer.querySelectorAll(
                        ".user-receipt__block",
                    )[1];
                    
                    let subtotal = 0;
                    const itemsList = receipt.accus || [];
                    itemsList.forEach(item => {
                        subtotal += item.subtotal || 0;
                    });
                    const totalCost = receipt.price_owed || subtotal;
                    const deliveryCost = totalCost - subtotal;

                    if (blockPenyerahan) {
                        const dds = blockPenyerahan.querySelectorAll("dd");
                        if (dds[0])
                            dds[0].textContent = deliveryCost > 0 ? "Dijemput Kurir Indoprima" : "Antar ke Gudang";
                        if (dds[1])
                            dds[1].textContent = o.city ? o.city.name : "-";
                        if (dds[2]) dds[2].textContent = deliveryCost > 0 ? rupiah(deliveryCost) : "Gratis";
                        if (dds[3])
                             dds[3].textContent = o.pickup_address_note || "-";
                    }

                    // Populate Accu Items Table
                    const tableBody = receiptContainer.querySelector(".user-receipt__table tbody");
                    if (tableBody) {
                        if (itemsList.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="4"><div class="user-receipt__empty"><strong>Detail aki belum tersedia</strong><span>Item akan muncul setelah transaksi terhubung.</span></div></td></tr>';
                        } else {
                            tableBody.innerHTML = itemsList.map(item => `
                                <tr>
                                    <td>
                                        <strong>${item.name || "-"}</strong>
                                        <small>${item.brand || "-"}</small>
                                    </td>
                                    <td>${item.amount || 1} unit</td>
                                    <td>${rupiah(item.price || 0)}</td>
                                    <td><strong>${rupiah(item.subtotal || 0)}</strong></td>
                                </tr>
                            `).join("");
                        }
                    }

                    // Populate calculations
                    const summaryBlocks = receiptContainer.querySelector(".user-receipt__summary");
                    if (summaryBlocks) {
                        const divs = summaryBlocks.querySelectorAll("div");
                        if (divs[0]) divs[0].querySelector("strong").textContent = rupiah(subtotal);
                        if (divs[1]) divs[1].querySelector("strong").textContent = deliveryCost > 0 ? rupiah(deliveryCost) : "Gratis";
                        if (divs[2]) divs[2].querySelector("strong").textContent = "—";
                        
                        const grandTotalElement = receiptContainer.querySelector(".user-receipt__grand-total strong");
                        if (grandTotalElement) {
                            grandTotalElement.textContent = rupiah(totalCost);
                        }
                    }

                    // Show or hide proof section
                    const proofSection = document.querySelector("[data-proof-section]");
                    if (proofSection) {
                        if (isPaid) {
                            proofSection.removeAttribute("hidden");
                            proofSection.style.display = "block";
                            if (receipt.transfer) {
                                const transfer = receipt.transfer;
                                const dds = proofSection.querySelectorAll("dd");
                                if (dds[0]) dds[0].textContent = new Date(transfer.transfer_date).toLocaleDateString("id-ID");
                                if (dds[1]) dds[1].textContent = transfer.id || "-";
                                const img = proofSection.querySelector("img");
                                if (img && transfer.proof_image) {
                                    img.src = `/storage/${transfer.proof_image}`;
                                }
                            }
                        } else {
                            proofSection.setAttribute("hidden", "true");
                            proofSection.style.display = "none";
                        }
                    }
                }
            })();
        }
    }

    // Expose userCart Map globally to window if not already done, so we can access it
    if (typeof window.userCart === 'undefined') {
        window.userCart = new Map();
    }

    // ── COORDINATE PICKER MAP & NEAREST WAREHOUSE LOGIC ──
    const btnOpenUserMap = document.getElementById("btn-open-user-map");
    const modalUserMap = document.getElementById("modal-user-map");
    const btnSaveUserCoords = document.getElementById("btn-save-user-coords");
    const userCoordsBadge = document.getElementById("user-coords-badge");
    const userSelectedLat = document.getElementById("user-selected-lat");
    const userSelectedLng = document.getElementById("user-selected-lng");

    const userAddressInput = document.getElementById("user-address-input");
    const userCityInput = document.getElementById("user-city-input");
    const userZipInput = document.getElementById("user-zip-input");
    const checkoutSubmitBtn = document.getElementById("checkout-submit-btn");

    const nearestWarehouseInfo = document.getElementById("nearest-warehouse-info");
    const nearestWarehouseDetail = document.getElementById("nearest-warehouse-detail");

    let userLat = parseFloat(localStorage.getItem("pickup_lat")) || null;
    let userLng = parseFloat(localStorage.getItem("pickup_long")) || null;
    let userMap = null;
    let userMarker = null;
    let warehousesList = [];

    // Helper Haversine distance in km
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Fetch warehouses to use in distance calculation
    async function loadWarehouses() {
        const res = await fetchPublicApi("/storages");
        if (res.data) {
            warehousesList = res.data;
            if (userLat && userLng) {
                findAndDisplayNearestWarehouse();
            }
        }
    }
    loadWarehouses();

    function findAndDisplayNearestWarehouse() {
        if (!userLat || !userLng || !warehousesList.length) return;

        let nearest = null;
        let minDistance = Infinity;

        warehousesList.forEach(w => {
            const dist = calculateDistance(userLat, userLng, parseFloat(w.lat), parseFloat(w.long));
            if (dist < minDistance) {
                minDistance = dist;
                nearest = w;
            }
        });

        if (nearest) {
            if (userCoordsBadge && nearestWarehouseDetail) {
                userCoordsBadge.style.display = "block";
                nearestWarehouseDetail.innerHTML = `<strong>${nearest.name}</strong><br>${nearest.address}<br><span style="color:#2563eb; font-weight:700;">Jarak: ${minDistance.toFixed(2)} km</span>`;
            }
            localStorage.setItem("nearest_warehouse_name", nearest.name);
            localStorage.setItem("nearest_warehouse_address", nearest.address);
            localStorage.setItem("nearest_warehouse_distance", minDistance.toFixed(2));

            // Calculate delivery fee dynamically if courier is chosen
            updatePickupFee(minDistance);
        }
    }

    function updatePickupFee(distance) {
        const selectedMethod = document.querySelector('input[name="delivery_method"]:checked')?.value;
        const pickupLabel = document.getElementById("user-pickup-fee-label") || document.querySelector("[data-cart-pickup]");
        
        if (selectedMethod === "courier") {
            const fee = Math.max(10000, Math.round(distance * 2000));
            localStorage.setItem("pickup_fee", fee);
            if (pickupLabel) pickupLabel.textContent = rupiah(fee);
            
            // Trigger cart total recalculation
            recalculateTotal(fee);
        } else {
            localStorage.removeItem("pickup_fee");
            if (pickupLabel) pickupLabel.textContent = "Gratis";
            recalculateTotal(0);
        }
    }

    function recalculateTotal(fee) {
        const cartSubtotal = document.querySelector("[data-cart-subtotal]");
        const cartTotal = document.querySelector("[data-cart-total]");
        
        if (cartSubtotal) {
            let subVal = 0;
            const subText = cartSubtotal.textContent.replace(/[^\d]/g, "");
            if (subText) subVal = parseInt(subText);
            
            if (cartTotal && subVal > 0) {
                cartTotal.textContent = rupiah(subVal + fee);
            }
        }
    }

    // Monitor delivery method change to recalculate fee
    document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
        radio.addEventListener("change", () => {
            if (userLat && userLng) {
                findAndDisplayNearestWarehouse();
            }
        });
    });

    // Populate saved inputs from localStorage
    if (userAddressInput) userAddressInput.value = localStorage.getItem("pickup_address") || "";
    if (userCityInput) userCityInput.value = localStorage.getItem("pickup_city") || "";
    if (userZipInput) userZipInput.value = localStorage.getItem("pickup_zip") || "";

    if (userLat && userLng) {
        if (userCoordsBadge) userCoordsBadge.style.display = "block";
        if (userSelectedLat) userSelectedLat.textContent = userLat.toFixed(5);
        if (userSelectedLng) userSelectedLng.textContent = userLng.toFixed(5);
    }

    // Open User Map Picker
    btnOpenUserMap?.addEventListener("click", () => {
        if (modalUserMap) modalUserMap.style.display = "flex";
        
        // Load Leaflet dynamically if not loaded
        if (typeof L === "undefined") {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
            document.head.appendChild(link);

            const script = document.createElement("script");
            script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
            document.head.appendChild(script);

            script.onload = () => {
                initPickerMap();
            };
        } else {
            initPickerMap();
        }
    });

    function initPickerMap() {
        const defaultLat = userLat || -7.2575;
        const defaultLng = userLng || 112.7521;

        if (!userMap) {
            userMap = L.map("user-map-picker").setView([defaultLat, defaultLng], 12);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors"
            }).addTo(userMap);

            userMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(userMap);

            userMap.on("click", (e) => {
                userMarker.setLatLng(e.latlng);
            });
        } else {
            userMap.setView([defaultLat, defaultLng], 12);
            userMarker.setLatLng([defaultLat, defaultLng]);
            setTimeout(() => {
                userMap.invalidateSize();
            }, 200);
        }
    }

    // Save coords from picker map
    btnSaveUserCoords?.addEventListener("click", () => {
        if (userMarker) {
            const pos = userMarker.getLatLng();
            userLat = pos.lat;
            userLng = pos.lng;
            localStorage.setItem("pickup_lat", userLat);
            localStorage.setItem("pickup_long", userLng);

            if (userCoordsBadge) userCoordsBadge.style.display = "block";

            if (modalUserMap) modalUserMap.style.display = "none";
            findAndDisplayNearestWarehouse();
        }
    });

    // Validation & Submit button
    checkoutSubmitBtn?.addEventListener("click", (e) => {
        e.preventDefault();

        // 1. Validate Cart
        const cartSize = window.userCart ? window.userCart.size : 0;
        if (cartSize === 0) {
            showCustomAlert("Keranjang belanja kosong! Silakan tambahkan minimal satu aki ke keranjang sebelum melanjutkan.");
            return;
        }

        // 2. Validate Address, City, Zip
        const address = userAddressInput ? userAddressInput.value.trim() : "";
        const city = userCityInput ? userCityInput.value.trim() : "";
        const zip = userZipInput ? userZipInput.value.trim() : "";

        if (!address) {
            showCustomAlert("Harap isi alamat lengkap Anda.");
            if (userAddressInput) userAddressInput.focus();
            return;
        }
        if (!city) {
            showCustomAlert("Harap isi nama kota.");
            if (userCityInput) userCityInput.focus();
            return;
        }
        if (!zip) {
            showCustomAlert("Harap isi kode pos.");
            if (userZipInput) userZipInput.focus();
            return;
        }

        // 3. Validate Coordinates Map
        if (!userLat || !userLng) {
            showCustomAlert("Harap tentukan lokasi koordinat Anda di peta dengan menekan tombol peta.");
            return;
        }

        // Save valid fields to localStorage
        localStorage.setItem("pickup_address", address);
        localStorage.setItem("pickup_city", city);
        localStorage.setItem("pickup_zip", zip);
        localStorage.setItem("pickup_cart", JSON.stringify(Array.from(window.userCart.values())));

        // Redirect to identity details
        window.location.href = "/user/identitas";
    });
});
