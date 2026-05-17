/*
 * DiviNationKit — Expandable Text Block
 * Replaces the inline <script> the legacy shortcode used to emit.
 */
(function () {
    function init() {
        var wrappers = document.querySelectorAll('.expandable-wrapper');
        wrappers.forEach(function (wrapper) {
            if (wrapper.dataset.dnkExpInit) return;
            wrapper.dataset.dnkExpInit = '1';

            var content = wrapper.querySelector('.exp-content');
            var toggle  = wrapper.querySelector('.exp-toggle');
            if (!content || !toggle) return;

            var more = toggle.dataset.more || toggle.textContent;
            var less = toggle.dataset.less || 'Read Less';

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                var expanded = wrapper.classList.toggle('is-expanded');

                if (expanded) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    toggle.textContent = less;
                } else {
                    content.style.maxHeight = '';
                    toggle.textContent = more;
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
