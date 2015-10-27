<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Load extends SuperObject {

	private $files = array();

	public static $APP_FILE = 'application_file';
	public static $PLUGIN_FILE = 'plugin_file';

	function view($name) {
		return $this->getFile($name, 'view');
	}

	function appNamespace() {
		return $this->config->get('settings.namespace', 'SuperPowers');
	}

	function module($name) {


		$namespace = $this->appNamespace();
		$appClassName = "\\{$namespace}";
		$pluginClassName = "\\SuperPowers";

		$parts = explode('.', $name);

		foreach($parts as $part){
			$part = ucfirst($part);
			$appClassName .= "\\{$part}";
			$pluginClassName .= "\\{$part}";
		}

			if(class_exists($appClassName)){
			$klass = new $appClassName;
			return $klass;
		}
		else if(class_exists($pluginClassName)){
			$klass = new $pluginClassName;
			return $klass;
		}

		return null;
	}

	function controller($name) {
		$name = ucfirst($name);
		return $this->module("Controller.{$name}");
	}

	function model($name, $object) {
		$name = ucfirst($name);

		/** @var \SuperPowers\Model\SuperModel $model */
		$model = $this->module("Model.{$name}");
		if($model) {
			$model->setObject($object);
		}

		return $model;
	}

	function router() {
		$nameSpace = $this->appNamespace();

		if(class_exists("\\$nameSpace\\Router")){

			$route = "\\$nameSpace\\Router";

			return new $route;
		}
	}

	function property($postId, $groupId, $propertyId, $index = 0) {

		$typeId     = $this->post->getType($postId);
		$subtypeId  = $this->post->getSubtype($postId);

		$definition = $this->definition->property($typeId, $subtypeId, $groupId, $propertyId);

		if(empty($definition)) return false;

		$type = ucfirst($definition['type']);

		$property = $this->module("Property.{$type}.{$type}");

		if(empty($property)){
			trigger_error(
				sprintf(
					"Fatal error: Failed to load property: %s, in %s",
					$type,
					get_class($this)
				)
			);

			exit;
		}

		$property->validate($definition);
		$property->load($groupId, $index, $definition, $postId);

		return $property;
	}

	function whereIsFile($file) {

		if($this->applicationFile($file)) {
			return static::$APP_FILE;
		}
		else if($this->pluginFile($file)) {
			return static::$PLUGIN_FILE;
		}

		return false;
	}

	function file($name, $folder = "controllers") {

		$name = str_replace('.', '/', $name);
		if(!empty($folder)){
			$file = "/{$folder}/{$name}.php";
		} else {
			$file = "/{$name}.php";
		}


		switch($this->whereIsFile($file)) {
			case static::$APP_FILE:
				return $this->loadFile(SUPERPOWERS_APPLICATION_DIR . "/{$folder}/{$name}.php");
				break;
			case static::$PLUGIN_FILE:
				return $this->loadFile(SUPERPOWERS_DIR . "/{$folder}/{$name}.php");
				break;
			default:
				return false;
		}
	}

	function getFile($name, $folder = 'controllers'){

		$folder = ucfirst($folder);
		$name = ucfirst($name);
		$file = "/{$folder}/{$name}.php";
		if($this->applicationFile($file)) {
			if(!$this->fileIsLoaded(SUPERPOWERS_APPLICATION_DIR . "/{$folder}/{$name}.php")){
				return SUPERPOWERS_APPLICATION_DIR . "/{$folder}/{$name}.php";
			}
		}
		else if($this->pluginFile($file)) {
			return SUPERPOWERS_DIR . "/{$folder}/{$name}.php";
		}
		else {
			$showError = $this->config->get('settings.show_error');
			if($showError == true){
				throw new \ErrorException("No {$folder} named {$name} was found!");
			}
			else {
				$this->app->error404();
			}
		}
	}

	function superProperty() {
		$this->file("SuperProperty", 'properties');
	}

	function superDatasource() {
		$this->file("SuperDatasource", 'datasources');
	}

	function superComposer() {
		$this->file("SuperComposer", 'composers');
	}

	function datasource($type, $args = null){

		$datasource = $this->module("Datasource.{$type}");

		return $datasource;
	}

	function fileIsLoaded($file){
		return array_key_exists($file, $this->files);
	}

	function loadFile($file, $env = "plugin"){
		if(!$this->fileIsLoaded($file)){
			$this->files[$file] = $env;
			return require $file;
		}
	}

	function applicationFile($file){
		return file_exists(SUPERPOWERS_APPLICATION_DIR . $file);
	}

	function pluginFile($file){
		return file_exists(SUPERPOWERS_DIR . $file);
	}

	function composer($name, $params){

		$composer = $this->module("Composer.$name");
		if($composer){

			$composer->load($params);
			return $composer;
		}

		return false;
	}
}