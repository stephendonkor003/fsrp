<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#006B3F" />

    <title>Events & Webinars | FSRP - Food System Resilience Program for Eastern and Southern Africa</title>

    <meta name="description" content="Engage with FSRP webinars and events on food-security preparedness, resilient production, regional markets, safeguards, procurement, and program implementation across Eastern and Southern Africa." />
    <meta name="keywords" content="FSRP events, FSRP webinars, Food System Resilience Program, Eastern and Southern Africa, food security events, regional markets, resilience implementation">
    <meta name="author" content="FSRP Secretariat" />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="{{ route('events') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Events & Webinars | FSRP Eastern and Southern Africa" />
    <meta property="og:description" content="Engage with FSRP webinars and events on food-security preparedness, resilient production, regional markets, safeguards, procurement, and program implementation across Eastern and Southern Africa." />
    <meta property="og:image" content="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}" />
    <meta property="og:url" content="{{ route('events') }}" />
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Events & Webinars | FSRP Eastern and Southern Africa" />
    <meta name="twitter:description" content="Engage with FSRP webinars and events on food-security preparedness, resilient production, regional markets, safeguards, procurement, and program implementation across Eastern and Southern Africa." />
    <meta name="twitter:image" content="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}" />

    <link rel="icon" href="{{ asset('assets/images/au3.jpg') }}" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}" />

    <style>
        :root {
            --au-green:      #006B3F;
            --au-green-dark: #004d2e;
            --au-green-light:#009A44;
            --gold:          #fbbc05;
            --orange:        #e16435;
            --light:         #f0f4f0;
        }

        body {
            font-family: "Inter", sans-serif;
            background: var(--light);
            color: #333;
            margin: 0;
        }

        /* ===== PAGE HERO ===== */
        .events-hero {
            position: relative;
            background: url('{{ asset('assets/images/au3.jpg') }}') center/cover no-repeat;
            height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }

        .events-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(0, 77, 46, 0.88), rgba(0, 0, 0, 0.65));
        }

        .events-hero .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 1.5rem;
        }

        .events-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gold);
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .events-hero p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: rgba(255,255,255,0.90);
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            background: #fff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.10);
            border-radius: 14px;
            max-width: 800px;
            margin: -50px auto 3rem;
            padding: 1.4rem 1.8rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        .filter-bar input {
            flex: 1;
            min-width: 220px;
            padding: 0.8rem 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.25s;
        }

        .filter-bar input:focus {
            outline: none;
            border-color: var(--au-green);
            box-shadow: 0 0 0 3px rgba(0,107,63,0.10);
        }

        .filter-bar button {
            background: var(--au-green);
            color: #fff;
            border: none;
            padding: 0.85rem 1.8rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.25s;
            white-space: nowrap;
        }

        .filter-bar button:hover {
            background: var(--au-green-dark);
        }

        /* ===== EVENTS GRID ===== */
        .events-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto 5rem;
            padding: 0 2rem;
        }

        .event-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.09);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid var(--au-green);
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 28px rgba(0, 107, 63, 0.16);
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-card .card-body {
            padding: 1.3rem 1.4rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .event-card h4 {
            color: var(--au-green);
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.4;
            margin: 0;
        }

        .event-date {
            color: var(--au-green-light);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .event-card p {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        .event-card .card-links {
            margin-top: auto;
            padding-top: 0.8rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-view {
            display: inline-block;
            background: var(--au-green);
            color: #fff;
            padding: 0.5rem 1.1rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: background 0.25s;
            width: fit-content;
        }

        .btn-view:hover {
            background: var(--au-green-light);
            color: #fff;
        }

        .events-empty {
            grid-column: 1 / -1;
            background: #fff;
            border-radius: 14px;
            padding: 2.5rem;
            text-align: center;
            border-top: 4px solid var(--au-green);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.09);
        }

        .events-empty h2 {
            color: var(--au-green);
            margin: 0 0 0.75rem;
        }

        .events-empty p {
            color: #617468;
            margin: 0;
        }

        .events-result-note {
            max-width: 1200px;
            margin: -1.5rem auto 2rem;
            padding: 0 2rem;
            color: #617468;
            font-size: 0.95rem;
        }

        .pagination-wrap {
            max-width: 1200px;
            margin: -3rem auto 4rem;
            padding: 0 2rem;
        }

        /* ===== MODAL ===== */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.70);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            max-width: 560px;
            width: 90%;
            text-align: center;
            position: relative;
            border-top: 4px solid var(--au-green);
        }

        .modal-box h3 {
            color: var(--au-green);
            margin-bottom: 0.8rem;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 14px;
            background: none;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: var(--au-green);
            line-height: 1;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .events-container { grid-template-columns: repeat(2, 1fr); }
            .events-hero h1 { font-size: 2rem; }
        }

        @media (max-width: 600px) {
            .events-container {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .filter-bar {
                margin: -40px 1rem 2rem;
                padding: 1rem;
            }

            .events-hero h1 { font-size: 1.7rem; }
            .events-hero { height: 300px; }
        }
    </style>
</head>

<body>
    <x-public-header active="events" language-style="events" />

    <!-- ====== HERO ====== -->
    <section class="events-hero">
        <div class="hero-inner">
            <h1>Webinars &amp; Strategic Events</h1>
            <p>
                Engage with FSRP implementation teams, member states, and partners through webinars
                and strategic events focused on food-security preparedness, resilient production,
                regional markets, safeguards, procurement, and evidence-based decision-making.
            </p>
        </div>
    </section>

    <!-- ====== FILTER BAR ====== -->
    <form class="filter-bar" method="GET" action="{{ route('events') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search events by keyword..." id="searchInput" />
        <button type="submit">Search Events</button>
    </form>

    @if(request('q'))
        <p class="events-result-note">
            Showing {{ $events->total() }} {{ \Illuminate\Support\Str::plural('result', $events->total()) }}
            for "<strong>{{ request('q') }}</strong>".
            <a href="{{ route('events') }}">Clear search</a>
        </p>
    @endif

    <!-- ====== EVENTS GRID ====== -->
    <section class="events-container" id="eventsContainer">
        @forelse($events as $event)
            @php
                $fallbackImages = [
                    'assets/images/au1.jpg',
                    'assets/images/au2.webp',
                    'assets/images/au3.jpg',
                    'assets/images/au4.jpg',
                    'assets/images/au5.jpg',
                    'assets/images/au6.jpg',
                    'assets/images/au7.jpg',
                ];
                $coverUrl = $event->cover_image_path
                    ? asset('storage/' . $event->cover_image_path)
                    : asset($fallbackImages[$loop->index % count($fallbackImages)]);
            @endphp
            <article class="event-card">
                <img src="{{ $coverUrl }}" alt="{{ $event->title }}" loading="lazy">
                <div class="card-body">
                    <p class="event-date">
                        {{ optional($event->published_at)->format('F j, Y') }}
                        @if($event->tags && count($event->tags))
                            &mdash; {{ collect($event->tags)->take(2)->implode(' | ') }}
                        @endif
                    </p>
                    <h4>{{ $event->title }}</h4>
                    <p>{{ $event->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($event->body), 160) }}</p>
                    <div class="card-links">
                        <a href="{{ route('news.show', $event) }}" class="btn-view">View Details</a>
                        @foreach($event->attachments->take(2) as $attachment)
                            <a href="{{ route('news.attachments.download', [$event, $attachment]) }}" class="btn-view">
                                {{ $attachment->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </article>
        @empty
            <div class="events-empty">
                <h2>No approved events yet</h2>
                <p>Approved back-office webinar and event posts will appear here once published.</p>
            </div>
        @endforelse
    </section>

    @if($events->hasPages())
        <div class="pagination-wrap">
            {{ $events->links() }}
        </div>
    @endif

    <!-- ====== FOOTER ====== -->
    <footer id="contact" class="footer" role="contentinfo">
        <div class="footer-content">
            <div class="footer-logo">
                <h3>FSRP<span> Administration</span></h3>
                <p>
                    Food System Resilience Program (FSRP) for Eastern and Southern Africa - supporting food-security
                    preparedness, regional market coordination, public communication, and accountable implementation.
                </p>
            </div>

            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="{{ route('landing.index') }}">Home</a>
                <a href="{{ route('events') }}">Events &amp; Webinars</a>
                <a href="{{ route('careers.index') }}">Careers</a>
                <a href="{{ route('public.procurement.index') }}">Procurement</a>
                <a href="#contact">Contact</a>
            </div>

            <div class="footer-contact">
                <h4>Contact</h4>
                <p>Email: fsrpinfo@africanunion.org</p>
                <p>&copy; {{ date('Y') }} Food System Resilience Program (FSRP) for Eastern and Southern Africa</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>Supporting food-system resilience coordination, member-state reporting, and evidence-based implementation across Eastern and Southern Africa.</p>
        </div>
    </footer>

    <script>
        function openMobileNav() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navOverlay');
            const btn = document.getElementById('hamburgerBtn');
            nav.classList.add('open');
            overlay.style.display = 'block';
            requestAnimationFrame(() => overlay.classList.add('visible'));
            btn.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            const nav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navOverlay');
            const btn = document.getElementById('hamburgerBtn');
            nav.classList.remove('open');
            overlay.classList.remove('visible');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            setTimeout(() => { overlay.style.display = 'none'; }, 300);
        }

        function toggleMobileDropdown(btn) {
            const items = btn.nextElementSibling;
            const isOpen = items.classList.contains('open');
            document.querySelectorAll('.mobile-dropdown-items.open').forEach(el => el.classList.remove('open'));
            document.querySelectorAll('.mobile-dropdown-toggle.open').forEach(el => el.classList.remove('open'));
            if (!isOpen) { items.classList.add('open'); btn.classList.add('open'); }
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.lang-switcher.open').forEach(function(el) {
                if (!el.contains(e.target)) el.classList.remove('open');
            });
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeMobileNav();
                document.querySelectorAll('.lang-switcher.open').forEach(el => el.classList.remove('open'));
            }
        });
    </script>

    <script type="text/javascript">
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/69204852eba156195f5dae48/1jaj1l0r8';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>

</body>
</html>
