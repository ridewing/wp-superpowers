<?php namespace SuperPowers;

class Load extends SuperObject{

	private $files = array();

	function view($name) {
		return $this->getFile($name, 'views');
	}

	function controller($name) {

		$parts = explode('.', $name);

		$klassName = '\\SuperPowers\\Controller';

		$parent = '';

		foreach($parts as $part){
			$parentKlass = $klassName;
			$klassName .= "\\{$part}";

			$parent .= $part . '.';

			if(!class_exists($klassName)){
				$loaded = $this->controllerFile(ucfirst(rtrim($parent, ".")));
				if(!$loaded){
					if(class_exists($parentKlass)){
						$klass = new $parentKlass;
						return $klass;
					}
				}
			}
		}

		if(class_exists($klassName)){
			$klass = new $klassName;
			return $klass;
		}

	}

	function controllerFile($name) {
		return $this->file($name);
	}

	function property($postId, $groupId, $propertyId, $index = 0) {

		if(!class_exists('\\SuperPowers\\Property\\SuperProperty')) {
			$this->superProperty();
		}

		$typeId     = $this->post->getType($postId);
		$subtypeId  = $this->post->getSubtype($postId);

		$definition = $this->definition->property($typeId, $subtypeId, $groupId, $propertyId);

		if(empty($definition)) return false;

		$klassName = "\\SuperPowers\\Property\\{$definition['type']}";

		if(!class_exists($klassName))
			$this->propertyFile($definition['type']);

		/** @var SuperProperty $klass */
		$klass = new $klassName;
		$klass->validate($definition);
		$klass->load($groupId, $index, $definition, $postId);

		return $klass;
	}

	function propertyFile($name) {
		return $this->file($name, "properties/{$name}");
	}

	function file($name, $folder = "controllers", $required = true){

		$name = str_replace('.', '/', $name);
		$file = "/{$folder}/{$name}.php";
		if($this->applicationFile($file)) {
			if(!$this->fileIsLoaded(SUPERPOWERS_APPLICATION_DIR . "/{$folder}/{$name}.php")){
				return $this->loadFile(SUPERPOWERS_APPLICATION_DIR . "/{$folder}/{$name}.php", "application");
			}
			return true;
		}
		else if($this->pluginFile($file)) {
			return $this->loadFile(SUPERPOWERS_DIR . "/{$folder}/{$name}.php");
		}

		return false;
	}

	function getFile($name, $folder = 'controllers'){
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
			throw new \ErrorException("No {$folder} named {$name} was found!");
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

		if(!class_exists('\\SuperPowers\\Datasource\\SuperDatasource')) {
			$this->superDatasource();
		}

		$name = mb_strtolower($type);

		$klassName = '\\SuperPowers\\Datasource\\' . ucfirst($name);

		if(!class_exists($klassName)){
			$this->file($name, 'datasources');
		}



		$klass = new $klassName;
		return $klass;
	}

	function fileIsLoaded($file){
		return array_key_exists($file, $this->files);
	}

	function loadFile($file, $env = "plugin"){
		$this->files[$file] = $env;
		return require $file;
	}

	function applicationFile($file){
		return file_exists(SUPERPOWERS_APPLICATION_DIR . $file);
	}

	function pluginFile($file){
		return file_exists(SUPERPOWERS_DIR . $file);
	}

	function composer($name){
		if(!class_exists('\\SuperPowers\\Composer\\SuperComposer')) {
			$this->superComposer();
		}

		$composerExists = $this->file($name, 'composers', false);

		if($composerExists){
			$nameParts = explode('.', $name);

			$klassName = '\\SuperPowers\\Composer\\';

			foreach($nameParts as $part) {
				$klassName .= ucfirst($part);
			}

			$klass = new $klassName;
			return $klass;
		}

		return false;
	}
}