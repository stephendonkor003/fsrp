@props([
    'active' => '',
    'languageStyle' => 'public',
    'contactHref' => '#contact',
])

@php
    $active = (string) $active;
    $programActive = in_array($active, ['programs', 'events', 'careers'], true);
    $analyticsActive = in_array($active, ['analytics', 'impact', 'commodities', 'indicators', 'treaties'], true);
    $procurementActive = $active === 'procurement';
@endphp

<div class="mobile-nav-overlay" id="navOverlay" onclick="closeMobileNav()"></div>

<nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
    <div class="mobile-nav-header">
        <img src="{{ asset('assets/images/au.png') }}" alt="FSRP">
        <button type="button" class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Close menu">&times;</button>
    </div>

    <a href="{{ route('landing.index') }}" class="{{ $active === 'home' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('navigation.home') }}</a>

    <button type="button" class="mobile-dropdown-toggle {{ $programActive ? 'open' : '' }}" onclick="toggleMobileDropdown(this)" aria-expanded="{{ $programActive ? 'true' : 'false' }}">
        {{ __('public_pages.programs') }} <span class="mobile-dropdown-arrow">▾</span>
    </button>
    <div class="mobile-dropdown-items {{ $programActive ? 'open' : '' }}" aria-hidden="{{ $programActive ? 'false' : 'true' }}">
        <a href="{{ route('events') }}" class="{{ $active === 'events' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('landing.events_webinars') }}</a>
        <a href="{{ route('careers.index') }}" class="{{ $active === 'careers' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('navigation.careers') }}</a>
    </div>

    <button type="button" class="mobile-dropdown-toggle {{ $analyticsActive ? 'open' : '' }}" onclick="toggleMobileDropdown(this)" aria-expanded="{{ $analyticsActive ? 'true' : 'false' }}">
        {{ __('public_pages.food_security_analytics') }} <span class="mobile-dropdown-arrow">▾</span>
    </button>
    <div class="mobile-dropdown-items {{ $analyticsActive ? 'open' : '' }}" aria-hidden="{{ $analyticsActive ? 'false' : 'true' }}">
        <a href="{{ route('impact.map') }}" class="{{ $active === 'impact' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('navigation.impact_map') }}</a>
        <a href="{{ route('food-security.commodities') }}" class="{{ $active === 'commodities' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('navigation.food_commodities_map') }}</a>
        <a href="{{ route('world.indicators.performance') }}" class="{{ $active === 'indicators' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('navigation.world_indicators_performance') }}</a>
    </div>

    <a href="{{ route('news.index') }}" class="{{ $active === 'news' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('public_pages.news_updates') }}</a>
    <a href="{{ route('gallery.index') }}" class="{{ $active === 'gallery' ? 'active' : '' }}" onclick="closeMobileNav()">{{ __('public_pages.gallery') }}</a>
    <a href="{{ $contactHref }}" onclick="closeMobileNav()">{{ __('navigation.contact') }}</a>

    <div class="mobile-nav-actions">
        <a href="{{ route('public.procurement.index') }}" class="btn btn-primary {{ $procurementActive ? 'active' : '' }}">{{ __('public_pages.policy_programs_research') }}</a>
        <a href="{{ route('login') }}" class="btn btn-login">{{ __('navigation.login') }}</a>
        <x-language-selector :style="$languageStyle . '-mobile'" />
    </div>
</nav>

<header class="navbar public-site-header" role="banner">
    <a href="{{ route('landing.index') }}" class="logo" aria-label="FSRP Home">
        <img src="{{ asset('assets/images/au.png') }}" alt="Western and Central Africa - West Africa Food System Resilience Program (FSRP)" class="logo-sm">
    </a>

    <nav class="nav-links" aria-label="Main navigation">
        <a href="{{ route('landing.index') }}" class="{{ $active === 'home' ? 'active' : '' }}">{{ __('navigation.home') }}</a>

        <div class="has-dropdown">
            <a href="#" class="{{ $programActive ? 'active' : '' }}">{{ __('public_pages.programs') }}</a>
            <ul class="nav-dropdown">
                <li><a href="{{ route('events') }}" class="{{ $active === 'events' ? 'active' : '' }}">{{ __('landing.events_webinars') }}</a></li>
                <li><a href="{{ route('careers.index') }}" class="{{ $active === 'careers' ? 'active' : '' }}">{{ __('navigation.careers') }}</a></li>
            </ul>
        </div>

        <div class="has-dropdown">
            <a href="#" class="{{ $analyticsActive ? 'active' : '' }}">{{ __('public_pages.food_security_analytics') }}</a>
            <ul class="nav-dropdown">
                <li><a href="{{ route('impact.map') }}" class="{{ $active === 'impact' ? 'active' : '' }}">{{ __('navigation.impact_map') }}</a></li>
                <li><a href="{{ route('food-security.commodities') }}" class="{{ $active === 'commodities' ? 'active' : '' }}">{{ __('navigation.food_commodities_map') }}</a></li>
                <li><a href="{{ route('world.indicators.performance') }}" class="{{ $active === 'indicators' ? 'active' : '' }}">{{ __('navigation.world_indicators_performance') }}</a></li>
            </ul>
        </div>

        <a href="{{ route('news.index') }}" class="{{ $active === 'news' ? 'active' : '' }}">{{ __('public_pages.news_updates') }}</a>
        <a href="{{ route('gallery.index') }}" class="{{ $active === 'gallery' ? 'active' : '' }}">{{ __('public_pages.gallery') }}</a>
        <a href="{{ $contactHref }}">{{ __('navigation.contact') }}</a>
    </nav>

    <div class="nav-actions">
        <a href="{{ route('public.procurement.index') }}" class="btn btn-primary {{ $procurementActive ? 'active' : '' }}">{{ __('public_pages.policy_programs_research') }}</a>
        <a href="{{ route('login') }}" class="btn btn-login">{{ __('navigation.login') }}</a>
        <x-language-selector :style="$languageStyle" />
    </div>

    <button type="button" class="hamburger-btn" id="hamburgerBtn" onclick="openMobileNav()" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>
</header>

@once
    <style>
        .public-site-header.navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1200;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.9rem 4%;
            background: var(--au-green);
            box-shadow: 0 2px 16px rgba(0,0,0,0.25);
        }

        .public-site-header .nav-actions .btn.active {
            background: var(--gold);
            color: #000;
        }

        .mobile-nav a.active,
        .mobile-dropdown-items a.active {
            color: var(--gold);
        }
    </style>

    <script>
        function openMobileNav() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navOverlay');
            const btn = document.getElementById('hamburgerBtn');
            if (!nav || !overlay || !btn) return;
            nav.classList.add('open');
            overlay.style.display = 'block';
            requestAnimationFrame(() => overlay.classList.add('visible'));
            btn.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navOverlay');
            const btn = document.getElementById('hamburgerBtn');
            if (!nav || !overlay || !btn) return;
            nav.classList.remove('open');
            overlay.classList.remove('visible');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            setTimeout(() => { overlay.style.display = 'none'; }, 300);
        }

        function toggleMobileDropdown(trigger) {
            const items = trigger.nextElementSibling;
            const isOpen = items && items.classList.contains('open');
            document.querySelectorAll('.mobile-dropdown-items.open').forEach(el => {
                if (el !== items) {
                    el.classList.remove('open');
                    el.setAttribute('aria-hidden', 'true');
                }
            });
            document.querySelectorAll('.mobile-dropdown-toggle.open').forEach(el => {
                if (el !== trigger) {
                    el.classList.remove('open');
                    el.setAttribute('aria-expanded', 'false');
                }
            });
            if (!items) return;
            items.classList.toggle('open', !isOpen);
            items.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
            trigger.classList.toggle('open', !isOpen);
            trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        }
    </script>
@endonce
