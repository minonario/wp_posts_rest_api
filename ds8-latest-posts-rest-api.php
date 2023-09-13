<?php
/**
 * @package DS8 Latest Posts Rest API
 */
/*
Plugin Name: DS8 Latest Posts Rest API
Plugin URI: https://deseisaocho.com/
Description: Muestra las noticias por medio de servicios <strong>REST API</strong>
Version: 1.0
Author: JLMA
Author URI: https://deseisaocho.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: ds8_latest_posts
*/


if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'DS8LATESTPOSTS_VERSION', '1' );
define( 'DS8LATESTPOSTS_MINIMUM_WP_VERSION', '5.0' );
define( 'DS8LATESTPOSTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'DS8_Latest_Posts', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'DS8_Latest_Posts', 'plugin_deactivation' ) );

//require_once DS8LATESTPOSTS_PLUGIN_DIR . '/includes/helpers.php';
require_once( DS8LATESTPOSTS_PLUGIN_DIR . 'class.ds8_latest_posts.php' );

add_action( 'init', array( 'DS8_Latest_Posts', 'init' ) );

global $restapi_ds8_posts;
$restapi_ds8_posts = DS8_Latest_Posts::get_instance();

if ( is_admin() ) {
	require_once( DS8LATESTPOSTS_PLUGIN_DIR . 'class.ds8_lastest_posts_admin.php' );
	add_action( 'init', array( 'DS8LastestPostsAdmin', 'init' ) );
}