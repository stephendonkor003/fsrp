@extends('layouts.app')

@section('title', 'Sub-Activities Overview')

@section('content')

    <style>
        /* Tree indentation */
        .tree-level-0 {
            margin-left: 0;
        }

        .tree-level-1 {
            margin-left: 20px;
        }

        .tree-level-2 {
            margin-left: 40px;
        }

        .tree-level-3 {
            margin-left: 60px;
        }

        .tree-icon {
            font-size: 18px;
            margin-right: 6px;
        }

        /* Accordion visual levels */
        .lvl-program {
            background: #eef5ff;
            border-left: 4px solid #0d6efd;
        }

        .lvl-project {
            background: #f5f2ff;
            border-left: 4px solid #6610f2;
        }

        .lvl-activity {
            background: #f4fff5;
            border-left: 4px solid #198754;
        }

        .lvl-sub {
            background: #ffffff;
            border-left: 3px solid #20c997;
        }

        .hover-row:hover {
            background: #eef4ff;
            transition: 0.2s;
        }

        .progress-sm {
            height: 10px;
            border-radius: 5px;
        }

        /* Search highlight */
        .search-highlight {
            background: yellow;
            padding: 2px 4px;
        }
    </style>

    <main class="nxl-container fade-in">
        <div class="nxl-content">

            <!-- HEADER -->
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Sub-Activities Management</h4>
                    <p class="text-muted">PROGRAM → PROJECT → ACTIVITY → SUB-ACTIVITY</p>
                </div>
                <a href="{{ route('budget.activities.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left-circle"></i> Back to Activities
                </a>
            </div>

            <!-- SEARCH + SORT -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <input id="treeSearch" type="text" class="form-control me-2" placeholder="Search anything...">
                        <select id="sortSelect" class="form-select w-auto">
                            <option value="">Sort By</option>
                            <option value="program">Program</option>
                            <option value="project">Project</option>
                            <option value="activity">Activity</option>
                            <option value="allocation">Allocation (High → Low)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TREE VIEW -->
            <div class="accordion" id="programAccordion">

                @forelse ($programs as $pIndex => $program)
                    @php
                        $programId = 'program_' . $pIndex;
                    @endphp

                    <div class="accordion-item lvl-program mb-3 border-0 shadow-sm rounded">

                        <h2 class="accordion-header" id="heading_{{ $programId }}">
                            <button class="accordion-button collapsed fw-bold" data-bs-toggle="collapse"
                                data-bs-target="#collapse_{{ $programId }}">
                                <span class="tree-icon">📘</span>
                                <span class="tree-label" data-label="program">{{ $program->name }}</span>
                            </button>
                        </h2>

                        <div id="collapse_{{ $programId }}" class="accordion-collapse collapse">
                            <div class="accordion-body tree-level-1">

                                @foreach ($program->projects as $prIndex => $project)
                                    @php
                                        $projectId = 'project_' . $pIndex . '_' . $prIndex;
                                        $totalProject = $project->activities->sum(
                                            fn($act) => $act->allocations->sum('amount'),
                                        );
                                    @endphp

                                    <!-- PROJECT -->
                                    <div class="accordion mb-3" id="acc_pr_{{ $projectId }}">
                                        <div class="accordion-item lvl-project rounded">

                                            <h2 class="accordion-header" id="heading_{{ $projectId }}">
                                                <button class="accordion-button collapsed fw-semibold" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse_{{ $projectId }}">

                                                    <span class="tree-icon">📌</span>
                                                    <span class="tree-label" data-label="project">
                                                        {{ $project->project_id }} — {{ $project->name }}
                                                    </span>
                                                </button>
                                            </h2>

                                            <div id="collapse_{{ $projectId }}" class="accordion-collapse collapse">
                                                <div class="accordion-body tree-level-2">

                                                    <!-- ACTIVITIES -->
                                                    @foreach ($project->activities as $aIndex => $activity)
                                                        @php
                                                            $activityId = 'activity_' . $projectId . '_' . $aIndex;
                                                            $activityTotal = $activity->allocations->sum('amount');
                                                        @endphp

                                                        <div class="accordion-item lvl-activity rounded mb-2">

                                                            <h2 class="accordion-header" id="heading_{{ $activityId }}">
                                                                <button class="accordion-button collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse_{{ $activityId }}">

                                                                    <span class="tree-icon">🎯</span>
                                                                    <span class="tree-label" data-label="activity">
                                                                        {{ $activity->name }}
                                                                    </span>

                                                                    <div class="ms-auto text-end small">
                                                                        <div class="text-muted">Total Allocated:</div>
                                                                        <strong>{{ number_format($activityTotal, 2) }}
                                                                            {{ $project->currency }}</strong>

                                                                        <!-- Progress Bar -->
                                                                        @php
                                                                            $percent =
                                                                                $project->total_budget > 0
                                                                                    ? ($activityTotal /
                                                                                            $project->total_budget) *
                                                                                        100
                                                                                    : 0;
                                                                        @endphp
                                                                        <div class="progress progress-sm mt-1">
                                                                            <div class="progress-bar bg-success"
                                                                                style="width: {{ $percent }}%"></div>
                                                                        </div>
                                                                    </div>

                                                                </button>
                                                            </h2>

                                                            <div id="collapse_{{ $activityId }}"
                                                                class="accordion-collapse collapse">
                                                                <div class="accordion-body tree-level-3">

                                                                    <!-- Add Sub-Activity -->
                                                                    <div class="text-end mb-3">
                                                                        @can('subactivities.create')
                                                                            <a href="{{ route('budget.subactivities.create', $activity->id) }}"
                                                                                class="btn btn-success btn-sm">
                                                                                <i class="bi bi-plus-circle"></i> Add
                                                                                Sub-Activity
                                                                            </a>
                                                                        @endcan
                                                                    </div>

                                                                    <!-- SUB-ACTIVITIES -->
                                                                    @foreach ($activity->subActivities as $sIndex => $sub)
                                                                        @php
                                                                            $subId =
                                                                                'sub_' . $activityId . '_' . $sIndex;
                                                                            $subTotal = $sub->allocations->sum(
                                                                                'amount',
                                                                            );
                                                                        @endphp

                                                                        <div class="accordion-item lvl-sub rounded mb-2">

                                                                            <h2 class="accordion-header"
                                                                                id="heading_{{ $subId }}">
                                                                                <button class="accordion-button collapsed"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapse_{{ $subId }}">

                                                                                    <span class="tree-icon">📍</span>
                                                                                    <span class="tree-label"
                                                                                        data-label="sub">
                                                                                        {{ $sub->name }}
                                                                                    </span>

                                                                                    <div class="ms-auto small text-end">
                                                                                        <strong>{{ number_format($subTotal, 2) }}
                                                                                            {{ $project->currency }}</strong>

                                                                                        <!-- Progress bar inside Activity -->
                                                                                        @php
                                                                                            $activityPercent =
                                                                                                $activityTotal > 0
                                                                                                    ? ($subTotal /
                                                                                                            $activityTotal) *
                                                                                                        100
                                                                                                    : 0;
                                                                                        @endphp
                                                                                        <div
                                                                                            class="progress progress-sm mt-1">
                                                                                            <div class="progress-bar bg-info"
                                                                                                style="width: {{ $activityPercent }}%">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                </button>
                                                                            </h2>

                                                                            <div id="collapse_{{ $subId }}"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">

                                                                                    <div class="mb-3">
                                                                                        <strong>Expected Outcome:</strong>
                                                                                        @if ($sub->expected_outcome_type === 'percentage')
                                                                                            {{ $sub->expected_outcome_value ?? 'N/A' }}%
                                                                                        @elseif ($sub->expected_outcome_type === 'text')
                                                                                            {{ $sub->expected_outcome_value ?? 'N/A' }}
                                                                                        @else
                                                                                            N/A
                                                                                        @endif
                                                                                    </div>

                                                                                    <!-- Allocations Table -->
                                                                                    <table
                                                                                        class="table table-bordered table-hover">
                                                                                        <thead class="table-light">
                                                                                            <tr>
                                                                                                <th>Year</th>
                                                                                                <th>Amount
                                                                                                    ({{ $project->currency }})
                                                                                                </th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($sub->allocations as $alloc)
                                                                                                <tr class="hover-row">
                                                                                                    <td>{{ $alloc->year }}
                                                                                                    </td>
                                                                                                    <td>{{ number_format($alloc->amount, 2) }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>

                                                                                    <div class="text-end mt-3">
                                                                                        @can('activities.allocate')
                                                                                            <a class="btn btn-primary btn-sm"
                                                                                                href="{{ route('budget.subactivities.allocations.edit', $sub->id) }}">
                                                                                                <i class="bi bi-sliders"></i>
                                                                                                Edit Allocations
                                                                                            </a>
                                                                                        @endcan
                                                                                        @can('subactivities.delete')
                                                                                            <form
                                                                                                action="{{ route('budget.subactivities.destroy', $sub->id) }}"
                                                                                                method="POST" class="d-inline"
                                                                                                onsubmit="return confirm('Delete this Sub-Activity?')">
                                                                                                @csrf @method('DELETE')
                                                                                                <button
                                                                                                    class="btn btn-danger btn-sm">
                                                                                                    <i class="bi bi-trash"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        @endcan
                                                                                    </div>

                                                                                </div>
                                                                            </div>

                                                                        </div> <!-- END SUB -->
                                                                    @endforeach

                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted">No Programs Found.</p>
                    </div>
                @endforelse

            </div>

        </div>
    </main>

    <!-- SEARCH + SORT SCRIPT -->
    <script>
        document.getElementById("treeSearch").addEventListener("input", function() {
            let search = this.value.toLowerCase();
            document.querySelectorAll(".tree-label").forEach(function(label) {
                let text = label.innerText.toLowerCase();
                label.style.background = text.includes(search) && search !== "" ? "yellow" : "transparent";
            });
        });
    </script>

@endsection
