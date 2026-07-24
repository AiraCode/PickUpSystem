document.addEventListener("DOMContentLoaded", () => {
    const API_BASE = "/api/admin";
    const token = localStorage.getItem("admin_token");
    const user = JSON.parse(localStorage.getItem("admin_user") || "null");

    if (!token && window.location.pathname !== "/admin/login") {
        if (window.location.pathname !== "/admin/pengguna") {
            window.location.href = "/admin/login";
            return;
        }
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

    // Live Real-Time Clock Ticker Widget
    const clockEl = document.getElementById("admin-live-clock");
    if (clockEl) {
        const updateClock = () => {
            const now = new Date();
            const dateStr = now.toLocaleDateString("id-ID", {
                weekday: "short",
                day: "numeric",
                month: "short",
                year: "numeric",
            });
            const timeStr = now.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });
            clockEl.innerHTML = `<span style="font-weight:500; color:#475569;">${dateStr}</span> <span style="color:#2563eb; font-weight:700;">${timeStr} WIB</span>`;
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    const fetchApi = async (endpoint, options = {}) => {
        const headers = {
            Accept: "application/json",
        };
        if (!(options.body instanceof FormData)) {
            headers["Content-Type"] = "application/json";
        }
        if (token) {
            headers["Authorization"] = `Bearer ${token}`;
        }
        if (sessionStorage.getItem("easter_egg_unlocked") === "true") {
            const secretPass = sessionStorage.getItem("easter_egg_pass") || "";
            headers["X-Easter-Egg-Pass"] = secretPass;
        }

        let apiTarget = `${API_BASE}${endpoint}`;
        if (endpoint.startsWith("/users")) {
            apiTarget = `/api/public-admin${endpoint}`;
        }

        const response = await fetch(apiTarget, {
            ...options,
            headers,
        });
        if (response.status === 401 && token) {
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
        const isDark = document.documentElement.classList.contains("admin-dark-mode");
        const colors = isDark ? {
            pending: { bg: "rgba(245, 158, 11, 0.25)", color: "#fbbf24" },
            processing: { bg: "rgba(59, 130, 246, 0.25)", color: "#60a5fa" },
            completed: { bg: "rgba(16, 185, 129, 0.25)", color: "#34d399" },
            cancelled: { bg: "rgba(239, 68, 68, 0.25)", color: "#f87171" },
        } : {
            pending: { bg: "#fef3c7", color: "#92400e" },
            processing: { bg: "#dbeafe", color: "#1e40af" },
            completed: { bg: "#d1fae5", color: "#065f46" },
            cancelled: { bg: "#fee2e2", color: "#991b1b" },
        };
        const c = colors[status] || (isDark ? { bg: "rgba(148, 163, 184, 0.25)", color: "#cbd5e1" } : { bg: "#f3f4f6", color: "#374151" });
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
        const periodSelect = document.getElementById("dashboard-period-select");

        const loadDashboardStats = async (period = "7days") => {
            const res = await fetchApi(`/dashboard-stats?period=${period}`);
            if (!res || !res.data) return;

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
            if (!res.data.attention_orders || !res.data.attention_orders.length) {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            } else {
                tbody.innerHTML = res.data.attention_orders
                    .map(
                        (o) => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.customer ? o.customer.name : "-"}</td>
                        <td>${o.city ? o.city.name : "-"}</td>
                        <td>${new Date(o.created_at).toLocaleDateString("id-ID")}</td>
                        <td>${statusBadge(o.status)}</td>
                    </tr>`,
                    )
                    .join("");
            }

            const actList = document.getElementById("activity-list-container");
            const actEmpty = document.getElementById("activity-empty-state");
            const shipments = res.data.recent_activities?.shipments || [];
            if (!shipments.length) {
                if (actEmpty) actEmpty.innerHTML = `<strong>Belum ada aktivitas</strong>`;
            } else {
                if (actEmpty) actEmpty.style.display = "none";
                if (actList) {
                    actList.style.display = "flex";
                    actList.innerHTML = shipments
                        .map(
                            (s) => `
                        <div style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                            <strong style="display:block; font-size:12px;">Pengiriman #${s.id}</strong>
                            <small style="color:#6d727c;">Ke Gudang ${s.warehouse ? s.warehouse.name : "-"} - ${s.status}</small>
                        </div>`,
                        )
                        .join("");
                }
            }

            const chartContainer = document.getElementById("chart-container");
            const chartEmpty = document.getElementById("chart-empty-state");
            const chartData = res.data.activity_chart?.data || [];

            if (chartContainer) {
                if (!chartData.length) {
                    if (chartEmpty) chartEmpty.style.display = "flex";
                    chartContainer.style.display = "none";
                } else {
                    if (chartEmpty) chartEmpty.style.display = "none";
                    chartContainer.style.display = "block";
                    chartContainer.style.border = "none";
                    chartContainer.style.padding = "0";

                    const maxCount = Math.max(...chartData.map((d) => d.count), 1);

                    const barsHtml = chartData
                        .map((d) => {
                            const pct = Math.max(Math.round((d.count / maxCount) * 100), 4);
                            const countDisplay = d.count > 0 ? `<span style="font-size:11px; font-weight:700; color:#2563eb; margin-bottom:6px;">${d.count}</span>` : `<span style="font-size:10px; color:#cbd5e1; margin-bottom:6px;">0</span>`;
                            const barBg = d.count > 0 ? "linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%)" : "#e2e8f0";
                            const barShadow = d.count > 0 ? "0 4px 10px rgba(37,99,235,0.2)" : "none";

                            return `
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; min-width:0;" title="${d.label}: ${d.count} transaksi">
                            ${countDisplay}
                            <div style="width:100%; max-width:${chartData.length > 20 ? '16px' : '38px'}; height:${pct}%; background:${barBg}; border-radius:6px 6px 0 0; transition: height 0.3s ease; box-shadow:${barShadow};"></div>
                            <span style="font-size:${chartData.length > 20 ? '9px' : '11px'}; color:#64748b; font-weight:600; margin-top:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;">${d.label}</span>
                        </div>`;
                        })
                        .join("");

                    chartContainer.innerHTML = `
                        <div style="height:230px; border:1px solid #f1f5f9; border-radius:10px; background:#fafafa; padding:24px 16px 12px 16px; position:relative; overflow:hidden;">
                            <div style="position:absolute; inset:24px 16px 30px 16px; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none; z-index:0;">
                                <div style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                                <div style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                                <div style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                            </div>
                            <div style="display:flex; align-items:flex-end; justify-content:space-between; width:100%; height:100%; position:relative; z-index:1; gap:${chartData.length > 20 ? '3px' : '12px'};">
                                ${barsHtml}
                            </div>
                        </div>
                    `;
                }
            }
        };

        loadDashboardStats();

        if (periodSelect) {
            periodSelect.addEventListener("change", (e) => {
                loadDashboardStats(e.target.value);
            });
        }
    }

    if (window.location.pathname === "/admin/transaksi") {
        let activeStatus = "pending";
        let searchQuery = "";
        let cityFilter = "";
        let bankFilter = "";
        let dateStartFilter = "";
        let dateEndFilter = "";
        let sortOrder = "desc";
        let currentDetailOrder = null;
        let orderMap = null;
        let orderMarker = null;

        const searchInput = document.getElementById("order-search-input");
        const btnOpenFilterModal = document.getElementById("btn-open-filter-modal");
        const modalShopeeFilter = document.getElementById("modal-shopee-filter");
        const formShopeeFilter = document.getElementById("form-shopee-filter");
        const filterCitySelect = document.getElementById("filter-city-select");
        const filterBankSelect = document.getElementById("filter-bank-select");
        const filterDateStart = document.getElementById("filter-date-start");
        const filterDateEnd = document.getElementById("filter-date-end");
        const filterSortSelect = document.getElementById("filter-sort-select");
        const btnResetShopeeFilter = document.getElementById("btn-reset-shopee-filter");
        const filterActiveCount = document.getElementById("filter-active-count");
        const activeFiltersBar = document.getElementById("active-filters-bar");
        const activeFilterTags = document.getElementById("active-filter-tags");

        const btnResetFilter = document.getElementById("btn-reset-order-filter");
        const activeBadge = document.getElementById("active-tab-badge");
        const uploadArea = document.getElementById("upload-area");
        const uploadInput = document.getElementById("upload-proof");
        const uploadPreview = document.getElementById("upload-preview");
        const uploadPlaceholder = document.getElementById("upload-placeholder");
        const containerCancelReason = document.getElementById("container-cancel-reason");
        const cancelReasonInput = document.getElementById("cancel-reason");
        const orderUpdateError = document.getElementById("order-update-error");

        const loadFilterOptions = async () => {
            const [citiesRes, banksRes] = await Promise.all([
                fetchApi("/cities"),
                fetchApi("/banks")
            ]);
            if (filterCitySelect && citiesRes.data) {
                filterCitySelect.innerHTML = `<option value="">Semua Kota</option>` +
                    citiesRes.data.map((c) => `<option value="${c.id}">${c.name}</option>`).join("");
            }
            if (filterBankSelect && banksRes.data) {
                filterBankSelect.innerHTML = `<option value="">Semua Bank</option>` +
                    banksRes.data.map((b) => `<option value="${b.id}">${b.name}</option>`).join("");
            }
        };
        loadFilterOptions();

        const updateActiveFilterDisplay = () => {
            let activeCount = 0;
            let tags = [];

            if (cityFilter && filterCitySelect) {
                activeCount++;
                const selectedText = filterCitySelect.options[filterCitySelect.selectedIndex]?.text || "Kota";
                tags.push(`Kota: ${selectedText}`);
            }
            if (bankFilter && filterBankSelect) {
                activeCount++;
                const selectedText = filterBankSelect.options[filterBankSelect.selectedIndex]?.text || "Bank";
                tags.push(`Bank: ${selectedText}`);
            }
            if (dateStartFilter) {
                activeCount++;
                tags.push(`Mulai: ${dateStartFilter}`);
            }
            if (dateEndFilter) {
                activeCount++;
                tags.push(`Sampai: ${dateEndFilter}`);
            }
            if (sortOrder === "asc") {
                activeCount++;
                tags.push(`Urutan: Terlama`);
            }

            if (filterActiveCount) {
                if (activeCount > 0) {
                    filterActiveCount.innerText = activeCount;
                    filterActiveCount.style.display = "inline-block";
                } else {
                    filterActiveCount.style.display = "none";
                }
            }

            if (activeFiltersBar && activeFilterTags) {
                if (tags.length > 0) {
                    activeFiltersBar.style.display = "flex";
                    activeFilterTags.innerHTML = tags.map((t) => `
                        <span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:12px; font-weight:600; font-size:11px;">${t}</span>
                    `).join("");
                } else {
                    activeFiltersBar.style.display = "none";
                }
            }
        };

        const loadOrders = async () => {
            const tbody = document.getElementById("orders-tbody");
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="7"><div class="admin-table-empty"><strong>Memuat data pesanan...</strong></div></td></tr>`;
            }

            let queryParams = [];
            if (searchQuery) {
                queryParams.push(`search=${encodeURIComponent(searchQuery)}`);
            } else {
                if (activeStatus) queryParams.push(`status=${activeStatus}`);
            }
            if (cityFilter) queryParams.push(`city_id=${cityFilter}`);
            if (bankFilter) queryParams.push(`bank_id=${bankFilter}`);
            if (dateStartFilter) queryParams.push(`date_start=${dateStartFilter}`);
            if (dateEndFilter) queryParams.push(`date_end=${dateEndFilter}`);
            if (sortOrder) queryParams.push(`sort=${sortOrder}`);

            const url = `/orders?${queryParams.join("&")}`;
            const res = await fetchApi(url);

            if (res.counts) {
                const c = res.counts;
                if (document.getElementById("count-pending")) document.getElementById("count-pending").innerText = c.pending.toLocaleString("id-ID");
                if (document.getElementById("count-processing")) document.getElementById("count-processing").innerText = c.processing.toLocaleString("id-ID");
                if (document.getElementById("count-completed")) document.getElementById("count-completed").innerText = c.completed.toLocaleString("id-ID");
                if (document.getElementById("count-cancelled")) document.getElementById("count-cancelled").innerText = c.cancelled.toLocaleString("id-ID");
                if (document.getElementById("count-all")) document.getElementById("count-all").innerText = c.all.toLocaleString("id-ID");
            }

            if (activeBadge) {
                const isDark = document.documentElement.classList.contains("admin-dark-mode");
                if (searchQuery) {
                    activeBadge.innerText = "HASIL PENCARIAN (SEMUA STATUS)";
                    activeBadge.style.background = isDark ? "rgba(59, 130, 246, 0.25)" : "#dbeafe";
                    activeBadge.style.color = isDark ? "#60a5fa" : "#1e40af";
                } else {
                    const statusLabels = isDark ? {
                        pending: { text: "PENDING", bg: "rgba(245, 158, 11, 0.25)", color: "#fbbf24" },
                        processing: { text: "PROCESSING", bg: "rgba(59, 130, 246, 0.25)", color: "#60a5fa" },
                        completed: { text: "COMPLETED", bg: "rgba(16, 185, 129, 0.25)", color: "#34d399" },
                        cancelled: { text: "CANCELLED", bg: "rgba(239, 68, 68, 0.25)", color: "#f87171" },
                        all: { text: "SEMUA TRANSAKSI", bg: "rgba(148, 163, 184, 0.25)", color: "#cbd5e1" },
                    } : {
                        pending: { text: "PENDING", bg: "#fef3c7", color: "#92400e" },
                        processing: { text: "PROCESSING", bg: "#dbeafe", color: "#1e40af" },
                        completed: { text: "COMPLETED", bg: "#d1fae5", color: "#065f46" },
                        cancelled: { text: "CANCELLED", bg: "#fee2e2", color: "#991b1b" },
                        all: { text: "SEMUA TRANSAKSI", bg: "#f3f4f6", color: "#374151" },
                    };
                    const st = statusLabels[activeStatus] || (isDark ? { text: activeStatus.toUpperCase(), bg: "rgba(148, 163, 184, 0.25)", color: "#cbd5e1" } : { text: activeStatus.toUpperCase(), bg: "#f3f4f6", color: "#374151" });
                    activeBadge.innerText = st.text;
                    activeBadge.style.background = st.bg;
                    activeBadge.style.color = st.color;
                }
            }

            if (res.data && res.data.length) {
                tbody.innerHTML = res.data
                    .map(
                        (o) => `
                    <tr>
                        <td style="font-weight:600; color:#3b82f6;">#${o.id}</td>
                        <td style="font-weight:500;">${o.customer ? o.customer.name : "-"}</td>
                        <td>${o.city ? o.city.name : "-"}</td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${o.pickup_address || "-"}</td>
                        <td>${new Date(o.created_at).toLocaleDateString("id-ID")}</td>
                        <td>${statusBadge(o.status)}</td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button onclick="viewOrderDetail(${o.id})" class="admin-button admin-button--primary" style="height:30px; font-size:11px;">Detail</button>
                                <button onclick="editOrderStatus(${o.id}, '${o.status}')" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Update</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="7"><div class="admin-table-empty"><strong>Tidak ada pesanan ditemukan</strong></div></td></tr>`;
            }
        };

        window.switchOrderTab = (status) => {
            activeStatus = status;
            searchQuery = "";
            if (searchInput) searchInput.value = "";

            const isDark = document.documentElement.classList.contains("admin-dark-mode");

            document.querySelectorAll(".order-status-tab").forEach((card) => {
                card.classList.remove("active");
                card.style.borderColor = isDark ? "#334155" : "#e5e7eb";
                card.style.background = isDark ? "#1e293b" : "#ffffff";
            });

            const activeCard = document.getElementById(`card-status-${status}`);
            if (activeCard) {
                activeCard.classList.add("active");
                const cardColors = isDark ? {
                    pending: { border: "#f59e0b", bg: "rgba(245, 158, 11, 0.18)" },
                    processing: { border: "#3b82f6", bg: "rgba(59, 130, 246, 0.18)" },
                    completed: { border: "#10b981", bg: "rgba(16, 185, 129, 0.18)" },
                    cancelled: { border: "#ef4444", bg: "rgba(239, 68, 68, 0.18)" },
                    all: { border: "#94a3b8", bg: "rgba(148, 163, 184, 0.18)" },
                } : {
                    pending: { border: "#f59e0b", bg: "#fffbeb" },
                    processing: { border: "#3b82f6", bg: "#eff6ff" },
                    completed: { border: "#10b981", bg: "#ecfdf5" },
                    cancelled: { border: "#ef4444", bg: "#fef2f2" },
                    all: { border: "#6b7280", bg: "#f9fafb" },
                };
                const c = cardColors[status] || (isDark ? { border: "#3b82f6", bg: "rgba(59, 130, 246, 0.18)" } : { border: "#3b82f6", bg: "#eff6ff" });
                activeCard.style.borderColor = c.border;
                activeCard.style.background = c.bg;
            }

            loadOrders();
        };

        let searchTimeout = null;
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchQuery = e.target.value.trim();
                    loadOrders();
                }, 300);
            });
        }

        if (btnOpenFilterModal && modalShopeeFilter) {
            btnOpenFilterModal.addEventListener("click", () => {
                modalShopeeFilter.style.display = "flex";
            });
        }

        if (formShopeeFilter) {
            formShopeeFilter.addEventListener("submit", (e) => {
                e.preventDefault();
                cityFilter = filterCitySelect ? filterCitySelect.value : "";
                bankFilter = filterBankSelect ? filterBankSelect.value : "";
                dateStartFilter = filterDateStart ? filterDateStart.value : "";
                dateEndFilter = filterDateEnd ? filterDateEnd.value : "";
                sortOrder = filterSortSelect ? filterSortSelect.value : "desc";
                if (modalShopeeFilter) modalShopeeFilter.style.display = "none";
                updateActiveFilterDisplay();
                loadOrders();
            });
        }

        const resetAllFilters = () => {
            searchQuery = "";
            cityFilter = "";
            bankFilter = "";
            dateStartFilter = "";
            dateEndFilter = "";
            sortOrder = "desc";
            if (searchInput) searchInput.value = "";
            if (filterCitySelect) filterCitySelect.value = "";
            if (filterBankSelect) filterBankSelect.value = "";
            if (filterDateStart) filterDateStart.value = "";
            if (filterDateEnd) filterDateEnd.value = "";
            if (filterSortSelect) filterSortSelect.value = "desc";
            updateActiveFilterDisplay();
        };

        if (btnResetShopeeFilter) {
            btnResetShopeeFilter.addEventListener("click", () => {
                resetAllFilters();
                if (modalShopeeFilter) modalShopeeFilter.style.display = "none";
                switchOrderTab("pending");
            });
        }

        if (btnResetFilter) {
            btnResetFilter.addEventListener("click", () => {
                resetAllFilters();
                switchOrderTab("pending");
            });
        }

        if (uploadInput) {
            uploadInput.addEventListener("change", (e) => {
                const file = e.target.files && e.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        if (orderUpdateError) {
                            orderUpdateError.innerText = "Ukuran foto terlalu besar (Maksimal 10MB)!";
                            orderUpdateError.style.display = "block";
                        }
                        uploadInput.value = "";
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        if (uploadPreview) {
                            uploadPreview.src = ev.target.result;
                            uploadPreview.style.display = "block";
                        }
                        if (uploadPlaceholder) {
                            uploadPlaceholder.style.display = "none";
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

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
            currentDetailOrder = o;

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

        const btnOpenMap = document.getElementById("btn-open-order-map");
        if (btnOpenMap) {
            btnOpenMap.addEventListener("click", () => {
                if (!currentDetailOrder) return;
                const o = currentDetailOrder;
                const c = o.customer || {};
                
                const lat = parseFloat(o.pickup_lat || c.lat || -7.250445);
                const lng = parseFloat(o.pickup_long || c.long || 112.768845);
                const cityName = o.city ? o.city.name : "Kota Layanan";
                const custName = c.name || "Customer";
                const address = o.pickup_address || c.address || "Alamat Penjemputan";

                if (document.getElementById("map-modal-subtitle")) {
                    document.getElementById("map-modal-subtitle").innerText = `Pelanggan: ${custName} • ${cityName}`;
                }
                if (document.getElementById("map-modal-coords")) {
                    document.getElementById("map-modal-coords").innerText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                }
                if (document.getElementById("map-modal-city-badge")) {
                    document.getElementById("map-modal-city-badge").innerText = cityName;
                }

                document.getElementById("modal-order-map").style.display = "flex";

                setTimeout(() => {
                    if (!orderMap) {
                        orderMap = L.map("order-map-view").setView([lat, lng], 15);
                        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                            attribution: "&copy; OpenStreetMap contributors",
                        }).addTo(orderMap);
                        orderMarker = L.marker([lat, lng]).addTo(orderMap);
                    } else {
                        orderMap.setView([lat, lng], 15);
                        orderMarker.setLatLng([lat, lng]);
                    }
                    orderMarker.bindPopup(`
                        <div style="font-size:12px; font-family:sans-serif;">
                            <strong style="color:#2563eb; display:block; margin-bottom:4px;">📍 ${custName}</strong>
                            <p style="margin:0; color:#374151;">${address}</p>
                        </div>
                    `).openPopup();
                    orderMap.invalidateSize();
                }, 200);
            });
        }

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

        switchOrderTab("pending");
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
                        <td>
                            <img src="${a.img_url || '/img/default-accu.png'}" alt="${a.name}" style="width:36px; height:36px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; background:#f9fafb;" onerror="this.src='/img/default-accu.png'">
                        </td>
                        <td style="font-weight:500;">${a.brand}</td>
                        <td>${a.name}</td>
                        <td>
                            <button onclick="deleteAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada aki</strong></div></td></tr>`;
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
                        <td>
                            <img src="${a.img_url || '/img/default-accu.png'}" alt="${a.name}" style="width:32px; height:32px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb; background:#f9fafb;" onerror="this.src='/img/default-accu.png'">
                        </td>
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
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada harga diset untuk kota ${cityName}</strong></div></td></tr>`;
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

        window.openAddCityModal = () => {
            loadCities();
            loadTrashedCities();
            document.getElementById("modal-add-city").style.display = "flex";
        };

        window.openAddAccuModal = () => {
            loadAccus();
            loadTrashedAccus();
            const imgInput = document.getElementById("accu-img");
            const imgPreview = document.getElementById("accu-img-preview");
            const imgPreviewContainer = document.getElementById("accu-img-preview-container");
            if (imgInput) imgInput.value = "";
            if (imgPreview) imgPreview.src = "";
            if (imgPreviewContainer) imgPreviewContainer.style.display = "none";
            document.getElementById("modal-add-accu").style.display = "flex";
        };

        window.deleteCity = (id) => {
            showConfirm(
                "Hapus Kota",
                "Yakin ingin menghapus kota ini?",
                async () => {
                    await fetchApi(`/cities/${id}`, { method: "DELETE" });
                    showToast("Kota berhasil dihapus", "success");
                    loadCities();
                    loadTrashedCities();
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
                    loadTrashedAccus();
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
                    loadTrashedAccus();
                },
            );
        };

        const loadTrashedCities = async () => {
            const res = await fetchApi("/cities/trashed");
            const listEl = document.getElementById("trashed-cities-list");
            if (listEl) {
                if (res.data && res.data.length) {
                    listEl.innerHTML = res.data
                        .map(
                            (c) =>
                                `<div style="display:flex; justify-content:space-between; align-items:center; padding:4px 0; border-bottom:1px solid #f3f4f6;">
                                    <span>${c.name}</span>
                                    <button type="button" onclick="restoreCity(${c.id})" class="admin-button admin-button--primary" style="height:24px; padding:0 8px; font-size:10px;">Pulihkan</button>
                                </div>`,
                        )
                        .join("");
                } else {
                    listEl.innerHTML = `<span style="color:#9ca3af;">Tidak ada kota terhapus</span>`;
                }
            }
        };

        const loadTrashedAccus = async () => {
            const res = await fetchApi("/accus/trashed");
            const listEl = document.getElementById("trashed-accus-list");
            if (listEl) {
                if (res.data && res.data.length) {
                    listEl.innerHTML = res.data
                        .map(
                            (a) =>
                                `<div style="display:flex; justify-content:space-between; align-items:center; padding:4px 0; border-bottom:1px solid #f3f4f6;">
                                    <span>${a.brand} - ${a.name}</span>
                                    <button type="button" onclick="restoreAccu(${a.id})" class="admin-button admin-button--primary" style="height:24px; padding:0 8px; font-size:10px;">Pulihkan</button>
                                </div>`,
                        )
                        .join("");
                } else {
                    listEl.innerHTML = `<span style="color:#9ca3af;">Tidak ada aki terhapus</span>`;
                }
            }
        };

        window.restoreCity = async (id) => {
            const res = await fetchApi(`/cities/${id}/restore`, {
                method: "POST",
            });
            showToast(res.message || "Kota berhasil dipulihkan", "success");
            loadCities();
            loadTrashedCities();
        };

        window.restoreAccu = async (id) => {
            const res = await fetchApi(`/accus/${id}/restore`, {
                method: "POST",
            });
            showToast(res.message || "Aki berhasil dipulihkan", "success");
            loadAccus();
            loadTrashedAccus();
        };

        loadTrashedCities();
        loadTrashedAccus();

        const accuBrandSel = document.getElementById("accu-brand");
        const accuBrandOtherWrap = document.getElementById(
            "accu-brand-other-wrap",
        );
        const accuBrandOtherInput = document.getElementById("accu-brand-other");
        if (accuBrandSel) {
            accuBrandSel.addEventListener("change", () => {
                if (accuBrandSel.value === "Lainnya") {
                    if (accuBrandOtherWrap)
                        accuBrandOtherWrap.style.display = "block";
                    if (accuBrandOtherInput)
                        accuBrandOtherInput.required = true;
                } else {
                    if (accuBrandOtherWrap)
                        accuBrandOtherWrap.style.display = "none";
                    if (accuBrandOtherInput)
                        accuBrandOtherInput.required = false;
                }
            });
        }

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
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-city").style.display =
                        "none";
                    document.getElementById("form-add-city").reset();
                    showToast(res.message || "Kota berhasil ditambahkan!", "success");
                    loadCities();
                    loadTrashedCities();
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

        const accuImgInput = document.getElementById("accu-img");
        const accuImgPreview = document.getElementById("accu-img-preview");
        const accuImgPreviewContainer = document.getElementById("accu-img-preview-container");
        if (accuImgInput && accuImgPreview) {
            accuImgInput.addEventListener("change", () => {
                const file = accuImgInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        accuImgPreview.src = e.target.result;
                        if (accuImgPreviewContainer) accuImgPreviewContainer.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                } else {
                    accuImgPreview.src = "";
                    if (accuImgPreviewContainer) accuImgPreviewContainer.style.display = "none";
                }
            });
        }

        document
            .getElementById("form-add-accu")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                let brandVal = document.getElementById("accu-brand").value;
                if (brandVal === "Lainnya") {
                    const customBrand = document
                        .getElementById("accu-brand-other")
                        .value.trim();
                    if (!customBrand) {
                        showToast(
                            "Masukkan nama brand baru terlebih dahulu",
                            "warning",
                        );
                        return;
                    }
                    brandVal = customBrand;
                }
                const accuNameVal = document
                    .getElementById("accu-name")
                    .value.trim();

                const formData = new FormData();
                formData.append("brand", brandVal);
                formData.append("name", accuNameVal);
                
                const imgInput = document.getElementById("accu-img");
                if (imgInput && imgInput.files && imgInput.files[0]) {
                    formData.append("img", imgInput.files[0]);
                }

                const res = await fetchApi("/accus", {
                    method: "POST",
                    body: formData,
                });
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-accu").style.display =
                        "none";
                    document.getElementById("form-add-accu").reset();
                    if (accuImgPreviewContainer) accuImgPreviewContainer.style.display = "none";
                    if (accuBrandOtherWrap)
                        accuBrandOtherWrap.style.display = "none";
                    showToast(res.message || "Aki baru berhasil disimpan!", "success");
                    loadAccus();
                    loadTrashedAccus();
                } else {
                    showToast(res.message || "Gagal menyimpan aki", "error");
                }
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
        const loadTrashedStorages = async () => {
            const res = await fetchApi("/storages/trashed");
            const listEl = document.getElementById("trashed-storages-list");
            if (listEl) {
                if (res.data && res.data.length) {
                    listEl.innerHTML = res.data
                        .map(
                            (s) =>
                                `<div style="display:flex; justify-content:space-between; align-items:center; padding:4px 0; border-bottom:1px solid #f3f4f6;">
                                    <span>${s.name} (${s.address || "-"})</span>
                                    <button type="button" onclick="restoreStorage(${s.id})" class="admin-button admin-button--primary" style="height:24px; padding:0 8px; font-size:10px;">Pulihkan</button>
                                </div>`,
                        )
                        .join("");
                } else {
                    listEl.innerHTML = `<span style="color:#9ca3af;">Tidak ada gudang terhapus</span>`;
                }
            }
        };

        window.restoreStorage = async (id) => {
            const res = await fetchApi(`/storages/${id}/restore`, {
                method: "POST",
            });
            showToast(res.message || "Gudang berhasil dipulihkan", "success");
            loadStorages();
            loadTrashedStorages();
        };

        loadStorages();
        loadTrashedStorages();

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
                const res = await fetchApi("/storages", {
                    method: "POST",
                    body: JSON.stringify({
                        name: document.getElementById("storage-name").value,
                        address:
                            document.getElementById("storage-address").value,
                        lat: lat,
                        long: lng,
                    }),
                });
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-storage").style.display =
                        "none";
                    document.getElementById("form-add-storage").reset();
                    document.getElementById("map-coords").innerText =
                        "Belum ada titik dipilih";
                    if (addMarker) {
                        addMap.removeLayer(addMarker);
                        addMarker = null;
                    }
                    showToast(res.message || "Gudang berhasil ditambahkan!", "success");
                    loadStorages();
                    loadTrashedStorages();
                } else {
                    showToast(res.message || "Gagal menyimpan gudang", "error");
                }
            });

        window.openAddStorageModal = () => {
            loadStorages();
            loadTrashedStorages();
            document.getElementById("modal-add-storage").style.display = "flex";
            setTimeout(() => {
                if (window.addMap) window.addMap.invalidateSize();
            }, 200);
        };

        window.deleteStorage = (id) => {
            document.getElementById("delete-storage-id").value = id;
            document.getElementById("delete-storage-password").value = "";
            document.getElementById("modal-delete-storage").style.display =
                "flex";
        };

        const formDeleteStorage = document.getElementById("form-delete-storage");
        if (formDeleteStorage) {
            formDeleteStorage.addEventListener("submit", async (e) => {
                e.preventDefault();
                const id = document.getElementById("delete-storage-id").value;
                const password = document.getElementById(
                    "delete-storage-password",
                ).value;

                const res = await fetchApi(`/storages/${id}`, {
                    method: "DELETE",
                    body: JSON.stringify({ password }),
                });

                if (
                    res &&
                    res.message &&
                    !res.errors &&
                    (!res.status || res.status < 400)
                ) {
                    document.getElementById("modal-delete-storage").style.display =
                        "none";
                    showToast(res.message, "success");
                    loadStorages();
                    loadTrashedStorages();
                } else {
                    showToast(
                        res.message || "Password admin salah / Gagal menghapus gudang",
                        "error",
                    );
                }
            });
        }
    }

    if (window.location.pathname === "/admin/pengguna") {
        const modalLock = document.getElementById("modal-easter-egg-lock");
        const formAuth = document.getElementById("form-easter-egg-auth");
        const authError = document.getElementById("easter-egg-error");

        const checkEasterEggLock = () => {
            const isUnlocked = sessionStorage.getItem("easter_egg_unlocked") === "true";
            if (!isUnlocked) {
                if (modalLock) modalLock.style.display = "flex";
                return false;
            }
            if (modalLock) modalLock.style.display = "none";
            return true;
        };

        if (formAuth) {
            formAuth.addEventListener("submit", async (e) => {
                e.preventDefault();
                const inputPass = document.getElementById("easter-egg-password").value;
                const verifyRes = await fetch("/api/public-admin/verify", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "Accept": "application/json" },
                    body: JSON.stringify({ secret: inputPass }),
                });
                const verifyData = await verifyRes.json();
                if (verifyRes.ok && verifyData.valid) {
                    sessionStorage.setItem("easter_egg_pass", inputPass);
                    sessionStorage.setItem("easter_egg_unlocked", "true");
                    if (authError) authError.style.display = "none";
                    if (modalLock) modalLock.style.display = "none";
                    showToast("Akses Rahasia Dibuka! Anda dapat membuat akun admin baru.", "success");
                    loadUsers();
                } else {
                    if (authError) {
                        authError.innerText = verifyData.message || "Password rahasia salah!";
                        authError.style.display = "block";
                    }
                }
            });
        }

        const loadUsers = async () => {
            if (!checkEasterEggLock()) return;
            const res = await fetchApi("/users");
            const tbody = document.getElementById("users-tbody");
            if (res && res.data && res.data.length) {
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

        if (checkEasterEggLock()) {
            loadUsers();
        }

        document
            .getElementById("form-add-user")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                if (!checkEasterEggLock()) return;
                const payload = {
                    name: document.getElementById("user-name").value,
                    password: document.getElementById("user-password").value,
                };
                const idVal = document.getElementById("user-id").value;
                payload.id = idVal
                    ? idVal
                    : Math.floor(Math.random() * 1000000);
                const res = await fetchApi("/users", {
                    method: "POST",
                    body: JSON.stringify(payload),
                });
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-user").style.display =
                        "none";
                    document.getElementById("form-add-user").reset();
                    showToast(res.message || "Staf admin berhasil ditambahkan!", "success");
                    loadUsers();
                } else {
                    showToast(res?.message || "Gagal membuat akun admin", "error");
                }
            });

        window.deleteUser = (id) => {
            if (!checkEasterEggLock()) return;
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

    if (window.location.pathname === "/admin/laporan") {
        const yearSelect = document.getElementById("report-year-select");

        const loadReportData = async (selectedYear) => {
            const endpoint = selectedYear ? `/reports?year=${selectedYear}` : "/reports";
            const res = await fetchApi(endpoint);
            if (!res || !res.data) return;

            const d = res.data;
            const s = d.summary;

            if (yearSelect && d.available_years && d.available_years.length) {
                yearSelect.innerHTML = d.available_years
                    .map(y => `<option value="${y}" ${y == d.selected_year ? 'selected' : ''}>${y}</option>`)
                    .join("");
            }

            document.getElementById("report-stat-sales").innerText = rupiah(s.total_sales);
            document.getElementById("report-stat-orders").innerText = s.total_orders.toLocaleString("id-ID");
            document.getElementById("report-stat-completed-note").innerText = `${s.completed_orders.toLocaleString("id-ID")} Selesai`;
            document.getElementById("report-stat-avg").innerText = rupiah(s.avg_transaction_value);
            document.getElementById("report-stat-cancelled").innerText = `${s.cancelled_orders.toLocaleString("id-ID")} (${s.cancellation_rate}%)`;

            const chartTitle = document.getElementById("chart-title");
            if (chartTitle) chartTitle.innerText = `Pendapatan Bulanan Tahun ${d.selected_year}`;

            const barsContainer = document.getElementById("chart-bars-container");
            const labelsContainer = document.getElementById("chart-labels-container");
            const maxLabel = document.getElementById("chart-max-label");

            const monthly = d.monthly_chart || [];
            const maxRev = Math.max(...monthly.map(m => m.revenue), 1);

            if (maxLabel) {
                maxLabel.innerText = `Tertinggi: ${rupiah(maxRev)}`;
            }

            if (barsContainer && labelsContainer) {
                const isDark = document.documentElement.classList.contains("admin-dark-mode");
                const chartTextColor = isDark ? "#cbd5e1" : "#4b5563";
                const chartLabelColor = isDark ? "#94a3b8" : "#6b7280";
                const gridBorderColor = isDark ? "rgba(255,255,255,0.2)" : "#000";

                barsContainer.innerHTML = `
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none; opacity:0.15; z-index:0;">
                        <div style="border-top:1px dashed ${gridBorderColor}; width:100%;"></div>
                        <div style="border-top:1px dashed ${gridBorderColor}; width:100%;"></div>
                        <div style="border-top:1px dashed ${gridBorderColor}; width:100%;"></div>
                    </div>
                ` + monthly.map(m => {
                    const pct = Math.max(Math.round((m.revenue / maxRev) * 100), 4);
                    const formattedRev = m.revenue > 0 
                        ? (m.revenue >= 1000000000 ? (m.revenue / 1000000000).toFixed(1) + 'M' : (m.revenue >= 1000000 ? (m.revenue / 1000000).toFixed(0) + 'jt' : (m.revenue / 1000).toFixed(0) + 'k')) 
                        : '0';

                    return `
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; z-index:1;" title="${m.month_name}: ${rupiah(m.revenue)} (${m.receipts_count} struk)">
                        <div style="font-size:10px; font-weight:600; color:${chartTextColor}; margin-bottom:4px; white-space:nowrap;">${formattedRev}</div>
                        <div style="width:75%; max-width:32px; height:${pct}%; background:linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%); border-radius:4px 4px 0 0; transition: height 0.4s ease;"></div>
                    </div>`;
                }).join("");

                labelsContainer.innerHTML = monthly.map(m => `
                    <div style="flex:1; text-align:center; font-size:11px; font-weight:600; color:${chartLabelColor};">${m.month_name}</div>
                `).join("");
            }

            const topAccusTbody = document.getElementById("top-accus-tbody");
            if (topAccusTbody) {
                if (d.top_accus && d.top_accus.length) {
                    topAccusTbody.innerHTML = d.top_accus.map(a => `
                        <tr>
                            <td style="font-weight:500;">${a.brand || '-'}</td>
                            <td>${a.name}</td>
                            <td style="text-align:right; font-weight:600; color:#1d4ed8;">${Number(a.total_sold).toLocaleString('id-ID')} unit</td>
                        </tr>
                    `).join("");
                } else {
                    topAccusTbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty">Belum ada data penjualan</div></td></tr>`;
                }
            }

            const topCitiesTbody = document.getElementById("top-cities-tbody");
            if (topCitiesTbody) {
                if (d.top_cities && d.top_cities.length) {
                    topCitiesTbody.innerHTML = d.top_cities.map(c => `
                        <tr>
                            <td style="font-weight:500;">${c.name}</td>
                            <td style="text-align:right; font-weight:600; color:#10b981;">${Number(c.total_orders).toLocaleString('id-ID')} order</td>
                        </tr>
                    `).join("");
                } else {
                    topCitiesTbody.innerHTML = `<tr><td colspan="2"><div class="admin-table-empty">Belum ada data area</div></td></tr>`;
                }
            }
        };

        loadReportData();

        if (yearSelect) {
            yearSelect.addEventListener("change", (e) => {
                loadReportData(e.target.value);
            });
        }
    }
});
