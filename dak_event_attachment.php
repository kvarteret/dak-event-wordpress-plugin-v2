<?php


/**
 * This file adds one meta data box to wordpress' attachment post type
 * and a few utilities.
 */


add_action( 'add_meta_boxes', 'dak_event_attachment_meta_boxes');
add_action( 'add_attachment', 'dak_event_save_attachment');
add_action( 'edit_attachment', 'dak_event_save_attachment');

$max_image_size_temp = 1.25 * 1024 * 1024;
define('DAK_MAX_IMAGE_SIZE', $max_image_size_temp);

function dak_event_attachment_meta_boxes() {
	add_meta_box(
		'dak_event_image_source',
		__('image source'),
		'dak_event_image_source',
		'attachment'
	);
}

function dak_event_image_source($attachment) {
	wp_nonce_field(plugin_basename(__FILE__), 'dak_event_nonce_image_source');
	$image_source = get_post_meta($attachment->ID, 'dak_event_image_source', true);
	echo '<input type="text" name="dak_event_image_source" value="'. esc_url($image_source) .'" />';
}

function dak_event_save_attachment($attachment_id) {
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (!isset($_POST['dak_event_nonce_image_source']) || !wp_verify_nonce( $_POST['dak_event_nonce_image_source'], plugin_basename(__FILE__) )) {
		return $attachment_id;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $attachment_id )) {
		return $attachment_id;
	}

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.

	error_log("running");

	$image_source = null;
	if (!empty($_POST['dak_event_image_source'])) {
		$image_source = esc_url_raw($_POST['dak_event_image_source']);
	}

	if (!empty($image_source)) {
		update_post_meta($attachment_id, 'dak_event_image_source', $image_source);
	} else {
		delete_post_meta($attachment_id, 'dak_event_image_source');
	}
}

/**
 * Will delete an attachment with dak_event_image_source set to the specified URL.
 *
 * @param url The original source URL of the image
 * @return true if succesful delete
 */
function dak_event_delete_attachment($url) {
	$posts = get_posts(
		array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'meta_key' => 'dak_event_image_source',
			'meta_value' => $url
		)
	);

	if (!empty($posts)) {
		foreach ($posts as $p) {
			wp_delete_attachment($p->ID, true);
		}

		return true;
	}

	return false;
}

/**
 * Downloads and stores an image from DAK Event System.
 * It will add a metadata field containing the image source.
 * This way it gets easier to search for images used by the Event system
 * and also delete them. It will try to search for images used multiple times.
 *
 * @param object image_object an object containing at least a url field and a description field
 * @param int max_image_size Maximum allowed file size of an image
 * @returns id|WP_Error id on success
 */
function dak_event_get_image($image_url, $desc = null, $max_image_size=0) {
	if ($max_image_size <= 0) {
		$max_image_size = DAK_MAX_IMAGE_SIZE;
	}

	$attachments = get_posts(
		array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'meta_key' => 'dak_event_image_source',
			'meta_value' => $image_url
		)
	);

	error_log(print_r($attachments, true));

	if (empty($attachments)) {
		$id = dak_media_sideload_image($image_url, null, $desc, $max_image_size);
		if (is_wp_error($id)) {
			return $id;
		}

		update_post_meta($id, 'dak_event_image_source', $image_url);
		return $id;
	} else {
		return $attachments[0]->ID;
	}
}

/**
 * Download an image from the specified URL and attach it to a post.
 * Modified version of media_sideload_image
 *
 * @since 2.6.0
 *
 * @param string $file The URL of the image to download
 * @param int $post_id The post ID the media is to be associated with
 * @param string $desc Optional. Description of the image
 * @param int max_image_size Maximum allowed file size of an image
 * @return id|WP_Error id on success
 */
function dak_media_sideload_image($file, $post_id, $desc = null, $max_image_size = 0) {
	global $post_type_namespace;

	if ($max_image_size <= 0) {
		$max_image_size = DAK_MAX_IMAGE_SIZE;
	}

	if ( ! empty($file) ) {
		// We will ask the file's webserver for the size of the file
		// in order to not download it if it is too large
		$fileHeader = wp_remote_head($file);

		if (is_wp_error($fileHeader)) {
			error_log($fileHeader->get_error_code() . ' ' . $fileHeader->get_error_message());
		}

		if (!empty($fileHeader['headers']['content-length']) &&
			(intval($fileHeader['headers']['content-length']) > $max_image_size)) {
			return new WP_Error('image_too_big', 'Image file size is too big');
		}

		// Download file to temp location
		$tmp = download_url( $file );

		// Set variables for storage
		// fix file filename for query strings
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array['name'] = $post_type_namespace . '-' . basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		if (filesize($file_array['tmp_name']) > $max_image_size) {
			unlink($file_array['tmp_name']);
			return new WP_Error('image_too_big', 'Image file size is too big');
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );
		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return $id;
		}

		return $id;
	} else {
		new WP_Error('missing_parameters', 'Missing parameters');
	}
}
