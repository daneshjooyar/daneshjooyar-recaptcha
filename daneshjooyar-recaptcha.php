<?php
/**
 * Plugin Name: Daneshjooyar Recaptcha
 * Plugin URI: htts://daneshjooyar.com
 * Author: Hamed Moodi
 * Author URI: http://ircodex.ir
 * Description: Recaptcha Plugin for security
 * Version: 1.0.0
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: daneshjooyar-recaptcha
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define Constant
 */
define( 'DYRECAPTCHA_CLASS', plugin_dir_path( __FILE__ ) . 'classes/');

/**
 * Define Urls
 */

/*
 * Load all classes by 'DYRECAPTCHA_' prefix from classes folder
 */
spl_autoload_register(function( $class ){
    if(strpos($class, 'DYRECAPTCHA_') !== FALSE ){
        include_once( DYRECAPTCHA_CLASS . $class . '.php' );
    }
});

add_action('init', function(){
    load_plugin_textdomain('daneshjooyar-recaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
});

$dyrecaptchaCore = new DYRECAPTCHA_Core();
$dyrecaptchaCore->run();