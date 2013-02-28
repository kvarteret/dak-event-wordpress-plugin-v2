<?php
$post_type_namespace = "dak_event";

function dak_event_create_post_type() {
    global $post_type_namespace;

    register_post_type(
        $post_type_namespace,
         array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' ),
                'add_new' => __( 'Add New Event' ),
                'add_new_item' => __( 'Add New Event' ),
                'edit_item' => __( 'Edit Event' ),
                'new_item' => __( 'Add New Event' ),
                'view_item' => __( 'View Event' ),
                'search_items' => __( 'Search Events' ),
                'not_found' => __( 'No Events found' ),
                'not_found_in_trash' => __( 'No Events found in trash' )
            ),
            'public' => true,
            'supports' => array( 'title', "content", 'thumbnail' ),
            'capability_type' => 'post',
            'register_meta_box_cb' => 'dak_event_add_metaboxes'
        )
    );
}

/* Adds a box to the main column on the Post and Page edit screens */
function dak_event_add_metaboxes() { 
    global $post_type_namespace;

    add_meta_box( 
        "dak_event_id",
        __("id"), "dak_event_id",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_url",
        __("url"), "dak_event_url",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_ical",
        __("ical"), "dak_event_ical",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_linkout",
        __("linkout"), "dak_event_linkout",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_start_date",
        __("startDate"), "dak_event_start_date",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_start_time",
        __("startTime"), "dak_event_start_time",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_end_date",
        __("endDate"), "dak_event_end_date",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_end_time",
        __("endTime"), "dak_event_end_time",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_is_accepted",
        __("is_accepted"), "dak_event_is_accepted",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_is_public",
        __("is_visible"), "dak_event_is_public",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_custom_location",
        __("customLocation"), "dak_event_custom_location",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_common_location_id",
        __("commonLocation_id"), "dak_event_common_location_id",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_common_location_name",
        __("commonLocation_name"), "dak_event_common_location_name",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_location_id",
        __("location_id"), "dak_event_location_id",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_arranger_id",
        __("arranger_id"), "dak_event_arranger_id",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_arranger_name",
        __("arranger_name"), "dak_event_arranger_name",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_arranger_logo",
        __("arranger_logo"), "dak_event_arranger_logo",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_arranger_description",
        __("arranger_description"), "dak_event_arranger_description",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_festival_id",
        __("festival_id"), "dak_event_festival_id",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_primary_picture_url",
        __("primaryPicture url"), "dak_event_primary_picture_url",
        $post_type_namespace
    );

	add_meta_box(
        "dak_event_primary_picture_description",
        __("primaryPicture description"), "dak_event_primary_picture_description",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_covercharge",
        __("covercharge"), "dak_event_covercharge",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_age_limit",
        __("age_limit"), "dak_event_age_limit",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_created_at",
        __("created_at"), "dak_event_created_at",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_updated_at",
        __("updated_at"), "dak_event_updated_at",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_arranger",
        __("arranger"), "dak_event_arranger",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_categories",
        __("categories"), "dak_event_categories",
        $post_type_namespace
    );

    add_meta_box( 
        "dak_event_festival",
        __("festival"), "dak_event_festival",
        $post_type_namespace
    );
}

function dak_event_id() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_id', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_id" value="'.$meta.'" />';
   
}

function dak_event_url() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_url', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_url" value="'.$meta.'" />';
   
}

function dak_event_ical() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_ical', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_ical" value="'.$meta.'" />';
   
}

function dak_event_linkout() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_linkout', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_linkout" value="'.$meta.'" />';
   
}

function dak_event_start_date() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_start_date', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_start_date" value="'.$meta.'" />';
   
}

function dak_event_start_time() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_start_time', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_start_time" value="'.$meta.'" />';
   
}

function dak_event_end_date() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_end_date', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_end_date" value="'.$meta.'" />';
   
}

function dak_event_end_time() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_end_time', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_end_time" value="'.$meta.'" />';
   
}

function dak_event_is_accepted() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_is_accepted', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_is_accepted" value="'.$meta.'" />';
   
}

function dak_event_is_public() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_is_public', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_is_public" value="'.$meta.'" />';
   
}

function dak_event_custom_location() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_custom_location', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_custom_location" value="'.$meta.'" />';
   
}

function dak_event_common_location_id() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_common_location_id', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_common_location_id" value="'.$meta.'" />';
   
}

function dak_event_common_location_name() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_common_location_name', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_common_location_name" value="'.$meta.'" />';
   
}

function dak_event_location_id() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_location_id', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_location_id" value="'.$meta.'" />';
   
}

function dak_event_arranger_id() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_arranger_id', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_arranger_id" value="'.$meta.'" />';
   
}

function dak_event_arranger_name() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_arranger_name', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_arranger_name" value="'.$meta.'" />';
   
}

function dak_event_arranger_logo() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_arranger_logo', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_arranger_logo" value="'.$meta.'" />';
   
}

function dak_event_arranger_description() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_arranger_description', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_arranger_description" value="'.$meta.'" />';
   
}

function dak_event_festival_id() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_festival_id', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_festival_id" value="'.$meta.'" />';
   
}

function dak_event_primary_picture_url() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_primary_picture_url', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_primary_picture_url" value="'.$meta.'" />';
}

function dak_event_primary_picture_description() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_primary_picture_description', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_primary_picture_description" value="'.$meta.'" />';
   
}

function dak_event_covercharge() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_covercharge', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_covercharge" value="'.$meta.'" />';
   
}

function dak_event_age_limit() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_age_limit', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_age_limit" value="'.$meta.'" />';
   
}

function dak_event_created_at() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_created_at', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_created_at" value="'.$meta.'" />';
   
}

function dak_event_updated_at() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_updated_at', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_updated_at" value="'.$meta.'" />';
   
}

function dak_event_arranger() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_arranger', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_arranger" value="'.$meta.'" />';
   
}

function dak_event_categories() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_categories', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_categories" value="'.$meta.'" />';
   
}

function dak_event_festival() {
    global $post;
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, 'dak_event_festival', true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="dak_event_festival" value="'.$meta.'" />';
   
}

// Method hijacked from Devin @ http://wptheming.com/2010/08/custom-metabox-for-post-type/ 
function dak_event_save_post_meta($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (!isset($_POST['meta_noncename']) || !wp_verify_nonce( $_POST['meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    if(!empty($_POST['dak_event_id'])) $dak_event_meta['dak_event_id'] = $_POST['dak_event_id'];
    if(!empty($_POST['dak_event_url'])) $dak_event_meta['dak_event_url'] = $_POST['dak_event_url'];
    if(!empty($_POST['dak_event_ical'])) $dak_event_meta['dak_event_ical'] = $_POST['dak_event_ical'];
    if(!empty($_POST['dak_event_linkout'])) $dak_event_meta['dak_event_linkout'] = $_POST['dak_event_linkout'];
    if(!empty($_POST['dak_event_start_date'])) $dak_event_meta['dak_event_start_date'] = $_POST['dak_event_start_date'];
    if(!empty($_POST['dak_event_start_time'])) $dak_event_meta['dak_event_start_time'] = $_POST['dak_event_start_time'];
    if(!empty($_POST['dak_event_end_date'])) $dak_event_meta['dak_event_end_date'] = $_POST['dak_event_end_date'];
    if(!empty($_POST['dak_event_end_time'])) $dak_event_meta['dak_event_end_time'] = $_POST['dak_event_end_time'];
    if(!empty($_POST['dak_event_is_accepted'])) $dak_event_meta['dak_event_is_accepted'] = $_POST['dak_event_is_accepted'];
    if(!empty($_POST['dak_event_is_public'])) $dak_event_meta['dak_event_is_public'] = $_POST['dak_event_is_public'];
    if(!empty($_POST['dak_event_custom_location'])) $dak_event_meta['dak_event_custom_location'] = $_POST['dak_event_custom_location'];
    if(!empty($_POST['dak_event_common_location_id'])) $dak_event_meta['dak_event_common_location_id'] = $_POST['dak_event_common_location_id'];
    if(!empty($_POST['dak_event_common_location_name'])) $dak_event_meta['dak_event_common_location_name'] = $_POST['dak_event_common_location_name'];
    if(!empty($_POST['dak_event_location_id'])) $dak_event_meta['dak_event_location_id'] = $_POST['dak_event_location_id'];
    if(!empty($_POST['dak_event_arranger_id'])) $dak_event_meta['dak_event_arranger_id'] = $_POST['dak_event_arranger_id'];
    if(!empty($_POST['dak_event_arranger_name'])) $dak_event_meta['dak_event_arranger_name'] = $_POST['dak_event_arranger_name'];
    if(!empty($_POST['dak_event_arranger_logo'])) $dak_event_meta['dak_event_arranger_logo'] = $_POST['dak_event_arranger_logo'];
    if(!empty($_POST['dak_event_arranger_description'])) $dak_event_meta['dak_event_arranger_description'] = $_POST['dak_event_arranger_description'];
    if(!empty($_POST['dak_event_festival_id'])) $dak_event_meta['dak_event_festival_id'] = $_POST['dak_event_festival_id'];
    if(!empty($_POST['dak_event_primary_picture'])) $dak_event_meta['dak_event_primary_picture'] = $_POST['dak_event_primary_picture'];
    if(!empty($_POST['dak_event_covercharge'])) $dak_event_meta['dak_event_covercharge'] = $_POST['dak_event_covercharge'];
    if(!empty($_POST['dak_event_age_limit'])) $dak_event_meta['dak_event_age_limit'] = $_POST['dak_event_age_limit'];
    if(!empty($_POST['dak_event_created_at'])) $dak_event_meta['dak_event_created_at'] = $_POST['dak_event_created_at'];
    if(!empty($_POST['dak_event_updated_at'])) $dak_event_meta['dak_event_updated_at'] = $_POST['dak_event_updated_at'];
    if(!empty($_POST['dak_event_arranger'])) $dak_event_meta['dak_event_arranger'] = $_POST['dak_event_arranger'];
    if(!empty($_POST['dak_event_categories'])) $dak_event_meta['dak_event_categories'] = $_POST['dak_event_categories'];
    if(!empty($_POST['dak_event_festival'])) $dak_event_meta['dak_event_festival'] = $_POST['dak_event_festival'];

    // Add values of $events_meta as custom fields
    foreach ($dak_event_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}


?>
