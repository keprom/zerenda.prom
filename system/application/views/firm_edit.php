<h3>#

<?php
function select($var,$name,$value)
{
	$string="<select name={$name}_id>";
	foreach ($var->result() as $v) 
	{
		$string.="<option value={$v->id} ".($v->id==$value?" selected ":"")." >{$v->name}</option>";
	}
	$string.="</select><br>";
	echo $string;
}
$r=$r->row();
echo form_open("billing/firm_edition/".$r->id);

echo "Номер договора <input name=dogovor value='".$r->dogovor."'> <br>
  Наименование организации <input name=name value='".$r->name."' >"; ?></h3>
<br>
<p>
<?php
echo "<b>Адрес</b>: <input name=address value='".$r->address."' > <br>";
echo "<b>Телефон</b>: <input name=telefon value='".$r->telefon."' > <br><br>";
echo "<b>РНН</b>: <input name=rnn value= '".$r->rnn."' ><br><br>";
echo "<b>Дата договора</b>: <input name=dogovor_date value='".$r->dogovor_date."' ><br>";
echo "<b>Имя директора</b>: <input name=director_name value='".$r->director_name."' ><br>";
echo "<b>Количество учредителей: </b> <input name=person value='".$r->person."' ><br>";
echo "<b>Адрес директора</b>: <input name=director_address value='".$r->director_address."' ><br>";
echo "<b>БИН</b>: <input type='text' size = '18' maxlength = '12' name=bin value='".trim($r->bin)."'<br><br>";
echo "<b>Расчетный счет</b>: <input name=raschetnyy_schet value='".$r->raschetnyy_schet."' ><br>";
echo "<b>БИК</b>: <input name=bik value='".$r->bik."' ><br>";
echo "<select name=org_type ><option value=1  ";
if ($r->org_type==1) echo " selected ";
echo "> TOO </option><option value=2 ";
if ($r->org_type==2) echo " selected ";
echo " > ИП</option></select><br>";
echo "<br>Банк: ";
select ($bank,'bank',$r->bank_id);
echo "Отрасль: ";
select ($firm_otrasl,'otrasl',$r->otrasl_id);
echo "Группа: ";
select ($firm_subgroup,'subgroup',$r->subgroup_id);
echo "Группа по потреблению: ";
select ($firm_power_group,'firm_power_group',$r->firm_power_group_id);
echo "Турэ: ";
select ($ture,'ture',$r->ture_id);

echo "<br><br><br><br>";
echo "<input type=submit value='Изменить' >";
echo "</form>";


?>

</p>
<hr/>
