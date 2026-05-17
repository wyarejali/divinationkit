/*
 * DiviNationKit — Mobile Dropdown Menu
 * Per-item submenu toggler with slide animation.
 */
(function ($) {
    $(window).on('load', function () {
        var menuItems = $('.mobile_nav .menu-item-has-children > a');

        menuItems.each(function () {
            var link = $(this);

            if (link.next('.mobile_menu_toggler').length) {
                return;
            }

            var toggler = $('<a href="#" class="mobile_menu_toggler" aria-expanded="false"></a>');
            link.after(toggler);

            var $subMenu = link.nextAll('.sub-menu').first();

            if ($subMenu.length) {
                var $newSubMenu = $('<ol>').addClass('dina_sub_menu');
                $subMenu.children().appendTo($newSubMenu);
                $subMenu.replaceWith($newSubMenu);
            }
        });

        $('.mobile_nav .menu-item-has-children > a + .mobile_menu_toggler').on('click', function (e) {
            e.preventDefault();
            var toggle  = $(this);
            var submenu = toggle.next('.dina_sub_menu');

            toggle.toggleClass('menu-open');
            toggle.attr('aria-expanded', toggle.hasClass('menu-open') ? 'true' : 'false');

            if (submenu.length) {
                submenu.slideToggle(300);
            }
        });
    });
})(jQuery);
