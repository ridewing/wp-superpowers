<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

/**
 * Class Repository
 *
 * @package SuperPowers\Library
 */
class Repository extends SuperObject {

	/**
	 * @param $table
	 * @param $query
	 *
	 * @return array|null|object
	 */
	function get($table, $query, $args = array()){
		global $wpdb;

		$table = "{$wpdb->prefix}{$table}";
		$query = str_replace('%t', $table, $query);

		$query = $wpdb->prepare($query, $args);

		$results = $wpdb->get_results($query);

		return $results;
	}

	/**
	 * @param $postArgs
	 * @param array $properties
	 * @param array $terms
	 *
	 * @return int|\WP_Error
	 */
	function createPost(Array $postArgs, Array $properties = array(), Array $terms = array()) {

		$postId = wp_insert_post( $postArgs );

		foreach($terms as $termsType => $termsArray) {
			wp_set_post_terms($postId, $termsArray, $termsType);
		}

		foreach($properties as $propertyKey => $propertyValue) {
			$id = explode('.', $propertyKey);
			$groupId = $id[0];
			$propertyId = $id[1];

			$this->property->setValue($postId, $groupId, $propertyId, $propertyValue);
		}

		return $postId;
	}

	function updatePost($postId, Array $postArgs, Array $properties = array(), Array $terms = array()) {

		$post = get_post($postId);

		foreach($postArgs as $key => $value) {
			$post->{$key} = $value;
		}

		wp_update_post($post);

		foreach($properties as $propertyKey => $propertyValue) {
			$id = explode('.', $propertyKey);
			$groupId = $id[0];
			$propertyId = $id[1];

			$this->property->setValue($postId, $groupId, $propertyId, $propertyValue);
		}

		foreach($terms as $termsType => $termsArray) {
			wp_set_post_terms($postId, $termsArray, $termsType);
		}

		return $postId;
	}

	/**
	 * @param $name
	 * @param $data
	 * @param $authorId
	 * @param $parentId
	 *
	 * @return int
	 */
	function createAttachment($name, $data, $authorId, $parentId) {
		$attachment = $this->image->uploadBase64Image($name, $data);

		// Prepare an array of post data for the attachment.
		$attachmentData = array(
			'guid' => $attachment['url'],
			'post_mime_type' => $attachment['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($attachment['file'])),
			'post_content' => '',
			'post_status' => 'inherit',
			'post_author' => $authorId
		);

		// Insert the attachment.
		$attachmentId = wp_insert_attachment($attachmentData, $attachment['file'], $parentId);

		return $attachmentId;
	}
}