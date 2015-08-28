<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Viewcache extends SuperObject {

	private $postId = null;
	private $context = null;
	private $composerContext = null;

	function load($postId, $context){
		$this->setPost($postId);
		$this->setContext($context);
	}

	function path($view){

		$view = trim($view, '/');
		$view = str_replace('/', '.', $view);


		$postPath = $this->postPath();
		$path = "{$postPath}/{$this->context}/";//

		if(!empty($this->composerContext)) {
			$path .= "{$this->composerContext}/";
		}

		$path .= "{$view}.view";

		return $path;
	}

	function postPath(){
		return SUPERPOWERS_APPLICATION_DIR . "/cache/views/{$this->postId}";
	}

	function exists($view){
		$path = $this->path($view);

		return file_exists($path);
	}

	function get($view){
		$path = $this->path($view);

		if(file_exists($path)) {
			return file_get_contents($path);
		}

		return false;
	}

	function set($view, $content){
		$path = $this->path($view);

		$this->file->ensureStructure($path);
		file_put_contents($path, $content);
	}

	function delete($view){
		$path = $this->path($view);

		if(file_exists($path)){
			return unlink($path);
		}
	}

	function deleteAll(){
		$path = $this->postPath();

		if(file_exists($path)){
			return $this->file->deleteDir($path);
		}
	}

	function setPost($postId){
		$this->postId = $postId;
	}

	function setContext($context){
		$this->context = $context;
	}

	function setComposerContext($context){
		$this->composerContext = $context;
	}

	function reset(){
		$this->setPost(null);
		$this->setContext(null);
		$this->setComposerContext(null);
	}


}