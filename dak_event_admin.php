<?php

function dak_event_admin_add_settings_page() {
	add_options_page(
		'DAK Events Settings',
		'DAK Events Settings',
		'manage_options',
		'dak_event_admin',
		'dak_event_admin_page'
	);
}

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
		'server_url', // id
		'URL to event server', // title
		'dak_event_server_url', // callback function that deals with content
		'dak_event_admin', // page
		'dak_event_settings_main' // which section do this field belong to?
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
</div>
<?php
}

function dak_event_server_url() {
	$settings = get_option('dak_event_settings');
	error_log(print_r($settings, true));

	$server_url = '';
	if (!empty($settings['server_url'])) {
		$server_url = $settings['server_url'];
	}

	echo '<input id="dak_event_server_url" type="text" size="60" name="dak_event_settings[server_url]" value="' . $server_url . '" />';
}

function dak_event_settings_validate($input) {
    $new_input = array();

    $new_input['server_url'] = esc_url_raw($input['server_url']);

    return $new_input;
}
