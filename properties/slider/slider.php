<?php namespace SuperPowers\Property;

class Slider extends SuperProperty {

	function load($groupId, $groupIndex, $definition, $postId)
	{
		parent::load($groupId, $groupIndex, $definition, $postId);
	}

	function view() {

		if(empty($this->value)){
			$this->value = 0;
		}

		$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $this->definition, $this->value);
		return $view;
	}


}