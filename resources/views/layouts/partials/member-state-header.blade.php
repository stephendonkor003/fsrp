@php
    $portalUser = auth()->user();
    $portalMemberState = $portalUser?->memberState;
    $portalFlagUrl = $portalMemberState?->flag_url ?: asset('assets/images/au.png');
    $portalDisplayName = $portalUser?->name ?: 'Member State user';
    $portalInitial = mb_strtoupper(mb_substr(trim($portalDisplayName), 0, 1));
    $portalNavItems = [
        [
            'label' => 'Dashboard',
            'route' => 'member-state.dashboard',
            'active' => 'member-state.dashboard',
            'icon' => 'feather-grid',
        ],
        [
            'label' => 'Submit data',
            'route' => 'member-state.reporting.index',
            'active' => 'member-state.reporting.*',
            'icon' => 'feather-edit-3',
        ],
        [
            'label' => 'Performance',
            'route' => 'member-state.comparisons.index',
            'active' => 'member-state.comparisons.*',
            'icon' => 'feather-bar-chart-2',
        ],
        [
            'label' => 'Messages',
            'route' => 'member-state.communications.index',
            'active' => 'member-state.communications.*',
            'icon' => 'feather-bell',
        ],
        [
            'label' => 'Records',
            'route' => 'member-state.national-data.index',
            'active' => 'member-state.national-data.*',
            'icon' => 'feather-folder',
        ],
    ];
@endphp

<header class="ms-portal-header">
    <div class="ms-portal-header-inner">
        <a href="{{ route('member-state.dashboard') }}" class="ms-portal-brand" aria-label="Member State Reporting Portal home">
            <span class="ms-brand-mark ms-brand-country-flag">
                <img src="{{ $portalFlagUrl }}" alt="{{ $portalMemberState?->name ?? 'Member State' }} flag">
            </span>
            <span class="ms-brand-copy">
                <small>Food Systems Resilience Programme</small>
                <strong>Member State Portal</strong>
            </span>
        </a>

        <nav class="ms-portal-actions ms-primary-nav" aria-label="Member State portal navigation">
            @foreach ($portalNavItems as $portalNavItem)
                @php($portalNavActive = request()->routeIs($portalNavItem['active']))
                <a href="{{ route($portalNavItem['route']) }}"
                    class="ms-header-home ms-nav-link ms-primary-nav-link {{ $portalNavActive ? 'active' : '' }}"
                    aria-label="{{ $portalNavItem['label'] }}"
                    title="{{ $portalNavItem['label'] }}"
                    @if ($portalNavActive) aria-current="page" @endif>
                    <i class="{{ $portalNavItem['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $portalNavItem['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="ms-portal-user-tools">
            <span class="ms-portal-security" title="Your portal session is protected">
                <i class="feather-shield" aria-hidden="true"></i>
                <span>Secure</span>
            </span>

            <div class="ms-header-user ms-user-identity" title="{{ $portalUser?->email }}">
                <span class="ms-user-avatar" aria-hidden="true">{{ $portalInitial }}</span>
                <span class="ms-user-copy">
                    <strong>{{ $portalDisplayName }}</strong>
                    <small>{{ $portalMemberState?->name ?? 'Member State' }}</small>
                </span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="ms-signout-form">
                @csrf
                <button type="submit" class="ms-logout-button" aria-label="Sign out" title="Sign out">
                    <i class="feather-log-out" aria-hidden="true"></i>
                    <span class="ms-logout-label">Sign out</span>
                </button>
            </form>
        </div>
    </div>
</header>
