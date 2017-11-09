<table>
    <? foreach ($dates as $date) : ?>
    <tr>
        <td>
            <?= date("G:h", $date['date']) ?>
        </td>
    </tr>
    <? endforeach ?>
</table>