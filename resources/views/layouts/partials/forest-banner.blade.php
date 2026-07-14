<section class="attp-forest-banner" aria-label="FSRP administration portal">
    <div class="attp-forest-banner__plants" aria-hidden="true"></div>
    <div class="attp-forest-banner__copy">
        <span class="attp-forest-banner__eyebrow">
            <i class="feather-feather" aria-hidden="true"></i>
            FSRP Administration
        </span>
        <strong>Growing resilience, rooted in Africa.</strong>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.documentElement;
        const header = document.querySelector('.nxl-header');

        if (!header) return;

        const syncAdminHeaderHeight = () => {
            root.style.setProperty('--attp-primary-header-height', `${Math.ceil(header.getBoundingClientRect().height)}px`);
        };

        syncAdminHeaderHeight();

        if ('ResizeObserver' in window) {
            new ResizeObserver(syncAdminHeaderHeight).observe(header);
        } else {
            window.addEventListener('resize', syncAdminHeaderHeight, { passive: true });
        }
    });
</script>
