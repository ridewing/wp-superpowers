<?php namespace SuperPowers\Property\Image;

use SuperPowers\Property\SuperProperty;

class Image extends SuperProperty {
	function getValue($args = null)
	{
		$default = array('size' => 'default', 'fit' => true, 'constrain' => false, 'raw' => false, 'color' => false);
		$args = wp_parse_args($args, $default);

		$value = parent::getValue($args);

		// Value has attachment
		if(!empty($value->id)) {

			$source = get_attached_file($value->id);

			if($args['raw']) {
				return wp_get_attachment_url($value->id);
			}

			if($args['color']) {
				return $this->image->getColor($source);
			}

			$base = wp_upload_dir();

			$extension = $this->image->getExtension($source);

			$path = "{$base['basedir']}/generated/{$this->postId}/{$this->id}/{$args['size']}.{$extension}";

			if(!file_exists($path)) {
				if(array_key_exists($args['size'], $this->definition['size'])){
					$size = $this->definition['size'][$args['size']];
				}
				else {
					$size = $args['size'];
				}

				$size = explode('x', $size);

				if($args['constrain']) {
					if($args['constrain'] == 'width') {
						$size[1] = null;
					} else if($args['constrain'] == 'height') {
						$size[0] = null;
					}
				}

				$this->image->create($source, $path, $size[0], $size[1], $args['fit']);
			}

			return "{$base['baseurl']}/generated/{$this->postId}/{$this->id}/{$args['size']}.{$extension}";
		}

		return $value;
	}


	function validate(Array $def)
	{
		if(!array_key_exists('size', $def)){
			throw new \ErrorException("Image property definition in type '{$this->app->controller->type}' is missing required property 'size'");
		}

		if(!array_key_exists('default', $def['size'])){
			throw new \ErrorException("Image property definition in type '{$this->app->controller->type}' is missing required default size");
		}

		return parent::validate($def);
	}

	function loadValue()
	{
		$value = get_post_meta($this->postId, $this->getIdentifier(), true);
		$this->setValue(json_decode($value));
	}

	function view()
	{
		$args = $this->definition;
		$args['default'] = $args['size']['default'];
		$args['attachment'] = null;

		unset($args['size']['default']);

		usort($args['size'], array(&$this, 'orderSizes'));

		if(!empty($this->value) && !empty($this->value->id)){
			$args['attachment'] = json_encode(wp_prepare_attachment_for_js($this->value->id));
		}

		$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $args, json_encode($this->value));
		return $view;
	}

	function orderSizes($a, $b)
	{
		$a = explode('x', $a);
		$b = explode('x', $b);

		if($a[1] > $b[1]){
			return -1;
		}

		if($a[1] == $b[1])
		{
			if($a[0] > $b[0]){
				return -1;
			}
		}

		return 1;
	}
}