<?php
$post_type_namespace = "dak_event";

/**
 * Fields that are supposed to be saved
 * $dak_event_storage_fields = array();
 *
 * Meta fields that are not to be saved as a meta box, generally because they
 * do not need to be saved or because they are stored somewhere else.
 * Eg. in the actual post (title, excerpt and content)
 * $dak_event_non_storage_fields = array();
 *
 * All fields - storage and non-storage
 * $dak_event_all_fields = array();
 */

// Fields that are supposed to be saved
$dak_event_storage_fields = array(
	// Structure is: field name, editor callback
	"dak_event_id" => dak_event_make_field("int"),
	"dak_event_provider" => dak_event_make_field(),

	"dak_event_url" => dak_event_make_field(),
	"dak_event_ical" => dak_event_make_field(),
	"dak_event_linkout" => dak_event_make_field(),

	"dak_event_start_date" => dak_event_make_field(),
	"dak_event_start_time" => dak_event_make_field(),
	"dak_event_end_date" => dak_event_make_field(),
	"dak_event_end_time" => dak_event_make_field(),

	"dak_event_start_datetime" => dak_event_make_field(),
	"dak_event_end_datetime" => dak_event_make_field(),

	"dak_event_custom_location" => dak_event_make_field(),
	"dak_event_common_location_id" => dak_event_make_field("int"),
	"dak_event_common_location_name" => dak_event_make_field(),

	"dak_event_arranger_id" => dak_event_make_field("int"),
	"dak_event_arranger_name" => dak_event_make_field(),
	"dak_event_arranger_logo" => dak_event_make_field(),
	"dak_event_arranger_description" => dak_event_make_field(),

	"dak_event_festival" => dak_event_make_field(),
	"dak_event_festival_id" => dak_event_make_field("int"),

	"dak_event_primary_picture_url" => dak_event_make_field(),
	"dak_event_primary_picture_description" => dak_event_make_field(),

	"dak_event_covercharge" => dak_event_make_field(),
	"dak_event_age_limit" => dak_event_make_field(),

	"dak_event_categories" => dak_event_make_field(),

	"dak_event_created_at" => dak_event_make_field(),
	"dak_event_updated_at" => dak_event_make_field(),
);

// Meta fields that are not to be saved as a meta box, generally because they
// do not need to be saved or because they are stored somewhere else.
// Eg. in the actual post (title, excerpt and content)
$dak_event_non_storage_fields = array(
	"dak_event_title",
	"dak_event_excerpt",
	"dak_event_content"
);

// All fields
$dak_event_all_fields = array_merge(
	array_keys($dak_event_storage_fields),
	$dak_event_non_storage_fields
);

function dak_event_make_field($type = "text", $editor_callback = "dak_event_text_box") {
	return array(
		'editor_callback' => $editor_callback,
		'type' => $type
	);
}

/**
 * Converts data coming from the database to its intended type
 */
function dak_event_convert_data(array $dataCollection = array()) {
	global $dak_event_storage_fields;
	$converted = array();

	foreach($dataCollection as $data) {
		$key = $data['key'];

		if (isset($dak_event_storage_fields[$key])) {
			$type = $dak_event_storage_fields[$key]['type'];

			if ($type == 'int') {
				$data['value'] = intval($data['value']);
			}
		}

		$converted[] = $data;
	}

	return $converted;
}

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
	global $dak_event_storage_fields;

	foreach ($dak_event_storage_fields as $field_name => $field_data) {
		if ($field_data['editor_callback'] == null) {
			continue;
		}

		add_meta_box(
			$field_name,
			__($field_name),
			$field_data['editor_callback'],
			$post_type_namespace
		);
	}
}

function dak_event_text_box($post, $metabox) {
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );
    $meta = get_post_meta($post->ID, $metabox['id'], true);
    echo '<input type="hidden" name="meta_noncename" value="'.$nonce.'" />';
    echo '<input type="text" name="' . $metabox['id'] . '" value="'.$meta.'" />';
}

// Method hijacked from Devin @ http://wptheming.com/2010/08/custom-metabox-for-post-type/ 
function dak_event_save_post_meta($post_id, $post) {
	global $dak_event_storage_fields;

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
    $dak_event_meta = array();

	foreach (array_keys($dak_event_storage_fields) as $field_name) {
		if (!empty($_POST[$field_name])) {
			$dak_event_meta[$field_name] = $_POST[$field_name];
		}
	}

	// Add values of $dak_events_meta as custom fields
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
