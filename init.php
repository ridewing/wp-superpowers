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
$autoloader = dirname(dirname(dirname(__DIR__))).'/vendor/autoload.php';
if(file_exists($autoloader)){
  require_once(dirname(dirname(dirname(__DIR__))).'/vendor/autoload.php');
}


if (defined('WP_ENV') && WP_ENV == 'development')  {

  error_reporting(E_ALL & ~E_DEPRECATED);
  ini_set('display_errors', '1');
}

if (!defined('SUPERPOWERS_AJAX')) {
  define("SUPERPOWERS_AJAX", false);
}

require_once "core/Helpers.php";
require_once "core/SuperPowers.php";

$superPowers = new \SuperPowers\SuperPowers();

if(!SUPERPOWERS_AJAX){
  add_action( 'init', function() use($superPowers)
  {
    $superPowers->boot();
  });
}