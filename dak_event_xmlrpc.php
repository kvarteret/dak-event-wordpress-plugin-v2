<?php
/*
 * xmlrpc methods to create custom post types remotely
 *
 * Author: Lisa Halvorsen, Snorre Davøen, Robin G. Aaberg
 * Version: 0.0000003
 */
require_once('eventsCalendarClient.php');
require_once('linticketCalendarClient.php');


    // Add method names here
$dak_event_xmlrpc_methods = array( 'dak_event_ping' => 'dak_event_ping');
$cacheType = 0;



/*
 *  Adding xml-rpc methods
 */     

function dak_event_add_xmlrpc_methods($methods) {
    error_log("Add xmlrpc methods");
    
    $methods['dak_event_ping'] = 'dak_event_ping';

    //global $dak_event_xmlrpc_methods;

    // foreach ($dak_event_xmlrpc_methods as $xmlrpc_method => $php_method) {
    //     $methods[$xmlrpc_method] = $php_method;
    // }
    //error_log(print_r($_POST, true));

    return $methods;

}

    
function dak_event_ping($args) {
    error_log("Running dak_event_ping");
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    # xmlrpc-arguments
    error_log(print_r($args, true));

    $username = $args[0];
    $password = $args[1];

    if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
        return $wp_xmlrpc_server->error;

    /*
     *  Functionality if authenticated and authorized:
     */

    $provider = $args[2];
    $type = $args[3];
    $arrangement = $args[4];
    $id = $args[5];

    if($arrangement == 'event') {
        if($type == 'update') {
            dak_event_updateEvent($id, $provider);
        } else if($type == 'delete') {
            dak_event_deleteEvent($id, $provider);
        }
    }
}

function dak_event_updateEvent($id, $provider, $payload = null) {
    $settings = get_option('dak_event_settings');

    $apiUrl = $settings['providers'][$provider]['server_url'];
    $class = $settings['providers'][$provider]['client_type'];

    $eventData = $payload;

    $client = new $class($apiUrl);

    if (empty($eventData)) {
        $response = $client->event($id);
    }
    $post_to_insert = array();

    # Check if post already exist
    $post_id = dak_event_findPostIdOfEvent($id, $provider);

    # Default wp post fields
    if (!empty($post_id)) {
        # Our wp post already exists
        $post_to_insert['ID'] = $post_id;
    }

    $meta_data_array = $client->translate($eventData);

    # We must remember to provide post type
    $post_to_insert['post_type'] = 'dak_event';
    # We must provide title and/or content
    $post_to_insert['post_title'] = $meta_data_array['dak_event_title'];
    $post_to_insert['post_content'] = $meta_data_array['dak_event_description'];
    $post_to_insert['post_excerpt'] = $meta_data_array['dak_event_lead_paragraph'];
    $post_to_insert['post_status'] = 'publish';

    $post_id = wp_insert_post($post_to_insert, true);

    if (is_wp_error($post_id)) {
        error_log("eventdata:" . print_r($eventData, true));
        error_log("post_id:" . $post_id->get_error_code() . " " . $post_id->get_error_message());
    } else {

        $meta_data_array['dak_event_provider'] = $provider;

        #Dak event meta-fields, remember that we need to prepend our namespace
        # for each key we use from the source

        error_log(print_r($meta_data_array, true));

        foreach($meta_data_array as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        // Set event categories
        $categories = $client->extractCategories($eventData);

        wp_set_post_terms($post_id, $categories, 'dak_event_category');


        if (!empty($meta_data_array['dak_event_primary_picture_url'])) {
            error_log("Will insert picture");

            $attachment_id = dak_event_get_image(
                $meta_data_array['dak_event_primary_picture_url'],
                (!empty($meta_data_array['dak_event_primary_picture_description']) ? $meta_data_array['dak_event_primary_picture_description'] : null)
            );


            if (is_wp_error($attachment_id)) {
                error_log($attachment_id->get_error_message());
            } else {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

    }
}

function dak_event_deleteEvent($id, $provider) {
    $post_id = dak_event_findPostIdOfEvent($id, $provider);

    if (!empty($post_id)) {
        if (has_post_thumbnail($post_id)) {
            wp_delete_attachment(get_post_thumbnail_id($post_id), true);
        }

        wp_delete_post($post_id);
    }
}

function dak_event_findPostIdOfEvent($id, $provider) {
    # Check if post already exist
    $posts = get_posts(array(
        'post_type' => 'dak_event',
        'post_status' => 'any', // this is important if you deal with drafted and public posts
        'meta_query' => array(
            array(
                 'key' => 'dak_event_id',
                 'value' => $id,
            ),
            array(
                 'key' => 'dak_event_provider',
                 'value' => $provider,
            )
        )
    ));

    $post_id = null;
    if (!empty($posts)) {
        $post_id = $posts[0]->ID;
    }

    return $post_id;
}

/**
 * Will purge the database of all events, must be called multiple times,
 * or you can call it with $limit = -1 to remove all posts at once
 */
function dak_event_purgeEvents($provider, $limit = 20) {
    $queryArgs = array(
        'posts_per_page' => $limit,
        'post_type' => 'dak_event',
        'post_status' => 'any', // this is important if you deal with drafted and public posts
        'meta_query' => array(
            array(
                'key' => 'dak_event_provider',
                'value' => $provider
            )
        )
    );


    $posts = get_posts($queryArgs);
    
    foreach($posts as $post) {
        error_log(sprintf("will delete post %d", $post->ID));

        if (has_post_thumbnail($post->ID)) {
            wp_delete_attachment(get_post_thumbnail_id($post->ID), true);
        }

        wp_delete_post($post->ID, true);
    }

    return array(
        'count' => count($posts),
        'limit' => $limit
    );
}

/**
 * Will import events from event database
 */
function dak_event_importEvents($provider, $offset = 0, $limit = 10) {
    global $cacheType;

    $settings = get_option('dak_event_settings');
    $apiUrl = $settings['providers'][$provider]['server_url'];
    $class = $settings['providers'][$provider]['client_type'];

    $client = new $class($apiUrl);

    $queryArgs = array(
        'noCurrentEvents' => 1
    );
    $events = $client->eventList($queryArgs, $limit, $offset);

    foreach ($events->data as $event) {
        $meta = $client->translate($event);
        dak_event_updateEvent($meta['dak_event_id'], $provider, $event);
    }

    return array(
        'offset' => $events->offset,
        'count' => $events->count,
        'limit' => $events->limit,
        'totalCount' => $events->totalCount
    );
}
