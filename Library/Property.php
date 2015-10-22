<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;
/**
 * Class Property
 * Class to fetch property values
 * @package SuperPowers
 */
class Property extends SuperObject {

	function getIdentifier($groupId, $propertyId, $index = 0) {
		return "superpowers.{$groupId}.{$propertyId}.{$index}";
	}

	/**
	 * Get value for property from current post
	 *
	 * @param string $groupId
	 * @param string $propertyId
	 * @param null|array $args
	 * @param int $index
	 * @return mixed
	 */
	function value($groupId, $propertyId, $args = null, $index = 0) {
		global $post;
		return $this->getValue($post->ID, $groupId, $propertyId, $args, $index);
	}

	/**
	 * Get value for property from any post
	 *
	 * @param int|string $postId
	 * @param string $groupId
	 * @param string $propertyId
	 * @param null|array $args
	 * @param int $index
	 * @return mixed
	 */
	function getValue($postId, $groupId, $propertyId, $args = null, $index = 0) {

		/** @var SuperProperty $property */
		$property = $this->load->property($postId, $groupId, $propertyId, $index);

		if(!$property) return null;

		return $property->getValue($args);
	}

	function setValue($postId, $groupId, $propertyId, $value, $index = 0) {
		/** @var \SuperPowers\Property\SuperProperty $property */
		$property = $this->load->property($postId, $groupId, $propertyId, $index);

		if(!$property) return null;

		$property->save($value);
	}

	/**
	 * Get value from all properties in group
	 *
	 * @param int|string $postId
	 * @param string $groupId
	 * @param null|array $args
	 * @return array
	 */
	function getGroupValues($postId, $groupId, $args = null) {

		// Get type for this post
		$typeId     = $this->post->getType($postId);
		$subtypeId  = $this->post->getSubtype($postId);

		// Get group definition for post
		$groupDefinition = $this->definition->group($typeId, $subtypeId, $groupId);

		$count = 0;

		if ($groupDefinition['repeatable']) {
			$count = $this->group->getGroupRepeatForPost($postId, $groupId);
		}

		$values = array();

		for($index = 0; $index < $count; $index++) {
			foreach($groupDefinition['properties'] as $propertyDef) {

				$propArgs = null;

				if(is_array($args)){
					if(array_key_exists($propertyDef['id'], $args)) {
						$propArgs = $args[$propertyDef['id']];
					}
				}

				$value = $this->getValue($postId, $groupId, $propertyDef['id'], $propArgs, $index);

				if($groupDefinition['repeatable']) {
					if(!array_key_exists($index, $values)) {
						$values[$index] = array();
					}

					$values[$index][$propertyDef['id']] = $value;
				} else {
					$values[$propertyDef['id']] = $value;
				}
			}
		}

		return $values;
	}

	/**
	 * Get value from all properties in group at index
	 *
	 * @param int|string $postId
	 * @param string $groupId
	 * @param int $index
	 * @param null|array $args
	 * @return array
	 */
	function getGroupValuesAtIndex($postId, $groupId, $index = 0, $args = null) {

		$typeId = $this->post->getType($postId);
		$subtypeId = $this->post->getSubtype($postId);

		$groupDefinition = $this->definition->group($typeId, $subtypeId, $groupId);

		$values = array();

		foreach($groupDefinition['properties'] as $propertyDef){

			$propArgs = null;

			if(is_array($args)){
				if(array_key_exists($propertyDef['id'], $args)) {
					$propArgs = $args[$propertyDef['id']];
				}
			}

			$value = $this->getValue($postId, $groupId, $propertyDef['id'], $propArgs, $index);
			$values[$propertyDef['id']] = $value;
		}

		return $values;
	}
}