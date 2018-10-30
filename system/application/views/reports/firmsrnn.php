<html>
<head>
<title>Учет электроэнергии. Промышленный отдел</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>
<center>Перечень действующих организаций и предприятий</center>

<br>
<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr>
<td>№ П.П.</td>
<td># Дог.</td>
<td width="33%" >Наименование предприятия</td>
<td width="33%" >Адрес предприятия</td>
<td width="15%" >РНН</td>
</tr>
<?php $i=1;foreach($firms->result() as $firm):?>
<tr>
<td><?php echo $i++;?></td>
<td><?php echo $firm->dogovor; ?></td>
<td><?php echo $firm->firm_name; ?></td>
<td><?php echo $firm->firm_address; ?></td>
<td><?php echo $firm->rnn; ?></td>
</tr>
<?php endforeach;?>
</table>