<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your smugmug album posts!
Author: Snorre Davøen, Lisa Halvorsen, Robin Garen Aaberg.
Version: 0.0003
*/
$post_type_namespace = "dak_event";

# Import plugin php modules
require_once(__DIR__ . '/dak_event_post_type.php');
require_once(__DIR__ . '/dak_event_attachment.php');
require_once(__DIR__ . '/dak_event_xmlrpc.php');
require_once(__DIR__ . '/dak_event_admin.php');
//require_once('dak_event_register_taxonomy.php');

# Set up hooks
error_log("Setting up plugin");

$dak_event_provider_types = array(
    // 'className' => 'Class Name'
    'eventsCalendarClient' => 'DAK Events Calendar Client',
    'linticketCalendarClient' => 'LinTicket Calendar Client'
);

// Adding xml-rpc methods
add_filter( 'xmlrpc_methods', 'dak_event_add_xmlrpc_methods');
add_action('init', 'dak_event_create_post_type');
add_action('save_post', 'dak_event_save_post_meta', 1, 2);

if (is_admin()) {
    add_action('admin_menu', 'dak_event_admin_add_settings_page');
    add_action('admin_init', 'dak_event_admin_init');
}

function dak_event_activate() {
	dak_event_create_post_type();

	// Remeber to refresh url rewriting rules when activating and deactivating a plugin
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'dak_event_activate');

function dak_event_deactivate() {
	// Remeber to refresh url rewriting rules when activating and deactivating a plugin
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'dak_event_deactivate');

function dak_event_api_init() {
	global $dakevent_api_event;

	// TODO: Check if WP-API is activated, if not install github.com/rmccure/WP-API
	if (class_exists("WP_JSON_CustomPostType")) {
		require_once (__DIR__ . '/api/DakEvent_API_Event.php');
		$dakevent_api_event = new DakEvent_API_Event();
	} else {
		add_action('admin_notices', 'dak_event_missing_requirement');
	}
}
add_action( 'plugins_loaded', 'dak_event_api_init' );

function dak_event_missing_requirement() {
	echo "<div class=\"error\">";
	
	echo "Kvarteret Event Post Type requires <a href=\"github.com/rmccue/WP-API\">WP-API</a>";

	echo "</div>";
}
