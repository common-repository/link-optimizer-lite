<?php
/* Copyright 2016 Adam Bard, All rights reserved */

class LinkOptimizerAdmin {

    public static function init(){

        LinkOptimizerAdmin::init_menu();
    }

    public static function init_menu(){
        add_plugins_page(
            'Link Optimizer Lite', // Page Title
            'Link Optimizer Lite', // Menu Title
            'manage_options', // Capability
            'link-optimizer',
            array('LinkOptimizerAdmin', 'admin_page'));
    }

    public static function admin_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] === 'LinkOptimizer::save'){
            $result = LinkOptimizerAdmin::handle_save();
            if($result){
                LinkOptimizerAdmin::list_page();
            }else{
                LinkOptimizerAdmin::edit_page();
            }
        }elseif($_GET['action'] == 'edit'){
            LinkOptimizerAdmin::edit_page();
        }else{
            LinkOptimizerAdmin::list_page();
        }
    }

    private static function get_sets(){
        global $wpdb;

        $q = "SELECT * FROM " . LINK_OPTIMIZER__SET_TABLE_NAME;
        $result = $wpdb->get_results($q, ARRAY_A);

        $sets = array();
        foreach($result as $set){
            $set['links'] = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . LINK_OPTIMIZER__LINK_TABLE_NAME
                    . " WHERE set_id=%d", $set['id']
                ), ARRAY_A
            );

            $sets[] = $set;
        }

        return $sets;
    }

    public static function list_page(){
        $sets = LinkOptimizerAdmin::get_sets();
        $ctx = array('sets' => $sets);
        include(LINK_OPTIMIZER__PLUGIN_DIR. 'tpl/list.html.php');
    }

    public static function edit_page(){

        $sets = LinkOptimizerAdmin::get_sets();
        $ctx = array('sets' => $sets);

        $serialized = json_encode($ctx);

        include(LINK_OPTIMIZER__PLUGIN_DIR. 'tpl/admin.html.php');
    }

    public static function validate_data($data){
        return $data;
    }

    public static function handle_save(){
        global $wpdb;

        $data = json_decode(stripslashes($_POST['serialized-data']), true);
        $data = LinkOptimizerAdmin::validate_data($data);
        if($data === null){
            add_settings_error(
                'LinkOptimizerAdmin::handle_save',
                esc_attr('error'),
                "Error updating links: data could not be deserialized",
                "error"
            );
            return false;
        }

        $link_ids = array();
        $set_ids = array();

        // First pass: Grab ids so we can delete unused ones.
        foreach($data['sets'] as $set){
            if($set['id']){
                $set_ids[] = esc_sql($set['id']);
            }

            foreach($set['links'] as $link){
                if($link['id']){
                    $link_ids[] = esc_sql($link['id']);
                }
            }
        }

        // Clean up deleted stuff.


        if(count($link_ids) > 0){
            $link_id_clause = implode(',', $link_ids);
            $wpdb->query(
                "DELETE FROM " . LINK_OPTIMIZER__LINK_TABLE_NAME
                . " WHERE id NOT IN ($link_id_clause)"
            );
        }else{
            $wpdb->query(
                "DELETE FROM " . LINK_OPTIMIZER__LINK_TABLE_NAME
            );
        }

        if(count($set_ids) > 0){
            $set_id_clause = implode(',', $set_ids);
            $wpdb->query(
                "DELETE FROM " . LINK_OPTIMIZER__SET_TABLE_NAME
                . " WHERE id NOT IN ($set_id_clause)"
            );
        }else{
            $wpdb->query(
                "DELETE FROM " . LINK_OPTIMIZER__SET_TABLE_NAME
            );
        }

        // Next pass: add
        foreach($data['sets'] as $set){
            // Special case for rotation strategy -- this param is not present
            // on lite.
            $rotation_strategy = $set['rotation_strategy'];
            if($rotation_strategy === null){
                $rotation_strategy = LINK_OPTIMIZER__ROTATION_STRATEGY__FLAT;
            }

            $wpdb->replace(
                LINK_OPTIMIZER__SET_TABLE_NAME,
                array(
                    'id' => $set['id'],
                    'name' => $set['name'],
                    'slug' => $set['slug'],
                    'rotation_strategy' => $rotation_strategy
                )
            );

            $set_id = $wpdb->insert_id;

            foreach($set['links'] as $link){
                // Can't just replace, or we'll wipe out hits/clicks
                if($link['id']){
                    $wpdb->update(
                        LINK_OPTIMIZER__LINK_TABLE_NAME,
                        array(
                            'link_text' => $link['link_text'],
                            'link_url' => $link['link_url'],
                            'link_title' => $link['link_title']
                        ), array(
                            'id' => $link['id']
                        )
                    );
                }else{
                    $insert = $wpdb->insert(
                        LINK_OPTIMIZER__LINK_TABLE_NAME,
                        array(
                            'set_id' => $set_id,
                            'link_text' => $link['link_text'],
                            'link_url' => $link['link_url'],
                            'link_title' => $link['link_title']
                        )
                    );
                }
            }
        }

        add_settings_error(
            '',
            esc_attr(''),
            "Your links have been updated",
            "updated"
        );

        return true;
    }
}

add_action('admin_menu', array('LinkOptimizerAdmin', 'init'));