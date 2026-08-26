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
        initSectionRules();
        initHeroLogo();
        initHeroVideo();
        initHeroFlip();
        initOdometers();
        initFadeUpSections();
        initSubmitParticles();
        initAboutTilt();
        initMagneticButtons();
        initBackToTop();
        initSubmitForm();
        initSubmitCountdown();
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
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var isOpen = false;
        var animated = logo ? [mobileMenu, logo].concat(Array.prototype.slice.call(links)) : [mobileMenu].concat(Array.prototype.slice.call(links));

        function setHamburgerState(open) {
            menuBtn.classList.remove('is-open');
            closeBtn.classList.toggle('is-open', open);
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuBtn.setAttribute('aria-label', open ? 'סגור תפריט' : 'פתח תפריט');
        }

        function hideMenu() {
            mobileMenu.classList.add('opacity-0', 'pointer-events-none');
            mobileMenu.classList.remove('pointer-events-auto', 'is-open');
            document.body.style.overflow = '';
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
                        y: 18,
                        duration: 0.2,
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
                }
                return;
            }

            gsap.killTweensOf(animated);
            gsap.set(mobileMenu, { opacity: 0 });
            gsap.set(links, { opacity: 0, y: 28, scale: 0.94 });
            if (logo) {
                gsap.set(logo, { opacity: 0, y: 36 });
            }
            gsap.to(mobileMenu, {
                opacity: 1,
                duration: 0.35,
                ease: 'power2.out'
            });
            gsap.to(links, {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.48,
                stagger: 0.05,
                delay: 0.16,
                ease: 'power3.out',
                onComplete: function () {
                    if (!isOpen || !logo) {
                        return;
                    }
                    gsap.to(logo, {
                        opacity: 1,
                        y: 0,
                        duration: 0.55,
                        ease: 'power3.out'
                    });
                }
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
                        slidesPerView: 4,
                        centeredSlides: false,
                        spaceBetween: 24
                    }
                }
            }));
        }

        if (document.querySelector('.hta-media-swiper')) {
            var mediaRoot = document.querySelector('.hta-media-swiper');
            var uniqueCount = mediaRoot.querySelectorAll('.swiper-wrapper > .swiper-slide').length;
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
                    delay: 6000,
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
                    slideChange: function (swiper) {
                        syncMediaBullets(swiper, uniqueCount);
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
            gsap.from(el, {
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
            if (pipe !== -1) {
                var ltr = document.createElement('span');
                ltr.className = 'hta-bidi-ltr';
                ltr.setAttribute('dir', 'ltr');
                fillOdometer(ltr, text.slice(0, pipe));
                frag.appendChild(ltr);
                fillOdometer(frag, text.slice(pipe));
            } else {
                var wrap = document.createElement('span');
                wrap.className = 'hta-bidi-ltr';
                wrap.setAttribute('dir', 'ltr');
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

    function wrapHeroLtrRuns(el) {
        var text = el.textContent || '';
        el.innerHTML = text.replace(/(\d[\d.,\-\/]*)/g, '<span class="hta-bidi-ltr" dir="ltr">$1</span>');
    }

    function splitWordsFallback(el) {
        var text = (el.textContent || '').trim();
        var parts = text ? text.split(/\s+/) : [];
        var words = [];
        el.textContent = '';
        parts.forEach(function (part, index) {
            var span = document.createElement('span');
            span.className = 'word';
            span.style.display = 'inline-block';
            span.textContent = part;
            el.appendChild(span);
            words.push(span);
            if (index < parts.length - 1) {
                el.appendChild(document.createTextNode(' '));
            }
        });
        return words;
    }

    function initHeroFlip() {
        var el = document.querySelector('.hta-hero-title');
        if (!el || typeof gsap === 'undefined') {
            return;
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        // SplitText wraps words in spans — breaks background-clip gradient (blank title).
        // Keep the same Fade Up motion on the whole title in gradient mode.
        if (el.classList.contains('is-fill-gradient')) {
            gsap.fromTo(
                el,
                { y: 40, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.8, ease: 'power2.out' }
            );
            return;
        }

        wrapHeroLtrRuns(el);

        var words;
        if (typeof SplitText !== 'undefined') {
            if (typeof gsap.registerPlugin === 'function') {
                gsap.registerPlugin(SplitText);
            }
            if (typeof SplitText.create === 'function') {
                words = SplitText.create(el, { type: 'words' }).words;
            } else {
                words = new SplitText(el, { type: 'words' }).words;
            }
        } else {
            words = splitWordsFallback(el);
        }

        if (!words || !words.length) {
            return;
        }

        el.classList.add('is-split-words');

        gsap.fromTo(
            words,
            { y: 40, opacity: 0 },
            { y: 0, opacity: 1, stagger: 0.15, duration: 0.8, ease: 'power2.out' }
        );
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

    function initSubmitParticles() {
        var section = document.getElementById('submit-event');
        var field = section && section.querySelector('.hta-particle-field');
        if (!section || !field || typeof gsap === 'undefined') {
            return;
        }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        var count = window.matchMedia('(max-width: 767px)').matches ? 28 : 52;
        var tweens = [];
        var i;
        var particle;
        var size;
        var startOpacity;

        for (i = 0; i < count; i++) {
            particle = document.createElement('span');
            var roll = Math.random();
            if (roll < 0.42) {
                size = gsap.utils.random(1, 1.8);
            } else if (roll < 0.78) {
                size = gsap.utils.random(1.8, 3);
            } else if (roll < 0.94) {
                size = gsap.utils.random(3, 4.2);
            } else {
                size = gsap.utils.random(4.2, 5.5);
            }
            startOpacity = gsap.utils.random(0.22, 0.58);
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            particle.style.boxShadow = '0 0 ' + (size * 1.4) + 'px rgb(34 211 238 / 0.5), 0 0 ' + (size * 2.6) + 'px rgb(34 211 238 / 0.2)';
            particle.style.left = gsap.utils.random(1, 99) + '%';
            particle.style.top = gsap.utils.random(8, 96) + '%';
            field.appendChild(particle);

            gsap.set(particle, { opacity: startOpacity, x: 0, y: 0 });
            tweens.push(gsap.to(particle, {
                y: gsap.utils.random(-180, -60),
                x: gsap.utils.random(-40, 40),
                opacity: 0.08,
                duration: gsap.utils.random(2.8, 5.5),
                delay: gsap.utils.random(0, 3.5),
                repeat: -1,
                ease: 'none'
            }));
        }

        if (typeof ScrollTrigger === 'undefined') {
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

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var button = form.querySelector('button[type="submit"]');
            var status = document.getElementById('hta-form-status');
            var agree = form.querySelector('#event_agree');
            var cityInput = form.querySelector('#event_location');

            if (agree && !agree.checked) {
                if (status) {
                    status.textContent = htaLanding.i18n.agree || 'יש לאשר את התקנון, מדיניות הפרטיות ואת נכונות המידע.';
                    status.className = 'text-sm min-h-5 is-error';
                }
                if (agree.focus) {
                    agree.focus();
                }
                return;
            }

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

            if (cityInput && typeof cityInput.htaCityValidate === 'function') {
                cityInput.htaCityValidate().then(function (ok) {
                    if (!ok) {
                        if (status) {
                            status.textContent = htaLanding.i18n.city || 'יש לבחור עיר או יישוב מתוך הרשימה.';
                            status.className = 'text-sm min-h-5 is-error';
                        }
                        cityInput.focus();
                        return;
                    }
                    send();
                });
                return;
            }

            send();
        });
    }
})();
