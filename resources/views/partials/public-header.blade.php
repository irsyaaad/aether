<style>
  :root {
    --aether-blue: #2B8BD1;
    --aether-deep: #1768B5;
    --aether-navy: #102654;
    --aether-orange: #F29B3A;
    --aether-gold: #F6C75A;
  }
  .pub-header-wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 24px;
    box-sizing: border-box;
  }
  .pub-top-testi {
    background: linear-gradient(90deg, #2B8BD1, #58B7EA 55%, #F2A23A) !important;
    color: #fff !important;
    font-size: 13px;
    overflow: hidden;
    line-height: 1.4;
  }
  .pub-top-testi .pub-header-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 38px;
    white-space: nowrap;
  }
  .pub-top-testi .pub-tag {
    background: #FFF5D8 !important;
    color: #B36A12 !important;
    padding: 4px 14px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 11px;
    letter-spacing: .04em;
    flex-shrink: 0;
  }
  .pub-top-testi .pub-scroll {
    overflow: hidden;
    flex: 1;
  }
  .pub-top-testi .pub-move {
    display: inline-block;
    padding-left: 100%;
    animation: pubMarquee 28s linear infinite;
  }
  @keyframes pubMarquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }

  .pub-main-header {
    background: rgba(255, 255, 255, .96) !important;
    position: sticky;
    top: 0;
    z-index: 50;
    border-bottom: 1px solid #E5EDF5 !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
  }
  .pub-nav1 {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 14px 0 10px;
  }
  .pub-brand-link {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    flex-shrink: 0;
  }
  .pub-brand-logo-img {
    height: 48px;
    width: auto;
    object-fit: contain;
    display: block;
    transition: transform 0.2s ease;
  }
  .pub-brand-link:hover .pub-brand-logo-img {
    transform: scale(1.02);
  }
  .pub-search {
    flex: 1;
    display: flex;
    gap: 10px;
    max-width: 540px;
    margin: 0 auto;
  }
  .pub-search input {
    flex: 1;
    border: 1.5px solid #CBD5E1;
    border-radius: 999px;
    height: 42px;
    padding: 0 18px;
    font-size: 14px;
    outline: none;
    background: #F8FAFC;
    font-family: inherit;
    color: #1E293B;
    transition: all 0.2s ease;
  }
  .pub-search input:focus {
    border-color: #2B8BD1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(43, 139, 209, 0.15);
  }
  .pub-search button {
    height: 42px;
    padding: 0 22px;
    border: none;
    border-radius: 999px;
    background: #102654;
    color: #fff;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s ease;
    font-family: inherit;
  }
  .pub-search button:hover {
    background: #1b3591;
  }
  .pub-auth-actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }
  .pub-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 999px;
    font-weight: 800 !important;
    text-decoration: none !important;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .pub-btn-yellow {
    background: linear-gradient(135deg, #FFD978, #F29B3A) !important;
    color: #26384A !important;
    padding: 9px 18px;
    font-size: 13px;
    box-shadow: 0 4px 12px rgba(242, 155, 58, 0.25);
  }
  .pub-btn-yellow:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(242, 155, 58, 0.35);
  }
  .pub-btn-white {
    background: #fff !important;
    color: #1F6FAF !important;
    border: 1px solid rgba(31, 111, 175, .15) !important;
    padding: 8px 18px;
    font-size: 13px;
  }
  .pub-btn-white:hover {
    background: #F8FAFC !important;
    border-color: rgba(31, 111, 175, .3) !important;
  }

  .pub-nav2 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 12px 0 14px;
    font-size: 14px;
    border-top: 1px solid #F1F5F9;
    position: relative;
    box-sizing: border-box;
  }
  .pub-nav2 > a {
    font-weight: 600;
    color: #334155;
    transition: color 0.2s ease;
    text-decoration: none !important;
    padding: 6px 2px;
    white-space: nowrap;
    position: relative;
  }
  .pub-nav2 > a:hover {
    color: var(--aether-blue);
  }
  .pub-nav2 > a.active {
    color: var(--aether-deep) !important;
    font-weight: 800 !important;
  }
  .pub-nav2 > a.active::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 0;
    right: 0;
    height: 2.5px;
    background: #2B8BD1;
    border-radius: 2px;
  }

  .pub-nav2-dropdown {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding: 4px 0;
  }
  .pub-nav2-dropdown-toggle {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
    color: #334155;
    background: transparent;
    border: none;
    padding: 6px 6px;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    position: relative;
  }
  .pub-nav2-dropdown-toggle:hover,
  .pub-nav2-dropdown.is-open .pub-nav2-dropdown-toggle {
    color: var(--aether-blue);
    background: rgba(43, 139, 209, 0.08);
  }
  .pub-nav2-dropdown.active .pub-nav2-dropdown-toggle {
    color: var(--aether-deep) !important;
    font-weight: 800 !important;
  }
  .pub-nav2-dropdown.active .pub-nav2-dropdown-toggle::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 4px;
    right: 4px;
    height: 2.5px;
    background: #2B8BD1;
    border-radius: 2px;
  }
  .pub-nav2-dropdown-toggle svg {
    transition: transform 0.2s ease;
  }
  .pub-nav2-dropdown:hover .pub-nav2-dropdown-toggle svg,
  .pub-nav2-dropdown.is-open .pub-nav2-dropdown-toggle svg {
    transform: rotate(180deg);
  }

  /* Dropdown Menu with zero-gap hitbox bridge */
  .pub-nav2-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 2px;
    min-width: 275px;
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
    padding: 8px 0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(6px);
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
    pointer-events: none;
    z-index: 1000;
  }
  .pub-nav2-dropdown-menu::before {
    content: "";
    position: absolute;
    top: -14px;
    left: 0;
    right: 0;
    height: 16px;
    background: transparent;
  }
  .pub-nav2-dropdown:hover .pub-nav2-dropdown-menu,
  .pub-nav2-dropdown.is-open .pub-nav2-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
  }
  .pub-nav2-dropdown-menu a {
    display: block;
    padding: 10px 18px;
    font-size: 13.5px;
    color: #334155;
    font-weight: 500;
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none !important;
  }
  .pub-nav2-dropdown-menu a:hover {
    background: #F1F5F9;
    color: var(--aether-blue);
    padding-left: 22px;
  }
  .pub-nav2-dropdown-menu a.active {
    background: #EFF6FF !important;
    color: var(--aether-deep) !important;
    font-weight: 700 !important;
  }

  @media(max-width: 980px) {
    .pub-search { max-width: none; }
    .pub-nav1 { flex-wrap: wrap; }
    .pub-auth-actions { margin-left: 0; }
    .pub-nav2 { flex-wrap: wrap; justify-content: center; gap: 12px 18px; }
  }
  @media(max-width: 640px) {
    .pub-brand-logo-img { height: 38px; }
    .pub-search { order: 3; width: 100%; }
    .pub-nav2 { overflow: visible; flex-wrap: wrap; gap: 8px 12px; justify-content: flex-start; padding-bottom: 12px; }
  }
</style>

<!-- TOP TICKER -->
<div class="pub-top-testi">
  <div class="pub-header-wrap">
    <span class="pub-tag">★ TESTIMONI</span>
    <div class="pub-scroll">
      <div class="pub-move">
        “Pendampingannya sangat membantu memahami alur riset.” • “Penjelasan analisis data lebih mudah dipahami.” • “Diskusinya enak, tidak kaku, dan tetap profesional.” • “Aether bantu olah sampai paham untuk skripsi, tesis, dan publikasi ilmiah.” • “Dari data mentah hingga interpretasi siap submit.” •
      </div>
    </div>
  </div>
</div>

<!-- HEADER -->
<header class="pub-main-header">
  <div class="pub-header-wrap pub-nav1">
    <a class="pub-brand-link" href="{{ url('/') }}" aria-label="Aether Data Lab">
      <img src="{{ asset('images/logo_aether.png') }}" alt="Aether Data Lab" class="pub-brand-logo-img">
    </a>

    <form class="pub-search" action="{{ route('landing.berita') }}" method="GET">
      <input name="q" value="{{ request('q') }}" placeholder="Cari: SPSS, SmartPLS, AMOS, NVivo, data panel..." type="text"/>
      <button type="submit">Cari</button>
    </form>

    <div class="pub-auth-actions">
      @auth
        <a href="{{ url('/dashboard') }}" class="pub-btn pub-btn-yellow">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="pub-btn pub-btn-white">Masuk</a>
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="pub-btn pub-btn-yellow">Daftar</a>
        @endif
      @endauth
    </div>
  </div>

  <div class="pub-header-wrap pub-nav2">
    <a href="{{ route('landing.promo') }}" class="{{ request()->routeIs('landing.promo') ? 'active' : '' }}">Promo Program</a>

    <div class="pub-nav2-dropdown {{ request()->routeIs('landing.paket-olah-data', 'landing.jurnal-aether', 'landing.cek-turnitin') ? 'active' : '' }}">
      <button type="button" class="pub-nav2-dropdown-toggle">
        Olah Data &amp; Publikasi
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>
      <div class="pub-nav2-dropdown-menu">
        <a href="{{ route('landing.paket-olah-data') }}" class="{{ request()->routeIs('landing.paket-olah-data') ? 'active' : '' }}">Paket Olah Data</a>
        <a href="{{ route('landing.jurnal-aether') }}" class="{{ request()->routeIs('landing.jurnal-aether') ? 'active' : '' }}">Jurnal Aether</a>
        <a href="{{ route('landing.cek-turnitin') }}" class="{{ request()->routeIs('landing.cek-turnitin') ? 'active' : '' }}">Cek Turnitin</a>
      </div>
    </div>

    <div class="pub-nav2-dropdown {{ request()->routeIs('landing.workshop-pelatihan', 'landing.mentoring-penelitian', 'landing.mentoring-karir', 'landing.mentoring-beasiswa', 'landing.mentoring-lomba') ? 'active' : '' }}">
      <button type="button" class="pub-nav2-dropdown-toggle">
        Class &amp; Programs
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>
      <div class="pub-nav2-dropdown-menu">
        <a href="{{ route('landing.workshop-pelatihan') }}" class="{{ request()->routeIs('landing.workshop-pelatihan') ? 'active' : '' }}">Workshop &amp; Pelatihan</a>
        <a href="{{ route('landing.mentoring-penelitian') }}" class="{{ request()->routeIs('landing.mentoring-penelitian') ? 'active' : '' }}">Mentoring Penelitian (Skripsi, Tesis, Disertasi)</a>
        <a href="{{ route('landing.mentoring-karir') }}" class="{{ request()->routeIs('landing.mentoring-karir') ? 'active' : '' }}">Mentoring Persiapan Karir</a>
        <a href="{{ route('landing.mentoring-beasiswa') }}" class="{{ request()->routeIs('landing.mentoring-beasiswa') ? 'active' : '' }}">Mentoring Beasiswa</a>
        <a href="{{ route('landing.mentoring-lomba') }}" class="{{ request()->routeIs('landing.mentoring-lomba') ? 'active' : '' }}">Mentoring Lomba</a>
      </div>
    </div>

    <a href="{{ route('landing.ai-research') }}" class="{{ request()->routeIs('landing.ai-research') ? 'active' : '' }}">AI Research</a>
    <a href="{{ route('landing.quiz') }}" class="{{ request()->routeIs('landing.quiz') ? 'active' : '' }}">Quiz Aether</a>
    <a href="{{ route('landing.keunggulan') }}" class="{{ request()->routeIs('landing.keunggulan') ? 'active' : '' }}">Keunggulan &amp; Alur</a>
    <a href="{{ route('landing.testimoni') }}" class="{{ request()->routeIs('landing.testimoni') ? 'active' : '' }}">Testimoni</a>
    <a href="{{ route('landing.faq') }}" class="{{ request()->routeIs('landing.faq') ? 'active' : '' }}">FAQ</a>
  </div>
</header>

<script>
  (function() {
    document.querySelectorAll('.pub-nav2-dropdown').forEach(dropdown => {
      const toggle = dropdown.querySelector('.pub-nav2-dropdown-toggle');
      let closeTimer = null;

      function openDropdown() {
        clearTimeout(closeTimer);
        document.querySelectorAll('.pub-nav2-dropdown').forEach(d => {
          if (d !== dropdown) d.classList.remove('is-open');
        });
        dropdown.classList.add('is-open');
      }

      function closeDropdown(delay = 250) {
        clearTimeout(closeTimer);
        closeTimer = setTimeout(() => {
          dropdown.classList.remove('is-open');
        }, delay);
      }

      dropdown.addEventListener('mouseenter', () => openDropdown());
      dropdown.addEventListener('mouseleave', () => closeDropdown(250));

      if (toggle) {
        toggle.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (dropdown.classList.contains('is-open')) {
            dropdown.classList.remove('is-open');
          } else {
            openDropdown();
          }
        });
      }

      const menu = dropdown.querySelector('.pub-nav2-dropdown-menu');
      if (menu) {
        menu.addEventListener('click', (e) => {
          e.stopPropagation();
        });
      }
    });

    document.addEventListener('click', () => {
      document.querySelectorAll('.pub-nav2-dropdown').forEach(d => d.classList.remove('is-open'));
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.pub-nav2-dropdown').forEach(d => d.classList.remove('is-open'));
      }
    });
  })();
</script>
