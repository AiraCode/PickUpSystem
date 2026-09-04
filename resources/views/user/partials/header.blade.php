<header class="user-header {{ $headerClass ?? '' }}" data-user-header>
    <div class="user-header__inner">
        <a href="/user" class="user-brand reveal-hidden delay-0" aria-label="One Stop Solution user">
            <img src="{{ asset('img/Logo_user-removebg-preview.png') }}" alt="AKIKU"
                style="height: 140px; width: auto; object-fit: contain;">
        </a>

        @unless($hideNav ?? false)
            <button type="button" class="user-menu-toggle" data-user-menu-toggle aria-expanded="false"
                aria-label="Buka menu">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <nav class="user-nav reveal-hidden delay-200" data-user-menu aria-label="Navigasi user">
                <a href="/user" data-user-nav-link>Home</a>
                <a href="/user#faq" data-user-nav-link>FAQ</a>
                <a href="/user#hubungi-kami" data-user-nav-link>Hubungi Kami</a>
                <div class="user-nav__language">
                    <div class="lang-switch" data-lang-switch>
                        <button type="button" class="lang-btn is-active" data-lang-btn="id" aria-label="Bahasa Indonesia">ID</button>
                        <span class="lang-divider">|</span>
                        <button type="button" class="lang-btn" data-lang-btn="en" aria-label="English Language">EN</button>
                    </div>
                </div>
            </nav>
        @endunless

        <div class="user-header__actions reveal-hidden delay-200" style="display: flex; align-items: center; gap: 12px;">
            <div class="lang-switch" data-lang-switch>
                <button type="button" class="lang-btn is-active" data-lang-btn="id" aria-label="Bahasa Indonesia">ID</button>
                <span class="lang-divider">|</span>
                <button type="button" class="lang-btn" data-lang-btn="en" aria-label="English Language">EN</button>
            </div>
        </div>
    </div>
</header>