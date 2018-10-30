<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['0'].'.'.$d['1'].'.'.$d['2'];
}
?>
<html>
<head>
<title>Учет электроэнергии. Промышленный отдел</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>

<center>
Список потребителей, имеющих кредиторскую задолженность,
 за потребленную электроэнергию<br>
<?php echo $we->org_name;?> за <?php echo $period_name->current_period;?>
<br>по участку <?php echo $ture->name;?>  за <?php echo date('d.m.Y');?>
</center>
<br>
<br>
<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr>
<td>№ </td>
<td>№ Дог.</td>
<td>Наименование потребителя</td>
<td>Адрес</td>
<td>Послед. начисл.</td>
<td>Кредиторская задолженность(тенге)</td>
</tr>

<?php 
$group_id=-1;
$sum_dolg=0;
$j=1;
$sum_subgroup=0;
foreach($firms->result() as $firm):?>
<?php 
if ($group_id!=$firm->firm_subgroup_name){
	if ($group_id!=-1)
		echo "<tr><td colspan=5><b>Итого:</b></td><td align=right >".(-1*$sum_subgroup)."</td></tr>";
	echo "<tr><td colspan=6><b>{$firm->firm_subgroup_name}</b></td></tr>";
	$group_id=$firm->firm_subgroup_name;
	$sum_subgroup=0;
}
$sum_subgroup+= f_d($firm->dolg);
?>

<tr>

<td align=center><?php echo $j++;?></td>
<td align=center><?php echo $firm->dogovor;?></td>
<td align=left ><?php echo $firm->firm_name;?></td>
<td><?php echo $firm->address;?></td>
<td align=right><?php echo f_d($firm->nachisleno);?></td>
<td align=right><?php echo f_d(-1*$firm->dolg);?></td>
</tr>
<?php $sum_dolg+=$firm->dolg;endforeach; 
if ($group_id!=-1)
		echo "<tr><td colspan=5><b>Итого:</b></td><td align=right >".(-1*$sum_subgroup)."</td></tr>";
?>
<tr>
<td colspan=2 align=right><b>Итого:</b></td>
<td>&nbsp;</td>
<td colspan=3 align=right><b><?php echo f_d(-1*$sum_dolg);?></b></td>
</tr>
</table>

</html>