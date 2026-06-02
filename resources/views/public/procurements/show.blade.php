<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ $procurement->title }} | FSRP</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('public_pages.procurement_apply_meta_description', ['title' => $procurement->title]) }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f7f4f2;
            margin: 0;
        }

        .page-header {
            background: url('/assets/three.webp') center/cover no-repeat;
            height: 320px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .page-header::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(82, 43, 57, .75);
        }

        .header-content {
            position: relative;
            max-width: 900px;
            text-align: center;
            padding: 0 1rem;
        }

        .header-content h1 {
            color: #fbbc05;
            font-size: 2.3rem;
        }

        .container {
            max-width: 1100px;
            margin: -80px auto 4rem;
            padding: 0 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
            margin-bottom: 2rem;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .meta-item {
            background: #f7f4f2;
            padding: 1rem;
            border-radius: 10px;
            font-size: .95rem;
        }

        .meta-item strong {
            color: #522b39;
        }

        .alert {
            padding: 1rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.2rem;
            font-size: .95rem;
        }

        .alert-success {
            background: #e7f7ed;
            color: #157347;
        }

        .alert-danger {
            background: #fdecea;
            color: #842029;
        }

        .form-group {
            margin-bottom: 1.4rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: .4rem;
            color: #522b39;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: .7rem .8rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: .95rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .error-text {
            color: #c0392b;
            font-size: .85rem;
            margin-top: .3rem;
        }

        .btn-submit {
            background: #a70d53;
            color: #fff;
            padding: .8rem 2rem;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .btn-submit:hover {
            background: #e16435;
        }


        .form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #522b39;
        }

        .required {
            color: red;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 0.65rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
        }

        .option-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .option-item {
            font-size: 0.9rem;
        }

        .error-text {
            margin-top: 4px;
            color: #c0392b;
            font-size: 0.85rem;
        }

        @media (max-width: 1100px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .procurement-description {
            margin-top: 1rem;
            line-height: 1.7;
        }

        .procurement-description h1,
        .procurement-description h2,
        .procurement-description h3 {
            color: #522b39;
            margin-top: 1.2rem;
        }

        .procurement-description ul {
            padding-left: 1.5rem;
            list-style: disc;
        }

        .procurement-description table {
            width: 100%;
            border-collapse: collapse;
        }

        .procurement-description table td,
        .procurement-description table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
<x-public-header active="procurement" language-style="procurement-show" />

    {{-- ===== HEADER ===== --}}
    <section class="page-header">
        <div class="header-content">
            <br>
            <br>
            <br>
            <br>
            <h1>{{ $procurement->title }}</h1>
            <p>{{ __('public_pages.procurement_reference') }}: {{ $procurement->reference_no ?? __('public_pages.dash') }}</p>
        </div>
    </section>

    <div class="container">
        <br>
        <br>
        <br>
        <br>
        <br>
        {{-- ===== SUCCESS MESSAGE ===== --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== ERROR SUMMARY ===== --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>{{ __('public_pages.procurement_error_summary') }}</strong>
            </div>
        @endif

        {{-- ===== PROCUREMENT DETAILS ===== --}}
            <div class="card">
                <h3>{{ __('public_pages.procurement_details') }}</h3>

                <div style="margin-top:1rem; line-height:1.7;">
                    {!! nl2br(e(strip_tags($procurement->description ?? ''))) !!}
                </div>


            {{-- <div class="meta-grid">
                <div class="meta-item">
                    <strong>Fiscal Year:</strong><br>
                    {{ $procurement->fiscal_year ?? 'N/A' }}
                </div>

                <div class="meta-item">
                    <strong>Estimated Budget:</strong><br>
                    {{ number_format($procurement->estimated_budget ?? 0, 2) }}
                </div>

                <div class="meta-item">
                    <strong>Status:</strong><br>
                    {{ ucfirst($procurement->status) }}
                </div>
            </div> --}}
        </div>

        {{-- ===== APPLICATION FORM ===== --}}
        <div class="card">
            <h3>{{ __('public_pages.procurement_application_form') }}</h3>

            {{-- @if ($form && $form->fields->count()) --}}
            @if ($form?->fields?->isNotEmpty())

                <form method="POST" action="{{ route('public.procurement.apply', $procurement->slug) }}"
                    enctype="multipart/form-data">

                    @csrf

                    {{-- ===== GRID WRAPPER ===== --}}
                    <div class="form-grid">

                        @foreach ($form->fields as $field)
                            @php
                                $oldValue = old($field->field_key);

                                // Normalize multi-select old value (Select2 may return CSV or array)
                                if (in_array($field->field_type, ['checkbox', 'multiselect']) && is_string($oldValue)) {
                                    $oldValue = array_filter(array_map('trim', explode(',', $oldValue)));
                                }

                                // Parse comma-separated options safely
                                $options = collect(explode(',', (string) $field->options))
                                    ->map(fn($opt) => trim($opt))
                                    ->filter()
                                    ->values()
                                    ->toArray();

                                $isRequired = $field->is_required;

                                // Normalize datetime-local
                                $dateTimeValue = $oldValue;
                                if ($field->field_type === 'datetime-local' && $oldValue) {
                                    try {
                                        $dateTimeValue = \Carbon\Carbon::parse($oldValue)->format('Y-m-d\TH:i');
                                    } catch (\Exception $e) {
                                        $dateTimeValue = $oldValue;
                                    }
                                }
                            @endphp

                            <div class="form-group">
                                <label>
                                    {{ $field->label }}
                                    @if ($isRequired)
                                        <span class="required">*</span>
                                    @endif
                                </label>

                                {{-- TEXT --}}
                                @if ($field->field_type === 'text')
                                    <input type="text" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- EMAIL --}}
                                @elseif ($field->field_type === 'email')
                                    <input type="email" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- NUMBER --}}
                                @elseif ($field->field_type === 'number')
                                    <input type="number" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- DATE --}}
                                @elseif ($field->field_type === 'date')
                                    <input type="date" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- TIME --}}
                                @elseif ($field->field_type === 'time')
                                    <input type="time" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- DATETIME LOCAL --}}
                                @elseif ($field->field_type === 'datetime-local')
                                    <input type="datetime-local" name="{{ $field->field_key }}"
                                        value="{{ $dateTimeValue }}" {{ $isRequired ? 'required' : '' }}>

                                    {{-- URL --}}
                                @elseif ($field->field_type === 'url')
                                    <input type="url" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- PHONE --}}
                                @elseif ($field->field_type === 'tel')
                                    <input type="tel" name="{{ $field->field_key }}" value="{{ $oldValue }}"
                                        {{ $isRequired ? 'required' : '' }}>

                                    {{-- TEXTAREA --}}
                                @elseif ($field->field_type === 'textarea')
                                    <textarea name="{{ $field->field_key }}" rows="4" {{ $isRequired ? 'required' : '' }}>{{ $oldValue }}</textarea>

                                    {{-- SINGLE SELECT (SELECT2) --}}
                                @elseif ($field->field_type === 'select')
                                    <select name="{{ $field->field_key }}" class="form-select select2-single"
                                        data-placeholder="{{ __('public_pages.procurement_select_option') }}" {{ $isRequired ? 'required' : '' }}>
                                        <option></option>
                                        @foreach ($options as $option)
                                            <option value="{{ $option }}"
                                                {{ (string) $oldValue === (string) $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- MULTI SELECT (SELECT2) --}}
                                @elseif (in_array($field->field_type, ['checkbox', 'multiselect']))
                                    <select name="{{ $field->field_key }}[]" class="form-select select2-multiple"
                                        multiple data-placeholder="{{ __('public_pages.procurement_select_options') }}"
                                        {{ $isRequired ? 'required' : '' }}>
                                        @foreach ($options as $option)
                                            <option value="{{ $option }}"
                                                {{ is_array($oldValue) && in_array($option, $oldValue) ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($isRequired)
                                        <small class="text-muted">{{ __('public_pages.procurement_select_options') }}</small>
                                    @endif

                                    {{-- FILE --}}
                                @elseif ($field->field_type === 'file')
                                    <input type="file" name="{{ $field->field_key }}"
                                        {{ $isRequired ? 'required' : '' }}>
                                @endif

                                @error($field->field_key)
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                    </div>


                    <div style="text-align:center;margin-top:2rem;">
                        <button type="submit" class="btn-submit">
                            {{ __('public_pages.procurement_submit_application') }}
                        </button>
                    </div>

                </form>
            @else
                <p style="color:#999;">
                    {{ __('public_pages.procurement_no_form') }}
                </p>
            @endif


        </div>

    </div>
    <x-public-footer />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-single').select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                allowClear: true
            });

            $('.select2-multiple').select2({
                width: '100%',
                closeOnSelect: false,
                allowClear: true
            });
        });
    </script>



    <script src="assets/script.js"></script>

    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/6968b44f895de4198b902486/1jf0g0m8k';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
</body>

</html>
