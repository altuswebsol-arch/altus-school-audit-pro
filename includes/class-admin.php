<?php

if (!defined('ABSPATH')) exit;

class Altus_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu() {

        add_menu_page(
            'Altus Audit',
            'Altus Audit',
            'manage_options',
            'altus-audit',
            [$this, 'dashboard'],
            'dashicons-chart-area',
            2
        );

        add_submenu_page(
            'altus-audit',
            'Settings',
            'Settings',
            'manage_options',
            'altus-settings',
            [$this, 'settings']
        );
    }

    public function dashboard() {
        echo '<div class="wrap">';
        echo '<h1>Altus Audit</h1>';
        echo '<p><b>Status:</b> Plugin Active</p>';
        echo '<p>Go to Settings → Add API Key</p>';
        echo '</div>';
    }

    public function settings() {

        ?>
        <div class="wrap">
            <h1>Altus Settings</h1>

            <form method="post" action="options.php">

                <?php settings_fields('altus_settings_group'); ?>

                <table class="form-table">

                    <tr>
                        <th>Google PageSpeed API Key</th>
                        <td>
                            <input type="text"
                                   name="altus_api_key"
                                   value="<?php echo esc_attr(get_option('altus_api_key')); ?>"
                                   class="regular-text">
                        </td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }
}