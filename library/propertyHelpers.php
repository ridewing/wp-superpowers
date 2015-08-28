<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class PropertyHelpers extends SuperObject {

	public $PROPERTY_PREFIX = 'superpowers';

	function getPropertyPrefix() {
		return $this->PROPERTY_PREFIX;
	}

	function getOnlySuperPropertiesFromArray($array) {

		if(array_key_exists('superpowers', $array)){
			return $array['superpowers'];
		}

		return array();
	}

	function getPropertyInGroup($groupId, $propertyId, $definition) {

		if(array_key_exists($groupId, $definition['groups'])){
			foreach($definition['groups'][$groupId]['properties'] as $property){
				if($property['id'] == $propertyId) {
					return $property;
				}
			}
		}
	}

	function getView($propertyId, $groupId, $index, $propertyDefinition, $value = null, $args = null, $viewName = 'view')
	{
		$applicationPropertyView = SUPERPOWERS_APPLICATION_DIR . '/properties/' . $propertyDefinition['type'] .  "/view/{$viewName}.php";
		$pluginPropertyView = SUPERPOWERS_DIR . '/properties/' . $propertyDefinition['type'] . "/view/{$viewName}.php";

		$view = null;

		if (file_exists($applicationPropertyView)) {
			$view = $applicationPropertyView;

		} else if (file_exists($pluginPropertyView)) {
			$view = $pluginPropertyView;
		}

		if (empty($propertyDefinition['id'])) {

			throw new \ErrorException("Property is missing required field \"id\"");
		}

		if ($view) {
			$propertyDefinition['inputName'] = "{$this->getPropertyPrefix()}[$groupId][{$index}][{$propertyId}]";
			$propertyDefinition['inputNameModel'] = "{$this->getPropertyPrefix()}[$groupId][%index%][{$propertyId}]";
			$propertyDefinition['id'] = $propertyId;
			$propertyDefinition['value'] = $value;
			$propertyDefinition['args'] = $args;
			$propertyDefinition['index'] = $index;

			return $this->html->render($view, $propertyDefinition);
		}

		throw new \ErrorException("Could not find view for property: {$propertyDefinition['type']}");
	}
}