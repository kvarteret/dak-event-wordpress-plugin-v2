<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your smugmug album posts!
Author: Snorre Davøen, Lisa Halvorsen, Robin Garen Aaberg.
Version: 0.0003
*/
$post_type_namespace = "dak_event";

$meta_names = array(
        "dak_event_id" => "id",
        "dak_event_url" => "url",
        "dak_event_ical" => "ical",
    	"dak_event_linkout" => "linkout",
    	"dak_event_startDate" => "start_date",
    	"dak_event_startTime" => "start_time",
    	"dak_event_endDate" => "end_date",
    	"dak_event_endTime" => "end_time",
    	"dak_event_is_accepted" => "is_accepted",
    	"dak_event_is_public" => "is_public",
    	"dak_event_customLocation" => "custom_location",
        "dak_event_commonLocation_id" => "common_location_id",
        "dak_event_commonLocation_name" => "common_location_name",
    	"dak_event_location_id" => "location_id",
    	"dak_event_arranger_id" => "arranger_id",
        "dak_event_arranger_name" => "arranger_name",
        "dak_event_arranger_logo" => "arranger_logo",
        "dak_event_arranger_description" => "arranger_description",
    	"dak_event_festival_id" => "festival_id",
        "dak_event_primaryPicture_url" => "primary_picture_url",
        "dak_event_primaryPicture_desc" => "primary_picture_desc",
    	"dak_event_covercharge" => "covercharge",
    	"dak_event_age_limit" => "age_limit",
    	"dak_event_created_at" => "created_at",
    	"dak_event_updated_at" => "updated_at",
        "dak_event_arranger" => "arranger",
        "dak_event_categories" => "categories",
        "dak_event_festival" => "festival",
    );

# Import plugin php modules
require_once('dak_event_post_type.php');
require_once('dak_event_xmlrpc.php');
require_once('dak_event_admin.php');
//require_once('dak_event_register_taxonomy.php');

# Set up hooks
error_log("Setting up plugin");

// Adding xml-rpc methods
add_filter( 'xmlrpc_methods', 'dak_event_add_xmlrpc_methods');
add_action('init', 'dak_event_create_post_type');
//add_action('init', 'dak_event_register_taxonomy');
add_action('save_post', 'dak_event_save_post_meta', 1, 2);

if (is_admin()) {
    add_action('admin_menu', 'dak_event_admin_add_settings_page');
    add_action('admin_init', 'dak_event_admin_init');
}

?>
