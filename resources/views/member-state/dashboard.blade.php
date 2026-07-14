@extends('layouts.app')

@section('title', 'Member State Dashboard')

@section('content')
    <main class="ms-reporting-hub ms-dashboard-page" data-card-search>
        @include('member-state.partials.page-heading', [
            'headingId' => 'dashboard-page-title',
            'headingEyebrow' => 'Overview',
            'headingTitle' => 'Your reporting centre',
            'headingDescription' => 'Submit country information, review programme results, follow official messages, and manage supporting records.',
            'headingActionRoute' => 'member-state.reporting.index',
            'headingActionLabel' => 'Start reporting',
        ])

        @include('member-state.partials.card-search', [
            'searchId' => 'dashboard-card-search',
            'searchPlaceholder' => 'Search dashboard items, records, performance, or notifications...',
            'searchCount' => count($portalCards),
            'searchItemLabel' => 'dashboard items',
        ])

        <section class="ms-dashboard-actions" aria-labelledby="dashboard-actions-title">
            <div class="ms-section-heading ms-section-heading-compact">
                <div>
                    <span class="ms-section-kicker">Portal services</span>
                    <h2 id="dashboard-actions-title">Quick access</h2>
                </div>
                <p>Open the tools used for everyday reporting work.</p>
            </div>

            <div class="ms-dashboard-grid" data-search-grid>
                @foreach ($portalCards as $card)
                    <a href="{{ route($card['route']) }}"
                        class="ms-action-card ms-action-card--{{ $card['theme'] }}"
                        data-search-item
                        data-search-text="{{ $card['title'] }} {{ $card['eyebrow'] }} {{ $card['description'] }} {{ $card['keywords'] }}"
                        aria-label="Open {{ $card['title'] }}">
                        <div class="ms-action-art">
                            <span class="ms-action-number">{{ $card['number'] }}</span>
                            <img src="{{ asset($card['image']) }}"
                                alt=""
                                width="768"
                                height="768">
                        </div>
                        <div class="ms-action-body">
                            <span class="ms-action-eyebrow">{{ $card['eyebrow'] }}</span>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['description'] }}</p>
                            <span class="ms-action-open">
                                Open workspace
                                <i class="feather-arrow-right" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            @include('member-state.partials.search-empty', [
                'emptyTitle' => 'No dashboard items found',
                'emptyMessage' => 'Try a different word such as submit, performance, notifications, documents, or data.',
            ])
        </section>

        <aside class="ms-help-strip" aria-labelledby="dashboard-help-title">
            <span class="ms-help-strip-icon" aria-hidden="true">
                <i class="feather-help-circle"></i>
            </span>
            <div class="ms-help-strip-copy">
                <span class="ms-section-kicker">Reporting support</span>
                <h2 id="dashboard-help-title">Need help with a submission?</h2>
                <p>Send a question to the regional reporting team and follow the response from your portal.</p>
            </div>
            <a href="{{ route('member-state.questions.index') }}" class="ms-help-strip-action">
                Get help
                <i class="feather-arrow-right" aria-hidden="true"></i>
            </a>
        </aside>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/member-state-card-search.js') }}" defer></script>
@endpush
