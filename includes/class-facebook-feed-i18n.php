<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/thomasnavarro
 * @since      1.0.0
 *
 * @package    Facebook_Feed
 * @subpackage Facebook_Feed/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Facebook_Feed
 * @subpackage Facebook_Feed/includes
 * @author     Zimages <thomas@zimages.fr>
 */

namespace Zimages\Wordpress\Plugins;

class Facebook_Feed_i18n
{


    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'facebook-feed',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
