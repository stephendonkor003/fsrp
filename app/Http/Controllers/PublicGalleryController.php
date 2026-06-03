<?php

namespace App\Http\Controllers;

use App\Models\GalleryMedia;
use Illuminate\Http\Request;

class PublicGalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryMedia::published()->with('creator');

        if ($request->filled('type')) {
            $query->where('media_type', $request->string('type'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('q')) {
            $search = '%' . trim((string) $request->input('q')) . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('caption', 'like', $search);
            });
        }

        $mediaItems = $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = GalleryMedia::published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $typeCounts = [
            'image' => GalleryMedia::published()->where('media_type', 'image')->count(),
            'video' => GalleryMedia::published()->where('media_type', 'video')->count(),
        ];

        return view('public.gallery.index', compact('mediaItems', 'categories', 'typeCounts'));
    }
}
