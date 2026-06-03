<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GalleryMedia extends BaseModel
{
    protected $table = 'attp_gallery_media';

    protected $fillable = [
        'title',
        'slug',
        'media_type',
        'category',
        'description',
        'caption',
        'alt_text',
        'file_path',
        'thumbnail_path',
        'file_name',
        'mime_type',
        'file_size_bytes',
        'status',
        'is_featured',
        'sort_order',
        'captured_at',
        'created_by',
        'uploaded_by',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'published_at',
        'review_notes',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'captured_at' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public static function categories(): array
    {
        return [
            'events' => 'Events',
            'field_visits' => 'Field Visits',
            'trainings' => 'Trainings',
            'workshops' => 'Workshops',
            'beneficiaries' => 'Beneficiaries',
            'infrastructure' => 'Infrastructure',
            'media' => 'Media',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('approved_at')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->approved_at !== null
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (GalleryMedia $media): void {
            if (blank($media->slug)) {
                $base = Str::slug($media->title);
                $slug = $base;
                $counter = 1;

                while (self::where('slug', $slug)->when($media->exists, fn ($query) => $query->where('id', '!=', $media->id))->exists()) {
                    $slug = $base . '-' . $counter++;
                }

                $media->slug = $slug;
            }
        });
    }
}
