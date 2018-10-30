<html>
<head>
<title>Учет электроэнергии. Промышленный отдел</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>
<center>
<h2>Сведения о расходе электроэнергии</h2>
</center>
<table width=100%> 
<tr>
<td align=left colspan=3><b>Поставщик</b> "Зеренда Энерго"</td>
<td align=right><b>Тел.</b> ____________</td>
</tr>
<tr>

<?php foreach($akt->result() as $point): 
$firm_name = $point->firm_name;
$firm_rnn = $point->rnn;
$firm_dogovor = $point->dogovor;
endforeach;
?>
<td align=left><b>Покупатель</b> <?php echo $firm_name;?></td>
<td align=left><b>РНН</b> <?php echo $firm_rnn;?></td>
<td align=left><b>Абонент</b> <?php echo $firm_dogovor;?></td>
</tr>
</table> 

<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr> 
 <td>П/П</td>
 <td>№ Т.У.</td>
 <td>Наименование</td>
 <td>№ счетчика</td>
 <td>Вид счетчика</td>
 <td>кВт</td>
 <td>Коэффиц. транс. тока</td>
 <td>ИТОГО</td>
</tr>
<?php $i=0;
$sum = 0;
$sum_period = 0;
$period=-1;
$period_name = "";
$iter  = 0;?>
<?php foreach($akt->result() as $point): 
if ($period!=$point->period_id){
if ($iter ==1){ echo "<tr><td colspan = 7><b> Итого за {$period_name}</b></td><td><b>{$sum_period}</b></td><tr>";}
$period=$point->period_id;
$period_name=$point->period_name;
$iter = 1;
$sum_period = 0;
?>
<tr>
<td colspan = "8"><b><?php echo $period_name;?> </b></td>
</tr>
<?php
}?>

<tr> 
 <td><?php echo $i++; ?></td>
 <td><?php echo $point->bill_id;?></td>
 <td><?php echo $point->bill_name;?></td>
 <td><?php echo $point->gos_nomer;?></td>
 <td><?php echo $point->tariff_name;?></td>
 <td><?php echo $point->counter_diff;?></td>
 <td><?php echo $point->transform;?></td>
 <td><?php echo $point->itogo_kvt; 
 $sum+=$point->itogo_kvt;
 $sum_period+=$point->itogo_kvt;?></td>
</tr>
<?php endforeach;
echo "<tr><td colspan = 7><b> Итого за {$period_name}</b></td><td><b>{$sum_period}</b></td><tr>";?>
<tr>
<td colspan = "7"> <b>Итого за период:</b></td>
<td><b><?php echo $sum;?></b></td>
</tr>
</table>
<br>
<br>
<br>
<center>
<table>
<tr>
<td>Выдал (Должность, ФИО)</td>
<td> _________________________</td>
</tr>
<tr>
<td>Принял (Должность, ФИО)</td>
<td> _________________________</td>
</tr>
</table>
</center>
<div align=right>отдел Сбыта</div>

</html>