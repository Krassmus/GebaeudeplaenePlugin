<form class="default studip_form"
      method="post"
      action="<?= PluginEngine::getLink($plugin, array(), "info/edit/".$resource->getId()) ?>"
      data-dialog>
    <section>
        <legend><?= sprintf(_("Gebäudeplan-Information zu %s"), htmlReady($resource->name)) ?></legend>
        <label>
            <?= _("Top-Information") ?>
            <textarea style="width: 90%;" name="top_info" class="add_toolbar"><?= htmlReady($info['top_info']) ?></textarea>
        </label>
        <label>
            <?= _("Seiten-Information") ?>
            <textarea style="width: 90%;" name="side_info" class="add_toolbar"><?= htmlReady($info['side_info']) ?></textarea>
        </label>
        <label>
            <?= _("Unten-Information") ?>
            <textarea style="width: 90%;" name="bottom_info" class="add_toolbar"><?= htmlReady($info['bottom_info']) ?></textarea>
        </label>
    </section>
    <div data-dialog-button>
        <?= \Studip\Button::create(_("Speichern")) ?>
    </div>
</form>