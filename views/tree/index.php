<table class="default">
    <thead>
    <tr>
        <th><?= _("Name") ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? if (Request::option("resource_id")) : ?>
        <? $resource = GPResource::find(Request::option("resource_id")) ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource['parent_id']), "tree/index") ?>">
                    ..
                </a>
            </td>
            <td></td>
        </tr>
    <? endif ?>
    <? foreach ($resources as $resource) : ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource->getId()), "tree/index") ?>">
                    <?= htmlReady($resource['name']) ?>
                </a>
            </td>
            <td class="actions">
                <? if ($GLOBALS['perm']->have_perm("admin")) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array(), "info/edit/".$resource->getId()) ?>" data-dialog>
                        <?= Assets::img("icons/20/blue/edit") ?>
                    </a>
                <? endif ?>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource->getId()), "plan") ?>"
                   title="<?= _("Plan anzeigen") ?>">
                    <?= Assets::img("icons/20/blue/tan3") ?>
                </a>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>