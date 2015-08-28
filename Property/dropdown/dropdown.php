<?php namespace SuperPowers\Property\Dropdown;

use SuperPowers\Property\SuperProperty;

class Dropdown extends SuperProperty {

	/** @var  \SuperPowers\Datasource\SuperDatasource */
	protected $data;

	function load($groupId, $groupIndex, $definition, $postId)
	{
		parent::load($groupId, $groupIndex, $definition, $postId);

		$this->loadDatasource($this->definition['datasource']);
	}

	function loadDatasource($type){
		$this->data = $this->load->datasource($type);
	}

	function view() {

		if($this->data->isGrouped()){
			$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $this->definition, $this->value, array('values' => $this->data->get($this->definition)), 'grouped');
		} else {
			$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $this->definition, $this->value, array('values' => $this->data->get($this->definition)));
		}

		return $view;
	}


}