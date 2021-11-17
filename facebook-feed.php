<?php

/**
 * Plugin Name:       Facebook Feed
 * Description:       Get latest latest posts from Facebook profile or page.
 * Version:           1.0.0
 * Author:            Zimages
 * Author URI:        https://github.com/thomasnavarro
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       facebook-feed
 * Domain Path:       /languages
 */

namespace Zimages\Wordpress\Plugins;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('FACEBOOK_FEED_VERSION', '1.0.0');

require plugin_dir_path(__FILE__) . 'includes/class-facebook-feed.php';

function run_facebook_feed()
{
    $plugin = new Facebook_Feed();
    $plugin->run();
}

run_facebook_feed();
