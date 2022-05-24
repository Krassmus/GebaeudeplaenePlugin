jQuery(function () {
    window.setInterval(function () {
        jQuery.ajax({
            url: STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/gebaeudeplaeneplugin/plan/get_update",
            data: {
                "resource_id": jQuery("#gebaeudeplan").data("resource_id"),
                "free": jQuery("#gebaeudeplan").data("free")
            },
            success: function (html) {
                jQuery("#gebaeudeplan").html(html);
            }
        });
    }, 1000 * 10); //60
});

STUDIP.showGebaeudeplan = function () {
    var plan = jQuery("#gebaeudeplan")[0];
    if (plan.requestFullscreen) {
        plan.requestFullscreen();
    } else if (plan.msRequestFullscreen) {
        plan.msRequestFullscreen();
    } else if (plan.mozRequestFullScreen) {
        plan.mozRequestFullScreen();
    } else if (plan.webkitRequestFullscreen) {
        plan.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
};

