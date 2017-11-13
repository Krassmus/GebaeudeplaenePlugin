<div style="padding: 20px; display: flex; flex-direction: row; align-content: stretch; align-items: stretch; justify-content: space-evenly; height: 100vh;">
    <div style="width: 100%; display: flex; flex-direction: column; align-content: stretch; align-items: stretch; justify-content: space-evenly;">
        <? if ($resource->info && $resource->info['top_info']) : ?>
            <div class="top_info" style="padding: 10px;">
                <?= formatReady($resource->info ? $resource->info['top_info'] : "") ?>
            </div>
        <? endif ?>
        <table class="default">
            <caption><?= htmlReady($resource->name) ?></caption>
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
            <? $max = 12 + (!$resource->info || !$resource->info['top_info'] ? 2 : 0) + (!$resource->info || !$resource->info['bottom_info'] ? 4 : 0) ?>
            <? if (count($dates)) : ?>
                <? foreach ($dates as $number => $date) : ?>
                    <? if ($number >= $max) { break; } ?>
                    <tr class="<?= is_a($date, "CourseExDate") ? "ex_termin" : "" ?>">
                        <td>
                            <?= date("G:i", $date['date']) ?>
                        </td>
                        <td>
                            <?= date("G:i", $date['end_time']) ?>
                        </td>
                        <td>
                            <?= htmlReady($date->course->name) ?>
                        </td>
                        <td>
                            <ul class="clean">
                                <? if (count($date->dozenten)) : ?>
                                    <? foreach ($date->dozenten as $dozent) : ?>
                                        <li><?= htmlReady($dozent->getFullname()) ?></li>
                                    <? endforeach ?>
                                <? else : ?>
                                    <? foreach ($date->course->members->filter(function ($member, $value) { return $member['status'] === "dozent"; }) as $member) : ?>
                                        <li><?= htmlReady($member->getUserFullname()) ?></li>
                                    <? endforeach ?>
                                <? endif ?>
                            </ul>
                        </td>
                        <td>
                            <? if (!is_a($date, "CourseExDate")) : ?>
                                <?= htmlReady($date->getRoomName()) ?>
                            <? else : ?>
                                <? $room = GPResource::find($date['resource_id']) ?>
                                <?= htmlReady($room ? $room['name'] : "") ?>
                            <? endif ?>
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
        <? if ($resource->info && $resource->info['bottom_info']) : ?>
            <div class="bottom_info" style="padding: 10px; min-height: 200px;">
                <?= formatReady($resource->info ? $resource->info['bottom_info'] : "") ?>
            </div>
        <? endif ?>
    </div>
    <? if ($resource->info && $resource->info['side_info']) : ?>
        <div class="side_info" style="display: flex; justify-content: center; align-items: center; padding: 10px;">
            <?= formatReady($resource->info ? $resource->info['side_info'] : "") ?>
        </div>
    <? endif ?>
</div>