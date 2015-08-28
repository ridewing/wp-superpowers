<label for="<?php echo $inputName ?>"><?php echo $label ?></label>
<select class="super-input superpower-property-value" id="<?php echo $inputName ?>" data-name="<?php echo $inputNameModel ?>" data-id="<?php echo $id ?>" name="<?php echo $inputName ?>">
	<option value="-1">Select</option>
	<?php foreach($args['values'] as $groupName => $values): ?>
		<optgroup label="<?php echo $groupName ?>">
			<?php foreach($values as $key => $option): ?>
			<option <?php echo ($key == $value)?'selected':'' ?> value="<?php echo $key ?>"><?php echo $option ?></option>
			<?php endforeach; ?>
		</optgroup>
	<?php endforeach; ?>
</select>