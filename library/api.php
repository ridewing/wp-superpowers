<?php namespace SuperPowers;

/**
 * Class Api
 * Used to handle api calls and response
 * @package SuperPowers
 */
class Api extends SuperObject {

	/**
	 * @param array|string $data
	 * @param bool $success
	 * @return array
	 */
	function response($data, $success = true) {

		if(empty($data))
		{
			$success = false;
		}

		if(!is_array($data)){
			$data = array('data' => $data);
		}

		$data['success'] = $success;

		return $data;
	}
}