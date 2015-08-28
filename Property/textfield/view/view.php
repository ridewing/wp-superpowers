<label class="super-property-label" for="<?php echo $inputName ?>"><?php echo $label ?></label>
<input class="super-input superpower-property-value" id="<?php echo $inputName ?>" data-id="<?php echo $id ?>" data-name="<?php echo $inputNameModel ?>" name="<?php echo $inputName ?>" type="text" placeholder="<?php echo $placeholder ?>" value="<?php echo $value ?>" />

<?php if(!empty($params['description'])): ?>
	<p><i><?php echo $params['description'] ?></i></p>
<?php endif ?>