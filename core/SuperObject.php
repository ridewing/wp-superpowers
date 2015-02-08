<?php namespace SuperPowers;

require_once "Cache.php";

/**
 * @property Api api
 * @property Composer composer
 * @property Config config
 * @property Create create
 * @property Definition definition
 * @property File file
 * @property Group group
 * @property Html html
 * @property Image image
 * @property Load load
 * @property Post post
 * @property Property property
 * @property PropertyHelpers propertyHelpers
 */
class SuperObject {

	/**
	 * Application
	 * @var SuperPowers
	 */
	protected $app;

	/**
	 * Session cache storage
	 * @var ApplicationCache
	 */
	protected $cache;

	public $applicationDirectory;
	public $directory;

	function __construct()
	{
		$this->cache = new ApplicationCache();
		if(defined('SUPERPOWERS_APPLICATION_DIR')){
			$this->applicationDirectory = SUPERPOWERS_APPLICATION_DIR;
		}
		if(defined('SUPERPOWERS_DIR')){
			$this->directory = SUPERPOWERS_DIR;
		}

		global $superPowers;

		$this->app = $superPowers;
	}

	function loadClass($name, $type, $skipCache = false)
	{
		$lowercaseName = strtolower($name);
		$cacheKey = $this->cache->getKey($name, $type);

		if(!$skipCache && $this->cache->exists($cacheKey)) {
			return $this->cache->get($cacheKey);
		}

		$lowercaseName = strtolower($name);
		$uppercaseName = ucfirst($lowercaseName);
		$applicationPath = "{$this->applicationDirectory}/$type/$name.php";
		$pluginPath = "{$this->directory}/$type/$name.php";

		if(file_exists($applicationPath))
		{
			$filepath = $applicationPath;
		}
		else if (file_exists($pluginPath))
		{
			$filepath = $pluginPath;
		}

		if(!empty($filepath)){

			require $filepath;

			$klassname = '\\SuperPowers\\' . $uppercaseName;
			$klass = new $klassname;

			$this->cache->set($cacheKey, $klass);

			return $klass;
		}
		else
		{
			echo $pluginPath;
			die();
			throw new \ErrorException("Can't load {$type}: {$name}");
		}
	}

	public function __get($name)
	{
		return $this->loadClass($name, 'library');
	}

}