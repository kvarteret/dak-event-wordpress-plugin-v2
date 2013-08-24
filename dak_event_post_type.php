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

	register_taxonomy('dak_event_category', $post_type_namespace);
}

/* Adds a box to the main column on the Post and Page edit screens */
function dak_event_add_metaboxes() { 
    global $post_type_namespace;

    add_meta_box(
        "dak_event_id",
        __("id"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_provider",
        __("provider"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_url",
        __("url"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_ical",
        __("ical"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_linkout",
        __("linkout"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_start_date",
        __("startDate"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_start_time",
        __("startTime"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_end_date",
        __("endDate"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_end_time",
        __("endTime"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_custom_location",
        __("customLocation"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_common_location_id",
        __("commonLocation_id"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_common_location_name",
        __("commonLocation_name"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_location_id",
        __("location_id"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_arranger_id",
        __("arranger_id"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_arranger_name",
        __("arranger_name"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_arranger_logo",
        __("arranger_logo"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_arranger_description",
        __("arranger_description"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_festival_id",
        __("festival_id"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_primary_picture_url",
        __("primaryPicture url"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_primary_picture_description",
        __("primaryPicture description"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_covercharge",
        __("covercharge"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_age_limit",
        __("age_limit"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_created_at",
        __("created_at"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_updated_at",
        __("updated_at"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_arranger",
        __("arranger"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_categories",
        __("categories"), "dak_event_text_box",
        $post_type_namespace
    );

    add_meta_box(
        "dak_event_festival",
        __("festival"), "dak_event_text_box",
        $post_type_namespace
    );
}

function dak_event_text_box($post, $metabox) {
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, $metabox['id'], true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="' . $metabox['id'] . '" value="'.$meta.'" />';
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
