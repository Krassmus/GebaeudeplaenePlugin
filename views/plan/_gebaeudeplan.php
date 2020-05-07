<div style="padding: 20px; display: flex; flex-direction: row; align-content: stretch; align-items: stretch; justify-content: space-evenly; height: 100vh;">
    <div style="width: 100%; display: flex; flex-direction: column; align-content: stretch; align-items: stretch; justify-content: center;">
        <? $resourceinfo = GPResourceInfo::find($resource->id) ?>
        <? if ($resourceinfo && $resourceinfo['top_info']) : ?>
            <div class="top_info" style="padding: 10px;">
                <?= formatReady($resourceinfo ? $resourceinfo['top_info'] : "") ?>
            </div>
        <? endif ?>
        <table class="default">
            <caption><?= htmlReady($resourceinfo['title'] ?: $resource->name) ?></caption>
            <thead>
                <tr>
                    <th><?= _("Beginn") ?></th>
                    <th><?= _("Ende") ?></th>
                    <th><?= _("Name") ?></th>
                    <th><?= _("Dozierende") ?></th>
                    <th><?= _("Raum") ?></th>
                </tr>
            </thead>
            <tbody>
            <? $max = 12 + (!$resourceinfo || !$resourceinfo['top_info'] ? 2 : 0) + (!$resourceinfo || !$resourceinfo['bottom_info'] ? 4 : 0) ?>
            <? if (count($dates)) : ?>
                <? foreach ($dates as $number => $date) : ?>
                    <? if ($number >= $max) { break; } ?>

                    <tr class="<?= $date['class'] ?>">
                        <td>
                            <?= date("G:i", $date['begin']) ?>
                        </td>
                        <td>
                            <?= date("G:i", $date['end']) ?>
                        </td>
                        <td>
                            <?= htmlReady($date['name']) ?>
                        </td>
                        <td>
                            <? if ($date['dozenten']) : ?>
                                <? foreach (explode(",", $date['dozenten']) as $count => $dozent_id) {
                                    echo $count > 0 ? ", " : "";
                                    $dozent = User::find($dozent_id);
                                    if ($dozent) {
                                        echo htmlReady(
                                        $dozent['title_front']
                                                ? $dozent['title_front']. " ". $dozent['nachname']
                                                : $dozent['vorname']. " ". $dozent['nachname']
                                        );
                                    }
                                } ?>
                            <? endif ?>
                        </td>
                        <td>
                            <?= htmlReady($date['room']) ?>
                        </td>
                    </tr>
                <? endforeach ?>
                <? for ($i = 0; $i < $max - count($dates); $i++) : ?>
                    <tr>
                        <td colspan="100">&nbsp;</td>
                    </tr>
                <? endfor ?>
            <? else : ?>
                <tr>
                    <td colspan="100" style="text-align: center;"><?= _("Heute keine Veranstaltungen mehr.") ?></td>
                    <? for ($i = 0; $i < $max - 1; $i++) : ?>
                        <tr>
                            <td colspan="100">&nbsp;</td>
                        </tr>
                    <? endfor ?>
                </tr>
            <? endif ?>
            </tbody>
        </table>
        <? if ($resourceinfo && $resourceinfo['bottom_info']) : ?>
            <div class="bottom_info" style="padding: 10px; min-height: 200px;">
                <?= formatReady($resourceinfo ? $resourceinfo['bottom_info'] : "") ?>
            </div>
        <? endif ?>
    </div>
    <? if ($resourceinfo && $resourceinfo['side_info']) : ?>
        <div class="side_info" style="display: flex; justify-content: center; align-items: center; padding: 10px;">
            <?= formatReady($resourceinfo ? $resourceinfo['side_info'] : "") ?>
        </div>
    <? endif ?>
</div>
