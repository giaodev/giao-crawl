<?php
/*
Plugin Name: Giao Crawl
Plugin URI: https://kienthucvotan.com/
Description: Giao Crawl
Author: Giao
Version: 1.0
Text Domain: giao
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'GIAO_CRAWL_VERSION', '1.0' );
define( 'GIAO_CRAWL__MINIMUM_WP_VERSION', '1.0' );
define( 'GIAO_CRAWL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'GIAO_CRAWL', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'GIAO_CRAWL', 'plugin_deactivation' ) );

require_once( GIAO_CRAWL__PLUGIN_DIR . 'class.giao-crawl.php' );

add_action( 'init', array( 'GIAO_CRAWL', 'init' ) );

if ( is_admin() ) {
    require_once( GIAO_CRAWL__PLUGIN_DIR . 'class.giao-crawl-admin.php' );
    add_action( 'init', array( 'GIAO_CRAWL_ADMIN', 'init' ) );
}

