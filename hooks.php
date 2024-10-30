<?php
/* Copyright 2016 Adam Bard, All rights reserved */

require(LINK_OPTIMIZER__PLUGIN_DIR . 'FlatRotation.php');
//

class LinkOptimizerHooks{
    public static function rotation_strategy($set_id)
    {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    rotation_strategy
                FROM
                    ". LINK_OPTIMIZER__SET_TABLE_NAME ."
                WHERE id=%d LIMIT 1",
                $set_id
            ), ARRAY_A
        );

        return intval($row['rotation_strategy']);
    }
    /**
     * Grep through $content to find [linkoptimizer:XX] tags,
     * and replace them with one of the links from the link set.
     */
    public static function replace_linkoptimizer_tags($content){
        global $wpdb;

        $result = preg_match_all("/\\[linkoptimizer:([0-9]+)\\]/", $content, $matches);

        if($matches){
            foreach($matches[1] as $set_id){

                //
                $rotator = new FlatRotation($set_id, true, false);
                //

                $link = $rotator->choose_link();


                // Build the redirector url
                $url = "?" . http_build_query(array(
                    'lo-lid' => $link['id']
                ));

                $link_el = "<a href=\"$url\" title=\"${link['link_title']}\">${link['link_text']}</a>";

                $replacement = str_replace("[linkoptimizer:$set_id]", $link_el, $content);
                if($replacement){
                    $content = $replacement;
                }
            }
        }
        return $content;
    }

    /**
     * Handle a redirect when a link id is present (usually an on-page-link click)
     */
    private static function redirect_by_link_id($link_id){
        global $wpdb;

        $url = $wpdb->get_var(
            $wpdb->prepare("SELECT link_url FROM " . LINK_OPTIMIZER__LINK_TABLE_NAME . " WHERE id=%d", $_GET['lo-lid'])
        );

        if($url){
            //

            wp_redirect($url, 302);
            exit;
        }
    }

    /**
     * Handle a redirect by set id (usually a shortlink)
     */
    private static function redirect_by_set_id($set_id){
        // Shortlinks are always flat rotations
        $rotator = new FlatRotation($set_id, false, true);
        $link = $rotator->choose_link();
        if($link){
            wp_redirect($link['link_url'], 302);
            exit;
        }
    }

    public static function handle_redirect(){
        global $wpdb;

        if($_GET['lo-lid']){
            LinkOptimizerHooks::redirect_by_link_id($_GET['lo-lid']);
        }

        if($_GET['lo-sid']){
            LinkOptimizerHooks::redirect_by_set_id($_GET['lo-sid']);
        }

        // Short link support
        $result = preg_match('|^/([^/]*)/?$|', $_SERVER['REQUEST_URI'], $match);
        if($match && $match[1]){
            $slug = $match[1];
            $set_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM "
                    . LINK_OPTIMIZER__SET_TABLE_NAME
                    . " WHERE slug=%s", $slug
                )
            );
            if($set_id !== null){
                LinkOptimizerHooks::redirect_by_set_id($set_id);
            }
        }
    }
}

add_filter('the_content', array('LinkOptimizerHooks', 'replace_linkoptimizer_tags'));
add_filter('widget_text', array('LinkOptimizerHooks', 'replace_linkoptimizer_tags'));

add_action('wp_loaded', array('LinkOptimizerHooks', 'handle_redirect'));