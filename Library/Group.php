<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Group extends SuperObject {

	function getGroupRepeatForPost($postId, $groupId) {

		$repeat = get_post_meta($postId, "superpowers.{$groupId}", true);

		if(empty($repeat)) return 0;

		return $repeat;
	}

	function setGroupRepeatForPost($postId, $groupId, $repeat) {
		update_post_meta($postId, "superpowers.{$groupId}", $repeat);
	}

	function addGroupRepeatForPost($postId, $groupId, $add = 1) {
		$current = $this->getGroupRepeatForPost($postId,$groupId);
		$this->setGroupRepeatForPost($postId,$groupId, $current + $add);
		return $current + $add;
	}

	function subtractGroupRepeatForPost($postId, $groupId, $subtract = 1) {
		$current = $this->getGroupRepeatForPost($postId,$groupId);
		$this->setGroupRepeatForPost($postId,$groupId, $current - $subtract);
		return $current - $subtract;
	}

	function getRepeatButton($groupId){
		return $this->html->render(SUPERPOWERS_DIR . '/View/Group/Repeatbutton.php', array('groupId' => $groupId));
	}

	function getControlls($groupId, $index) {
		return $this->html->render(SUPERPOWERS_DIR . '/View/Group/Controlls.php', array('groupId' => $groupId, 'index' => $index));
	}

	function getRemoveButton($groupId) {
		return $this->html->render(SUPERPOWERS_DIR . '/View/Group/Remove.php', array('groupId' => $groupId));
	}
}