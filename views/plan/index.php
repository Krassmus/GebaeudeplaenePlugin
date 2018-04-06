<?= \Studip\LinkButton::create(_("Zurück"), PluginEngine::getURL($plugin, array('resource_id' => $resource['parent_id']), "tree/index")) ?>
<?= \Studip\LinkButton::create(_("Vollbild"), "#", array('onclick' => "STUDIP.showGebaeudeplan(); return false;")) ?>
<?= \Studip\LinkButton::create(_("Kiosk-Modus"), PluginEngine::getURL($plugin, array('resource_id' => $resource->getId()), "plan/kiosk"), array()) ?>
<?= \Studip\LinkButton::create(_("Informationen bearbeiten"), PluginEngine::getURL($plugin, array(), "info/edit/".$resource->getId()), array('data-dialog' => 1)) ?>

<div id="gebaeudeplan" style="background: white;" data-resource_id="<?= htmlReady(Request::option("resource_id")) ?>">
    <?= $this->render_partial("plan/_gebaeudeplan") ?>
</div>
