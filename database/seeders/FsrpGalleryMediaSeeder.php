<?php

namespace Database\Seeders;

use App\Models\GalleryMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FsrpGalleryMediaSeeder extends Seeder
{
    public function run(): void
    {
        $publishedAt = now()->subHours(2);

        $items = [
            [
                'source' => 'media1.jpeg',
                'storage_path' => 'gallery/seeded/media1.jpeg',
                'slug' => 'fsrp-gallery-media-1',
                'title' => 'FSRP Field Implementation Moment',
                'media_type' => 'image',
                'category' => 'field_visits',
                'caption' => 'FSRP field implementation and food system resilience activity.',
                'description' => 'A public gallery image highlighting FSRP implementation activity and food system resilience coordination.',
                'alt_text' => 'FSRP field implementation activity',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'source' => 'media2.mp4',
                'storage_path' => 'gallery/seeded/media2.mp4',
                'slug' => 'fsrp-gallery-media-2',
                'title' => 'FSRP Program Activity Video One',
                'media_type' => 'video',
                'category' => 'events',
                'caption' => 'Video from FSRP program activity and stakeholder engagement.',
                'description' => 'A public gallery video documenting FSRP program activity, coordination, and stakeholder engagement.',
                'alt_text' => 'FSRP program activity video',
                'thumbnail_path' => 'gallery/seeded/media1.jpeg',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'source' => 'media3.mp4',
                'storage_path' => 'gallery/seeded/media3.mp4',
                'slug' => 'fsrp-gallery-media-3',
                'title' => 'FSRP Program Activity Video Two',
                'media_type' => 'video',
                'category' => 'workshops',
                'caption' => 'Video from FSRP public communication and program activity.',
                'description' => 'A public gallery video documenting FSRP communication, activity visibility, and implementation progress.',
                'alt_text' => 'FSRP program communication video',
                'thumbnail_path' => 'gallery/seeded/media1.jpeg',
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            $sourcePath = database_path('seeders/data/gallery/' . $item['source']);

            if (! File::exists($sourcePath)) {
                $this->command?->warn("Gallery seed media missing: {$sourcePath}");
                continue;
            }

            Storage::disk('public')->put($item['storage_path'], File::get($sourcePath));
            File::ensureDirectoryExists(public_path('storage/' . dirname($item['storage_path'])));
            File::copy($sourcePath, public_path('storage/' . $item['storage_path']));

            GalleryMedia::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'media_type' => $item['media_type'],
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'caption' => $item['caption'],
                    'alt_text' => $item['alt_text'],
                    'file_path' => $item['storage_path'],
                    'thumbnail_path' => $item['thumbnail_path'] ?? null,
                    'file_name' => $item['source'],
                    'mime_type' => File::mimeType($sourcePath),
                    'file_size_bytes' => File::size($sourcePath),
                    'status' => 'published',
                    'is_featured' => $item['is_featured'],
                    'sort_order' => $item['sort_order'],
                    'submitted_at' => $publishedAt,
                    'approved_at' => $publishedAt,
                    'published_at' => $publishedAt,
                    'review_notes' => 'Seeded from the bundled FSRP gallery media set.',
                ]
            );
        }
    }
}
