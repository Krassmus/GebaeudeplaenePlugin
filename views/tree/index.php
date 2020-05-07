<table class="default">
    <thead>
    <tr>
        <th><?= _("Name") ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? if (Request::option("resource_id")) : ?>
        <? $resource = Resource::find(Request::option("resource_id")) ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource['parent_id']), "tree/index") ?>">
                    <?= Icon::create("arr_1left", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
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
                        <?= Icon::create("edit", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                    </a>
                <? endif ?>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource->getId()), "plan") ?>"
                   title="<?= _("Plan anzeigen") ?>">
                    <?= Icon::create("tan3", "clickable")->asImg(20, array('class' => "text-bottom")) ?>
                </a>
                <a href="<?= PluginEngine::getLink($plugin, array('resource_id' => $resource->getId(), 'free' => 1), "plan") ?>"
                   title="<?= _("Plan mit freien Ortsangaben anzeigen") ?>">
                    <?= Icon::create("tan3+add", "clickable")->asImg(20, array("class" => "text-bottom")) ?>
                </a>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
