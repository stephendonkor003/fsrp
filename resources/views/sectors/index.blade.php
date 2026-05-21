@extends('layouts.app')

@section('content')
    <div class="nxl-container">

        <div class="page-header d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-dark">Sectors</h4>
            @can('sector.create')
                <a href="{{ route('budget.sectors.create') }}" class="btn btn-primary">
                    <i class="feather-plus-circle me-1"></i> New Sector
                </a>
            @endcan
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-body">
                <x-data-table
                    id="sectorsTable"
                >
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Governance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sectors as $sector)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sector->name }}</div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($sector->description, 80) ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $sector->governanceNode->name ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $sector->governanceNode->level->name ?? '' }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
            </div>
        </div>

    </div>
@endsection
