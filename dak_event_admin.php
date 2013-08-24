<?php

function dak_event_admin_init() {
    //add_action('admin_menu', 'dak_event_admin_add_settings_page');
    register_setting(
        'dak_event_settings', // Option group
        'dak_event_settings', // option name
        'dak_event_settings_validate' // sanitizer/validator function
    );
    add_settings_section(
        'dak_event_settings_main', // id
        'DAK Event Settings', // title
        'dak_event_section_settings_text', // Function that fills the section with the desired content
        'dak_event_admin' // Which page these settings belong to?
    );
    add_settings_field(
        'providers', // id
        'List of providers', // title
        'dak_event_providers', // callback function that deals with content
        'dak_event_admin', // page
        'dak_event_settings_main' // which section do this field belong to?
    );

    add_action('admin_enqueue_scripts', 'dak_event_admin_scripts');
    add_action('wp_ajax_dak_event_import', 'dak_event_ajax_importEvents');
    add_action('wp_ajax_dak_event_purge', 'dak_event_ajax_purgeEvents');
}

/** Admin page stuff **/
function dak_event_admin_add_settings_page() {
    add_options_page(
        'DAK Events Settings',
        'DAK Events Settings',
        'manage_options',
        'dak_event_admin',
        'dak_event_admin_page'
    );
}

function dak_event_section_settings_text() {
    echo '<p>DAK Event Settings</p>';
}

function dak_event_admin_page() {
?>
<div class="wrap">
  <h2>DAK Event Settings</h2>
  <form action="options.php" method="post">
    <?php settings_fields('dak_event_settings'); ?>
    <?php do_settings_sections('dak_event_admin'); ?>

    <?php submit_button(); ?>
  </form>
  <p>
    <button type="button" class="button button-secondary" id="dak_event_purge">
      Purge
      <span class="msg"></span>
    </button>
    <button type="button" class="button button-secondary" id="dak_event_import">
      Import
      <span class="msg"></span>
    </button>
  </p>
</div>
<?php
}

function dak_event_providers() {
    $settings = get_option('dak_event_settings');
    error_log(print_r($settings, true));

    $providers = array();
    if (!empty($settings['providers'])) {
        $providers = $settings['providers'];
    }

    foreach ($providers as $nick => $provider) {
        ?>
        <div>
          <h4><?php echo $nick ?></h4>
          <label>Service url:
            <input type="text" size="60"
              name="dak_event_settings[providers][<?php echo $nick ?>][server_url]"
              value="<?php echo $provider['server_url'] ?>" />
          </label>
          <br />
          Service type: <select name="dak_event_settings[providers][<?php echo $nick ?>][client_type]">
            <option value="">Select a provider</option>
            <?php
            foreach ($GLOBALS['dak_event_provider_types'] as $class_name => $pretty_name) {
                $selected = '';
                if ($provider['client_type'] == $class_name) {
                    $selected = 'selected="selected"';
                }
                echo "<option ${selected} value=\"${class_name}\">${pretty_name}</option>\n";
            }
            ?>
          </select>
          <br />
          <label>Delete?
            <input type="checkbox"
              name="dak_event_settings[providers][<?php echo $nick ?>][delete]" value="true" />
          </label>
        </div>
        <?php
    }

    ?>
    <div>
      <h4>New provider:</h4>
      <label>Nick:
        <input type="text" size="10" name="dak_event_settings[new_provider][nick]" />
      </label>
      <br />
      <label>Service url:
        <input type="text" size="60" name="dak_event_settings[new_provider][server_url]" />
      </label>
      <br />
      Service type: <select name="dak_event_settings[new_provider][client_type]">
        <option value="">Select a provider</option>
        <?php
        foreach ($GLOBALS['dak_event_provider_types'] as $class_name => $pretty_name) {
            echo "<option value=\"${class_name}\">${pretty_name}</option>\n";
        }
        ?>
      </select>
    </div>
    <?php

}

function dak_event_settings_validate($input) {
    $old_settings = get_option('dak_event_settings');
    $settings = array(
        'providers' => array(),
    );

    // Validate existing providers
    foreach ($input['providers'] as $nick => $provider) {
        if (!empty($old_settings[$nick]) && $provider['delete'] !== "true") {
             if (empty($GLOBALS['dak_event_provider_types'][$provider['client_type']])) {
                 continue;
             }

             $settings['providers'][$nick] = array(
                 'server_url' => esc_url_raw($provider['server_url'], array('http', 'https')),
                 'client_type' => $provider['client_type'],
             );
        }
    }

    // Validate a possible new provider
    if (!empty($input['new_provider']['nick']) && !empty($input['new_provider']['server_url'])) {
        if (empty($GLOBALS['dak_event_provider_types'][$input['new_provider']['client_type']])) {
            continue;
        }

        $provider = array(
             'server_url' => esc_url_raw($input['new_provider']['server_url']),
             'client_type' => $input['new_provider']['client_type'],
        );

        $safe_nick = sanitize_key($input['new_provider']['nick']);

        if (!empty($safe_nick) && empty($settings['providers'][$safe_nick])) {
            $settings['providers'][$safe_nick] = $provider;
        }
    }

    return $settings;
}

/** AJAX-stuff **/
function dak_event_admin_scripts($hook) {
    wp_enqueue_script('ajax-script', plugins_url('/js/admin.js',  __FILE__),  array('jquery'));
}

function dak_event_ajax_importEvents() {
    $startTime = timer_stop();
    $retVal = array(
        'offset' => 0,
        'count' => 0,
        'limit' => 0,
        'totalCount' => 0,
        'runtime' => 0.0
    );

    if (isset($_POST['offset'])) {
        $retVal = dak_event_importEvents(intval($_POST['offset']), 10);
    }

    $endTime = timer_stop();
    $retVal['runtime'] = $endTime - $startTime;
    if (ob_get_length() > 0) {
		ob_clean(); // Remove previous outputs, ie. error messages
	}
    echo json_encode($retVal);

    die(); // this is required to return a proper result
}

function dak_event_ajax_purgeEvents() {
    error_log("elapsed time bf: " . timer_stop());

	if (ob_get_length() > 0) {
	    ob_clean(); // Remove previous outputs, ie. error messages
	}
    echo json_encode(dak_event_purgeEvents());
    
    error_log("elapsed time af: " . timer_stop());
    die();
}
