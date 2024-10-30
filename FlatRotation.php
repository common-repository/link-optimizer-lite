<?php
/* Copyright 2016 Adam Bard, all rights reserved */

require_once(LINK_OPTIMIZER__PLUGIN_DIR . 'Rotation.php');

class FlatRotation implements Rotation {
    private $set_id;
    private $record_hit;
    private $record_shortlink_follow;

    public function __construct($set_id, $record_hit, $record_shortlink_follow){
        $this->set_id = $set_id;
        $this->record_hit = $record_hit;
        $this->record_shortlink_follow = $record_shortlink_follow;
    }

    public function choose_link(){
        global $wpdb;

        $link = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    id,
                    link_text,
                    link_url,
                    link_title,
                    hits,
                    clicks / hits AS ratio
                FROM
                    ". LINK_OPTIMIZER__LINK_TABLE_NAME ."
                WHERE set_id=%d
                ORDER BY RAND() DESC LIMIT 1", $this->set_id
            ), ARRAY_A
        );

        // 

        return $link;
    }
}