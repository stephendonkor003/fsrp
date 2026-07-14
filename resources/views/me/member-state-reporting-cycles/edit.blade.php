@extends('layouts.app')

@section('title', 'Edit Member State Reporting Cycle')

@section('content')
    <div class="nxl-container">
        <div class="page-header mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit {{ $cycle->display_label }}</h4>
                <p class="text-muted mb-0">Control when this reporting cycle appears in the Member State portal.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('budget.me.member-state-reporting-cycles.update', $cycle) }}">
            @csrf
            @method('PUT')
            @include('me.member-state-reporting-cycles._form')
        </form>
    </div>
@endsection
