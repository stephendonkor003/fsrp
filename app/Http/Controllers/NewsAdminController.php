<?php

namespace App\Http\Controllers;

use App\Mail\NewsPublishedNotification;
use App\Models\NewsAttachment;
use App\Models\NewsPost;
use App\Models\NewsSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class NewsAdminController extends Controller
{
    public function index(Request $request)
    {
        $posts = NewsPost::with(['creator', 'approver'])
            ->withCount('attachments')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('system.news.index', compact('posts'));
    }

    public function create()
    {
        return view('system.news.form', ['post' => new NewsPost]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $action = (string) $request->input('action', 'draft');

        if ($action === 'publish') {
            abort_unless($this->canPublish($request), 403, 'You do not have permission to publish news.');
        }

        $now = now();
        $data['created_by'] = $request->user()?->id;
        $data['status'] = match ($action) {
            'submit' => 'submitted',
            'publish' => 'published',
            default => 'draft',
        };
        $data['submitted_by'] = in_array($action, ['submit', 'publish'], true) ? $request->user()?->id : null;
        $data['submitted_at'] = in_array($action, ['submit', 'publish'], true) ? $now : null;
        $data['approved_by'] = $action === 'publish' ? $request->user()?->id : null;
        $data['approved_at'] = $action === 'publish' ? $now : null;
        $data['published_at'] = $action === 'publish' ? $now : null;

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $this->storeCoverImage($request);
        }

        $post = NewsPost::create($data);
        $this->storeAttachments($request, $post);

        $message = $action === 'publish'
            ? 'News post saved and published. It is now visible on the public news page.'
            : ($action === 'submit' ? 'News post saved and submitted for approval.' : 'News draft saved.');

        if ($post->status === 'published') {
            $message .= $this->notifyPublishedPost($post->fresh());
        }

        return redirect()->route('system.news.edit', $post)->with('success', $message);
    }

    public function edit(NewsPost $post)
    {
        $post->load('attachments');

        return view('system.news.form', compact('post'));
    }

    public function update(Request $request, NewsPost $post)
    {
        $data = $this->validated($request);
        $action = (string) $request->input('action', 'draft');
        $previousCoverPath = null;

        if ($action === 'publish') {
            abort_unless($this->canPublish($request), 403, 'You do not have permission to publish news.');
        }

        if ($request->hasFile('cover_image')) {
            $previousCoverPath = $post->cover_image_path;
            $data['cover_image_path'] = $this->storeCoverImage($request);
        }

        if ($action === 'submit') {
            $data['status'] = 'submitted';
            $data['submitted_by'] = $request->user()?->id;
            $data['submitted_at'] = now();
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['published_at'] = null;
        } elseif ($action === 'publish') {
            $data['status'] = 'published';
            $data['submitted_by'] = $post->submitted_by ?: $request->user()?->id;
            $data['submitted_at'] = $post->submitted_at ?: now();
            $data['approved_by'] = $request->user()?->id;
            $data['approved_at'] = $post->approved_at ?: now();
            $data['published_at'] = $post->published_at ?: now();
        }

        $post->update($data);

        if ($previousCoverPath && $previousCoverPath !== $post->cover_image_path) {
            Storage::disk('public')->delete($previousCoverPath);
        }

        $this->storeAttachments($request, $post);

        $message = $action === 'publish'
            ? 'News post updated and published. It is visible on the public news page.'
            : ($action === 'submit' ? 'News post updated and submitted for approval.' : 'News post updated.');

        if ($post->status === 'published') {
            $message .= $this->notifyPublishedPost($post->fresh());
        }

        return redirect()->route('system.news.edit', $post)->with('success', $message);
    }

    public function approve(Request $request, NewsPost $post)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,published,rejected',
            'review_notes' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        if ($data['status'] === 'rejected') {
            $post->update([
                'status' => 'rejected',
                'review_notes' => $data['review_notes'] ?? null,
                'approved_by' => null,
                'approved_at' => null,
                'published_at' => null,
            ]);

            return back()->with('success', 'News post rejected.');
        }

        $post->update([
            'status' => $data['status'],
            'review_notes' => $data['review_notes'] ?? null,
            'approved_by' => $request->user()?->id,
            'approved_at' => now(),
            'published_at' => $data['status'] === 'published'
                ? ($data['published_at'] ?? now())
                : null,
        ]);

        $message = $post->status === 'published'
            ? 'News approval saved. The post is visible on the public news page.'.$this->notifyPublishedPost($post->fresh())
            : 'News approval saved.';

        return back()->with('success', $message);
    }

    public function destroyAttachment(NewsPost $post, NewsAttachment $attachment)
    {
        abort_unless($attachment->news_post_id === $post->id, 404);
        Storage::disk('local')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }

    private function validated(Request $request): array
    {
        $post = $request->route('post');
        $requestedSlug = trim((string) $request->input('slug'));
        $slugBase = Str::slug($requestedSlug !== '' ? $requestedSlug : (string) $request->input('title'));
        $slugBase = $slugBase !== '' ? $slugBase : 'news';
        $slug = $requestedSlug !== '' ? $slugBase : $this->availableSlug($slugBase, $post);

        $request->merge(['slug' => $slug]);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attp_news_posts', 'slug')->when($post?->exists, fn ($rule) => $rule->ignore($post->id)),
            ],
            'category' => 'required|in:policy,research,events,announcement,press',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'tags' => 'nullable|string|max:1000',
            'action' => ['required', Rule::in(['draft', 'submit', 'publish'])],
            'cover_image' => 'nullable|image|max:20480',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:20480',
        ], [
            'cover_image.uploaded' => 'The cover image could not be uploaded. The server must allow files up to 25 MB and requests up to 64 MB.',
            'cover_image.max' => 'The cover image must not be larger than 20 MB.',
            'attachments.max' => 'You may attach up to 10 files to one news post.',
            'attachments.*.uploaded' => 'An attachment could not be uploaded. The server must allow files up to 25 MB and requests up to 64 MB.',
            'attachments.*.max' => 'Each attachment must not be larger than 20 MB.',
            'attachments.*.mimes' => 'Attachments must be PDF, Office, ZIP, JPG, JPEG, or PNG files.',
        ]);

        $data['tags'] = collect(explode(',', (string) ($data['tags'] ?? '')))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
        $data['body'] = $this->sanitizeNewsHtml($data['body']);

        $plainBody = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($data['body']))));
        if ($plainBody === '' && ! preg_match('/<img\b/i', $data['body'])) {
            throw ValidationException::withMessages([
                'body' => 'Please enter the news story in the Body editor before saving.',
            ]);
        }

        unset($data['action'], $data['cover_image'], $data['attachments']);

        return $data;
    }

    private function availableSlug(string $base, ?NewsPost $post = null): string
    {
        $slug = $base;
        $counter = 2;

        while (NewsPost::query()
            ->where('slug', $slug)
            ->when($post?->exists, fn ($query) => $query->whereKeyNot($post->id))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function canPublish(Request $request): bool
    {
        $user = $request->user();

        return $user !== null
            && ($user->hasPermission('news.approve') || $user->hasPermission('communications.respond'));
    }

    private function notifyPublishedPost(NewsPost $post): string
    {
        try {
            $this->notifySubscribers($post);
        } catch (Throwable $exception) {
            Log::warning('News subscriber notification failed.', [
                'news_post_id' => $post->id,
                'message' => $exception->getMessage(),
            ]);

            return ' Subscriber email notification could not be queued, but publication succeeded.';
        }

        return '';
    }

    private function storeCoverImage(Request $request): string
    {
        try {
            $path = $request->file('cover_image')?->store('news/covers', 'public');
        } catch (Throwable $exception) {
            Log::error('News cover image storage failed.', [
                'user_id' => $request->user()?->id,
                'storage_path' => storage_path('app/public/news/covers'),
                'message' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'cover_image' => 'The cover image could not be saved because the server upload directory is not writable. Run the production storage preparation script on the server.',
            ]);
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'cover_image' => 'The cover image could not be saved because the server upload directory is not writable. Run the production storage preparation script on the server.',
            ]);
        }

        return $path;
    }

    private function storeAttachments(Request $request, NewsPost $post): void
    {
        foreach ($request->file('attachments', []) as $file) {
            if (! $file) {
                continue;
            }

            try {
                $path = $file->store("news/attachments/{$post->id}", 'local');
            } catch (Throwable $exception) {
                Log::error('News attachment storage failed.', [
                    'news_post_id' => $post->id,
                    'user_id' => $request->user()?->id,
                    'storage_path' => storage_path("app/private/news/attachments/{$post->id}"),
                    'message' => $exception->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'attachments' => 'An attachment could not be saved because the server upload directory is not writable. Run the production storage preparation script on the server.',
                ]);
            }

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'attachments' => 'An attachment could not be saved because the server upload directory is not writable. Run the production storage preparation script on the server.',
                ]);
            }

            $post->attachments()->create([
                'uploaded_by' => $request->user()?->id,
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size_bytes' => $file->getSize(),
            ]);
        }
    }

    private function notifySubscribers(NewsPost $post): void
    {
        if ($post->notified_at) {
            return;
        }

        NewsSubscriber::active()->orderBy('email')->chunk(100, function ($subscribers) use ($post) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewsPublishedNotification($post, $subscriber));
            }
        });

        $post->update(['notified_at' => now()]);
    }

    private function sanitizeNewsHtml(string $html): string
    {
        $html = preg_replace('#<(script|iframe|object|embed|form|input|button|textarea|select|style)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = preg_replace('#\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)#is', '', $html) ?? '';
        $html = preg_replace('#(href|src)\s*=\s*("|\')\s*javascript:[^"\']*\2#is', '$1="#"', $html) ?? '';

        return trim($html);
    }
}
