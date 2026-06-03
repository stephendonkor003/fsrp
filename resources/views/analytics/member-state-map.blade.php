<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page['title'] }} - FSRP</title>
    <meta name="description" content="{{ $page['intro'] }}">
    <meta name="keywords" content="FSRP commodities map, food commodities, food security analytics, Eastern and Southern Africa, member-state reporting, commodity trends, Food System Resilience Program">
    <meta name="author" content="Food System Resilience Program (FSRP) for Eastern and Southern Africa">
    <link rel="canonical" href="{{ route('food-security.commodities') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $page['title'] }} - FSRP Eastern and Southern Africa">
    <meta property="og:description" content="{{ $page['intro'] }}">
    <meta property="og:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <meta property="og:url" content="{{ route('food-security.commodities') }}">
    <meta property="og:site_name" content="FSRP Eastern and Southern Africa">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page['title'] }} - FSRP Eastern and Southern Africa">
    <meta name="twitter:description" content="{{ $page['intro'] }}">
    <meta name="twitter:image" content="{{ asset('assets/images/fsrp/water-food-resilience-2.jpg') }}">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --fs-ink: #132018;
            --fs-muted: #607067;
            --fs-line: #d8e5dc;
            --fs-panel: #fff;
            --fs-green: #006B3F;
            --fs-green-dark: #004d2e;
            --fs-gold: #fbbc05;
            --fs-coral: #d95f43;
            --fs-blue: #2563eb;
        }

        body {
            margin: 0;
            color: var(--fs-ink);
            background: linear-gradient(180deg, #f4f8f1 0%, #edf6ef 45%, #f8fafc 100%);
            padding-top: 76px;
        }

        .navbar {
            z-index: 1200;
        }

        .fs-shell {
            width: min(1440px, 95%);
            margin: 0 auto;
        }

        .fs-hero {
            margin-top: 1rem;
            border-radius: 16px;
            padding: 1.15rem 1.25rem;
            color: #fff;
            background: linear-gradient(128deg, var(--fs-green-dark) 0%, var(--fs-green) 58%, #c17b12 100%);
            box-shadow: 0 18px 38px rgba(0, 77, 46, .24);
        }

        .fs-hero .eyebrow {
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .78);
            font-weight: 800;
        }

        .fs-hero h1 {
            margin: .28rem 0 .4rem;
            color: #fff;
            font-size: clamp(1.45rem, 2.25vw, 2.35rem);
            line-height: 1.15;
        }

        .fs-hero p {
            margin: 0;
            max-width: 1120px;
            color: rgba(255, 255, 255, .9);
            line-height: 1.55;
        }

        .fs-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: .9rem;
        }

        .fs-tabs a {
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 999px;
            padding: .35rem .72rem;
            color: #fff;
            text-decoration: none;
            background: rgba(255, 255, 255, .08);
            font-size: .78rem;
            font-weight: 700;
        }

        .fs-tabs a.active {
            background: var(--fs-gold);
            color: #111827;
            border-color: var(--fs-gold);
        }

        .fs-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .72rem;
            margin-top: .9rem;
        }

        .fs-card,
        .fs-panel {
            background: var(--fs-panel);
            border: 1px solid var(--fs-line);
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .07);
        }

        .fs-card {
            padding: .78rem .9rem;
        }

        .fs-card .label,
        .fs-form label,
        .fs-table th {
            font-size: .7rem;
            letter-spacing: .055em;
            text-transform: uppercase;
            color: var(--fs-muted);
            font-weight: 800;
        }

        .fs-card .value {
            margin-top: .18rem;
            font-size: 1.18rem;
            font-weight: 850;
            color: var(--fs-green-dark);
        }

        .fs-grid {
            display: grid;
            grid-template-columns: 310px minmax(0, 1fr);
            gap: .9rem;
            margin-top: .9rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        .fs-panel-head {
            padding: .8rem .95rem;
            border-bottom: 1px solid var(--fs-line);
            background: linear-gradient(180deg, #fff 0%, #f7fbf7 100%);
        }

        .fs-panel-head h2,
        .fs-panel-head h3 {
            margin: 0;
            font-size: 1rem;
            color: var(--fs-green-dark);
        }

        .fs-panel-head p {
            margin: .25rem 0 0;
            color: #475569;
            font-size: .82rem;
            line-height: 1.4;
        }

        .fs-form {
            display: grid;
            gap: .65rem;
            padding: .85rem .95rem 1rem;
        }

        .fs-form input,
        .fs-form select,
        .fs-form button,
        .fs-form .fs-reset {
            width: 100%;
            border: 1px solid #cfdccf;
            border-radius: 8px;
            padding: .5rem .55rem;
            background: #fff;
            color: var(--fs-ink);
            font-size: .86rem;
        }

        .fs-form select[multiple] {
            min-height: 150px;
        }

        .fs-form button {
            border: 0;
            background: linear-gradient(120deg, var(--fs-green), #23a35b);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }

        .fs-form .fs-reset {
            display: inline-flex;
            justify-content: center;
            text-decoration: none;
            background: #eef4ee;
            font-weight: 800;
        }

        .fs-main {
            display: grid;
            gap: .9rem;
        }

        #analyticsMap {
            min-height: 620px;
            background: #e7efe9;
        }

        .fs-map-status {
            margin: .65rem .95rem .9rem;
            padding: .55rem .65rem;
            border: 1px solid #d9dfc7;
            border-radius: 9px;
            background: #f6f9ec;
            color: #3f6212;
            font-size: .82rem;
        }

        .fs-map-status.error {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #9f1239;
        }

        .fs-compare {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 1fr;
            gap: .9rem;
        }

        .fs-chart-wrap {
            height: 360px;
            padding: .8rem;
        }

        .fs-table-wrap {
            overflow: auto;
            max-height: 390px;
        }

        .fs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .82rem;
        }

        .fs-table th,
        .fs-table td {
            border-bottom: 1px solid #e8eee9;
            padding: .5rem .55rem;
            text-align: left;
            vertical-align: top;
        }

        .fs-table thead th {
            position: sticky;
            top: 0;
            background: #f8fbf8;
            z-index: 1;
        }

        .fs-empty {
            border: 1px dashed #aab9af;
            border-radius: 10px;
            background: #f8fbf8;
            color: var(--fs-muted);
            padding: .85rem;
            margin: .85rem;
        }

        .fs-popup-title {
            font-weight: 850;
            color: var(--fs-green-dark);
        }

        .fs-popup-grid {
            display: grid;
            gap: .25rem;
            margin-top: .35rem;
            font-size: .78rem;
        }

        @media (max-width: 1100px) {
            .fs-grid,
            .fs-compare {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            body {
                padding-top: 76px;
            }

            .fs-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            #analyticsMap {
                min-height: 520px;
            }
        }

        @media (max-width: 520px) {
            .fs-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <x-public-header active="commodities" language-style="commodities" contact-href="{{ route('landing.index') }}#contact" />

    <main class="fs-shell">
        <section class="fs-hero">
            <div class="eyebrow">{{ $page['eyebrow'] }}</div>
            <h1>{{ $page['title'] }}</h1>
            <p>{{ $page['intro'] }}</p>
            <div class="fs-tabs">
                <a href="{{ route('impact.map') }}">{{ __('navigation.impact_map') }}</a>
                <a href="{{ route('food-security.commodities') }}" class="{{ ($page['active'] ?? '') === 'commodities' ? 'active' : '' }}">{{ __('navigation.food_commodities_map') }}</a>
                <a href="{{ route('world.indicators.performance') }}">{{ __('navigation.world_indicators_performance') }}</a>
            </div>
        </section>

        <section class="fs-summary">
            @foreach($summaryCards as $card)
                <article class="fs-card">
                    <div class="label">{{ $card['label'] }}</div>
                    <div class="value">{{ $card['value'] }}</div>
                </article>
            @endforeach
        </section>

        <section class="fs-grid">
            <aside class="fs-panel">
                <div class="fs-panel-head">
                    <h2>{{ __('public_pages.analytics_filters_comparison') }}</h2>
                    <p>{{ __('public_pages.analytics_filters_help') }}</p>
                </div>
                <form method="GET" action="{{ $page['route'] }}" class="fs-form">
                    <div>
                        <label for="metric">{{ __('public_pages.analytics_map_metric') }}</label>
                        <select id="metric" name="metric">
                            @foreach($metricOptions as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['metric'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($commodityOptions->isNotEmpty())
                        <div>
                            <label for="commodity_id">{{ __('public_pages.analytics_commodity') }}</label>
                            <select id="commodity_id" name="commodity_id">
                                <option value="">{{ __('public_pages.analytics_all_approved_commodities') }}</option>
                                @foreach($commodityOptions as $commodity)
                                    <option value="{{ $commodity->id }}" @selected(($filters['commodity_id'] ?? '') === $commodity->id)>{{ $commodity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label for="from">{{ __('public_pages.analytics_from') }}</label>
                        <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
                    </div>
                    <div>
                        <label for="to">{{ __('public_pages.analytics_to') }}</label>
                        <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
                    </div>
                    <div>
                        <label for="member_state_ids">{{ __('public_pages.analytics_compare_member_states') }}</label>
                        <select id="member_state_ids" name="member_state_ids[]" multiple>
                            @foreach($memberStates as $state)
                                <option value="{{ $state->id }}" @selected(in_array($state->id, $filters['member_state_ids'] ?? [], true))>{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit">{{ __('public_pages.analytics_apply_comparison') }}</button>
                    <a href="{{ $page['route'] }}" class="fs-reset">{{ __('public_pages.analytics_reset') }}</a>
                </form>
            </aside>

            <section class="fs-main">
                <article class="fs-panel">
                    <div class="fs-panel-head">
                        <h2>{{ $page['map_title'] }}</h2>
                        <p>{{ $page['map_hint'] }}</p>
                    </div>
                    <div id="analyticsMap"></div>
                    <div class="fs-map-status" id="mapStatus">{{ __('public_pages.analytics_preparing_map') }}</div>
                </article>

                <div class="fs-compare">
                    <article class="fs-panel">
                        <div class="fs-panel-head">
                            <h3>{{ __('public_pages.analytics_comparison_chart') }}</h3>
                            <p>{{ __('public_pages.analytics_comparison_help', ['metric' => $metricOptions[$filters['metric']] ?? __('public_pages.analytics_selected_metric')]) }}</p>
                        </div>
                        @if($comparisonRows->isNotEmpty())
                            <div class="fs-chart-wrap"><canvas id="comparisonChart"></canvas></div>
                        @else
                            <div class="fs-empty">{{ $page['empty_text'] }}</div>
                        @endif
                    </article>

                    <article class="fs-panel">
                        <div class="fs-panel-head">
                            <h3>{{ __('public_pages.analytics_approved_data_table') }}</h3>
                            <p>{{ __('public_pages.analytics_approved_data_help') }}</p>
                        </div>
                        @if($stateRows->isNotEmpty())
                            <div class="fs-table-wrap">
                                <table class="fs-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('public_pages.member_state') }}</th>
                                            <th>{{ $metricOptions[$filters['metric']] ?? __('public_pages.analytics_selected_metric') }}</th>
                                            <th>{{ __('public_pages.analytics_approved_points') }}</th>
                                            <th>{{ __('public_pages.analytics_latest_evidence') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stateRows as $row)
                                            <tr>
                                                <td>
                                                    <strong>{{ $row['name'] }}</strong>
                                                    <div class="small text-muted">{{ $row['code_alpha2'] ?: $row['code'] }}</div>
                                                </td>
                                                <td>{{ number_format((float) ($row['metrics'][$filters['metric']] ?? 0), 2) }}</td>
                                                <td>{{ number_format((int) ($row['metrics']['data_points'] ?? 0)) }}</td>
                                                <td>
                                                    <div>{{ $row['meta']['latest_recorded_on'] ?? __('public_pages.analytics_no_date') }}</div>
                                                    @if(!empty($row['meta']['commodities']))
                                                        <div class="small text-muted">{{ $row['meta']['commodities'] }}</div>
                                                    @endif
                                                    @if(!empty($row['meta']['latest_summary']))
                                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($row['meta']['latest_summary'], 120) }}</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="fs-empty">{{ $page['empty_text'] }}</div>
                        @endif
                    </article>
                </div>
            </section>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>{{ __('public_pages.analytics_footer_bottom') }}</p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/shpjs@6.2.0/dist/shp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const shapeFiles = @json($shapeFiles);
        const mapData = @json($mapData);
        const comparisonRows = @json($comparisonRows->values());
        const metric = @json($filters['metric']);
        const metricLabel = @json($metricOptions[$filters['metric']] ?? 'Selected metric');

        function openMobileNav() {
            document.getElementById('mobileNav')?.classList.add('open');
            document.getElementById('navOverlay')?.classList.add('open');
            document.getElementById('hamburgerBtn')?.setAttribute('aria-expanded', 'true');
        }

        function closeMobileNav() {
            document.getElementById('mobileNav')?.classList.remove('open');
            document.getElementById('navOverlay')?.classList.remove('open');
            document.getElementById('hamburgerBtn')?.setAttribute('aria-expanded', 'false');
            document.querySelectorAll('.mobile-dropdown-toggle.open').forEach(el => el.classList.remove('open'));
            document.querySelectorAll('.mobile-dropdown-items.open').forEach(el => el.classList.remove('open'));
        }

        function toggleMobileDropdown(trigger) {
            const items = trigger.nextElementSibling;
            trigger.classList.toggle('open');
            items?.classList.toggle('open');
        }

        function normalizeCountryName(name) {
            const input = (name || '').toString();
            const normalized = typeof input.normalize === 'function' ? input.normalize('NFD') : input;
            return normalized
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[\u2019']/g, '')
                .replace(/[^a-zA-Z0-9 ]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
        }

        const dataByName = Object.values(mapData).reduce((lookup, row) => {
            lookup[normalizeCountryName(row.name)] = row;
            return lookup;
        }, {});

        const aliasToCode = {
            'cabo verde': 'CV',
            'cape verde': 'CV',
            'ivory coast': 'CI',
            'cote divoire': 'CI',
            'c te divoire': 'CI',
            'eswatini': 'SZ',
            'swaziland': 'SZ',
            'democratic republic of congo': 'CD',
            'democratic republic of the congo': 'CD',
            'republic of congo': 'CG',
            'congo brazzaville': 'CG',
            'sao tome and principe': 'ST',
            'sao tome principe': 'ST'
        };

        function featureName(feature) {
            const props = feature?.properties || {};
            return props.name || props.NAME || props.admin || props.ADMIN || props.country || 'Country';
        }

        function featureCode(feature) {
            const props = feature?.properties || {};
            return (props['ISO3166-1-Alpha-2'] || props.ISO_A2 || props.iso_a2 || props.code || '').toString().toUpperCase();
        }

        function rowForFeature(feature) {
            const name = featureName(feature);
            const normalized = normalizeCountryName(name);
            const code = featureCode(feature) || aliasToCode[normalized] || '';
            return mapData[code] || dataByName[normalized] || null;
        }

        function metricValue(row) {
            return Number(row?.metrics?.[metric] || 0);
        }

        const values = Object.values(mapData).map(metricValue).filter(value => value > 0);
        const maxValue = Math.max(...values, 1);

        function colorFor(row) {
            if (!row) return '#d8e1db';
            const intensity = Math.max(0, Math.min(1, metricValue(row) / maxValue));
            if (intensity >= .75) return '#166534';
            if (intensity >= .5) return '#16a34a';
            if (intensity >= .25) return '#fbbc05';
            if (intensity > 0) return '#d95f43';
            return '#e5e7eb';
        }

        function popupFor(row, countryName) {
            if (!row) {
                return `<div class="fs-popup-title">${countryName}</div><div>No approved data for the selected filters.</div>`;
            }

            const dataPoints = Number(row.metrics?.data_points || 0).toLocaleString();
            const latest = row.meta?.latest_recorded_on || 'No date';
            const summary = row.meta?.latest_summary ? `<div>${escapeHtml(row.meta.latest_summary).slice(0, 220)}</div>` : '';
            const commodities = row.meta?.commodities ? `<div><strong>Commodities:</strong> ${escapeHtml(row.meta.commodities)}</div>` : '';

            return `
                <div class="fs-popup-title">${escapeHtml(row.name)}</div>
                <div class="fs-popup-grid">
                    <div><strong>${escapeHtml(metricLabel)}:</strong> ${metricValue(row).toLocaleString(undefined, { maximumFractionDigits: 2 })}</div>
                    <div><strong>Approved points:</strong> ${dataPoints}</div>
                    <div><strong>Latest evidence:</strong> ${escapeHtml(latest)}</div>
                    ${commodities}
                    ${summary}
                </div>
            `;
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        const map = L.map('analyticsMap', {
            center: [0, 20],
            zoom: 3,
            minZoom: 3,
            maxZoom: 8,
            scrollWheelZoom: true,
            attributionControl: false
        });
        const layerGroup = L.featureGroup().addTo(map);
        const mapStatus = document.getElementById('mapStatus');

        function setMapStatus(message, isError = false) {
            if (!mapStatus) return;
            mapStatus.textContent = message;
            mapStatus.classList.toggle('error', isError);
        }

        function toFeatureCollection(payload) {
            if (!payload) return { type: 'FeatureCollection', features: [] };
            if (payload.type === 'FeatureCollection') return payload;
            if (payload.type === 'Feature') return { type: 'FeatureCollection', features: [payload] };
            if (Array.isArray(payload.features)) return { type: 'FeatureCollection', features: payload.features };
            if (Array.isArray(payload)) return { type: 'FeatureCollection', features: payload.flatMap(item => toFeatureCollection(item).features) };
            return { type: 'FeatureCollection', features: [] };
        }

        async function loadShape(shapeUrl) {
            const resolvedUrl = new URL(shapeUrl, window.location.origin).toString();
            if (/\.(geojson|json)(\?|#|$)/i.test(resolvedUrl)) {
                const response = await fetch(resolvedUrl);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            }
            if (typeof shp === 'function') {
                return shp(resolvedUrl);
            }
            throw new Error('No shape loader available.');
        }

        function addFeatureCollection(payload) {
            const featureCollection = toFeatureCollection(payload);
            L.geoJSON(featureCollection, {
                style: feature => ({
                    color: '#f8fafc',
                    weight: 1,
                    fillColor: colorFor(rowForFeature(feature)),
                    fillOpacity: .84
                }),
                onEachFeature: (feature, layer) => {
                    const name = featureName(feature);
                    const row = rowForFeature(feature);
                    layer.bindPopup(popupFor(row, name));
                    layer.on('mouseover', () => layer.setStyle({ weight: 2, color: '#111827' }));
                    layer.on('mouseout', () => layer.setStyle({ weight: 1, color: '#f8fafc' }));
                }
            }).addTo(layerGroup);
        }

        async function loadMap() {
            if (!shapeFiles.length) {
                setMapStatus('No Africa map file found in public/assets/Africa.', true);
                return;
            }

            setMapStatus(`Loading ${shapeFiles.length} Africa map source file(s)...`);
            let loaded = 0;
            for (const shapeUrl of shapeFiles) {
                try {
                    addFeatureCollection(await loadShape(shapeUrl));
                    loaded += 1;
                } catch (error) {
                    console.warn('Failed to load map source', shapeUrl, error);
                }
            }

            if (!loaded || !layerGroup.getLayers().length) {
                setMapStatus('Africa map could not be loaded.', true);
                return;
            }

            map.fitBounds(layerGroup.getBounds(), { padding: [18, 18] });
            setMapStatus(`Map loaded. ${Object.keys(mapData).length} member state(s) have approved data for the current filters.`);
        }

        function renderComparisonChart() {
            const canvas = document.getElementById('comparisonChart');
            if (!canvas || !comparisonRows.length || typeof Chart === 'undefined') return;

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: comparisonRows.map(row => row.name),
                    datasets: [{
                        label: metricLabel,
                        data: comparisonRows.map(row => metricValue(row)),
                        backgroundColor: '#006B3F',
                        borderColor: '#004d2e',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: context => `${metricLabel}: ${Number(context.raw || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}` } }
                    },
                    scales: {
                        x: { ticks: { maxRotation: 45, minRotation: 0 } },
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMap();
            renderComparisonChart();
        });
    </script>
</body>
</html>
