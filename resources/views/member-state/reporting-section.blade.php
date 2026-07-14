@extends('layouts.app')

@section('title', 'Section ' . $reportingSection['letter'] . ': ' . $reportingSection['title'])

@section('content')
    <main class="ms-reporting-page">
        <section class="ms-section-overview" aria-labelledby="section-page-title">
            <div class="ms-section-overview-copy">
                <div class="ms-section-overview-meta">
                    <span class="ms-section-letter">Section {{ $reportingSection['letter'] }}</span>
                    <span class="ms-section-overview-type">
                        <i class="feather-file-text" aria-hidden="true"></i>
                        Reporting module
                    </span>
                    <span class="ms-section-overview-type">
                        <i class="feather-calendar" aria-hidden="true"></i>
                        {{ $reportSubmission->reportingCycle?->reportingFrequency?->name }} · {{ $reportSubmission->reportingCycle?->display_label }}
                    </span>
                </div>
                <h1 id="section-page-title">{{ $reportingSection['title'] }}</h1>
                <p>{{ $reportingSection['description'] }}</p>
            </div>

            <div class="ms-section-overview-art" aria-hidden="true">
                <span>{{ $reportingSection['letter'] }}</span>
                <img src="{{ asset($reportingSection['image']) }}"
                    alt=""
                    width="768"
                    height="768">
            </div>
        </section>

        <section class="ms-section-ready" aria-labelledby="section-ready-title">
            <div class="ms-section-ready-header">
                <div class="ms-ready-icon" aria-hidden="true">
                    <i class="feather-edit-3"></i>
                </div>
                <span class="ms-ready-status">
                    <i class="feather-check-circle" aria-hidden="true"></i>
                    {{ $reportSubmission->status_label }} report
                </span>
            </div>

            <div class="ms-section-ready-copy">
                <span class="ms-section-kicker">Reporting workspace</span>
                <h2 id="section-ready-title">This module is ready for its reporting content</h2>
                <p>
                    The dedicated page and navigation for Section {{ $reportingSection['letter'] }} are in place.
                    Its reporting fields, tables, workflow, and guidance can be configured when the section requirements are provided.
                </p>
            </div>

            <a href="{{ route('member-state.reporting.index', ['submission' => $reportSubmission->id]) }}" class="ms-secondary-back">
                <i class="feather-arrow-left" aria-hidden="true"></i>
                All reporting sections
            </a>
        </section>
    </main>
@endsection
