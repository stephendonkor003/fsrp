@extends('layouts.app')

@section('title', 'Configure Member State Reporting Cycle')

@section('content')
    <div class="nxl-container">
        <div class="page-header mb-4">
            <div>
                <h4 class="fw-bold mb-1">Configure Member State Reporting Cycle</h4>
                <p class="text-muted mb-0">Choose the frequency and period that Member States may report against.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('budget.me.member-state-reporting-cycles.store') }}">
            @csrf
            @include('me.member-state-reporting-cycles._form')
        </form>
    </div>
@endsection
