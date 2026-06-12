<?php

if (!defined('ABSPATH')) exit;

class Altus_Settings {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Add settings page under "Settings"
     */
    public function add_menu() {

        add_options_page(
            'Altus Audit Settings',
            'Altus Audit',
            'manage_options',
            'altus-audit-settings',
            [$this, 'settings_page']
        );
    }

    /**
     * Register setting (this is what saves to wp_options)
     */
    public function register_settings() {

        register_setting(
            'altus_settings_group',
            'altus_api_key',
            [
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            ]
        );

        add_settings_section(
            'altus_main_section',
            'API Configuration',
            null,
            'altus-audit-settings'
        );

        add_settings_field(
            'altus_api_key_field',
            'Google PageSpeed API Key',
            [$this, 'api_key_field'],
            'altus-audit-settings',
            'altus_main_section'
        );
    }

    /**
     * Input field (ONLY ONE — prevents duplication)
     */
    public function api_key_field() {

        $value = get_option('altus_api_key');
        ?>

        <input type="text"
               name="altus_api_key"
               value="<?php echo esc_attr($value); ?>"
               style="width:400px;"
               placeholder="Enter Google API Key">

        <?php
    }

    /**
     * Settings page UI
     */
    public function settings_page() {
        ?>

        <div class="wrap">
            <h1>Altus Audit Settings</h1>

            <form method="post" action="options.php">

                <?php
                settings_fields('altus_settings_group');
                do_settings_sections('altus-audit-settings');
                submit_button('Save API Key');
                ?>

            </form>
        </div>

        <?php
    }
}