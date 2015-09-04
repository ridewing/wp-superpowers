<?php namespace SuperPowers\Datasource;

class Posts extends SuperDatasource {

	function isGrouped() {
		return true;
	}

	function get($args = null) {
		if(empty($this->data)) {

			$query = new \WP_Query(array(
				'post_type' => $args['posttype'],
				'posts_per_page' => -1,
				'post_status' => 'publish'
			));

			$posts = array();

			foreach($query->posts as $post){
				if(!array_key_exists($post->post_type, $posts)){
					$posts[$post->post_type] = array();
				}

				$posts[$post->post_type][$post->ID] = $post->post_title;
			}

			$types = $this->config->get('types');


			foreach($types as $type) {
				if(array_key_exists($type['id'], $posts)) {
					$this->data[$type['label']] = $posts[$type['id']];
				}
			}
		}

		return $this->data;
	}
}