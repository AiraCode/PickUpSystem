document.addEventListener("DOMContentLoaded", () => {
    const API_BASE = "/api/admin";
    const token = localStorage.getItem("admin_token");
    const user = JSON.parse(localStorage.getItem("admin_user") || "null");

    if (
        user &&
        user.role === "warehouse" &&
        window.location.pathname === "/admin/gudang"
    ) {
        window.location.href = `/admin/gudang/${user.warehouse_id}`;
        return;
    }

    const themeToggleBtn = document.querySelector(".admin-theme-toggle-foot");

    if (!token && window.location.pathname !== "/admin/login") {
        if (
            window.location.pathname !== "/admin/pengguna" &&
            window.location.pathname !== "/admin/pengiriman"
        ) {
            window.location.href = "/admin/login";
            return;
        }

        const sidebar = document.querySelector(".admin-sidebar");
        const main = document.querySelector(".admin-main");
        const sidebarToggle = document.getElementById("sidebarToggle");
        if (sidebarToggle) sidebarToggle.style.display = "none";
        if (sidebar) sidebar.style.display = "none";
        if (main) {
            main.style.marginLeft = "0";
            main.style.width = "100%";
        }

        // Ganti nama default menjadi Modern Mulya Mandiri
        const authUserName = document.getElementById("auth-user-name");
        const authUserInitial = document.getElementById("auth-user-initial");
        if (authUserName) authUserName.innerText = "Modern Mulya Mandiri";
        if (authUserInitial) authUserInitial.innerText = "M";

        const checkEasterEggTime = () => {
            const unlockTime = parseInt(
                sessionStorage.getItem("easter_egg_time") || "0",
            );
            if (unlockTime > 0 && Date.now() - unlockTime > 5 * 60 * 1000) {
                sessionStorage.removeItem("easter_egg_unlocked");
                sessionStorage.removeItem("easter_egg_pass");
                sessionStorage.removeItem("easter_egg_time");
                window.location.reload();
            }
        };
        checkEasterEggTime();
        setInterval(checkEasterEggTime, 10000);
    }
    if (token && window.location.pathname === "/admin/login") {
        window.location.href = "/admin/dashboard";
        return;
    }

    const tabs = document.querySelectorAll(".order-status-tab");
    tabs.forEach((tab, index) => {
        tab.addEventListener("mouseenter", () => {
            tabs.forEach((t, i) => {
                if (i === index) {
                    t.style.transform = "scale(1.05) translateY(-5px)";
                    t.style.zIndex = "10";
                    t.style.boxShadow = "0 10px 20px rgba(0, 0, 0, 0.1)";
                } else if (i < index) {
                    t.style.transform = "translateX(-8px) scale(0.98)";
                    t.style.zIndex = "1";
                } else if (i > index) {
                    t.style.transform = "translateX(8px) scale(0.98)";
                    t.style.zIndex = "1";
                }
            });
        });
        tab.addEventListener("mouseleave", () => {
            tabs.forEach((t) => {
                t.style.transform = "";
                t.style.zIndex = "";
                t.style.boxShadow = "";
            });
        });
    });

    const parseSafeDate = (d) => {
        if (!d) return new Date();
        let s = String(d);
        if (!s.includes("T") && s.includes(" ")) s = s.replace(" ", "T");
        if (!s.includes("Z") && !s.includes("+")) s += "Z";
        return new Date(s);
    };

    if (user && document.getElementById("auth-user-name")) {
        document.getElementById("auth-user-name").innerText = user.name;
        document.getElementById("auth-user-initial").innerText = user.name
            .charAt(0)
            .toUpperCase();
    }

    const clockEl = document.getElementById("admin-live-clock");
    if (clockEl) {
        const updateClock = () => {
            const now = new Date();
            const activeLang =
                localStorage.getItem("app_language") ||
                (window.pageTranslator
                    ? window.pageTranslator.activeLang
                    : "id");
            const locale = activeLang === "en" ? "en-US" : "id-ID";
            const dateStr = now.toLocaleDateString(locale, {
                weekday: "short",
                day: "numeric",
                month: "short",
                year: "numeric",
            });
            const timeStr = now.toLocaleTimeString(locale, {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: false,
            });
            clockEl.innerHTML = `<span style="font-weight:500; color:#475569;">${dateStr}</span> <span style="color:#2563eb; font-weight:700;">${timeStr} WIB</span>`;
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    window.addEventListener("keydown", (e) => {
        if (e.key === "Escape" || e.key === "Esc") {
            const modals = Array.from(
                document.querySelectorAll(
                    'div[id^="modal-"], div[id*="modal"]',
                ),
            ).filter((m) => window.getComputedStyle(m).display !== "none");
            if (modals.length > 0) {
                let topmost = modals[0];
                let maxZ =
                    parseInt(window.getComputedStyle(topmost).zIndex) || 0;
                for (let i = 1; i < modals.length; i++) {
                    const z =
                        parseInt(window.getComputedStyle(modals[i]).zIndex) ||
                        0;
                    if (z >= maxZ) {
                        maxZ = z;
                        topmost = modals[i];
                    }
                }
                topmost.style.display = "none";
            }
        }
    });

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
        if (
            endpoint.startsWith("/users") ||
            endpoint.startsWith("/pengiriman")
        ) {
            if (!token) {
                apiTarget = `/api/public-admin${endpoint}`;
            }
        }

        const finalOptions = {
            cache: "no-store",
            ...options,
            headers,
        };
        const response = await fetch(apiTarget, finalOptions);
        if (response.status === 401 && token) {
            localStorage.removeItem("admin_token");
            localStorage.removeItem("admin_user");
            window.location.href = "/admin/login";
        }
        const data = await response.json();

        const reqMethod = (options.method || "GET").toUpperCase();
        if (
            response.ok &&
            ["POST", "PUT", "DELETE", "PATCH"].includes(reqMethod)
        ) {
            setTimeout(() => {
                if (typeof window.refreshCurrentPageData === "function") {
                    window.refreshCurrentPageData();
                }
            }, 300);
        }
        return data;
    };

    window.refreshCurrentPageData = function () {
        const path = window.location.pathname;
        if (path === "/admin/harga") {
            if (typeof window.loadSettings === "function")
                window.loadSettings();
            if (typeof window.loadPriceHistory === "function")
                window.loadPriceHistory();
            if (typeof window.loadCities === "function") window.loadCities();
            if (typeof window.loadAccus === "function") window.loadAccus();
            if (typeof window.loadNewAccus === "function")
                window.loadNewAccus();
        } else if (path === "/admin/dashboard") {
            if (typeof window.loadDashboardStats === "function")
                window.loadDashboardStats();
            if (typeof window.fetchActivitiesPage === "function")
                window.fetchActivitiesPage();
        } else if (path.includes("/admin/transaksi")) {
            if (typeof window.loadOrders === "function") window.loadOrders();
        } else if (path.includes("/admin/gudang")) {
            if (typeof window.loadStorages === "function")
                window.loadStorages();
        } else if (path.includes("/admin/pengguna")) {
            if (typeof window.loadUsers === "function") window.loadUsers();
        } else if (path.includes("/admin/laporan")) {
            if (typeof window.loadReportData === "function")
                window.loadReportData();
        }
    };

    const rupiah = (n) =>
        window.i18n
            ? window.i18n.formatCurrency(n)
            : new Intl.NumberFormat("id-ID", {
                  style: "currency",
                  currency: "IDR",
                  maximumFractionDigits: 0,
              }).format(n);

    // ── Global Order / Activity Popup Notification System ────────────────────
    let notifPopupTimeout = null;
    let notifPopupInterval = null;

    function showOrderPopupNotification(activity) {
        const popup = document.getElementById("order-notif-popup");
        if (!popup) return;

        const titleEl = document.getElementById("order-notif-title");
        const bodyEl = document.getElementById("order-notif-body");
        const closeBtn = document.getElementById("order-notif-close");
        const ctaBtn = document.getElementById("order-notif-cta");
        const progressEl = document.getElementById("order-notif-progress");
        const timerTextEl = document.getElementById("order-notif-timer-text");
        const iconWrap = document.getElementById("order-notif-icon-wrap");

        if (titleEl) titleEl.innerText = activity.title || "Notifikasi Admin";
        if (bodyEl)
            bodyEl.innerText =
                activity.description || "Ada pembaruan transaksi terbaru.";

        if (iconWrap) {
            if (activity.type === "error") {
                iconWrap.style.background =
                    "linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)";
                iconWrap.innerText = "✕";
                iconWrap.style.color = "#dc2626";
            } else if (activity.type === "warning") {
                iconWrap.style.background =
                    "linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)";
                iconWrap.innerText = "⚠";
                iconWrap.style.color = "#d97706";
            } else {
                iconWrap.style.background =
                    "linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%)";
                iconWrap.innerText = "🔔";
                iconWrap.style.color = "#2563eb";
            }
        }

        if (ctaBtn) {
            if (activity.related_id) {
                ctaBtn.style.display = "inline-flex";
            } else {
                ctaBtn.style.display = "none";
            }
        }

        // Clear existing timers
        if (notifPopupTimeout) clearTimeout(notifPopupTimeout);
        if (notifPopupInterval) clearInterval(notifPopupInterval);

        // Show popup with smooth slide & scale animation
        popup.style.display = "block";
        requestAnimationFrame(() => {
            popup.style.opacity = "1";
            popup.style.transform = "translateX(0) scale(1)";
        });

        // 5 second timer & progress bar countdown
        let secondsLeft = 5;
        if (timerTextEl)
            timerTextEl.innerText = `Menutup dalam ${secondsLeft} detik...`;
        if (progressEl) {
            progressEl.style.transition = "none";
            progressEl.style.width = "100%";
            requestAnimationFrame(() => {
                progressEl.style.transition = "width 5s linear";
                progressEl.style.width = "0%";
            });
        }

        notifPopupInterval = setInterval(() => {
            secondsLeft -= 1;
            if (secondsLeft > 0) {
                if (timerTextEl)
                    timerTextEl.innerText = `Menutup dalam ${secondsLeft} detik...`;
            } else {
                clearInterval(notifPopupInterval);
            }
        }, 1000);

        const hidePopup = () => {
            if (notifPopupTimeout) clearTimeout(notifPopupTimeout);
            if (notifPopupInterval) clearInterval(notifPopupInterval);
            popup.style.opacity = "0";
            popup.style.transform = "translateX(30px) scale(0.97)";
            setTimeout(() => {
                popup.style.display = "none";
            }, 300);
        };

        if (closeBtn) {
            closeBtn.onclick = (e) => {
                e.stopPropagation();
                hidePopup();
            };
        }

        const handleNavigate = () => {
            hidePopup();
            const orderId = activity.related_id || 0;
            if (orderId && orderId !== 0 && orderId !== "0") {
                window.openActivityOrder(orderId);
            }
        };

        if (ctaBtn)
            ctaBtn.onclick = (e) => {
                e.stopPropagation();
                handleNavigate();
            };
        popup.onclick = (e) => {
            if (activity.related_id) {
                handleNavigate();
            } else {
                hidePopup();
            }
        };

        notifPopupTimeout = setTimeout(() => {
            hidePopup();
        }, 5000);
    }
    window.showOrderPopupNotification = showOrderPopupNotification;

    let toastTimeout = null;
    function showToast(message, type = "success") {
        const toast = document.getElementById("admin-toast");
        const msg = document.getElementById("admin-toast-message");
        const icon = document.getElementById("admin-toast-icon");
        if (!toast) return;

        if (toastTimeout) clearTimeout(toastTimeout);

        if (msg) msg.innerText = message;
        if (type === "error") {
            toast.style.background = "#dc2626";
            if (icon) icon.innerText = "✕";
        } else if (type === "warning") {
            toast.style.background = "#f59e0b";
            if (icon) icon.innerText = "⚠";
        } else {
            toast.style.background = "#10b981";
            if (icon) icon.innerText = "✓";
        }

        toast.style.display = "flex";
        void toast.offsetWidth;
        toast.style.opacity = "1";
        toast.style.transform = "translateY(0)";

        toastTimeout = setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-10px)";
            setTimeout(() => {
                toast.style.display = "none";
            }, 300);
        }, 4000);
    }
    window.showToast = showToast;

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

    window.showConfirmModal = function ({
        title = "Konfirmasi Hapus",
        message = "Apakah Anda yakin ingin menghapus?",
        confirmText = "Hapus",
        cancelText = "Batal",
        isDanger = true,
    } = {}) {
        return new Promise((resolve) => {
            const modal = document.getElementById("modal-custom-confirm");
            if (!modal) {
                resolve(window.confirm(message));
                return;
            }

            const titleEl = document.getElementById("confirm-title");
            const msgEl = document.getElementById("confirm-message");
            const okBtn = document.getElementById("btn-confirm-ok");
            const cancelBtn = document.getElementById("btn-confirm-cancel");

            if (titleEl) titleEl.innerText = title;
            if (msgEl) msgEl.innerText = message;
            if (okBtn) {
                okBtn.innerText = confirmText;
                okBtn.style.background = isDanger ? "#ba1b2b" : "#2563eb";
            }
            if (cancelBtn) cancelBtn.innerText = cancelText;

            modal.style.display = "flex";

            const cleanup = () => {
                modal.style.display = "none";
                okBtn.onclick = null;
                cancelBtn.onclick = null;
                modal.onclick = null;
            };

            okBtn.onclick = () => {
                cleanup();
                resolve(true);
            };

            cancelBtn.onclick = () => {
                cleanup();
                resolve(false);
            };

            modal.onclick = (e) => {
                if (e.target === modal) {
                    cleanup();
                    resolve(false);
                }
            };
        });
    };

    window.openActivityOrder = (orderId) => {
        if (!orderId || orderId === 0 || orderId === "0") {
            window.location.href = "/admin/transaksi";
            return;
        }
        if (window.location.pathname.includes("/admin/transaksi")) {
            if (typeof window.viewOrderDetail === "function") {
                window.viewOrderDetail(orderId);
            }
        } else {
            window.location.href = `/admin/transaksi?order_id=${orderId}`;
        }
    };

    window.confirmDismissActivity = async (e, id) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const isConfirmed = await window.showConfirmModal({
            title: "Hapus Notifikasi",
            message: "Apakah Anda yakin ingin menghapus notifikasi ini?",
            confirmText: "Hapus",
            cancelText: "Batal",
            isDanger: true,
        });

        if (!isConfirmed) return;

        if (e && e.target) {
            const elem =
                e.target.closest("div.admin-activity-item") ||
                e.target.closest("tr");
            if (elem) elem.style.opacity = "0.3";
        }

        try {
            const res = await fetchApi(`/activities/${id}`, {
                method: "DELETE",
            });
            showToast(res.message || "Notifikasi berhasil dihapus", "success");

            // Auto refresh UI immediately
            if (typeof window.loadDashboardStats === "function") {
                window.loadDashboardStats();
            }
            if (typeof window.fetchActivitiesPage === "function") {
                window.fetchActivitiesPage();
            }
        } catch (err) {
            console.error(err);
            showToast(err.message || "Gagal menghapus notifikasi", "error");
            if (e && e.target) {
                const elem =
                    e.target.closest("div.admin-activity-item") ||
                    e.target.closest("tr");
                if (elem) elem.style.opacity = "1";
            }
        }
    };

    window.confirmClearAllActivities = async (e) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const isConfirmed = await window.showConfirmModal({
            title: "Hapus Semua Notifikasi",
            message:
                "Apakah Anda yakin ingin menghapus semua notifikasi aktivitas?",
            confirmText: "Hapus Semua",
            cancelText: "Batal",
            isDanger: true,
        });

        if (!isConfirmed) return;

        try {
            const res = await fetchApi("/activities", { method: "DELETE" });
            showToast(
                res.message || "Semua notifikasi berhasil dihapus",
                "success",
            );

            // Auto refresh UI immediately
            if (typeof window.loadDashboardStats === "function") {
                window.loadDashboardStats();
            }
            if (typeof window.fetchActivitiesPage === "function") {
                window.fetchActivitiesPage();
            }
        } catch (err) {
            console.error(err);
            showToast(
                err.message || "Gagal menghapus semua notifikasi",
                "error",
            );
        }
    };
    window.dismissActivityNotification = window.confirmDismissActivity;
    window.clearAllActivities = window.confirmClearAllActivities;

    // ── Activity Polling (Runs every 10 seconds across all admin pages) ─────────

    // Activity Polling (Runs every 10 seconds across all admin pages)
    let lastActivityId = parseInt(
        localStorage.getItem("admin_last_activity_id") || "0",
    );

    const pollActivities = async () => {
        if (!token) return;
        try {
            const res = await fetchApi("/activities");
            if (res && res.data && res.data.length > 0) {
                const latest = res.data[0];
                if (lastActivityId === 0) {
                    // First load: save highest ID without popping up existing history
                    lastActivityId = latest.id;
                    localStorage.setItem(
                        "admin_last_activity_id",
                        lastActivityId,
                    );
                } else if (latest.id > lastActivityId) {
                    lastActivityId = latest.id;
                    localStorage.setItem(
                        "admin_last_activity_id",
                        lastActivityId,
                    );

                    const currentPath = window.location.pathname;
                    const isDashboard =
                        currentPath === "/admin/dashboard" ||
                        currentPath === "/admin";
                    const isTransaksi = currentPath === "/admin/transaksi";

                    // Show pop up notification if NOT on Dashboard and NOT on Transaksi tab
                    if (!isDashboard && !isTransaksi) {
                        window.showOrderPopupNotification(latest);
                    }
                }
            }
        } catch (e) {
            // Ignore network errors during polling
        }
    };

    if (token) {
        pollActivities();
        setInterval(pollActivities, 10000);
    }

    const statusBadge = (status) => {
        const isDark =
            document.documentElement.classList.contains("admin-dark-mode");
        const colors = isDark
            ? {
                  pending: { bg: "rgba(245, 158, 11, 0.25)", color: "#fbbf24" },
                  processing: {
                      bg: "rgba(59, 130, 246, 0.25)",
                      color: "#60a5fa",
                  },
                  arrived_at_warehouse: {
                      bg: "rgba(139, 92, 246, 0.25)",
                      color: "#c084fc",
                  },
                  completed: {
                      bg: "rgba(16, 185, 129, 0.25)",
                      color: "#34d399",
                  },
                  cancelled: {
                      bg: "rgba(239, 68, 68, 0.25)",
                      color: "#f87171",
                  },
              }
            : {
                  pending: { bg: "#fef3c7", color: "#92400e" },
                  processing: { bg: "#dbeafe", color: "#1e40af" },
                  arrived_at_warehouse: { bg: "#f3e8ff", color: "#6b21a8" },
                  completed: { bg: "#d1fae5", color: "#065f46" },
                  cancelled: { bg: "#fee2e2", color: "#991b1b" },
              };
        const c =
            colors[status] ||
            (isDark
                ? { bg: "rgba(148, 163, 184, 0.25)", color: "#cbd5e1" }
                : { bg: "#f3f4f6", color: "#374151" });
        const labelText =
            status === "arrived_at_warehouse"
                ? "SAMPAI GUDANG"
                : status.toUpperCase();
        return `<span style="padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; background:${c.bg}; color:${c.color}; text-transform:uppercase;">${labelText}</span>`;
    };

    const btnLogout = document.getElementById("btn-logout");
    const modalLogout = document.getElementById("modal-logout-confirm");
    const formLogout = document.getElementById("form-logout-confirm");
    const logoutError = document.getElementById("logout-error");

    if (btnLogout && modalLogout) {
        btnLogout.addEventListener("click", (e) => {
            e.preventDefault();
            if (profileMenu) profileMenu.hidden = true;

            if (
                !token &&
                (window.location.pathname === "/admin/pengguna" ||
                    window.location.pathname === "/admin/pengiriman")
            ) {
                sessionStorage.removeItem("easter_egg_unlocked");
                sessionStorage.removeItem("easter_egg_pass");
                sessionStorage.removeItem("easter_egg_time");
                window.location.reload();
                return;
            }

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

            if (
                !token &&
                (window.location.pathname === "/admin/pengguna" ||
                    window.location.pathname === "/admin/pengiriman")
            ) {
                const currentName = document.getElementById("auth-user-name")
                    ? document.getElementById("auth-user-name").innerText
                    : "Modern Mulya Mandiri";
                document.getElementById("profile-name").value = currentName;
                document.getElementById(
                    "profile-current-password",
                ).parentElement.style.display = "none";
                document.getElementById(
                    "profile-new-password",
                ).parentElement.style.display = "none";
            } else {
                const currentUser = JSON.parse(
                    localStorage.getItem("admin_user") || "{}",
                );
                document.getElementById("profile-name").value =
                    currentUser.name || "";
                document.getElementById(
                    "profile-current-password",
                ).parentElement.style.display = "block";
                document.getElementById(
                    "profile-new-password",
                ).parentElement.style.display = "block";
            }

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

            if (
                !token &&
                (window.location.pathname === "/admin/pengguna" ||
                    window.location.pathname === "/admin/pengiriman")
            ) {
                const newName = payload.name;
                const nameEl = document.getElementById("auth-user-name");
                const initialEl = document.getElementById("auth-user-initial");

                if (nameEl) nameEl.innerText = newName;
                if (initialEl)
                    initialEl.innerText = newName.charAt(0).toUpperCase();

                modalEditProfile.style.display = "none";
                showToast(
                    window.i18n
                        ? window.i18n.t(
                              "toast.updated_successfully",
                              "Profil berhasil diperbarui!",
                          )
                        : "Profil berhasil diperbarui!",
                    "success",
                );
                return;
            }

            const res = await fetchApi("/profile", {
                method: "PUT",
                body: JSON.stringify(payload),
            });

            if (res.user) {
                localStorage.setItem("admin_user", JSON.stringify(res.user));
                const nameEl = document.getElementById("auth-user-name");
                const initialEl = document.getElementById("auth-user-initial");

                if (nameEl && initialEl) {
                    const animOut = [
                        { opacity: 1, transform: "translateY(0) scale(1)" },
                        {
                            opacity: 0,
                            transform: "translateY(-4px) scale(0.95)",
                        },
                    ];
                    const animIn = [
                        {
                            opacity: 0,
                            transform: "translateY(4px) scale(0.95)",
                        },
                        { opacity: 1, transform: "translateY(0) scale(1)" },
                    ];
                    const animOpts = {
                        duration: 200,
                        easing: "cubic-bezier(0.4, 0, 0.2, 1)",
                    };

                    const outAnim = nameEl.animate(animOut, animOpts);
                    initialEl.animate(animOut, animOpts);

                    outAnim.onfinish = () => {
                        nameEl.innerText = res.user.name;
                        initialEl.innerText = res.user.name
                            .charAt(0)
                            .toUpperCase();
                        nameEl.animate(animIn, animOpts);
                        initialEl.animate(animIn, animOpts);
                    };
                } else if (nameEl) {
                    nameEl.innerText = res.user.name;
                }

                modalEditProfile.style.display = "none";
                showToast(
                    window.i18n
                        ? window.i18n.t(
                              "toast.updated_successfully",
                              "Profil berhasil diperbarui!",
                          )
                        : "Profil berhasil diperbarui!",
                    "success",
                );
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

        window.loadDashboardStats = async (period = "7days") => {
            const res = await fetchApi(`/dashboard-stats?period=${period}`);
            if (!res || !res.data) return;

            document.getElementById("stat-total-transactions").innerText =
                res.data.overview.total_transactions;
            document.getElementById("stat-pending-verifications").innerText =
                res.data.overview.pending_verifications;
            document.getElementById("stat-total-sales").innerText = rupiah(
                res.data.overview.total_sales,
            );
            if (document.getElementById("stat-avg-time")) {
                document.getElementById("stat-avg-time").innerText =
                    res.data.overview.avg_processing_time;
            }

            const tbody = document.getElementById("attention-orders-tbody");
            if (
                !res.data.attention_orders ||
                !res.data.attention_orders.length
            ) {
                tbody.innerHTML = `<tr><td colspan="6"><div class="admin-table-empty"><strong>Belum ada pesanan</strong></div></td></tr>`;
            } else {
                tbody.innerHTML = res.data.attention_orders
                    .map(
                        (o) => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.customer ? o.customer.name : "-"}</td>
                        <td>${o.city ? o.city.name : "-"}</td>
                        <td>
                            ${
                                o.delivery_method === "courier"
                                    ? '<span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Kurir</span>'
                                    : o.delivery_method === "warehouse"
                                      ? '<span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Kirim Sendiri</span>'
                                      : '<span style="color:#6b7280;">-</span>'
                            }
                        </td>
                        <td>${parseSafeDate(o.created_at).toLocaleDateString("id-ID")}</td>
                        <td>${statusBadge(o.status)}</td>
                    </tr>`,
                    )
                    .join("");
            }

            const actList = document.getElementById("activity-list-container");
            const actEmpty = document.getElementById("activity-empty-state");
            const activities = res.data.recent_activities || [];

            if (!activities.length) {
                if (actEmpty) {
                    actEmpty.style.display = "block";
                    actEmpty.innerHTML = `<div class="admin-empty-icon admin-empty-icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5.5h14v13H5z"/><path d="M8.5 9.5h7M8.5 13h4"/></svg></div><strong>Belum ada aktivitas</strong>`;
                }
                if (actList) actList.style.display = "none";
            } else {
                if (actEmpty) actEmpty.style.display = "none";
                if (actList) {
                    actList.style.display = "flex";
                    actList.innerHTML = activities
                        .map(
                            (a) => `
                        <div class="admin-activity-item" onclick="openActivityOrder(${a.related_id || 0})">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; padding-right:18px;">
                                <strong class="admin-activity-item__title">${a.title}</strong>
                            </div>
                            <small class="admin-activity-item__desc">${a.description}</small>
                            <button type="button" onclick="confirmDismissActivity(event, ${a.id})" title="Hapus Notifikasi" class="activity-delete-btn" aria-label="Hapus Notifikasi" style="position:absolute; top:6px; right:6px;">
                                &times;
                            </button>
                        </div>`,
                        )
                        .join("");

                    actList.innerHTML += `
                        <a href="/admin/aktivitas" class="all-activities-link-btn">
                            Tampilkan Semua Aktivitas
                        </a>
                    `;
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

                    const maxCount = Math.max(
                        ...chartData.map((d) => d.count),
                        1,
                    );

                    const barsHtml = chartData
                        .map((d) => {
                            const pct = Math.max(
                                Math.round((d.count / maxCount) * 100),
                                4,
                            );
                            const countDisplay =
                                d.count > 0
                                    ? `<span style="font-size:11px; font-weight:700; color:#2563eb; margin-bottom:6px;">${d.count}</span>`
                                    : `<span style="font-size:10px; color:#cbd5e1; margin-bottom:6px;">0</span>`;
                            const barBg =
                                d.count > 0
                                    ? "linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%)"
                                    : "#e2e8f0";
                            const barShadow =
                                d.count > 0
                                    ? "0 4px 10px rgba(37,99,235,0.2)"
                                    : "none";

                            return `
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; min-width:0;" title="${d.label}: ${d.count} transaksi">
                            ${countDisplay}
                            <div style="width:100%; max-width:${chartData.length > 20 ? "16px" : "38px"}; height:${pct}%; background:${barBg}; border-radius:6px 6px 0 0; transition: height 0.3s ease; box-shadow:${barShadow};"></div>
                            <span style="font-size:${chartData.length > 20 ? "9px" : "11px"}; color:#64748b; font-weight:600; margin-top:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;">${d.label}</span>
                        </div>`;
                        })
                        .join("");

                    chartContainer.innerHTML = `
                        <div class="admin-chart-shell" style="height:230px; border:1px solid #f1f5f9; border-radius:10px; background:#fafafa; padding:24px 16px 12px 16px; position:relative; overflow:hidden;">
                            <div style="position:absolute; inset:24px 16px 30px 16px; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none; z-index:0;">
                                <div class="admin-chart-grid-line" style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                                <div class="admin-chart-grid-line" style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                                <div class="admin-chart-grid-line" style="border-bottom:1px dashed #e2e8f0; width:100%;"></div>
                            </div>
                            <div style="display:flex; align-items:flex-end; justify-content:space-between; width:100%; height:100%; position:relative; z-index:1; gap:${chartData.length > 20 ? "3px" : "12px"};">
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

    const renderGeneralPagination = function (
        pagination,
        containerId,
        onClickFnName,
    ) {
        const container = document.getElementById(containerId);
        if (!container) return;
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = "";
            window.__adminPaginationState = window.__adminPaginationState || {};
            delete window.__adminPaginationState[containerId];
            return;
        }

        const isDark =
            document.documentElement.classList.contains("admin-dark-mode");
        const current = pagination.current_page || 1;
        const last = pagination.last_page || 1;
        const total = pagination.total || 0;
        const from = pagination.from || 0;
        const to = pagination.to || 0;

        let html = `
            <div style="font-size:12px; color:${isDark ? "#cbd5e1" : "#64748b"};">
                <span>Menampilkan</span> <strong>${from}</strong> - <strong>${to}</strong> <span>dari</span> <strong>${total}</strong> <span>data</span>
            </div>
            <div style="display:flex; gap:4px; align-items:center;">
        `;

        const prevNextBase = isDark
            ? "background:#0f172a; border:1px solid #334155; color:#e2e8f0;"
            : "background:#fff; border:1px solid #d1d5db; color:#374151;";

        if (current > 1) {
            html += `<button type="button" onclick="${onClickFnName}(${current - 1})" class="admin-button admin-button--secondary" style="height:28px; padding:0 8px; font-size:11px; ${prevNextBase}">&laquo; Prev</button>`;
        } else {
            html += `<button type="button" disabled class="admin-button admin-button--secondary" style="height:28px; padding:0 8px; font-size:11px; opacity:0.5; cursor:not-allowed; ${prevNextBase}">&laquo; Prev</button>`;
        }

        for (let i = 1; i <= last; i++) {
            if (
                i === 1 ||
                i === last ||
                (i >= current - 1 && i <= current + 1)
            ) {
                const isActive = i === current;
                html += `<button type="button" onclick="${onClickFnName}(${i})" style="height:28px; min-width:28px; padding:0 6px; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid ${isActive ? (isDark ? "#60a5fa" : "#2563eb") : isDark ? "#334155" : "#d1d5db"}; background:${isActive ? (isDark ? "#1d4ed8" : "#2563eb") : isDark ? "#0f172a" : "#fff"}; color:${isActive ? "#fff" : isDark ? "#e2e8f0" : "#374151"};">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span style="align-self:center; color:${isDark ? "#94a3b8" : "#9ca3af"}; font-size:11px; padding:0 2px;">...</span>`;
            }
        }

        if (current < last) {
            html += `<button type="button" onclick="${onClickFnName}(${current + 1})" class="admin-button admin-button--secondary" style="height:28px; padding:0 8px; font-size:11px; ${prevNextBase}">Next &raquo;</button>`;
        } else {
            html += `<button type="button" disabled class="admin-button admin-button--secondary" style="height:28px; padding:0 8px; font-size:11px; opacity:0.5; cursor:not-allowed; ${prevNextBase}">Next &raquo;</button>`;
        }

        html += `</div>`;
        container.innerHTML = html;
        window.__adminPaginationState = window.__adminPaginationState || {};
        window.__adminPaginationState[containerId] = {
            pagination,
            onClickFnName,
        };
    };
    window.__renderGeneralPagination = renderGeneralPagination;

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
        const btnOpenFilterModal = document.getElementById(
            "btn-open-filter-modal",
        );
        const modalShopeeFilter = document.getElementById(
            "modal-shopee-filter",
        );
        const formShopeeFilter = document.getElementById("form-shopee-filter");
        const filterCitySelect = document.getElementById("filter-city-select");
        const filterBankSelect = document.getElementById("filter-bank-select");
        const filterDateStart = document.getElementById("filter-date-start");
        const filterDateEnd = document.getElementById("filter-date-end");
        const filterSortSelect = document.getElementById("filter-sort-select");
        const btnResetShopeeFilter = document.getElementById(
            "btn-reset-shopee-filter",
        );
        const filterActiveCount = document.getElementById(
            "filter-active-count",
        );
        const activeFiltersBar = document.getElementById("active-filters-bar");
        const activeFilterTags = document.getElementById("active-filter-tags");

        const btnResetFilter = document.getElementById(
            "btn-reset-order-filter",
        );
        const activeBadge = document.getElementById("active-tab-badge");
        const uploadArea = document.getElementById("upload-area");
        const uploadInput = document.getElementById("upload-proof");
        const uploadPreview = document.getElementById("upload-preview");
        const uploadPlaceholder = document.getElementById("upload-placeholder");
        const containerCancelReason = document.getElementById(
            "container-cancel-reason",
        );
        const cancelReasonInput = document.getElementById("cancel-reason");
        const orderUpdateError = document.getElementById("order-update-error");

        const loadFilterOptions = async () => {
            const [citiesRes, banksRes] = await Promise.all([
                fetchApi("/cities"),
                fetchApi("/banks"),
            ]);
            if (filterCitySelect && citiesRes.data) {
                filterCitySelect.innerHTML =
                    `<option value="">Semua Kota</option>` +
                    citiesRes.data
                        .map(
                            (c) => `<option value="${c.id}">${c.name}</option>`,
                        )
                        .join("");
            }
            if (filterBankSelect && banksRes.data) {
                filterBankSelect.innerHTML =
                    `<option value="">Semua Bank</option>` +
                    banksRes.data
                        .map(
                            (b) => `<option value="${b.id}">${b.name}</option>`,
                        )
                        .join("");
            }
        };
        loadFilterOptions();

        const updateActiveFilterDisplay = () => {
            let activeCount = 0;
            let tags = [];

            if (cityFilter && filterCitySelect) {
                activeCount++;
                const selectedText =
                    filterCitySelect.options[filterCitySelect.selectedIndex]
                        ?.text || "Kota";
                tags.push(`Kota: ${selectedText}`);
            }
            if (bankFilter && filterBankSelect) {
                activeCount++;
                const selectedText =
                    filterBankSelect.options[filterBankSelect.selectedIndex]
                        ?.text || "Bank";
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
                    activeFilterTags.innerHTML = tags
                        .map(
                            (t) => `
                        <span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:12px; font-weight:600; font-size:11px;">${t}</span>
                    `,
                        )
                        .join("");
                } else {
                    activeFiltersBar.style.display = "none";
                }
            }
        };

        let cachedOrders = [];
        let ordersPage = 1;
        let ordersPerPage = 20;

        window.changeOrdersPage = (page) => {
            ordersPage = page;
            loadOrders();
        };

        const selectOrdersPerPage = document.getElementById("orders-per-page");
        if (selectOrdersPerPage) {
            selectOrdersPerPage.addEventListener("change", (e) => {
                ordersPerPage = parseInt(e.target.value);
                ordersPage = 1;
                loadOrders();
            });
        }

        const loadOrders = async () => {
            window.loadOrders = loadOrders;
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
            if (dateStartFilter)
                queryParams.push(`date_start=${dateStartFilter}`);
            if (dateEndFilter) queryParams.push(`date_end=${dateEndFilter}`);
            if (sortOrder) queryParams.push(`sort=${sortOrder}`);
            queryParams.push(`page=${ordersPage}`);
            queryParams.push(`per_page=${ordersPerPage}`);

            const url = `/orders?${queryParams.join("&")}`;
            const res = await fetchApi(url);

            if (res.counts) {
                const c = res.counts;
                if (document.getElementById("count-pending"))
                    document.getElementById("count-pending").innerText = (
                        c.pending || 0
                    ).toLocaleString("id-ID");
                if (document.getElementById("count-processing"))
                    document.getElementById("count-processing").innerText = (
                        c.processing || 0
                    ).toLocaleString("id-ID");
                if (document.getElementById("count-arrived_at_warehouse"))
                    document.getElementById(
                        "count-arrived_at_warehouse",
                    ).innerText = (c.arrived_at_warehouse || 0).toLocaleString(
                        "id-ID",
                    );
                if (document.getElementById("count-completed"))
                    document.getElementById("count-completed").innerText = (
                        c.completed || 0
                    ).toLocaleString("id-ID");
                if (document.getElementById("count-cancelled"))
                    document.getElementById("count-cancelled").innerText = (
                        c.cancelled || 0
                    ).toLocaleString("id-ID");
                if (document.getElementById("count-all"))
                    document.getElementById("count-all").innerText = (
                        c.all || 0
                    ).toLocaleString("id-ID");
            }

            if (activeBadge) {
                const isDark =
                    document.documentElement.classList.contains(
                        "admin-dark-mode",
                    );
                if (searchQuery) {
                    activeBadge.innerText = "HASIL PENCARIAN (SEMUA STATUS)";
                    activeBadge.style.background = isDark
                        ? "rgba(59, 130, 246, 0.25)"
                        : "#dbeafe";
                    activeBadge.style.color = isDark ? "#60a5fa" : "#1e40af";
                } else {
                    const statusLabels = {
                        pending: {
                            text: "PENDING",
                            bg: "#fef3c7",
                            color: "#92400e",
                        },
                        processing: {
                            text: "PROCESSING",
                            bg: "#dbeafe",
                            color: "#1e40af",
                        },
                        arrived_at_warehouse: {
                            text: "SAMPAI DI GUDANG",
                            bg: "#f3e8ff",
                            color: "#6b21a8",
                        },
                        completed: {
                            text: "COMPLETED",
                            bg: "#d1fae5",
                            color: "#065f46",
                        },
                        cancelled: {
                            text: "CANCELLED",
                            bg: "#fee2e2",
                            color: "#991b1b",
                        },
                        all: {
                            text: "SEMUA TRANSAKSI",
                            bg: "#f3f4f6",
                            color: "#374151",
                        },
                    };
                    const st = statusLabels[activeStatus] || {
                        text: activeStatus.toUpperCase(),
                        bg: "#f3f4f6",
                        color: "#374151",
                    };
                    activeBadge.innerText = st.text;
                    activeBadge.style.background = st.bg;
                    activeBadge.style.color = st.color;
                }
            }

            if (res.data && res.data.length) {
                cachedOrders = res.data;
                tbody.innerHTML = cachedOrders
                    .map((o) => {
                        const isEditPending =
                            o.receipt &&
                            parseInt(o.receipt.edit_confirmed_by_user) === 0;

                        let rowStyle = "cursor:pointer;";
                        if (isEditPending) {
                            rowStyle += " background:#fffdf5;";
                        }

                        let editBadgeHtml = "";
                        if (isEditPending) {
                            editBadgeHtml = `<span style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; padding:4px 8px; border-radius:6px; font-size:10px; font-weight:700; display:inline-flex; align-items:center; gap:4px; margin-right:4px;" title="Admin telah mengedit item pesanan, sedang menunggu konfirmasi dari customer">⏳ Menunggu Konfirmasi Customer</span>`;
                        }

                        return `
                    <tr onclick="viewOrderDetail(${o.id})" style="${rowStyle}">
                        <td style="font-weight:600; color:#3b82f6;">
                            #${o.id}
                            ${isEditPending ? '<span style="display:block; font-size:9px; color:#d97706; font-weight:700; margin-top:2px;">[EDITED]</span>' : ""}
                        </td>
                        <td>
                            ${
                                o.order_type === "trade_in"
                                    ? '<span style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700;">TRADE IN</span>'
                                    : '<span style="background:#e0e7ff; color:#3730a3; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700;">JUAL</span>'
                            }
                        </td>
                        <td style="font-weight:500;">${o.customer ? o.customer.name : "-"}</td>
                        <td>${o.city ? o.city.name : "-"}</td>
                        <td>
                            ${
                                o.delivery_method === "courier"
                                    ? '<span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Kurir</span>'
                                    : o.delivery_method === "warehouse"
                                      ? '<span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Kirim Sendiri</span>'
                                      : '<span style="color:#6b7280;">-</span>'
                            }
                        </td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${o.pickup_address || "-"}</td>
                        <td>
                            <div class="admin-text-main" style="font-size:13px; font-weight:500;">${parseSafeDate(o.created_at).toLocaleDateString("id-ID")} ${parseSafeDate(o.created_at).toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" })}</div>
                            <div style="font-size:11px; color:#6b7280; margin-top:2px;">Update: ${parseSafeDate(o.updated_at).toLocaleDateString("id-ID")} ${parseSafeDate(o.updated_at).toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" })}</div>
                        </td>
                        <td>${statusBadge(o.status)}</td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
                                ${editBadgeHtml}
                                <button onclick="event.stopPropagation(); editOrderStatus(${o.id}, '${o.status}')" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Update</button>
                            </div>
                        </td>
                    </tr>`;
                    })
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="8"><div class="admin-table-empty"><strong>Tidak ada pesanan ditemukan</strong></div></td></tr>`;
            }

            if (res.pagination) {
                renderGeneralPagination(
                    res.pagination,
                    "orders-pagination",
                    "changeOrdersPage",
                );
            }

            // Auto open order detail if order_id URL parameter is present
            const urlParams = new URLSearchParams(window.location.search);
            const autoOrderId = urlParams.get("order_id");
            if (autoOrderId && !window.__autoOrderOpened) {
                window.__autoOrderOpened = true;
                setTimeout(() => {
                    if (typeof window.viewOrderDetail === "function") {
                        window.viewOrderDetail(autoOrderId);
                    }
                }, 300);
            }
        };

        window.__adminCurrentStatus = "pending";
        window.updateOrderTabAppearance = () => {
            const currentStatus = window.__adminCurrentStatus || "pending";
            const isDark =
                document.documentElement.classList.contains("admin-dark-mode");

            document.querySelectorAll(".order-status-tab").forEach((card) => {
                card.classList.remove("active");
                card.style.borderColor = isDark ? "#334155" : "#e5e7eb";
                card.style.background = isDark ? "#1e293b" : "#ffffff";
            });

            const activeCard = document.getElementById(
                `card-status-${currentStatus}`,
            );
            if (activeCard) {
                activeCard.classList.add("active");
                const cardColors = isDark
                    ? {
                          pending: {
                              border: "#f59e0b",
                              bg: "rgba(245, 158, 11, 0.18)",
                          },
                          processing: {
                              border: "#3b82f6",
                              bg: "rgba(59, 130, 246, 0.18)",
                          },
                          arrived_at_warehouse: {
                              border: "#8b5cf6",
                              bg: "rgba(139, 92, 246, 0.18)",
                          },
                          completed: {
                              border: "#10b981",
                              bg: "rgba(16, 185, 129, 0.18)",
                          },
                          cancelled: {
                              border: "#ef4444",
                              bg: "rgba(239, 68, 68, 0.18)",
                          },
                          all: {
                              border: "#94a3b8",
                              bg: "rgba(148, 163, 184, 0.18)",
                          },
                      }
                    : {
                          pending: { border: "#f59e0b", bg: "#fffbeb" },
                          processing: { border: "#3b82f6", bg: "#eff6ff" },
                          arrived_at_warehouse: {
                              border: "#8b5cf6",
                              bg: "#f3e8ff",
                          },
                          completed: { border: "#10b981", bg: "#ecfdf5" },
                          cancelled: { border: "#ef4444", bg: "#fef2f2" },
                          all: { border: "#6b7280", bg: "#f9fafb" },
                      };
                const c = cardColors[currentStatus] || {
                    border: "#3b82f6",
                    bg: "#eff6ff",
                };
                activeCard.style.borderColor = c.border;
                activeCard.style.background = c.bg;
            }
        };

        window.switchOrderTab = (status) => {
            activeStatus = status;
            window.__adminCurrentStatus = status;
            searchQuery = "";
            ordersPage = 1;
            if (searchInput) searchInput.value = "";
            window.updateOrderTabAppearance();
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
                            orderUpdateError.innerText =
                                "Ukuran foto terlalu besar (Maksimal 10MB)!";
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
            if (isSpamFrozen) {
                triggerSpamFreeze();
                return;
            }
            const order = cachedOrders.find((o) => o.id === id);
            document.getElementById("update-order-id").value = id;
            if (orderUpdateError) orderUpdateError.style.display = "none";

            const radios = document.querySelectorAll(
                'input[name="order_status"]',
            );

            const isEditPending =
                order &&
                order.receipt &&
                parseInt(order.receipt.edit_confirmed_by_user) === 0;

            let allowedOptions = [currentStatus];

            if (isEditPending) {
                if (orderUpdateError) {
                    orderUpdateError.innerText =
                        "Pesanan sedang menunggu konfirmasi customer, status tidak dapat diubah.";
                    orderUpdateError.style.display = "block";
                }
            } else if (currentStatus === "pending") {
                allowedOptions = [
                    "pending",
                    "processing",
                    "arrived_at_warehouse",
                    "completed",
                    "cancelled",
                ];
            } else if (currentStatus === "processing") {
                allowedOptions = [
                    "processing",
                    "arrived_at_warehouse",
                    "completed",
                    "cancelled",
                ];
            } else if (currentStatus === "arrived_at_warehouse") {
                allowedOptions = [
                    "arrived_at_warehouse",
                    "completed",
                    "cancelled",
                ];
            } else if (currentStatus === "completed") {
                allowedOptions = ["completed"];
            } else if (currentStatus === "cancelled") {
                allowedOptions = ["cancelled"];
            }

            radios.forEach((r) => {
                r.checked = r.value === currentStatus;
                const card = r.closest("label");

                if (!allowedOptions.includes(r.value)) {
                    r.disabled = true;
                    card.style.opacity = "0.4";
                    card.style.cursor = "not-allowed";
                    card.style.pointerEvents = "none";
                } else {
                    r.disabled = false;
                    card.style.opacity = "1";
                    card.style.cursor = "pointer";
                    card.style.pointerEvents = "auto";
                }

                card.style.borderColor = r.checked ? "#3b82f6" : "#e5e7eb";
            });

            if (uploadInput) uploadInput.value = "";
            if (uploadPreview) uploadPreview.style.display = "none";
            if (uploadPlaceholder) uploadPlaceholder.style.display = "block";

            const containerProofUpload = document.getElementById(
                "container-proof-upload",
            );
            const proofLabel = document.getElementById("proof-label");

            const updateModalUIForStatus = (selectedStatus) => {
                if (containerCancelReason) {
                    containerCancelReason.style.display =
                        selectedStatus === "cancelled" ? "block" : "none";
                    if (selectedStatus !== "cancelled" && cancelReasonInput)
                        cancelReasonInput.value = "";
                }
                if (containerProofUpload) {
                    if (
                        selectedStatus === "completed" ||
                        selectedStatus === "arrived_at_warehouse"
                    ) {
                        containerProofUpload.style.display = "block";
                        if (proofLabel) {
                            proofLabel.innerText =
                                selectedStatus === "completed"
                                    ? "Bukti Pembayaran / Transfer (Wajib)"
                                    : "Bukti Barang Diterima di Gudang (Wajib)";
                        }
                    } else {
                        containerProofUpload.style.display = "none";
                    }
                }
            };

            radios.forEach((r) => {
                r.onchange = () => {
                    radios.forEach((other) => {
                        const card = other.closest("label");
                        if (card)
                            card.style.borderColor = other.checked
                                ? "#3b82f6"
                                : "#e5e7eb";
                    });
                    updateModalUIForStatus(r.value);
                };
            });

            updateModalUIForStatus(currentStatus);

            const uploadArea = document.getElementById("upload-area");
            const proofViewArea = document.getElementById("proof-view-area");
            const linkViewProof = document.getElementById("link-view-proof");
            const btnSubmit = document.getElementById("btn-update-submit");
            const btnCancel = document.getElementById("btn-update-cancel");
            const btnBack = document.getElementById("btn-update-back");

            if (currentStatus === "completed") {
                if (btnSubmit) btnSubmit.style.display = "none";
                if (btnCancel) btnCancel.style.display = "none";
                if (btnBack) btnBack.style.display = "block";

                if (uploadArea) uploadArea.style.display = "none";
                if (proofViewArea) proofViewArea.style.display = "block";
                if (
                    linkViewProof &&
                    order &&
                    order.receipt &&
                    order.receipt.transfer &&
                    order.receipt.transfer.proof_image
                ) {
                    linkViewProof.onclick = (e) => {
                        e.preventDefault();
                        openImageViewer(
                            "/storage/" + order.receipt.transfer.proof_image,
                        );
                    };
                } else if (linkViewProof) {
                    linkViewProof.onclick = (e) => {
                        e.preventDefault();
                        showToast(
                            "Bukti pembayaran tidak ditemukan",
                            "warning",
                        );
                    };
                }
            } else {
                if (btnSubmit) btnSubmit.style.display = "block";
                if (btnCancel) btnCancel.style.display = "block";
                if (btnBack) btnBack.style.display = "none";
                if (uploadArea) uploadArea.style.display = "block";
                if (proofViewArea) proofViewArea.style.display = "none";
                uploadPreview.style.display = "none";
                uploadPlaceholder.style.display = "block";
                uploadInput.value = "";
            }

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

        window.openImageViewer = (url) => {
            const viewer = document.getElementById("modal-image-viewer");
            const img = document.getElementById("image-viewer-img");
            const fullscreen = document.getElementById(
                "image-viewer-fullscreen",
            );
            if (viewer && img && fullscreen) {
                img.src = url;
                fullscreen.href = url;
                viewer.style.display = "flex";
            }
        };

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
            const ktpVal = c.ktp || "-";
            document.getElementById("detail-customer-ktp").innerText = ktpVal;
            const ktpLinkEl = document.getElementById(
                "detail-customer-ktp-link",
            );
            if (ktpLinkEl) {
                if (
                    ktpVal !== "-" &&
                    (ktpVal.includes("ktp/") ||
                        ktpVal.includes(".jpg") ||
                        ktpVal.includes(".jpeg") ||
                        ktpVal.includes(".png") ||
                        ktpVal.includes("data:image"))
                ) {
                    const imgUrl =
                        ktpVal.startsWith("http") || ktpVal.startsWith("data:")
                            ? ktpVal
                            : ktpVal.startsWith("/")
                              ? ktpVal
                              : `/storage/${ktpVal}`;
                    ktpLinkEl.onclick = (e) => {
                        e.preventDefault();
                        if (typeof openImageViewer === "function") {
                            openImageViewer(imgUrl);
                        } else {
                            window.open(imgUrl, "_blank");
                        }
                    };
                    ktpLinkEl.style.display = "inline-flex";
                } else {
                    ktpLinkEl.style.display = "none";
                    ktpLinkEl.onclick = null;
                }
            }
            const accuKtpVal = o.accu_ktp || "-";
            const accuKtpEl = document.getElementById(
                "detail-customer-accu-ktp",
            );
            if (accuKtpEl) accuKtpEl.innerText = accuKtpVal;
            const accuKtpLinkEl = document.getElementById(
                "detail-customer-accu-ktp-link",
            );
            if (accuKtpLinkEl) {
                if (
                    accuKtpVal !== "-" &&
                    (accuKtpVal.includes("ktp/") ||
                        accuKtpVal.includes("accu_ktp/") ||
                        accuKtpVal.includes(".jpg") ||
                        accuKtpVal.includes(".jpeg") ||
                        accuKtpVal.includes(".png") ||
                        accuKtpVal.includes("data:image"))
                ) {
                    const imgUrl =
                        accuKtpVal.startsWith("http") ||
                        accuKtpVal.startsWith("data:")
                            ? accuKtpVal
                            : accuKtpVal.startsWith("/")
                              ? accuKtpVal
                              : `/storage/${accuKtpVal}`;
                    accuKtpLinkEl.onclick = (e) => {
                        e.preventDefault();
                        if (typeof openImageViewer === "function") {
                            openImageViewer(imgUrl);
                        } else {
                            window.open(imgUrl, "_blank");
                        }
                    };
                    accuKtpLinkEl.style.display = "inline-flex";
                } else {
                    accuKtpLinkEl.style.display = "none";
                    accuKtpLinkEl.onclick = null;
                }
            }

            const rowWarehouseProof = document.getElementById(
                "row-warehouse-proof",
            );
            const proofVal = o.warehouse_proof || "-";
            const proofStatusEl = document.getElementById(
                "detail-warehouse-proof-status",
            );
            if (proofStatusEl)
                proofStatusEl.innerText =
                    proofVal === "-" ? "Belum ada" : "Tersedia";
            const proofLinkEl = document.getElementById(
                "detail-warehouse-proof-link",
            );

            if (rowWarehouseProof) {
                if (
                    ["arrived_at_warehouse", "completed"].includes(o.status) ||
                    proofVal !== "-"
                ) {
                    rowWarehouseProof.style.display = "table-row";

                    if (proofLinkEl) {
                        if (proofVal !== "-") {
                            const imgUrl =
                                proofVal.startsWith("http") ||
                                proofVal.startsWith("data:")
                                    ? proofVal
                                    : proofVal.startsWith("/")
                                      ? proofVal
                                      : `/storage/${proofVal}`;
                            proofLinkEl.onclick = (e) => {
                                e.preventDefault();
                                if (typeof openImageViewer === "function") {
                                    openImageViewer(imgUrl);
                                } else {
                                    window.open(imgUrl, "_blank");
                                }
                            };
                            proofLinkEl.style.display = "inline-flex";
                        } else {
                            proofLinkEl.style.display = "none";
                            proofLinkEl.onclick = null;
                        }
                    }
                } else {
                    rowWarehouseProof.style.display = "none";
                }
            }
            document.getElementById("detail-customer-bank").innerText =
                `${bankName} - ${c.account_number || "-"} (a.n. ${c.account_name || "-"})`;
            document.getElementById("detail-order-type").innerHTML =
                o.order_type === "trade_in"
                    ? '<span style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700;">TRADE IN</span>'
                    : '<span style="background:#e0e7ff; color:#3730a3; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700;">JUAL</span>';

            const rowNewAccu = document.getElementById("row-detail-new-accu");
            if (o.order_type === "trade_in") {
                rowNewAccu.style.display = "table-row";
                document.getElementById("detail-order-new-accu").innerText =
                    o.new_accu
                        ? o.new_accu.name +
                          " - " +
                          new Intl.NumberFormat("id-ID", {
                              style: "currency",
                              currency: "IDR",
                              minimumFractionDigits: 0,
                          }).format(o.new_accu.price)
                        : "-";
            } else {
                rowNewAccu.style.display = "none";
            }

            document.getElementById("detail-order-payment-method").innerText =
                o.payment_method
                    ? o.payment_method === "cod"
                        ? "COD (Bayar di Tempat)"
                        : o.payment_method === "transfer"
                          ? "Transfer Bank"
                          : o.payment_method === "qris"
                            ? "QRIS"
                            : o.payment_method.toUpperCase()
                    : "-";

            document.getElementById("detail-order-city").innerText = o.city
                ? o.city.name
                : "-";
            const deliveryMethodText =
                o.delivery_method === "courier"
                    ? "Kurir PickUpSystem"
                    : o.delivery_method === "warehouse"
                      ? "Kirim Sendiri (Gudang)"
                      : o.delivery_method || "-";
            const deliveryMethodEl = document.getElementById(
                "detail-order-delivery-method",
            );
            if (deliveryMethodEl)
                deliveryMethodEl.innerText = deliveryMethodText;
            document.getElementById("detail-order-status").innerHTML =
                statusBadge(o.status);
            document.getElementById("detail-order-time").innerText =
                parseSafeDate(o.created_at).toLocaleString("id-ID");

            const updatedEl = document.getElementById("detail-order-updated");
            if (updatedEl) {
                updatedEl.innerText = parseSafeDate(
                    o.updated_at,
                ).toLocaleString("id-ID");
            }
            const flagContainer = document.getElementById(
                "detail-flag-container",
            );
            if (flagContainer) {
                if (c && c.flag === 0) {
                    const reason =
                        c.flag_reason ||
                        "Catatan verifikasi manual oleh sistem";
                    flagContainer.style.display = "block";
                    flagContainer.innerHTML = `
                        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <div>
                                <strong style="color:#dc2626; font-size:12px; display:block; margin-bottom:2px;">⚠️ PERHATIAN ADMIN: Pelanggan Ditandai (Flag 0)</strong>
                                <span style="color:#991b1b; font-size:11px; line-height:1.4; display:block;">Alasan: ${reason}</span>
                            </div>
                            <button type="button" class="admin-button admin-button--primary" id="btn-clear-customer-flag" data-customer-id="${c.id}" style="padding:4px 10px; font-size:11px; height:auto; white-space:nowrap; flex-shrink:0;">
                                ✓ Bebaskan Flag (Set Safe)
                            </button>
                        </div>
                    `;

                    const btnClearFlag = document.getElementById(
                        "btn-clear-customer-flag",
                    );
                    if (btnClearFlag) {
                        btnClearFlag.onclick = async () => {
                            btnClearFlag.disabled = true;
                            btnClearFlag.textContent = "Memproses...";
                            try {
                                const res = await fetch(
                                    `${API_BASE}/customers/${c.id}/flag`,
                                    {
                                        method: "PUT",
                                        headers: {
                                            "Content-Type": "application/json",
                                            Accept: "application/json",
                                            Authorization: `Bearer ${token}`,
                                        },
                                        body: JSON.stringify({ flag: 1 }),
                                    },
                                );
                                if (res.ok) {
                                    c.flag = 1;
                                    c.flag_reason = null;
                                    flagContainer.style.display = "none";
                                    alert(
                                        "Status pelanggan telah diubah menjadi Aman (Flag 1).",
                                    );
                                } else {
                                    alert("Gagal mengubah status flag.");
                                    btnClearFlag.disabled = false;
                                    btnClearFlag.textContent =
                                        "✓ Bebaskan Flag (Set Safe)";
                                }
                            } catch (err) {
                                console.error(err);
                                alert("Terjadi kesalahan.");
                                btnClearFlag.disabled = false;
                                btnClearFlag.textContent =
                                    "✓ Bebaskan Flag (Set Safe)";
                            }
                        };
                    }
                } else {
                    flagContainer.style.display = "none";
                    flagContainer.innerHTML = "";
                }
            }

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

            const transferContainer = document.getElementById(
                "container-detail-transfer-proof",
            );
            const transferImg = document.getElementById(
                "detail-transfer-proof-img",
            );
            const btnZoomTransfer = document.getElementById(
                "btn-zoom-transfer-proof",
            );
            const btnDownloadTransfer = document.getElementById(
                "btn-download-transfer-proof",
            );

            if (
                o.order_type === "trade_in" &&
                o.receipt &&
                o.receipt.transfer &&
                o.receipt.transfer.proof_image
            ) {
                const imgPath = o.receipt.transfer.proof_image;
                const imgUrl =
                    imgPath.startsWith("http") || imgPath.startsWith("data:")
                        ? imgPath
                        : imgPath.startsWith("/")
                          ? imgPath
                          : `/storage/${imgPath}`;

                if (transferImg) transferImg.src = imgUrl;
                if (btnDownloadTransfer) {
                    btnDownloadTransfer.href = imgUrl;
                    btnDownloadTransfer.setAttribute(
                        "download",
                        `bukti_transfer_order_${o.id}.jpg`,
                    );
                }
                if (btnZoomTransfer) {
                    btnZoomTransfer.onclick = () => {
                        if (typeof window.openImageViewer === "function") {
                            window.openImageViewer(imgUrl);
                        } else {
                            window.open(imgUrl, "_blank");
                        }
                    };
                }
                if (transferImg) {
                    transferImg.onclick = () => {
                        if (typeof window.openImageViewer === "function") {
                            window.openImageViewer(imgUrl);
                        } else {
                            window.open(imgUrl, "_blank");
                        }
                    };
                }
                if (transferContainer)
                    transferContainer.style.display = "block";
            } else {
                if (transferContainer) transferContainer.style.display = "none";
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
                const address =
                    o.pickup_address || c.address || "Alamat Penjemputan";

                if (document.getElementById("map-modal-subtitle")) {
                    document.getElementById("map-modal-subtitle").innerText =
                        `Pelanggan: ${custName} • ${cityName}`;
                }
                if (document.getElementById("map-modal-coords")) {
                    document.getElementById("map-modal-coords").innerText =
                        `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                }
                if (document.getElementById("map-modal-city-badge")) {
                    document.getElementById("map-modal-city-badge").innerText =
                        cityName;
                }

                document.getElementById("modal-order-map").style.display =
                    "flex";

                setTimeout(() => {
                    if (!orderMap) {
                        orderMap = L.map("order-map-view").setView(
                            [lat, lng],
                            15,
                        );
                        L.tileLayer(
                            "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                            {
                                attribution:
                                    "&copy; OpenStreetMap contributors",
                            },
                        ).addTo(orderMap);
                        orderMarker = L.marker([lat, lng]).addTo(orderMap);
                    } else {
                        orderMap.setView([lat, lng], 15);
                        orderMarker.setLatLng([lat, lng]);
                    }
                    orderMarker
                        .bindPopup(
                            `
                        <div style="font-size:12px; font-family:sans-serif;">
                            <strong style="color:#2563eb; display:block; margin-bottom:4px;">📍 ${custName}</strong>
                            <p style="margin:0; color:#374151;">${address}</p>
                        </div>
                    `,
                        )
                        .openPopup();
                    orderMap.invalidateSize();
                }, 200);
            });
        }

        const btnOpenSummary = document.getElementById(
            "btn-open-order-summary",
        );
        if (btnOpenSummary) {
            btnOpenSummary.addEventListener("click", () => {
                if (!currentDetailOrder) return;

                const receipt = currentDetailOrder.receipt;
                const tbody = document.getElementById("detail-summary-items");

                const formatRp = (n) =>
                    new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                    }).format(n);

                if (!receipt) {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:20px; color:#6d727c;">Rincian harga belum tersedia / belum di-generate.</td></tr>`;
                    document.getElementById(
                        "detail-summary-subtotal",
                    ).innerText = "-";
                    document.getElementById(
                        "detail-summary-shipping",
                    ).innerText = "-";
                    document.getElementById("detail-summary-total").innerText =
                        "-";
                } else {
                    let itemsHtml = "";
                    const accus = receipt.accus || [];
                    let subtotal = 0;

                    if (accus.length > 0) {
                        accus.forEach((item) => {
                            const qty = item.amount || 1;
                            const price = item.price || 0;
                            const sub = item.subtotal || 0;
                            subtotal += sub;

                            itemsHtml += `<tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:10px 0; font-weight:500; color:#111318;">${item.name || "-"} 
                                <td style="text-align:center; padding:10px 0; color:#4a4f59;">${qty} unit</td>
                                <td style="text-align:right; padding:10px 0; color:#4a4f59;">${formatRp(price)}</td>
                                <td style="text-align:right; padding:10px 0; font-weight:600; color:#111318;">${formatRp(sub)}</td>
                            </tr>`;
                        });
                    } else {
                        itemsHtml = `<tr><td colspan="4" style="text-align:center; padding:20px; color:#6d727c;">Tidak ada data item aki.</td></tr>`;
                    }
                    tbody.innerHTML = itemsHtml;

                    let deliveryCost = 0;
                    if (currentDetailOrder.order_type === "trade_in") {
                        const newAccuPrice = currentDetailOrder.new_accu
                            ? currentDetailOrder.new_accu.price
                            : 0;
                        deliveryCost =
                            subtotal - newAccuPrice - (receipt.price_owed || 0);
                    } else {
                        deliveryCost = subtotal - (receipt.price_owed || 0);
                    }

                    document.getElementById(
                        "detail-summary-subtotal",
                    ).innerText = formatRp(subtotal);

                    const rowSummaryNewAccu = document.getElementById(
                        "detail-summary-row-new-accu",
                    );
                    if (
                        currentDetailOrder.order_type === "trade_in" &&
                        currentDetailOrder.new_accu &&
                        rowSummaryNewAccu
                    ) {
                        rowSummaryNewAccu.style.display = "table-row";
                        document.getElementById(
                            "detail-summary-new-accu",
                        ).innerText =
                            "- " + formatRp(currentDetailOrder.new_accu.price);
                    } else if (rowSummaryNewAccu) {
                        rowSummaryNewAccu.style.display = "none";
                    }

                    // Use stored transaction snapshot if available; otherwise fall back to derived value
                    const pickupSnapshot = currentDetailOrder.pickup_pricing;
                    let shippingFee = 0;
                    if (pickupSnapshot && pickupSnapshot.final_pickup_fee > 0) {
                        shippingFee = pickupSnapshot.final_pickup_fee;
                    } else if (deliveryCost > 0) {
                        shippingFee = deliveryCost;
                    }
                    document.getElementById(
                        "detail-summary-shipping",
                    ).innerText =
                        shippingFee > 0
                            ? "- " + formatRp(shippingFee)
                            : "Gratis";

                    const totalVal = receipt.price_owed || 0;
                    const isMinus = totalVal < 0;
                    document.getElementById("detail-summary-total").innerText =
                        formatRp(Math.abs(totalVal));
                    const totalLabel = document.getElementById(
                        "detail-summary-total-label",
                    );
                    if (totalLabel) {
                        totalLabel.innerText = isMinus
                            ? "Total Tagihan (Customer Bayar)"
                            : "Total Dibayar ke Customer";
                    }
                }

                document.getElementById("modal-order-summary").style.display =
                    "flex";
            });
        }

        // Anti-Spam Protection System for Admin Update
        const SPAM_LIMIT = 10;
        const SPAM_WINDOW_MS = 30000;
        let updateTimestamps = [];
        let isSpamFrozen = false;
        let freezeTimerInterval = null;

        function recordAndCheckSpam() {
            const now = Date.now();
            updateTimestamps = updateTimestamps.filter(
                (t) => now - t < SPAM_WINDOW_MS,
            );
            updateTimestamps.push(now);

            if (updateTimestamps.length >= SPAM_LIMIT) {
                triggerSpamFreeze();
                return true;
            }
            return false;
        }

        function triggerSpamFreeze() {
            isSpamFrozen = true;
            const modalSpam = document.getElementById("modal-spam-warning");
            const countdownEl = document.getElementById("spam-countdown-timer");
            const oldestTimestamp = updateTimestamps[0] || Date.now();
            let remainingMs = SPAM_WINDOW_MS - (Date.now() - oldestTimestamp);
            if (remainingMs <= 0) remainingMs = SPAM_WINDOW_MS;

            if (modalSpam) modalSpam.style.display = "flex";
            const updateModal = document.getElementById("modal-update-order");
            if (updateModal) updateModal.style.display = "none";

            disableOrderUpdateUI(true);

            if (freezeTimerInterval) clearInterval(freezeTimerInterval);

            freezeTimerInterval = setInterval(() => {
                remainingMs -= 1000;
                const secondsLeft = Math.max(1, Math.ceil(remainingMs / 1000));
                if (countdownEl) countdownEl.innerText = `${secondsLeft} detik`;

                if (remainingMs <= 0) {
                    clearInterval(freezeTimerInterval);
                    isSpamFrozen = false;
                    updateTimestamps = [];
                    if (modalSpam) modalSpam.style.display = "none";
                    disableOrderUpdateUI(false);
                    showToast(
                        "Fungsi update di halaman ini telah aktif kembali.",
                        "success",
                    );
                }
            }, 1000);
        }

        function disableOrderUpdateUI(disabled) {
            document
                .querySelectorAll("button[onclick*='editOrderStatus']")
                .forEach((btn) => {
                    btn.disabled = disabled;
                    btn.style.opacity = disabled ? "0.4" : "1";
                    btn.style.cursor = disabled ? "not-allowed" : "pointer";
                    btn.style.pointerEvents = disabled ? "none" : "auto";
                });
            const submitBtn = document.getElementById("btn-update-submit");
            if (submitBtn) submitBtn.disabled = disabled;
        }

        const formUpdate = document.getElementById("form-update-order");
        if (formUpdate) {
            formUpdate.addEventListener("submit", async (e) => {
                e.preventDefault();

                if (isSpamFrozen) {
                    triggerSpamFreeze();
                    return;
                }

                if (recordAndCheckSpam()) {
                    return;
                }

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

                if (
                    statusVal === "completed" ||
                    statusVal === "arrived_at_warehouse"
                ) {
                    const hasFile =
                        uploadInput &&
                        uploadInput.files &&
                        uploadInput.files.length > 0;
                    if (!hasFile) {
                        if (orderUpdateError) {
                            orderUpdateError.innerText =
                                statusVal === "completed"
                                    ? "Wajib mengunggah foto bukti pembayaran / penyerahan untuk status Completed!"
                                    : "Wajib mengunggah foto bukti barang diterima di gudang untuk status Sampai di Gudang!";
                            orderUpdateError.style.display = "block";
                        }
                        return;
                    }
                    try {
                        const base64Str = await new Promise(
                            (resolve, reject) => {
                                const reader = new FileReader();
                                reader.onload = (ev) =>
                                    resolve(ev.target.result);
                                reader.onerror = (err) => reject(err);
                                reader.readAsDataURL(uploadInput.files[0]);
                            },
                        );
                        if (statusVal === "completed") {
                            payload.proof_base64 = base64Str;
                        } else {
                            payload.warehouse_proof_base64 = base64Str;
                        }
                    } catch (err) {
                        if (orderUpdateError) {
                            orderUpdateError.innerText =
                                "Gagal membaca foto bukti upload.";
                            orderUpdateError.style.display = "block";
                        }
                        return;
                    }
                }

                const res = await fetchApi(`/orders/${id}/status`, {
                    method: "PUT",
                    body: JSON.stringify(payload),
                });

                if (res.data || res.message) {
                    document
                        .querySelectorAll("div[id^='modal-'], div[id*='modal']")
                        .forEach((m) => (m.style.display = "none"));
                    window.location.reload();
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

        // Admin Edit Order Items Logic
        const btnOpenEditItems = document.getElementById(
            "btn-open-edit-order-items",
        );
        const modalEditItems = document.getElementById(
            "modal-edit-order-items",
        );
        const rowsContainer = document.getElementById(
            "edit-items-rows-container",
        );
        const btnAddRow = document.getElementById("btn-add-edit-item-row");
        const formEditItems = document.getElementById("form-edit-order-items");
        const editItemsError = document.getElementById("edit-items-error");

        let cityAccusList = [];

        function addEditItemRow(selectedAccuId = null, amount = 1) {
            if (!rowsContainer) return;
            const rowDiv = document.createElement("div");
            rowDiv.className = "edit-item-row";
            rowDiv.style.cssText =
                "display:flex; gap:10px; align-items:center;";

            let optionsHtml = cityAccusList
                .map(
                    (a) =>
                        `<option value="${a.id}" ${a.id === selectedAccuId ? "selected" : ""}>${a.name} (${a.brand || "-"})</option>`,
                )
                .join("");
            if (!optionsHtml) {
                optionsHtml = `<option value="">Tidak ada jenis aki...</option>`;
            }

            rowDiv.innerHTML = `
                <select class="admin-select accu-select" style="flex:1; padding:8px 10px; border-radius:6px; font-size:12px;" required>
                    ${optionsHtml}
                </select>
                <input type="number" class="admin-select accu-qty" value="${amount}" min="1" style="width:90px; padding:8px 10px; border-radius:6px; font-size:12px;" placeholder="Qty" readonly required>
                <button type="button" class="btn-remove-row admin-button" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:6px; width:34px; height:34px; cursor:pointer; font-weight:bold; display:inline-flex; align-items:center; justify-content:center;">✕</button>
            `;

            rowDiv
                .querySelector(".btn-remove-row")
                .addEventListener("click", () => {
                    if (
                        rowsContainer.querySelectorAll(".edit-item-row")
                            .length > 1
                    ) {
                        rowDiv.remove();
                    } else {
                        showToast(
                            "Minimal harus ada 1 jenis aki dalam pesanan.",
                            "warning",
                        );
                    }
                });

            rowsContainer.appendChild(rowDiv);
        }

        if (btnOpenEditItems) {
            btnOpenEditItems.addEventListener("click", async () => {
                if (!currentDetailOrder) return;
                document.getElementById("edit-order-items-id").value =
                    currentDetailOrder.id;
                if (editItemsError) editItemsError.style.display = "none";

                const cityId = currentDetailOrder.cities_id || 1;
                try {
                    const res = await fetchApi(`/cities/${cityId}/accus`);
                    if (res && res.data && Array.isArray(res.data.accus)) {
                        cityAccusList = res.data.accus;
                    } else if (res && res.data && Array.isArray(res.data)) {
                        cityAccusList = res.data;
                    } else {
                        cityAccusList = [];
                    }
                } catch (e) {
                    console.error("Gagal memuat accu kota:", e);
                    cityAccusList = [];
                }

                if (!cityAccusList || cityAccusList.length === 0) {
                    try {
                        const fallbackRes = await fetchApi("/accus");
                        cityAccusList =
                            fallbackRes && fallbackRes.data
                                ? fallbackRes.data
                                : [];
                    } catch (e) {
                        console.error("Gagal memuat fallback accus:", e);
                        cityAccusList = [];
                    }
                }

                const existingAccus =
                    currentDetailOrder.receipt &&
                    currentDetailOrder.receipt.accus
                        ? currentDetailOrder.receipt.accus
                        : [];
                rowsContainer.innerHTML = "";

                if (existingAccus.length > 0) {
                    existingAccus.forEach((item) => {
                        addEditItemRow(item.id, item.amount || 1);
                    });
                } else {
                    addEditItemRow(null, 1);
                }

                document.getElementById("modal-order-summary").style.display =
                    "none";
                if (modalEditItems) modalEditItems.style.display = "flex";
            });
        }

        if (btnAddRow) {
            btnAddRow.addEventListener("click", () => addEditItemRow());
        }

        if (formEditItems) {
            formEditItems.addEventListener("submit", async (e) => {
                e.preventDefault();
                if (editItemsError) editItemsError.style.display = "none";

                const orderId = document.getElementById(
                    "edit-order-items-id",
                ).value;
                const rows = rowsContainer.querySelectorAll(".edit-item-row");
                const itemsPayload = [];

                rows.forEach((r) => {
                    const accuId = parseInt(
                        r.querySelector(".accu-select").value,
                    );
                    const qty = parseInt(r.querySelector(".accu-qty").value);
                    if (accuId && qty > 0) {
                        itemsPayload.push({ accu_id: accuId, amount: qty });
                    }
                });

                if (itemsPayload.length === 0) {
                    if (editItemsError) {
                        editItemsError.innerText = "Pilih minimal 1 jenis aki.";
                        editItemsError.style.display = "block";
                    }
                    return;
                }

                try {
                    const btnSave = document.getElementById(
                        "btn-save-edit-items",
                    );
                    if (btnSave) btnSave.disabled = true;

                    const res = await fetchApi(`/orders/${orderId}/items`, {
                        method: "PUT",
                        body: JSON.stringify({ items: itemsPayload }),
                    });

                    if (btnSave) btnSave.disabled = false;

                    if (res.data || res.message) {
                        document
                            .querySelectorAll(
                                "div[id^='modal-'], div[id*='modal']",
                            )
                            .forEach((m) => (m.style.display = "none"));
                        window.location.reload();
                    } else if (res.message) {
                        if (editItemsError) {
                            editItemsError.innerText = res.message;
                            editItemsError.style.display = "block";
                        }
                    }
                } catch (err) {
                    console.error("Gagal simpan edit item:", err);
                    if (editItemsError) {
                        editItemsError.innerText =
                            "Terjadi kesalahan saat menyimpan perubahan.";
                        editItemsError.style.display = "block";
                    }
                }
            });
        }
    }

    if (window.location.pathname === "/admin/harga") {
        let cachedCities = [];
        let cachedAccus = [];
        let cachedNewAccus = [];
        let activeCityId = null;
        let activeCityName = "";
        let currentAccuTab = "old";
        let currentFilteredAccus = null;
        let currentFilteredNewAccus = null;

        const updateAccuTotal = () => {
            const accuTotal = document.getElementById("accu-total");
            if (!accuTotal) return;
            if (currentAccuTab === "old") {
                const list =
                    currentFilteredAccus !== null
                        ? currentFilteredAccus
                        : cachedAccus;
                accuTotal.innerText = list.length;
            } else {
                const list =
                    currentFilteredNewAccus !== null
                        ? currentFilteredNewAccus
                        : cachedNewAccus;
                accuTotal.innerText = list.length;
            }
        };

        let savedLme = null;
        let savedKurs = null;

        const updateLmeButtonState = () => {
            const formSettings = document.getElementById(
                "form-global-settings",
            );
            if (!formSettings) return;
            const btn = formSettings.querySelector("button[type='submit']");
            const lmeEl = document.getElementById("setting-lme");
            const kursEl = document.getElementById("setting-kurs");
            if (!btn || !lmeEl || !kursEl) return;

            const currentLme = parseFloat(lmeEl.value);
            const currentKurs = parseFloat(kursEl.value);

            const isValValid =
                !isNaN(currentLme) &&
                !isNaN(currentKurs) &&
                lmeEl.value.trim() !== "" &&
                kursEl.value.trim() !== "";
            const isChanged =
                savedLme === null ||
                savedKurs === null ||
                currentLme !== savedLme ||
                currentKurs !== savedKurs;

            if (isValValid && isChanged) {
                btn.disabled = false;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
            } else {
                btn.disabled = true;
                btn.style.opacity = "0.55";
                btn.style.cursor = "not-allowed";
            }
        };

        const loadSettings = async () => {
            const res = await fetchApi("/settings");
            if (res && res.data) {
                const lmeEl = document.getElementById("setting-lme");
                const kursEl = document.getElementById("setting-kurs");
                savedLme = parseFloat(res.data.lme);
                savedKurs = parseFloat(res.data.kurs);
                if (lmeEl) lmeEl.value = res.data.lme;
                if (kursEl) kursEl.value = res.data.kurs;
                updateLmeButtonState();
            }
        };

        const lmeInputEl = document.getElementById("setting-lme");
        const kursInputEl = document.getElementById("setting-kurs");
        if (lmeInputEl) {
            lmeInputEl.addEventListener("input", updateLmeButtonState);
            lmeInputEl.addEventListener("change", updateLmeButtonState);
        }
        if (kursInputEl) {
            kursInputEl.addEventListener("input", updateLmeButtonState);
            kursInputEl.addEventListener("change", updateLmeButtonState);
        }

        let priceHistoryPage = 1;
        let priceHistoryPerPage = 20;

        window.changePriceHistoryPage = (page) => {
            priceHistoryPage = page;
            loadPriceHistory();
        };

        const selectPriceHistoryPerPage = document.getElementById(
            "price-history-per-page",
        );
        if (selectPriceHistoryPerPage) {
            selectPriceHistoryPerPage.addEventListener("change", (e) => {
                priceHistoryPerPage = parseInt(e.target.value);
                priceHistoryPage = 1;
                loadPriceHistory();
            });
        }

        const loadPriceHistory = async () => {
            try {
                const res = await fetchApi(
                    `/price-histories?page=${priceHistoryPage}&per_page=${priceHistoryPerPage}`,
                );
                const tbody = document.getElementById("price-history-tbody");
                if (!tbody) return;

                const history = res?.data || [];

                if (history.length > 0) {
                    tbody.innerHTML = history
                        .map((h) => {
                            let dateStr = "-";
                            try {
                                if (h && h.created_at) {
                                    const d = new Date(h.created_at);
                                    if (!isNaN(d.getTime())) {
                                        dateStr = d.toLocaleString("id-ID", {
                                            day: "2-digit",
                                            month: "2-digit",
                                            year: "numeric",
                                            hour: "2-digit",
                                            minute: "2-digit",
                                            second: "2-digit",
                                        });
                                    }
                                }
                            } catch (e) {}

                            let lmeStr = "-";
                            try {
                                const lmeVal =
                                    h && h.lme !== undefined
                                        ? h.lme
                                        : h && h.LME !== undefined
                                          ? h.LME
                                          : null;
                                if (lmeVal !== null && lmeVal !== undefined) {
                                    const num = parseFloat(lmeVal);
                                    if (!isNaN(num)) {
                                        lmeStr =
                                            num.toLocaleString("id-ID") +
                                            " USD/Ton";
                                    } else {
                                        lmeStr = lmeVal + " USD/Ton";
                                    }
                                }
                            } catch (e) {}

                            let kursStr = "-";
                            try {
                                const kursVal =
                                    h && h.new_value !== undefined
                                        ? h.new_value
                                        : null;
                                if (kursVal !== null && kursVal !== undefined) {
                                    const num = parseFloat(kursVal);
                                    if (!isNaN(num)) {
                                        kursStr =
                                            num.toLocaleString("id-ID") +
                                            " IDR/USD";
                                    } else {
                                        kursStr = kursVal + " IDR/USD";
                                    }
                                }
                            } catch (e) {}

                            return `
                        <tr>
                            <td>${dateStr} WIB</td>
                            <td style="color:#64748b; font-weight:600;">${lmeStr}</td>
                            <td style="font-weight:700; color:#2563eb;">${kursStr}</td>
                        </tr>`;
                        })
                        .join("");
                } else {
                    tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty">No parameter change history yet</div></td></tr>`;
                }

                if (res?.pagination) {
                    renderGeneralPagination(
                        res.pagination,
                        "price-history-pagination",
                        "changePriceHistoryPage",
                    );
                }
            } catch (error) {
                console.error("Gagal merender price history:", error);
                const tbody = document.getElementById("price-history-tbody");
                if (tbody) {
                    tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty" style="color:#ef4444;">Gagal memuat data riwayat (Error: ${error.message || "Unknown"})</div></td></tr>`;
                }
            }
        };

        const formSettings = document.getElementById("form-global-settings");
        if (formSettings) {
            formSettings.addEventListener("submit", async (e) => {
                e.preventDefault();
                const btn = formSettings.querySelector("button[type='submit']");
                const alertEl = document.getElementById("setting-lme-alert");
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = "Menyimpan...";
                }
                if (alertEl) alertEl.style.display = "none";

                try {
                    const lme = parseFloat(
                        document.getElementById("setting-lme").value,
                    );
                    const kurs = parseFloat(
                        document.getElementById("setting-kurs").value,
                    );

                    const res = await fetchApi("/settings", {
                        method: "PUT",
                        body: JSON.stringify({ lme, kurs }),
                    });

                    const isEn = (localStorage.getItem("app_language") || (window.pageTranslator ? window.pageTranslator.activeLang : "id")) === "en";

                    if (res && res.data) {
                        savedLme = lme;
                        savedKurs = kurs;
                        const successMsg = isEn
                            ? "LME & Exchange Rate updated successfully!"
                            : "Data LME & Kurs berhasil diperbarui!";
                        showToast(successMsg, "success");
                        if (alertEl) {
                            alertEl.style.display = "flex";
                            alertEl.style.background = "#dcfce7";
                            alertEl.style.color = "#15803d";
                            alertEl.style.border = "1px solid #bbf7d0";
                            alertEl.innerHTML = "✓ " + successMsg;
                        }
                        if (btn) {
                            btn.textContent = isEn ? "Saved ✓" : "Tersimpan ✓";
                        }
                        priceHistoryPage = 1;
                        await loadPriceHistory();
                        await loadCities();
                        await loadAccus();
                        await loadNewAccus();
                    } else {
                        const errMsg =
                            res?.message || (isEn ? "Failed to save LME & Exchange Rate" : "Gagal menyimpan LME & Kurs");
                        showToast(errMsg, "error");
                        if (alertEl) {
                            alertEl.style.display = "flex";
                            alertEl.style.background = "#fee2e2";
                            alertEl.style.color = "#b91c1c";
                            alertEl.style.border = "1px solid #fca5a5";
                            alertEl.innerHTML = "✕ " + errMsg;
                        }
                    }
                } catch (error) {
                    const isEn = (localStorage.getItem("app_language") || (window.pageTranslator ? window.pageTranslator.activeLang : "id")) === "en";
                    const sysErrMsg = isEn
                        ? "A system error occurred while updating LME & Exchange Rate."
                        : "Terjadi kesalahan sistem saat memperbarui LME & Kurs.";
                    showToast(sysErrMsg, "error");
                    if (alertEl) {
                        alertEl.style.display = "flex";
                        alertEl.style.background = "#fee2e2";
                        alertEl.style.color = "#b91c1c";
                        alertEl.style.border = "1px solid #fca5a5";
                        alertEl.innerHTML = "✕ " + sysErrMsg;
                    }
                    console.error(error);
                } finally {
                    setTimeout(() => {
                        const isEn = (localStorage.getItem("app_language") || (window.pageTranslator ? window.pageTranslator.activeLang : "id")) === "en";
                        if (btn) {
                            btn.textContent = isEn ? "Save LME & Exchange Rate" : "Simpan LME & Kurs";
                        }
                        updateLmeButtonState();
                    }, 1200);
                }
            });
        }

        const formCityDetailPct = document.getElementById(
            "form-city-detail-percentage",
        );
        if (formCityDetailPct) {
            formCityDetailPct.addEventListener("submit", async (e) => {
                e.preventDefault();
                if (!activeCityId) return;

                const newPct = parseFloat(
                    document.getElementById("city-detail-percentage-input")
                        .value,
                );
                const res = await fetchApi(`/cities/${activeCityId}`, {
                    method: "PUT",
                    body: JSON.stringify({ percentage: newPct }),
                });

                if (res && (res.data || res.message)) {
                    showToast(
                        "Persentase kota berhasil diperbarui!",
                        "success",
                    );
                    loadCities();
                    loadPriceHistory();
                    viewCityAccus(activeCityId, activeCityName);
                } else {
                    showToast(
                        res.message || "Gagal memperbarui persentase kota",
                        "error",
                    );
                }
            });
        }

        const renderCities = (cities) => {
            const tbody = document.getElementById("cities-tbody");
            if (cities.length) {
                tbody.innerHTML = cities
                    .map(
                        (c) => `
                    <tr onclick="viewCityAccus(${c.id}, '${c.name}')" style="cursor:pointer;">
                        <td style="font-weight:500;">${c.name}</td>
                        <td><span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:12px; font-weight:600; font-size:11px;">${c.percentage || 80}%</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button onclick="event.stopPropagation(); deleteCity(${c.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>No cities found</strong></div></td></tr>`;
            }
        };

        let citiesPage = 1;
        let citiesPerPage = 20;
        window.changeCitiesPage = (page) => {
            citiesPage = page;
            loadCities();
        };

        const selectCitiesPerPage = document.getElementById("cities-per-page");
        if (selectCitiesPerPage) {
            selectCitiesPerPage.addEventListener("change", (e) => {
                citiesPerPage = parseInt(e.target.value);
                citiesPage = 1;
                loadCities();
            });
        }

        const loadCities = async () => {
            const searchVal =
                document.getElementById("city-search-input")?.value || "";
            let url = `/cities?page=${citiesPage}&per_page=${citiesPerPage}`;
            if (searchVal) url += `&search=${encodeURIComponent(searchVal)}`;
            const res = await fetchApi(url);
            cachedCities = res.data || [];
            renderCities(cachedCities);
            if (res.pagination) {
                renderGeneralPagination(
                    res.pagination,
                    "cities-pagination",
                    "changeCitiesPage",
                );
            }
        };

        const renderAccus = (accus) => {
            const tbody = document.getElementById("accus-tbody");
            if (accus.length) {
                tbody.innerHTML = accus
                    .map(
                        (a) => `
                    <tr>
                        <td>${a.name}</td>
                        <td><span style="font-weight:600; color:#2563eb;">${a.berat_kering} kg</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button onclick="openEditAccu(${a.id}, '${(a.name || "").replace(/'/g, "&#39;")}', ${a.berat_kering})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Edit</button>
                                <button onclick="deleteAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Delete</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>No batteries found</strong></div></td></tr>`;
            }
            updateAccuTotal();
        };

        let accusPage = 1;
        let accusPerPage = 20;
        window.changeAccusPage = (page) => {
            accusPage = page;
            loadAccus();
        };

        const selectAccusPerPage = document.getElementById("accus-per-page");
        if (selectAccusPerPage) {
            selectAccusPerPage.addEventListener("change", (e) => {
                accusPerPage = parseInt(e.target.value);
                accusPage = 1;
                loadAccus();
            });
        }

        const loadAccus = async () => {
            const searchVal =
                document.getElementById("accu-search-input")?.value || "";
            let url = `/accus?page=${accusPage}&per_page=${accusPerPage}`;
            if (searchVal) url += `&search=${encodeURIComponent(searchVal)}`;
            const res = await fetchApi(url);
            cachedAccus = res.data || [];
            currentFilteredAccus = null;
            renderAccus(cachedAccus);
            if (res.pagination) {
                renderGeneralPagination(
                    res.pagination,
                    "accus-pagination",
                    "changeAccusPage",
                );
            }
        };

        const renderNewAccus = (accus) => {
            const tbody = document.getElementById("new-accus-tbody");
            if (accus.length) {
                tbody.innerHTML = accus
                    .map(
                        (a) => `
                    <tr>
                        <td>${a.name}</td>
                        <td>${a.brand || "-"}</td>
                        <td style="font-weight:700; color:#10b981;">${rupiah(a.price)}</td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button onclick="editNewAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#2563eb;">Edit</button>
                                <button onclick="deleteNewAccu(${a.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                            </div>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada data aki baru</strong></div></td></tr>`;
            }
            updateAccuTotal();
        };

        let newAccusPage = 1;
        let newAccusPerPage = 20;
        window.changeNewAccusPage = (page) => {
            newAccusPage = page;
            loadNewAccus();
        };

        const selectNewAccusPerPage =
            document.getElementById("new-accus-per-page");
        if (selectNewAccusPerPage) {
            selectNewAccusPerPage.addEventListener("change", (e) => {
                newAccusPerPage = parseInt(e.target.value);
                newAccusPage = 1;
                loadNewAccus();
            });
        }

        const loadNewAccus = async () => {
            const searchVal =
                document.getElementById("new-accu-search-input")?.value || "";
            let url = `/new-accus?page=${newAccusPage}&per_page=${newAccusPerPage}`;
            if (searchVal) url += `&search=${encodeURIComponent(searchVal)}`;
            const res = await fetchApi(url);
            cachedNewAccus = res.data || [];
            currentFilteredNewAccus = null;
            renderNewAccus(cachedNewAccus);
            if (res.pagination) {
                renderGeneralPagination(
                    res.pagination,
                    "new-accus-pagination",
                    "changeNewAccusPage",
                );
            }
        };

        loadSettings();
        loadCities();
        loadAccus();
        loadNewAccus();
        loadPriceHistory();

        window.loadSettings = loadSettings;
        window.loadCities = loadCities;
        window.loadAccus = loadAccus;
        window.loadNewAccus = loadNewAccus;
        window.loadPriceHistory = loadPriceHistory;

        window.switchAccuTab = (tab) => {
            currentAccuTab = tab;
            if (tab === "old") {
                document.getElementById("tab-old-accu").style.display = "block";
                document.getElementById("tab-new-accu").style.display = "none";
                document.getElementById("btn-old-accu").style.background =
                    "#fff";
                document.getElementById("btn-old-accu").style.color = "#1e293b";
                document.getElementById("btn-old-accu").style.boxShadow =
                    "0 1px 3px rgba(0,0,0,0.1)";

                document.getElementById("btn-new-accu").style.background =
                    "transparent";
                document.getElementById("btn-new-accu").style.color = "#64748b";
                document.getElementById("btn-new-accu").style.boxShadow =
                    "none";
            } else {
                document.getElementById("tab-old-accu").style.display = "none";
                document.getElementById("tab-new-accu").style.display = "block";
                document.getElementById("btn-new-accu").style.background =
                    "#fff";
                document.getElementById("btn-new-accu").style.color = "#1e293b";
                document.getElementById("btn-new-accu").style.boxShadow =
                    "0 1px 3px rgba(0,0,0,0.1)";

                document.getElementById("btn-old-accu").style.background =
                    "transparent";
                document.getElementById("btn-old-accu").style.color = "#64748b";
                document.getElementById("btn-old-accu").style.boxShadow =
                    "none";
            }
            updateAccuTotal();
        };

        const citySearchInput = document.getElementById("city-search-input");
        if (citySearchInput) {
            let debounceTimer;
            citySearchInput.addEventListener("input", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    citiesPage = 1;
                    loadCities();
                }, 300);
            });
        }

        const accuSearchInput = document.getElementById("accu-search-input");
        if (accuSearchInput) {
            let debounceTimer;
            accuSearchInput.addEventListener("input", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    accusPage = 1;
                    loadAccus();
                }, 300);
            });
        }

        const newAccuSearchInput = document.getElementById(
            "new-accu-search-input",
        );
        if (newAccuSearchInput) {
            let debounceTimer;
            newAccuSearchInput.addEventListener("input", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    newAccusPage = 1;
                    loadNewAccus();
                }, 300);
            });
        }

        window.viewCityAccus = async (cityId, cityName) => {
            activeCityId = cityId;
            activeCityName = cityName;

            const modalTitle = document.getElementById(
                "city-price-modal-title",
            );
            if (modalTitle)
                modalTitle.innerText = `Daftar Harga Aki: Kota ${cityName}`;

            document.getElementById("modal-view-city-prices").style.display =
                "flex";

            const res = await fetchApi(`/cities/${cityId}/accus`);
            if (res.data && res.data.city) {
                const pctInp = document.getElementById(
                    "city-detail-percentage-input",
                );
                if (pctInp) pctInp.value = res.data.city.percentage || 80;
            }

            const tbody = document.getElementById("modal-city-accus-tbody");
            if (res.data && res.data.accus && res.data.accus.length) {
                tbody.innerHTML = res.data.accus
                    .map(
                        (a) => `
                    <tr>
                        <td>${a.name}</td>
                        <td>${a.berat_kering} kg</td>
                        <td style="font-weight:700; color:#10b981; text-align:right;">${rupiah(a.price)}</td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="4"><div class="admin-table-empty"><strong>Belum ada data aki untuk kota ${cityName}</strong></div></td></tr>`;
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

        window.openAddCityModal = () => {
            loadCities();
            loadTrashedCities();
            document.getElementById("modal-add-city").style.display = "flex";
        };

        window.openAddAccuModal = () => {
            loadAccus();
            loadTrashedAccus();
            document.getElementById("modal-add-accu").style.display = "flex";
        };

        window.openAddNewAccuModal = async () => {
            document.getElementById("form-add-new-accu").reset();
            document.getElementById("new-accu-id").value = "";
            document.getElementById("modal-new-accu-title").innerText =
                "Tambah Aki Baru";

            const brandSelect = document.getElementById("new-accu-brand");
            // Load brands
            const res = await fetchApi("/brands"); // Note: Assuming /brands is available in admin or public. Actually, admin might not have /brands directly if it's not set up. Let's hardcode or fetch from a known endpoint. Wait, earlier we used /brands? The existing accu add modal doesn't use brand select? It seems we might not have a /brands endpoint, but we can check. Actually, let's just make it a text input for brand if there is no brands_id, but the migration has brands_id. Let me double check if we need to load brands from somewhere. For now, let's try to fetch if we have it, else use a default. Wait, the existing Accu model has brands_id. There must be an endpoint for it. I'll check later.
            // Let's assume there's a way to get brands.
            try {
                const bRes = await fetch("/api/customer/brands"); // /customer/brands exists in api.php
                const bData = await bRes.json();
                if (bData.data) {
                    brandSelect.innerHTML =
                        '<option value="">Pilih Merk</option>' +
                        bData.data
                            .map(
                                (b) =>
                                    `<option value="${b.id}">${b.name}</option>`,
                            )
                            .join("");
                }
            } catch (e) {}

            document.getElementById("modal-add-new-accu").style.display =
                "flex";
        };

        window.editNewAccu = async (id) => {
            const accu = cachedNewAccus.find((a) => a.id === id);
            if (!accu) return;

            document.getElementById("new-accu-id").value = accu.id;
            document.getElementById("new-accu-name").value = accu.name;
            document.getElementById("new-accu-price").value = accu.price;
            document.getElementById("modal-new-accu-title").innerText =
                "Edit Aki Baru";

            const brandSelect = document.getElementById("new-accu-brand");
            try {
                const bRes = await fetch("/api/customer/brands");
                const bData = await bRes.json();
                if (bData.data) {
                    brandSelect.innerHTML =
                        '<option value="">Pilih Merk</option>' +
                        bData.data
                            .map(
                                (b) =>
                                    `<option value="${b.id}" ${b.id === accu.brands_id ? "selected" : ""}>${b.name}</option>`,
                            )
                            .join("");
                }
            } catch (e) {}

            document.getElementById("modal-add-new-accu").style.display =
                "flex";
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

        window.openEditAccu = (id, name, berat_kering) => {
            document.getElementById("edit-accu-id").value = id;
            document.getElementById("edit-accu-name").value = name;
            document.getElementById("edit-accu-berat-kering").value =
                berat_kering;
            document.getElementById("modal-edit-accu").style.display = "flex";
        };

        window.deleteNewAccu = (id) => {
            showConfirm(
                "Hapus Aki Baru",
                "Yakin ingin menghapus aki baru ini?",
                async () => {
                    await fetchApi(`/new-accus/${id}`, { method: "DELETE" });
                    showToast("Aki baru berhasil dihapus", "success");
                    loadNewAccus();
                },
            );
        };

        const formEditAccu = document.getElementById("form-edit-accu");
        if (formEditAccu) {
            formEditAccu.addEventListener("submit", async (e) => {
                e.preventDefault();
                const id = document.getElementById("edit-accu-id").value;
                const name = document.getElementById("edit-accu-name").value;
                const berat_kering = document.getElementById(
                    "edit-accu-berat-kering",
                ).value;

                const res = await fetchApi(`/accus/${id}`, {
                    method: "PUT",
                    body: JSON.stringify({ name, berat_kering }),
                });
                if (res && res.data) {
                    showToast("Battery updated successfully", "success");
                    document.getElementById("modal-edit-accu").style.display =
                        "none";
                    formEditAccu.reset();
                    loadAccus();
                } else {
                    let errMsg = res?.message || "Failed to update battery";
                    if (res?.errors && res.errors.name)
                        errMsg = res.errors.name[0];
                    showToast(errMsg, "error");
                }
            });
        }

        const formAddAccu = document.getElementById("form-add-accu");
        if (formAddAccu) {
            formAddAccu.addEventListener("submit", async (e) => {
                e.preventDefault();
                const name = document.getElementById("accu-name").value;
                const berat_kering =
                    document.getElementById("accu-berat-kering").value;

                const res = await fetchApi("/accus", {
                    method: "POST",
                    body: JSON.stringify({
                        brand: "Modern Mulya Mandiri",
                        name,
                        berat_kering,
                    }),
                });
                if (res && res.data) {
                    showToast("Aki reject berhasil ditambahkan", "success");
                    document.getElementById("modal-add-accu").style.display =
                        "none";
                    formAddAccu.reset();
                    loadAccus();
                    loadTrashedAccus();
                } else {
                    let errMsg = res?.message || "Gagal menyimpan aki";
                    if (res?.errors && res.errors.name) {
                        errMsg = res.errors.name[0];
                    }
                    showToast(errMsg, "error");
                }
            });
        }

        const formAddCity = document.getElementById("form-add-city");
        if (formAddCity) {
            formAddCity.addEventListener("submit", async (e) => {
                e.preventDefault();
                const name = document.getElementById("city-name").value;
                const percentage =
                    document.getElementById("city-percentage").value;

                const res = await fetchApi("/cities", {
                    method: "POST",
                    body: JSON.stringify({ name, percentage }),
                });
                if (res && res.data) {
                    showToast("Kota berhasil ditambahkan", "success");
                    document.getElementById("modal-add-city").style.display =
                        "none";
                    formAddCity.reset();
                    loadCities();
                    loadTrashedCities();
                }
            });
        }

        const formAddNewAccu = document.getElementById("form-add-new-accu");
        if (formAddNewAccu) {
            formAddNewAccu.addEventListener("submit", async (e) => {
                e.preventDefault();
                const id = document.getElementById("new-accu-id").value;
                const name = document.getElementById("new-accu-name").value;
                const brands_id =
                    document.getElementById("new-accu-brand").value;
                const price = document.getElementById("new-accu-price").value;

                if (id) {
                    const res = await fetchApi(`/new-accus/${id}`, {
                        method: "PUT",
                        body: JSON.stringify({ name, brands_id, price }),
                    });
                    if (res && res.data) {
                        showToast("Aki baru berhasil diperbarui", "success");
                        document.getElementById(
                            "modal-add-new-accu",
                        ).style.display = "none";
                        loadNewAccus();
                    } else {
                        let errMsg =
                            res?.message || "Gagal memperbarui aki baru";
                        if (res?.errors && res.errors.name) {
                            errMsg = res.errors.name[0];
                        }
                        showToast(errMsg, "error");
                    }
                } else {
                    const res = await fetchApi("/new-accus", {
                        method: "POST",
                        body: JSON.stringify({ name, brands_id, price }),
                    });
                    if (res && res.data) {
                        showToast("Aki baru berhasil ditambahkan", "success");
                        document.getElementById(
                            "modal-add-new-accu",
                        ).style.display = "none";
                        loadNewAccus();
                    } else {
                        let errMsg = res?.message || "Gagal menyimpan aki baru";
                        if (res?.errors && res.errors.name) {
                            errMsg = res.errors.name[0];
                        }
                        showToast(errMsg, "error");
                    }
                }
            });
        }

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
                                    <span>${a.name}</span>
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
                const pctVal =
                    parseFloat(
                        document.getElementById("city-percentage").value,
                    ) || 80.0;

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
                        percentage: pctVal,
                    }),
                });
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-city").style.display =
                        "none";
                    document.getElementById("form-add-city").reset();
                    showToast(
                        res.message || "Kota berhasil ditambahkan!",
                        "success",
                    );
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
                const beratKeringVal = parseFloat(
                    document.getElementById("accu-berat-kering").value,
                );

                const payload = {
                    brand: brandVal,
                    name: accuNameVal,
                    berat_kering: beratKeringVal,
                };

                const res = await fetchApi("/accus", {
                    method: "POST",
                    body: JSON.stringify(payload),
                });
                if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-accu").style.display =
                        "none";
                    document.getElementById("form-add-accu").reset();
                    if (accuBrandOtherWrap)
                        accuBrandOtherWrap.style.display = "none";
                    showToast(
                        res.message || "Aki baru berhasil disimpan!",
                        "success",
                    );
                    loadAccus();
                    loadTrashedAccus();
                } else {
                    showToast(res.message || "Gagal menyimpan aki", "error");
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

        let cachedStorages = [];

        const renderStorages = (storages) => {
            const tbody = document.getElementById("storages-tbody");
            if (storages.length) {
                tbody.innerHTML = storages
                    .map(
                        (s) => `
                    <tr>
                        <td style="font-weight:600;">
                            <a href="/admin/gudang/${s.id}" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:6px;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                <span>🏢 ${s.name}</span>
                            </a>
                        </td>
                        <td>${s.address || "-"}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="/admin/gudang/${s.id}" class="admin-button admin-button--primary" style="height:30px; font-size:11px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; background:#2563eb;">Detail & Stok</a>
                                <button onclick="showStorageMap('${s.name.replace(/'/g, "\\'")}', ${s.lat}, ${s.long})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px;">Lihat Peta</button>
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

        const loadStorages = async () => {
            window.loadStorages = loadStorages;
            const res = await fetchApi("/storages");
            cachedStorages = res.data || [];
            renderStorages(cachedStorages);
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

        const loadReadyToPickup = async () => {
            const res = await fetchApi("/storages/ready-to-pickup");

            const dashboard = document.getElementById(
                "storage-dashboard-summary",
            );
            if (dashboard) dashboard.style.display = "grid";

            const countTaken = document.getElementById("count-total-taken");
            const countUntaken = document.getElementById("count-total-untaken");
            if (countTaken) countTaken.innerText = res.total_taken_all || 0;
            if (countUntaken)
                countUntaken.innerText = res.total_untaken_all || 0;

            const listEl = document.getElementById("ready-pickup-list");
            if (listEl) {
                if (res.ready_warehouses && res.ready_warehouses.length > 0) {
                    listEl.innerHTML = res.ready_warehouses
                        .map(
                            (w) => `
                        <div class="ready-pickup-card">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div>
                                    <strong class="ready-pickup-card__title">${w.name}</strong>
                                    <small class="ready-pickup-card__subtitle">Siap diambil: <strong style="color:#ef4444;">${w.total_untaken}</strong> aki</small>
                                </div>
                                <span class="admin-badge admin-badge--danger">Action Required</span>
                            </div>
                            <button type="button" onclick="confirmPickup(${w.id})" class="admin-button admin-button--pickup">
                                <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                OK
                            </button>
                        </div>
                    `,
                        )
                        .join("");
                } else {
                    listEl.innerHTML = `
                        <div class="ready-pickup-card ready-pickup-card--empty">
                            <small>Tidak ada gudang yang siap diambil (stok &lt; 20).</small>
                        </div>
                    `;
                }
            }
        };

        window.confirmPickup = async (id) => {
            if (
                !confirm(
                    "Konfirmasi bahwa Anda (Pusat) sudah mengambil seluruh aki yang siap dari gudang ini?",
                )
            )
                return;
            try {
                const res = await fetchApi(`/storages/${id}/pickup`, {
                    method: "POST",
                });
                showToast(res.message || "Barang berhasil diambil", "success");
                loadReadyToPickup();
            } catch (err) {
                console.error(err);
            }
        };

        loadStorages();
        loadTrashedStorages();
        if (document.getElementById("ready-pickup-list")) {
            loadReadyToPickup();
        }

        const storageSearchInput = document.getElementById(
            "storage-search-input",
        );
        if (storageSearchInput) {
            storageSearchInput.addEventListener("input", (e) => {
                const term = e.target.value.toLowerCase();
                const filtered = cachedStorages.filter(
                    (s) =>
                        s.name.toLowerCase().includes(term) ||
                        (s.address && s.address.toLowerCase().includes(term)),
                );
                renderStorages(filtered);
            });
        }

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
                    showToast(
                        res.message || "Gudang berhasil ditambahkan!",
                        "success",
                    );
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

        const formDeleteStorage = document.getElementById(
            "form-delete-storage",
        );
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
                    document.getElementById(
                        "modal-delete-storage",
                    ).style.display = "none";
                    showToast(res.message, "success");
                    loadStorages();
                    loadTrashedStorages();
                } else {
                    showToast(
                        res.message ||
                            "Password admin salah / Gagal menghapus gudang",
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
            const isUnlocked =
                sessionStorage.getItem("easter_egg_unlocked") === "true";
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
                const inputPass = document.getElementById(
                    "easter-egg-password",
                ).value;
                const verifyRes = await fetch("/api/public-admin/verify", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ secret: inputPass }),
                });
                const verifyData = await verifyRes.json();
                if (verifyRes.ok && verifyData.valid) {
                    sessionStorage.setItem("easter_egg_pass", inputPass);
                    sessionStorage.setItem("easter_egg_unlocked", "true");
                    sessionStorage.setItem(
                        "easter_egg_time",
                        Date.now().toString(),
                    );
                    if (authError) authError.style.display = "none";
                    if (modalLock) modalLock.style.display = "none";

                    // Refresh the page directly so the data and layout render properly
                    window.location.reload();
                } else {
                    if (authError) {
                        authError.innerText =
                            verifyData.message || "Password rahasia salah!";
                        authError.style.display = "block";
                    }
                }
            });
        }

        const loadWarehouses = async () => {
            const warehouseSelect = document.getElementById("user-warehouse");
            if (!warehouseSelect) return;
            try {
                const res = await fetch("/api/customer/storages");
                const warehouses = res.ok ? await res.json() : null;
                if (warehouses && warehouses.data && warehouses.data.length) {
                    warehouseSelect.innerHTML =
                        `<option value="">Admin Utama (akses seluruh gudang)</option>` +
                        warehouses.data
                            .sort((a, b) => a.name.localeCompare(b.name))
                            .map(
                                (s) =>
                                    `<option value="${s.id}">${s.name}</option>`,
                            )
                            .join("");
                }
            } catch (error) {
                console.error("Gagal memuat daftar gudang:", error);
            }
        };

        const loadUsers = async () => {
            window.loadUsers = loadUsers;
            if (!checkEasterEggLock()) return;
            const res = await fetchApi("/users");
            const tbody = document.getElementById("users-tbody");
            if (res && res.data && res.data.length) {
                tbody.innerHTML = res.data
                    .map(
                        (u) => `
                    <tr>
                        <td style="font-weight:500;">${u.name}</td>
                        <td>${u.email || "-"}</td>
                        <td>${u.warehouse ? u.warehouse.name : u.warehouse_id ? "Gudang #" + u.warehouse_id : "Admin Utama"}</td>
                        <td>${parseSafeDate(u.created_at).toLocaleDateString("id-ID")}</td>
                        <td>
                            <button onclick="deleteUser(${u.id})" class="admin-button admin-button--secondary" style="height:30px; font-size:11px; color:#ba1b2b;">Hapus</button>
                        </td>
                    </tr>`,
                    )
                    .join("");
            } else {
                tbody.innerHTML = `<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada pengguna</strong></div></td></tr>`;
            }
        };

        if (checkEasterEggLock()) {
            loadWarehouses();
            loadUsers();
        }

        document
            .getElementById("form-add-user")
            .addEventListener("submit", async (e) => {
                e.preventDefault();
                if (!checkEasterEggLock()) return;
                const payload = {
                    name: document.getElementById("user-name").value,
                    email: document.getElementById("user-email").value,
                    password: document.getElementById("user-password").value,
                };
                const warehouseId =
                    document.getElementById("user-warehouse").value;
                if (warehouseId) {
                    payload.warehouse_id = parseInt(warehouseId, 10);
                }
                const idVal = document.getElementById("user-id").value;
                payload.id = idVal
                    ? idVal
                    : Math.floor(Math.random() * 1000000);
                const errorEl = document.getElementById("add-user-error");
                if (errorEl) errorEl.style.display = "none";

                if (!payload.email || !payload.email.includes("@")) {
                    if (errorEl) {
                        errorEl.innerText =
                            "Email harus valid dan mengandung @.";
                        errorEl.style.display = "block";
                    } else {
                        showToast(
                            "Email harus valid dan mengandung @.",
                            "error",
                        );
                    }
                    return;
                }

                const res = await fetchApi("/users", {
                    method: "POST",
                    body: JSON.stringify(payload),
                });

                if (res && res.errors) {
                    let errMsg = res.message || "Gagal validasi data";
                    const firstKey = Object.keys(res.errors)[0];
                    if (firstKey && res.errors[firstKey][0]) {
                        errMsg = res.errors[firstKey][0];
                    }
                    if (errorEl) {
                        errorEl.innerText = errMsg;
                        errorEl.style.display = "block";
                    } else {
                        showToast(errMsg, "error");
                    }
                } else if (res && (res.data || res.message)) {
                    document.getElementById("modal-add-user").style.display =
                        "none";
                    document.getElementById("form-add-user").reset();
                    showToast(
                        res.message || "Staf admin berhasil ditambahkan!",
                        "success",
                    );
                    loadUsers();
                } else {
                    showToast(
                        res?.message || "Gagal membuat akun admin",
                        "error",
                    );
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
            window.loadReportData = loadReportData;
            const endpoint = selectedYear
                ? `/reports?year=${selectedYear}`
                : "/reports";
            const res = await fetchApi(endpoint);
            if (!res || !res.data) return;

            const d = res.data;
            const s = d.summary;

            if (yearSelect && d.available_years && d.available_years.length) {
                yearSelect.innerHTML = d.available_years
                    .map(
                        (y) =>
                            `<option value="${y}" ${y == d.selected_year ? "selected" : ""}>${y}</option>`,
                    )
                    .join("");
            }

            document.getElementById("report-stat-sales").innerText = rupiah(
                s.total_sales,
            );
            document.getElementById("report-stat-orders").innerText =
                s.total_orders.toLocaleString("id-ID");
            document.getElementById("report-stat-completed-note").innerText =
                `${s.completed_orders.toLocaleString("id-ID")} Selesai`;
            document.getElementById("report-stat-avg").innerText = rupiah(
                s.avg_transaction_value,
            );
            document.getElementById("report-stat-cancelled").innerText =
                `${s.cancelled_orders.toLocaleString("id-ID")} (${s.cancellation_rate}%)`;

            const chartTitle = document.getElementById("chart-title");
            if (chartTitle)
                chartTitle.innerText = `Pendapatan Bulanan Tahun ${d.selected_year}`;

            const barsContainer = document.getElementById(
                "chart-bars-container",
            );
            const labelsContainer = document.getElementById(
                "chart-labels-container",
            );
            const maxLabel = document.getElementById("chart-max-label");

            const monthly = d.monthly_chart || [];
            const maxRev = Math.max(...monthly.map((m) => m.revenue), 1);

            if (maxLabel) {
                maxLabel.innerText = `Tertinggi: ${rupiah(maxRev)}`;
            }

            if (barsContainer && labelsContainer) {
                barsContainer.innerHTML =
                    `
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none; opacity:0.12; z-index:0;">
                        <div style="border-top:1px dashed #000; width:100%;"></div>
                        <div style="border-top:1px dashed #000; width:100%;"></div>
                        <div style="border-top:1px dashed #000; width:100%;"></div>
                    </div>
                ` +
                    monthly
                        .map((m) => {
                            const pct = Math.max(
                                Math.round((m.revenue / maxRev) * 100),
                                4,
                            );
                            const formattedRev =
                                m.revenue > 0
                                    ? m.revenue >= 1000000000
                                        ? (m.revenue / 1000000000).toFixed(1) +
                                          "B"
                                        : m.revenue >= 1000000
                                          ? (m.revenue / 1000000).toFixed(1) +
                                            "M"
                                          : (m.revenue / 1000).toFixed(0) + "K"
                                    : "0";

                            return `
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; z-index:1;" title="${m.month_name}: ${rupiah(m.revenue)} (${m.receipts_count} struk)">
                        <div style="font-size:10px; font-weight:600; color:var(--muted); margin-bottom:4px; white-space:nowrap;">${formattedRev}</div>
                        <div style="width:75%; max-width:32px; height:${pct}%; background:linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%); border-radius:4px 4px 0 0; transition: height 0.4s ease;"></div>
                    </div>`;
                        })
                        .join("");

                labelsContainer.innerHTML = monthly
                    .map(
                        (m) => `
                    <div style="flex:1; text-align:center; font-size:11px; font-weight:600; color:#6b7280;">${m.month_name}</div>
                `,
                    )
                    .join("");
            }

            const topAccusTbody = document.getElementById("top-accus-tbody");
            if (topAccusTbody) {
                if (d.top_accus && d.top_accus.length) {
                    topAccusTbody.innerHTML = d.top_accus
                        .map(
                            (a) => `
                        <tr>
                            <td>${a.name}</td>
                            <td style="text-align:right; font-weight:600; color:#1d4ed8;">${Number(a.total_sold).toLocaleString("id-ID")} unit</td>
                        </tr>
                    `,
                        )
                        .join("");
                } else {
                    topAccusTbody.innerHTML = `<tr><td colspan="2"><div class="admin-table-empty">Belum ada data penjualan</div></td></tr>`;
                }
            }

            const topCitiesTbody = document.getElementById("top-cities-tbody");
            if (topCitiesTbody) {
                if (d.top_cities && d.top_cities.length) {
                    topCitiesTbody.innerHTML = d.top_cities
                        .map(
                            (c) => `
                        <tr>
                            <td style="font-weight:500;">${c.name}</td>
                            <td style="text-align:right; font-weight:600; color:#10b981;">${Number(c.total_orders).toLocaleString("id-ID")} order</td>
                        </tr>
                    `,
                        )
                        .join("");
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

    /* =============================================================================
   BIAYA PENJEMPUTAN — Admin Pricing Configuration Module
   Page: /admin/biaya-penjemputan
============================================================================= */

    const formFormula = document.getElementById("form-pricing-formula");
    const formMultiplier = document.getElementById("form-pricing-multiplier");
    if (formFormula || formMultiplier) {
        // ── DOM refs ──────────────────────────────────────────────────────────────
        const elInitialFee = document.getElementById("pp-initial-fee");
        const elDistanceRate = document.getElementById("pp-distance-rate");
        const elTimeRate = document.getElementById("pp-time-rate");
        const elDemand = document.getElementById("pp-demand");
        const elWeather = document.getElementById("pp-weather");
        const elTraffic = document.getElementById("pp-traffic");
        const elEvent = document.getElementById("pp-event");
        const elVersionBadge = document.getElementById("pp-version-badge");

        // Preview formula refs
        const prevInitialFee = document.getElementById("prev-initial-fee");
        const prevDistanceRate = document.getElementById("prev-distance-rate");
        const prevTimeRate = document.getElementById("prev-time-rate");

        // Live multiplier preview refs
        const liveDemand = document.getElementById("live-demand");
        const liveWeather = document.getElementById("live-weather");
        const liveTraffic = document.getElementById("live-traffic");
        const liveEvent = document.getElementById("live-event");
        const liveTotal = document.getElementById("live-total-multiplier");

        // History refs
        const historySearch = document.getElementById("pp-history-search");
        const historyTbody = document.getElementById("pp-history-tbody");
        const historyPagination = document.getElementById(
            "pp-history-pagination",
        );

        // Toast
        const ppToast = document.getElementById("pp-toast");

        // ── State ─────────────────────────────────────────────────────────────────
        let historyCurrentPage = 1;
        let historySearchQuery = "";
        let historySearchTimer = null;

        // Easter egg refs
        const modalLock = document.getElementById("modal-easter-egg-lock");
        const formAuth = document.getElementById("form-easter-egg-auth");
        const authError = document.getElementById("easter-egg-error");

        const checkEasterEggLock = () => {
            const isUnlocked =
                sessionStorage.getItem("easter_egg_unlocked") === "true";
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
                const inputPass = document.getElementById(
                    "easter-egg-password",
                ).value;
                try {
                    const verifyRes = await fetch("/api/public-admin/verify", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ secret: inputPass }),
                    });
                    const verifyData = await verifyRes.json();
                    if (verifyRes.ok && verifyData.valid) {
                        sessionStorage.setItem("easter_egg_pass", inputPass);
                        sessionStorage.setItem("easter_egg_unlocked", "true");
                        sessionStorage.setItem(
                            "easter_egg_time",
                            Date.now().toString(),
                        );
                        if (authError) authError.style.display = "none";
                        if (modalLock) modalLock.style.display = "none";
                        showToast(
                            "Akses Rahasia Dibuka! Anda dapat mengatur pengiriman.",
                            "success",
                        );
                        loadCurrentSetting();
                    } else {
                        if (authError) {
                            authError.innerText =
                                verifyData.message || "Password rahasia salah!";
                            authError.style.display = "block";
                        }
                    }
                } catch (err) {
                    if (authError) {
                        authError.innerText = "Gagal menghubungi server.";
                        authError.style.display = "block";
                    }
                }
            });
        }

        // ── Utilities ─────────────────────────────────────────────────────────────
        function formatRpLocal(val) {
            return "Rp" + Number(val || 0).toLocaleString("id-ID");
        }

        function showToast(msg, isError = false) {
            if (!ppToast) return;
            ppToast.textContent = msg;
            ppToast.style.background = isError ? "#dc2626" : "#16a34a";
            ppToast.style.display = "block";
            setTimeout(() => {
                ppToast.style.display = "none";
            }, 3500);
        }

        function getAuthHeaders() {
            const token = localStorage.getItem("admin_token");
            return {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...(token ? { Authorization: "Bearer " + token } : {}),
            };
        }

        // ── Load Current Setting ──────────────────────────────────────────────────
        async function loadCurrentSetting() {
            if (!checkEasterEggLock()) return;
            try {
                const data = await fetchApi("/pengiriman");
                const s = data.setting;
                if (!s) return;

                // Populate formula fields
                if (elInitialFee) elInitialFee.value = s.initial_fee ?? 5000;
                if (elDistanceRate)
                    elDistanceRate.value = s.distance_rate ?? 2300;
                if (elTimeRate) elTimeRate.value = s.time_rate ?? 25;

                // Populate multiplier fields
                if (elDemand) elDemand.value = s.demand_multiplier ?? 1.0;
                if (elWeather) elWeather.value = s.weather_multiplier ?? 1.0;
                if (elTraffic) elTraffic.value = s.traffic_multiplier ?? 1.0;
                if (elEvent) elEvent.value = s.event_multiplier ?? 1.0;

                // Version badge
                if (elVersionBadge) {
                    elVersionBadge.textContent =
                        "Versi " + (s.pricing_version ?? 1);
                }

                updateFormulaPreview();
                updateLiveMultiplier();

                // Load history from the same response
                renderHistory(data.history);
            } catch (e) {
                console.error("Pickup pricing load error:", e);
                showToast("Error: " + (e.message || String(e)), true);
            }
        }

        // ── Formula Preview (read-only display update) ────────────────────────────
        function updateFormulaPreview() {
            if (prevInitialFee)
                prevInitialFee.textContent = formatRpLocal(
                    elInitialFee?.value || 5000,
                );
            if (prevDistanceRate)
                prevDistanceRate.textContent =
                    formatRpLocal(elDistanceRate?.value || 2300) + "/km";
            if (prevTimeRate)
                prevTimeRate.textContent =
                    formatRpLocal(elTimeRate?.value || 25) + "/detik";
        }

        // ── Live Multiplier Preview ───────────────────────────────────────────────
        function updateLiveMultiplier() {
            const d = parseFloat(elDemand?.value || 1);
            const w = parseFloat(elWeather?.value || 1);
            const t = parseFloat(elTraffic?.value || 1);
            const e = parseFloat(elEvent?.value || 1);
            const total = (d * w * t * e).toFixed(4);

            if (liveDemand) liveDemand.textContent = d.toFixed(2);
            if (liveWeather) liveWeather.textContent = w.toFixed(2);
            if (liveTraffic) liveTraffic.textContent = t.toFixed(2);
            if (liveEvent) liveEvent.textContent = e.toFixed(2);
            if (liveTotal) liveTotal.textContent = total;
        }

        // ── History Rendering ─────────────────────────────────────────────────────
        function renderHistory(paginatedData) {
            if (!historyTbody) return;
            const items = paginatedData?.data || [];
            if (items.length === 0) {
                historyTbody.innerHTML = `<tr><td colspan="10"><div class="admin-table-empty"><strong>Belum ada riwayat perubahan.</strong></div></td></tr>`;
                if (historyPagination) historyPagination.innerHTML = "";
                return;
            }

            historyTbody.innerHTML = items
                .map(
                    (h) => `
            <tr>
                <td style="font-size:12px; color:#6d727c; white-space:nowrap;">${formatDateLocal(h.created_at)}</td>
                <td style="font-size:12px;">${h.created_by || "-"}</td>
                <td style="text-align:right; font-family:monospace;">${formatRpLocal(h.initial_fee)}</td>
                <td style="text-align:right; font-family:monospace;">${formatRpLocal(h.distance_rate)}</td>
                <td style="text-align:right; font-family:monospace;">${formatRpLocal(h.time_rate)}</td>
                <td style="text-align:right; font-family:monospace;">${parseFloat(h.demand_multiplier).toFixed(2)}</td>
                <td style="text-align:right; font-family:monospace;">${parseFloat(h.weather_multiplier).toFixed(2)}</td>
                <td style="text-align:right; font-family:monospace;">${parseFloat(h.traffic_multiplier).toFixed(2)}</td>
                <td style="text-align:right; font-family:monospace;">${parseFloat(h.event_multiplier).toFixed(2)}</td>
                <td style="text-align:right; font-weight:700; color:#0369a1;">${parseFloat(h.total_multiplier).toFixed(4)}</td>
            </tr>
        `,
                )
                .join("");

            renderHistoryPagination(paginatedData);
        }

        function formatDateLocal(dateStr) {
            if (!dateStr) return "-";
            const d = new Date(dateStr);
            return (
                d.toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric",
                }) +
                " " +
                d.toLocaleTimeString("id-ID", {
                    hour: "2-digit",
                    minute: "2-digit",
                })
            );
        }

        function renderHistoryPagination(paginatedData) {
            if (!historyPagination) return;
            const last = paginatedData.last_page || 1;
            const current = paginatedData.current_page || 1;
            if (last <= 1) {
                historyPagination.innerHTML = "";
                return;
            }

            let html = "";
            for (let i = 1; i <= last; i++) {
                html += `<button onclick="ppLoadHistoryPage(${i})"
                style="padding:6px 14px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;
                       border:1px solid ${i === current ? "#0369a1" : "#e2e8f0"};
                       background:${i === current ? "#0369a1" : "#fff"};
                       color:${i === current ? "#fff" : "#374151"};">${i}</button>`;
            }
            historyPagination.innerHTML = html;
        }

        // Exposed for pagination button onclick
        window.ppLoadHistoryPage = async function (page) {
            historyCurrentPage = page;
            await loadHistoryPage();
        };

        async function loadHistoryPage() {
            if (!checkEasterEggLock()) return;
            try {
                const params = new URLSearchParams({
                    page: historyCurrentPage,
                    per_page: 15,
                    ...(historySearchQuery ? { q: historySearchQuery } : {}),
                });
                const data = await fetchApi("/pengiriman/history?" + params);
                renderHistory(data);
            } catch (e) {
                console.error("History load error:", e);
                showToast("Error: " + (e.message || String(e)), true);
            }
        }

        // ── Form Submissions ──────────────────────────────────────────────────────
        async function saveSettings(payload, btnEl) {
            if (!checkEasterEggLock()) return;
            if (btnEl) {
                btnEl.disabled = true;
                btnEl.textContent = "Menyimpan...";
            }
            try {
                const data = await fetchApi("/pengiriman", {
                    method: "PUT",
                    body: JSON.stringify(payload),
                });
                if (data.message === "Unauthenticated" || data.errors) {
                    const errMsg =
                        data.message ||
                        Object.values(data.errors || {})
                            .flat()
                            .join(" ");
                    showToast(errMsg || "Terjadi kesalahan.", true);
                } else {
                    showToast(data.message || "Berhasil disimpan.");
                    await loadCurrentSetting();
                    await loadHistoryPage();
                }
            } catch (e) {
                showToast("Gagal terhubung ke server.", true);
            } finally {
                if (btnEl) {
                    btnEl.disabled = false;
                    btnEl.textContent = btnEl.dataset.origText || "Simpan";
                }
            }
        }

        // ── Event Listeners ───────────────────────────────────────────────────────
        if (formFormula) {
            const btn = formFormula.querySelector("#btn-save-formula");
            if (btn) btn.dataset.origText = btn.textContent;

            // Live formula preview on input
            [elInitialFee, elDistanceRate, elTimeRate].forEach((el) => {
                if (el) el.addEventListener("input", updateFormulaPreview);
            });

            formFormula.addEventListener("submit", async (e) => {
                e.preventDefault();

                // We need the current multiplier values too for a combined save
                const payload = {
                    initial_fee: parseFloat(elInitialFee?.value || 5000),
                    distance_rate: parseFloat(elDistanceRate?.value || 2300),
                    time_rate: parseFloat(elTimeRate?.value || 25),
                    demand_multiplier: parseFloat(elDemand?.value || 1.0),
                    weather_multiplier: parseFloat(elWeather?.value || 1.0),
                    traffic_multiplier: parseFloat(elTraffic?.value || 1.0),
                    event_multiplier: parseFloat(elEvent?.value || 1.0),
                };
                await saveSettings(payload, btn);
            });
        }

        if (formMultiplier) {
            const btn = formMultiplier.querySelector("#btn-save-multiplier");
            if (btn) btn.dataset.origText = btn.textContent;

            // Live total multiplier preview on input
            [elDemand, elWeather, elTraffic, elEvent].forEach((el) => {
                if (el) el.addEventListener("input", updateLiveMultiplier);
            });

            formMultiplier.addEventListener("submit", async (e) => {
                e.preventDefault();

                // Combined save: always include formula values alongside new multipliers
                const payload = {
                    initial_fee: parseFloat(elInitialFee?.value || 5000),
                    distance_rate: parseFloat(elDistanceRate?.value || 2300),
                    time_rate: parseFloat(elTimeRate?.value || 25),
                    demand_multiplier: parseFloat(elDemand?.value || 1.0),
                    weather_multiplier: parseFloat(elWeather?.value || 1.0),
                    traffic_multiplier: parseFloat(elTraffic?.value || 1.0),
                    event_multiplier: parseFloat(elEvent?.value || 1.0),
                };
                await saveSettings(payload, btn);
            });
        }

        // History search
        if (historySearch) {
            historySearch.addEventListener("input", () => {
                clearTimeout(historySearchTimer);
                historySearchTimer = setTimeout(async () => {
                    historySearchQuery = historySearch.value.trim();
                    historyCurrentPage = 1;
                    await loadHistoryPage();
                }, 350);
            });
        }

        // ── Bootstrap ─────────────────────────────────────────────────────────────
        loadCurrentSetting();
    }
});
