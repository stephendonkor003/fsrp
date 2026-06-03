<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('faq.page_title') }}</title>
    <meta name="description" content="{{ __('faq.meta_description') }}">
    <meta name="keywords" content="FSRP FAQ, Food System Resilience Program, Eastern and Southern Africa, food security questions, procurement FAQ, safeguards FAQ, monitoring and reporting">
    <meta name="author" content="Food System Resilience Program (FSRP) for Eastern and Southern Africa">
    <link rel="canonical" href="{{ route('applicants.faq') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('faq.page_title') }}">
    <meta property="og:description" content="{{ __('faq.meta_description') }}">
    <meta property="og:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <meta property="og:url" content="{{ route('applicants.faq') }}">
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('faq.page_title') }}">
    <meta name="twitter:description" content="{{ __('faq.meta_description') }}">
    <meta name="twitter:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <link rel="icon" href="{{ asset('assets/images/au.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <style>
        :root {
            --au-green: #006B3F;
            --au-green-dark: #004d2e;
            --au-green-soft: #e8f5ee;
            --gold: #fbbc05;
            --light: #f0f5f1;
        }

        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: var(--light);
            color: #1a2e22;
        }

        .faq-hero {
            min-height: 360px;
            background:
                linear-gradient(135deg, rgba(0, 77, 46, .94), rgba(0, 107, 63, .86)),
                url('{{ asset('assets/images/au3.jpg') }}') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 96px 24px 72px;
            color: #fff;
        }

        .faq-hero h1 {
            color: var(--gold);
            font-size: clamp(2rem, 4vw, 3.1rem);
            margin: 0 0 14px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .faq-hero p {
            max-width: 780px;
            margin: 0 auto;
            line-height: 1.75;
            color: rgba(255, 255, 255, .92);
        }

        .faq-wrap {
            max-width: 1120px;
            margin: -44px auto 64px;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        .faq-section {
            background: #fff;
            border: 1px solid #e0ebe5;
            border-radius: 8px;
            box-shadow: 0 12px 30px rgba(0, 77, 46, .1);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .faq-section h2 {
            margin: 0;
            padding: 18px 22px;
            background: var(--au-green-dark);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 0;
        }

        .faq-list {
            display: grid;
            gap: 1px;
            background: #e0ebe5;
        }

        .faq-item {
            background: #fff;
            padding: 22px;
        }

        .faq-item h3 {
            margin: 0 0 10px;
            color: var(--au-green-dark);
            font-size: 1rem;
            line-height: 1.45;
            letter-spacing: 0;
        }

        .faq-item p {
            margin: 0;
            color: #33483b;
            font-size: .96rem;
            line-height: 1.75;
        }

        [dir="rtl"] body {
            text-align: right;
        }

        [dir="rtl"] .faq-hero {
            text-align: center;
        }

        @media (max-width: 640px) {
            .faq-hero {
                min-height: 300px;
                padding: 86px 18px 58px;
            }

            .faq-wrap {
                padding: 0 16px;
                margin-bottom: 48px;
            }

            .faq-section h2,
            .faq-item {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>
</head>

<body>
    <x-public-header active="faq" language-style="faq" />

    <section class="faq-hero">
        <div>
            <h1>{{ __('faq.title') }}</h1>
            <p>{{ __('faq.intro') }}</p>
        </div>
    </section>

    @php
        $sections = trans('faq.sections');
        $sections = is_array($sections) ? $sections : [];
    @endphp

    <main class="faq-wrap">
        @foreach ($sections as $section)
            <section class="faq-section">
                <h2>{{ $section['heading'] ?? '' }}</h2>
                <div class="faq-list">
                    @foreach (($section['items'] ?? []) as $item)
                        <article class="faq-item">
                            <h3>{{ $item['q'] ?? '' }}</h3>
                            <p>{{ $item['a'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </main>

    <x-public-footer />
</body>

</html>
