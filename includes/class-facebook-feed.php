<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/thomasnavarro
 * @since      1.0.0
 *
 * @package    Facebook_Feed
 * @subpackage Facebook_Feed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Facebook_Feed
 * @subpackage Facebook_Feed/includes
 * @author     Zimages <thomas@zimages.fr>
 */

 namespace Zimages\Wordpress\Plugins;

class Facebook_Feed
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Facebook_Feed_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('FACEBOOK_FEED_VERSION')) {
            $this->version = FACEBOOK_FEED_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'facebook-feed';

        $this->load_dependencies();
        $this->set_locale();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-facebook-feed-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-facebook-feed-loader.php';

        $this->loader = new Facebook_Feed_Loader();
    }

    private function set_locale()
    {
        $plugin_i18n = new Facebook_Feed_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }

    public static function get_posts($fbid = '')
    {
        if (!empty($fbid)) {
            $transient_name = apply_filters('facebook-feed/posts_transient', 'facebook-'.$fbid, $fbid);
            $posts = get_transient($transient_name);

            if (!empty($posts) || false != $posts) {
                return $posts;
            }

            $parameters = [
                'access_token' => apply_filters('facebook-feed/parameters/access_token', ''),
                'locale' => apply_filters('facebook-feed/parameters/locale', 'fr_FR'),
                // 'since' => apply_filters('facebook-feed/parameters/since', date('d/m/Y')),
                'limit' => apply_filters('facebook-feed/parameters/limit', '10'),
                'fields' => implode(',', apply_filters('facebook-feed/parameters/fields', array('id', 'created_time', 'status_type', 'message', 'story', 'full_picture', 'permalink_url'))),
            ];

            $response = self::call_api($fbid, apply_filters('facebook-feed/parameters/api', $parameters));
            if ($response === false) {
                $expire = 60*60*12; // 12h
                set_transient($transient_name, $response, apply_filters('facebook-feed/posts_transient_lifetime', $expire));
                return;
            }

            $response = json_decode($response['body'], true);

            // FB graph API 2.x => 3.3 backwards comp
            if (isset($val['status_type']) && isset($val['permalink_url'])) {
                foreach ($response as $key => $val) {
                    $response['data'][ $key ]['type'] = $val['status_type'];
                    $response['data'][ $key ]['link'] = $val['permalink_url'];
                }
            }

            $response = apply_filters('facebook-feed/posts', $response);
            $expire = 60*60*12; // 12h
            set_transient($transient_name, $response, apply_filters('facebook-feed/posts_transient_lifetime', $expire));

            return $response;
        }

        return false;
    }

    private static function call_api($fbid = '', $parameters = array())
    {
        if (!empty($fbid) || !empty($parameters)) {
            $parameters = http_build_query($parameters);
            $response = wp_remote_get('https://graph.facebook.com/'.$fbid.'/feed/?'.$parameters);

            if (is_wp_error($response) || $response['response']['code'] !== 200) {
                self::write_log('response status code not 200 OK, fbid : '.$fbid);
                return false;
            }
            return $response;
        }

        return false;
    }

    private static function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}
