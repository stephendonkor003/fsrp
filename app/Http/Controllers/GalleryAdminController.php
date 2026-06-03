<?php

namespace App\Http\Controllers;

use App\Models\GalleryMedia;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GalleryAdminController extends Controller
{
    public function index(Request $request)
    {
        $mediaItems = GalleryMedia::with(['creator', 'approver'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('media_type', $request->string('type')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = GalleryMedia::categories();

        return view('system.gallery.index', compact('mediaItems', 'categories'));
    }

    public function create()
    {
        return view('system.gallery.form', [
            'media' => new GalleryMedia(),
            'categories' => GalleryMedia::categories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $file = $request->file('media_file');
        $mediaType = $this->detectMediaType($file);

        $data = array_merge($data, $this->storedMediaPayload($file, $mediaType));
        $data['media_type'] = $mediaType;
        $data['created_by'] = $request->user()?->id;
        $data['uploaded_by'] = $request->user()?->id;
        $data['status'] = $request->input('action') === 'submit' ? 'submitted' : 'draft';
        $data['submitted_by'] = $data['status'] === 'submitted' ? $request->user()?->id : null;
        $data['submitted_at'] = $data['status'] === 'submitted' ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('gallery/thumbnails', 'public');
        }

        $media = GalleryMedia::create($data);

        return redirect()->route('system.gallery.edit', $media)->with('success', 'Gallery media saved.');
    }

    public function edit(GalleryMedia $media)
    {
        return view('system.gallery.form', [
            'media' => $media,
            'categories' => GalleryMedia::categories(),
        ]);
    }

    public function update(Request $request, GalleryMedia $media)
    {
        $data = $this->validated($request);

        if ($request->hasFile('media_file')) {
            Storage::disk('public')->delete($media->file_path);

            $file = $request->file('media_file');
            $mediaType = $this->detectMediaType($file);

            $data = array_merge($data, $this->storedMediaPayload($file, $mediaType));
            $data['media_type'] = $mediaType;
            $data['uploaded_by'] = $request->user()?->id;
        }

        if ($request->hasFile('thumbnail')) {
            if ($media->thumbnail_path) {
                Storage::disk('public')->delete($media->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('gallery/thumbnails', 'public');
        }

        if ($request->input('action') === 'submit') {
            $data['status'] = 'submitted';
            $data['submitted_by'] = $request->user()?->id;
            $data['submitted_at'] = now();
        }

        $media->update($data);

        return redirect()->route('system.gallery.edit', $media)->with('success', 'Gallery media updated.');
    }

    public function approve(Request $request, GalleryMedia $media)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,published,rejected',
            'review_notes' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        if ($data['status'] === 'rejected') {
            $media->update([
                'status' => 'rejected',
                'review_notes' => $data['review_notes'] ?? null,
                'approved_by' => null,
                'approved_at' => null,
                'published_at' => null,
            ]);

            return back()->with('success', 'Gallery media rejected.');
        }

        $media->update([
            'status' => $data['status'],
            'review_notes' => $data['review_notes'] ?? null,
            'approved_by' => $request->user()?->id,
            'approved_at' => now(),
            'published_at' => $data['status'] === 'published'
                ? ($data['published_at'] ?? now())
                : null,
        ]);

        return back()->with('success', 'Gallery approval saved.');
    }

    public function destroy(GalleryMedia $media)
    {
        Storage::disk('public')->delete(array_filter([
            $media->file_path,
            $media->thumbnail_path,
        ]));

        $media->delete();

        return redirect()->route('system.gallery.index')->with('success', 'Gallery media removed.');
    }

    private function validated(Request $request): array
    {
        $media = $request->route('media');
        $slug = filled($request->input('slug'))
            ? Str::slug((string) $request->input('slug'))
            : Str::slug((string) $request->input('title'));

        $request->merge(['slug' => $slug]);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('attp_gallery_media', 'slug')->when($media?->exists, fn ($rule) => $rule->ignore($media->id)),
            ],
            'category' => ['required', Rule::in(array_keys(GalleryMedia::categories()))],
            'description' => 'nullable|string',
            'caption' => 'nullable|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'captured_at' => 'nullable|date',
            'sort_order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'media_file' => [
                $media?->exists ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,wmv,webm',
                'max:102400',
            ],
            'thumbnail' => 'nullable|image|max:4096',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['slug'] = $data['slug'] ?: null;

        unset($data['media_file'], $data['thumbnail']);

        return $data;
    }

    private function storedMediaPayload(UploadedFile $file, string $mediaType): array
    {
        return [
            'file_path' => $file->store('gallery/' . ($mediaType === 'video' ? 'videos' : 'images'), 'public'),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
        ];
    }

    private function detectMediaType(UploadedFile $file): string
    {
        $mime = (string) $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (Str::startsWith($mime, 'video/') || in_array($extension, ['mp4', 'mov', 'avi', 'wmv', 'webm'], true)) {
            return 'video';
        }

        return 'image';
    }
}
