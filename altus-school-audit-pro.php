<?php

/**
 * Plugin Name: Altus Audit Tool
 * Description: Google PageSpeed audit tool with branded reports for schools.
 * Version: 1.0.0
 * Author: Altus Web Solutions
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

define('ASAP_PATH', plugin_dir_path(__FILE__));
define('ASAP_URL', plugin_dir_url(__FILE__));

/**
 * LOAD FILES
 */
require_once ASAP_PATH . 'includes/class-leads.php';
require_once ASAP_PATH . 'includes/class-settings.php';
require_once ASAP_PATH . 'includes/class-admin.php';
require_once ASAP_PATH . 'includes/class-api.php';
require_once ASAP_PATH . 'includes/class-shortcode.php';

/**
 * ACTIVATE PLUGIN
 */
register_activation_hook(__FILE__, function () {
    if (class_exists('Altus_Leads')) {
        Altus_Leads::create_table();
    }
});

/**
 * INIT PLUGIN
 */
add_action('plugins_loaded', function () {

    new Altus_Settings();
    new Altus_Admin();
    new Altus_Shortcode();

});