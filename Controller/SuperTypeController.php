<?php namespace SuperPowers\Controller;

use SuperPowers\Core\SuperObject;

abstract class SuperTypeController extends SuperObject {

	/** @var string */
	public $type;

	/** @var string */
	public $subtype;

	/** @var string */
	public $subview;

	/** @var int  */
	public $postId;

	/** @var array */
	public $def;

	/** @var bool */
	public $hasPost = false;

	/** @var bool */
	public $cached = true;

	/** @var string */
	public $title;

	/** @var int  */
	public $page = 1;

	abstract public function getDefinition();

	/**
	 * @param string $salt
	 *
	 * @return string
	 */
	function guid($salt = ""){
		return md5("{$this->type}:{$this->subtype}:{$this->postId}:{$this->getViewName()}:{$salt}:{$this->page}");
	}

	function load($type, $subtype) {
		$this->type = $type;
		$this->subtype = $subtype;
		$this->def = $this->getDefinition();

		add_action('admin_head', array(&$this, 'addScriptData'));
	}

	function getViewName() {

		$name = $this->type;

		if(!empty($this->subtype)){
			$name .= '.' . $this->subtype;
		}

		return $name;
	}

	function addScriptData(){

		$data = json_encode(array(
			'type'          => $this->type,
			'controller'    => ucfirst($this->type),
			'subtype'       => $this->subtype,
			'postId'        => $this->postId,
			'definition'    => $this->def,
			'api'           => $this->app->url . '/api/controller.php'
		));

		echo "<script>var _superData = {$data};</script>";
	}

	function setPost($postId) {
		$this->postId = $postId;
		$this->hasPost = true;

		$pageTitle = get_bloginfo('name');

		if(!is_front_page()) {
			$post = get_post($this->postId);
			$pageTitle = "{$post->post_title} | {$pageTitle}";
		}

		$this->app->setTitle($pageTitle);
	}

	function setSubview($subview) {
		$this->subview = $subview;
	}

	public function save($args) {

		if(!empty($args['subtype'])){
			$this->post->setSubtype($this->postId, $args['subtype']);
		}

		// Filter out only plugin properties and values
		$groups = $this->propertyHelpers->getOnlySuperPropertiesFromArray($args);


		foreach($groups as $groupId => $group) {

			$count = 0;

			$groupDefinition = $this->definition->group($this->type, $this->subtype, $groupId);
			$propertiesToRemove = array_flip(array_map(function($property){ return $property['id']; }, $groupDefinition['properties']));

			foreach($group as $index => $properties) {
				$allPropertiesAreEmpty = true;
				$count++;

				foreach($properties as $propertyId => $value){

					$property = $this->load->property($this->postId, $groupId, $propertyId, $index);

					unset( $propertiesToRemove[$propertyId] );

					if((is_string($value) && strlen($value) > 0 && $value != '-1') || !empty($value) && $value != '-1') {
						$property->save($value);
						$allPropertiesAreEmpty = false;
					} else {
						$property->delete();
					}
				}

				if($allPropertiesAreEmpty){
					$count--;
				}
			}

			// Remove properties that has not been touched
			foreach(array_flip($propertiesToRemove) as $propertyId){
				$property = $this->load->property($this->postId, $groupId, $propertyId, $index);
				$property->delete();
			}

			$this->group->setGroupRepeatForPost($this->postId, $groupId, $count);
		}

		$this->cache->removeViewForPost($this->guid());
		$this->image->clearImagesForPost($this->postId);
	}

	/**
	 * @param $args
	 */
	public function post($args){

	}

	public function render() {

		if(!empty($this->subview)){
			$this->renderSubview($this->subview);
		}

		$viewName = $this->getViewName();

		if($this->config->get('settings.cache') && $this->cached){
			if($this->viewcache->exists( $this->guid() )){
				_s($this->viewcache->get( $this->guid() ));
				exit();
			}
		}

		$args = $this->view();

		$content = $this->html->getView($viewName, $args);

		if($this->config->get('settings.cache') && $this->cached){
			$this->viewcache->set($this->guid(), $content);
		}

		_s($content);
		exit();
	}

	protected function getContext(){
		$context = $this->type;
		if(!empty($this->subtype)){
			$context .= ".{$this->subtype}";
		}

		if(!empty($this->subview)){
			$context .= ".{$this->subview}";
		}

		return $context;
	}

	protected function renderSubview($view) {

	}


	protected function view() {
		return null;
	}

	public function listView() {

	}

	public function editView() {

		add_action( 'add_meta_boxes', array(&$this, 'buildGroups'));
	}

	public function buildGroups() {

		if(empty($this->def['groups'])) {
			return;
		}

		foreach($this->def['groups'] as $groupId => $group) {
			add_meta_box($groupId, $group['name'], array(&$this, 'renderGroup'), $this->type, 'normal', 'default', $group);
		}
	}

	public function renderGroup($post, $data) {

		$count = 0;

		if($data['args']['repeatable']){
			$count = $this->group->getGroupRepeatForPost($post->ID, $data['id']);
		}

		// We always render at least one repeat
		$count = max(1, $count);

		echo "<div class='super-group-wrapper'>";

		for($index = 0; $index < $count; $index++){
			echo $this->getGroupContent($data['id'], $index);
		}

		echo "</div>";

		if($data['args']['repeatable']){
			echo $this->group->getRepeatButton($data['id']);
		}
	}

	public function getGroupContent($groupId, $index = 0) {
		$content = '<div class="super-group-content" data-id="'.$groupId.'">';
		//$content .= "<input type='hidden' name='superpowers[{$groupId}][{$index}][groupIndex]' class='superpowers-group-index' value='{$index}' />";
		$content .= $this->group->getControlls($groupId, $index);
		$content .= $this->group->getRemoveButton($groupId);
		$def = $this->definition->group($this->type, $this->subtype, $groupId);

		if(!empty($def['properties'])){
			foreach($def['properties'] as $property ){
				/** @var SuperPropertyController $property */
				$property = $this->load->property($this->postId, $groupId, $property['id'], $index);
				if($property) {
					$property->loadValue();
					$content .= $property->view();
				}
			}
		}
		$content .= '</div>';

		return $content;
	}

	public function addGroup($args) {

		$this->setPost($args['postId']);

		$count = $this->group->addGroupRepeatForPost($args['postId'], $args['groupId']);

		$content = $this->getGroupContent($args['groupId'], $count-1);

		return $this->api->response($content);
	}
}