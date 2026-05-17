/*
 * DiviNationKit — Reading Progress Bar
 * Updates the fill width based on document scroll position.
 */
(function () {
    var fill = null;
    var ticking = false;

    function update() {
        if (!fill) return;
        var doc = document.documentElement;
        var max = (doc.scrollHeight || document.body.scrollHeight) - window.innerHeight;
        var pct = max > 0
            ? Math.min(100, Math.max(0, (window.scrollY / max) * 100))
            : 0;
        fill.style.width = pct + '%';
    }

    function onScroll() {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(function () {
            update();
            ticking = false;
        });
    }

    function init() {
        fill = document.querySelector('.dnk-progress-bar .dnk-progress-bar-fill');
        if (!fill) return;
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
