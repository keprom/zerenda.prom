<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	{
		if  ($var == null ) return "&nbsp;"; else
			return sprintf("%22.2f",$var);
	}
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['0'].'.'.$d['1'].'.'.$d['2'];
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>

<center>
<b>Анализ<br> учета полученной товарной продукции ( электроэнергии )<br> по 
<?php echo $org->org_name; ?> <br> с применением дифференцированных тарифов за 
 <?php echo $period->name;?></b>
</center>
<br>
<br>

<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
 <tr align=center>
 <td rowspan=3>№
 </td>
 <td rowspan=3>Наименование потребителя
 </td>
 <td colspan=6>Получено товарной продукции
 </td>
 <td rowspan=3 width=80px>Всего кВт/ч
 </td>
 <td rowspan=3 width=100px>Всего тенге (без НДС)
 </td>
 <td rowspan=3 width=100px>Всего тенге (с НДС)
 </td>
 <td rowspan=3 width=110px>По ср. отпускному тарифу (с НДС 20.65)
 </td>
 <td rowspan=3 width=110px>Разница (+,-)
 </td>
 </tr>
 <tr>
 <td colspan=2>
  По тарифу дневной (18.44)
 </td>
 <td colspan=2>
  По тарифу ночной (7.32)
 </td>
 <td colspan=2>
  По тарифу вечерний (42.48)
 </td>
 </tr>
 <tr>
 <td >
  кВт/ч
 </td>
 <td >
  тенге
 </td>
 <td >
  кВт/ч
 </td>
 <td >
  тенге
 </td>
 <td >
  кВт/ч
 </td>
 <td >
  тенге
 </td>
  
 
 
 </tr>
<?php 
$sum_day_kvt=0;
$sum_day_tenge=0;
$sum_night_kvt=0;
$sum_night_tenge=0;
$sum_evening_kvt=0;
$sum_evening_tenge=0;
$sum_bez_nds=0;
$sum_with_nds=0;
$vsego_kvt=0;
$po_otp_tarif=0;
$buf_sum_with_nds=0;
$buf_sum_otp_nds=0;
$buf_vsego_kvt=0;

$j=1;
foreach($diff->result() as $d ):

$sum_day_kvt+=$d->itogo_kvt_day;
$sum_day_tenge+=$d->tenge_day;
$sum_night_kvt+=$d->itogo_kvt_night;
$sum_night_tenge+=$d->tenge_night;
$sum_evening_kvt+=$d->itogo_kvt_evening;
$sum_evening_tenge+=$d->tenge_evening;
$buf_sum_with_nds=0;
$buf_sum_otp_nds=0;
$buf_vsego_kvt=0;

$sum_bez_nds+=($d->tenge_night+$d->tenge_day+$d->tenge_evening);
$sum_with_nds+=(($d->tenge_night+$d->tenge_day+$d->tenge_evening)*(100+$d->nds)/100);
$vsego_kvt+=($d->itogo_kvt_day+$d->itogo_kvt_night+$d->itogo_kvt_evening);
$buf_vsego_kvt+=($d->itogo_kvt_day+$d->itogo_kvt_night+$d->itogo_kvt_evening);
$buf_sum_with_nds+=(($d->tenge_night+$d->tenge_day+$d->tenge_evening)*(($d->nds+100)/100));
$buf_sum_otp_nds=($buf_vsego_kvt)*20.65;

?>
<tr>
<td>
	<?php echo $j++;?>
</td> 
<td>
	<?php echo $d->firm_subgroup_name;?>
</td> 
<td>
	<?php echo f_d($d->itogo_kvt_day);?>
</td> 
<td>
	<?php echo f_d($d->tenge_day);?>
</td> 
<td>
	<?php echo f_d($d->itogo_kvt_night);?>
</td> 
<td>
	<?php echo f_d($d->tenge_night);?>
</td> 
<td>
	<?php echo f_d($d->itogo_kvt_evening);?>
</td> 
<td>
	<?php echo f_d($d->tenge_evening);?>
</td> 

<td>
	<?php echo f_d($buf_vsego_kvt);?>
</td> 
<td>
	<?php echo f_d($d->tenge_night+$d->tenge_day+$d->tenge_evening);?>
</td> 
<td>
	<?php echo f_d($buf_sum_with_nds);?>
</td> 
<td>
	<?php echo f_d($buf_sum_otp_nds);?>
</td> 
<td>
	<?php echo f_d($buf_sum_with_nds-$buf_sum_otp_nds);?>
</td> 

</tr> 
 <?php endforeach;?>
  <tr>
  <td>&nbsp;
  </td>
 <th align=right>
  <b>Итого</b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_day_kvt);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_day_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_night_kvt);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_night_tenge);?></b>
 </th>
  <th align=right>
  <b><?php echo f_d($sum_evening_kvt);?></b>
 </th>
 
  <th align=right>
  <b><?php echo f_d($sum_evening_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_night_kvt+$sum_day_kvt+$sum_evening_kvt);?></b>
 </th>
    <th align=right>
  <b><?php echo f_d($sum_night_tenge+$sum_day_tenge+$sum_evening_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_with_nds);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($vsego_kvt*20.65);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_with_nds-($vsego_kvt*20.65));?></b>
 </th>
 
 </tr>
 </table>
 <br>
 <table width=100% style="border: black;font-family: Verdana; font-size:  small;">
 <tr align=center>
<td > Директор филиала ТОО КЭЦ Зеренда-Энерго </td>
<td> <?php echo $org->director;?></td>
</tr><tr></tr><tr></tr>
 <tr align=center>
<td > Зам. Директора по сбыту</td>
<td> <?php echo $org->zam_directora_po_sbytu;?></td>
</tr>
 </body>
</html>