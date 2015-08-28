<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('#subtype').change(function()
		{
			window.location = window.location + '&subtype=' + jQuery(this).val();
		});
	});
</script>

<p>
	Select a layout for your <?php echo $type ?>:

</p>
<select name="subtype" id="subtype">
	<option value="">Select...</option>
	<?php foreach ($subtypes as $subtype): ?>
		<option value="<?php echo $subtype['id'] ?>"><?php echo $subtype['label'] ?></option>
	<?php endforeach ?>
</select>
