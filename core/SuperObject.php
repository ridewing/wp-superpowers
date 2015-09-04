<?php namespace SuperPowers\Core;

require_once "Cache.php";

/**
 * @property \SuperPowers\Library\Api api
 * @property \SuperPowers\Library\Composer composer
 * @property \SuperPowers\Library\Create create
 * @property \SuperPowers\Library\Definition definition
 * @property \SuperPowers\Library\File file
 * @property \SuperPowers\Library\Group group
 * @property \SuperPowers\Library\Html html
 * @property \SuperPowers\Library\Viewcache viewcache
 * @property \SuperPowers\Library\Image image
 * @property \SuperPowers\Library\Load load
 * @property \SuperPowers\Library\Post post
 * @property \SuperPowers\Library\Property property
 * @property \SuperPowers\Library\PropertyHelpers propertyHelpers
 * @property \SuperPowers\Library\Session session
 * @property \SuperPowers\Library\Request request
 */
class SuperObject {

	/**
	 * Application
	 * @var SuperPowers
	 */
	protected $app;

	/**
	 * Session cache storage
	 * @var \SuperPowers\Core\Cache
	 */
	protected $cache;

	/**
	 * Application config class
	 * @var \SuperPowers\Core\Config
	 */
	protected $config;

	public $applicationDirectory;
	public $directory;

	function __construct()
	{

		if(defined('SUPERPOWERS_APPLICATION_DIR')){
			$this->applicationDirectory = SUPERPOWERS_APPLICATION_DIR;
		}
		if(defined('SUPERPOWERS_DIR')){
			$this->directory = SUPERPOWERS_DIR;
		}


		global $superPowers, $superPowersCache, $superPowersConfig;

		$this->app = $superPowers;
		$this->cache = $superPowersCache;
		$this->config = $superPowersConfig;
	}

	public function __reloadGlobals(){
		global $superPowers, $superPowersCache, $superPowersConfig;

		$this->app = $superPowers;
		$this->cache = $superPowersCache;
		$this->config = $superPowersConfig;
	}

	public function __load($name){

		if($this->cache->exists("superObject.{$name}")) {
			return $this->cache->get("superObject.{$name}");
		}

		$parts = explode('.', $name);

		$namespace = "";

		if(isset($this->config)){
			$namespace = $this->config->get('settings.namespace');
		}

		$appClassName = "\\{$namespace}";
		$pluginClassName = "\\SuperPowers";

		foreach($parts as $part){
			$part = ucfirst($part);
			$appClassName .= "\\{$part}";
			$pluginClassName .= "\\{$part}";
		}

		if(class_exists($appClassName)) {
			$klass = new $appClassName;
		}
		else if(class_exists($pluginClassName))
		{
			$klass = new $pluginClassName;
		}

		if(isset($klass)) {
			$this->cache->set("superObject.{$name}", $klass);
			return $klass;
		}
		else {
			trigger_error("Error loading lib {$name}, App class: {$appClassName}, Plugin class: {$pluginClassName}"); exit;
		}
	}

	public function __get($name)
	{
		$name = ucfirst($name);
		return $this->__load("Library.$name");
	}

}