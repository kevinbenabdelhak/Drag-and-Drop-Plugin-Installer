<?php
/**
 * Plugin Name: Drag & Drop Plugin Installer
 * Plugin URI: https://kevin-benabdelhak.fr/plugins/drag-and-drop-plugin-installer/
 * Description: Drag & Drop Plugin Installer permet d'installer et d'activuer une ou plusieurs extension WordPress facilement depuis n'importe quelle page de l'administration.
 * Author: Kevin BENABDELHAK
 * Author URI: https://kevin-benabdelhak.fr/a-propos/
 */

if (!defined('ABSPATH')) {
    exit;
}




if ( !class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$monUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kevinbenabdelhak/Drag-and-Drop-Plugin-Installer/', 
    __FILE__,
    'Drag-&-Drop-Plugin-Installer' 
);

$monUpdateChecker->setBranch('main');









// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/upload-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/activate-handler.php';