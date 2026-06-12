<?php

if (!defined('ABSPATH')) exit;

class Altus_Leads {

    public static function create_table() {
        global $wpdb;

        $table = $wpdb->prefix . "altus_leads";

        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id INT AUTO_INCREMENT,
            name VARCHAR(255),
            email VARCHAR(255),
            website TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}