<?php
function f_d($var)
{
    if ($var == 0) return "&nbsp;"; else
        return sprintf("%22.2f", $var);
}

function f_d2($var)
{
    return sprintf("%22.2f", $var);
} ?>
<b>Оплата за период по организации:</b><br><br><br>
<table border=1px width=100% style="border: black;" cellspacing=0px cellpadding=0px>
    <tr>
        <td><b>Оплата с ндс</b></td>
        <td><b>Ндс</b></td>
        <td><b>Номер счета</b></td>
        <td><b>Расшифровка счета</b></td>
        <td width=200px><b>Дата</b></td>
    </tr>
    <?php
    $sum = 0;
    $sum_nds = 0;
    foreach ($firm_oplata as $o):
        $sum += $o->value * ($o->nds + 100) / 100;
        $sum_nds += $o->value * $o->nds / 100;
        echo "<tr><td align='right'>" . f_d($o->value * ($o->nds + 100) / 100) . "</td><td align='right'>" . f_d($o->value * $o->nds / 100) . "</td><td  align='center'>"
            . $o->number .
            "</td><td>"
            . $o->name .
            "</td><td  align='center'>"
            . $o->data .
            "</td></tr>";
    endforeach;

    ?>
    <tr>
        <td><b>Итого</b></td>
        <td>.</td>
        <td>.</td>
        <td>.</td>
        <td>.</td>
    </tr>
    <tr>
        <td><?php echo f_d2($sum) ?></td>
        <td><?php echo f_d2($sum_nds); ?></td>
        <td>.</td>
        <td>.</td>
        <td>.</td>
    </tr>
</table>