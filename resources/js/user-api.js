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

    const parseSafeDate = (d) => {
        if (!d) return new Date();
        let s = String(d);
        if (!s.includes("T") && s.includes(" ")) s = s.replace(" ", "T");
        if (!s.includes("Z") && !s.includes("+")) s += "Z";
        return new Date(s);
    };

    const rupiah = (n) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(n);

    const showCustomAlert = (msg) => {
        if (typeof window.userAlert === "function") {
            window.userAlert(msg);
        } else {
            alert(msg);
        }
    };
    const cityCoordinates = {};
    const fallbackCityCoords = {
        surabaya: { lat: -7.2575, lng: 112.7521 },
        jakarta: { lat: -6.2088, lng: 106.8456 },
        bandung: { lat: -6.9175, lng: 107.6191 },
        semarang: { lat: -6.9666, lng: 110.4196 },
        yogyakarta: { lat: -7.7956, lng: 110.3695 },
        makassar: { lat: -5.1477, lng: 119.4327 },
        palu: { lat: -0.9003, lng: 119.8708 },
        balikpapan: { lat: -1.2379, lng: 116.8529 },
        medan: { lat: 3.5952, lng: 98.6722 },
        denpasar: { lat: -8.6705, lng: 115.2126 },
        palembang: { lat: -2.9761, lng: 104.7754 },
        manado: { lat: 1.4748, lng: 124.8421 },
    };
    let selectedCityName = "";
    if (
        window.location.pathname === "/user" ||
        window.location.pathname === "/"
    ) {
        localStorage.removeItem("pickup_address");
        localStorage.removeItem("pickup_city");
        localStorage.removeItem("pickup_zip");
        localStorage.removeItem("pickup_lat");
        localStorage.removeItem("pickup_long");
        localStorage.removeItem("pickup_cart");
        localStorage.removeItem("pickup_fee");
        localStorage.removeItem("pickup_delivery_method");
        localStorage.removeItem("nearest_warehouse_name");
        localStorage.removeItem("nearest_warehouse_address");
        localStorage.removeItem("nearest_warehouse_distance");
        if (window.userCart) {
            window.userCart.clear();
        }
    }
    const citySelect = document.querySelector("[data-city-select]");
    const cityStatus = document.querySelector("[data-city-status]");
    const searchInput = document.getElementById("accu-search-input");
    searchInput?.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase().trim();
        const productCards = document.querySelectorAll("[data-product-card]");
        productCards.forEach((card) => {
            const name =
                card.getAttribute("data-product-name")?.toLowerCase() || "";
            const brand =
                card.getAttribute("data-product-brand")?.toLowerCase() || "";
            if (query.length > 0 && (name.includes(query) || brand.includes(query))) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
        
        const hintEl = document.getElementById("search-hint-placeholder");
        if (hintEl) {
            hintEl.style.display = query.length === 0 ? "block" : "none";
        }
    });

    if (citySelect) {
        (async () => {
            const res = await fetchPublicApi("/cities");
            if (res.data && res.data.length) {
                citySelect.innerHTML =
                    '<option value="" disabled selected>-- Pilih Kota Penyerahan --</option>' +
                    res.data
                        .map(
                            (c) => `<option value="${c.id}">${c.name}</option>`,
                        )
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
            selectedCityName = cityName;
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
    function renderProductCards(accus) {
        const batteryList = document.getElementById("user-battery-list");
        if (!batteryList) return;

        if (!accus || accus.length === 0) {
            batteryList.innerHTML = `
                <div style="text-align:center; padding: 40px 20px; color: #64748b;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <strong>Tidak ada aki tersedia untuk kota ini.</strong>
                    <p style="font-size:13px;">Silakan pilih kota lain atau hubungi kami untuk informasi lebih lanjut.</p>
                </div>`;
            return;
        }

        const formatRupiah = (number) =>
            "Rp " + Number(number).toLocaleString("id-ID");

        const searchQuery = searchInput?.value.toLowerCase().trim() || "";

        let cardsHtml = accus
            .map((accu) => {
                const displayStyle = (searchQuery.length > 0 && (accu.name.toLowerCase().includes(searchQuery) || accu.brand.toLowerCase().includes(searchQuery))) ? "" : "display:none;";

                return `
                <div class="user-battery-item" data-product-card data-product-name="${accu.name}" data-product-brand="${accu.brand}" data-product-price="${accu.price}" data-accu-id="${accu.id}" style="${displayStyle}">
                    <div class="user-battery-item__left">
                        <h3>${accu.name}</h3>
                        <div class="user-quantity">
                            <button type="button" data-quantity-minus aria-label="Kurangi jumlah">−</button>
                            <input type="number" value="1" min="1" max="9999" data-quantity aria-label="Jumlah ${accu.name}">
                            <button type="button" data-quantity-plus aria-label="Tambah jumlah">+</button>
                        </div>
                    </div>
                    <div class="user-battery-item__right">
                        <button type="button" class="user-add-button" data-add-to-cart>+ Tambahkan ke Keranjang</button>
                    </div>
                </div>`;
            })
            .join("");
            
        let hintHtml = `<div id="search-hint-placeholder" style="text-align:center; padding: 40px 20px; color: #64748b; ${searchQuery.length === 0 ? '' : 'display:none;'}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.4;">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <strong>Silakan mencari jenis aki terlebih dahulu.</strong>
                    <p style="font-size:13px;">Ketikkan jenis aki pada kolom pencarian di atas.</p>
                </div>`;
                
        batteryList.innerHTML = hintHtml + cardsHtml;
        bindProductCardEvents();
        if (typeof window.updateProductCardButtons === "function") {
            window.updateProductCardButtons();
        }
    }

    window.updateProductCardButtons = function () {
        document.querySelectorAll("[data-product-card]").forEach((card) => {
            const name = card.dataset.productName;
            const addButton = card.querySelector("[data-add-to-cart]");
            if (
                addButton &&
                name &&
                window.userCart &&
                window.userCart.has(name)
            ) {
                if (addButton.getAttribute("data-is-animating") !== "true") {
                    addButton.textContent = "Update jumlah keranjang";
                }
                addButton.classList.add("user-add-button--update");
            } else if (addButton) {
                addButton.textContent = "+ Tambahkan ke Keranjang";
                addButton.classList.remove("user-add-button--update");
                addButton.setAttribute("data-is-animating", "false");
            }
        });
    };

    function bindProductCardEvents() {
        document.querySelectorAll("[data-product-card]").forEach((card) => {
            const quantityInput = card.querySelector("[data-quantity]");
            const minusButton = card.querySelector("[data-quantity-minus]");
            const plusButton = card.querySelector("[data-quantity-plus]");
            const addButton = card.querySelector("[data-add-to-cart]");

            const setQuantity = (value) => {
                if (quantityInput)
                    quantityInput.value = Math.min(
                        9999,
                        Math.max(1, Number(value) || 1),
                    );
            };

            minusButton?.addEventListener("click", () =>
                setQuantity(Number(quantityInput?.value) - 1),
            );
            plusButton?.addEventListener("click", () =>
                setQuantity(Number(quantityInput?.value) + 1),
            );

            addButton?.addEventListener("click", () => {
                const name = card.dataset.productName || "Aki";
                const brand = card.dataset.productBrand || "Modern Mulya Mandiri";
                const price = Number(card.dataset.productPrice) || 0;
                const id = Number(card.getAttribute("data-accu-id")) || 1;
                const quantity = Math.min(
                    9999,
                    Math.max(1, Number(quantityInput?.value) || 1),
                );

                if (!window.userCart.has(name) && window.userCart.size >= 5) {
                    showCustomAlert("Maksimal 5 jenis aki per transaksi!");
                    return;
                }

                const isUpdate = window.userCart.has(name);

                window.userCart.set(name, { id, name, brand, price, quantity });
                if (typeof window.renderUserCart === "function") {
                    window.renderUserCart();
                }

                if (typeof window.updateProductCardButtons === "function") {
                    window.updateProductCardButtons();
                }

                if (isUpdate) {
                    addButton.setAttribute("data-is-animating", "true");
                    const originalText = "Update jumlah keranjang";
                    addButton.textContent = "✓ Jumlah telah diupdate";
                    addButton.style.backgroundColor = "#e04b4b"; 
                    addButton.style.borderColor = "#e04b4b";
                    addButton.style.color = "#fff";
                    
                    if (addButton.dataset.animTimeoutId) {
                        clearTimeout(Number(addButton.dataset.animTimeoutId));
                    }
                    
                    const timeoutId = setTimeout(() => {
                        addButton.setAttribute("data-is-animating", "false");
                        if (window.userCart.has(name)) {
                            addButton.textContent = originalText;
                        }
                        addButton.style.backgroundColor = "";
                        addButton.style.borderColor = "";
                        addButton.style.color = "";
                    }, 1500);
                    
                    addButton.dataset.animTimeoutId = timeoutId.toString();
                }
            });
        });
    }

    async function loadCityPrices(cityId) {
        if (!cityId) return;
        const res = await fetchPublicApi(`/cities/${cityId}/accus`);
        if (!res.data || !res.data.accus) return;

        const accus = res.data.accus;
        renderProductCards(accus);
        if (window.userCart && window.userCart.size > 0) {
            window.userCart.forEach((item, key) => {
                const matchingAccu = accus.find(
                    (a) => a.id === item.id || a.name === item.name,
                );
                if (matchingAccu && matchingAccu.price) {
                    item.price = matchingAccu.price;
                } else {
                }
            });
            if (typeof window.renderUserCart === "function") {
                window.renderUserCart();
            }
        }
    }
    const identityForm = document.querySelector("[data-identity-form]");
    const bankSelect = document.querySelector('select[name="bank_type"]');

    if (bankSelect) {
        (async () => {
            const res = await fetchPublicApi("/banks");
            if (res.data && res.data.length) {
                bankSelect.innerHTML =
                    '<option value="" selected disabled></option>' +
                    res.data
                        .map(
                            (b) => `<option value="${b.id}">${b.name}</option>`,
                        )
                        .join("");
            }
        })();
    }

    if (identityForm) {
        //UI validasi nama pemilik rekening (hanya huruf + spasi)
        const holderInput = identityForm.querySelector(
            'input[name="account_holder"]',
        );
        if (holderInput) {
            holderInput.addEventListener("keypress", (e) => {
                if (
                    e.key &&
                    e.key.length === 1 &&
                    !/[a-zA-Z\s.,]/.test(e.key)
                ) {
                    e.preventDefault();
                    showCustomAlert(
                        "Kolom nama hanya menerima huruf, spasi, titik, dan koma!",
                    );
                }
            });
            holderInput.addEventListener("paste", (e) => {
                const pastedText = (
                    e.clipboardData || window.clipboardData
                ).getData("text");
                if (!/^[a-zA-Z\s.,]+$/.test(pastedText)) {
                    e.preventDefault();
                    showCustomAlert(
                        "Teks yang ditempelkan hanya boleh berisi huruf, spasi, titik, dan koma!",
                    );
                }
            });
            holderInput.addEventListener("input", (e) => {
                const start = e.target.selectionStart;
                const end = e.target.selectionEnd;
                e.target.value = e.target.value
                    .replace(/[^a-zA-Z\s.,]/g, "")
                    .toUpperCase();
                e.target.setSelectionRange(start, end);
            });
        }
        
        const manualNameInputEl = identityForm.querySelector(
            'input[name="manual_full_name"]',
        );
        if (manualNameInputEl) {
            manualNameInputEl.addEventListener("keypress", (e) => {
                if (
                    e.key &&
                    e.key.length === 1 &&
                    !/[a-zA-Z\s.,]/.test(e.key)
                ) {
                    e.preventDefault();
                    showCustomAlert(
                        "Kolom nama hanya menerima huruf, spasi, titik, dan koma!",
                    );
                }
            });
            manualNameInputEl.addEventListener("paste", (e) => {
                const pastedText = (
                    e.clipboardData || window.clipboardData
                ).getData("text");
                if (!/^[a-zA-Z\s.,]+$/.test(pastedText)) {
                    e.preventDefault();
                    showCustomAlert(
                        "Teks yang ditempelkan hanya boleh berisi huruf, spasi, titik, dan koma!",
                    );
                }
            });
            manualNameInputEl.addEventListener("input", (e) => {
                const start = e.target.selectionStart;
                const end = e.target.selectionEnd;
                e.target.value = e.target.value
                    .replace(/[^a-zA-Z\s.,]/g, "")
                    .toUpperCase();
                e.target.setSelectionRange(start, end);
            });
        }

        const accountNumberInput = identityForm.querySelector(
            'input[name="account_number"]',
        );
        const accountHint = document.getElementById("account-hint");
        let currentBankRule = null;

        if (bankSelect && accountNumberInput && accountHint) {
            const bankRules = {
                BCA: { min: 10, max: 10, msg: "10 digit" },
                Mandiri: { min: 12, max: 17, msg: "antara 12-17 digit" },
                BNI: { min: 7, max: 11, msg: "antara 7-11 digit" },
                BRI: { min: 13, max: 17, msg: "antara 13-17 digit" },
                "CIMB Niaga": { min: 10, max: 13, msg: "antara 10-13 digit" },
            };

            const validateAccountNumber = () => {
                if (!currentBankRule) return;
                const val = accountNumberInput.value;
                if (val.length === 0) {
                    accountHint.textContent = `*pastikan no rekening ${currentBankRule.msg}`;
                    accountHint.style.display = "block";
                    accountNumberInput.setCustomValidity("Wajib diisi");
                } else if (
                    val.length < currentBankRule.min ||
                    val.length > currentBankRule.max
                ) {
                    accountHint.textContent = `*pastikan no rekening ${currentBankRule.msg}`;
                    accountHint.style.display = "block";
                    accountNumberInput.setCustomValidity(
                        `Nomor rekening harus ${currentBankRule.msg}`,
                    );
                } else {
                    accountHint.style.display = "none";
                    accountNumberInput.setCustomValidity("");
                }
            };

            bankSelect.addEventListener("change", (e) => {
                const bankName = e.target.options[e.target.selectedIndex].text;

                // Fallback for banks not in the explicit list
                currentBankRule = Object.keys(bankRules).find((k) =>
                    bankName.toLowerCase().includes(k.toLowerCase()),
                )
                    ? bankRules[
                          Object.keys(bankRules).find((k) =>
                              bankName.toLowerCase().includes(k.toLowerCase()),
                          )
                      ]
                    : { min: 5, max: 30, msg: "minimal 5 digit" };

                accountNumberInput.disabled = false;
                accountNumberInput.style.cursor = "text";
                accountNumberInput.style.background = "var(--user-white)";

                validateAccountNumber();
            });

            accountNumberInput.addEventListener("input", (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, "");
                validateAccountNumber();
            });
        }

        const waInput = identityForm.querySelector('input[name="whatsapp"]');
        const waHint1 = document.getElementById("wa-hint-1");
        const waHint2 = document.getElementById("wa-hint-2");

        if (waInput && waHint1 && waHint2) {
            const validateWa = () => {
                const val = waInput.value;
                let isValid1 = val.length > 0 && val.startsWith("0");
                let isValid2 = val.length >= 10 && val.length <= 13;

                waHint1.style.display = isValid1 ? "none" : "block";
                waHint2.style.display = isValid2 ? "none" : "block";

                if (val.length === 0) {
                    waInput.setCustomValidity("Wajib diisi");
                } else if (!isValid1) {
                    waInput.setCustomValidity("Nomor harus diawali dengan 0");
                } else if (!isValid2) {
                    waInput.setCustomValidity(
                        "Jumlah digit harus antara 10-13",
                    );
                } else {
                    waInput.setCustomValidity("");
                }
            };

            waInput.addEventListener("input", (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, "");
                validateWa();
            });

            validateWa();
        }

        const numberInputs = identityForm.querySelectorAll(
            'input[name="account_number"], input[name="whatsapp"]',
        );
        numberInputs.forEach((input) => {
            input.addEventListener("keypress", (e) => {
                if (e.key && e.key.length === 1 && !/[0-9]/.test(e.key)) {
                    e.preventDefault();
                    showCustomAlert(
                        "Kolom ini hanya menerima angka! Masukan huruf atau simbol tidak diperbolehkan.",
                    );
                }
            });
            input.addEventListener("paste", (e) => {
                const pastedText = (
                    e.clipboardData || window.clipboardData
                ).getData("text");
                if (!/^\d+$/.test(pastedText)) {
                    e.preventDefault();
                    showCustomAlert(
                        "Teks yang ditempelkan mengandung karakter non-angka! Kolom ini wajib angka.",
                    );
                }
            });
            input.addEventListener("input", (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, "");
            });
        });

        // UI Upload Choice & Camera Handler
        let activeUploadTarget = "ktp"; // "ktp" atau "accu_ktp"
        let cameraStream = null;

        const uploadKtpTrigger = document.getElementById("upload-ktp-trigger");
        const uploadAccuKtpTrigger = document.getElementById("upload-accu-ktp-trigger");
        const modalUploadChoice = document.getElementById("modal-upload-choice");
        const modalLiveCamera = document.getElementById("modal-live-camera");
        const cameraVideo = document.getElementById("camera-video");
        const cameraCanvas = document.getElementById("camera-canvas");

        const btnChoiceCamera = document.getElementById("btn-choice-camera");
        const btnChoiceGallery = document.getElementById("btn-choice-gallery");
        const btnChoiceCancel = document.getElementById("btn-choice-cancel");
        const btnCloseCamera = document.getElementById("btn-close-camera");
        const btnCapturePhoto = document.getElementById("btn-capture-photo");

        const ktpFileInput = document.getElementById("ktp-file-input");
        const accuKtpFileInput = document.getElementById("accu-ktp-file-input");

        const openChoiceModal = (target) => {
            activeUploadTarget = target;
            if (modalUploadChoice) modalUploadChoice.style.display = "flex";
        };

        uploadKtpTrigger?.addEventListener("click", (e) => {
            if (e.target.tagName.toLowerCase() === 'input') return;
            openChoiceModal("ktp");
        });
        uploadAccuKtpTrigger?.addEventListener("click", (e) => {
            if (e.target.tagName.toLowerCase() === 'input') return;
            openChoiceModal("accu_ktp");
        });

        btnChoiceCancel?.addEventListener("click", () => {
            if (modalUploadChoice) modalUploadChoice.style.display = "none";
        });

        btnChoiceGallery?.addEventListener("click", () => {
            if (modalUploadChoice) modalUploadChoice.style.display = "none";
            if (activeUploadTarget === "ktp" && ktpFileInput) {
                ktpFileInput.click();
            } else if (activeUploadTarget === "accu_ktp" && accuKtpFileInput) {
                accuKtpFileInput.click();
            }
        });

        const stopCamera = () => {
            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
                cameraStream = null;
            }
            if (modalLiveCamera) modalLiveCamera.style.display = "none";
        };

        btnCloseCamera?.addEventListener("click", stopCamera);

        btnChoiceCamera?.addEventListener("click", async () => {
            if (modalUploadChoice) modalUploadChoice.style.display = "none";
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showCustomAlert("Perangkat atau browser Anda tidak mendukung akses kamera langsung. Beralih ke pengunggahan dari galeri.");
                if (activeUploadTarget === "ktp" && ktpFileInput) ktpFileInput.click();
                else if (activeUploadTarget === "accu_ktp" && accuKtpFileInput) accuKtpFileInput.click();
                return;
            }

            try {
                // Percobaan 1: Kamera belakang (Mobile)
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "environment", width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false,
                    });
                } catch (envErr) {
                    // Percobaan 2: Fallback ke kamera manapun yang tersedia (Laptop / Webcam)
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false,
                    });
                }

                if (cameraVideo) {
                    cameraVideo.srcObject = cameraStream;
                }
                const cameraTitle = document.getElementById("camera-modal-title");
                if (cameraTitle) {
                    cameraTitle.textContent = activeUploadTarget === "ktp" ? "Foto Dokumen KTP / SIM" : "Foto Aki & KTP (1 Frame)";
                }
                if (modalLiveCamera) modalLiveCamera.style.display = "flex";
            } catch (err) {
                let errorMsg = "Gagal mengakses kamera.";
                if (err.name === "NotAllowedError" || err.name === "PermissionDeniedError") {
                    errorMsg = "Izin kamera ditolak oleh browser. Silakan beri izin akses kamera di pengaturan browser Anda, atau gunakan opsi Galeri.";
                } else if (err.name === "NotFoundError" || err.name === "DevicesNotFoundError") {
                    errorMsg = "Kamera tidak ditemukan pada perangkat Anda. Beralih ke opsi Galeri.";
                }

                showCustomAlert(errorMsg);
                if (activeUploadTarget === "ktp" && ktpFileInput) ktpFileInput.click();
                else if (activeUploadTarget === "accu_ktp" && accuKtpFileInput) accuKtpFileInput.click();
            }
        });

        btnCapturePhoto?.addEventListener("click", () => {
            if (!cameraVideo || !cameraCanvas) return;
            const context = cameraCanvas.getContext("2d");
            cameraCanvas.width = cameraVideo.videoWidth || 1280;
            cameraCanvas.height = cameraVideo.videoHeight || 720;
            context.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

            cameraCanvas.toBlob((blob) => {
                if (!blob) return;
                const fileName = activeUploadTarget === "ktp" ? "ktp_camera.jpg" : "accu_ktp_camera.jpg";
                const capturedFile = new File([blob], fileName, { type: "image/jpeg" });
                const container = new DataTransfer();
                container.items.add(capturedFile);

                if (activeUploadTarget === "ktp" && ktpFileInput) {
                    ktpFileInput.files = container.files;
                    ktpFileInput.dispatchEvent(new Event("change"));
                } else if (activeUploadTarget === "accu_ktp" && accuKtpFileInput) {
                    accuKtpFileInput.files = container.files;
                    accuKtpFileInput.dispatchEvent(new Event("change"));
                }
                stopCamera();
            }, "image/jpeg", 0.9);
        });

        if (accuKtpFileInput) {
            accuKtpFileInput.addEventListener("change", (e) => {
                const file = e.target.files[0];
                const label = document.getElementById("accu-ktp-filename-label");
                const hint = document.getElementById("accu-ktp-size-hint");

                if (!file) {
                    if (label) label.textContent = "Upload foto Aki & KTP dalam 1 Frame";
                    if (hint) hint.style.display = "none";
                    return;
                }

                if (!file.type.startsWith("image/")) {
                    alert("Harap upload file berupa gambar (JPEG, PNG).");
                    accuKtpFileInput.value = "";
                    if (label) label.textContent = "Upload foto Aki & KTP dalam 1 Frame";
                    if (hint) hint.style.display = "none";
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert("Ukuran file terlalu besar (Maksimal 5MB).");
                    if (hint) hint.style.display = "block";
                    accuKtpFileInput.value = "";
                    if (label) label.textContent = "Upload foto Aki & KTP dalam 1 Frame";
                    return;
                } else {
                    if (hint) hint.style.display = "none";
                }

                if (label) label.textContent = "✓ " + file.name;
            });
        }

        //UI OCR upload KTP/SIM
        const ktpInputNode = document.querySelector(
            'input[name="identity_document"]',
        );
        const ocrNameWrapper = document.getElementById("ocr-name-wrapper");
        const ocrNameInput = identityForm.querySelector(
            'input[name="full_name"]',
        );
        const ocrStatus = document.getElementById("ocr-status");

        if (ktpInputNode) {
            ktpInputNode.addEventListener("change", async (e) => {
                const file = e.target.files[0];
                const nameEl = e.target
                    .closest(".user-upload-field")
                    .querySelector("strong");

                const sizeHint = document.getElementById("ktp-size-hint");

                if (!file) {
                    if (nameEl) nameEl.textContent = "Upload foto KTP atau SIM";
                    if (ocrNameWrapper) ocrNameWrapper.style.display = "none";
                    if (ocrNameInput) ocrNameInput.value = "";
                    if (sizeHint) sizeHint.style.display = "none";
                    return;
                }

                if (!file.type.startsWith("image/")) {
                    alert("Harap upload file berupa gambar (JPEG, PNG).");
                    ktpInputNode.value = "";
                    if (nameEl) nameEl.textContent = "Upload foto KTP atau SIM";
                    if (ocrNameWrapper) ocrNameWrapper.style.display = "none";
                    if (ocrNameInput) ocrNameInput.value = "";
                    if (sizeHint) sizeHint.style.display = "none";
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert("Ukuran file terlalu besar (Maksimal 5MB).");
                    if (sizeHint) sizeHint.style.display = "block";
                    ktpInputNode.value = ""; // Bersihkan file yang di-upload
                    if (nameEl) nameEl.textContent = "Upload foto KTP atau SIM";
                    if (ocrNameWrapper) ocrNameWrapper.style.display = "none";
                    if (ocrNameInput) ocrNameInput.value = "";
                    return;
                } else {
                    if (sizeHint) sizeHint.style.display = "none";
                }

                if (nameEl) nameEl.textContent = file.name;

                //Tampilkan loading
                if (ocrNameWrapper) ocrNameWrapper.style.display = "block";
                if (ocrNameInput) ocrNameInput.value = "";
                if (ocrStatus) {
                    ocrStatus.style.display = "block";
                    ocrStatus.style.color = "#2563eb";
                    ocrStatus.innerHTML =
                        '<span style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4" stroke-linecap="round"/></svg> Mengekstrak nama dari KTP/SIM...</span>';
                }

                try {
                    const formData = new FormData();
                    formData.append("image", file);

                    // Lacak percobaan upload
                    const now = Date.now();
                    let attempts = JSON.parse(localStorage.getItem("ktp_upload_attempts") || "[]");
                    attempts = attempts.filter(time => now - time < 10 * 60 * 1000); // 10 menit
                    attempts.push(now);
                    localStorage.setItem("ktp_upload_attempts", JSON.stringify(attempts));

                    if (attempts.length > 3) {
                        const manualWrapper = document.getElementById("manual-name-wrapper");
                        if (manualWrapper) {
                            manualWrapper.style.display = "block";
                            const manualInput = manualWrapper.querySelector('input');
                            if (manualInput) manualInput.required = true;
                            
                            const ocrInput = document.querySelector('input[name="full_name"]');
                            if (ocrInput) ocrInput.required = false;
                        }
                    }

                    const res = await fetch("/api/customer/ocr/extract-name", {
                        method: "POST",
                        headers: { Accept: "application/json" },
                        body: formData,
                    });
                    const data = await res.json();

                    const viewBtn = document.getElementById("view-ktp-btn");
                    const ktpOverlayImg = document.getElementById("ktp-overlay-img");

                    if (data.name) {
                        if (ocrNameInput) ocrNameInput.value = data.name;
                        if (ocrStatus) {
                            ocrStatus.style.color = "#16a34a";
                            ocrStatus.innerHTML =
                                "✓ Nama berhasil diekstrak dari dokumen. (jika nama yang diekstraksi salah, lakukan foto sesuai dengan contoh)";
                        }
                        if (viewBtn && ktpOverlayImg) {
                            viewBtn.style.display = "inline-block";
                            viewBtn.style.color = "#16a34a";
                            ktpOverlayImg.src = "/img/ktp_template.jpeg";
                        }
                    } else {
                        if (ocrStatus) {
                            ocrStatus.style.color = "#dc2626";
                            ocrStatus.innerHTML =
                                "✗ " +
                                (data.message ||
                                    "Gagal membaca nama. Coba upload ulang dengan foto yang lebih jelas.");
                        }
                        if (viewBtn && ktpOverlayImg) {
                            viewBtn.style.display = "inline-block";
                            viewBtn.style.color = "#dc2626";
                            ktpOverlayImg.src = "/img/ktp_template.jpeg";
                        }
                    }
                } catch (err) {
                    if (ocrStatus) {
                        ocrStatus.style.color = "#dc2626";
                        ocrStatus.innerHTML =
                            "✗ Terjadi kesalahan jaringan saat memproses OCR.";
                    }
                    const viewBtn = document.getElementById("view-ktp-btn");
                    const ktpOverlayImg = document.getElementById("ktp-overlay-img");
                    if (viewBtn && ktpOverlayImg) {
                        viewBtn.style.display = "inline-block";
                        viewBtn.style.color = "#dc2626";
                        ktpOverlayImg.src = "/img/ktp_template.jpeg";
                    }
                }
            });
        }

        const flowSummary = document.querySelector(".user-flow-summary");
        if (flowSummary) {
            const address =
                localStorage.getItem("pickup_address") || "Belum diisi";
            const city = localStorage.getItem("pickup_city") || "";
            const zip = localStorage.getItem("pickup_zip") || "";

            const addressSummary = flowSummary
                .querySelectorAll(".user-flow-summary__item")[2]
                ?.querySelector("strong");
            if (addressSummary) {
                addressSummary.textContent = `${address}, ${city} ${zip}`;
            }
            const savedCart = JSON.parse(
                localStorage.getItem("pickup_cart") || "[]",
            );
            const itemsSummary = flowSummary
                .querySelectorAll(".user-flow-summary__item")[0]
                ?.querySelector("strong");
            let totalItems = 0;
            let subtotal = 0;
            let itemsHtml = "";

            const orderTypeVal = localStorage.getItem("pickup_order_type") || "sell";
            const paymentMethodWrapper = document.getElementById("payment-method-wrapper");
            if (paymentMethodWrapper) {
                paymentMethodWrapper.style.display = orderTypeVal === "trade_in" ? "block" : "none";
            }

            //UI ringkasan pesanan
            savedCart.forEach((item) => {
                if (item && item.quantity) {
                    totalItems += item.quantity;
                    subtotal += item.price * item.quantity;
                    itemsHtml += `<div style="margin-bottom: 6px;">${item.name} <span style="font-weight: 400;">(${item.quantity} unit)</span><br><span style="font-size: 13px; font-weight: 600; color: var(--user-blue);">${rupiah(item.price * item.quantity)}</span></div>`;
                }
            });

            if (orderTypeVal === "trade_in") {
                const newAccuName = localStorage.getItem("pickup_trade_in_accu_name") || "Aki Baru";
                const newAccuPrice = parseFloat(localStorage.getItem("pickup_trade_in_accu_price")) || 0;
                itemsHtml += `<div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1; color: #dc2626;">
                    <strong>[TUKAR AKI] ${newAccuName}</strong><br>
                    <span style="font-size: 13px; font-weight: 600;">- ${rupiah(newAccuPrice)}</span>
                </div>`;
            }

            if (itemsSummary) {
                itemsSummary.innerHTML = itemsHtml || `${totalItems} unit aki`;
            }
            const deliverySummary = flowSummary
                .querySelectorAll(".user-flow-summary__item")[1]
                ?.querySelector("strong");
            const savedDeliveryMethod =
                localStorage.getItem("pickup_delivery_method") || "warehouse";
            const deliveryMethod =
                savedDeliveryMethod === "courier"
                    ? "Dijemput Kurir"
                    : "Antar ke Gudang";
            if (deliverySummary) {
                deliverySummary.textContent = deliveryMethod;
            }
            const fee = Number(localStorage.getItem("pickup_fee")) || 0;
            const totalSummary = flowSummary.querySelector(
                ".user-flow-summary__total strong",
            );
            if (totalSummary) {
                if (orderTypeVal === "trade_in") {
                    const newAccuPrice = parseFloat(localStorage.getItem("pickup_trade_in_accu_price")) || 0;
                    const netDiff = (subtotal - fee) - newAccuPrice;
                    const isMinus = netDiff < 0;
                    const totalLabel = totalSummary.previousElementSibling || flowSummary.querySelector(".user-flow-summary__total span");
                    if (totalLabel) {
                        totalLabel.textContent = isMinus ? "Estimasi Biaya Tambah" : "Total Estimasi Diterima";
                    }
                    totalSummary.textContent = rupiah(Math.abs(netDiff));
                } else {
                    totalSummary.textContent = rupiah(subtotal - fee);
                }
            }
        }
        const modal = document.querySelector("[data-identity-modal]");
        const modalConfirmBtn = modal?.querySelector(".user-button--primary");
        const modalCancelBtns = modal?.querySelectorAll("[data-modal-close]");

        modalCancelBtns?.forEach((btn) => {
            btn.addEventListener("click", () => {
                if (modal) modal.hidden = true;
            });
        });
        let formDataToSubmit = null;

        identityForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const form = e.target;

            let nameVal =
                form.querySelector('input[name="full_name"]')?.value.trim() ||
                "";
            const manualWrapper = document.getElementById("manual-name-wrapper");
            const manualInput = form.querySelector('input[name="manual_full_name"]');
            if (manualWrapper && manualWrapper.style.display !== "none" && manualInput && manualInput.value.trim() !== "") {
                nameVal = manualInput.value.trim();
            }

            const holderVal =
                form
                    .querySelector('input[name="account_holder"]')
                    ?.value.trim() || "";
            const numberVal =
                form
                    .querySelector('input[name="account_number"]')
                    ?.value.trim() || "";
            const waVal =
                form.querySelector('input[name="whatsapp"]')?.value.trim() ||
                "";

            //Validasi OCR nama
            if (!nameVal) {
                showCustomAlert(
                    "Harap upload foto KTP atau SIM terlebih dahulu agar nama dapat diekstrak otomatis.",
                );
                return;
            }
            const namePattern = /^[a-zA-Z\s.,]+$/;
            if (!namePattern.test(holderVal)) {
                showCustomAlert(
                    "Nama pemilik rekening hanya boleh berisi huruf, spasi, titik, dan koma!",
                );
                return;
            }
            const numberPattern = /^[0-9]+$/;
            if (!numberPattern.test(numberVal)) {
                showCustomAlert("Nomor rekening hanya boleh berisi angka!");
                return;
            }
            if (!numberPattern.test(waVal)) {
                showCustomAlert("Nomor WhatsApp hanya boleh berisi angka!");
                return;
            }
            //Validasi nama KTP harus sama dengan nama pemilik rekening (mengabaikan gelar)
            const stripTitles = (str) => {
                let s = str.toLowerCase();
                // Gabungkan gelar yang sering terpisah spasi akibat OCR (misal "s. psi" -> "spsi")
                s = s.replace(/\b(s|m)\b\.?\s+(psi|pd|kom|si|t|e|h|s)\b/g, "$1$2");
                s = s.replace(/\ba\b\.?\s+md\b/g, "amd");
                s = s.replace(/\bamd\b\.?\s+kep\b/g, "amdkep");
                
                // Hapus titik agar gelar seperti "s.psi" menyatu jadi "spsi"
                s = s.replace(/\./g, "");
                
                // Pisahkan string berdasarkan karakter non-alfabet (spasi, koma, strip, dll)
                let words = s.split(/[^a-z]+/);
                
                const titles = ['spsi', 'spd', 'skom', 'msi', 'prof', 'dr', 'amd', 'kep', 'amdkep', 'st', 'se', 'sh', 'ir', 'dra', 'drs', 'h', 'hj', 'ss', 'mkom', 'mpd', 'ssi', 'sst'];
                
                return words.filter(w => !titles.includes(w)).join('');
            };
            const cleanNameVal = stripTitles(nameVal);
            const cleanHolderVal = stripTitles(holderVal);
            if (
                cleanNameVal !== cleanHolderVal
            ) {
                showCustomAlert(
                    "Nama pada KTP/SIM tidak sesuai dengan nama pemilik rekening! Pastikan kedua nama identik untuk mencegah identitas ganda (Gelar akademik diabaikan).",
                );
                return;
            }
            const ktpInput = form.querySelector(
                'input[name="identity_document"]',
            );
            const ktpFile = ktpInput ? ktpInput.files[0] : null;
            if (!ktpFile) {
                showCustomAlert("Harap upload foto KTP atau SIM Anda.");
                return;
            }
            let ktpBase64 = null;
            try {
                ktpBase64 = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (ev) => resolve(ev.target.result);
                    reader.onerror = (err) => reject(err);
                    reader.readAsDataURL(ktpFile);
                });
            } catch (err) {
                showCustomAlert("Gagal membaca file gambar.");
                return;
            }

            const cityId = localStorage.getItem("pickup_city_id") || 1;
            const addressInput =
                document.querySelector('textarea[name="address"]') ||
                form.querySelector('input[name="full_name"]');

            const savedCartItems = JSON.parse(
                localStorage.getItem("pickup_cart") || "[]",
            );
            const itemsPayload = savedCartItems.map((item) => ({
                id: parseInt(item.id) || 1,
                quantity: parseInt(item.quantity) || 1,
            }));
            const deliveryMethodVal =
                localStorage.getItem("pickup_delivery_method") || "warehouse";

            const orderTypeVal = localStorage.getItem("pickup_order_type") || "sell";
            const newAccusIdVal = parseInt(localStorage.getItem("pickup_trade_in_accu_id")) || null;
            const paymentMethodInput = document.getElementById("payment-method-select");
            const paymentMethodVal = paymentMethodInput ? paymentMethodInput.value : null;

            if (orderTypeVal === "trade_in" && (!paymentMethodVal || paymentMethodVal === "")) {
                showCustomAlert("Harap pilih metode pembayaran untuk Trade In.");
                return;
            }

            formDataToSubmit = {
                name: nameVal,
                phone_number: waVal,
                address:
                    localStorage.getItem("pickup_address") ||
                    nameVal + " - Surabaya",
                address_note: localStorage.getItem("pickup_address_note") || "",
                banks_id: bankSelect ? parseInt(bankSelect.value) || 1 : 1,
                account_name: holderVal,
                account_number: numberVal,
                cities_id: parseInt(cityId) || 1,
                pickup_address:
                    localStorage.getItem("pickup_address") ||
                    "Jl. Raya Utama No. 12",
                pickup_address_note:
                    localStorage.getItem("pickup_address_note") || "",
                pickup_lat:
                    parseFloat(localStorage.getItem("pickup_lat")) || -7.2575,
                pickup_long:
                    parseFloat(localStorage.getItem("pickup_long")) || 112.7521,
                delivery_method: deliveryMethodVal,
                items: itemsPayload,
                ktp_base64: ktpBase64,
                order_type: orderTypeVal,
                new_accus_id: newAccusIdVal,
                payment_method: paymentMethodVal,
            };

            if (modal) {
                const elNama = document.getElementById("summary-nama");
                const elWa = document.getElementById("summary-wa");
                const elBank = document.getElementById("summary-bank");
                const elAlamat = document.getElementById("summary-alamat");
                const elCatatan = document.getElementById("summary-catatan");

                if (elNama) elNama.textContent = nameVal;
                if (elWa) elWa.textContent = waVal;
                if (elBank) {
                    const bankText = bankSelect
                        ? bankSelect.options[bankSelect.selectedIndex]?.text
                        : "";
                    elBank.textContent = `${bankText} - ${numberVal} (a.n ${holderVal})`;
                }
                if (elAlamat)
                    elAlamat.textContent = formDataToSubmit.pickup_address;
                if (elCatatan)
                    elCatatan.textContent =
                        formDataToSubmit.pickup_address_note || "-";

                //UI ringkasan aki di modal
                const modalCartItems =
                    document.getElementById("modal-cart-items");
                if (modalCartItems) {
                    let modalItemsHtml = "";
                    let modalSubtotal = 0;
                    savedCartItems.forEach((item) => {
                        const qty = parseInt(item.quantity) || 1;
                        const price = parseFloat(item.price) || 0;
                        const sub = qty * price;
                        modalSubtotal += sub;
                        modalItemsHtml += `
                            <tr>
                                <td><strong>${item.name}</strong></td>
                                <td style="text-align: center;">${qty} unit</td>
                                <td style="text-align: right;">${rupiah(price)}</td>
                                <td style="text-align: right; font-weight: 600; color: #0f172a;">${rupiah(sub)}</td>
                            </tr>
                        `;
                    });

                    if (orderTypeVal === "trade_in" && newAccusIdVal) {
                        const newAccuName = localStorage.getItem("pickup_trade_in_accu_name") || "Aki Baru";
                        const newAccuPrice = parseFloat(localStorage.getItem("pickup_trade_in_accu_price")) || 0;
                        modalItemsHtml += `
                            <tr>
                                <td><strong>${newAccuName}</strong><br><small style="color: #ef4444; font-weight:700;">[TRADE IN] Aki Baru</small></td>
                                <td style="text-align: center;">1 unit</td>
                                <td style="text-align: right; color:#ef4444;">- ${rupiah(newAccuPrice)}</td>
                                <td style="text-align: right; font-weight: 600; color: #ef4444;">- ${rupiah(newAccuPrice)}</td>
                            </tr>
                        `;
                    }

                    modalCartItems.innerHTML = modalItemsHtml;

                    const elSubtotal =
                        document.getElementById("modal-subtotal");
                    const elFee = document.getElementById("modal-fee");
                    const elTotal = document.getElementById("modal-total");
                    const fee = Number(localStorage.getItem("pickup_fee")) || 0;

                    if (elSubtotal)
                        elSubtotal.textContent = rupiah(modalSubtotal);
                    if (elFee)
                        elFee.textContent = fee === 0 ? "Gratis" : "- " + rupiah(fee);
                    if (elTotal) {
                        let finalTotal = modalSubtotal - fee;
                        if (orderTypeVal === "trade_in" && newAccusIdVal) {
                            const newAccuPrice = parseFloat(localStorage.getItem("pickup_trade_in_accu_price")) || 0;
                            finalTotal -= newAccuPrice;
                        }
                        const isMinus = finalTotal < 0;
                        elTotal.textContent = rupiah(Math.abs(finalTotal));
                        const totalLabelEl = document.querySelector(".user-receipt-modal .user-modal-summary__total strong:first-child") || document.getElementById("modal-total-label");
                        if (totalLabelEl) {
                            totalLabelEl.textContent = isMinus ? "Tagihan Pembayaran" : "Total yang Anda Terima";
                        }
                    }
                }

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
                showCustomAlert(res.message || "Gagal mengirim pesanan");
                if (modalConfirmBtn) {
                    modalConfirmBtn.disabled = false;
                    modalConfirmBtn.innerHTML =
                        'Sudah Benar <span aria-hidden="true">→</span>';
                }
            }
        }
    }
    const receiptContainer = document.querySelector("[data-receipt]");
    if (receiptContainer) {
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
                    const metaMeta = receiptContainer.querySelector(
                        ".user-receipt__meta strong",
                    );
                    const metaDate = receiptContainer.querySelector(
                        ".user-receipt__meta small",
                    );
                    if (metaMeta) metaMeta.textContent = `#ORDER-${o.order_id}`;

                    const orderStatus = o.status || "pending";
                    const isPaid = orderStatus === "completed";

                    if (metaDate) {
                        const transDate = o.created_at;
                        const dateObj = parseSafeDate(transDate);
                        const dateStr = dateObj.toLocaleDateString("id-ID");
                        const timeStr = dateObj
                            .toLocaleTimeString("id-ID", {
                                hour: "2-digit",
                                minute: "2-digit",
                            })
                            .replace(".", ":");

                        const updateObj = parseSafeDate(
                            o.updated_at || o.created_at,
                        );
                        const updateDateStr =
                            updateObj.toLocaleDateString("id-ID");
                        const updateTimeStr = updateObj
                            .toLocaleTimeString("id-ID", {
                                hour: "2-digit",
                                minute: "2-digit",
                            })
                            .replace(".", ":");

                        metaDate.innerHTML = `Tanggal transaksi: ${dateStr} ${timeStr} WIB<br><span style="color:#6d727c; font-weight:500;">Update: ${updateDateStr} ${updateTimeStr} WIB</span>`;
                    }
                    const badge = receiptContainer.querySelector(
                        "[data-receipt-badge]",
                    );
                    if (badge) {
                        let statusText = "UNPAID";
                        let statusClass = "unpaid";

                        if (orderStatus === "completed") {
                            statusText = "PAID";
                            statusClass = "paid";
                        } else if (orderStatus === "processing") {
                            statusText = "PROCESSING";
                            statusClass = "processing";
                        } else if (orderStatus === "cancelled") {
                            statusText = "CANCELLED";
                            statusClass = "cancelled";
                        }

                        badge.textContent = statusText;
                        badge.className = `user-receipt__status user-receipt__status--${statusClass}`;
                    }

                    const cancelReasonContainer = document.getElementById(
                        "receipt-cancel-reason",
                    );
                    if (cancelReasonContainer) {
                        if (orderStatus === "cancelled" && o.cancel_reason) {
                            const cancelReasonText =
                                document.getElementById("cancel-reason-text");
                            if (cancelReasonText)
                                cancelReasonText.textContent = o.cancel_reason;
                            cancelReasonContainer.style.display = "block";
                        } else {
                            cancelReasonContainer.style.display = "none";
                        }
                    }
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
                    const blockPenyerahan = receiptContainer.querySelectorAll(
                        ".user-receipt__block",
                    )[1];

                    let subtotal = 0;
                    const itemsList = receipt.accus || [];
                    itemsList.forEach((item) => {
                        subtotal += item.subtotal || 0;
                    });
                    const totalCost = receipt.price_owed || subtotal;
                    let deliveryCost = subtotal - totalCost;
                    
                    // Handle old orders where totalCost (price_owed) was computed incorrectly as > subtotal
                    if (deliveryCost < 0) {
                        deliveryCost = Math.abs(deliveryCost);
                    }
                    
                    const orderDeliveryMethod =
                        o.delivery_method || "warehouse";
                    const isCourier = orderDeliveryMethod === "courier";

                    if (blockPenyerahan) {
                        const dds = blockPenyerahan.querySelectorAll("dd");
                        if (dds[0])
                            dds[0].textContent = isCourier
                                ? "Dijemput Kurir"
                                : "Antar ke Gudang";
                        if (dds[1])
                            dds[1].textContent = o.city ? o.city.name : "-";
                        if (dds[2])
                            dds[2].textContent =
                                isCourier && deliveryCost > 0
                                    ? "- " + rupiah(deliveryCost)
                                    : "Gratis";

                        const noteDisplay = document.getElementById(
                            "receipt-note-display",
                        );
                        const btnEditNote =
                            document.getElementById("btn-edit-note");
                        const editContainer = document.getElementById(
                            "receipt-note-edit-container",
                        );
                        const noteInput =
                            document.getElementById("receipt-note-input");
                        const btnCancelNote =
                            document.getElementById("btn-cancel-note");
                        const btnSaveNote =
                            document.getElementById("btn-save-note");

                        if (noteDisplay) {
                            noteDisplay.textContent =
                                o.pickup_address_note || "-";
                        }

                        if (
                            btnEditNote &&
                            editContainer &&
                            noteInput &&
                            btnCancelNote &&
                            btnSaveNote
                        ) {
                            if (!btnEditNote.hasAttribute("data-bound")) {
                                btnEditNote.setAttribute("data-bound", "true");

                                btnEditNote.addEventListener("click", () => {
                                    noteInput.value =
                                        noteDisplay.textContent === "-"
                                            ? ""
                                            : noteDisplay.textContent;
                                    noteDisplay.style.display = "none";
                                    editContainer.style.display = "block";
                                    btnEditNote.style.display = "none";
                                });

                                btnCancelNote.addEventListener("click", () => {
                                    noteDisplay.style.display = "block";
                                    editContainer.style.display = "none";
                                    btnEditNote.style.display = "block";
                                });

                                btnSaveNote.addEventListener(
                                    "click",
                                    async () => {
                                        const newNote = noteInput.value.trim();
                                        btnSaveNote.disabled = true;
                                        btnSaveNote.textContent = "...";

                                        try {
                                            const res = await fetch(
                                                `/api/customer/orders/${o.order_id}/note`,
                                                {
                                                    method: "PUT",
                                                    headers: {
                                                        "Content-Type":
                                                            "application/json",
                                                        Accept: "application/json",
                                                    },
                                                    body: JSON.stringify({
                                                        note: newNote,
                                                    }),
                                                },
                                            );
                                            if (res.ok) {
                                                noteDisplay.textContent =
                                                    newNote || "-";
                                                localStorage.setItem(
                                                    "pickup_address_note",
                                                    newNote,
                                                );
                                            } else {
                                                showCustomAlert(
                                                    "Gagal memperbarui catatan.",
                                                );
                                            }
                                        } catch (e) {
                                            console.error(e);
                                            showCustomAlert(
                                                "Terjadi kesalahan.",
                                            );
                                        }

                                        btnSaveNote.disabled = false;
                                        btnSaveNote.textContent = "Simpan";

                                        noteDisplay.style.display = "block";
                                        editContainer.style.display = "none";
                                        btnEditNote.style.display = "block";
                                    },
                                );
                            }
                        }
                    }
                    const tableBody = receiptContainer.querySelector(
                        ".user-receipt__table tbody",
                    );
                    if (tableBody) {
                        if (itemsList.length === 0) {
                            tableBody.innerHTML =
                                '<tr><td colspan="4"><div class="user-receipt__empty"><strong>Detail aki belum tersedia</strong><span>Item akan muncul setelah transaksi terhubung.</span></div></td></tr>';
                        } else {
                            tableBody.innerHTML = itemsList
                                .map(
                                    (item) => `
                                <tr>
                                    <td>
                                        <strong>${item.name || "-"}</strong>
                                    </td>
                                    <td>${item.amount || 1} unit</td>
                                    <td>${rupiah(item.price || 0)}</td>
                                    <td><strong>${rupiah(item.subtotal || 0)}</strong></td>
                                </tr>
                            `,
                                )
                                .join("");
                        }
                    }
                    const summaryBlocks = receiptContainer.querySelector(
                        ".user-receipt__summary",
                    );
                    if (summaryBlocks) {
                        const divs = summaryBlocks.querySelectorAll("div");
                        if (divs[0])
                            divs[0].querySelector("strong").textContent =
                                rupiah(subtotal);
                        if (divs[1])
                            divs[1].querySelector("strong").textContent =
                                deliveryCost > 0
                                    ? "- " + rupiah(deliveryCost)
                                    : "Gratis";

                        const grandTotalElement =
                            receiptContainer.querySelector(
                                ".user-receipt__grand-total strong",
                            );
                        if (grandTotalElement) {
                            grandTotalElement.textContent = rupiah(totalCost);
                        }
                    }
                    const proofSection = document.querySelector(
                        "[data-proof-section]",
                    );
                    if (proofSection) {
                        if (isPaid) {
                            proofSection.removeAttribute("hidden");
                            proofSection.style.display = "block";
                            if (receipt.transfer) {
                                const transfer = receipt.transfer;
                                const dds = proofSection.querySelectorAll("dd");
                                if (dds[0]) {
                                    const transferDateObj = parseSafeDate(
                                        transfer.transfer_date,
                                    );
                                    dds[0].innerHTML = `${transferDateObj.toLocaleDateString("id-ID")}<br><small style="font-size: 11px; color: #64748b;">${transferDateObj.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" }).replace(".", ":")} WIB</small>`;
                                }
                                if (dds[1])
                                    dds[1].textContent = transfer.id || "-";
                                const img = proofSection.querySelector("img");
                                const notFoundSpan = proofSection.querySelector(
                                    ".user-image-not-found",
                                );
                                if (img && transfer.proof_image) {
                                    img.src = `/storage/${transfer.proof_image}`;
                                    img.parentElement.classList.add(
                                        "is-loaded",
                                    );
                                    if (notFoundSpan)
                                        notFoundSpan.style.display = "none";
                                } else {
                                    if (img)
                                        img.parentElement.classList.remove(
                                            "is-loaded",
                                        );
                                    if (notFoundSpan)
                                        notFoundSpan.style.display = "flex";
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
    if (typeof window.userCart === "undefined") {
        window.userCart = new Map();
    }
    const btnOpenUserMap = document.getElementById("btn-open-user-map");
    const modalUserMap = document.getElementById("modal-user-map");
    const btnSaveUserCoords = document.getElementById("btn-save-user-coords");
    const userCoordsBadge = document.getElementById("user-coords-badge");
    const userSelectedLat = document.getElementById("user-selected-lat");
    const userSelectedLng = document.getElementById("user-selected-lng");

    const userAddressInput = document.getElementById("user-address-input");
    const userCityInput = document.getElementById("user-city-input");
    const userZipInput = document.getElementById("user-zip-input");
    const userNoteInput = document.getElementById("user-note-input");
    const checkoutSubmitBtn = document.getElementById("checkout-submit-btn");

    const nearestWarehouseInfo = document.getElementById(
        "nearest-warehouse-info",
    );
    const nearestWarehouseDetail = document.getElementById(
        "nearest-warehouse-detail",
    );

    let userLat = parseFloat(localStorage.getItem("pickup_lat")) || null;
    let userLng = parseFloat(localStorage.getItem("pickup_long")) || null;

    if (userLat && userLng) {
        const addressFields = document.getElementById("user-address-fields");
        const latlongText = document.getElementById("user-latlong-text");
        if (addressFields) addressFields.style.display = "block";
        if (latlongText) {
            latlongText.style.display = "block";
            latlongText.innerHTML = `<strong>Koordinat Peta:</strong> ${userLat.toFixed(6)}, ${userLng.toFixed(6)}`;
        }
    }
    let userMap = null;
    let userMarker = null;
    let warehousesList = [];
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = ((lat2 - lat1) * Math.PI) / 180;
        const dLon = ((lon2 - lon1) * Math.PI) / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos((lat1 * Math.PI) / 180) *
                Math.cos((lat2 * Math.PI) / 180) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
    function getSelectedCityCoords() {
        const cityName =
            selectedCityName ||
            localStorage.getItem("pickup_city_name") ||
            localStorage.getItem("pickup_city") ||
            "";
        const key = cityName.toLowerCase().trim();
        if (cityCoordinates[key]) {
            return cityCoordinates[key];
        }
        if (fallbackCityCoords[key]) {
            return fallbackCityCoords[key];
        }
        return { lat: -7.2575, lng: 112.7521 };
    }
    async function loadWarehouses() {
        const res = await fetchPublicApi("/storages");
        if (res.data) {
            warehousesList = res.data;
            warehousesList.forEach((w) => {
                const cityName = (w.name || "").toLowerCase();
                const cityWords = cityName.split(/\s+/);
                cityWords.forEach((word) => {
                    if (word.length > 3 && word !== "gudang") {
                        if (!cityCoordinates[word]) {
                            cityCoordinates[word] = {
                                lat: parseFloat(w.lat),
                                lng: parseFloat(w.long),
                            };
                        }
                    }
                });
            });

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

        warehousesList.forEach((w) => {
            const dist = calculateDistance(
                userLat,
                userLng,
                parseFloat(w.lat),
                parseFloat(w.long),
            );
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
            localStorage.setItem(
                "nearest_warehouse_distance",
                minDistance.toFixed(2),
            );
            updatePickupFee(minDistance);
        }
    }

    function updatePickupFee(distance) {
        const radioChecked = document.querySelector('input[name="delivery_method"]:checked');
        const selectedMethod = radioChecked ? radioChecked.value : (localStorage.getItem("pickup_delivery_method") || "warehouse");
        
        const pickupLabel =
            document.getElementById("user-pickup-fee-label") ||
            document.querySelector("[data-cart-pickup]");

        if (selectedMethod === "courier") {
            const fee = Math.max(10000, Math.round(distance * 2000));
            localStorage.setItem("pickup_fee", fee);
            if (pickupLabel) pickupLabel.textContent = "- " + rupiah(fee);
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
                cartTotal.textContent = rupiah(subVal - fee);
            }
        }
    }
    const savedOrderType = localStorage.getItem("pickup_order_type") || "sell";
    document
        .querySelectorAll('input[name="order_type_selection"]')
        .forEach((radio) => {
            if (radio.value === savedOrderType) {
                radio.checked = true;
                const card = radio.closest(".user-radio-card");
                if (card) card.classList.add("is-selected");
            } else {
                const card = radio.closest(".user-radio-card");
                if (card) card.classList.remove("is-selected");
            }

            radio.addEventListener("change", () => {
                document
                    .querySelectorAll('input[name="order_type_selection"]')
                    .forEach((r) => {
                        const card = r.closest(".user-radio-card");
                        if (card) card.classList.toggle("is-selected", r.checked);
                    });
                localStorage.setItem("pickup_order_type", radio.value);
            });
        });

    document
        .querySelectorAll('input[name="delivery_method"]')
        .forEach((radio) => {
            radio.addEventListener("change", () => {
                const selectedMethod =
                    document.querySelector(
                        'input[name="delivery_method"]:checked',
                    )?.value || "warehouse";
                localStorage.setItem("pickup_delivery_method", selectedMethod);

                if (userLat && userLng) {
                    findAndDisplayNearestWarehouse();
                } else {
                    const pickupLabel =
                        document.getElementById("user-pickup-fee-label") ||
                        document.querySelector("[data-cart-pickup]");
                    if (selectedMethod === "courier") {
                        if (pickupLabel)
                            pickupLabel.textContent =
                                "Dihitung setelah pilih lokasi";
                    } else {
                        localStorage.removeItem("pickup_fee");
                        if (pickupLabel) pickupLabel.textContent = "Gratis";
                    }
                }

                // Show/hide pickup fee warning hint
                const pickupWarning = document.getElementById("pickup-fee-warning");
                if (pickupWarning) {
                    pickupWarning.style.display = selectedMethod === "courier" ? "block" : "none";
                }

                if (selectedMethod === "courier" && btnOpenUserMap) {
                    btnOpenUserMap.click();
                }
            });
        });
    if (userAddressInput)
        userAddressInput.value = localStorage.getItem("pickup_address") || "";
    if (userCityInput)
        userCityInput.value =
            localStorage.getItem("pickup_city") || selectedCityName || "";
    if (userZipInput)
        userZipInput.value = localStorage.getItem("pickup_zip") || "";
    if (userNoteInput)
        userNoteInput.value = localStorage.getItem("pickup_address_note") || "";

    if (window.location.pathname === "/identity" || window.location.pathname === "/user/identitas") {
        const isTradeIn = localStorage.getItem("pickup_order_type") === "trade_in";
        const paymentWrapper = document.getElementById("payment-method-wrapper");
        const paymentSelect = document.getElementById("payment-method-select");
        if (paymentWrapper && paymentSelect && isTradeIn) {
            paymentWrapper.style.display = "block";
            paymentSelect.required = true;
        }
    }

    const addressBadge = document.getElementById("user-selected-address");
    if (addressBadge && localStorage.getItem("pickup_address")) {
        addressBadge.textContent = localStorage.getItem("pickup_address");
    }

    if (userLat && userLng) {
        if (userCoordsBadge) userCoordsBadge.style.display = "block";
        if (userSelectedLat) userSelectedLat.textContent = userLat.toFixed(5);
        if (userSelectedLng) userSelectedLng.textContent = userLng.toFixed(5);
    }
    async function geocodeAddress() {
        const address = userAddressInput ? userAddressInput.value.trim() : "";
        const city = userCityInput
            ? userCityInput.value.trim()
            : selectedCityName || "";

        if (!address && !city) return null;

        const searchQuery = [address, city, "Indonesia"]
            .filter(Boolean)
            .join(", ");

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=1&countrycodes=id`,
            );
            const results = await response.json();

            if (results && results.length > 0) {
                return {
                    lat: parseFloat(results[0].lat),
                    lng: parseFloat(results[0].lon),
                };
            }
        } catch (err) {
            console.warn("Geocoding failed:", err);
        }

        return null;
    }

    async function handleAddressChange() {
        if (!userAddressInput) return;
        const address = userAddressInput.value.trim();
        if (address) {
            const geocoded = await geocodeAddress();
            if (geocoded) {
                userLat = geocoded.lat;
                userLng = geocoded.lng;
                localStorage.setItem("pickup_lat", userLat);
                localStorage.setItem("pickup_long", userLng);
                findAndDisplayNearestWarehouse();

                if (
                    typeof userMap !== "undefined" &&
                    userMap &&
                    typeof userMarker !== "undefined" &&
                    userMarker
                ) {
                    userMap.setView([userLat, userLng], 16);
                    userMarker.setLatLng([userLat, userLng]);
                }
            }
        }
    }

    if (userAddressInput) {
        userAddressInput.addEventListener("blur", handleAddressChange);
    }
    if (userCityInput) {
        userCityInput.addEventListener("blur", handleAddressChange);
    }
    btnOpenUserMap?.addEventListener("click", async () => {
        if (modalUserMap) modalUserMap.style.display = "flex";
        if (typeof L === "undefined") {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
            document.head.appendChild(link);

            const script = document.createElement("script");
            script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
            document.head.appendChild(script);

            script.onload = async () => {
                await initPickerMap();
            };
        } else {
            await initPickerMap();
        }
    });

    async function initPickerMap() {
        let mapLat, mapLng;

        if (userLat && userLng) {
            mapLat = userLat;
            mapLng = userLng;
        } else {
            const geocoded = await geocodeAddress();
            if (geocoded) {
                mapLat = geocoded.lat;
                mapLng = geocoded.lng;
            } else {
                const cityCoords = getSelectedCityCoords();
                mapLat = cityCoords.lat;
                mapLng = cityCoords.lng;
            }
        }

        if (!userMap) {
            userMap = L.map("user-map-picker").setView([mapLat, mapLng], 16);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors",
            }).addTo(userMap);

            userMarker = L.marker([mapLat, mapLng], { draggable: true }).addTo(
                userMap,
            );

            userMap.on("click", (e) => {
                userMarker.setLatLng(e.latlng);
            });
        } else {
            userMap.setView([mapLat, mapLng], 16);
            userMarker.setLatLng([mapLat, mapLng]);
            setTimeout(() => {
                userMap.invalidateSize();
            }, 200);
        }
        const mapSearchInput = document.getElementById("map-search-input");
        const btnMapSearch = document.getElementById("btn-map-search");
        if (
            btnMapSearch &&
            mapSearchInput &&
            !btnMapSearch.hasAttribute("data-bound")
        ) {
            btnMapSearch.setAttribute("data-bound", "true");
            btnMapSearch.addEventListener("click", async () => {
                const query = mapSearchInput.value.trim();
                if (!query) return;
                const oldText = btnMapSearch.textContent;
                btnMapSearch.textContent = "...";

                const performSearch = async (q) => {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&countrycodes=id`,
                    );
                    return await res.json();
                };

                try {
                    let results = await performSearch(
                        `${query}, ${selectedCityName || "Surabaya"}`,
                    );

                    if (!results || results.length === 0) {
                        results = await performSearch(query);
                    }

                    if (!results || results.length === 0) {
                        let simplified = query
                            .replace(
                                /(no\.|nomor|blok|kav\.|kavling)\s*[a-z0-9-]+/gi,
                                "",
                            )
                            .trim();
                        simplified = simplified
                            .replace(/\s+\d+[a-z]*$/i, "")
                            .trim();
                        if (simplified && simplified !== query) {
                            results = await performSearch(
                                `${simplified}, ${selectedCityName || "Surabaya"}`,
                            );
                            if (!results || results.length === 0) {
                                results = await performSearch(simplified);
                            }
                        }
                    }

                    if (results && results.length > 0) {
                        const lat = parseFloat(results[0].lat);
                        const lon = parseFloat(results[0].lon);
                        if (userMap && userMarker) {
                            userMap.setView([lat, lon], 16);
                            userMarker.setLatLng([lat, lon]);
                        }
                    } else {
                        showCustomAlert(
                            "Lokasi tidak ditemukan. Cobalah hapus nomor rumah atau cari nama jalan utamanya saja, lalu geser pin secara manual.",
                        );
                    }
                } catch (e) {
                    console.error(e);
                }
                btnMapSearch.textContent = oldText;
            });
            mapSearchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    btnMapSearch.click();
                }
            });
        }
    }
    btnSaveUserCoords?.addEventListener("click", async () => {
        if (userMarker) {
            btnSaveUserCoords.disabled = true;
            btnSaveUserCoords.textContent = "Menyimpan...";

            const pos = userMarker.getLatLng();
            userLat = pos.lat;
            userLng = pos.lng;
            localStorage.setItem("pickup_lat", userLat);
            localStorage.setItem("pickup_long", userLng);

            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLng}&zoom=18&addressdetails=1`,
                );
                const result = await response.json();
                if (result && result.display_name) {
                    const mapSearchInput =
                        document.getElementById("map-search-input");
                    const userTypedAddress = mapSearchInput
                        ? mapSearchInput.value.trim()
                        : "";

                    const addressStr = userTypedAddress || result.display_name;
                    const cityStr =
                        result.address?.city ||
                        result.address?.town ||
                        result.address?.village ||
                        result.address?.county ||
                        "";
                    const zipStr = result.address?.postcode || "";

                    if (userAddressInput) userAddressInput.value = addressStr;
                    if (userCityInput) userCityInput.value = cityStr;
                    if (userZipInput) userZipInput.value = zipStr;

                    localStorage.setItem("pickup_address", addressStr);
                    localStorage.setItem("pickup_city", cityStr);
                    localStorage.setItem("pickup_zip", zipStr);

                    const addressBadge = document.getElementById(
                        "user-selected-address",
                    );
                    if (addressBadge) addressBadge.textContent = addressStr;
                }
            } catch (e) {
                console.error("Reverse geocode failed", e);
            }

            if (userCoordsBadge) userCoordsBadge.style.display = "block";

            const addressFields = document.getElementById(
                "user-address-fields",
            );
            const latlongText = document.getElementById("user-latlong-text");
            if (addressFields) addressFields.style.display = "block";
            if (latlongText) {
                latlongText.style.display = "block";
                latlongText.innerHTML = `<strong>Koordinat Peta:</strong> ${userLat.toFixed(6)}, ${userLng.toFixed(6)}`;
            }

            if (modalUserMap) modalUserMap.style.display = "none";
            findAndDisplayNearestWarehouse();

            btnSaveUserCoords.disabled = false;
            btnSaveUserCoords.textContent = "Konfirmasi Lokasi";
        }
    });
    checkoutSubmitBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        const cartSize = window.userCart ? window.userCart.size : 0;
        if (cartSize === 0) {
            showCustomAlert(
                "Keranjang belanja kosong! Silakan tambahkan minimal satu aki ke keranjang sebelum melanjutkan.",
            );
            return;
        }
        const address = userAddressInput ? userAddressInput.value.trim() : "";
        const city = userCityInput ? userCityInput.value.trim() : "";
        const zip = userZipInput ? userZipInput.value.trim() : "";

        if (!address || !city) {
            showCustomAlert(
                "Harap tentukan lokasi Anda melalui peta terlebih dahulu.",
            );
            return;
        }
        if (!userLat || !userLng) {
            showCustomAlert(
                "Harap tentukan lokasi koordinat Anda di peta dengan menekan tombol peta.",
            );
            return;
        }
        localStorage.setItem("pickup_address", address);
        localStorage.setItem("pickup_city", city);
        localStorage.setItem("pickup_zip", zip);
        const note = userNoteInput ? userNoteInput.value.trim() : "";
        localStorage.setItem("pickup_address_note", note);
        localStorage.setItem(
            "pickup_cart",
            JSON.stringify(Array.from(window.userCart.values())),
        );
        const selectedDelivery =
            document.querySelector('input[name="delivery_method"]:checked')
                ?.value || "warehouse";
        localStorage.setItem("pickup_delivery_method", selectedDelivery);
        
        const selectedOrderType = 
            document.querySelector('input[name="order_type_selection"]:checked')
                ?.value || "sell";
        localStorage.setItem("pickup_order_type", selectedOrderType);
        
        if (selectedOrderType === "trade_in") {
            window.location.href = "/trade-in";
        } else {
            window.location.href = "/user/identitas";
        }
    });

    const viewBtn = document.getElementById("view-ktp-btn");
    const ktpOverlay = document.getElementById("ktp-overlay");
    const closeOverlay = document.getElementById("close-ktp-overlay");
    if (viewBtn && ktpOverlay && closeOverlay) {
        viewBtn.addEventListener("click", (e) => {
            e.preventDefault();
            ktpOverlay.style.display = "flex";
        });
        closeOverlay.addEventListener("click", () => {
            ktpOverlay.style.display = "none";
        });
        ktpOverlay.addEventListener("click", (e) => {
            if (e.target === ktpOverlay) {
                ktpOverlay.style.display = "none";
            }
        });
    }

    // Trade-In Logic
    if (window.location.pathname === "/trade-in") {
        let newAccus = [];
        let selectedNewAccu = null;

        const renderNewAccus = (data) => {
            const grid = document.getElementById("new-accus-grid");
            if (!grid) return;
            grid.innerHTML = "";
            if (data.length === 0) {
                grid.innerHTML = '<div style="padding:40px; text-align:center; color:#64748b; font-size:14px; grid-column:1/-1;">Tidak ada aki baru yang cocok.</div>';
                return;
            }
            data.forEach(accu => {
                const card = document.createElement("div");
                card.className = "user-product-card";
                card.style.cursor = "pointer";
                if (selectedNewAccu && selectedNewAccu.id === accu.id) {
                    card.style.border = "2px solid #2563eb";
                    card.style.boxShadow = "0 10px 25px -5px rgba(37,99,235,0.3)";
                }
                const priceFmt = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(accu.price);
                card.innerHTML = `
                    <div class="user-product-card__content">
                        <h3>${accu.name}</h3>
                        <p class="user-product-card__price" style="color:#2563eb;">${priceFmt}</p>
                    </div>
                `;
                card.onclick = () => {
                    selectedNewAccu = accu;
                    renderNewAccus(newAccus);
                    updateTradeInSelected();
                };
                grid.appendChild(card);
            });
        };

        const updateTradeInSelected = () => {
            const container = document.getElementById("new-accu-selected");
            const btn = document.getElementById("btn-trade-in-continue");
            const rejectContainer = document.getElementById("reject-accu-summary");
            const netSummary = document.getElementById("trade-in-net-summary");
            if (!container || !btn) return;

            const savedCart = JSON.parse(localStorage.getItem("pickup_cart") || "[]");
            let rejectSubtotal = 0;

            if (rejectContainer) {
                if (savedCart.length > 0) {
                    let itemsHtml = savedCart.map(item => {
                        const sub = item.price * item.quantity;
                        rejectSubtotal += sub;
                        return `<div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px; color:#475569;">
                            <span>${item.name} (${item.quantity} unit)</span>
                            <span style="font-weight:600; color:#0f172a;">${rupiah(sub)}</span>
                        </div>`;
                    }).join("");

                    rejectContainer.innerHTML = `
                        <div style="padding:12px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:14px;">
                            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:6px; display:block;">Aki Reject Pilihan Anda:</span>
                            ${itemsHtml}
                            <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:#10b981; padding-top:6px; margin-top:6px; border-top:1px dashed #cbd5e1;">
                                <span>Potongan Aki Reject:</span>
                                <span>- ${rupiah(rejectSubtotal)}</span>
                            </div>
                        </div>
                    `;
                } else {
                    rejectContainer.innerHTML = `
                        <div style="padding:10px; background:#fff7ed; border-radius:8px; border:1px solid #fed7aa; color:#c2410c; font-size:12px; margin-bottom:14px;">
                            <strong>Belum ada aki reject dipilih.</strong> <a href="/user" style="color:#ea580c; text-decoration:underline;">Pilih di katalog landing</a>.
                        </div>
                    `;
                }
            }

            if (selectedNewAccu) {
                const priceFmt = rupiah(selectedNewAccu.price);
                container.innerHTML = `
                    <div style="text-align:left; padding:12px; background:#eff6ff; border-radius:8px; border:1px solid #bfdbfe;">
                        <span style="font-size:11px; font-weight:700; color:#1d4ed8; text-transform:uppercase; margin-bottom:4px; display:block;">Aki Baru Pilihan Anda:</span>
                        <div style="font-size:15px; font-weight:600; color:#1e3a8a; margin-bottom:4px;">${selectedNewAccu.name}</div>
                        <div style="font-size:16px; font-weight:700; color:#2563eb;">${priceFmt}</div>
                    </div>
                `;
                btn.style.opacity = "1";
                btn.style.pointerEvents = "auto";

                if (netSummary) {
                    const diff = selectedNewAccu.price - rejectSubtotal;
                    if (diff >= 0) {
                        netSummary.innerHTML = `
                            <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid #e2e8f0; font-size:13px;">
                                <strong>Estimasi Biaya Tambah:</strong>
                                <strong style="color:#2563eb; font-size:15px;">${rupiah(diff)}</strong>
                            </div>
                        `;
                    } else {
                        netSummary.innerHTML = `
                            <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid #e2e8f0; font-size:13px;">
                                <strong>Estimasi Uang Diterima:</strong>
                                <strong style="color:#10b981; font-size:15px;">${rupiah(Math.abs(diff))}</strong>
                            </div>
                        `;
                    }
                }
            } else {
                container.innerHTML = `
                    <span style="display:block; font-size:24px; margin-bottom:8px;">
                        <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:none; stroke:currentColor; stroke-width:2; margin:auto;">
                            <rect x="2" y="7" width="20" height="15" rx="2" ry="2"/>
                            <polyline points="17 2 12 7 7 2"/>
                        </svg>
                    </span>
                    <strong>Belum ada aki baru dipilih</strong>
                    <p style="font-size:12px;">Silakan pilih 1 aki dari katalog.</p>
                `;
                btn.style.opacity = "0.5";
                btn.style.pointerEvents = "none";
                if (netSummary) netSummary.innerHTML = "";
            }
        };

        const initTradeIn = async () => {
            updateTradeInSelected();
            const res = await fetchPublicApi("/new-accus");
            if (res && res.data) {
                newAccus = res.data;
                renderNewAccus(newAccus);
            }
        };

        const searchInputTradeIn = document.getElementById("new-accu-search-input");
        if (searchInputTradeIn) {
            searchInputTradeIn.addEventListener("input", (e) => {
                const q = e.target.value.toLowerCase().trim();
                const filtered = newAccus.filter(a => a.name.toLowerCase().includes(q) || (a.brand_relation && a.brand_relation.name.toLowerCase().includes(q)));
                renderNewAccus(filtered);
            });
        }

        const btnContinue = document.getElementById("btn-trade-in-continue");
        if (btnContinue) {
            btnContinue.addEventListener("click", () => {
                if (!selectedNewAccu) return;
                localStorage.setItem("pickup_trade_in_accu_id", selectedNewAccu.id);
                localStorage.setItem("pickup_trade_in_accu_name", selectedNewAccu.name);
                localStorage.setItem("pickup_trade_in_accu_price", selectedNewAccu.price);
                localStorage.setItem("pickup_order_type", "trade_in");
                window.location.href = "/identity";
            });
        }

        initTradeIn();
    }
});
