<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('.subtype').on('click', function(e)
		{
			e.preventDefault();
			window.location = window.location + '&subtype=' + jQuery(this).data('value');
		});
	});
</script>

<p>
	Select a layout for your <?php echo $type ?>:

</p>

<div class="super-subtypes">
	<?php foreach (array_chunk($subtypes, 3) as $subtypeChunk): ?>
		<div class="row">
			<?php foreach ($subtypeChunk as $subtype): ?>
				<div class="subtype" data-value="<?php echo $subtype['id'] ?>">
					<a href >
						<img src="http://placehold.it/75">
						<p><?php echo $subtype['label'] ?></p>
					</a>
				</div>
			<?php endforeach ?>
		</div>
	<?php endforeach ?>
</div>
