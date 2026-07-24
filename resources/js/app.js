import.meta.glob(["../img/**", "/resources/img/**"]);

const sidebar = document.querySelector("#admin-sidebar");
const sidebarOverlay = document.querySelector("[data-sidebar-overlay]");
const sidebarOpen = document.querySelector("[data-sidebar-open]");
const sidebarClose = document.querySelector("[data-sidebar-close]");

const setSidebar = (isOpen) => {
    sidebar?.classList.toggle("is-open", isOpen);
    sidebarOverlay?.classList.toggle("is-visible", isOpen);
    document.body.classList.toggle("overflow-hidden", isOpen);
};

sidebarOpen?.addEventListener("click", () => setSidebar(true));
sidebarClose?.addEventListener("click", () => setSidebar(false));
sidebarOverlay?.addEventListener("click", () => setSidebar(false));

document.querySelectorAll("[data-nav-link]").forEach((link) => {
    link.addEventListener("click", () => {
        document
            .querySelectorAll("[data-nav-link]")
            .forEach((item) => item.classList.remove("is-active"));
        link.classList.add("is-active");
        setSidebar(false);
    });
});

const profileToggle = document.querySelector("[data-profile-toggle]");
const profileMenu = document.querySelector("[data-profile-menu]");

profileToggle?.addEventListener("click", () => {
    const isOpen = profileToggle.getAttribute("aria-expanded") === "true";
    profileToggle.setAttribute("aria-expanded", String(!isOpen));
    if (profileMenu) profileMenu.hidden = isOpen;
});

document.addEventListener("click", (event) => {
    if (!profileToggle || !profileMenu || profileMenu.hidden) return;
    if (
        !profileToggle.contains(event.target) &&
        !profileMenu.contains(event.target)
    ) {
        profileToggle.setAttribute("aria-expanded", "false");
        profileMenu.hidden = true;
    }
});
const userHeaders = document.querySelectorAll("[data-user-header]");

userHeaders.forEach((header) => {
    if (header.classList.contains("user-header--solid")) return;
    const updateHeader = () =>
        header.classList.toggle("is-scrolled", window.scrollY > 24);
    window.addEventListener("scroll", updateHeader, { passive: true });
    updateHeader();
});

const userMenuToggle = document.querySelector("[data-user-menu-toggle]");
const userMenu = document.querySelector("[data-user-menu]");

userMenuToggle?.addEventListener("click", () => {
    const isOpen = userMenuToggle.getAttribute("aria-expanded") === "true";
    userMenuToggle.setAttribute("aria-expanded", String(!isOpen));
    userMenu?.classList.toggle("is-open", !isOpen);
});

const handleUserNavClick = (event) => {
    const link = event.currentTarget;
    const href = link.getAttribute("href");
    if (!href) return;

    const hashIndex = href.indexOf("#");
    const targetHash = hashIndex !== -1 ? href.substring(hashIndex + 1) : "";
    const targetPath = hashIndex !== -1 ? href.substring(0, hashIndex) : href;

    const currentPath = window.location.pathname.replace(/\/$/, "");
    const isLandingPage =
        currentPath === "/user" ||
        currentPath === "" ||
        currentPath.endsWith("/user");

    if (isLandingPage) {
        let targetElement = null;

        if (targetHash) {
            targetElement = document.getElementById(targetHash);
        } else if (
            targetPath === "/user" ||
            targetPath === "#" ||
            href === "/user"
        ) {
            targetElement = document.getElementById("top") || document.body;
        }

        if (targetElement) {
            event.preventDefault();

            userMenuToggle?.setAttribute("aria-expanded", "false");
            userMenu?.classList.remove("is-open");

            const header = document.querySelector("[data-user-header]");
            const headerOffset = header ? header.offsetHeight : 80;
            const elementPosition =
                targetElement.getBoundingClientRect().top + window.scrollY;
            const offsetPosition = targetHash
                ? Math.max(0, elementPosition - headerOffset)
                : 0;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });

            if (targetHash) {
                history.pushState(null, "", "#" + targetHash);
            } else {
                history.pushState(null, "", window.location.pathname);
            }
        }
    }
};

document
    .querySelectorAll('[data-user-nav-link], a[href^="#"]')
    .forEach((link) => {
        link.addEventListener("click", handleUserNavClick);
    });

const citySelect = document.querySelector("[data-city-select]");
const cityWarning = document.querySelector("[data-city-warning]");
const batteryContainer = document.querySelector("[data-battery-container]");
const batteryDisabledPlaceholder = document.querySelector("[data-battery-disabled-placeholder]");
const storeCityName = document.querySelector("[data-store-city]");

const updateCityState = () => {
    if (!citySelect) return;
    const selectedVal = citySelect.value;
    const cityText = citySelect.options[citySelect.selectedIndex]?.text;

    if (!selectedVal) {
        if (batteryContainer) batteryContainer.style.display = "none";
        if (batteryDisabledPlaceholder) batteryDisabledPlaceholder.style.display = "block";
        if (cityWarning) {
            cityWarning.innerHTML = "<strong>*Daftar harga aki berdasarkan wilayah yang dipilih.</strong>";
        }
    } else {
        if (batteryDisabledPlaceholder) batteryDisabledPlaceholder.style.display = "none";
        if (batteryContainer) batteryContainer.style.display = "block";
        if (storeCityName) storeCityName.textContent = cityText.toUpperCase();
        if (cityWarning) {
            cityWarning.innerHTML = "<strong>*Daftar harga aki berdasarkan wilayah " + cityText + " yang dipilih.</strong>";
        }
    }
};

citySelect?.addEventListener("change", updateCityState);

const userCart = new Map();
window.userCart = userCart;
const cartItemsContainer = document.querySelector("[data-cart-items]");
const cartEmpty = document.querySelector("[data-cart-empty]");
const cartCount = document.querySelector("[data-cart-count]");
const cartSubtotal = document.querySelector("[data-cart-subtotal]");
const cartTotal = document.querySelector("[data-cart-total]");

const formatRupiah = (number) => {
    return "Rp " + Number(number).toLocaleString("id-ID");
};

const renderUserCart = () => {
    if (!cartItemsContainer || !cartEmpty) return;

    if (!userCart.size) {
        cartItemsContainer.innerHTML = cartEmpty.outerHTML;
    } else {
        cartItemsContainer.innerHTML = Array.from(userCart.values())
            .map(
                (item) =>
                    '<div class="user-cart-line">' +
                    '<div class="user-cart-line__copy">' +
                    '<strong>' + item.name + '</strong>' +
                    '<small>' + item.quantity + ' unit × ' + formatRupiah(item.price) + '</small>' +
                    '</div>' +
                    '<div class="user-cart-line__right">' +
                    '<strong class="user-cart-line__price">' + formatRupiah(item.price * item.quantity) + '</strong>' +
                    '<button type="button" class="user-cart-delete-btn" data-delete-item="' + item.name + '">Hapus</button>' +
                    '</div>' +
                    '</div>',
            )
            .join("");
    }

    const totalQuantity = Array.from(userCart.values()).reduce(
        (total, item) => total + item.quantity,
        0,
    );

    const subtotal = Array.from(userCart.values()).reduce(
        (total, item) => total + item.price * item.quantity,
        0,
    );

    // Get pickup fee from localStorage for total calculation
    const pickupFee = parseInt(localStorage.getItem("pickup_fee") || "0");
    const selectedMethod = document.querySelector('input[name="delivery_method"]:checked')?.value;
    const effectiveFee = (selectedMethod === "courier") ? pickupFee : 0;

    if (cartCount) cartCount.textContent = totalQuantity;
    if (cartSubtotal) cartSubtotal.textContent = userCart.size ? formatRupiah(subtotal) : "—";
    if (cartTotal) cartTotal.textContent = userCart.size ? formatRupiah(subtotal + effectiveFee) : "—";

    // Update pickup fee label
    const pickupLabel = document.getElementById("user-pickup-fee-label") || document.querySelector("[data-cart-pickup]");
    if (pickupLabel && selectedMethod === "courier" && effectiveFee > 0) {
        pickupLabel.textContent = formatRupiah(effectiveFee);
    }

    cartItemsContainer.querySelectorAll("[data-delete-item]").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const key = e.currentTarget.dataset.deleteItem;
            if (key && userCart.has(key)) {
                userCart.delete(key);
                renderUserCart();
            }
        });
    });
};

// Expose renderUserCart globally for user-api.js
window.renderUserCart = renderUserCart;

// Product card events are now bound dynamically by user-api.js after API load
// No static binding needed here since cards are rendered from API

document.querySelectorAll("[data-pickup-method]").forEach((radio) => {
    radio.addEventListener("change", () => {
        document
            .querySelectorAll(".user-radio-card")
            .forEach((card) =>
                card.classList.toggle(
                    "is-selected",
                    card.contains(radio) && radio.checked,
                ),
            );
        const pickupFee = document.querySelector("[data-cart-pickup]");
        if (pickupFee)
            pickupFee.textContent =
                radio.value === "courier"
                    ? "Dihitung berdasarkan jarak"
                    : "Gratis";

        // Re-render cart to update total with/without pickup fee
        renderUserCart();
    });
});

// Submission handled entirely by user-api.js with validation logic

document.querySelectorAll("[data-modal-close]").forEach((closeButton) => {
    closeButton.addEventListener("click", () => {
        const modal = document.querySelector("[data-identity-modal]");
        if (modal) modal.hidden = true;
        document.body.classList.remove("overflow-hidden");
    });
});

const receiptBadge = document.querySelector("[data-receipt-badge]");
const proofSection = document.querySelector("[data-proof-section]");

document.querySelectorAll("[data-receipt-status]").forEach((button) => {
    button.addEventListener("click", () => {
        const status = button.dataset.receiptStatus;
        document
            .querySelectorAll("[data-receipt-status]")
            .forEach((item) =>
                item.classList.toggle("is-active", item === button),
            );
        receiptBadge?.classList.toggle(
            "user-receipt__status--paid",
            status === "paid",
        );
        receiptBadge?.classList.toggle(
            "user-receipt__status--unpaid",
            status !== "paid",
        );
        if (receiptBadge)
            receiptBadge.textContent = status === "paid" ? "PAID" : "UNPAID";
        if (proofSection) proofSection.hidden = status !== "paid";
    });
});

updateCityState();
renderUserCart();

