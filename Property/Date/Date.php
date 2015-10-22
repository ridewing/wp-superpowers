<?php namespace SuperPowers\Property\Date;

use SuperPowers\Property\SuperProperty;

class Date extends SuperProperty {

	function getValue($args = null)
	{
		$value = parent::getValue($args);

		if(!empty($value)){
			$date = $value['date'];

			if(!empty($value['time']))
				$date .= " {$value['time']}";

			$timezone = new \DateTimeZone("Europe/Stockholm");
			$value = new \DateTime($date, $timezone);
		}

		return $value;
	}

	function view()
	{
		if(empty($this->value))
		{
			$this->value = array('date' => null, 'time' => null);
		}

		if(!array_key_exists('time', $this->value)){

			$this->value['time'] = null;
		}

		$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $this->definition, $this->value);
		return $view;
	}
}