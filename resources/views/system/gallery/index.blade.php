@extends('layouts.app')

@section('title', 'Gallery Administration')

@section('content')
    <div class="nxl-container">
        <div class="card mb-4 border-0"
            style="background: linear-gradient(120deg, #0f172a, #0f766e 55%, #0ea5e9); color: #fff; border-radius: 14px;">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="text-uppercase small text-white-50 fw-semibold">Back-Office Media Library</div>
                    <h4 class="fw-bold mb-1 text-white">Gallery Administration</h4>
                    <p class="mb-0 text-white-50">Upload, review, approve, and publish public gallery images and videos.</p>
                </div>
                @can('gallery.manage')
                    <a href="{{ route('system.gallery.create') }}" class="btn btn-light">
                        <i class="feather-plus-circle me-1"></i> Upload Media
                    </a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('system.gallery.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All statuses</option>
                            @foreach (['draft', 'submitted', 'approved', 'published', 'rejected'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Media Type</label>
                        <select name="type" class="form-select">
                            <option value="">All media</option>
                            <option value="image" @selected(request('type') === 'image')>Images</option>
                            <option value="video" @selected(request('type') === 'video')>Videos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All categories</option>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">
                            <i class="feather-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-light border" href="{{ route('system.gallery.index') }}">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="feather-image me-1"></i> Gallery Register
                </h5>
                <span class="badge bg-light text-dark">{{ $mediaItems->total() }} items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Media</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mediaItems as $media)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded overflow-hidden bg-light border" style="width: 74px; height: 54px;">
                                                @if ($media->isImage())
                                                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->alt_text ?: $media->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @elseif ($media->thumbnail_path)
                                                    <img src="{{ asset('storage/' . $media->thumbnail_path) }}" alt="{{ $media->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                        <i class="feather-video"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $media->title }}</div>
                                                <small class="text-muted">{{ $media->file_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark text-capitalize">
                                            <i class="feather-{{ $media->isVideo() ? 'video' : 'image' }} me-1"></i>{{ $media->media_type }}
                                        </span>
                                    </td>
                                    <td>{{ $categories[$media->category] ?? ucfirst(str_replace('_', ' ', $media->category)) }}</td>
                                    <td>
                                        <span class="badge text-capitalize bg-{{ $media->status === 'published' ? 'success' : ($media->status === 'rejected' ? 'danger' : ($media->status === 'approved' ? 'primary' : 'secondary')) }}">
                                            {{ $media->status }}
                                        </span>
                                        @if ($media->is_featured)
                                            <span class="badge bg-warning text-dark ms-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($media->published_at)->format('d M Y H:i') ?? 'Not published' }}</td>
                                    <td class="text-end pe-4">
                                        <a class="btn btn-sm btn-light border" href="{{ route('system.gallery.edit', $media) }}">
                                            <i class="feather-edit-2 me-1"></i> Edit / Review
                                        </a>
                                        @if ($media->isPublished())
                                            <a class="btn btn-sm btn-primary" href="{{ route('gallery.index') }}" target="_blank">
                                                <i class="feather-external-link me-1"></i> Public
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">No gallery media uploaded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($mediaItems->hasPages())
                <div class="card-footer bg-white">
                    {{ $mediaItems->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
