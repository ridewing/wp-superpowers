<?php namespace SuperPowers\Datasource;

class Posts extends SuperDatasource {

	function get($args = null) {
		if(empty($this->data)) {

			$posts = get_posts(array(
				'post_type' => $args['posttype']
			));

			foreach($posts as $post){
				$this->data[$post->ID] = $post->post_title;
			}
		}

		return $this->data;
	}
}