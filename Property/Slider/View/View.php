<label class='slider-label'><?php echo $label ?> <span><?php echo $value ?></span></label>
<div class="property-slider" id='slider-<?php echo $id ?>-<?php echo $index ?>'>
	<input class="superpower-property-value" id="<?php echo $inputName ?>" data-id="<?php echo $id ?>" data-name="<?php echo $inputNameModel ?>" name="<?php echo $inputName ?>" type="hidden" value="<?php echo $value ?>" />
</div>
<script>
	$(document).ready(function(){
		SuperPowers.property.slider($("#slider-<?php echo $id ?>-<?php echo $index ?>"), <?php echo $value ?>)
	});
</script>