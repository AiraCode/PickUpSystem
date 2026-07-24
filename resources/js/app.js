import.meta.glob([
    '../img/**',
]);

const sidebar = document.querySelector('#admin-sidebar');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
const sidebarOpen = document.querySelector('[data-sidebar-open]');
const sidebarClose = document.querySelector('[data-sidebar-close]');

const setSidebar = (isOpen) => {
    sidebar?.classList.toggle('is-open', isOpen);
    sidebarOverlay?.classList.toggle('is-visible', isOpen);
    document.body.classList.toggle('overflow-hidden', isOpen);
};

sidebarOpen?.addEventListener('click', () => setSidebar(true));
sidebarClose?.addEventListener('click', () => setSidebar(false));
sidebarOverlay?.addEventListener('click', () => setSidebar(false));

document.querySelectorAll('[data-nav-link]').forEach((link) => {
    link.addEventListener('click', () => {
        document.querySelectorAll('[data-nav-link]').forEach((item) => item.classList.remove('is-active'));
        link.classList.add('is-active');
        setSidebar(false);
    });
});

const profileToggle = document.querySelector('[data-profile-toggle]');
const profileMenu = document.querySelector('[data-profile-menu]');

profileToggle?.addEventListener('click', () => {
    const isOpen = profileToggle.getAttribute('aria-expanded') === 'true';
    profileToggle.setAttribute('aria-expanded', String(!isOpen));
    if (profileMenu) profileMenu.hidden = isOpen;
});

document.addEventListener('click', (event) => {
    if (!profileToggle || !profileMenu || profileMenu.hidden) return;
    if (!profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
        profileToggle.setAttribute('aria-expanded', 'false');
        profileMenu.hidden = true;
    }
});
const userHeaders = document.querySelectorAll('[data-user-header]');

userHeaders.forEach((header) => {
    if (header.classList.contains('user-header--solid')) return;
    const updateHeader = () => header.classList.toggle('is-scrolled', window.scrollY > 24);
    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();
});

const userMenuToggle = document.querySelector('[data-user-menu-toggle]');
const userMenu = document.querySelector('[data-user-menu]');

userMenuToggle?.addEventListener('click', () => {
    const isOpen = userMenuToggle.getAttribute('aria-expanded') === 'true';
    userMenuToggle.setAttribute('aria-expanded', String(!isOpen));
    userMenu?.classList.toggle('is-open', !isOpen);
});

document.querySelectorAll('[data-user-nav-link]').forEach((link) => {
    link.addEventListener('click', () => {
        userMenuToggle?.setAttribute('aria-expanded', 'false');
        userMenu?.classList.remove('is-open');
    });
});

const citySelect = document.querySelector('[data-city-select]');
const cityStatus = document.querySelector('[data-city-status]');

citySelect?.addEventListener('change', () => {
    const city = citySelect.options[citySelect.selectedIndex]?.text || 'wilayah terpilih';
    if (cityStatus) cityStatus.textContent = 'Data harga ' + city + ' mengikuti data yang terhubung pada sistem.';
});

const userCart = new Map();
const cartItemsContainer = document.querySelector('[data-cart-items]');
const cartEmpty = document.querySelector('[data-cart-empty]');
const cartCount = document.querySelector('[data-cart-count]');
const cartSubtotal = document.querySelector('[data-cart-subtotal]');
const cartTotal = document.querySelector('[data-cart-total]');

const renderUserCart = () => {
    if (!cartItemsContainer || !cartEmpty) return;

    if (!userCart.size) {
        cartItemsContainer.innerHTML = cartEmpty.outerHTML;
    } else {
        cartItemsContainer.innerHTML = Array.from(userCart.values()).map((item) =>
            '<div class="user-cart-line">' +
            '<span class="user-cart-line__copy"><strong>' + item.name + '</strong><small>' + item.brand + ' · harga belum tersedia</small></span>' +
            '<span class="user-cart-line__qty">' + item.quantity + ' unit</span>' +
            '</div>'
        ).join('');
    }

    const quantity = Array.from(userCart.values()).reduce((total, item) => total + item.quantity, 0);
    if (cartCount) cartCount.textContent = quantity;
    if (cartSubtotal) cartSubtotal.textContent = userCart.size ? 'Menunggu data harga' : '—';
    if (cartTotal) cartTotal.textContent = userCart.size ? 'Menunggu data harga' : '—';
};

document.querySelectorAll('[data-product-card]').forEach((card) => {
    const quantityInput = card.querySelector('[data-quantity]');
    const minusButton = card.querySelector('[data-quantity-minus]');
    const plusButton = card.querySelector('[data-quantity-plus]');
    const addButton = card.querySelector('[data-add-to-cart]');

    const setQuantity = (value) => {
        if (quantityInput) quantityInput.value = Math.min(99, Math.max(1, Number(value) || 1));
    };

    minusButton?.addEventListener('click', () => setQuantity(Number(quantityInput?.value) - 1));
    plusButton?.addEventListener('click', () => setQuantity(Number(quantityInput?.value) + 1));

    addButton?.addEventListener('click', () => {
        const name = card.dataset.productName || 'Aki';
        const brand = card.dataset.productBrand || 'Indoprima';
        const quantity = Math.min(99, Math.max(1, Number(quantityInput?.value) || 1));
        userCart.set(name, { name, brand, quantity });
        renderUserCart();
        const originalText = addButton.textContent;
        addButton.textContent = 'Ditambahkan';
        window.setTimeout(() => { addButton.textContent = originalText; }, 1000);
    });
});

document.querySelectorAll('[data-pickup-method]').forEach((radio) => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.user-radio-card').forEach((card) => card.classList.toggle('is-selected', card.contains(radio) && radio.checked));
        const pickupFee = document.querySelector('[data-cart-pickup]');
        if (pickupFee) pickupFee.textContent = radio.value === 'courier' ? 'Dihitung berdasarkan jarak' : 'Gratis';
    });
});

const identityForm = document.querySelector('[data-identity-form]');
const identityModal = document.querySelector('[data-identity-modal]');

identityForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    if (identityModal) {
        identityModal.hidden = false;
        document.body.classList.add('overflow-hidden');
    }
});

document.querySelectorAll('[data-modal-close]').forEach((closeButton) => {
    closeButton.addEventListener('click', () => {
        if (identityModal) identityModal.hidden = true;
        document.body.classList.remove('overflow-hidden');
    });
});

const receiptBadge = document.querySelector('[data-receipt-badge]');
const proofSection = document.querySelector('[data-proof-section]');

document.querySelectorAll('[data-receipt-status]').forEach((button) => {
    button.addEventListener('click', () => {
        const status = button.dataset.receiptStatus;
        document.querySelectorAll('[data-receipt-status]').forEach((item) => item.classList.toggle('is-active', item === button));
        receiptBadge?.classList.toggle('user-receipt__status--paid', status === 'paid');
        receiptBadge?.classList.toggle('user-receipt__status--unpaid', status !== 'paid');
        if (receiptBadge) receiptBadge.textContent = status === 'paid' ? 'PAID' : 'UNPAID';
        if (proofSection) proofSection.hidden = status !== 'paid';
    });
});

renderUserCart();
