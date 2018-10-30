<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.0f",$var);
}

?>
<html>
<head>
<title>24 Энергетика</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<table>
<?php $sum=0;
foreach($energo->result() as $e ):
$sum+=$e->sum;
?>
<tr>
<td>
<?php echo $e->firm_otrasl_name;?>
</td>
<td align="right">
<?php echo f_d($e->sum);?>
</td>
</tr>
<?php endforeach;?>
<tr>
<td>Итого:</td>
<td align=right><?php echo "<b>$sum</b>"; ?></td>
</tr>
</table>
</body>
</html>
