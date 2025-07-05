<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_activate_plugins_action', 'handle_plugins_activation');

function handle_plugins_activation() {
    check_ajax_referer('activate_plugins_nonce');

    if (current_user_can('activate_plugins')) {
        $plugin_files = json_decode(stripslashes($_POST['plugins']), true);
        
        foreach ($plugin_files as $plugin_file) {
            $activate = activate_plugin($plugin_file);
            if (is_wp_error($activate)) {
                wp_send_json_error(array(
                    'message' => 'Échec de l\'activation de l\'extension: ' . $activate->get_error_message()
                ));
            }
        }
        
        wp_send_json_success(array('message' => 'Toutes les extensions ont été activées avec succès.'));
    } else {
        wp_send_json_error(array('message' => 'Vous n\'avez pas la permission d\'activer des extensions.'));
    }
    wp_die();
}