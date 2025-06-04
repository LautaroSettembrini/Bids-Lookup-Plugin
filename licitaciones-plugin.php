<?php
/*
Plugin Name: Bids Lookup Plugin
Description: Modular plugin to search and filter bids via AJAX, with pagination and filter controls.
Version: 1.0
Author: Lautaro Settembrini
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include modules
require_once plugin_dir_path(__FILE__) . 'inc/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'inc/ajax-handler.php';
