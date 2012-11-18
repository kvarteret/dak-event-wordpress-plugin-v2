<?php
/*
 * xmlrpc methods to create custom post types remotely
 *
 * Author: Lisa Halvorsen, Snorre Davøen, Robin G. Aaberg
 * Version: 0.0000003
 */
    // Add method names here
    $dak_event_xmlrpc_methods = array( 'dak_event.update_event' => 'dak_event_update_event',
'dak_event.delete_event' => 'dak_event_delete_event',
    );

/*
 *  Adding xml-rpc methods
 */     

function dak_event_add_xmlrpc_methods($methods) {
    global $dak_event_xmlrpc_methods;

    foreach ($dak_event_xmlrpc_methods as $xmlrpc_method => $php_method) {
        $methods[$xmlrpc_method] = $php_method;
    }

    return $methods;

}

    
    function dak_event_update_event($args) {
        global $wp_xmlrpc_server;
        $wp_xmlrpc_server->escape( $args );
        # xmlrp
        
        $blog_id  = $args[0];
        $username = $args[1];
        $password = $args[2];

        if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
            return $wp_xmlrpc_server->error;

        /*
         *  Functionality if authenticated and authorized:
         */
        
        #Send me the Codez here

    }

    
    function dak_event_delete_event($args) {
        global $wp_xmlrpc_server;
        $wp_xmlrpc_server->escape( $args );
        # xmlrp
        
        $blog_id  = $args[0];
        $username = $args[1];
        $password = $args[2];

        if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
            return $wp_xmlrpc_server->error;

        /*
         *  Functionality if authenticated and authorized:
         */
        
        #Send me the Codez here

    }


?>