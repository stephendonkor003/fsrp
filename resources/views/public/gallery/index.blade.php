<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | FSRP Eastern and Southern Africa</title>
    <meta name="description" content="Approved FSRP public gallery featuring images and videos from food-system resilience events, field visits, trainings, and implementation activities across Eastern and Southern Africa.">
    <meta name="keywords" content="FSRP gallery, Food System Resilience Program, Eastern and Southern Africa, food security photos, resilience field visits, FSRP events, implementation activities">
    <meta name="author" content="Food System Resilience Program (FSRP) for Eastern and Southern Africa">
    <link rel="canonical" href="{{ route('gallery.index') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Gallery | FSRP Eastern and Southern Africa">
    <meta property="og:description" content="Approved FSRP images and videos from events, field visits, trainings, and implementation activities across Eastern and Southern Africa.">
    <meta property="og:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <meta property="og:url" content="{{ route('gallery.index') }}">
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Gallery | FSRP Eastern and Southern Africa">
    <meta name="twitter:description" content="Approved FSRP images and videos from events, field visits, trainings, and implementation activities across Eastern and Southern Africa.">
    <meta name="twitter:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <link rel="icon" href="{{ asset('assets/images/au.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <style>
        :root {
            --au-green: #006B3F;
            --au-green-dark: #004d2e;
            --gold: #fbbc05;
            --light: #f0f5f1;
            --ink: #14231b;
            --muted: #617466;
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: var(--light); color: var(--ink); }

        .gallery-hero {
            min-height: 340px;
            padding: 92px 24px 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            background:
                linear-gradient(135deg, rgba(0, 53, 31, .94), rgba(0, 107, 63, .76)),
                url('{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}') center/cover no-repeat;
        }

        .gallery-hero h1 { margin: 0 0 12px; color: var(--gold); font-size: 2.8rem; font-weight: 800; }
        .gallery-hero p { max-width: 760px; margin: 0 auto; line-height: 1.75; color: rgba(255,255,255,.92); }

        .filter-wrap { max-width: 1160px; margin: -44px auto 0; padding: 0 24px; position: relative; z-index: 10; }
        .filter-bar {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0,0,0,.13);
            padding: 20px 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar input,
        .filter-bar select {
            flex: 1;
            min-width: 175px;
            border: 1px solid #ccd8cf;
            border-radius: 8px;
            padding: 11px 14px;
            font: inherit;
            font-size: .95rem;
            background: #f7faf8;
            outline: none;
        }
        .filter-bar input:focus,
        .filter-bar select:focus { border-color: var(--au-green); background: #fff; }
        .filter-bar button,
        .filter-clear {
            padding: 11px 22px;
            border-radius: 8px;
            border: 0;
            font-weight: 700;
            font-size: .95rem;
            text-decoration: none;
            white-space: nowrap;
        }
        .filter-bar button { background: var(--au-green); color: #fff; cursor: pointer; }
        .filter-clear { border: 1.5px solid #ccd8cf; color: #4a6355; background: #fff; }

        .content-wrap { max-width: 1160px; margin: 40px auto 64px; padding: 0 24px; }
        .gallery-summary { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .summary-chip {
            background: #fff;
            border: 1px solid #dce8e1;
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--muted);
            font-size: .9rem;
            font-weight: 700;
        }
        .summary-chip strong { color: var(--au-green-dark); }

        .gallery-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .gallery-card {
            background: #fff;
            border: 1px solid #e0ebe5;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            display: flex;
            flex-direction: column;
        }
        .gallery-media {
            position: relative;
            aspect-ratio: 4 / 3;
            background: #dce8e1;
            overflow: hidden;
        }
        .gallery-media img,
        .gallery-media video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .gallery-media video { background: #0f172a; }
        .type-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0, 53, 31, .88);
            color: #fff;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 800;
            text-transform: capitalize;
        }
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--gold);
            color: #172018;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 800;
        }
        .gallery-body { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .gallery-category {
            color: var(--au-green);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: .06em;
            font-size: .72rem;
        }
        .gallery-title { margin: 0; font-size: 1.05rem; line-height: 1.35; color: #102018; }
        .gallery-description { margin: 0; color: #4a6355; line-height: 1.6; font-size: .92rem; flex: 1; }
        .gallery-meta { color: #7a9384; font-size: .82rem; }

        .gallery-empty {
            grid-column: 1 / -1;
            background: #fff;
            border: 1px solid #e0ebe5;
            border-radius: 8px;
            padding: 60px 40px;
            text-align: center;
        }
        .gallery-empty h2 { color: var(--au-green); margin: 0 0 10px; }
        .gallery-empty p { color: #6b8676; margin: 0; }
        .pagination-wrap { margin-top: 36px; display: flex; justify-content: center; }
        footer.footer { margin-top: 0; }

        @media (max-width: 960px) {
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            .gallery-hero { padding-top: 84px; }
            .gallery-hero h1 { font-size: 2rem; }
            .gallery-grid { grid-template-columns: 1fr; }
            .filter-wrap { margin-top: -30px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar input,
            .filter-bar select { min-width: 0; }
        }
    </style>
</head>
<body>
<x-public-header active="gallery" language-style="gallery" />

<section class="gallery-hero">
    <div>
        <h1>Gallery</h1>
        <p>Approved FSRP images and videos from program events, field missions, trainings, workshops, and implementation activities.</p>
    </div>
</section>

<div class="filter-wrap">
    <form class="filter-bar" method="GET" action="{{ route('gallery.index') }}">
        <input name="q" value="{{ request('q') }}" placeholder="Search gallery..." aria-label="Search gallery">
        <select name="type" aria-label="Media type">
            <option value="">All media</option>
            <option value="image" @selected(request('type') === 'image')>Images</option>
            <option value="video" @selected(request('type') === 'video')>Videos</option>
        </select>
        <select name="category" aria-label="Category">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected(request('category') === $cat)>
                    {{ \App\Models\GalleryMedia::categories()[$cat] ?? ucfirst(str_replace('_', ' ', $cat)) }}
                </option>
            @endforeach
        </select>
        <button type="submit">Search</button>
        @if(request('q') || request('type') || request('category'))
            <a class="filter-clear" href="{{ route('gallery.index') }}">Clear</a>
        @endif
    </form>
</div>

<main class="content-wrap">
    <div class="gallery-summary">
        <span class="summary-chip"><strong>{{ $mediaItems->total() }}</strong> approved items</span>
        <span class="summary-chip"><strong>{{ $typeCounts['image'] }}</strong> images</span>
        <span class="summary-chip"><strong>{{ $typeCounts['video'] }}</strong> videos</span>
    </div>

    <section class="gallery-grid">
        @forelse($mediaItems as $media)
            <article class="gallery-card">
                <div class="gallery-media">
                    @if($media->isImage())
                        <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->alt_text ?: $media->title }}" loading="lazy">
                        </a>
                    @else
                        <video controls preload="metadata"
                            @if($media->thumbnail_path) poster="{{ asset('storage/' . $media->thumbnail_path) }}" @endif>
                            <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->mime_type ?: 'video/mp4' }}">
                        </video>
                    @endif
                    <span class="type-badge">{{ $media->media_type }}</span>
                    @if($media->is_featured)
                        <span class="featured-badge">Featured</span>
                    @endif
                </div>
                <div class="gallery-body">
                    <span class="gallery-category">{{ \App\Models\GalleryMedia::categories()[$media->category] ?? str_replace('_', ' ', $media->category) }}</span>
                    <h2 class="gallery-title">{{ $media->title }}</h2>
                    @if($media->caption || $media->description)
                        <p class="gallery-description">{{ $media->caption ?: \Illuminate\Support\Str::limit($media->description, 150) }}</p>
                    @endif
                    <div class="gallery-meta">
                        {{ optional($media->captured_at ?: $media->published_at)->format('d M Y') }}
                        @if($media->creator?->name)
                            &middot; {{ $media->creator->name }}
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="gallery-empty">
                <h2>No approved gallery media yet</h2>
                <p>Published FSRP images and videos will appear here once approved by the back office.</p>
            </div>
        @endforelse
    </section>

    @if($mediaItems->hasPages())
        <div class="pagination-wrap">
            {{ $mediaItems->links() }}
        </div>
    @endif
</main>

<footer id="contact" class="footer">
    <div class="footer-content">
        <div class="footer-logo">
            <h3>FSRP<span> &middot; Administration</span></h3>
            <p>Food System Resilience Program (FSRP) for Eastern and Southern Africa - supporting program coordination, public communication, and evidence-based implementation.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="{{ route('landing.index') }}">Home</a>
            <a href="{{ route('news.index') }}">News &amp; Updates</a>
            <a href="{{ route('gallery.index') }}">Gallery</a>
            <a href="{{ route('events') }}">Events</a>
        </div>
        <div class="footer-contact">
            <h4>Contact</h4>
            <p>Email: fsrpinfo@africanunion.org</p>
            <p>&copy; 2026 Food System Resilience Program (FSRP) for Eastern and Southern Africa</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Supporting FSRP coordination, governance, monitoring, reporting, and evidence-based decision-making across participating countries.</p>
    </div>
</footer>

<script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.lang-switcher.open').forEach(function(el) {
            if (!el.contains(e.target)) el.classList.remove('open');
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.lang-switcher.open').forEach(function(el) {
                el.classList.remove('open');
            });
        }
    });
</script>
</body>
</html>
