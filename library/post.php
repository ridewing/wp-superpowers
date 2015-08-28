<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Post extends SuperObject {

	/**
	 * @param int|string $postId
	 * @return false|string
	 */
	function getType($postId) {
		return get_post_type($postId);
	}

	/**
	 * @param int|string $postId
	 * @return mixed
	 */
	function getSubtype($postId) {
		return $this->getMeta($postId, "superpowersSubtype");
	}

	/**
	 * @param int|string $postId
	 * @param string $subtype
	 * @return bool|int
	 */
	function setSubtype($postId, $subtype) {
		return update_post_meta($postId, "superpowersSubtype", $subtype);
	}

	/**
	 * @param int|string $postId
	 * @param string $metaId
	 * @return mixed
	 */
	function getMeta($postId, $metaId) {
		return get_post_meta($postId, $metaId, true);
	}

	/**
	 * @param int|string $postId
	 * @param null|int $limit
	 * @return array|mixed|string|void
	 */
	function content($postId, $limit = null){

		$post = get_post($postId);

		if($limit){

			$content = $this->limitContent($post->post_content, $limit);

			// Wordpress content stuff
			$content = preg_replace('/\[.+\]/','', $content);
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			return $content;
		}

		return apply_filters('the_content', $post->post_content);
	}

	function limitContent($content, $limit = 80, $start = 0){
		if (strlen($content) > $limit) {
			// Limit string length
			$content = substr($content, $start, $limit);

			// Split to words
			$content = explode(' ', $content);

			// Remove last word (or part of word)
			array_pop($content);


			if($start > 0) {
				array_shift($content);
				array_unshift($content, "...");
			}


			// Put string together again
			$content = implode(' ', $content) . '...';
		}

		return $content;
	}

	function date($postId){
		$post = get_post($postId);

		return $this->formatDate($post->post_date);
	}

	function formatDate($dateString){
		$date = new \DateTime($dateString);

		$day = $date->format('j');
		$month = $this->translateMonth($date->format('n'));
		$time = $date->format('H:i');

		return "{$day} {$month}, kl {$time}";
	}

	function translateMonth($month){

		switch($month){
			case 1:
				return 'januari';
			case 2:
				return 'februari';
			case 3:
				return 'mars';
			case 4:
				return 'april';
			case 5:
				return 'maj';
			case 6:
				return 'juni';
			case 7:
				return 'juli';
			case 8:
				return 'augusti';
			case 9:
				return 'september';
			case 10:
				return 'oktober';
			case 11:
				return 'november';
			case 12:
				return 'december';
		}
	}

	function getLabel($postId){
		$post = get_post($postId);
		$type = $this->config->get("types.{$post->post_type}");
		if(!empty($type['label_swe'])) {
			return $type['label_swe'];
		}

		return $type['label'];
	}

}