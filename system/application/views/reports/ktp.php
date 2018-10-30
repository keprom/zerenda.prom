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
<title>Список ктп и к какой фирме относятся</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>
<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr align=center>
<td>№</td>
<td>Номер договора</td>
<td >Наименование фирмы</td>
<td>Название ТП</td>
<td>Название Точки Учета</td>

</tr>
<?php $i=1;?>
<?php foreach($firm->result() as $a): ?>
<tr align=left>
<td><?php echo $i++;?></td>
<td > <?php echo $a->firm_dog;?></td>
<td> <?php echo $a->firm_name;?></td>
<td> <?php echo $a->tp_name;?></td>
<td> <?php echo $a->name;?></td>

</tr>
<?php endforeach;?>
</table>
</html>