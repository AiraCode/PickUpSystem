<header class="user-header {{ $headerClass ?? '' }}" data-user-header>
    <div class="user-header__inner">
        <a href="/user" class="user-brand" aria-label="Pick Up System user">
            <span class="user-brand__mark" role="img" aria-label="Indoprima Group logo not found">
                <span class="user-brand__mark-red"></span>
                <span class="user-brand__mark-blue"></span>
            </span>
            <span class="user-brand__copy">
                <strong>Indoprima Group</strong>
                <small>Pick Up System</small>
            </span>
        </a>

        <button type="button" class="user-menu-toggle" data-user-menu-toggle aria-expanded="false" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
        </button>

        <nav class="user-nav" data-user-menu aria-label="Navigasi user">
            <a href="/user" data-user-nav-link>Home</a>
            <a href="/user#cara-menjual" data-user-nav-link>Cara Menjual</a>
            <a href="/user#daftar-harga" data-user-nav-link>Daftar Harga</a>
            <a href="/user#faq" data-user-nav-link>FAQ</a>
            <a href="/user#hubungi-kami" data-user-nav-link>Hubungi Kami</a>
        </nav>

        <div class="user-header__actions">
            <button type="button" class="user-language" aria-label="Pilih bahasa">
                <span class="is-active">ID</span>
                <span>|</span>
                <span>EN</span>
            </button>
            <a class="user-header__cta" href="/user#daftar-harga">Jual Aki</a>
        </div>
    </div>
</header>

