<?php namespace SuperPowers\Property\Bool;

use SuperPowers\Property\SuperProperty;


class Bool extends SuperProperty {

	function save($value = null)
	{
		if($value == 'on')
			$value = true;
		else
			$value = false;

		parent::save($value);
	}


}