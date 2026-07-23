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
