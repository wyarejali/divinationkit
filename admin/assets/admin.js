/*
 * DiviNationKit — Admin JS
 *   - Custom color + alpha picker (writes rgba() to a hidden input)
 *   - Range <-> number two-way binding
 *   - Tools feature toggle + collapse (collapsed by default)
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        cleanSavedFlag();
        initColorPickers();
        initRangeBindings();
        initFeatureCards();
        initCopyButtons();
    });

    /* ---------- post-save notice cleanup ---------- */

    function cleanSavedFlag() {
        // Strip ?dnk-saved=1 from the URL so reloads do not re-show the notice.
        if (window.history && window.history.replaceState) {
            try {
                var url = new URL(window.location.href);
                if (url.searchParams.has('dnk-saved')) {
                    url.searchParams.delete('dnk-saved');
                    var qs = url.searchParams.toString();
                    var clean = url.pathname + (qs ? '?' + qs : '') + url.hash;
                    window.history.replaceState({}, document.title, clean);
                }
            } catch (e) { /* noop */ }
        }

        var notice = document.querySelector('.dnk-notice-success');
        if (!notice) return;

        setTimeout(function () {
            notice.style.transition = 'opacity 0.35s ease';
            notice.style.opacity = '0';
            setTimeout(function () { notice.parentNode && notice.parentNode.removeChild(notice); }, 360);
        }, 3500);
    }

    /* ---------- copy-to-clipboard buttons ---------- */

    function initCopyButtons() {
        var buttons = document.querySelectorAll('.dnk-copy');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy') || '';
                var done = function () {
                    var original = btn.dataset.originalLabel || btn.textContent;
                    btn.dataset.originalLabel = original;
                    btn.textContent = btn.getAttribute('data-copied-label') || 'Copied!';
                    btn.classList.add('is-copied');
                    setTimeout(function () {
                        btn.textContent = original;
                        btn.classList.remove('is-copied');
                    }, 1600);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done).catch(fallback);
                } else {
                    fallback();
                }

                function fallback() {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); } catch (e) {}
                    document.body.removeChild(ta);
                    done();
                }
            });
        });
    }

    /* ---------- color picker ---------- */

    function parseColor(value) {
        if (typeof value !== 'string') return { r: 0, g: 0, b: 0, a: 1 };
        var v = value.trim();
        var m;

        m = v.match(/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([0-9]*\.?[0-9]+)\s*)?\)$/i);
        if (m) {
            return {
                r: clamp(parseInt(m[1], 10), 0, 255),
                g: clamp(parseInt(m[2], 10), 0, 255),
                b: clamp(parseInt(m[3], 10), 0, 255),
                a: m[4] !== undefined ? clamp(parseFloat(m[4]), 0, 1) : 1
            };
        }

        m = v.match(/^#?([a-f0-9]{6})([a-f0-9]{2})?$/i);
        if (m) {
            var hex = m[1];
            var alphaHex = m[2];
            return {
                r: parseInt(hex.substr(0, 2), 16),
                g: parseInt(hex.substr(2, 2), 16),
                b: parseInt(hex.substr(4, 2), 16),
                a: alphaHex ? parseInt(alphaHex, 16) / 255 : 1
            };
        }

        m = v.match(/^#?([a-f0-9])([a-f0-9])([a-f0-9])$/i);
        if (m) {
            return {
                r: parseInt(m[1] + m[1], 16),
                g: parseInt(m[2] + m[2], 16),
                b: parseInt(m[3] + m[3], 16),
                a: 1
            };
        }

        return { r: 0, g: 0, b: 0, a: 1 };
    }

    function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function (c) {
            var h = c.toString(16);
            return h.length === 1 ? '0' + h : h;
        }).join('');
    }

    function formatRgba(c) {
        var a = Math.round(c.a * 100) / 100;
        return 'rgba(' + c.r + ',' + c.g + ',' + c.b + ',' + a + ')';
    }

    function initColorPickers() {
        var controls = document.querySelectorAll('.dnk-color-control');
        controls.forEach(function (control) {
            var hidden = control.querySelector('.dnk-color-input');
            var hex    = control.querySelector('.dnk-color-hex');
            var alpha  = control.querySelector('.dnk-color-alpha');
            var alphaVal = control.querySelector('.dnk-color-alpha-val');
            var fill   = control.querySelector('.dnk-color-swatch-fill');

            var current = parseColor(hidden.value || control.getAttribute('data-default') || '#10b882');

            function sync(write) {
                hex.value = rgbToHex(current.r, current.g, current.b);
                alpha.value = Math.round(current.a * 100);
                alphaVal.textContent = Math.round(current.a * 100) + '%';
                fill.style.background = formatRgba(current);
                if (write) {
                    hidden.value = formatRgba(current);
                }
            }

            sync(false);

            hex.addEventListener('input', function () {
                var parsed = parseColor(hex.value);
                current.r = parsed.r; current.g = parsed.g; current.b = parsed.b;
                sync(true);
            });

            alpha.addEventListener('input', function () {
                current.a = clamp(parseInt(alpha.value, 10), 0, 100) / 100;
                sync(true);
            });
        });
    }

    /* ---------- range <-> number binding ---------- */

    function initRangeBindings() {
        var bound = document.querySelectorAll('input[data-bind]');
        bound.forEach(function (input) {
            input.addEventListener('input', function () {
                var target = document.getElementById(input.getAttribute('data-bind'));
                if (target && target.value !== input.value) {
                    target.value = input.value;
                }
            });
        });
    }

    /* ---------- feature cards (Tools tab) ---------- */

    function initFeatureCards() {
        var features = document.querySelectorAll('.dnk-feature');
        features.forEach(function (feature) {
            var toggle   = feature.querySelector('.dnk-feature-toggle');
            var collapse = feature.querySelector('.dnk-collapse-toggle');
            var body     = feature.querySelector('.dnk-feature-body');

            // toggle just controls the enabled state — does not auto-expand/collapse
            toggle.addEventListener('change', function () {
                feature.classList.toggle('is-enabled', toggle.checked);
            });

            collapse.addEventListener('click', function () {
                var expanded = collapse.getAttribute('aria-expanded') === 'true';
                collapse.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                if (expanded) {
                    body.setAttribute('hidden', 'hidden');
                } else {
                    body.removeAttribute('hidden');
                }
            });
        });
    }
})();
