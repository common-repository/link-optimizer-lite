<?php
/**
 * @package Link Optimizer Lite
 * @version 1.4.5
 */
/*
Plugin Name: Link Optimizer Lite
Plugin URI: http://refinry.com/affiliate-link-optimizer/

Description: Rotate, cloak, and shortlink-ify your affiliate (or any) links. Upgrade to Link Optimizer at any time to run automatic conversion split tests using a multi-armed bandit algorithm -- just set up your links, site back, and watch as the most converting variants are automatically selected for display!

Author: Adam Bard
Version: 1.4.5
Author URI: http://refinry.com/affiliate-link-optimizer/
*/

/* Copyright 2016 Adam Bard, All rights reserved */

define('LINK_OPTIMIZER__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LINK_OPTIMIZER__SET_TABLE_NAME', $wpdb->prefix . 'linkoptimizer_linksets');
define('LINK_OPTIMIZER__LINK_TABLE_NAME', $wpdb->prefix . 'linkoptimizer_links');
define('LINK_OPTIMIZER__ROTATION_STRATEGY__FLAT', 0);
define('LINK_OPTIMIZER__ROTATION_STRATEGY__EGB', 1);

require_once(LINK_OPTIMIZER__PLUGIN_DIR . 'install.php');
require_once(LINK_OPTIMIZER__PLUGIN_DIR . 'hooks.php');

if(is_admin()){
    require_once(LINK_OPTIMIZER__PLUGIN_DIR . 'admin.php');
}


?>