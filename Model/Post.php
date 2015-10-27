<?php namespace SuperPowers\Model;

/**
 * @property string title
 * @property string content
 * @property string contentShort
 * @property \WP_User author
 * @property array categories
 * @property string date
 */
class Post extends SuperModel {

	/**
	 * @return string
	 */
	function getTitle(){
		return $this->object->post_title;
	}

	/**
	 * @return string
	 */
	function getContent(){
		return $this->post->content($this->ID);
	}

	/**
	 * @return string
	 */
	function getContentShort(){
		return $this->post->content($this->ID, $this->config->get('settings.contentshort', 200));
	}

	/**
	 * @return \WP_User
	 */
	function getAuthor(){
		return get_user_by('id', $this->object->post_author);
	}

	/**
	 * @return array
	 */
	function getCategories(){
		$postCategories = wp_get_post_categories( $this->ID );
		$cats = array();

		foreach($postCategories as $c){
			$cat = get_category( $c );
			$cats[] = array( 'name' => $cat->name, 'slug' => $cat->slug );
		}

		return $cats;
	}

	/**
	 * @return string
	 */
	function getDate(){
		$tz = new \DateTimeZone($this->config->get('settings.timezone', 'Europe/Stockholm'));
		$date = new \DateTime($this->object->date, $tz);
		return $date->format($this->config->get('settings.timeformat', 'Y-m-d, \k\l H:i'));
	}
}