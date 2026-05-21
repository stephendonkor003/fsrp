// Background slider (only runs when .slide elements exist)
(function () {
    var slides = document.querySelectorAll('.slide');
    if (!slides.length) return;
    var index = 0;
    function showNextSlide() {
        slides[index].classList.remove('active');
        index = (index + 1) % slides.length;
        slides[index].classList.add('active');
    }
    setInterval(showNextSlide, 6000);
})();

// Country impact image sliders
(function () {
    var sliders = document.querySelectorAll('[data-country-impact-slider]');
    if (!sliders.length) return;

    sliders.forEach(function (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('.country-impact-slide'));
        var prevBtn = slider.querySelector('[data-country-slider-prev]');
        var nextBtn = slider.querySelector('[data-country-slider-next]');
        var dotsWrap = slider.querySelector('.country-slider-dots');
        var index = Math.max(0, slides.findIndex(function (slide) {
            return slide.classList.contains('active');
        }));
        var timer = null;

        if (slides.length <= 1) return;

        function setSlide(nextIndex) {
            index = (nextIndex + slides.length) % slides.length;
            slides.forEach(function (slide, slideIndex) {
                slide.classList.toggle('active', slideIndex === index);
            });

            if (dotsWrap) {
                dotsWrap.querySelectorAll('.country-slider-dot').forEach(function (dot, dotIndex) {
                    dot.classList.toggle('active', dotIndex === index);
                    dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
                });
            }
        }

        function restartTimer() {
            if (timer) clearInterval(timer);
            timer = setInterval(function () {
                setSlide(index + 1);
            }, 5000);
        }

        if (dotsWrap) {
            slides.forEach(function (_, slideIndex) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'country-slider-dot';
                dot.setAttribute('aria-label', 'Show image ' + (slideIndex + 1));
                dot.addEventListener('click', function () {
                    setSlide(slideIndex);
                    restartTimer();
                });
                dotsWrap.appendChild(dot);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                setSlide(index - 1);
                restartTimer();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                setSlide(index + 1);
                restartTimer();
            });
        }

        slider.addEventListener('mouseenter', function () {
            if (timer) clearInterval(timer);
        });

        slider.addEventListener('mouseleave', restartTimer);

        setSlide(index);
        restartTimer();
    });
})();

// Hero rotating headline + subtitle
const heroSlides = [
    {
        title: "West Africa Food System\nResilience Program",
        subtitle: "An administrative portal for coordinating implementation, fiduciary controls, procurement, monitoring, and reporting for FSRP."
    },
    {
        title: "Preparedness Against\nFood Insecurity",
        subtitle: "Support early warning, digital advisory services, food crisis prevention, and program coordination across participating countries."
    },
    {
        title: "Resilient Production\nAnd Value Chains",
        subtitle: "Track investments that improve the productive base, adaptive capacity, priority landscapes, and strategic food system value chains."
    },
    {
        title: "Regional Markets\nAnd Trade",
        subtitle: "Coordinate activities that strengthen regional food market integration, trade facilitation, and agricultural value-chain connectivity."
    },
    {
        title: "One Program.\nOne Control Center.",
        subtitle: "Bring planning, finance, procurement, safeguards, results monitoring, and audit evidence into one controlled administrative workspace."
    }
];

const typeEl     = document.getElementById("typewriter");
const subtitleEl = document.getElementById("hero-subtitle");

let heroIndex    = 0;
let charIndex    = 0;
let isDeleting   = false;
let typeSpeed    = 65;
let holdTimer    = null;

function runTypewriter() {
    if (!typeEl) return;

    const current = heroSlides[heroIndex];
    const raw     = current.title;

    if (!isDeleting) {
        // Type next character (treat \n as <br>)
        charIndex++;
        typeEl.innerHTML = raw.substring(0, charIndex).replace(/\n/g, "<br>");

        if (charIndex === raw.length) {
            // Finished typing — show subtitle, hold, then delete
            if (subtitleEl) {
                subtitleEl.textContent = current.subtitle;
                subtitleEl.classList.add("sub-visible");
            }
            holdTimer = setTimeout(function () {
                isDeleting = true;
                if (subtitleEl) subtitleEl.classList.remove("sub-visible");
                setTimeout(runTypewriter, 400);
            }, 3800);
            return;
        }
    } else {
        // Erase one character at a time from the raw string (not from innerHTML)
        charIndex--;
        typeEl.innerHTML = raw.substring(0, charIndex).replace(/\n/g, "<br>");

        if (charIndex === 0) {
            isDeleting = false;
            heroIndex  = (heroIndex + 1) % heroSlides.length;
            typeSpeed  = 65;
            setTimeout(runTypewriter, 300);
            return;
        }
    }

    typeSpeed = isDeleting ? 35 : 65;
    setTimeout(runTypewriter, typeSpeed);
}

window.addEventListener("load", function () {
    if (typeEl) {
        setTimeout(runTypewriter, 400);
    }
});

