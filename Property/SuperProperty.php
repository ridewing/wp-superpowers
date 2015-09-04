<?php namespace SuperPowers\Property;

use SuperPowers\Core\SuperObject;

abstract class SuperProperty extends SuperObject {

	public $id;
	public $type;

	public $value;
	protected $postId;
	protected $definition;
	protected $index;
	protected $groupId;

	private $hasValue = false;

	/**
	 * Validate property definition
	 * Used when loading a property
	 * @param array $def
	 * @return bool
	 */
	function validate(Array $def) {
		return true;
	}

	/**
	 * Setup the property
	 * @param string $groupId
	 * @param int $groupIndex
	 * @param array $definition
	 * @param int|string $postId
	 */
	function load($groupId, $groupIndex, $definition, $postId) {

		$this->postId       = $postId;
		$this->type         = $definition['type'];
		$this->id           = $definition['id'];
		$this->groupId      = $groupId;
		$this->definition   = $definition;
		$this->index        = $groupIndex;
	}

	/**
	 * Get value from db and set as current value
	 */
	function loadValue() {
		$value = $this->post->getMeta($this->postId, $this->getIdentifier());
		$this->setValue($value);
	}

	/**
	 * @param mixed $value
	 */
	function setValue($value) {
		$this->hasValue = true;
		$this->value = $value;
	}

	/**
	 * @param null|mixed $args
	 * @return mixed
	 */
	function getValue($args = null) {
		if(!$this->hasValue){
			$this->loadValue();
		}
		return $this->value;
	}

	/**
	 * Delete property value from db
	 */
	function delete() {
		if(!empty($this->postId)){
			delete_post_meta($this->postId, $this->getIdentifier());
		}
	}

	/**
	 * Store value to db
	 * If no value is provided the current value is stored
	 * @param mixed|null $value
	 */
	function save($value = null) {

		if(!empty($this->postId)){

			if($value === null)
				$value = $this->value;

			$metaId = $this->getIdentifier();
			$saved = update_post_meta($this->postId, $metaId, $value);
			return $saved;
		}
	}

	/**
	 * Get property identifier
	 * Used in db for identification etc
	 * @return string
	 */
	function getIdentifier() {
		return $this->property->getIdentifier($this->groupId, $this->id, $this->index);
		//return "superpowers.{$this->groupId}.{$this->id}.{$this->index}";
	}

	/**
	 * @return string
	 * @throws \ErrorException
	 */
	function view() {
		$view = $this->propertyHelpers->getView($this->id, $this->groupId, $this->index, $this->definition, $this->value);
		return $view;
	}
}