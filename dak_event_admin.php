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
		'dak_event_settings',
		'dak_event_settings',
		'dak_event_settings_validate'
	);
	add_settings_section(
		'dak_event_settings_main',
		'DAK Event Settings',
		'dak_event_section_text',
		'dak_event_admin'
	);
	add_settings_field(
		'server_url',
		'URL to event server',
		'dak_event_server_url',
		'dak_event_admin', 
		'dak_event_settings_main'
	);
}

function dak_event_section_text() {
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

	echo '<input id="dak_event_server_url" type="text" name="dak_event_settings[server_url]" value="' . $server_url . '" />';
}

function dak_event_settings_validate($input) {
	$new_input = array();
	
	$new_input['server_url'] = esc_url_raw($input['server_url']);

	return $new_input;
}
