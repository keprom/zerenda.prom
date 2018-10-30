<?php echo anchor('billing/perehod',"Перейти в след месяц");?><br><br><br><br>
<?php echo anchor('billing/dbase',"Импортировать оплату");?><br><br><br><br>
<?php echo anchor('billing/tariff_list',"Смена/добавление тарифов");?><br><br><br><br>
<?php echo anchor('billing/pre_nachislenie_v_analiz',"Экспортировать начисление в анализ по тп");?><br><br><br><br>
<?php echo anchor('billing/nachislenie_v_buhgalteriu',"Перенос начисления в бухгалтерию");?><br><br><br><br>
<?php echo anchor('billing/perenos_rek1',"Перенос реквизитов");?><br><br><br><br>
<?php echo anchor('billing/perenos_nach',"Перенос начисления по уровням");?>

<?php #added; ?>
<?php echo form_open('billing/nach_by_period', "method='post'"); ?>
<br><br><br>Начисления за период 
<select name="period_id" id="period_id">
    <?php foreach ($periods as $period) : ?>
        <option value="<?php echo $period->id; ?>"><?php echo $period->name; ?></option>
    <?php endforeach; ?>
</select>
<input type="submit" value="Выгрузить">
<?php echo form_close(); ?>
<?php #end of added; ?>