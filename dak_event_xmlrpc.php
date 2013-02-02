<?php
/*
 * xmlrpc methods to create custom post types remotely
 *
 * Author: Lisa Halvorsen, Snorre Davøen, Robin G. Aaberg
 * Version: 0.0000003
 */
require_once('eventsCalendarClient.php');


    // Add method names here
$dak_event_xmlrpc_methods = array( 'dak_event_ping' => 'dak_event_ping');
$apiUrl = 'http://localhost:8888/kvarteret_symfony_events/web';
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
    # xmlrp
    
    $username = $args[0];
    $password = $args[1];

    if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
        return $wp_xmlrpc_server->error;

    /*
     *  Functionality if authenticated and authorized:
     */

    $type = $args[2];
    $arrangement = $args[3];
    $id = $args[4];

    if($tarrangement == 'event') {
        if($type == 'update') {
            dak_event_updateEvent($id);
        } else if($type == 'delete') {
            dak_event_deleteEvent($id);
        }
    }
}

function dak_event_updateEvent($id) {

    global $apiUrl, $cacheType;

    $client = new eventsCalendarClient($apiUrl, null, $cacheType);
    $response = $client->event($id);
    $eventData = $response->data[0];
    # Check if post already exist
    $posts = get_posts(
        array(
            'meta_key' => 'dak_event_id',
            'meta_value' => $id,
            'meta_compare' => '==',
            'post_type' => 'dak_event' 
        )
    );
    $post_to_insert = array();
    $post_id;
    foreach($posts as $post) {
       $post_id = $post->id;
    }
    # Default wp post fields
    $post_to_insert['ID'] = $post_id;
    $post_to_insert['title'] = '';
    $post_to_insert['content'] = '';
    $post_to_insert['thumbnail'] = '';

    $post_id = wp_insert_post($post_to_insert);

    #Dak event meta-fields
    $meta_data_array = array();
    add_meta_to_post_array($eventData, $meta_data_array);

    foreach($post_to_insert as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    
    //$category_array = ();
    //wp_set_object_terms($post_id, $categories, 'dak_event', true);
}

function add_meta_to_post_array($object, $array, $prepend='') {
    foreach($object as $attrib => $value) {
        if(!is_object($value)) {
            $array[$prepend.$attrib] = $value;
        } elseif (is_array($object)) {
            # Nothing to do here
        } else {
            add_meta_to_post_array($value, $array, $attrib);
        }
    }
}

function dak_event_deleteEvent($id) {

}


?>