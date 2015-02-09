<?php namespace SuperPowers\Controller;

use SuperPowers\SuperObject;

abstract class SuperTypeController extends SuperObject {

	public $type;
	public $subtype;
	public $subview;
	public $postId;
	public $def;
	public $hasPost = false;

	abstract public function getDefinition();

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

		$this->cache->removeViewForPost($this->postId);
		$this->image->clearImagesForPost($this->postId);
	}

	public function post($args){

	}

	public function render() {
		if(!empty($this->subview)){
			$this->renderSubview($this->subview);
		}

		$viewName = $this->getViewName();
		$args = $this->view();

		$this->html->view($viewName, $args);
		exit();
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
				$property->loadValue();
				$content .= $property->view();
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