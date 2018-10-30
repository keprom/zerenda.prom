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
function dottozpt($var){
	return str_replace(".",",",$var);
}
?>
<html>
<head>
<title>Учет электроэнергии. Промышленный отдел</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>

<center>
<h3>Свод потребления электроэнергии КТП за <?php echo $period->name;?></h3>
</center>
<?php $sum_itogo_kvt=0;?>
<table  width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: xx-small;">


<td>
КТП
</td>
<td>
Наименование фирмы
</td>
<td>
Наименование т.у.
</td>
<td>
КВТ
</td>
<tr>

<?php foreach($sql_result->result() as $data):?>
<td>
<?php echo $data->tp_name;?>
</td>
<td align=right>
<?php echo $data->firm_name;?>
</td>
<td align=right>
<?php echo $data->name;?>
</td>
<td align=center>
<?php echo $data->itog_kvt;?>
</td>
</tr>
<?php 
			
			$sum_itogo_kvt+=$data->itog_kvt;
			?>
<?php endforeach;?>
		
<tr><td colspan=4>&nbsp;</td></tr>
<tr>
<td colspan=3 align=right>Итого КВТ по КТП <b><?php echo $data->tp_name;?></b></td>
			
			<td align=center><font size=1><?php echo ($sum_itogo_kvt); ?></font></td>
</tr>
</table>



</html>