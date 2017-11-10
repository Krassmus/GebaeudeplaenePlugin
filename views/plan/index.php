<?= \Studip\LinkButton::create(_("Vollbild"), "#", array('onclick' => "STUDIP.showGebaeudeplan(); return false;")) ?>
<?= \Studip\LinkButton::create(_("Informationen bearbeiten"), PluginEngine::getLink($plugin, array(), "info/edit/".$resource->getId()), array('data-dialog' => 1)) ?>

<div id="gebaeudeplan" style="background: white;">
    <?= $this->render_partial("plan/_gebaeudeplan") ?>
</div>

<script>
    jQuery(function () {
        window.setInterval(function () {
            jQuery.ajax({
                url: STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/gebaeudeplaeneplugin/plan/get_update",
                data: {
                    "resource_id": "<?= htmlReady(Request::option("resource_id")) ?>"
                },
                success: function (html) {
                    jQuery("#gebaeudeplan").html(html);
                }
            });
        }, 1000 * 6000);
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
</script>