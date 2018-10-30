<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var/1000);
}
function f_d2($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.0f",$var/1000);
}
?>
<html>
<head>
<title>Динамика увеличения задолжности</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>
<center>Динамика увеличения задолжности</center>
<center> за <?php echo $period->name;?> </center>
<?php 
$last_group=-1;

?>

<table  width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: xx-small;">
<tr align=center>
<td >
№ Дог.
</td>
<td width=200px>
Наименование потребителя
</td>
<td >
На начало отчетного периода
</td>
<td >
На конец периода
</td>
<td >
Увеличение
</td>
</tr>
<!-- Конец шапки -->
<?php $u_sum_debet=0;$u_sum_debet_end=0;$u_sum_inc=0?>

<?php $sum_debet=0;$sum_debet_end=0;$sum_inc=0?>
<?php foreach($sql_result->result() as $data):?>
<?php if ($last_group!=$data->subgroup_id):?>
	<?php if ($last_group!=-1): ?> 
		<tr align=right><td colspan=2 align=right><b>Итого по группе</b></td>
			<td><b>&nbsp;<?php echo f_d($sum_debet); ?></b></td>
			<td><b>&nbsp;<?php echo f_d($sum_debet_end); ?></b></td>
			<td><b>&nbsp;<?php echo f_d($sum_inc); ?></b></td>
			
			<?php 
			$u_sum_debet+=$sum_debet;
			$u_sum_debet_end+=$sum_debet_end;
			$u_sum_inc+=$sum_inc;
			?>
			<?php $sum_debet=0;$sum_debet_end=0;$sum_inc=0;?>
		</tr>
	<?php endif;?>
<tr><td colspan=9><b><?php echo $data->subgroup_name;?> </b></td></tr>
<?php endif;?>
<tr>
<td align=center>
<?php echo $data->dogovor;?>
</td>
<td>
<?php echo $data->firm_name;?>
</td>
<td align=right>&nbsp;
&nbsp;<?php echo f_d($data->debet_value-$data->kredit_value);?>
</td>
<td align=right>
 &nbsp;<?php echo f_d($data->debet_end);?>
</td>
<td align=right>
&nbsp;<?php echo f_d($data->debet_end-$data->debet_value/*+$data->kredit_value*/);?>
</td>

</tr>
<?php 
$last_group=$data->subgroup_id;
$sum_debet+=$data->debet_value-$data->kredit_value;
$sum_debet_end+=$data->debet_end;
$sum_inc+=$data->debet_end-$data->debet_value/*+$data->kredit_value*/;
?>
<?php endforeach;?>
<tr align=right><td colspan=2 align=right><b>Итого по группе</b></td>
			<td><b>&nbsp;<?php echo f_d($sum_debet); ?></b></td>
			<td><b>&nbsp;<?php echo f_d($sum_debet_end); ?></b></td>
			<td><b>&nbsp;<?php echo f_d($sum_inc); ?></b></td>
		</tr>
		<?php 
			$u_sum_debet+=$sum_debet;
			$u_sum_debet_end+=$sum_debet_end;
			$u_sum_inc+=$sum_inc;
			?>
<tr><td colspan=9>&nbsp;</td></tr>
<tr>
<td colspan=2 align=right><b>ИТОГО:</b></td>
<td align=right><font size=1><b><?php echo f_d($u_sum_debet); ?></b></font></td>
			<td align=right><font size=1>&nbsp;<b><?php echo f_d($u_sum_debet_end); ?></b></font></td>
			<td align=right><font size=1>&nbsp;<b><?php echo f_d2($u_sum_inc); ?></b></font></td>
</tr>

</table>
<br>
<br>
<center>
<table>
<tr>
<td>Директор</td><td width=150px></td><td> <?php echo trim($org_info->director);?></td>
</tr>
<tr>
<td>Зам. директора</td><td width=150px></td><td> <?php echo $org_info->zam_directora_po_sbytu?></td>
</tr>
<tr>
<td>Главный бухгалтер</td><td width=150px></td><td> <?php echo trim($org_info->glav_buh);?></td>
</tr>

</table>
</center>
</html>