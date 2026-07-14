@extends('layouts.app')

@section('title', 'Submit Information')

@section('content')
    <main class="ms-reporting-hub ms-submit-page" data-card-search>
        <section class="ms-frequency-panel" id="reporting-frequency" aria-labelledby="reporting-frequency-title">
            <div class="ms-frequency-panel-heading">
                <div>
                    <span class="ms-frequency-step">Step 1</span>
                    <h2 id="reporting-frequency-title">Reporting Frequency:</h2>
                    <p>Choose one M&amp;E-configured period before opening the reporting sections.</p>
                </div>
                <span class="ms-frequency-rule">
                    <i class="feather-shield" aria-hidden="true"></i>
                    One country report per period
                </span>
            </div>

            @if (session('success'))
                <div class="ms-frequency-alert ms-frequency-alert--success" role="status">
                    <i class="feather-check-circle" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="ms-frequency-alert ms-frequency-alert--warning" role="alert">
                    <i class="feather-alert-triangle" aria-hidden="true"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="ms-frequency-alert ms-frequency-alert--error" role="alert">
                    <i class="feather-alert-circle" aria-hidden="true"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('member-state.reporting.start') }}" class="ms-frequency-form">
                @csrf
                <fieldset>
                    <legend class="visually-hidden">Select one reporting frequency</legend>
                    <div class="ms-frequency-options">
                        @foreach ($frequencyOptions as $option)
                            @php
                                $cycle = $option['cycle'];
                                $submission = $option['submission'];
                                $isSelected = $cycle && $selectedSubmission?->reporting_cycle_id === $cycle->id;
                                $isAvailable = (bool) $option['available'];
                            @endphp
                            <label class="ms-frequency-option {{ $isAvailable ? '' : 'ms-frequency-option--disabled' }} {{ $isSelected ? 'is-selected' : '' }}">
                                <input type="radio"
                                    name="reporting_cycle_id"
                                    value="{{ $cycle?->id }}"
                                    data-frequency-label="{{ $option['label'] }}"
                                    @checked($cycle && old('reporting_cycle_id', $selectedSubmission?->reporting_cycle_id) === $cycle->id)
                                    @disabled(! $isAvailable)
                                    required>

                                <span class="ms-frequency-check" aria-hidden="true"></span>
                                <span class="ms-frequency-icon" aria-hidden="true">
                                    <i class="{{ $option['icon'] }}"></i>
                                </span>
                                <span class="ms-frequency-copy">
                                    <strong>{{ $option['label'] }}</strong>
                                    <small>{{ $cycle?->display_label ?? 'Not opened by M&E' }}</small>
                                    <span>{{ $option['description'] }}</span>

                                    @if ($submission)
                                        <em class="ms-frequency-state ms-frequency-state--{{ $submission->status }}">
                                            {{ $submission->status_label }} · {{ $submission->isEditable() ? 'Continue existing report' : 'View existing report' }}
                                        </em>
                                    @elseif ($isAvailable)
                                        <em class="ms-frequency-state ms-frequency-state--available">Available now</em>
                                    @else
                                        <em class="ms-frequency-state ms-frequency-state--unavailable">Awaiting M&amp;E configuration</em>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="ms-frequency-actions">
                    <span>
                        <i class="feather-info" aria-hidden="true"></i>
                        Selecting the same period again continues the existing workspace; it never creates a duplicate.
                    </span>
                    <button type="submit" class="ms-frequency-submit" data-frequency-submit
                        @disabled(! old('reporting_cycle_id') && ! $frequencyOptions->contains(fn ($option) => $option['cycle']?->id === $selectedSubmission?->reporting_cycle_id))>
                        <span>{{ $selectedSubmission ? 'Continue reporting' : 'Open reporting workspace' }}</span>
                        <i class="feather-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </section>

        @include('member-state.partials.page-heading', [
            'headingId' => 'reporting-page-title',
            'headingEyebrow' => 'Country reporting',
            'headingTitle' => 'Submit information',
            'headingDescription' => $selectedSubmission
                ? 'Continue the selected reporting package. All Sections A–R belong to one country submission for this period.'
                : 'Select a reporting frequency above, then choose a programme section to enter your country information.',
            'headingBadge' => count($reportingSections) . ' sections',
            'headingBadgeIcon' => 'feather-grid',
        ])

        @if ($selectedSubmission)
            <div class="ms-selected-cycle" role="status">
                <span class="ms-selected-cycle-icon" aria-hidden="true"><i class="feather-check"></i></span>
                <span>
                    <small>Selected reporting package</small>
                    <strong>{{ $selectedSubmission->reportingCycle?->reportingFrequency?->name }} · {{ $selectedSubmission->reportingCycle?->display_label }}</strong>
                </span>
                <em>{{ $selectedSubmission->status_label }}</em>
            </div>
        @endif

        @include('member-state.partials.card-search', [
            'searchId' => 'reporting-section-search',
            'searchPlaceholder' => 'Search by section letter, reporting topic, component, or keyword...',
            'searchCount' => count($reportingSections),
            'searchItemLabel' => 'reporting sections',
        ])

        <section class="ms-module-section" aria-labelledby="reporting-sections-title">
            <div class="ms-section-heading ms-section-heading-compact">
                <div>
                    <span class="ms-section-kicker">Programme modules</span>
                    <h2 id="reporting-sections-title">Reporting sections</h2>
                </div>
                <p>{{ $selectedSubmission ? 'Select a card to continue this reporting package.' : 'Select a reporting frequency to unlock these sections.' }}</p>
            </div>

            <div class="ms-module-grid" data-search-grid>
                @foreach ($reportingSections as $section)
                    @if ($selectedSubmission)
                        <a href="{{ route('member-state.reporting.show', ['section' => $section['slug'], 'submission' => $selectedSubmission->id]) }}"
                            class="ms-module-card"
                            data-search-item
                            data-search-text="Section {{ $section['letter'] }} {{ $section['title'] }} {{ $section['description'] }} {{ $section['slug'] }}"
                            aria-label="Open Section {{ $section['letter'] }}: {{ $section['title'] }}">
                    @else
                        <article class="ms-module-card ms-module-card--locked"
                            data-search-item
                            data-search-text="Section {{ $section['letter'] }} {{ $section['title'] }} {{ $section['description'] }} {{ $section['slug'] }}"
                            aria-disabled="true">
                    @endif
                        <div class="ms-module-art">
                            <span class="ms-module-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <img src="{{ asset($section['image']) }}"
                                alt=""
                                width="768"
                                height="768"
                                loading="{{ $loop->iteration <= 4 ? 'eager' : 'lazy' }}">
                        </div>
                        <div class="ms-module-body">
                            <span class="ms-module-label">Section {{ $section['letter'] }}</span>
                            <h3>{{ $section['title'] }}</h3>
                            <p>{{ $section['description'] }}</p>
                            <span class="ms-open-section">
                                {{ $selectedSubmission ? 'Open section' : 'Choose frequency first' }}
                                <i class="{{ $selectedSubmission ? 'feather-arrow-right' : 'feather-lock' }}" aria-hidden="true"></i>
                            </span>
                        </div>
                    @if ($selectedSubmission)
                        </a>
                    @else
                        </article>
                    @endif
                @endforeach
            </div>

            @include('member-state.partials.search-empty', [
                'emptyTitle' => 'No reporting sections found',
                'emptyMessage' => 'Check the spelling or try a broader topic such as climate, procurement, gender, finance, or results.',
            ])
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/member-state-card-search.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = Array.prototype.slice.call(document.querySelectorAll('input[name="reporting_cycle_id"]'));
            var submit = document.querySelector('[data-frequency-submit]');

            options.forEach(function (option) {
                option.addEventListener('change', function () {
                    document.querySelectorAll('.ms-frequency-option').forEach(function (card) {
                        card.classList.toggle('is-selected', !!card.querySelector('input:checked'));
                    });

                    if (submit) {
                        submit.disabled = false;
                    }
                });
            });
        });
    </script>
@endpush
