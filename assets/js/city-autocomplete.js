(function () {
    'use strict';

    var FINAL_MAP = {
        'ך': 'כ',
        'ם': 'מ',
        'ן': 'נ',
        'ף': 'פ',
        'ץ': 'צ'
    };

    function normalizeHe(value) {
        return String(value || '')
            .replace(/[\u0591-\u05C7]/g, '')
            .replace(/[ךםןףץ]/g, function (ch) {
                return FINAL_MAP[ch] || ch;
            })
            .replace(/['’ʻʹ`״"]/g, '')
            .replace(/[־–—-]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase();
    }

    function debounce(fn, wait) {
        var timer = 0;
        return function () {
            var ctx = this;
            var args = arguments;
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                fn.apply(ctx, args);
            }, wait);
        };
    }

    function initCityAutocomplete(input) {
        if (!input || input.dataset.htaCityReady === '1') {
            return;
        }

        var combobox = input.closest('.hta-city-combobox') || input.parentElement;
        var listbox = document.getElementById(input.getAttribute('aria-controls') || 'event_location_listbox');
        if (!combobox || !listbox) {
            return;
        }

        input.dataset.htaCityReady = '1';

        var cities = null;
        var citiesPromise = null;
        var activeIndex = -1;
        var matches = [];
        var selectedValue = '';
        var maxResults = 12;

        function citiesUrl() {
            return (typeof htaLanding !== 'undefined' && htaLanding.citiesUrl) || '';
        }

        function loadCities() {
            if (cities) {
                return Promise.resolve(cities);
            }
            if (citiesPromise) {
                return citiesPromise;
            }
            var url = citiesUrl();
            if (!url) {
                return Promise.resolve([]);
            }
            citiesPromise = fetch(url, { credentials: 'same-origin' })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    var list = Array.isArray(data) ? data : (data && data.cities) || [];
                    cities = list
                        .filter(function (name) {
                            return typeof name === 'string' && name.trim();
                        })
                        .map(function (name) {
                            return name.replace(/\s+/g, ' ').trim();
                        });
                    return cities;
                })
                .catch(function () {
                    cities = [];
                    return cities;
                });
            return citiesPromise;
        }

        function setExpanded(open) {
            input.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                listbox.hidden = false;
            } else {
                listbox.hidden = true;
                activeIndex = -1;
                input.removeAttribute('aria-activedescendant');
            }
        }

        function clearActive() {
            var options = listbox.querySelectorAll('[role="option"]');
            options.forEach(function (opt) {
                opt.setAttribute('aria-selected', 'false');
                opt.classList.remove('is-active');
            });
            activeIndex = -1;
            input.removeAttribute('aria-activedescendant');
        }

        function setActive(index) {
            var options = listbox.querySelectorAll('[role="option"]');
            if (!options.length) {
                clearActive();
                return;
            }
            if (index < 0) {
                index = options.length - 1;
            }
            if (index >= options.length) {
                index = 0;
            }
            clearActive();
            activeIndex = index;
            var opt = options[activeIndex];
            opt.setAttribute('aria-selected', 'true');
            opt.classList.add('is-active');
            input.setAttribute('aria-activedescendant', opt.id);
            if (typeof opt.scrollIntoView === 'function') {
                opt.scrollIntoView({ block: 'nearest' });
            }
        }

        function selectCity(name) {
            selectedValue = name;
            input.value = name;
            input.setAttribute('aria-invalid', 'false');
            setExpanded(false);
            listbox.innerHTML = '';
        }

        function markInvalidSelection() {
            if (selectedValue && input.value === selectedValue) {
                input.setAttribute('aria-invalid', 'false');
                return true;
            }
            selectedValue = '';
            input.setAttribute('aria-invalid', input.value.trim() ? 'true' : 'false');
            return false;
        }

        function rankMatches(query, list) {
            var q = normalizeHe(query);
            if (q.length < 2) {
                return [];
            }
            var starts = [];
            var contains = [];
            for (var i = 0; i < list.length; i++) {
                var name = list[i];
                var norm = normalizeHe(name);
                if (!norm) {
                    continue;
                }
                if (norm.indexOf(q) === 0) {
                    starts.push(name);
                } else if (norm.indexOf(q) !== -1) {
                    contains.push(name);
                }
            }
            return starts.concat(contains).slice(0, maxResults);
        }

        function renderMatches(items, query) {
            matches = items;
            listbox.innerHTML = '';
            if (!query || normalizeHe(query).length < 2) {
                setExpanded(false);
                return;
            }
            if (!items.length) {
                var empty = document.createElement('li');
                empty.className = 'hta-city-empty';
                empty.setAttribute('role', 'presentation');
                empty.textContent = 'לא נמצאו יישובים תואמים';
                listbox.appendChild(empty);
                setExpanded(true);
                clearActive();
                return;
            }

            items.forEach(function (name, index) {
                var option = document.createElement('li');
                option.id = input.id + '-opt-' + index;
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.className = 'hta-city-option';
                option.textContent = name;
                option.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    selectCity(name);
                });
                listbox.appendChild(option);
            });
            setExpanded(true);
            clearActive();
        }

        var runSearch = debounce(function () {
            var value = input.value;
            if (selectedValue && value !== selectedValue) {
                selectedValue = '';
            }
            markInvalidSelection();
            loadCities().then(function (list) {
                renderMatches(rankMatches(value, list), value);
            });
        }, 280);

        input.addEventListener('input', function () {
            runSearch();
        });

        input.addEventListener('focus', function () {
            loadCities();
            if (normalizeHe(input.value).length >= 2 && !selectedValue) {
                runSearch();
            }
        });

        input.addEventListener('blur', function () {
            window.setTimeout(function () {
                setExpanded(false);
                markInvalidSelection();
            }, 120);
        });

        input.addEventListener('keydown', function (event) {
            var open = input.getAttribute('aria-expanded') === 'true';
            var key = event.key;

            if (key === 'ArrowDown') {
                event.preventDefault();
                if (!open) {
                    runSearch();
                    loadCities().then(function (list) {
                        renderMatches(rankMatches(input.value, list), input.value);
                        setActive(0);
                    });
                    return;
                }
                setActive(activeIndex + 1);
                return;
            }

            if (key === 'ArrowUp') {
                if (!open) {
                    return;
                }
                event.preventDefault();
                setActive(activeIndex - 1);
                return;
            }

            if (key === 'Enter') {
                if (open && activeIndex >= 0 && matches[activeIndex]) {
                    event.preventDefault();
                    selectCity(matches[activeIndex]);
                }
                return;
            }

            if (key === 'Escape') {
                if (open) {
                    event.preventDefault();
                    setExpanded(false);
                }
            }
        });

        if (input.form) {
            input.form.addEventListener('reset', function () {
                selectedValue = '';
                setExpanded(false);
                listbox.innerHTML = '';
                input.setAttribute('aria-invalid', 'false');
            });
        }

        input.htaCityIsValid = function () {
            return !!(selectedValue && input.value === selectedValue);
        };

        input.htaCityValidate = function () {
            var value = input.value.replace(/\s+/g, ' ').trim();
            return loadCities().then(function (list) {
                if (selectedValue && value === selectedValue) {
                    input.setAttribute('aria-invalid', 'false');
                    return true;
                }
                if (list.indexOf(value) !== -1) {
                    selectCity(value);
                    return true;
                }
                selectedValue = '';
                input.setAttribute('aria-invalid', value ? 'true' : 'false');
                return false;
            });
        };
    }

    function boot() {
        var input = document.getElementById('event_location');
        if (input) {
            initCityAutocomplete(input);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
