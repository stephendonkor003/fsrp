<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $seoDescription = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 160);
        $seoImage = $post->cover_image_path ? asset('storage/' . $post->cover_image_path) : asset('assets/images/fsrp/water-food-resilience-2.jpg');
        $articleBody = preg_replace(
            '#<(p|div)\b[^>]*>(?:\s|&nbsp;|<br\s*/?>)*</\1>#i',
            '',
            $post->body
        ) ?? $post->body;
        $articleBody = preg_replace('#(?:<br\s*/?>\s*){3,}#i', '<br><br>', $articleBody) ?? $articleBody;
    @endphp
    <title>{{ $post->title }} | FSRP Eastern and Southern Africa News</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="FSRP news, {{ $post->title }}, Food System Resilience Program, Eastern and Southern Africa, food security, resilience implementation">
    <meta name="author" content="{{ $post->creator?->name ?? 'FSRP Communications' }}">
    <link rel="canonical" href="{{ route('news.show', $post) }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:url" content="{{ route('news.show', $post) }}">
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa">
    @if($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link rel="icon" href="{{ asset('assets/images/au.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <style>
        :root {
            --au-green:      #006B3F;
            --au-green-dark: #004d2e;
            --au-green-light:#009A44;
            --gold:          #fbbc05;
            --light:         #f0f5f1;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: var(--light); color: #1a2e22; }

        /* ── NAVBAR ── */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 5%; background: var(--au-green);
            box-shadow: 0 2px 12px rgba(0,0,0,.2);
            position: sticky; top: 0; z-index: 100;
        }
        .nav-links a { margin-left: 20px; font-weight: 500; color: #fff; text-decoration: none; font-size: .9rem; transition: color .2s; }
        .nav-links a.active, .nav-links a:hover { color: var(--gold); }

        /* ── ARTICLE HERO ── */
        .article-hero {
            position: relative; min-height: 320px;
            display: flex; flex-direction: column; justify-content: flex-end;
            overflow: hidden;
        }
        .hero-img {
            position: absolute; inset: 0;
            width: 100%; height: 100%; object-fit: cover;
        }
        .hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,30,15,.92) 0%, rgba(0,60,30,.55) 55%, rgba(0,0,0,.15) 100%);
        }
        .hero-content {
            position: relative; z-index: 2;
            max-width: 900px; margin: 0 auto; padding: 36px 24px 32px;
            color: #fff; width: 100%;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--gold); text-decoration: none; font-size: .85rem;
            font-weight: 600; margin-bottom: 14px; opacity: .9;
            transition: opacity .2s;
        }
        .back-link:hover { opacity: 1; }
        .article-badge {
            display: inline-block; background: var(--gold); color: #1a2e22;
            padding: 5px 14px; border-radius: 99px; font-size: .78rem;
            font-weight: 800; text-transform: capitalize; margin-bottom: 14px;
        }
        .hero-content h1 {
            font-family: 'Merriweather', Georgia, serif;
            font-size: 2rem; line-height: 1.28; margin: 0 0 12px; color: #fff;
            text-wrap: balance;
        }
        .hero-meta { font-size: .9rem; color: #b8d4c5; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .hero-meta-sep { opacity: .5; }

        /* ── LAYOUT ── */
        .article-layout {
            max-width: 900px; margin: 0 auto; padding: 28px 20px 48px;
        }

        /* ── ARTICLE CARD ── */
        .article-card {
            background: #fff; border-radius: 14px; border: 1px solid #e0ebe5;
            padding: 34px clamp(24px, 6vw, 64px); box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .article-body {
            max-width: 720px; margin: 0 auto;
            font-family: 'Inter', Arial, sans-serif;
            font-size: 1rem; line-height: 1.72; color: #2a3d30;
            overflow-wrap: anywhere;
        }
        .article-body > :first-child { margin-top: 0; }
        .article-body > :last-child { margin-bottom: 0; }
        .article-body h2 { font-size: 1.35rem; line-height: 1.35; margin: 1.6rem 0 .7rem; color: var(--au-green); }
        .article-body h3 { font-size: 1.1rem; line-height: 1.4; margin: 1.3rem 0 .6rem; color: var(--au-green-dark); }
        .article-body p { margin: 0 0 1rem; }
        .article-body p:empty, .article-body div:empty { display: none; }
        .article-body a { color: var(--au-green); }
        .article-body ul, .article-body ol { padding-left: 1.4rem; margin: 0 0 1rem; }
        .article-body li + li { margin-top: .35rem; }
        .article-body blockquote {
            border-left: 4px solid var(--gold); margin: 1.5rem 0;
            padding: .8rem 1.2rem; background: #f7faf8; color: #3a5040;
            font-style: italic;
        }
        .article-body img {
            display: block; max-width: 100%; height: auto; border-radius: 12px; margin: 1rem auto;
            box-shadow: 0 8px 22px rgba(0,0,0,.08);
        }
        .article-body table {
            width: 100%; border-collapse: collapse; margin: 1.4rem 0;
            font-family: 'Inter', Arial, sans-serif; font-size: .95rem;
        }
        .article-body th, .article-body td {
            border: 1px solid #dbe7df; padding: .7rem .8rem; text-align: left;
        }
        .article-body th { background: #f0f5f1; color: var(--au-green-dark); }
        .article-body figure { margin: 1.5rem 0; }
        .article-body figcaption { color: #6b8676; font-size: .88rem; text-align: center; }
        .article-body pre {
            background: #102018; color: #f8fafc; border-radius: 10px;
            overflow-x: auto; padding: 1rem; font-family: monospace;
        }

        /* ── TAGS ── */
        .tags-row { max-width: 720px; margin: 24px auto 0; padding-top: 18px; border-top: 1px solid #e8f0eb; display: flex; flex-wrap: wrap; gap: 8px; }
        .tag-chip {
            padding: 5px 12px; border-radius: 99px; border: 1.5px solid #c8d8ce;
            color: #3a5040; font-size: .82rem; font-weight: 500; text-decoration: none;
            transition: all .2s;
        }
        .tag-chip:hover { background: var(--au-green); color: #fff; border-color: var(--au-green); }

        /* ── SHARE ROW ── */
        .share-row { max-width: 720px; margin: 20px auto 0; padding-top: 16px; border-top: 1px solid #e8f0eb; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .share-row span { font-size: .85rem; font-weight: 600; color: #5a7065; }
        .share-btn {
            padding: 7px 16px; border-radius: 8px; border: 1.5px solid #c8d8ce;
            background: #fff; color: #3a5040; font-size: .82rem; font-weight: 600;
            text-decoration: none; cursor: pointer; transition: all .2s; display: inline-block;
        }
        .share-btn:hover { background: var(--au-green); color: #fff; border-color: var(--au-green); }

        /* ── SIDEBAR ── */
        .sidebar {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 18px; margin-top: 20px; align-items: start;
        }
        .side-card {
            background: #fff; border-radius: 14px; border: 1px solid #e0ebe5;
            padding: 22px 24px; box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }
        .side-card h3 { margin: 0 0 14px; font-size: 1rem; color: var(--au-green); border-bottom: 2px solid var(--gold); padding-bottom: 8px; }

        /* Downloads */
        .download-item {
            display: block; padding: 12px 14px; border-radius: 10px;
            border: 1px solid #e0ebe5; margin-bottom: 10px; color: #1a2e22;
            text-decoration: none; transition: all .2s; background: #f7faf8;
        }
        .download-item:last-child { margin-bottom: 0; }
        .download-item:hover { background: #e8f5ee; border-color: var(--au-green); }
        .download-item strong { display: block; font-size: .9rem; margin-bottom: 2px; }
        .download-item small { color: #7a9384; font-size: .78rem; }
        .dl-icon { float: right; color: var(--au-green); font-size: 1.1rem; margin-top: 2px; }

        /* Subscribe */
        .side-subscribe { background: var(--au-green-dark); border-color: transparent; }
        .side-subscribe h3 { color: var(--gold); border-bottom-color: rgba(251,188,5,.4); }
        .side-subscribe p { color: #c2ddd0; font-size: .88rem; line-height: 1.6; margin: 0 0 14px; }
        .sub-input { width: 100%; border: none; border-radius: 8px; padding: 11px 14px; font: inherit; font-size: .9rem; margin-bottom: 10px; outline: none; }
        .sub-btn {
            width: 100%; padding: 11px; border: none; border-radius: 8px;
            background: var(--gold); color: #1a2e22; font-weight: 700;
            cursor: pointer; font-size: .9rem; transition: opacity .2s;
        }
        .sub-btn:hover { opacity: .88; }

        /* Related */
        .related-item {
            display: block; padding: 12px 0; border-bottom: 1px solid #e8f0eb;
            text-decoration: none; color: #1a2e22; transition: color .2s;
        }
        .related-item:last-child { border-bottom: none; padding-bottom: 0; }
        .related-item:first-child { padding-top: 0; }
        .related-item:hover { color: var(--au-green); }
        .related-item span { display: block; font-weight: 600; font-size: .9rem; line-height: 1.4; margin-bottom: 3px; }
        .related-item small { font-size: .78rem; color: #7a9384; }

        /* ── ALERT ── */
        .alert-success {
            max-width: 1160px; margin: 20px auto 0; padding: 14px 24px;
            border-radius: 10px; background: #dcfce7; color: #166534;
            border: 1px solid #86efac; font-weight: 500;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero-content h1 { font-size: 1.7rem; }
            .article-hero { min-height: 280px; }
            .article-card { padding: 24px 20px; }
        }
        @media (max-width: 600px) {
            .article-layout { padding: 20px 14px 36px; }
            .article-card { border-radius: 10px; }
            .article-body { font-size: .98rem; line-height: 1.68; }
            .sidebar { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<x-public-header active="news" language-style="news-show" />

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<!-- ── ARTICLE HERO ── -->
<section class="article-hero">
    <img class="hero-img"
         src="{{ $post->cover_image_path ? asset('storage/' . $post->cover_image_path) : asset('assets/images/au1.jpg') }}"
         alt="{{ $post->title }}">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <a class="back-link" href="{{ route('news.index') }}">&#8592; Back to News</a>
        <div class="article-badge">{{ str_replace('_', ' ', $post->category) }}</div>
        <h1>{{ $post->title }}</h1>
        <div class="hero-meta">
            <span>{{ optional($post->published_at)->format('d M Y') }}</span>
            <span class="hero-meta-sep">|</span>
            <span>{{ $post->creator?->name ?? 'FSRP Communications' }}</span>
            @if($post->attachments->count())
                <span class="hero-meta-sep">|</span>
                <span>{{ $post->attachments->count() }} {{ \Illuminate\Support\Str::plural('attachment', $post->attachments->count()) }}</span>
            @endif
        </div>
    </div>
</section>

<!-- ── LAYOUT ── -->
<div class="article-layout">

    <!-- LEFT: ARTICLE BODY -->
    <main>
        <article class="article-card">
            <div class="article-body">{!! $articleBody !!}</div>

            @if($post->tags && count($post->tags))
                <div class="tags-row">
                    @foreach($post->tags as $tag)
                        <a class="tag-chip" href="{{ route('news.index', ['q' => $tag]) }}">{{ $tag }}</a>
                    @endforeach
                </div>
            @endif

            <div class="share-row">
                <span>Share:</span>
                <a class="share-btn" href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener">Twitter / X</a>
                <a class="share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener">LinkedIn</a>
                <button class="share-btn" onclick="navigator.clipboard.writeText(window.location.href).then(()=>this.textContent='Copied!').catch(()=>{})">Copy Link</button>
            </div>
        </article>
    </main>

    <!-- RIGHT: SIDEBAR -->
    <aside class="sidebar">

        <!-- Downloads -->
        @if($post->attachments->isNotEmpty())
            <div class="side-card">
                <h3>Downloads</h3>
                @foreach($post->attachments as $attachment)
                <a class="download-item" href="{{ route('news.attachments.download', [$post, $attachment]) }}">
                    <span class="dl-icon">&#8659;</span>
                    <strong>{{ $attachment->title }}</strong>
                    <small>{{ $attachment->file_name }}</small>
                </a>
                @endforeach
            </div>
        @endif

        <!-- Subscribe -->
        <div class="side-card side-subscribe">
            <h3>Stay Informed</h3>
            <p>Subscribe and receive new approved FSRP news directly in your inbox.</p>
            <form method="POST" action="{{ route('news.subscribe') }}">
                @csrf
                <input class="sub-input" type="email" name="email" placeholder="your@email.com" required>
                <button class="sub-btn" type="submit">Subscribe</button>
            </form>
        </div>

        <!-- Related -->
        @if($related->count())
            <div class="side-card">
                <h3>Related Updates</h3>
                @foreach($related as $item)
                    <a class="related-item" href="{{ route('news.show', $item) }}">
                        <span>{{ $item->title }}</span>
                        <small>{{ optional($item->published_at)->format('d M Y') }}</small>
                    </a>
                @endforeach
            </div>
        @endif

    </aside>
</div>

<!-- ── FOOTER ── -->
<footer id="contact" class="footer">
    <div class="footer-content">
        <div class="footer-logo">
            <h3>FSRP<span> · Administration</span></h3>
            <p>Food System Resilience Program (FSRP) for Eastern and Southern Africa - supporting food-security preparedness, regional market coordination, public communication, and accountable implementation.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="{{ route('landing.index') }}">Home</a>
            <a href="{{ route('news.index') }}">News &amp; Updates</a>
            <a href="{{ route('careers.index') }}">Careers</a>
            <a href="{{ route('events') }}">Events</a>
        </div>
        <div class="footer-contact">
            <h4>Contact</h4>
            <p>Email: fsrpinfo@africanunion.org</p>
            <p>&copy; 2026 Food System Resilience Program (FSRP) for Eastern and Southern Africa</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Supporting food-system resilience coordination, member-state reporting, and evidence-based implementation across Eastern and Southern Africa.</p>
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
