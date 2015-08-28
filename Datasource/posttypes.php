<?php namespace SuperPowers\Datasource;

class Posttypes extends SuperDatasource {

	function get($args = null) {
		if(empty($this->data)) {

			$types = $this->config->get('types');

			foreach($types as $type) {
				$this->data[$type['id']] = $type['label'];
			}
		}

		return $this->data;
	}
}