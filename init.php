<?php
/*
  Plugin Name: Super Powers
  Plugin URI: http://www.ridewing.se/superpowers
  Description: Create custom types and attach custom properties and templates to each type.
  Author: Nicklas Ridewing
  Version: 1.0
  Author URI: http://www.ridewing.se
 */

/*
|--------------------------------------------------------------------------
| Register Composer Autoloader
|--------------------------------------------------------------------------
*/
$autoloader = dirname(__FILE__).'/vendor/autoload.php';
if(file_exists($autoloader)){
  require_once($autoloader);
}

$applicationAutloader = dirname(WP_CONTENT_DIR) . '/vendor/autoload.php';
if(file_exists($applicationAutloader)) {
  require_once($applicationAutloader);
}


if (defined('WP_ENV') && WP_ENV == 'development')  {

  error_reporting(E_ALL & ~E_DEPRECATED);
  ini_set('display_errors', '1');
}

if (!defined('SUPERPOWERS_AJAX')) {
  define("SUPERPOWERS_AJAX", false);
}

require_once "Core/Helpers.php";
require_once "Core/SuperPowers.php";


$superPowers = new \SuperPowers\Core\SuperPowers();

$superPowersCache = new \SuperPowers\Core\Cache();
$superPowersConfig = new \SuperPowers\Core\Config();

$superPowers->__reloadGlobals();

add_action( 'init', function() use($superPowers) {
  $superPowers->registerRouter();
  $superPowers->boot();
});