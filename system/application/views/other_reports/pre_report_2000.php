<?php echo form_open("billing/report_2000"); ?>
<select name="period_year" id="">
    <?php foreach ($period_years as $p): ?>
        <option value="<?php echo $p->period_year; ?>"><?php echo $p->period_year; ?></option>
    <?php endforeach; ?>
</select>
<input type="submit" value="Открыть">
<?php echo form_close(); ?>
