@php
    $portalRouteName = request()->route()?->getName();
    $isReportingSection = request()->routeIs('member-state.reporting.show');
    $reportingSectionLetter = $reportingSection['letter'] ?? null;
    $reportingSectionTitle = $reportingSection['title'] ?? null;

    $portalCurrentLabel = match (true) {
        request()->routeIs('member-state.reporting.index') => 'Submit data',
        $isReportingSection && $reportingSectionLetter => 'Section ' . $reportingSectionLetter,
        $isReportingSection => \Illuminate\Support\Str::headline((string) request()->route('section')),
        request()->routeIs('member-state.comparisons.*') => 'Performance',
        request()->routeIs('member-state.communications.*') => 'Messages',
        request()->routeIs('member-state.national-data.*') => 'Documents and raw data',
        request()->routeIs('member-state.questions.*') => 'Help and feedback',
        request()->routeIs('member-state.commodities.*') => 'Commodity reporting',
        request()->routeIs('member-state.treaties.*') => 'Treaties and agreements',
        default => \Illuminate\Support\Str::headline((string) $portalRouteName),
    };

    $portalBackRoute = $isReportingSection
        ? route('member-state.reporting.index')
        : route('member-state.dashboard');
    $portalBackLabel = $isReportingSection ? 'Back to Submit data' : 'Back to Dashboard';
@endphp

<nav class="ms-route-nav" aria-label="Page navigation">
    <div class="ms-route-nav-inner">
        <a href="{{ $portalBackRoute }}" class="ms-route-back">
            <i class="feather-arrow-left" aria-hidden="true"></i>
            <span>{{ $portalBackLabel }}</span>
        </a>

        <ol class="ms-breadcrumbs ms-route-breadcrumb" aria-label="Breadcrumb">
            <li>
                <a href="{{ route('member-state.dashboard') }}">Dashboard</a>
            </li>

            @if ($isReportingSection)
                <li class="ms-route-separator" aria-hidden="true">
                    <i class="feather-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('member-state.reporting.index') }}">Submit data</a>
                </li>
            @endif

            <li class="ms-route-separator" aria-hidden="true">
                <i class="feather-chevron-right"></i>
            </li>
            <li class="ms-route-current" aria-current="page" title="{{ $reportingSectionTitle ?: $portalCurrentLabel }}">
                {{ $portalCurrentLabel }}
            </li>
        </ol>
    </div>
</nav>
