<label><?php echo $label ?></label>

<div class="superpower-images-holder" id="image-<?php echo $id ?>-<?php echo $index ?>"  >
	<input class="superpower-property-value" id="<?php echo $inputName ?>" data-id="<?php echo $id ?>" data-name="<?php echo $inputNameModel ?>" name="<?php echo $inputName ?>" type="hidden" value="<?php echo $value ?>" />
	<input class="superpower-property-attachment" type="hidden" value='<?php echo $attachment ?>'>

	<div class="superpower-image superpower-image-size-default" data-size="<?php echo $default ?>" >
		<p class="drop-hint">Drop image here <span>or browse</span></p>
		<img class="image-view" src=""/>
	</div>

	<div class="image-remove">
		<a href="#" class="image-remove-button">Remove image</a>
	</div>

	<div class="additional-images">
		<h3>Additional formats</h3>
	<?php foreach($size as $imageId => $size): ?>

		<div class="superpower-image superpower-image-size-<?php echo $imageId ?>" data-size="<?php echo $size ?>" >
			<p class="drop-hint">Drop image here <span>or browse</span></p>
			<img class="image-view" src=""/>
		</div>

	<?php endforeach; ?>
	</div>
</div>

<script>
	$(document).ready(function(){
		SuperPowers.property.image($('#image-<?php echo $id ?>-<?php echo $index ?>'))
	});
</script>