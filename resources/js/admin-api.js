document.addEventListener("DOMContentLoaded", () => {
    const API_BASE = "/api/admin";
    const token = localStorage.getItem("admin_token");
    const user = JSON.parse(localStorage.getItem("admin_user") || "null");

    if (!token && window.location.pathname !== "/admin/login") {
        window.location.href = "/admin/login";
        return;
    }
    if (token && window.location.pathname === "/admin/login") {
        window.location.href = "/admin/dashboard";
        return;
    }

    if (user && document.getElementById("auth-user-name")) {
        document.getElementById("auth-user-name").innerText = user.name;
        document.getElementById("auth-user-initial").innerText = user.name
            .charAt(0)
            .toUpperCase();
    }

    const fetchApi = async (endpoint, options = {}) => {
        const headers = {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
        if (token) headers["Authorization"] = `Bearer ${token}`;
        const response = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers,
        });
        if (response.status === 401) {
            localStorage.removeItem("admin_token");
            localStorage.removeItem("admin_user");
            window.location.href = "/admin/login";
        }
        return response.json();
    };

    const rupiah = (n) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(n);

    window.showToast = (message, type = "success") => {
        const toast = document.getElementById("admin-toast");
        const msg = document.getElementById("admin-toast-message");
        const icon = document.getElementById("admin-toast-icon");
        if (!toast) return;

        msg.innerText = message;
        if (type === "error") {
            toast.style.background = "#ba1b2b";
            icon.innerText = "✕";
        } else if (type === "warning") {
            toast.style.background = "#f59e0b";
            icon.innerText = "⚠";
        } else {
            toast.style.background = "#10b981";
            icon.innerText = "✓";
        }
        toast.style.display = "flex";
        toast.style.opacity = "1";

        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => {
                toast.style.display = "none";
            }, 300);
        }, 3200);
    };

    window.showConfirm = (title, message, onOk) => {
        const modal = document.getElementById("modal-custom-confirm");
        if (!modal) {
            if (confirm(message)) onOk();
            return;
        }
        document.getElementById("confirm-title").innerText = title;
        document.getElementById("confirm-message").innerText = message;
        modal.style.display = "flex";

        const btnOk = document.getElementById("btn-confirm-ok");
        const btnCancel = document.getElementById("btn-confirm-cancel");

        btnOk.onclick = () => {
            modal.style.display = "none";
            onOk();
        };

        btnCancel.onclick = () => {
            modal.style.display = "none";
        };
    };

    const statusBadge = (status) => {
        const colors = {
            pending: { bg: "#fef3c7", color: "#92400e" },
            processing: { bg: "#dbeafe", color: "#1e40af" },
            completed: { bg: "#d1fae5", color: "#065f46" },
            cancelled: { bg: "#fee2e2", color: "#991b1b" },
        };
        const c = colors[status] || { bg: "#f3f4f6", color: "#374151" };
        return `<span style="padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; background:${c.bg}; color:${c.color}; text-transform:uppercase;">${status}</span>`;
    };

    // Logout Modal Konfirmasi dengan Password
    const btnLogout = document.getElementById("btn-logout");
    const modalLogout = document.getElementById("modal-logout-confirm");
    const formLogout = document.getElementById("form-logout-confirm");
    const logoutError = document.getElementById("logout-error");

    if (btnLogout && modalLogout) {
        btnLogout.addEventListener("click", (e) => {
            e.preventDefault();
            if (profileMenu) profileMenu.hidden = true;
            document.getElementById("logout-password").value = "";
            if (logoutError) logoutError.style.display = "none";
            modalLogout.style.display = "flex";
        });
    }

    if (formLogout) {
        formLogout.addEventListener("submit", async (e) => {
            e.preventDefault();
            const password = document.getElementById("logout-password").value;
            logoutError.style.display = "none";

            try {
                const res = await fetch("/api/logout", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify({ password }),
                });

                const data = await res.json();

                if (res.ok) {
                    localStorage.removeItem("admin_token");
                    localStorage.removeItem("admin_user");
                    window.location.href = "/admin/login";
                } else {
                    logoutError.innerText =
                        data.message ||
                        (data.errors?.password
                            ? data.errors.password[0]
                            : "Password salah.");
                    logoutError.style.display = "block";
                }
            } catch (err) {
                logoutError.innerText = "Terjadi kesalahan sistem";
                logoutError.style.display = "block";
            }
        });
    }

    const btnEditProfile = document.getElementById("btn-edit-profile");
    const modalEditProfile = document.getElementById("modal-edit-profile");
    const formEditProfile = document.getElementById("form-edit-profile");
    const profileError = document.getElementById("profile-error");

    if (btnEditProfile && modalEditProfile) {
        btnEditProfile.addEventListener("click", (e) => {
            e.preventDefault();
            if (profileMenu) profileMenu.hidden = true;
            const currentUser = JSON.parse(
                localStorage.getItem("admin_user") || "{}",
            );
            document.getElementById("profile-name").value =
                currentUser.name || "";
            document.getElementById("profile-current-password").value = "";
            document.getElementById("profile-new-password").value = "";
            if (profileError) profileError.style.display = "none";
            modalEditProfile.style.display = "flex";
        });
    }

    if (formEditProfile) {
        formEditProfile.addEventListener("submit", async (e) => {
            e.preventDefault();
            profileError.style.display = "none";

            const payload = {
                name: document.getElementById("profile-name").value,
                current_password: document.getElementById(
                    "profile-current-password",
                ).value,
                new_password: document.getElementById("profile-new-password")
                    .value,
            };

            const res = await fetchApi("/profile", {
                method: "PUT",
                body: JSON.stringify(payload),
            });

            if (res.user) {
                localStorage.setItem("admin_user", JSON.stringify(res.user));
                if (document.getElementById("auth-user-name")) {
                    document.getElementById("auth-user-name").innerText =
                        res.user.name;
                    document.getElementById("auth-user-initial").innerText =
                        res.user.name.charAt(0).toUpperCase();
                }
                modalEditProfile.style.display = "none";
                showToast("Profil berhasil diperbarui!", "success");
            } else {
                profileError.innerText =
                    res.message || "Gagal memperbarui profil.";
                profileError.style.display = "block";
            }
        });
    }

    const profileBtn = document.getElementById("admin-profile-btn");
    const profileMenu = document.getElementById("admin-profile-menu");
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener("click", () => {
            profileMenu.hidden = !profileMenu.hidden;
        });
    }

    const loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const btn = document.getElementById("btn-submit");
            const err = document.getElementById("login-error");
            btn.disabled = true;
            btn.innerText = "Memproses...";
            err.style.display = "none";
            try {
                const res = await fetch("/api/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        name: document.getElementById("name").value,
                        password: document.getElementById("password").value,
                    }),
                });
                const data = await res.json();
                if (res.ok && data.token) {
                    localStorage.setItem("admin_token", data.token);
                    localStorage.setItem(
                        "admin_user",
                        JSON.stringify(data.user),
                    );
                    window.location.href = "/admin/dashboard";
                } else {
                    err.innerText = data.message || "Login gagal";
                    err.style.display = "block";
                }
            } catch (error) {
                err.innerText = "Terjadi kesalahan jaringan";
                err.style.display = "block";
            }
            btn.disabled = false;
            btn.innerText = "Masuk";
        });
    }

    if (window.location.pathname === "/admin/dashboard") {
        (async () => {
            const res = await fetchApi("/dashboard-stats");
            if (!res.data) return;
            document.getElementById("stat-total-transactions").innerText =
                res.data.overview.total_transactions;
            document.getElementById("stat-pending-verifications").innerText =
                res.data.overview.pending_verifications;
            document.getElementById("stat-total-sales").innerText = rupiah(
                res.data.overview.total_sales,
            );
            document.getElementById("stat-avg-time").innerText =
                res.data.overview.avg_processing_time;

            const tbody = document.getElementById("attention-orders-tbody");
            if (!res.data.attention_orders.length) {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            } else {
                tbody.innerHTML = res.data.attention_orders
                    .map(
                        (o) => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.customer.name}</td>
                        <td>${o.city.name}</td>
                        <td>${new Date(o.created_at).toLocaleDateString("id-ID")}</td>
                        <td>${statusBadge(o.status)}</td>
                    </tr>`,
                    )
                    .join("");
            }

            const actList = document.getElementById("activity-list-container");
            const actEmpty = document.getElementById("activity-empty-state");
            const shipments = res.data.recent_activities.shipments || [];
            if (!shipments.length) {
                actEmpty.innerHTML = `<strong>Belum ada aktivitas</strong>`;
            } else {
                actEmpty.style.display = "none";
                actList.style.display = "flex";
                actList.innerHTML = shipments
                    .map(
                        (s) => `
                    <div style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                        <strong style="display:block; font-size:12px;">Pengiriman #${s.id}</strong>
                        <small style="color:#6d727c;">Ke Gudang ${s.warehouse.name} - ${s.status}</small>
                    </div>`,
                    )
                    .join("");
            }
        })();
    }

    if (window.location.pathname === "/admin/transaksi") {
        const uploadArea = document.getElementById("upload-area");
        const uploadInput = document.getElementById("upload-proof");
        const uploadPreview = document.getElementById("upload-preview");
        const uploadPlaceholder = document.getElementById("upload-placeholder");
        const containerCancelReason = document.getElementById(
            "container-cancel-reason",
        );
        const cancelReasonInput = document.getElementById("cancel-reason");
        const orderUpdateError = document.getElementById("order-update-error");

        if (uploadArea && uploadInput) {
            uploadArea.addEventListener("click", () => uploadInput.click());
            uploadInput.addEventListener("change", () => {
                const file = uploadInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        uploadPreview.src = e.target.result;
                        uploadPreview.style.display = "block";
                        uploadPlaceholder.style.display = "none";
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const loadOrders = async () => {
            const res = await fetchApi("/orders");
            const tbody = document.getElementById("orders-tbody");
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data
                    .map(
                        (o) => `
                    <tr>
                        <td style="font-weight:500;">${o.customer ? o.customer.name : "-"}</td>
                        <td>${o.city ? o.city.name : "-"}</td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${o.pickup_address}</td>
                        <td>${new Date(o.created_at).toLocaleDateString("id-ID")}</td>
                        <td>${statusBadge(o.status)}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="viewOrderDetail(${o.id})" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Detail</button>
                                <button onclick="editOrderStatus(${o.id}, '${o.status}')" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Update</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="6"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            }
        };
        loadOrders();

        window.editOrderStatus = (id, currentStatus) => {
            document.getElementById("update-order-id").value = id;
            if (orderUpdateError) orderUpdateError.style.display = "none";

            const radios = document.querySelectorAll(
                'input[name="order_status"]',
            );
            radios.forEach((r) => {
                r.checked = r.value === currentStatus;
                const card = r.closest("label");
                card.style.borderColor = r.checked ? "#3b82f6" : "#e5e7eb";
            });

            if (containerCancelReason) {
                containerCancelReason.style.display =
                    currentStatus === "cancelled" ? "block" : "none";
                if (cancelReasonInput) cancelReasonInput.value = "";
            }

            uploadPreview.style.display = "none";
            uploadPlaceholder.style.display = "block";
            uploadInput.value = "";
            document.getElementById("modal-update-order").style.display =
                "flex";
        };

        document
            .querySelectorAll('input[name="order_status"]')
            .forEach((radio) => {
                radio.addEventListener("change", () => {
                    if (orderUpdateError)
                        orderUpdateError.style.display = "none";
                    document
                        .querySelectorAll('input[name="order_status"]')
                        .forEach((r) => {
                            r.closest("label").style.borderColor = r.checked
                                ? "#3b82f6"
                                : "#e5e7eb";
                        });
                    if (containerCancelReason) {
                        containerCancelReason.style.display =
                            radio.value === "cancelled" ? "block" : "none";
                    }
                });
            });

        window.viewOrderDetail = async (id) => {
            const res = await fetchApi(`/orders/${id}`);
            if (!res.data) return;
            const o = res.data;
            const c = o.customer || {};
            const bankName = c.bank && c.bank.name ? c.bank.name : "-";
            document.getElementById("detail-customer-name").innerText =
                c.name || "-";
            document.getElementById("detail-customer-phone").innerText =
                c.phone_number || "-";
            document.getElementById("detail-customer-address").innerText =
                c.address || "-";
            document.getElementById("detail-customer-ktp").innerText =
                c.ktp || "-";
            document.getElementById("detail-customer-bank").innerText =
                `${bankName} - ${c.account_number || "-"} (a.n. ${c.account_name || "-"})`;
            document.getElementById("detail-order-city").innerText = o.city
                ? o.city.name
                : "-";
            document.getElementById("detail-order-status").innerHTML =
                statusBadge(o.status);
            document.getElementById("detail-order-time").innerText = new Date(
                o.created_at,
            ).toLocaleString("id-ID");
            document.getElementById("detail-order-pickup-address").innerText =
                o.pickup_address || "-";
            const noteLabelEl = document.getElementById(
                "detail-order-note-label",
            );
            if (o.status === "cancelled") {
                if (noteLabelEl) noteLabelEl.innerText = "Alasan Pembatalan";
                document.getElementById("detail-order-pickup-note").innerText =
                    o.cancel_reason || "-";
            } else {
                if (noteLabelEl) noteLabelEl.innerText = "Catatan Alamat";
                document.getElementById("detail-order-pickup-note").innerText =
                    o.pickup_address_note || "-";
            }
            document.getElementById("modal-detail-order").style.display =
                "flex";
        };

        const formUpdate = document.getElementById("form-update-order");
        if (formUpdate) {
            formUpdate.addEventListener("submit", async (e) => {
                e.preventDefault();
                if (orderUpdateError) orderUpdateError.style.display = "none";

                const id = document.getElementById("update-order-id").value;
                const selected = document.querySelector(
                    'input[name="order_status"]:checked',
                );
                if (!selected) {
                    if (orderUpdateError) {
                        orderUpdateError.innerText =
                            "Pilih status terlebih dahulu.";
                        orderUpdateError.style.display = "block";
                    }
                    return;
                }

                const statusVal = selected.value;
                const payload = { status: statusVal };

                // Validation for Cancelled
                if (statusVal === "cancelled") {
                    const reason = cancelReasonInput
                        ? cancelReasonInput.value.trim()
                        : "";
                    if (!reason) {
                        if (orderUpdateError) {
                            orderUpdateError.innerText =
                                "Wajib memasukkan alasan pembatalan!";
                            orderUpdateError.style.display = "block";
                        }
                        return;
                    }
                    payload.cancel_reason = reason;
                }

                if (statusVal === "completed") {
                    const hasFile =
                        uploadInput &&
                        uploadInput.files &&
                        uploadInput.files.length > 0;
                    const hasPreview =
                        uploadPreview && uploadPreview.style.display !== "none";
                    if (!hasFile && !hasPreview) {
                        if (orderUpdateError) {
                            orderUpdateError.innerText =
                                "Wajib mengunggah foto bukti pembayaran / penyerahan untuk status Completed!";
                            orderUpdateError.style.display = "block";
                        }
                        return;
                    }
                    payload.proof_image = "uploaded";
                }

                const res = await fetchApi(`/orders/${id}/status`, {
                    method: "PUT",
                    body: JSON.stringify(payload),
                });

                if (res.data) {
                    document.getElementById(
                        "modal-update-order",
                    ).style.display = "none";
                    showToast("Status pesanan berhasil diperbarui!", "success");
                    loadOrders();
                } else {
                    if (orderUpdateError) {
                        orderUpdateError.innerText =
                            res.message || "Gagal memperbarui status order.";
                        orderUpdateError.style.display = "block";
                    }
                }
            });
        }
    }

    if (window.location.pathname === "/admin/harga") {
        let cachedCities = [];
        let cachedAccus = [];
        let activeCityId = null;
        let activeCityName = "";

        const loadCities = async () => {
            const res = await fetchApi("/cities");
            cachedCities = res.data || [];
            const tbody = document.getElementById("cities-tbody");
            if (cachedCities.length) {
                tbody.innerHTML = cachedCities
                    .map(
                        (c) => `
                    <tr>
                        <td style="font-weight:500;">${c.name}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="viewCityAccus(${c.id}, '${c.name}')" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Detail</button>
                                <button onclick="deleteCity(${c.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="2"><div class="admin-table-empty"><strong>Belum ada kota</strong></div></td></tr>`;
            }
            const sel = document.getElementById("set-price-city");
            if (sel)
                sel.innerHTML = cachedCities
                    .map((c) => `<option value="${c.id}">${c.name}</option>`)
                    .join("");
        };

        const loadAccus = async () => {
            const res = await fetchApi("/accus");
            cachedAccus = res.data || [];
            const tbody = document.getElementById("accus-tbody");
            if (cachedAccus.length) {
                tbody.innerHTML = cachedAccus
                    .map(
                        (a) => `
                    <tr>
                        <td style="font-weight:500;">${a.brand}</td>
                        <td>${a.name}</td>
                        <td>
                            <button onclick="deleteAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada aki</strong></div></td></tr>`;
            }
            const sel = document.getElementById("set-price-accu");
            if (sel)
                sel.innerHTML = cachedAccus
                    .map(
                        (a) =>
                            `<option value="${a.id}">${a.brand} - ${a.name}</option>`,
                    )
                    .join("");
        };

        loadCities();
        loadAccus();

        window.viewCityAccus = async (cityId, cityName) => {
            activeCityId = cityId;
            activeCityName = cityName;

            const modalTitle = document.getElementById(
                "city-price-modal-title",
            );
            if (modalTitle)
                modalTitle.innerText = `Daftar Harga Aki: ${cityName}`;

            document.getElementById("modal-view-city-prices").style.display =
                "flex";

            const res = await fetchApi(`/cities/${cityId}/accus`);
            const tbody = document.getElementById("modal-city-accus-tbody");
            if (res.data && res.data.accus && res.data.accus.length) {
                tbody.innerHTML = res.data.accus
                    .map(
                        (a) => `
                    <tr>
                        <td style="font-weight:500;">${a.brand}</td>
                        <td>${a.name}</td>
                        <td style="font-weight:600; color:#10b981;">${rupiah(a.price)}</td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button onclick="editCityAccuPrice(${cityId}, ${a.id}, ${a.price})" class="admin-button admin-button--primary" style="height:28px; font-size:11px;">Edit</button>
                                <button onclick="deleteCityAccu(${cityId}, ${a.id})" class="admin-button admin-button--secondary" style="height:28px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada harga diset untuk kota ${cityName}</strong></div></td></tr>`;
            }
        };

        const toTitleCase = (str) => {
            if (!str) return "";
            return str
                .toLowerCase()
                .split(" ")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" ");
        };

        window.openAddPriceForCurrentCity = () => {
            const citySel = document.getElementById("set-price-city");
            const accuSel = document.getElementById("set-price-accu");
            if (citySel) {
                if (activeCityId) citySel.value = activeCityId;
                citySel.disabled = false;
            }
            if (accuSel) accuSel.disabled = false;
            document.getElementById("set-price-modal-head").innerText =
                "Tambah Harga Aki";
            document.getElementById("modal-set-price").style.display = "flex";
        };

        window.editCityAccuPrice = (cityId, accuId, currentPrice) => {
            const citySel = document.getElementById("set-price-city");
            const accuSel = document.getElementById("set-price-accu");
            const priceVal = document.getElementById("set-price-value");
            if (citySel) {
                citySel.value = cityId;
                citySel.disabled = true;
            }
            if (accuSel) {
                accuSel.value = accuId;
                accuSel.disabled = true;
            }
            if (priceVal) priceVal.value = currentPrice;

            document.getElementById("set-price-modal-head").innerText =
                "Edit Harga Aki";
            document.getElementById("modal-set-price").style.display = "flex";
        };

        window.deleteCity = (id) => {
            showConfirm(
                "Hapus Kota",
                "Yakin ingin menghapus kota ini?",
                async () => {
                    await fetchApi(`/cities/${id}`, { method: "DELETE" });
                    showToast("Kota berhasil dihapus", "success");
                    loadCities();
                },
            );
        };

        window.deleteAccu = (id) => {
            showConfirm(
                "Hapus Aki",
                "Yakin ingin menghapus jenis aki ini?",
                async () => {
                    await fetchApi(`/accus/${id}`, { method: "DELETE" });
                    showToast("Aki berhasil dihapus", "success");
                    loadAccus();
                },
            );
        };

        window.deleteCityAccu = (cityId, accuId) => {
            showConfirm(
                "Hapus Harga",
                "Yakin ingin menghapus harga aki ini dari kota?",
                async () => {
                    await fetchApi(`/cities/${cityId}/accus/${accuId}`, {
                        method: "DELETE",
                    });
                    showToast("Harga aki berhasil dihapus", "success");
                    viewCityAccus(activeCityId, activeCityName);
                },
            );
        };

        const cityNameInput = document.getElementById("city-name");
        if (cityNameInput) {
            cityNameInput.addEventListener("blur", () => {
                cityNameInput.value = toTitleCase(cityNameInput.value);
            });
        }

        document
            .getElementById("form-add-city")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                const rawName = document
                    .getElementById("city-name")
                    .value.trim();
                if (!rawName) return;
                const formattedName = toTitleCase(rawName);
                document.getElementById("city-name").value = formattedName;

                const isDuplicate = cachedCities.some(
                    (c) =>
                        c.name.trim().toLowerCase() ===
                        formattedName.toLowerCase(),
                );
                if (isDuplicate) {
                    showToast(
                        `Nama kota "${formattedName}" sudah ada!`,
                        "warning",
                    );
                    return;
                }

                const res = await fetchApi("/cities", {
                    method: "POST",
                    body: JSON.stringify({
                        name: formattedName,
                    }),
                });
                if (res && res.data) {
                    document.getElementById("modal-add-city").style.display =
                        "none";
                    document.getElementById("form-add-city").reset();
                    showToast("Kota berhasil ditambahkan!", "success");
                    loadCities();
                } else {
                    showToast(
                        res.message ||
                            (res.errors && res.errors.name
                                ? res.errors.name[0]
                                : "Gagal menambahkan kota"),
                        "error",
                    );
                }
            });

        document
            .getElementById("form-add-accu")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                await fetchApi("/accus", {
                    method: "POST",
                    body: JSON.stringify({
                        brand: document.getElementById("accu-brand").value,
                        name: document.getElementById("accu-name").value,
                    }),
                });
                document.getElementById("modal-add-accu").style.display =
                    "none";
                document.getElementById("form-add-accu").reset();
                showToast("Aki baru berhasil ditambahkan!", "success");
                loadAccus();
            });

        document
            .getElementById("form-set-price")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                const cityId = document.getElementById("set-price-city").value;
                await fetchApi(`/cities/${cityId}/accus`, {
                    method: "POST",
                    body: JSON.stringify({
                        accus_id:
                            document.getElementById("set-price-accu").value,
                        price: document.getElementById("set-price-value").value,
                    }),
                });
                document.getElementById("modal-set-price").style.display =
                    "none";
                document.getElementById("form-set-price").reset();
                const citySelReset = document.getElementById("set-price-city");
                const accuSelReset = document.getElementById("set-price-accu");
                if (citySelReset) citySelReset.disabled = false;
                if (accuSelReset) accuSelReset.disabled = false;
                showToast("Harga aki berhasil disimpan!", "success");
                if (activeCityId && activeCityId == cityId) {
                    viewCityAccus(activeCityId, activeCityName);
                }
            });
    }

    if (window.location.pathname === "/admin/gudang") {
        let addMarker = null;

        const addMap = L.map("map-add").setView([-7.2575, 112.7521], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(addMap);
        window.addMap = addMap;

        addMap.on("click", (e) => {
            const { lat, lng } = e.latlng;
            document.getElementById("storage-lat").value = lat.toFixed(8);
            document.getElementById("storage-long").value = lng.toFixed(8);
            document.getElementById("map-coords").innerText =
                `Lat: ${lat.toFixed(6)}, Long: ${lng.toFixed(6)}`;

            if (addMarker) addMap.removeLayer(addMarker);
            addMarker = L.marker([lat, lng]).addTo(addMap);
        });

        let viewMap = null;

        window.showStorageMap = (name, lat, lng) => {
            document.getElementById("view-map-title").innerText =
                `Lokasi: ${name}`;
            document.getElementById("modal-view-map").style.display = "flex";

            setTimeout(() => {
                if (viewMap) {
                    viewMap.remove();
                    viewMap = null;
                }
                viewMap = L.map("map-view").setView([lat, lng], 15);
                L.tileLayer(
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                    {
                        attribution: "&copy; OpenStreetMap contributors",
                    },
                ).addTo(viewMap);
                L.marker([lat, lng])
                    .addTo(viewMap)
                    .bindPopup(`<strong>${name}</strong>`)
                    .openPopup();
            }, 200);
        };

        const loadStorages = async () => {
            const res = await fetchApi("/storages");
            const tbody = document.getElementById("storages-tbody");
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data
                    .map(
                        (s) => `
                    <tr>
                        <td style="font-weight:500;">${s.name}</td>
                        <td>${s.address || "-"}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button onclick="showStorageMap('${s.name}', ${s.lat}, ${s.long})" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Lihat Peta</button>
                                <button onclick="deleteStorage(${s.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada gudang</strong></div></td></tr>`;
            }
        };
        loadStorages();

        document
            .getElementById("form-add-storage")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                const lat = document.getElementById("storage-lat").value;
                const lng = document.getElementById("storage-long").value;
                if (!lat || !lng) {
                    showToast(
                        "Pilih lokasi di peta terlebih dahulu",
                        "warning",
                    );
                    return;
                }
                await fetchApi("/storages", {
                    method: "POST",
                    body: JSON.stringify({
                        name: document.getElementById("storage-name").value,
                        address:
                            document.getElementById("storage-address").value,
                        lat: lat,
                        long: lng,
                    }),
                });
                document.getElementById("modal-add-storage").style.display =
                    "none";
                document.getElementById("form-add-storage").reset();
                document.getElementById("map-coords").innerText =
                    "Belum ada titik dipilih";
                if (addMarker) {
                    addMap.removeLayer(addMarker);
                    addMarker = null;
                }
                showToast("Gudang berhasil ditambahkan!", "success");
                loadStorages();
            });

        window.deleteStorage = (id) => {
            showConfirm(
                "Hapus Gudang",
                "Yakin ingin menghapus gudang ini?",
                async () => {
                    const res = await fetchApi(`/storages/${id}`, {
                        method: "DELETE",
                    });
                    if (res && res.message && !res.errors) {
                        showToast(res.message, "success");
                        loadStorages();
                    } else {
                        showToast(
                            res.message || "Gagal menghapus gudang",
                            "error",
                        );
                    }
                },
            );
        };
    }

    if (window.location.pathname === "/admin/pengguna") {
        const loadUsers = async () => {
            const res = await fetchApi("/users");
            const tbody = document.getElementById("users-tbody");
            if (res.data && res.data.length) {
                tbody.innerHTML = res.data
                    .map(
                        (u) => `
                    <tr>
                        <td style="font-weight:500;">${u.name}</td>
                        <td>${new Date(u.created_at).toLocaleDateString("id-ID")}</td>
                        <td>
                            <button onclick="deleteUser(${u.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Belum ada pengguna</strong></div></td></tr>`;
            }
        };
        loadUsers();

        document
            .getElementById("form-add-user")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                const payload = {
                    name: document.getElementById("user-name").value,
                    password: document.getElementById("user-password").value,
                };
                const idVal = document.getElementById("user-id").value;
                payload.id = idVal
                    ? idVal
                    : Math.floor(Math.random() * 1000000);
                await fetchApi("/users", {
                    method: "POST",
                    body: JSON.stringify(payload),
                });
                document.getElementById("modal-add-user").style.display =
                    "none";
                document.getElementById("form-add-user").reset();
                showToast("Staf admin berhasil ditambahkan!", "success");
                loadUsers();
            });

        window.deleteUser = (id) => {
            showConfirm(
                "Hapus Pengguna",
                "Yakin ingin menghapus staf admin ini?",
                async () => {
                    await fetchApi(`/users/${id}`, { method: "DELETE" });
                    showToast("Staf admin berhasil dihapus", "success");
                    loadUsers();
                },
            );
        };
    }
});
