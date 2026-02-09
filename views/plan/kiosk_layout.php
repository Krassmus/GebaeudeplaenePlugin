<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="WINDOWS-1252">
    <title>
        <?= htmlReady(PageLayout::getTitle() . ' - ' . $GLOBALS['UNI_NAME_CLEAN']) ?>
    </title>
    <script>
        CKEDITOR_BASEPATH = "<?= Assets::url('javascripts/ckeditor/') ?>";
        String.locale = "<?= htmlReady(strtr($_SESSION['_language'], '_', '-')) ?>";

        document.querySelector('html').classList.replace('no-js', 'js');
        setTimeout(() => {
            // This needs to be put in a timeout since otherwise it will not match
            if (window.matchMedia('(max-width: 767px)').matches) {
                document.querySelector('html').classList.add('responsive-display');
            }
        }, 0);

        window.STUDIP = {
            ABSOLUTE_URI_STUDIP: "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>",
            ASSETS_URL: "<?= $GLOBALS['ASSETS_URL'] ?>",
            CSRF_TOKEN: {
                name: '<?=CSRFProtection::TOKEN?>',
                value: '<? try {echo CSRFProtection::token();} catch (SessionRequiredException $e){}?>'
            },
            STUDIP_SHORT_NAME: "<?= htmlReady(Config::get()->STUDIP_SHORT_NAME) ?>",
            URLHelper: {
                base_url: "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>",
                parameters: <?= json_encode(URLHelper::getLinkParams(), JSON_FORCE_OBJECT) ?>
            },
            jsupdate_enable: <?= json_encode(
                is_object($GLOBALS['perm']) &&
                $GLOBALS['perm']->have_perm('autor') &&
                PersonalNotifications::isActivated()) ?>,
            wysiwyg_enabled: <?= json_encode((bool) Config::get()->WYSIWYG) ?>,
            server_timestamp: <?= time() ?>
        }
    </script>
    <? if ($_SESSION['_language'] !== 'de_DE'): ?>
        <link rel="localization" hreflang="<?= htmlReady(strtr($_SESSION['_language'], '_', '-')) ?>"
              href="<?= URLHelper::getScriptLink('dispatch.php/localizations/' . $_SESSION['_language']) ?>" type="application/vnd.oftn.l10n+json">
    <? endif ?>

    <?= PageLayout::getHeadElements() ?>

    <script>
        window.STUDIP.editor_enabled = <?= json_encode((bool) Studip\Markup::editorEnabled()) ?> && CKEDITOR.env.isCompatible;
    </script>

</head>

<body>
    <div id="gebaeudeplan"
         style="background: white;"
         data-resource_id="<?= htmlReady(Request::option("resource_id")) ?>"
         data-free="<?= Request::int("free") ?>">
        <?= $this->render_partial("plan/_gebaeudeplan") ?>
    </div>
</body>
</html>
