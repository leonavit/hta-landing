(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    onReady(function () {
        initMenu();
        initNavScroll();
        initScrollSpy();
        initEventFilter();
        initGsap();
        initSubmitPhoneSkew();
        initSectionRules();
        initHeroLogo();
        initHeroVideo();
        initHeadingCascadeReveal();
        initOdometers();
        initHeroDateGlitch();
        initFadeUpSections();
        initSubmitParticles();
        initButtonParticles();
        initAboutTilt();
        initMagneticButtons();
        initBackToTop();
        initSectionRail();
        initSubmitForm();
        initSubmitCountdown();
        initDatetimeField();
        // Carousels are heavy on mobile — defer so hero date/logo animations start first.
        deferCarousels();
    });

    function deferCarousels() {
        function run() {
            initSwipers();
            initPartnersLoop();
        }

        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(run, { timeout: 400 });
            return;
        }

        window.requestAnimationFrame(function () {
            window.setTimeout(run, 0);
        });
    }

    function initMenu() {
        var menuBtn = document.getElementById('menuBtn');
        var closeBtn = document.getElementById('closeBtn');
        var mobileMenu = document.getElementById('mobileMenu');
        if (!menuBtn || !closeBtn || !mobileMenu) {
            return;
        }

        var links = mobileMenu.querySelectorAll('.mobile-link');
        var logo = mobileMenu.querySelector('.hta-mobile-logo-link');
        var logoImg = logo ? (logo.querySelector('.hta-mobile-logo') || logo) : null;
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var isOpen = false;
        var animated = logo
            ? [mobileMenu, logo, logoImg].concat(Array.prototype.slice.call(links))
            : [mobileMenu].concat(Array.prototype.slice.call(links));

        function setHamburgerState(open) {
            menuBtn.classList.remove('is-open');
            closeBtn.classList.toggle('is-open', open);
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuBtn.setAttribute('aria-label', open ? 'סגור תפריט' : 'פתח תפריט');
        }

        function clearLogoFilter() {
            if (!logoImg || typeof gsap === 'undefined') {
                return;
            }
            gsap.set(logoImg, { clearProps: 'filter' });
        }

        function hideMenu() {
            mobileMenu.classList.add('opacity-0', 'pointer-events-none');
            mobileMenu.classList.remove('pointer-events-auto', 'is-open');
            document.body.style.overflow = '';
            clearLogoFilter();
            if (logo && typeof gsap !== 'undefined') {
                gsap.set(logo, { clearProps: 'opacity,y,transform,filter' });
            }
        }

        function setMenuOpen(next) {
            if (isOpen === next) {
                return;
            }
            isOpen = next;
            setHamburgerState(next);

            if (!next) {
                if (typeof gsap === 'undefined' || reduced) {
                    hideMenu();
                    return;
                }
                gsap.killTweensOf(animated);
                if (logo) {
                    gsap.to(logo, {
                        opacity: 0,
                        duration: 0.22,
                        ease: 'power2.in'
                    });
                }
                if (logoImg) {
                    gsap.to(logoImg, {
                        filter: 'blur(10px)',
                        duration: 0.22,
                        ease: 'power2.in'
                    });
                }
                gsap.to(links, {
                    opacity: 0,
                    y: 14,
                    duration: 0.18,
                    stagger: 0.015,
                    ease: 'power2.in'
                });
                gsap.to(mobileMenu, {
                    opacity: 0,
                    duration: 0.28,
                    delay: 0.06,
                    ease: 'power2.in',
                    onComplete: hideMenu
                });
                return;
            }

            mobileMenu.classList.remove('opacity-0', 'pointer-events-none');
            mobileMenu.classList.add('pointer-events-auto', 'is-open');
            document.body.style.overflow = 'hidden';

            if (typeof gsap === 'undefined' || reduced) {
                if (typeof gsap !== 'undefined') {
                    gsap.set(mobileMenu, { opacity: 1 });
                    gsap.set(links, { opacity: 1, y: 0, scale: 1 });
                    if (logo) {
                        gsap.set(logo, { opacity: 1, y: 0 });
                    }
                    clearLogoFilter();
                }
                return;
            }

            gsap.killTweensOf(animated);
            gsap.set(mobileMenu, { opacity: 0 });
            gsap.set(links, { opacity: 0, y: 28, scale: 0.94 });
            if (logo && logoImg) {
                var logoRise = Math.max(24, (logoImg.getBoundingClientRect().height || 80) * 0.5);
                gsap.set(logo, {
                    opacity: 1,
                    y: logoRise
                });
                gsap.set(logoImg, {
                    filter: 'blur(14px)'
                });
            }
            gsap.to(mobileMenu, {
                opacity: 1,
                duration: 0.35,
                ease: 'power2.out'
            });
            if (logo && logoImg) {
                gsap.to(logo, {
                    y: 0,
                    duration: 0.45,
                    ease: 'power3.out'
                });
                gsap.to(logoImg, {
                    filter: 'blur(0px)',
                    duration: 0.55,
                    delay: 0.12,
                    ease: 'power2.out',
                    onComplete: function () {
                        if (!isOpen) {
                            return;
                        }
                        // Safari leaves a rectangular blur layer if filter stays as blur(0px).
                        clearLogoFilter();
                    }
                });
            }
            gsap.to(links, {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.48,
                stagger: 0.05,
                delay: 0.12,
                ease: 'power3.out'
            });
        }

        menuBtn.addEventListener('click', function () {
            setMenuOpen(true);
        });
        closeBtn.addEventListener('click', function () {
            setMenuOpen(false);
        });
        links.forEach(function (link) {
            link.addEventListener('click', function () {
                setMenuOpen(false);
            });
        });
        if (logo) {
            logo.addEventListener('click', function () {
                setMenuOpen(false);
            });
        }
    }

    function initNavScroll() {
        var bar = document.querySelector('.hta-site-nav');
        var logo = document.querySelector('.hta-nav-logo');
        var inner = document.querySelector('.hta-site-nav-inner');
        if (!bar || !logo) {
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var scrolled = null;
        var first = true;

        function apply(next) {
            if (scrolled === next) {
                return;
            }
            scrolled = next;
            bar.classList.toggle('is-scrolled', next);

            if (typeof gsap === 'undefined' || reduced) {
                first = false;
                return;
            }

            var logoH = next ? 48 : 96;
            var innerH = next ? 80 : 112;

            if (first) {
                first = false;
                gsap.set(logo, { height: logoH });
                if (inner) {
                    gsap.set(inner, { height: innerH });
                }
                return;
            }

            gsap.to(logo, {
                height: logoH,
                duration: 0.45,
                ease: 'power3.out',
                overwrite: true
            });
            if (inner) {
                gsap.to(inner, {
                    height: innerH,
                    duration: 0.45,
                    ease: 'power3.out',
                    overwrite: true
                });
            }
        }

        window.addEventListener('scroll', function () {
            apply(window.scrollY > 16);
        }, { passive: true });
        apply(window.scrollY > 16);
    }

    function initScrollSpy() {
        var sections = document.querySelectorAll('section[id], header[id]');
        var navLinks = document.querySelectorAll('.nav-link:not(.hta-btn)');
        if (!sections.length || !navLinks.length) {
            return;
        }

        window.addEventListener('scroll', function () {
            var current = '';
            sections.forEach(function (section) {
                if (window.scrollY >= section.offsetTop - 150) {
                    current = section.getAttribute('id');
                }
            });
            navLinks.forEach(function (link) {
                link.classList.toggle('is-active', link.getAttribute('data-target') === current);
            });
        });
    }

    function initEventFilter() {
        var searchInput = document.getElementById('searchInput');
        var categoryFilter = document.getElementById('categoryFilter');
        var cityFilter = document.getElementById('cityFilter');
        var eventsGrid = document.getElementById('eventsGrid');
        var eventCards = document.querySelectorAll('.event-card');
        var noResults = document.getElementById('noResults');
        var loadMoreBtn = document.getElementById('eventsLoadMore');
        if (!searchInput || !categoryFilter || !eventsGrid) {
            return;
        }

        var pageSizeAttr = eventsGrid.getAttribute('data-page-size') || '4';
        var showAll = pageSizeAttr === 'all';
        var pageSize = showAll ? Infinity : Math.max(1, parseInt(pageSizeAttr, 10) || 4);
        var visibleLimit = pageSize;

        function matchesFilters(card) {
            if (card.getAttribute('data-promoted') === '1') {
                return true;
            }
            var query = searchInput.value.toLowerCase().trim();
            var selectedCategory = categoryFilter.value;
            var selectedCity = cityFilter ? cityFilter.value : 'all';
            var titleData = (card.getAttribute('data-title') || '').toLowerCase();
            var categoryData = card.getAttribute('data-category');
            var cityData = card.getAttribute('data-city') || '';
            var matchesQuery = titleData.indexOf(query) !== -1;
            var matchesCategory = selectedCategory === 'all' || categoryData === selectedCategory;
            var matchesCity = selectedCity === 'all' || cityData === selectedCity;
            return matchesQuery && matchesCategory && matchesCity;
        }

        function renderEvents() {
            var matched = 0;
            var shown = 0;

            eventCards.forEach(function (card) {
                if (!matchesFilters(card)) {
                    card.style.display = 'none';
                    return;
                }
                matched += 1;
                if (shown < visibleLimit) {
                    card.style.display = 'flex';
                    shown += 1;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.classList.toggle('hidden', matched !== 0);
            }

            if (loadMoreBtn) {
                var hasMore = !showAll && matched > visibleLimit;
                loadMoreBtn.hidden = !hasMore;
            }
        }

        function resetAndRender() {
            visibleLimit = pageSize;
            renderEvents();
        }

        searchInput.addEventListener('input', resetAndRender);
        categoryFilter.addEventListener('change', resetAndRender);
        if (cityFilter) {
            cityFilter.addEventListener('change', resetAndRender);
        }

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                if (showAll) {
                    return;
                }
                visibleLimit += pageSize;
                renderEvents();
            });
        }

        renderEvents();
    }

    function initSwipers() {
        if (typeof Swiper === 'undefined') {
            return;
        }

        var shared = {
            rtl: true,
            speed: 600
        };

        if (document.querySelector('.hta-ambassadors-swiper')) {
            new Swiper('.hta-ambassadors-swiper', Object.assign({}, shared, {
                slidesPerView: 1.28,
                centeredSlides: true,
                spaceBetween: 14,
                watchSlidesProgress: true,
                pagination: { el: '.hta-ambassadors-swiper .swiper-pagination', clickable: true },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        centeredSlides: false,
                        spaceBetween: 24
                    },
                    768: {
                        slidesPerView: 5,
                        centeredSlides: false,
                        spaceBetween: 24
                    }
                }
            }));
        }

        if (document.querySelector('.hta-media-swiper')) {
            var mediaRoot = document.querySelector('.hta-media-swiper');
            var uniqueCount = mediaRoot.querySelectorAll('.swiper-wrapper > .swiper-slide').length;
            var mediaCountdownSeconds = 5;
            var mediaCountdownTimer = 0;
            var mediaCountdownLeft = mediaCountdownSeconds;
            var mediaAutoplayLocked = false;

            function clearMediaCountdown() {
                if (mediaCountdownTimer) {
                    window.clearInterval(mediaCountdownTimer);
                    mediaCountdownTimer = 0;
                }
            }

            function syncMediaCountdownVisibility(swiper) {
                mediaRoot.querySelectorAll('.hta-media-countdown').forEach(function (el) {
                    el.classList.remove('hta-media-countdown--visible');
                });
                var activeSlide = swiper && swiper.slides ? swiper.slides[swiper.activeIndex] : null;
                if (!activeSlide) {
                    activeSlide = mediaRoot.querySelector('.swiper-slide-active');
                }
                if (!activeSlide) {
                    return;
                }
                var countdown = activeSlide.querySelector('.hta-media-countdown');
                if (countdown) {
                    countdown.classList.add('hta-media-countdown--visible');
                }
            }

            function renderMediaCountdown(swiper, value) {
                syncMediaCountdownVisibility(swiper);
                var slide = swiper && swiper.slides ? swiper.slides[swiper.activeIndex] : null;
                if (!slide) {
                    slide = mediaRoot.querySelector('.swiper-slide-active');
                }
                if (!slide) {
                    return;
                }
                var el = slide.querySelector('.hta-media-countdown-value');
                if (el) {
                    el.textContent = String(value);
                }
            }

            function startMediaCountdown(swiper) {
                clearMediaCountdown();
                mediaCountdownLeft = mediaCountdownSeconds;
                renderMediaCountdown(swiper, mediaCountdownLeft);
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }
                mediaCountdownTimer = window.setInterval(function () {
                    if (!swiper || (swiper.autoplay && swiper.autoplay.running === false)) {
                        return;
                    }
                    if (mediaCountdownLeft <= 1) {
                        return;
                    }
                    mediaCountdownLeft -= 1;
                    renderMediaCountdown(swiper, mediaCountdownLeft);
                }, 1000);
            }

            function bindMediaAutoplayToggle(swiper) {
                var btn = mediaRoot.querySelector('.hta-media-autoplay-toggle');
                if (!btn || !swiper.autoplay) {
                    return;
                }

                function setPaused(paused) {
                    btn.classList.toggle('is-paused', paused);
                    btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
                    btn.setAttribute(
                        'aria-label',
                        paused
                            ? 'המשך קרוסלה'
                            : 'עצור קרוסלה'
                    );
                }

                btn.addEventListener('click', function () {
                    mediaAutoplayLocked = !mediaAutoplayLocked;
                    if (mediaAutoplayLocked) {
                        swiper.autoplay.stop();
                        clearMediaCountdown();
                        setPaused(true);
                        return;
                    }
                    swiper.autoplay.start();
                    startMediaCountdown(swiper);
                    setPaused(false);
                });
            }

            fillSlidesForLoop(mediaRoot, 6);
            new Swiper(mediaRoot, Object.assign({}, shared, {
                effect: 'coverflow',
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                slideToClickedSlide: true,
                autoplay: {
                    delay: mediaCountdownSeconds * 1000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                coverflowEffect: {
                    rotate: 42,
                    stretch: 0,
                    depth: 180,
                    modifier: 1,
                    slideShadows: false
                },
                pagination: {
                    el: '.hta-media-swiper .swiper-pagination',
                    clickable: true,
                    renderBullet: function (index, className) {
                        var extra = index >= uniqueCount ? ' hta-media-bullet-extra' : '';
                        return '<span class="' + className + extra + '"></span>';
                    }
                },
                breakpoints: {
                    768: {
                        slidesPerView: 3
                    }
                },
                on: {
                    init: function (swiper) {
                        syncMediaBullets(swiper, uniqueCount);
                        pauseInactiveMediaVideos(mediaRoot, swiper);
                        startMediaCountdown(swiper);
                        bindMediaAutoplayToggle(swiper);
                    },
                    slideChangeTransitionStart: function (swiper) {
                        pauseInactiveMediaVideos(mediaRoot, swiper);
                    },
                    slideChangeTransitionEnd: function (swiper) {
                        syncMediaCountdownVisibility(swiper);
                    },
                    slideChange: function (swiper) {
                        syncMediaBullets(swiper, uniqueCount);
                        pauseInactiveMediaVideos(mediaRoot, swiper);
                        if (!mediaAutoplayLocked) {
                            startMediaCountdown(swiper);
                        }
                    },
                    autoplayStart: function (swiper) {
                        if (!mediaAutoplayLocked) {
                            startMediaCountdown(swiper);
                        }
                    },
                    autoplayStop: function () {
                        clearMediaCountdown();
                    },
                    paginationUpdate: function (swiper) {
                        syncMediaBullets(swiper, uniqueCount);
                    }
                }
            }));
        }
    }

    function fillSlidesForLoop(root, minCount) {
        var wrapper = root && root.querySelector('.swiper-wrapper');
        if (!wrapper) {
            return;
        }
        var slides = wrapper.querySelectorAll('.swiper-slide');
        if (!slides.length) {
            return;
        }
        var html = Array.prototype.map.call(slides, function (slide) {
            return slide.outerHTML;
        }).join('');
        while (wrapper.querySelectorAll('.swiper-slide').length < minCount) {
            wrapper.insertAdjacentHTML('beforeend', html);
        }
    }

    function syncMediaBullets(swiper, uniqueCount) {
        var bullets = swiper.pagination && swiper.pagination.bullets;
        if (!bullets || !bullets.length) {
            return;
        }
        var real = swiper.realIndex % uniqueCount;
        bullets.forEach(function (bullet, index) {
            bullet.classList.toggle('swiper-pagination-bullet-active', index === real);
        });
    }

    function pauseInactiveMediaVideos(root, swiper) {
        if (!root) {
            return;
        }
        var active = swiper && swiper.slides ? swiper.slides[swiper.activeIndex] : root.querySelector('.swiper-slide-active');
        var slides = root.querySelectorAll('.swiper-slide');

        slides.forEach(function (slide) {
            if (active && slide === active) {
                return;
            }
            slide.querySelectorAll('iframe.hta-media-video, iframe[src*="youtube"]').forEach(function (iframe) {
                try {
                    if (iframe.contentWindow) {
                        iframe.contentWindow.postMessage(JSON.stringify({
                            event: 'command',
                            func: 'pauseVideo',
                            args: []
                        }), '*');
                    }
                } catch (err) {
                    // Ignore cross-origin failures.
                }
            });
            slide.querySelectorAll('video').forEach(function (video) {
                if (typeof video.pause === 'function' && !video.paused) {
                    video.pause();
                }
            });
        });
    }

    function initPartnersLoop() {
        var root = document.querySelector('.hta-partners-loop');
        var track = root && root.querySelector('.hta-partners-track');
        if (!root || !track || typeof gsap === 'undefined') {
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var loop = null;
        var st = null;
        var resizeTimer = 0;
        var lastWidth = window.innerWidth;

        function gapPx() {
            var gap = window.getComputedStyle(track).columnGap || window.getComputedStyle(track).gap;
            return parseFloat(gap) || 24;
        }

        function fillTrack() {
            var originals = Array.prototype.slice.call(track.querySelectorAll('.hta-partners-item:not([data-clone])'));
            if (!originals.length) {
                return originals;
            }
            track.querySelectorAll('.hta-partners-item[data-clone]').forEach(function (el) {
                el.remove();
            });
            var html = originals.map(function (item) {
                return item.outerHTML;
            }).join('');
            var guard = 0;
            while (track.scrollWidth < root.offsetWidth * 2 && guard < 8) {
                track.insertAdjacentHTML('beforeend', html);
                guard += 1;
            }
            Array.prototype.slice.call(track.querySelectorAll('.hta-partners-item')).forEach(function (item, i) {
                if (i >= originals.length) {
                    item.setAttribute('data-clone', '1');
                    item.setAttribute('aria-hidden', 'true');
                    item.querySelectorAll('a').forEach(function (a) {
                        a.setAttribute('tabindex', '-1');
                    });
                }
            });
            return Array.prototype.slice.call(track.querySelectorAll('.hta-partners-item'));
        }

        function killLoop() {
            if (st) {
                st.kill();
                st = null;
            }
            if (loop) {
                loop.kill();
                loop = null;
            }
            gsap.set(track.querySelectorAll('.hta-partners-item'), { x: 0, xPercent: 0 });
        }

        function playLoop() {
            if (!loop || reduced) {
                return;
            }
            if (loop.reversed()) {
                loop.reverse();
            } else {
                loop.play();
            }
        }

        function build(preserveProgress) {
            var progress = preserveProgress && loop ? loop.totalProgress() : 0;
            var wasPlaying = !!(loop && !loop.paused());
            killLoop();
            var items = fillTrack();
            if (items.length < 2 || reduced) {
                return;
            }

            loop = horizontalLoop(items, {
                repeat: -1,
                speed: 0.85,
                paddingRight: gapPx()
            });

            if (progress > 0) {
                loop.totalProgress(progress);
            }

            if (typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);
                st = ScrollTrigger.create({
                    trigger: root,
                    start: 'top bottom',
                    end: 'bottom top',
                    onEnter: playLoop,
                    onEnterBack: playLoop,
                    onLeave: function () {
                        if (loop) {
                            loop.pause();
                        }
                    },
                    onLeaveBack: function () {
                        if (loop) {
                            loop.pause();
                        }
                    }
                });
                if (st.isActive || wasPlaying) {
                    playLoop();
                } else {
                    loop.pause();
                }
            } else {
                playLoop();
            }
        }

        function onImagesReady(done) {
            var originals = track.querySelectorAll('.hta-partners-item:not([data-clone]) img');
            var imgs = originals.length ? originals : track.querySelectorAll('img');
            if (!imgs.length) {
                done();
                return;
            }
            var left = imgs.length;
            var finished = false;
            function finish() {
                if (finished) {
                    return;
                }
                finished = true;
                done();
            }
            function tick() {
                left -= 1;
                if (left <= 0) {
                    finish();
                }
            }
            Array.prototype.forEach.call(imgs, function (img) {
                if (img.complete) {
                    tick();
                } else {
                    img.addEventListener('load', tick, { once: true });
                    img.addEventListener('error', tick, { once: true });
                }
            });
            // Never block forever if an image stalls on mobile Safari.
            window.setTimeout(finish, 1200);
        }

        root.addEventListener('mouseenter', function () {
            if (loop) {
                loop.pause();
            }
        });
        root.addEventListener('mouseleave', function () {
            playLoop();
        });

        // iOS Safari fires resize when the URL bar shows/hides (height-only).
        // Rebuilding then resets the loop to the start — ignore those.
        window.addEventListener('resize', function () {
            var width = window.innerWidth;
            if (Math.abs(width - lastWidth) < 2) {
                return;
            }
            lastWidth = width;
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                build(true);
            }, 180);
        });

        onImagesReady(function () {
            build(false);
        });
    }

    /*
     * GSAP helper: seamless infinite loop along the x-axis.
     * https://gsap.com/docs/v3/HelperFunctions/helpers/seamlessLoop/
     */
    function horizontalLoop(items, config) {
        items = gsap.utils.toArray(items);
        config = config || {};
        var tl = gsap.timeline({
            repeat: config.repeat,
            paused: config.paused,
            defaults: { ease: 'none' },
            onReverseComplete: function () {
                tl.totalTime(tl.rawTime() + tl.duration() * 100);
            }
        });
        var length = items.length;
        var startX = items[0].offsetLeft;
        var times = [];
        var widths = [];
        var xPercents = [];
        var curIndex = 0;
        var pixelsPerSecond = (config.speed || 1) * 100;
        var snap = config.snap === false ? function (v) { return v; } : gsap.utils.snap(config.snap || 1);
        var totalWidth;
        var curX;
        var distanceToStart;
        var distanceToLoop;
        var item;
        var i;

        gsap.set(items, {
            xPercent: function (index, el) {
                var w = (widths[index] = parseFloat(gsap.getProperty(el, 'width', 'px')));
                xPercents[index] = snap(
                    (parseFloat(gsap.getProperty(el, 'x', 'px')) / w) * 100 +
                    gsap.getProperty(el, 'xPercent')
                );
                return xPercents[index];
            }
        });
        gsap.set(items, { x: 0 });
        totalWidth =
            items[length - 1].offsetLeft +
            (xPercents[length - 1] / 100) * widths[length - 1] -
            startX +
            items[length - 1].offsetWidth * gsap.getProperty(items[length - 1], 'scaleX') +
            (parseFloat(config.paddingRight) || 0);

        for (i = 0; i < length; i++) {
            item = items[i];
            curX = (xPercents[i] / 100) * widths[i];
            distanceToStart = item.offsetLeft + curX - startX;
            distanceToLoop = distanceToStart + widths[i] * gsap.getProperty(item, 'scaleX');
            tl.to(item, {
                xPercent: snap(((curX - distanceToLoop) / widths[i]) * 100),
                duration: distanceToLoop / pixelsPerSecond
            }, 0)
                .fromTo(item, {
                    xPercent: snap(((curX - distanceToLoop + totalWidth) / widths[i]) * 100)
                }, {
                    xPercent: xPercents[i],
                    duration: (curX - distanceToLoop + totalWidth - curX) / pixelsPerSecond,
                    immediateRender: false
                }, distanceToLoop / pixelsPerSecond)
                .add('label' + i, distanceToStart / pixelsPerSecond);
            times[i] = distanceToStart / pixelsPerSecond;
        }

        function toIndex(index, vars) {
            vars = vars || {};
            if (Math.abs(index - curIndex) > length / 2) {
                index += index > curIndex ? -length : length;
            }
            var newIndex = gsap.utils.wrap(0, length, index);
            var time = times[newIndex];
            if (time > tl.time() !== index > curIndex) {
                vars.modifiers = { time: gsap.utils.wrap(0, tl.duration()) };
                time += tl.duration() * (index > curIndex ? 1 : -1);
            }
            curIndex = newIndex;
            vars.overwrite = true;
            return tl.tweenTo(time, vars);
        }

        tl.next = function (vars) { return toIndex(curIndex + 1, vars); };
        tl.previous = function (vars) { return toIndex(curIndex - 1, vars); };
        tl.current = function () { return curIndex; };
        tl.toIndex = function (index, vars) { return toIndex(index, vars); };
        tl.times = times;
        tl.progress(1, true).progress(0, true);
        if (config.reversed) {
            tl.vars.onReverseComplete();
            tl.reverse();
        }
        return tl;
    }

    function initGsap() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }
        gsap.registerPlugin(ScrollTrigger);
        gsap.utils.toArray('[data-reveal]').forEach(function (el) {
            if (el.classList.contains('hta-section-title')) {
                return;
            }

            var targets = el;
            if (el.querySelector('.hta-section-title')) {
                targets = Array.prototype.filter.call(el.children, function (child) {
                    return !child.classList.contains('hta-section-title');
                });
                if (!targets.length) {
                    return;
                }
            }

            gsap.from(targets, {
                opacity: 0,
                y: 36,
                duration: 0.8,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 88%'
                }
            });
        });
    }

    function initSubmitPhoneSkew() {
        var phone = document.querySelector('.hta-submit-note-phone');
        if (!phone || typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);
        gsap.from(phone, {
            skewX: 20,
            opacity: 0,
            x: 16,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: phone,
                start: 'top 90%'
            }
        });
    }

    function initSectionRules() {
        var rules = document.querySelectorAll('.hta-rule-t, .hta-rule-y');
        if (!rules.length) {
            return;
        }

        function edgeFill(y, vh) {
            var zone = Math.min(180, vh * 0.24);
            if (y <= 0 || y >= vh) {
                return 1;
            }
            if (y < zone) {
                return 1 - y / zone;
            }
            if (y > vh - zone) {
                return (y - (vh - zone)) / zone;
            }
            return 0;
        }

        function updateRule(section) {
            var rect = section.getBoundingClientRect();
            var vh = window.innerHeight || document.documentElement.clientHeight;
            section.style.setProperty('--hta-rule-t', String(edgeFill(rect.top, vh)));
            if (section.classList.contains('hta-rule-y')) {
                section.style.setProperty('--hta-rule-t-b', String(edgeFill(rect.bottom, vh)));
            }
        }

        function updateAll() {
            rules.forEach(updateRule);
        }

        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            rules.forEach(function (section) {
                ScrollTrigger.create({
                    trigger: section,
                    start: 'top bottom',
                    end: 'bottom top',
                    onUpdate: function () {
                        updateRule(section);
                    },
                    onRefresh: function () {
                        updateRule(section);
                    }
                });
            });
        } else {
            window.addEventListener('scroll', updateAll, { passive: true });
            window.addEventListener('resize', updateAll);
        }

        updateAll();
    }

    function initOdometers() {
        var nodes = document.querySelectorAll('.hta-hero-kicker, .hta-topic-date');
        if (!nodes.length) {
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var canAnimate = typeof gsap !== 'undefined' && !reduced;

        function makeDigit(target) {
            var windowEl = document.createElement('span');
            windowEl.className = 'hta-odometer-digit';
            var strip = document.createElement('span');
            strip.className = 'digit-strip';
            strip.dataset.digit = String(target);
            var n;
            for (n = 0; n <= 9; n++) {
                var cell = document.createElement('span');
                cell.textContent = String(n);
                strip.appendChild(cell);
            }
            windowEl.appendChild(strip);
            return windowEl;
        }

        function fillOdometer(parent, str) {
            Array.from(str).forEach(function (ch) {
                if (/\d/.test(ch)) {
                    parent.appendChild(makeDigit(ch));
                } else {
                    parent.appendChild(document.createTextNode(ch));
                }
            });
        }

        function build(el) {
            if (el.dataset.odometerReady) {
                return;
            }
            var text = el.textContent || '';
            if (!text.trim()) {
                return;
            }
            el.setAttribute('aria-label', text.replace(/\s+/g, ' ').trim());
            el.dataset.odometerReady = '1';
            if (!canAnimate) {
                return;
            }

            var frag = document.createDocumentFragment();
            var pipe = text.indexOf('|');
            var isHeroKicker = el.classList.contains('hta-hero-kicker');
            if (pipe !== -1) {
                var ltr = document.createElement('span');
                ltr.className = 'hta-bidi-ltr' + (isHeroKicker ? ' hta-glitch-text' : '');
                ltr.setAttribute('dir', 'ltr');
                if (isHeroKicker) {
                    ltr.setAttribute('data-text', text.slice(0, pipe).replace(/\s+$/, ''));
                }
                fillOdometer(ltr, text.slice(0, pipe));
                frag.appendChild(ltr);
                fillOdometer(frag, text.slice(pipe));
            } else {
                var wrap = document.createElement('span');
                wrap.className = 'hta-bidi-ltr' + (isHeroKicker ? ' hta-glitch-text' : '');
                wrap.setAttribute('dir', 'ltr');
                if (isHeroKicker) {
                    wrap.setAttribute('data-text', text.replace(/\s+/g, ' ').trim());
                }
                fillOdometer(wrap, text);
                frag.appendChild(wrap);
            }
            el.textContent = '';
            el.appendChild(frag);
        }

        function roll(el) {
            if (!canAnimate) {
                return true;
            }
            if (el.dataset.odometerRolled) {
                return true;
            }
            var strips = el.querySelectorAll('.digit-strip');
            if (!strips.length) {
                return;
            }
            var firstCell = strips[0].querySelector('span');
            var digitHeight = firstCell ? firstCell.offsetHeight : 0;
            if (!digitHeight) {
                return false;
            }
            el.dataset.odometerRolled = '1';
            gsap.set(strips, { y: 0 });
            strips.forEach(function (strip, i) {
                var targetDigit = parseInt(strip.getAttribute('data-digit'), 10) || 0;
                gsap.to(strip, {
                    y: -targetDigit * digitHeight,
                    duration: 1.5,
                    delay: i * 0.07,
                    ease: 'power2.out'
                });
            });
            return true;
        }

        function rollWhenReady(el) {
            var attempts = 0;
            function tryRoll() {
                if (roll(el)) {
                    return;
                }
                if (attempts < 60) {
                    attempts += 1;
                    window.setTimeout(tryRoll, 50);
                }
            }
            tryRoll();
        }

        nodes.forEach(build);

        function startHero() {
            var hero = document.querySelector('.hta-hero-kicker');
            if (hero) {
                rollWhenReady(hero);
            }
        }

        // Don't wait forever on fonts.ready (can stall on iPhone); race a short timeout.
        var heroStarted = false;
        function startHeroOnce() {
            if (heroStarted) {
                return;
            }
            heroStarted = true;
            startHero();
        }

        if (document.fonts && document.fonts.ready && typeof document.fonts.ready.then === 'function') {
            document.fonts.ready.then(startHeroOnce);
            window.setTimeout(startHeroOnce, 120);
        } else {
            startHeroOnce();
        }

        var topicDates = document.querySelectorAll('.hta-topic-date');
        if (!topicDates.length) {
            return;
        }

        if (typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
            topicDates.forEach(function (el) {
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 88%',
                    once: true,
                    onEnter: function () {
                        rollWhenReady(el);
                    }
                });
            });
            return;
        }

        topicDates.forEach(rollWhenReady);
    }

    function initHeroDateGlitch() {
        if (typeof gsap === 'undefined') {
            return;
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        var el = document.querySelector('.hta-hero-kicker .hta-glitch-text');
        if (!el) {
            return;
        }

        var dateText = (el.getAttribute('data-text') || '').trim();
        if (!dateText) {
            return;
        }

        var playing = false;
        var ready = false;

        function flattenForGlitch() {
            // Classic demo needs plain text matching data-text (not odometer strips).
            el.textContent = dateText;
            el.classList.remove('hta-bidi-ltr');
            el.setAttribute('dir', 'ltr');
            ready = true;
        }

        function playGlitch() {
            if (!ready || playing) {
                return;
            }
            playing = true;
            el.classList.add('is-glitching');

            var tl = gsap.timeline({
                repeat: 2,
                onComplete: function () {
                    el.classList.remove('is-glitching');
                    gsap.set(el, { x: 0, skewX: 0, clearProps: 'transform' });
                    playing = false;
                }
            });

            tl.to(el, { x: -3, skewX: 10, duration: 0.05 })
                .to(el, { x: 3, skewX: -8, duration: 0.05 })
                .to(el, { x: 0, skewX: 0, duration: 0.05 });
        }

        // Wait for odometer roll to finish, then flatten + start interval.
        window.setTimeout(function () {
            flattenForGlitch();
            playGlitch();
            window.setInterval(playGlitch, 7000);
        }, 2400);
    }

    function initHeroVideo() {
        var video = document.querySelector('video.hta-hero-video');
        if (!video || typeof video.pause !== 'function') {
            return;
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            video.pause();
            video.removeAttribute('autoplay');
        }
    }

    function initHeroLogo() {
        var wrap = document.querySelector('.hta-hero-logo-wrap');
        var svg = document.querySelector('.hta-hero-logo');
        if (!svg || !wrap || wrap.hidden || wrap.classList.contains('is-hidden') || typeof gsap === 'undefined') {
            return;
        }

        var dots = Array.prototype.slice.call(svg.querySelectorAll('.hta-logo-dot'));
        if (!dots.length) {
            return;
        }

        dots.sort(function (a, b) {
            var ba = a.getBBox();
            var bb = b.getBBox();
            var sizeA = ba.width * ba.height;
            var sizeB = bb.width * bb.height;
            if (sizeA !== sizeB) {
                return sizeA - sizeB;
            }
            return (ba.y + ba.height / 2) - (bb.y + bb.height / 2);
        });

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        gsap.set(dots, {
            transformOrigin: '50% 50%',
            scale: 0.15,
            opacity: 0,
            y: -10
        });
        gsap.to(dots, {
            scale: 1,
            opacity: 1,
            y: 0,
            duration: 0.55,
            stagger: {
                each: 0.016,
                from: 'start'
            },
            ease: 'power3.out',
            onComplete: function () {
                gsap.to(dots, {
                    scale: 1.08,
                    duration: 2.4,
                    repeat: -1,
                    yoyo: true,
                    ease: 'sine.inOut',
                    stagger: {
                        each: 0.04,
                        from: 'start'
                    }
                });
            }
        });
    }

    function splitHeadingChars(el) {
        if (typeof SplitText === 'undefined') {
            return null;
        }

        if (typeof gsap.registerPlugin === 'function') {
            gsap.registerPlugin(SplitText);
        }

        if (typeof SplitText.create === 'function') {
            return SplitText.create(el, { type: 'chars' });
        }

        return new SplitText(el, { type: 'chars' });
    }

    function prepareSectionTitle(title) {
        if (!title) {
            return null;
        }

        if (title._htaTitleSplit) {
            return title._htaTitleSplit;
        }

        var split = splitHeadingChars(title);
        if (!split || !split.chars || !split.chars.length) {
            gsap.set(title, { opacity: 0, y: 40 });
            return null;
        }

        title._htaTitleSplit = split;
        title.classList.add('is-split-chars');

        gsap.set(split.chars, {
            y: -80,
            rotation: -15,
            opacity: 0
        });

        return split;
    }

    function animateSectionTitleIn(title) {
        if (!title || title.dataset.cascadePlayed) {
            return;
        }

        title.dataset.cascadePlayed = '1';
        title.classList.remove('is-cascade-pending');

        var split = title._htaTitleSplit || prepareSectionTitle(title);
        if (split && split.chars && split.chars.length) {
            gsap.to(split.chars, {
                y: 0,
                rotation: 0,
                opacity: 1,
                stagger: { each: 0.04, from: 'start' },
                duration: 0.5,
                ease: 'back.out(1.4)'
            });
            return;
        }

        gsap.to(title, {
            y: 0,
            opacity: 1,
            duration: 0.8,
            ease: 'power3.out',
            clearProps: 'transform,opacity'
        });
    }

    function animateHeroTitle(hero) {
        wrapHeroLtrRuns(hero);

        gsap.fromTo(hero, {
            y: 44,
            opacity: 0
        }, {
            y: 0,
            opacity: 1,
            duration: 0.82,
            delay: 0.28,
            ease: 'power3.out',
            clearProps: 'transform,opacity'
        });
    }

    function shouldDeferSectionTitlePlay(title) {
        if (!title || window.scrollY > 8) {
            return false;
        }

        var rect = title.getBoundingClientRect();
        var viewport = window.innerHeight || document.documentElement.clientHeight;
        return rect.top < viewport * 0.92 && rect.bottom > 0;
    }

    function bindSectionTitleReveal(title) {
        title.classList.add('is-cascade-pending');
        prepareSectionTitle(title);

        var played = false;
        function play() {
            if (played) {
                return;
            }
            played = true;
            animateSectionTitleIn(title);
        }

        if (typeof ScrollTrigger === 'undefined') {
            play();
            return;
        }

        ScrollTrigger.create({
            trigger: title,
            start: 'top 70%',
            once: true,
            onEnter: play
        });

        if (shouldDeferSectionTitlePlay(title)) {
            var onFirstScroll = function () {
                window.removeEventListener('scroll', onFirstScroll);
                if (played) {
                    return;
                }
                var rect = title.getBoundingClientRect();
                var viewport = window.innerHeight || document.documentElement.clientHeight;
                if (rect.top <= viewport * 0.7 && rect.bottom > 0) {
                    play();
                }
            };
            window.addEventListener('scroll', onFirstScroll, { passive: true });
            return;
        }

        ScrollTrigger.refresh();
        if (title.getBoundingClientRect().top <= (window.innerHeight || document.documentElement.clientHeight) * 0.7) {
            play();
        }
    }

    function initSectionCascadeReveal() {
        var sectionTitles = document.querySelectorAll('.hta-section-title');
        if (!sectionTitles.length || typeof gsap === 'undefined') {
            return;
        }

        sectionTitles.forEach(bindSectionTitleReveal);
    }

    function initHeadingCascadeReveal() {
        if (typeof gsap === 'undefined') {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        var hero = document.querySelector('.hta-hero-title');
        if (hero) {
            animateHeroTitle(hero);
        }

        if (typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);
        }

        initSectionCascadeReveal();
    }

    function wrapHeroLtrRuns(el) {
        var text = el.textContent || '';
        el.innerHTML = text.replace(/(\d[\d.,\-\/]*)/g, '<span class="hta-bidi-ltr" dir="ltr">$1</span>');
    }

    function fadeUpOnScroll(trigger, items) {
        if (!trigger || !items.length) {
            return;
        }
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);
        gsap.from(items, {
            opacity: 0,
            y: 40,
            duration: 0.6,
            stagger: 0.15,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: trigger,
                start: 'top 80%'
            }
        });
    }

    function initParticleFloatField(field, options) {
        options = options || {};
        if (!field || typeof gsap === 'undefined') {
            return [];
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return [];
        }

        var variant = options.variant || 'cyan';
        var isButton = variant === 'white';
        var count = options.count;
        if (!count) {
            count = isButton ? 10 : (window.matchMedia('(max-width: 767px)').matches ? 28 : 52);
        }

        var tweens = [];
        var i;
        var particle;
        var size;
        var startOpacity;

        for (i = 0; i < count; i++) {
            particle = document.createElement('span');
            var roll = Math.random();
            if (isButton) {
                if (roll < 0.5) {
                    size = gsap.utils.random(1, 1.6);
                } else if (roll < 0.85) {
                    size = gsap.utils.random(1.6, 2.4);
                } else {
                    size = gsap.utils.random(2.4, 3.2);
                }
                startOpacity = gsap.utils.random(0.18, 0.5);
            } else if (roll < 0.42) {
                size = gsap.utils.random(1, 1.8);
            } else if (roll < 0.78) {
                size = gsap.utils.random(1.8, 3);
            } else if (roll < 0.94) {
                size = gsap.utils.random(3, 4.2);
            } else {
                size = gsap.utils.random(4.2, 5.5);
            }
            if (!isButton) {
                startOpacity = gsap.utils.random(0.22, 0.58);
            }

            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            if (isButton) {
                particle.style.boxShadow = '0 0 ' + (size * 1.2) + 'px rgb(255 255 255 / 0.55), 0 0 ' + (size * 2) + 'px rgb(255 255 255 / 0.2)';
            } else {
                particle.style.boxShadow = '0 0 ' + (size * 1.4) + 'px rgb(34 211 238 / 0.5), 0 0 ' + (size * 2.6) + 'px rgb(34 211 238 / 0.2)';
            }
            particle.style.left = gsap.utils.random(1, 99) + '%';
            particle.style.top = gsap.utils.random(isButton ? 20 : 8, isButton ? 92 : 96) + '%';
            field.appendChild(particle);

            gsap.set(particle, { opacity: startOpacity, x: 0, y: 0 });
            tweens.push(gsap.to(particle, {
                y: gsap.utils.random(isButton ? -28 : -180, isButton ? -10 : -60),
                x: gsap.utils.random(isButton ? -10 : -40, isButton ? 10 : 40),
                opacity: isButton ? 0.06 : 0.08,
                duration: gsap.utils.random(isButton ? 1.8 : 2.8, isButton ? 3.6 : 5.5),
                delay: gsap.utils.random(0, isButton ? 2 : 3.5),
                repeat: -1,
                ease: 'none'
            }));
        }

        return tweens;
    }

    function initSubmitParticles() {
        var section = document.getElementById('submit-event');
        var field = section && section.querySelector('.hta-particle-field:not(.hta-particle-field--btn)');
        if (!section || !field) {
            return;
        }

        var tweens = initParticleFloatField(field, { variant: 'cyan' });
        if (!tweens.length || typeof ScrollTrigger === 'undefined') {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);
        var st = ScrollTrigger.create({
            trigger: section,
            start: 'top bottom',
            end: 'bottom top',
            onToggle: function (self) {
                tweens.forEach(function (tween) {
                    if (self.isActive) {
                        tween.play();
                    } else {
                        tween.pause();
                    }
                });
            }
        });
        if (!st.isActive) {
            tweens.forEach(function (tween) {
                tween.pause();
            });
        }
    }

    function initButtonParticles() {
        document.querySelectorAll('.hta-particle-field--btn').forEach(function (field) {
            var count = parseInt(field.getAttribute('data-particle-count'), 10);
            initParticleFloatField(field, {
                variant: 'white',
                count: isNaN(count) ? 10 : count
            });
        });
    }

    function initFadeUpSections() {
        fadeUpOnScroll(document.querySelector('.hta-topics-grid'), document.querySelectorAll('.hta-topic-card'));
        fadeUpOnScroll(document.getElementById('eventsGrid'), document.querySelectorAll('.event-card'));
        fadeUpOnScroll(document.querySelector('.hta-ambassadors-swiper'), document.querySelectorAll('.hta-ambassador-card'));
        fadeUpOnScroll(document.querySelector('.hta-media-swiper'), document.querySelectorAll('.hta-media-card'));
    }

    function initAboutTilt() {
        var cards = document.querySelectorAll('.hta-tilt-card');
        if (!cards.length || typeof gsap === 'undefined') {
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var coarse = window.matchMedia('(pointer: coarse)').matches;

        cards.forEach(function (card) {
            gsap.set(card, { transformPerspective: 600, transformStyle: 'preserve-3d' });

            if (card.classList.contains('hta-tilt-orbit')) {
                if (reduced) {
                    return;
                }

                var proxy = { a: 0 };
                var radius = 0.13;
                var tween = gsap.to(proxy, {
                    a: Math.PI * 2,
                    duration: 4.5,
                    ease: 'none',
                    repeat: -1,
                    onUpdate: function () {
                        var x = Math.cos(proxy.a) * radius;
                        var y = Math.sin(proxy.a) * radius;
                        gsap.set(card, {
                            rotateY: x * 20,
                            rotateX: -y * 20,
                            transformPerspective: 600
                        });
                    }
                });

                if (typeof ScrollTrigger === 'undefined') {
                    return;
                }
                gsap.registerPlugin(ScrollTrigger);
                var st = ScrollTrigger.create({
                    trigger: card,
                    start: 'top bottom',
                    end: 'bottom top',
                    onToggle: function (self) {
                        if (self.isActive) {
                            tween.play();
                        } else {
                            tween.pause();
                        }
                    }
                });
                if (!st.isActive) {
                    tween.pause();
                }
                return;
            }

            if (coarse || reduced) {
                return;
            }

            card.addEventListener('mousemove', function (e) {
                var rect = card.getBoundingClientRect();
                var x = (e.clientX - rect.left) / rect.width - 0.5;
                var y = (e.clientY - rect.top) / rect.height - 0.5;
                gsap.to(card, {
                    rotateY: x * 20,
                    rotateX: -y * 20,
                    duration: 0.3,
                    transformPerspective: 600,
                    overwrite: true
                });
            });

            card.addEventListener('mouseleave', function () {
                gsap.to(card, {
                    rotateY: 0,
                    rotateX: 0,
                    duration: 0.45,
                    ease: 'power3.out',
                    overwrite: true
                });
            });
        });
    }

    function initMagneticButtons() {
        if (typeof gsap === 'undefined') {
            return;
        }
        if (window.matchMedia('(pointer: coarse)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        var buttons = Array.prototype.slice.call(document.querySelectorAll('.hta-btn, .hta-btn-secondary'));
        if (!buttons.length) {
            return;
        }

        document.addEventListener('mousemove', function (e) {
            buttons.forEach(function (btn) {
                var rect = btn.getBoundingClientRect();
                var btnCenter = {
                    x: rect.left + rect.width / 2,
                    y: rect.top + rect.height / 2
                };
                var dx = e.clientX - btnCenter.x;
                var dy = e.clientY - btnCenter.y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 120) {
                    var pull = (120 - dist) / 120;
                    gsap.to(btn, { x: dx * pull * 0.4, y: dy * pull * 0.4, duration: 0.35, ease: 'power3.out', overwrite: true });
                } else {
                    gsap.to(btn, { x: 0, y: 0, duration: 0.45, ease: 'power3.out', overwrite: true });
                }
            });
        });
    }

    function initSectionRail() {
        var rail = document.getElementById('htaSectionRail');
        if (!rail) {
            return;
        }

        var dots = Array.prototype.slice.call(rail.querySelectorAll('.hta-section-rail-dot'));
        var sections = dots.map(function (dot) {
            var id = dot.getAttribute('data-section');
            return id ? document.getElementById(id) : null;
        }).filter(Boolean);

        if (!sections.length) {
            return;
        }

        var nav = document.querySelector('.hta-site-nav');
        var footer = document.querySelector('footer');
        var activeId = '';
        var ratios = new Map();

        function updateRailBounds() {
            if (!nav) {
                return;
            }
            var navBottom = nav.getBoundingClientRect().bottom;
            var viewportBottom = window.innerHeight;
            var footerTop = footer ? footer.getBoundingClientRect().top : viewportBottom;
            var railBottom = Math.min(footerTop, viewportBottom);
            var top = Math.max(0, navBottom);
            var height = Math.max(0, railBottom - top);

            rail.style.top = top + 'px';
            rail.style.height = height + 'px';
        }

        function setActive(id) {
            if (!id || activeId === id) {
                return;
            }
            activeId = id;
            dots.forEach(function (dot) {
                var isActive = dot.getAttribute('data-section') === id;
                dot.classList.toggle('is-active', isActive);
                if (isActive) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });
        }

        function pickVisibleSection() {
            var bestId = '';
            var bestRatio = 0;

            sections.forEach(function (section) {
                var ratio = ratios.get(section.id) || 0;
                if (ratio > bestRatio) {
                    bestRatio = ratio;
                    bestId = section.id;
                }
            });

            if (bestId && bestRatio > 0.12) {
                setActive(bestId);
            }
        }

        function onScrollOrResize() {
            updateRailBounds();
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    ratios.set(entry.target.id, entry.intersectionRatio);
                });
                pickVisibleSection();
            }, {
                threshold: [0, 0.15, 0.3, 0.45, 0.6, 0.75, 0.9, 1]
            });

            sections.forEach(function (section) {
                observer.observe(section);
            });
        } else {
            window.addEventListener('scroll', function () {
                var current = '';
                sections.forEach(function (section) {
                    if (window.scrollY >= section.offsetTop - 150) {
                        current = section.id;
                    }
                });
                setActive(current);
            }, { passive: true });
        }

        window.addEventListener('scroll', onScrollOrResize, { passive: true });
        window.addEventListener('resize', onScrollOrResize);
        onScrollOrResize();
        if (window.scrollY < 80) {
            setActive('hero');
        }
        pickVisibleSection();
    }

    function initBackToTop() {
        var btn = document.getElementById('htaBackTop');
        if (!btn) {
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var visible = false;

        function setVisible(next) {
            if (visible === next) {
                return;
            }
            visible = next;
            btn.classList.toggle('is-visible', next);
        }

        function update() {
            var doc = document.documentElement;
            var maxScroll = Math.max(0, (doc.scrollHeight || 0) - (window.innerHeight || 0));
            setVisible(maxScroll > 0 && window.scrollY >= maxScroll * 0.5);
        }

        function scrollToTop() {
            if (reduced || typeof gsap === 'undefined') {
                window.scrollTo(0, 0);
                return;
            }

            gsap.to(btn, {
                y: -8,
                duration: 0.18,
                yoyo: true,
                repeat: 1,
                ease: 'power2.out',
                overwrite: true
            });

            var proxy = { y: window.scrollY };
            gsap.to(proxy, {
                y: 0,
                duration: 0.9,
                ease: 'power3.inOut',
                overwrite: true,
                onUpdate: function () {
                    window.scrollTo(0, proxy.y);
                }
            });
        }

        window.addEventListener('scroll', update, { passive: true });
        btn.addEventListener('click', scrollToTop);
        update();
    }

    function initSubmitCountdown() {
        var root = document.querySelector('.hta-submit-countdown');
        if (!root) {
            return;
        }

        var targetStr = root.getAttribute('data-target');
        if (!targetStr) {
            return;
        }

        var target = new Date(targetStr).getTime();
        if (Number.isNaN(target)) {
            return;
        }

        var values = {
            days: root.querySelector('[data-unit="days"]'),
            hours: root.querySelector('[data-unit="hours"]'),
            minutes: root.querySelector('[data-unit="minutes"]'),
            seconds: root.querySelector('[data-unit="seconds"]'),
            milliseconds: root.querySelector('[data-unit="milliseconds"]')
        };

        function pad(value, length) {
            var text = String(value);
            while (text.length < length) {
                text = '0' + text;
            }
            return text;
        }

        var last = {
            days: null,
            hours: null,
            minutes: null,
            seconds: null,
            milliseconds: null
        };

        function setUnit(unit, text) {
            if (last[unit] === text || !values[unit]) {
                return;
            }
            last[unit] = text;
            values[unit].textContent = text;
        }

        function render() {
            var diff = Math.max(0, target - Date.now());
            var ms = diff % 1000;
            var totalSeconds = Math.floor(diff / 1000);
            var seconds = totalSeconds % 60;
            var totalMinutes = Math.floor(totalSeconds / 60);
            var minutes = totalMinutes % 60;
            var totalHours = Math.floor(totalMinutes / 60);
            var hours = totalHours % 24;
            var days = Math.floor(totalHours / 24);

            setUnit('days', String(days));
            setUnit('hours', pad(hours, 2));
            setUnit('minutes', pad(minutes, 2));
            setUnit('seconds', pad(seconds, 2));
            setUnit('milliseconds', pad(ms, 3));

            window.requestAnimationFrame(render);
        }

        window.requestAnimationFrame(render);
    }

    function initDatetimeField() {
        var input = document.getElementById('event_datetime');
        var display = document.getElementById('event_datetime_display');
        if (!input || !display) {
            return;
        }

        function formatValue(value) {
            // value: 2026-09-02T22:16 → 02/09/2026 22:16 (ASCII only, no Hebrew BiDi)
            if (!value || value.indexOf('T') === -1) {
                return '';
            }
            var parts = value.split('T');
            var date = parts[0].split('-');
            var time = (parts[1] || '').slice(0, 5);
            if (date.length !== 3 || !time) {
                return value.replace('T', ' ');
            }
            return date[2] + '/' + date[1] + '/' + date[0] + ' ' + time;
        }

        function syncDisplay() {
            var formatted = formatValue(input.value);
            display.textContent = formatted;
            display.classList.toggle('is-empty', !formatted);
        }

        input.addEventListener('input', syncDisplay);
        input.addEventListener('change', syncDisplay);
        input.addEventListener('blur', syncDisplay);
        if (input.form) {
            input.form.addEventListener('reset', function () {
                window.setTimeout(syncDisplay, 0);
            });
        }
        syncDisplay();
    }

    function initSubmitForm() {
        var panel = document.querySelector('.hta-submit-form-panel');
        if (panel) {
            panel.addEventListener('focusin', function () {
                panel.classList.add('is-draw');
            });
            panel.addEventListener('focusout', function (event) {
                var next = event.relatedTarget;
                if (next && panel.contains(next)) {
                    return;
                }
                panel.classList.remove('is-draw');
            });
        }

        var form = document.getElementById('hta-submit-form');
        if (!form || typeof htaLanding === 'undefined') {
            return;
        }

        var cityField = form.querySelector('.hta-city-field');
        var cityInput = form.querySelector('#event_location');
        var onlineInput = form.querySelector('#event_online');

        function syncOnlineField() {
            var isOnline = !!(onlineInput && onlineInput.checked);
            if (cityField) {
                cityField.hidden = isOnline;
                cityField.classList.toggle('is-online-hidden', isOnline);
            }
            if (cityInput) {
                cityInput.required = !isOnline;
                cityInput.setAttribute('aria-required', isOnline ? 'false' : 'true');
                if (isOnline) {
                    cityInput.setAttribute('aria-expanded', 'false');
                    cityInput.setAttribute('aria-invalid', 'false');
                    var cityErr = fieldErrorEl(cityInput);
                    if (cityErr) {
                        cityErr.textContent = '';
                        cityErr.hidden = true;
                    }
                    var cityIds = describedByWithoutError(cityInput);
                    if (cityIds.length) {
                        cityInput.setAttribute('aria-describedby', cityIds.join(' '));
                    } else {
                        cityInput.removeAttribute('aria-describedby');
                    }
                    var listbox = document.getElementById('event_location_listbox');
                    if (listbox) {
                        listbox.hidden = true;
                    }
                }
            }
        }

        if (onlineInput) {
            onlineInput.addEventListener('change', syncOnlineField);
            syncOnlineField();
        }

        var status = document.getElementById('hta-form-status');
        var nameInput = form.querySelector('#event_name');
        var companyInput = form.querySelector('#host_company');
        var datetimeInput = form.querySelector('#event_datetime');
        var agreeInput = form.querySelector('#event_agree');
        var fieldOrder = [nameInput, companyInput, datetimeInput, cityInput, agreeInput].filter(Boolean);

        function isOnlineChecked() {
            return !!(onlineInput && onlineInput.checked);
        }

        function fieldErrorEl(field) {
            return field ? document.getElementById(field.id + '_error') : null;
        }

        function describedByWithoutError(field) {
            var errId = field.id + '_error';
            var ids = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(function (id) {
                return id && id !== errId && id !== 'event_datetime_display';
            });
            if (!ids.length && field.id === 'event_location') {
                ids = ['event_location_hint'];
            }
            return ids;
        }

        function setFieldInvalid(field, message) {
            if (!field || !message) {
                return;
            }
            field.setAttribute('aria-invalid', 'true');
            var err = fieldErrorEl(field);
            var errId = field.id + '_error';
            if (err) {
                err.textContent = message;
                err.hidden = false;
            }
            var ids = describedByWithoutError(field);
            if (ids.indexOf(errId) === -1) {
                ids.unshift(errId);
            }
            field.setAttribute('aria-describedby', ids.join(' '));
        }

        function clearFieldInvalid(field) {
            if (!field) {
                return;
            }
            field.setAttribute('aria-invalid', 'false');
            var err = fieldErrorEl(field);
            if (err) {
                err.textContent = '';
                err.hidden = true;
            }
            var ids = describedByWithoutError(field);
            if (ids.length) {
                field.setAttribute('aria-describedby', ids.join(' '));
            } else {
                field.removeAttribute('aria-describedby');
            }
        }

        function clearAllFieldErrors() {
            fieldOrder.forEach(clearFieldInvalid);
        }

        function isFieldEmpty(field) {
            if (!field) {
                return true;
            }
            if (field.type === 'checkbox') {
                return !field.checked;
            }
            return !String(field.value || '').replace(/\s+/g, ' ').trim();
        }

        function validateAll(cityListOk) {
            var i18n = htaLanding.i18n || {};
            var firstInvalid = null;
            var invalidCount = 0;

            function fail(field, message) {
                setFieldInvalid(field, message);
                if (!firstInvalid) {
                    firstInvalid = field;
                }
                invalidCount += 1;
            }

            clearAllFieldErrors();

            if (isFieldEmpty(nameInput)) {
                fail(nameInput, i18n.name || 'יש להזין שם אירוע.');
            }
            if (isFieldEmpty(companyInput)) {
                fail(companyInput, i18n.company || 'יש להזין שם חברה מארחת.');
            }
            if (isFieldEmpty(datetimeInput)) {
                fail(datetimeInput, i18n.datetime || 'יש לבחור תאריך ושעה.');
            } else if (datetimeInput && !/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(datetimeInput.value)) {
                fail(datetimeInput, i18n.datetimeInvalid || 'תאריך או שעה לא תקינים.');
            }

            if (!isOnlineChecked() && cityInput) {
                if (isFieldEmpty(cityInput)) {
                    fail(cityInput, i18n.cityRequired || i18n.city || 'יש לבחור עיר או יישוב.');
                } else if (cityListOk === false) {
                    fail(cityInput, i18n.city || 'יש לבחור עיר או יישוב מתוך הרשימה.');
                }
            }

            if (agreeInput && !agreeInput.checked) {
                fail(agreeInput, i18n.agree || 'יש לאשר את התקנון, מדיניות הפרטיות ואת נכונות המידע.');
            }

            if (status) {
                if (invalidCount) {
                    var firstMessage = firstInvalid && fieldErrorEl(firstInvalid)
                        ? fieldErrorEl(firstInvalid).textContent
                        : '';
                    status.textContent = invalidCount === 1 && firstMessage
                        ? firstMessage
                        : (i18n.fixFields || 'יש לתקן את השדות המסומנים.');
                    status.className = 'text-sm min-h-5 is-error';
                } else {
                    status.textContent = '';
                    status.className = 'text-sm min-h-5';
                }
            }

            return firstInvalid;
        }

        function clearErrorOnEdit(field) {
            var err = fieldErrorEl(field);
            if (err && !err.hidden) {
                clearFieldInvalid(field);
            }
        }

        fieldOrder.forEach(function (field) {
            var evt = field.type === 'checkbox' || field.type === 'datetime-local' ? 'change' : 'input';
            field.addEventListener(evt, function () {
                clearErrorOnEdit(field);
            });
            if (field === cityInput) {
                field.addEventListener('change', function () {
                    clearErrorOnEdit(field);
                });
            }
        });

        function focusFirstInvalid(field) {
            if (!field) {
                return;
            }
            if (typeof field.scrollIntoView === 'function') {
                try {
                    field.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'instant' });
                } catch (err) {
                    field.scrollIntoView(true);
                }
            }
            window.requestAnimationFrame(function () {
                if (typeof field.focus === 'function') {
                    try {
                        field.focus({ preventScroll: true });
                    } catch (focusErr) {
                        field.focus();
                    }
                }
            });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var button = form.querySelector('button[type="submit"]');

            function send() {
                var data = new FormData(form);
                data.set('action', 'hta_submit_event');
                data.set('hta_nonce', htaLanding.nonce);

                if (button) {
                    button.disabled = true;
                    button.textContent = htaLanding.i18n.sending;
                }
                if (status) {
                    status.textContent = '';
                    status.className = 'text-sm min-h-5';
                }

                fetch(htaLanding.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (json) {
                        var ok = json && json.success;
                        var message = ok
                            ? (json.data && json.data.message) || htaLanding.i18n.success
                            : (json.data && json.data.message) || htaLanding.i18n.error;
                        if (status) {
                            status.textContent = message;
                            status.classList.add(ok ? 'is-ok' : 'is-error');
                        }
                        if (ok) {
                            form.reset();
                            clearAllFieldErrors();
                            syncOnlineField();
                            if (cityInput) {
                                cityInput.setAttribute('aria-invalid', 'false');
                                cityInput.setAttribute('aria-expanded', 'false');
                            }
                        }
                    })
                    .catch(function () {
                        if (status) {
                            status.textContent = htaLanding.i18n.error;
                            status.classList.add('is-error');
                        }
                    })
                    .finally(function () {
                        if (button) {
                            button.disabled = false;
                            button.textContent = 'שלחו להגשה';
                        }
                    });
            }

            function afterCityCheck(cityListOk) {
                var firstInvalid = validateAll(cityListOk);
                if (firstInvalid) {
                    focusFirstInvalid(firstInvalid);
                    return;
                }
                send();
            }

            if (!isOnlineChecked() && cityInput && !isFieldEmpty(cityInput) && typeof cityInput.htaCityValidate === 'function') {
                Promise.resolve(cityInput.htaCityValidate()).then(function (ok) {
                    afterCityCheck(!!ok);
                }).catch(function () {
                    afterCityCheck(false);
                });
                return;
            }

            afterCityCheck(true);
        });
    }
})();
