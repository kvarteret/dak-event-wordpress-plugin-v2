<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your post!
Author: Snorre Davøen, Lisa Halvorsen.
Version: 0.0001
*/

// Set up hooks
add_action('init', 'dak_create_post_type');
//add_action('add_meta_boxes', 'dak_add_meta_boxes');

function dak_create_post_type() {
	register_post_type(
		'dak_event',
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
            'supports' => array( 'title', 'thumbnail' ),
            'capability_type' => 'post',
            //'rewrite' => array("slug" => "dak_event"), // Permalinks format
            //'menu_position' => 5,
            'register_meta_box_cb' => 'dak_add_metaboxes'
        )
	);
}

/* Adds a box to the main column on the Post and Page edit screens */
function dak_add_metaboxes() {
    add_meta_box( 
        'dak_event_id',
        __( 'Event ID'), 'dak_event_id',
        'dak_event'
    );
}


function dak_event_id() {
    global $post;
    $meta = get_post_meta($post->ID, "dak_event_id", true);
    echo '<input type="text" name="_id" value="'.$meta.'" />';
}


?>