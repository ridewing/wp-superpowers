<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Definition extends SuperObject {

	function type($typeId, $subtypeId = null){
		$typeId = strtolower($typeId);

		/*$typeDefinition = $this->config->get("types.{$typeId}");

		if(!empty($subtypeId) && array_key_exists('subtypes', $typeDefinition)) {
			if(array_key_exists($subtypeId, $typeDefinition['subtypes'])){
				$typeDefinition = $typeDefinition['subtypes'][$subtypeId];
			}
		}*/

		if($subtypeId != null) {
			$typeId .= ".{$subtypeId}";
		}

		/** @var SuperTypeController $controller */
		$controller = $this->load->controller($typeId);
		return $controller->getDefinition();
	}

	function typeHasSubtype($typeId){
		$typeDefinition = $this->config->get("types.{$typeId}");
		return array_key_exists('subtypes', $typeDefinition);
	}

	function group($typeId, $subtypeId, $groupId){

		$typeDefinition = $this->type($typeId, $subtypeId);

		if(array_key_exists('groups', $typeDefinition) && array_key_exists($groupId, $typeDefinition['groups'])) {
			return $typeDefinition['groups'][$groupId];
		}
	}

	function property($typeId, $subtypeId, $groupId, $propertyId){

		$groupDefinition = $this->group($typeId, $subtypeId, $groupId);

		if(empty($groupDefinition)) return false;

		if(array_key_exists('properties', $groupDefinition)){
			foreach($groupDefinition['properties'] as $propertyDef){
				if($propertyDef['id'] == $propertyId){
					return $propertyDef;
				}
			}
		}
	}

}