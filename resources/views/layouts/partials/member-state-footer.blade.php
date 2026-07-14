<footer class="ms-portal-footer">
    <div class="ms-portal-footer-inner">
        <a href="{{ route('member-state.dashboard') }}" class="ms-footer-brand" aria-label="FSRP Member State Portal home">
            <span class="ms-footer-mark" aria-hidden="true"><i class="feather-feather"></i></span>
            <strong>FSRP Member State Portal</strong>
        </a>

        <span class="ms-footer-copyright">
            &copy; {{ now()->year }} Food Systems Resilience Programme
        </span>

        <div class="ms-footer-utilities">
            <a href="{{ route('member-state.questions.index') }}" class="ms-footer-help">
                <i class="feather-help-circle" aria-hidden="true"></i>
                Help &amp; feedback
            </a>

            <span class="ms-footer-secure">
                <i class="feather-lock" aria-hidden="true"></i>
                Secure access
            </span>

            <span class="ms-footer-online">
                <span class="ms-online-dot" aria-hidden="true"></span>
                Portal online
            </span>
        </div>
    </div>
</footer>
