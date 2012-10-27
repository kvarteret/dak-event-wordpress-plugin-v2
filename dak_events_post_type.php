<?php
/*
Plugin Name: DAK Event Post Type
Description: Add penguins to your post!
Author: Snorre Davøen, Lisa Halvorsen.
Version: 0.0001
*/

$post_type_name = "dak_event";

// Set up hooks
add_action('init', 'dak_create_post_type');
//add_action('add_meta_boxes', 'dak_add_meta_boxes');

function dak_create_post_type() {
    global $post_type_name;
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
}/* Adds a box to the main column on the Post and Page edit screens */
function dak_add_metaboxes() {
   add_meta_box( 
        "dak_events_id",
        __("id"), "dak_events_id",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_linkout",
        __("linkout"), "dak_events_linkout",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_start_date",
        __("startDate"), "dak_events_start_date",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_start_time",
        __("startTime"), "dak_events_start_time",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_end_date",
        __("endDate"), "dak_events_end_date",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_end_time",
        __("endTime"), "dak_events_end_time",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_is_accepted",
        __("is_accepted"), "dak_events_is_accepted",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_is_public",
        __("is_visible"), "dak_events_is_public",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_custom_location",
        __("customLocation"), "dak_events_custom_location",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_location_id",
        __("location_id"), "dak_events_location_id",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_arranger_id",
        __("arranger_id"), "dak_events_arranger_id",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_festival_id",
        __("festival_id"), "dak_events_festival_id",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_primary_picture",
        __("primaryPicture"), "dak_events_primary_picture",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_covercharge",
        __("covercharge"), "dak_events_covercharge",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_age_limit",
        __("age_limit"), "dak_events_age_limit",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_created_at",
        __("created_at"), "dak_events_created_at",
        $post_type_name
    );
   add_meta_box( 
        "dak_events_updated_at",
        __("updated_at"), "dak_events_updated_at",
        $post_type_name
    );
}

function dak_events_id() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_id, true);
    echo '<input type="text" name="dak_events_id" value="'.$meta.'" />';
   
}

function dak_events_linkout() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_linkout, true);
    echo '<input type="text" name="dak_events_linkout" value="'.$meta.'" />';
   
}

function dak_events_start_date() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_start_date, true);
    echo '<input type="text" name="dak_events_start_date" value="'.$meta.'" />';
   
}

function dak_events_start_time() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_start_time, true);
    echo '<input type="text" name="dak_events_start_time" value="'.$meta.'" />';
   
}

function dak_events_end_date() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_end_date, true);
    echo '<input type="text" name="dak_events_end_date" value="'.$meta.'" />';
   
}

function dak_events_end_time() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_end_time, true);
    echo '<input type="text" name="dak_events_end_time" value="'.$meta.'" />';
   
}

function dak_events_is_accepted() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_is_accepted, true);
    echo '<input type="text" name="dak_events_is_accepted" value="'.$meta.'" />';
   
}

function dak_events_is_public() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_is_public, true);
    echo '<input type="text" name="dak_events_is_public" value="'.$meta.'" />';
   
}

function dak_events_custom_location() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_custom_location, true);
    echo '<input type="text" name="dak_events_custom_location" value="'.$meta.'" />';
   
}

function dak_events_location_id() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_location_id, true);
    echo '<input type="text" name="dak_events_location_id" value="'.$meta.'" />';
   
}

function dak_events_arranger_id() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_arranger_id, true);
    echo '<input type="text" name="dak_events_arranger_id" value="'.$meta.'" />';
   
}

function dak_events_festival_id() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_festival_id, true);
    echo '<input type="text" name="dak_events_festival_id" value="'.$meta.'" />';
   
}

function dak_events_primary_picture() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_primary_picture, true);
    echo '<input type="text" name="dak_events_primary_picture" value="'.$meta.'" />';
   
}

function dak_events_covercharge() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_covercharge, true);
    echo '<input type="text" name="dak_events_covercharge" value="'.$meta.'" />';
   
}

function dak_events_age_limit() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_age_limit, true);
    echo '<input type="text" name="dak_events_age_limit" value="'.$meta.'" />';
   
}

function dak_events_created_at() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_created_at, true);
    echo '<input type="text" name="dak_events_created_at" value="'.$meta.'" />';
   
}

function dak_events_updated_at() {
    global $post;
    $meta = get_post_meta($post->ID, dak_events_updated_at, true);
    echo '<input type="text" name="dak_events_updated_at" value="'.$meta.'" />';
   
}

?>