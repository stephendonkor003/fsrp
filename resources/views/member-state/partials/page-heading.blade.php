@php
    $headingId = $headingId ?? 'member-state-page-title';
    $headingEyebrow = $headingEyebrow ?? null;
    $headingDescription = $headingDescription ?? null;
    $headingBadge = $headingBadge ?? null;
    $headingBadgeIcon = $headingBadgeIcon ?? 'feather-layers';
    $headingActionRoute = $headingActionRoute ?? null;
    $headingActionLabel = $headingActionLabel ?? null;
    $headingActionIcon = $headingActionIcon ?? 'feather-arrow-right';
@endphp

<section class="ms-page-heading" aria-labelledby="{{ $headingId }}">
    <div class="ms-page-heading-copy">
        @if ($headingEyebrow)
            <span class="ms-section-kicker">{{ $headingEyebrow }}</span>
        @endif

        <h1 id="{{ $headingId }}">{{ $headingTitle }}</h1>

        @if ($headingDescription)
            <p>{{ $headingDescription }}</p>
        @endif
    </div>

    @if ($headingBadge || ($headingActionRoute && $headingActionLabel))
        <div class="ms-page-heading-actions">
            @if ($headingBadge)
                <span class="ms-page-badge">
                    <i class="{{ $headingBadgeIcon }}" aria-hidden="true"></i>
                    {{ $headingBadge }}
                </span>
            @endif

            @if ($headingActionRoute && $headingActionLabel)
                <a href="{{ route($headingActionRoute) }}" class="ms-page-heading-action">
                    <span>{{ $headingActionLabel }}</span>
                    <i class="{{ $headingActionIcon }}" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    @endif
</section>
