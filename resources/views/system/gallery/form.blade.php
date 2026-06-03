@extends('layouts.app')

@section('title', $media->exists ? 'Edit Gallery Media' : 'Upload Gallery Media')

@section('content')
    <div class="nxl-container">
        <div class="card mb-4 border-0"
            style="background: linear-gradient(120deg, #0f172a, #0f766e 55%, #0ea5e9); color: #fff; border-radius: 14px;">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="text-uppercase small text-white-50 fw-semibold">Gallery Media</div>
                    <h4 class="fw-bold mb-1 text-white">{{ $media->exists ? 'Edit Gallery Media' : 'Upload Gallery Media' }}</h4>
                    <p class="mb-0 text-white-50">Prepare images and videos for approval before they appear on the public gallery page.</p>
                </div>
                <a href="{{ route('system.gallery.index') }}" class="btn btn-light">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">{{ $errors->first() }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <form class="card border-0 shadow-sm" method="POST" enctype="multipart/form-data"
                    action="{{ $media->exists ? route('system.gallery.update', $media) : route('system.gallery.store') }}">
                    @csrf
                    @if ($media->exists)
                        @method('PUT')
                    @endif

                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="feather-image me-1"></i> Media Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input name="title" class="form-control" value="{{ old('title', $media->title) }}" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Slug</label>
                                <input name="slug" class="form-control" value="{{ old('slug', $media->slug) }}" placeholder="Auto-generated if blank">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category', $media->category ?: 'events') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $media->description) }}</textarea>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Caption</label>
                            <input name="caption" class="form-control" value="{{ old('caption', $media->caption) }}" maxlength="500">
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alt Text</label>
                                <input name="alt_text" class="form-control" value="{{ old('alt_text', $media->alt_text) }}" maxlength="255">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Captured Date</label>
                                <input type="date" name="captured_at" class="form-control" value="{{ old('captured_at', optional($media->captured_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Sort Order</label>
                                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $media->sort_order ?? 0) }}">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Media File {{ $media->exists ? '' : '*' }}</label>
                                <input type="file" name="media_file" class="form-control"
                                    accept="image/*,video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-ms-wmv"
                                    {{ $media->exists ? '' : 'required' }}>
                                <div class="form-text">Allowed: JPG, PNG, WebP, GIF, MP4, MOV, AVI, WMV, WebM. Max 100 MB.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Video Thumbnail</label>
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                <div class="form-text">Optional, useful for videos before playback.</div>
                            </div>
                        </div>

                        <div class="form-check mt-3">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                                @checked(old('is_featured', $media->is_featured))>
                            <label class="form-check-label fw-semibold" for="is_featured">Feature this item first on the public gallery</label>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex flex-wrap justify-content-end gap-2">
                        <button class="btn btn-light border" type="submit" name="action" value="draft">
                            <i class="feather-save me-1"></i> Save Draft
                        </button>
                        <button class="btn btn-primary" type="submit" name="action" value="submit">
                            <i class="feather-send me-1"></i> Submit for Approval
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                @if ($media->exists)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="feather-eye me-1"></i> Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="rounded overflow-hidden border bg-light">
                                @if ($media->isImage())
                                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->alt_text ?: $media->title }}" class="img-fluid w-100">
                                @else
                                    <video controls preload="metadata" class="w-100"
                                        @if($media->thumbnail_path) poster="{{ asset('storage/' . $media->thumbnail_path) }}" @endif>
                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->mime_type ?: 'video/mp4' }}">
                                    </video>
                                @endif
                            </div>
                            <div class="small text-muted mt-2">{{ $media->file_name }} &middot; {{ number_format(($media->file_size_bytes ?? 0) / 1024, 1) }} KB</div>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="feather-activity me-1"></i> Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <span class="badge text-capitalize bg-secondary">{{ $media->status ?: 'draft' }}</span>
                            @if ($media->is_featured)
                                <span class="badge bg-warning text-dark">Featured</span>
                            @endif
                        </p>
                        <div class="small text-muted">Type</div>
                        <div class="fw-semibold mb-3">{{ $media->media_type ? ucfirst($media->media_type) : 'Not uploaded' }}</div>
                        <div class="small text-muted">Approved</div>
                        <div class="fw-semibold mb-3">{{ optional($media->approved_at)->format('d M Y H:i') ?? 'No' }}</div>
                        <div class="small text-muted">Published</div>
                        <div class="fw-semibold">{{ optional($media->published_at)->format('d M Y H:i') ?? 'No' }}</div>
                    </div>
                </div>

                @if ($media->exists)
                    @can('gallery.approve')
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 fw-bold"><i class="feather-check-circle me-1"></i> Approval</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('system.gallery.approve', $media) }}">
                                    @csrf
                                    <label class="form-label fw-semibold">Decision</label>
                                    <select name="status" class="form-select mb-3" required>
                                        <option value="approved">Approve only</option>
                                        <option value="published">Approve and publish</option>
                                        <option value="rejected">Reject</option>
                                    </select>

                                    <label class="form-label fw-semibold">Publish Date</label>
                                    <input type="datetime-local" name="published_at" class="form-control mb-3">

                                    <label class="form-label fw-semibold">Review Notes</label>
                                    <textarea name="review_notes" class="form-control mb-3" rows="4">{{ $media->review_notes }}</textarea>

                                    <button class="btn btn-success w-100" type="submit">
                                        <i class="feather-check me-1"></i> Save Approval
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endcan

                    @can('gallery.manage')
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <form method="POST" action="{{ route('system.gallery.destroy', $media) }}"
                                    onsubmit="return confirm('Remove this gallery media item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger w-100" type="submit">
                                        <i class="feather-trash-2 me-1"></i> Remove Media
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endcan
                @endif
            </div>
        </div>
    </div>
@endsection
