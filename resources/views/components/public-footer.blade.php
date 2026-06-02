<footer id="contact" class="footer" role="contentinfo">
    <div class="footer-content">
        <div class="footer-logo">
            <h3>FSRP<span> Administration</span></h3>
            <p>{{ __('landing.footer_description') }}</p>
        </div>

        <div class="footer-links">
            <h4>{{ __('landing.footer_links_title') }}</h4>
            <a href="{{ route('landing.index') }}">{{ __('landing.footer_link_home') }}</a>
            <a href="{{ route('landing.index') }}#process">{{ __('landing.footer_link_process') }}</a>
            <a href="{{ route('landing.index') }}#country-benefits">{{ __('landing.footer_link_countries') }}</a>
            <a href="{{ route('events') }}">{{ __('landing.events_webinars') }}</a>
            <a href="{{ route('careers.index') }}">{{ __('navigation.careers') }}</a>
            <a href="{{ route('public.procurement.index') }}">{{ __('public_pages.policy_programs_research') }}</a>
            <a href="{{ route('news.index') }}">{{ __('public_pages.news_updates') }}</a>
            <a href="{{ route('applicants.faq') }}">{{ __('navigation.faqs') }}</a>
            <a href="#contact">{{ __('navigation.contact') }}</a>
        </div>

        <div class="footer-contact">
            <h4>{{ __('landing.footer_contact_title') }}</h4>
            <p>{{ __('landing.footer_email') }}</p>
            <p>{{ __('landing.footer_copyright', ['year' => date('Y')]) }}</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>{{ __('public_pages.footer_bottom') }}</p>
    </div>
</footer>
