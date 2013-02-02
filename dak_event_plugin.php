<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your smugmug album posts!
Author: Snorre Davøen, Lisa Halvorsen, Robin Garen Aaberg.
Version: 0.0003
*/
$post_type_namespace = "dak_event";

# Import plugin php modules
require_once('dak_event_post_type.php');
require_once('dak_event_xmlrpc.php');
require_once('dak_event_register_taxonomy.php');

# Set up hooks
error_log("Setting up plugin");

// Adding xml-rpc methods
add_filter( 'xmlrpc_methods', 'dak_event_add_xmlrpc_methods');
add_action('init', 'dak_event_create_post_type');
add_action('init', 'dak_event_register_taxonomy');
add_action('save_post', 'dak_event_save_post_meta', 1, 2);


?>