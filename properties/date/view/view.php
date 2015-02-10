<label class="super-property-label" for="<?php echo $inputName ?>"><?php echo $label ?></label>
<input
	class="super-input superpower-property-value super-input-date"
	id="<?php echo $inputName ?>"
	data-id="<?php echo $id ?>"
	data-name="<?php echo $inputNameModel ?>"
	name="<?php echo $inputName ?>[date]"
	type="date"
	placeholder="<?php echo $placeholder ?>"
	value="<?php echo $value['date'] ?>"
	/>

<?php if(!empty($time)): ?>
	<input
		class="super-input superpower-property-value super-input-time"
		id="<?php echo $inputName ?>"
		data-id="<?php echo $id ?>"
		data-name="<?php echo $inputNameModel ?>"
		name="<?php echo $inputName ?>[time]"
		type="time"
		placeholder="<?php echo $placeholder ?>"
		value="<?php echo $value['time'] ?>"
		/>
<?php endif ?>