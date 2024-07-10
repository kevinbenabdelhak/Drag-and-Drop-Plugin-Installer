<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_upload_plugin_action', 'handle_plugin_upload');

function handle_plugin_upload() {
    check_ajax_referer('upload_plugin_nonce');

    if (current_user_can('install_plugins')) {
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $uploaded = wp_handle_upload($file, array('test_form' => false));

            if (isset($uploaded['file'])) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                $filename = $uploaded['file'];
                $zip = new ZipArchive;
                $res = $zip->open($filename);

                if ($res === TRUE) {
                    $plugin_slug = sanitize_file_name(basename($filename, '.zip'));
                    $extractPath = WP_PLUGIN_DIR . '/' . $plugin_slug;

                    if (!file_exists($extractPath)) {
                        mkdir($extractPath, 0777, true);
                    }
                    
                    if ($zip->extractTo($extractPath)) {
                        $zip->close();

                        $plugin_files = get_plugins('/' . $plugin_slug);
                        if (!empty($plugin_files)) {
                            $first_plugin_file = $plugin_slug . '/' . array_keys($plugin_files)[0];
                            wp_send_json_success(array('plugin_file' => $first_plugin_file));
                        } else {
                            wp_send_json_error(array('message' => 'Aucun fichier plugin trouvé après extraction.'));
                        }
                    } else {
                        wp_send_json_error(array('message' => 'Impossible d\'extraire l\'extension.'));
                    }
                } else {
                    wp_send_json_error(array('message' => 'Impossible d\'ouvrir l\'archive de l\'extension.'));
                }
            } else {
                wp_send_json_error(array('message' => 'Échec de l\'upload.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Aucun fichier uploadé.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Vous n\'avez pas la permission de télécharger des extensions.'));
    }
    wp_die();
}