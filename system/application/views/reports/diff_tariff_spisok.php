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
<b>Ведомость<br> учета полученной товарной продукции ( электроэнергии )<br> по <?php echo $org->org_name; ?> <br> за 
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
 <td rowspan=3>Начислено НДС тенге
 </td>
 <td rowspan=3>Всего с НДС тенге
 </td>
 </tr>
 <tr align=center>
 <td colspan=2>
  По тарифу дневной
 </td>
 <td colspan=2>
  По тарифу ночной
 </td>
 <td colspan=2>
  Итого
 </td>
 </tr>
 <tr align=center>
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
$sum_day_kvt=0;$sum_day_tenge=0;$sum_night_kvt=0;$sum_night_tenge=0;$sum_nds=0;$sum_with_nds=0;
$_sum_day_kvt=0;$_sum_day_tenge=0;$_sum_night_kvt=0;$_sum_night_tenge=0;$_sum_nds=0;$_sum_with_nds=0;

$j=1;
$last_group=-1;$last_group_name='';
foreach($diff->result() as $d ):
if ($last_group==-1)
{
	echo "<tr><td colspan=10> <b>{$d->firm_subgroup_name}</b> </td></tr>";
	$last_group=$d->firm_subgroup_id;
	$last_group_name=$d->firm_subgroup_name;
}
if ($last_group<>$d->firm_subgroup_id):
?>
	  <tr>
  <td>&nbsp;
  </td>
 <th align=right>
  <b>Итого по <?php echo $last_group_name; ?></b>
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
  <b><?php echo f_d($sum_night_kvt+$sum_day_kvt);?></b>
 </th>
  <th align=right>
  <b><?php echo f_d($sum_night_tenge+$sum_day_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_nds);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_with_nds);?></b>
 </th>
</tr>
<?php	
$sum_day_kvt=0;$sum_day_tenge=0;$sum_night_kvt=0;$sum_night_tenge=0;$sum_nds=0;$sum_with_nds=0;
	echo "<tr><td colspan=10> <b>{$d->firm_subgroup_name}</b> </td></tr>";
	$last_group=$d->firm_subgroup_id;
	$last_group_name=$d->firm_subgroup_name;
endif;

$sum_day_kvt+=$d->itogo_kvt_day;
$sum_day_tenge+=$d->tenge_day;
$sum_night_kvt+=$d->itogo_kvt_night;
$sum_night_tenge+=$d->tenge_night;
$sum_nds+=($d->tenge_night+$d->tenge_day)*$d->nds/100;
$sum_with_nds+=($d->tenge_night+$d->tenge_day)*($d->nds+100)/100;

$_sum_day_kvt+=$d->itogo_kvt_day;
$_sum_day_tenge+=$d->tenge_day;
$_sum_night_kvt+=$d->itogo_kvt_night;
$_sum_night_tenge+=$d->tenge_night;
$_sum_nds+=($d->tenge_night+$d->tenge_day)*$d->nds/100;
$_sum_with_nds+=($d->tenge_night+$d->tenge_day)*($d->nds+100)/100;

?>
<tr align=right>
<td>
	<?php echo $j++;?>
</td> 
<td align=left>
	<?php echo $d->firm_name;?>
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
	<?php echo f_d($d->itogo_kvt_night+$d->itogo_kvt_day);?>
</td> 
<td>
	<?php echo f_d($d->tenge_night+$d->tenge_day);?>
</td> 
<td>
	<?php echo f_d(($d->tenge_night+$d->tenge_day)*$d->nds/100);?>
</td> 
<td>
	<?php echo f_d(($d->tenge_night+$d->tenge_day)*($d->nds+100)/100);?>
</td> 

</tr> 
 <?php endforeach;?>
 
 <tr>
  <td>&nbsp;
  </td>
 <th align=right>
  <b>Итого по <?php echo $last_group_name; ?></b>
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
  <b><?php echo f_d($sum_night_kvt+$sum_day_kvt);?></b>
 </th>
  <th align=right>
  <b><?php echo f_d($sum_night_tenge+$sum_day_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_nds);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($sum_with_nds);?></b>
 </th>
</tr>

  <tr>
  <td>&nbsp;
  </td>
 <th align=right>
  <b>Итого</b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_day_kvt);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_day_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_night_kvt);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_night_tenge);?></b>
 </th>
  <th align=right>
  <b><?php echo f_d($_sum_night_kvt+$_sum_day_kvt);?></b>
 </th>
  <th align=right>
  <b><?php echo f_d($_sum_night_tenge+$_sum_day_tenge);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_nds);?></b>
 </th>
 <th align=right>
  <b><?php echo f_d($_sum_with_nds);?></b>
 </th>
 
 </tr>
 </table><br>
 <table width=100% style="border: black;font-family: Verdana; font-size:  small;">
 <tr align=center>
<td > Директор </td>
<td> <?php echo $org->director;?></td>
</tr><tr></tr><tr></tr>
 <tr align=center>
<td > Зам. Директора по сбыту</td>
<td> <?php echo $org->zam_directora_po_sbytu;?></td>
</tr>

</table> </center>
 </body>
</html>