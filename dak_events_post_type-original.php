<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your post!
Author: Snorre Davøen, Lisa Halvorsen.
Version: 0.0001
*/



$metaboxes = array(
    	"dak_events_id" => "id",
    	"dak_events_linkout" => "linkout",
    	"dak_events_start_date" => "startDate",
    	"dak_events_start_time" => "startTime",
    	"dak_events_end_date" => "endDate",
    	"dak_events_end_time" => "endTime",
    	"dak_events_is_accepted" => "is_accepted",
    	"dak_events_is_public" => "is_visible",
    	"dak_events_custom_location" => "customLocation",
    	"dak_events_location_id" => "location_id",
    	"dak_events_arranger_id" => "arranger_id",
    	"dak_events_festival_id" => "festival_id",
    	"dak_events_primary_picture" => "primaryPicture",
    	"dak_events_covercharge" => "covercharge",
    	"dak_events_age_limit" => "age_limit",
    	"dak_events_created_at" => "created_at",
    	"dak_events_updated_at" => "updated_at"

    	);

$post_type_name = "dak_event";

// Set up hooks
add_action('init', 'dak_create_post_type');
//add_action('add_meta_boxes', 'dak_add_meta_boxes');

function dak_create_post_type() {
	register_post_type(
		$post_type_name,
		 array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' ),
                'add_new' => __( 'Add New Event' ),
                'add_new_item' => __( 'Add New Event' ),
                'edit_item' => __( 'Edit Event' ),
                'new_item' => __( 'Add New Event' ),
                'view_item' => __( 'View Event' ),
                'search_items' => __( 'Search Event' ),
                'not_found' => __( 'No events found' ),
                'not_found_in_trash' => __( 'No events found in trash' )
            ),
            'public' => true,
            'supports' => array( 'title', "content", 'thumbnail' ),
            'capability_type' => 'post',
            //'rewrite' => array("slug" => "dak_event"), // Permalinks format
            //'menu_position' => 5,
            'register_meta_box_cb' => 'dak_add_metaboxes'
        )
	);
}

/* Adds a box to the main column on the Post and Page edit screens */
function dak_add_metaboxes() {
    

	foreach ($metaboxes as $metabox_id => $metabox_title) {
		add_meta_box( 
        	$metabox_id,
        	__( $metabox_title), $metabox_id,
        	$post_type_name
    	);
	}

    






}


function dak_event_id() {
    global $post;
    $meta = get_post_meta($post->ID, "dak_event_id", true);
    echo '<input type="text" name="_id" value="'.$meta.'" />';
}


?>