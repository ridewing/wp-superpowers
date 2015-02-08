<?php namespace SuperPowers;

class Create extends SuperObject {

	/**
	 * @param string $id
	 * @param string $type
	 * @param array $def
	 * @return array
	 */
	function property($id, $type, Array $def) {

		$def['id'] = $id;
		$def['type'] = $type;

		return wp_parse_args($def, $this->propertyDefaults());
	}

	/**
	 * @param string $id
	 * @param string $name
	 * @param array $def
	 * @return array
	 */
	function group($id, $name, Array $def) {
		$def['id'] = $id;
		$def['name'] = $name;

		return wp_parse_args($def, $this->groupDefaults());
	}

	/**
	 * @return array
	 */
	function propertyDefaults(){
		return array(
			"id"            => null,
			"type"          => 'textarea',
			"label"         => '',
			"placeholder"   => ''
		);
	}

	/**
	 * @return array
	 */
	function groupDefaults(){
		return array(
			"id"            => null,
			"name"          => "Super Group",
			"repeatable"    => false
		);
	}


}