<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('public_pages.procurement_page_title') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('public_pages.procurement_meta_description') }}">
    <meta name="keywords" content="FSRP procurements, Food System Resilience Program procurement, Eastern and Southern Africa, food security tenders, vendor opportunities, procurement calls">
    <meta name="author" content="Food System Resilience Program (FSRP) for Eastern and Southern Africa">
    <link rel="canonical" href="{{ route('public.procurement.index') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('public_pages.procurement_page_title') }}">
    <meta property="og:description" content="{{ __('public_pages.procurement_meta_description') }}">
    <meta property="og:image" content="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}">
    <meta property="og:url" content="{{ route('public.procurement.index') }}">
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('public_pages.procurement_page_title') }}">
    <meta name="twitter:description" content="{{ __('public_pages.procurement_meta_description') }}">
    <meta name="twitter:image" content="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f7f4f2;
            margin: 0;
        }

        /* ===== HEADER ===== */
        .events-header {
            background: url('/assets/three.webp') center/cover no-repeat;
            height: 360px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .events-header::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(82, 43, 57, 0.75);
        }

        .header-content {
            position: relative;
            text-align: center;
            max-width: 800px;
            padding: 0 1rem;
        }

        .header-content h1 {
            color: #fbbc05;
            font-size: 2.5rem;
            margin-bottom: .5rem;
        }

        .header-content p {
            font-size: 1.05rem;
            line-height: 1.6;
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            background: #fff;
            margin: -50px auto 2.5rem;
            max-width: 1100px;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            display: flex;
            gap: 1rem;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .filter-bar input {
            padding: .8rem 1rem;
            width: 360px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .filter-bar button {
            background: #a70d53;
            color: #fff;
            border: none;
            padding: .8rem 1.6rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .filter-bar button:hover {
            background: #e16435;
        }

        /* ===== GRID ===== */
        .events-container {
            max-width: 1200px;
            margin: 0 auto 4rem;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .event-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
            transition: transform .3s ease;
        }

        .event-card:hover {
            transform: translateY(-6px);
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.2rem 1.3rem;
        }

        .card-body h4 {
            color: #522b39;
            margin-bottom: .5rem;
            font-size: 1.1rem;
        }

        .card-body p {
            color: #555;
            font-size: .95rem;
            line-height: 1.5;
        }

        .btn-view {
            display: inline-block;
            margin-top: 1rem;
            background: #a70d53;
            color: #fff;
            padding: .6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: .3s;
        }

        .btn-view:hover {
            background: #e16435;
        }

        @media (max-width: 992px) {
            .events-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .events-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    @php
        $procurementImages = collect(File::files(public_path('assets/images')))
            ->filter(fn($file) => in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'webp']))
            ->shuffle()
            ->values();
    @endphp
<x-public-header active="procurement" language-style="procurement" />



    <section class="events-header">
        <div class="header-content">
            <h1>{{ __('public_pages.procurement_hero_title') }}</h1>
            <p>
                {{ __('public_pages.procurement_hero_intro') }}
            </p>
        </div>
    </section>
    <br>
    <br>
    <br>

    <div class="filter-bar">
        <input type="text" id="searchInput" placeholder="{{ __('public_pages.procurement_search_placeholder') }}">
        <button onclick="filterProcurements()">{{ __('public_pages.procurement_search_button') }}</button>
    </div>


    <section class="events-container" id="procurementContainer">

        @forelse($procurements as $procurement)
            <div class="event-card">

                @php
                    $image = $procurementImages[$loop->index % $procurementImages->count()];
                @endphp

                <img src="{{ asset('assets/images/' . $image->getFilename()) }}" alt="{{ __('public_pages.procurement_image_alt') }}">

                <div class="card-body">
                    <h4>{{ $procurement->title }}</h4>

                    <p>
                        {{ \Illuminate\Support\Str::limit(strip_tags($procurement->description), 130) }}
                    </p>

                    <a href="{{ route('public.procurement.show', $procurement->slug) }}" class="btn-view">
                        {{ __('public_pages.procurement_view_details_apply') }}
                    </a>
                </div>
            </div>

        @empty
            <p style="grid-column:1/-1;text-align:center;font-weight:600;">
                {{ __('public_pages.procurement_empty') }}
            </p>
        @endforelse

    </section>


    <script>
        function filterProcurements() {
            const search = document
                .getElementById('searchInput')
                .value
                .toLowerCase();

            document.querySelectorAll('.event-card').forEach(card => {
                card.style.display = card.innerText.toLowerCase().includes(search) ?
                    'block' :
                    'none';
            });
        }
    </script>

    <x-public-footer />


    <script src="assets/script.js"></script>

    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/69204852eba156195f5dae48/1jaj1l0r8';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>


</body>

</html>
