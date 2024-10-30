<?php
/* Copyright 2016 Adam Bard, All rights reserved */

class LinkOptimizerInstall {
    /**
    * Initialize database
    */
    public static function init_db(){
        global $wpdb;

        $set_table_name = LINK_OPTIMIZER__SET_TABLE_NAME;
        $link_table_name = LINK_OPTIMIZER__LINK_TABLE_NAME;

        $charset_collate = '';

        if ( ! empty( $wpdb->charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if ( ! empty( $wpdb->collate ) ) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta("CREATE TABLE $set_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            slug text NOT NULL,
            rotation_strategy integer DEFAULT 1 NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;");

        dbDelta("CREATE TABLE $link_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            set_id mediumint(9) NOT NULL,
            link_text text NOT NULL,
            link_url text NOT NULL,
            link_title text NOT NULL,
            hits integer DEFAULT 1 NOT NULL,
            clicks integer DEFAULT 1 NOT NULL,
            shortlink_follows integer DEFAULT 0 NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;");

        add_option( "lo_db_version", "1.2" );
    }
}

register_activation_hook( __FILE__, array('LinkOptimizerInstall', 'init_db' ));
add_action( 'plugins_loaded',  array('LinkOptimizerInstall', 'init_db' ) );
