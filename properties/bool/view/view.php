<label class="super-property-label" for="<?php echo $inputName ?>"><?php echo $label ?></label>
<input
	class="super-input superpower-property-value"
	id="<?php echo $inputName ?>"
	data-id="<?php echo $id ?>"
	data-name="<?php echo $inputNameModel ?>"
	name="<?php echo $inputName ?>"
	type="checkbox"
	placeholder="<?php echo $placeholder ?>"
	<?php echo ($value)?'checked':''; ?>
	/>
