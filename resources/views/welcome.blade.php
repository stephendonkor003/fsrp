<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <!-- Basic Meta -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('landing.page_title') }}</title>

    <meta name="description"
        content="The FSRP administrative portal supports coordination, procurement, finance, monitoring, reporting, and implementation oversight for the West Africa Food System Resilience Program." />

    <meta name="keywords"
        content="FSRP, West Africa Food System Resilience Program, food security preparedness, food system resilience, regional food markets, digital advisory services, agriculture procurement, program management, World Bank" />

    <meta name="author" content="Western and Central Africa - West Africa Food System Resilience Program (FSRP)" />
    <meta name="robots" content="index, follow" />
    <meta name="language" content="en" />
    <meta name="theme-color" content="#006B3F" />

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/au3.jpg') }}" type="image/png" />

    <!-- Open Graph -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="FSRP Administrative Portal - West Africa Food System Resilience Program" />
    <meta property="og:description"
        content="Administrative coordination, fiduciary oversight, procurement, monitoring, and reporting for the West Africa Food System Resilience Program." />
    <meta property="og:image" content="https://fsrp.africa/assets/images/au3.jpg" />
    <meta property="og:url" content="https://fsrp.africa/" />
    <meta property="og:site_name" content="FSRP" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="FSRP Administrative Portal - West Africa Food System Resilience Program" />
    <meta name="twitter:description"
        content="A controlled workspace for FSRP planning, procurement, finance, monitoring, reporting, and audit readiness." />
    <meta name="twitter:image" content="https://fsrp.africa/assets/images/au.png" />
    <meta name="twitter:site" content="@FSRP_WestAfrica" />

    <!-- Canonical URL -->
    <link rel="canonical" href="https://fsrp.africa/" />

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}?v={{ file_exists(public_path('assets/style.css')) ? filemtime(public_path('assets/style.css')) : time() }}" />

    <!-- RTL CSS for Arabic -->
    {!! app()->getLocale() === 'ar' ? '<link rel="stylesheet" href="' . asset('assets/css/rtl.css') . '">' : '' !!}

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "Organization",
      "name": "Western and Central Africa - West Africa Food System Resilience Program (FSRP)",
      "alternateName": "FSRP",
      "url": "https://fsrp.africa",
      "logo": "https://fsrp.africa/assets/images/au3.jpg",
      "description": "FSRP administrative portal for coordinating implementation, procurement, finance, monitoring, reporting, and oversight for food system resilience activities in participating countries.",
      "foundingLocation": {
        "@type": "Place",
        "name": "Africa"
      },
      "sameAs": [
        "https://www.linkedin.com/company/fsrp-west-africa",
        "https://twitter.com/FSRP_WestAfrica"
      ]
    }
    </script>
</head>




<body>
    <x-public-header active="home" language-style="landing" />

    <!-- ====== HERO SECTION ====== -->
    <section class="hero">
        <div class="overlay"></div>

        <div class="slider">
            <div class="slide active" style="background-image: url('{{ asset('assets/images/fsrp/water-food-resilience-1.jpg') }}');"></div>
            <div class="slide" style="background-image: url('{{ asset('assets/gallery/media1.jpeg') }}');"></div>
            <div class="slide video-slide">
                <video class="hero-video"
                    muted
                    playsinline
                    preload="auto"
                    controls
                    poster="{{ asset('assets/images/fsrp/water-food-resilience-1.jpg') }}">
                    <source src="{{ asset('assets/videos/fsrp-program-video.mp4') }}?v={{ file_exists(public_path('assets/videos/fsrp-program-video.mp4')) ? filemtime(public_path('assets/videos/fsrp-program-video.mp4')) : time() }}" type="video/mp4">
                </video>
            </div>
            <div class="slide video-slide">
                <video class="hero-video"
                    muted
                    playsinline
                    preload="auto"
                    controls
                    poster="{{ asset('assets/gallery/media1.jpeg') }}">
                    <source src="{{ asset('assets/gallery/media2.mp4') }}?v={{ file_exists(public_path('assets/gallery/media2.mp4')) ? filemtime(public_path('assets/gallery/media2.mp4')) : time() }}" type="video/mp4">
                </video>
            </div>
            <div class="slide video-slide">
                <video class="hero-video"
                    muted
                    playsinline
                    preload="auto"
                    controls
                    poster="{{ asset('assets/gallery/media1.jpeg') }}">
                    <source src="{{ asset('assets/gallery/media3.mp4') }}?v={{ file_exists(public_path('assets/gallery/media3.mp4')) ? filemtime(public_path('assets/gallery/media3.mp4')) : time() }}" type="video/mp4">
                </video>
            </div>
            <div class="slide" style="background-image: url('{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}');"></div>
            <div class="slide" style="background-image: url('{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}');"></div>
        </div>

        <div class="hero-content">
            <h1 id="typewriter" class="typing-text"></h1>
            <p id="hero-subtitle" class="hero-subtitle-text"></p>

            <a href="{{ route('login') }}" class="cta-btn">
                {{ __('landing.hero_cta') }}
            </a>
        </div>
    </section>

    <div class="section-stripe"></div>

    <!-- ====== SYSTEM PROCESS FLOW ====== -->
    <section id="process" class="process-section">
        <h2>{{ __('landing.process_title') }}</h2>
        <p>
            {{ __('landing.process_subtitle') }}
        </p>

        <div class="process-flow">

            <div class="flow-card">
                <span>1</span>
                <h3>{{ __('landing.step1_title') }}</h3>
                <p>
                    {{ __('landing.step1_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>2</span>
                <h3>{{ __('landing.step2_title') }}</h3>
                <p>
                    {{ __('landing.step2_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>3</span>
                <h3>{{ __('landing.step3_title') }}</h3>
                <p>
                    {{ __('landing.step3_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>4</span>
                <h3>{{ __('landing.step4_title') }}</h3>
                <p>
                    {{ __('landing.step4_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>5</span>
                <h3>{{ __('landing.step5_title') }}</h3>
                <p>
                    {{ __('landing.step5_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>6</span>
                <h3>{{ __('landing.step6_title') }}</h3>
                <p>
                    {{ __('landing.step6_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>7</span>
                <h3>{{ __('landing.step7_title') }}</h3>
                <p>
                    {{ __('landing.step7_desc') }}
                </p>
            </div>

            <div class="flow-card">
                <span>8</span>
                <h3>{{ __('landing.step8_title') }}</h3>
                <p>
                    {{ __('landing.step8_desc') }}
                </p>
            </div>

        </div>
    </section>

    <!-- ====== COUNTRY BENEFITS SECTION ====== -->
    <section id="country-benefits" class="country-impact-section">
        <div class="country-impact-header">
            <span>FSRP Country Impact</span>
            <h2>{{ __('landing.countries_title') }}</h2>
            <p>
                {{ __('landing.countries_subtitle') }}
            </p>
        </div>

        <div class="country-impact-feature">
            <div class="country-impact-image country-impact-slider" data-country-impact-slider>
                <div class="country-impact-slide active">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-1.jpg') }}" alt="Food system resilience support for participating African countries">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}" alt="Water and food system activities for FSRP countries">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}" alt="Regional food security coordination under FSRP">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/gallery/media1.jpeg') }}" alt="FSRP field implementation and food system resilience activity">
                </div>
                <button class="country-slider-btn country-slider-prev" type="button" data-country-slider-prev aria-label="Previous image">&#8249;</button>
                <button class="country-slider-btn country-slider-next" type="button" data-country-slider-next aria-label="Next image">&#8250;</button>
                <div class="country-slider-dots" aria-label="Image slider navigation"></div>
            </div>
            <div class="country-impact-copy">
                <span>01</span>
                <h3>{{ __('landing.countries_item1_title') }}</h3>
                <p>{{ __('landing.countries_item1_desc') }}</p>
                <h3>{{ __('landing.countries_item2_title') }}</h3>
                <p>{{ __('landing.countries_item2_desc') }}</p>
            </div>
        </div>

        <div class="country-impact-row">
            <div class="country-impact-copy">
                <span>02</span>
                <h3>{{ __('landing.countries_item3_title') }}</h3>
                <p>{{ __('landing.countries_item3_desc') }}</p>
                <h3>{{ __('landing.countries_item4_title') }}</h3>
                <p>{{ __('landing.countries_item4_desc') }}</p>
            </div>
            <div class="country-impact-image country-impact-slider" data-country-impact-slider>
                <div class="country-impact-slide active">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}" alt="Agriculture and market resilience activities under FSRP">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-3.jpg') }}" alt="Food system coordination and market resilience for participating countries">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/images/fsrp/water-food-resilience-1.jpg') }}" alt="Program support for water, agriculture, and resilient food systems">
                </div>
                <div class="country-impact-slide">
                    <img src="{{ asset('assets/gallery/media1.jpeg') }}" alt="FSRP field implementation activity supporting resilient food systems">
                </div>
                <button class="country-slider-btn country-slider-prev" type="button" data-country-slider-prev aria-label="Previous image">&#8249;</button>
                <button class="country-slider-btn country-slider-next" type="button" data-country-slider-next aria-label="Next image">&#8250;</button>
                <div class="country-slider-dots" aria-label="Image slider navigation"></div>
            </div>
        </div>

        <div class="country-impact-outcomes">
            <div>
                <strong>{{ __('landing.countries_item5_title') }}</strong>
                <p>{{ __('landing.countries_item5_desc') }}</p>
            </div>
            <div>
                <strong>{{ __('landing.countries_item6_title') }}</strong>
                <p>{{ __('landing.countries_item6_desc') }}</p>
            </div>
        </div>
    </section>

    <!-- ====== CUSTOMIZATION SECTION ====== -->
    <section id="customization" class="customization-section">
        <div class="content">
            <h2>{{ __('landing.governance_title') }}</h2>
            <p>
                {{ __('landing.governance_subtitle') }}
            </p>

            <ul>
                <li>{{ __('landing.governance_item1') }}</li>
                <li>{{ __('landing.governance_item2') }}</li>
                <li>{{ __('landing.governance_item3') }}</li>
                <li>{{ __('landing.governance_item4') }}</li>
                <li>{{ __('landing.governance_item5') }}</li>
            </ul>
        </div>
    </section>



    <!-- ====== FOOTER ====== -->
    <footer id="contact" class="footer" role="contentinfo">
        <div class="footer-content">

            <div class="footer-logo">
                <h3>FSRP<span> Administration</span></h3>
                <p>{{ __('landing.footer_description') }}</p>
            </div>

            <div class="footer-links">
                <h4>{{ __('landing.footer_links_title') }}</h4>
                <a href="{{ route('landing.index') }}">{{ __('landing.footer_link_home') }}</a>
                <a href="#process">{{ __('landing.footer_link_process') }}</a>
                <a href="#country-benefits">{{ __('landing.footer_link_countries') }}</a>
                <a href="#customization">{{ __('landing.footer_link_oversight') }}</a>
                <a href="#contact">{{ __('navigation.contact') }}</a>
                <a href="{{ route('careers.index') }}">{{ __('navigation.careers') }}</a>
            </div>

            <div class="footer-contact">
                <h4>{{ __('landing.footer_contact_title') }}</h4>
                <p>{{ __('landing.footer_email') }}</p>
                <p>{{ __('landing.footer_copyright', ['year' => date('Y')]) }}</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>Supporting FSRP administrative coordination, fiduciary oversight, monitoring, reporting, and implementation accountability.</p>
        </div>
    </footer>



    <script src="{{ asset('assets/script.js') }}?v={{ file_exists(public_path('assets/script.js')) ? filemtime(public_path('assets/script.js')) : time() }}"></script>

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

        function toggleMobileDropdown(trigger) {
            const btn = trigger.closest('.mobile-dropdown-toggle');
            const items = btn ? btn.nextElementSibling : null;

            if (!btn || !items || !items.classList.contains('mobile-dropdown-items')) {
                return;
            }

            const isOpen = items.classList.contains('open');

            document.querySelectorAll('.mobile-dropdown-items.open').forEach(el => {
                el.classList.remove('open');
                el.setAttribute('aria-hidden', 'true');
            });

            document.querySelectorAll('.mobile-dropdown-toggle.open').forEach(el => {
                el.classList.remove('open');
                el.setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                items.classList.add('open');
                items.setAttribute('aria-hidden', 'false');
                btn.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        // Close lang-switcher on outside click
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.lang-switcher.open').forEach(function(el) {
                if (!el.contains(e.target)) el.classList.remove('open');
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileNav();
                document.querySelectorAll('.lang-switcher.open').forEach(el => el.classList.remove('open'));
            }
        });
    </script>
    <!--Start of Tawk.to Script-->
    <!--Start of Tawk.to Script-->
    <!--Start of Tawk.to Script-->
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
    <!--End of Tawk.to Script-->
    <!--End of Tawk.to Script-->
    <!--End of Tawk.to Script-->
</body>

</html>
