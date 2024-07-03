<?php

add_action('wp_ajax_blocksy_dismissed_notice_handler', function () {
	update_option('dismissed-blocksy_plugin_notice', true);
	wp_die();
});

add_action(
	'wp_ajax_blocksy_dismissed_blocks_move_notice_handler',
	function () {
		update_option('dismissed-blocksy_blocks_drop_notice', true);
		wp_die();
	}
);

add_action('wp_ajax_blocksy_notice_button_click', function () {
	if (
		! current_user_can('activate_plugins')
		||
		! isset($_REQUEST['nonce'])
		||
		! wp_verify_nonce($_REQUEST['nonce'], 'ct-ajax-nonce')
	) {
		wp_send_json_error();
	}

	remove_action(
		'upgrader_process_complete',
		['Language_Pack_Upgrader', 'async_upgrade'],
		20
	);

	$manager = new Blocksy_Plugin_Manager();
	$status_descriptor = $manager->get_companion_status();

	if ($status_descriptor['status'] === 'active') {
		wp_send_json_success([
			'status' => 'active',
			'pluginUrl' => admin_url('admin.php?page=ct-dashboard')
		]);
	}

	if ($status_descriptor['status'] === 'uninstalled') {
		$manager->download_and_install($status_descriptor['slug']);
		$manager->plugin_activation($status_descriptor['slug']);

		wp_send_json_success([
			'status' => 'active',
			'pluginUrl' => admin_url('admin.php?page=ct-dashboard')
		]);
	}

	if ($status_descriptor['status'] === 'installed') {
		$manager->plugin_activation($status_descriptor['slug']);

		wp_send_json_success([
			'status' => 'active',
			'pluginUrl' => admin_url('admin.php?page=ct-dashboard')
		]);
	}

	wp_die();
});

