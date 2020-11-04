<?php
/**
 * Plugin Name: Support Desk
 * Plugin URI: https://larasoftbd.net
 * Description: Support serviceing    
 * Version: 1.0.0
 * Author: larasoftbd
 * Author URI: https://larasoftbd.net
 * Text Domain: support-desk
 * Domain Path: /languages
 * Requires at least: 1.0.0
 * Tested up to: 0.0
 *
 * @package     Support Desk
 * @category 	Core
 * @author 		larasoftbd
 */


if ( ! defined( 'ABSPATH' ) ) { exit; }
define('supportDeskDIR', plugin_dir_path( __FILE__ ));

define('SD_PATH',__FILE__);

define('supportDeskURL', plugin_dir_url( __FILE__ ));


require_once(supportDeskDIR . 'inc/class.php');
$sprt = new supportDeskClass();