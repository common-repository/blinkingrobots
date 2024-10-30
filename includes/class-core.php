<?php
/**
 * Main class which sets all together
 *
 * @since      1.0.0
 */

namespace BlinkingRobots;


use Exception;

class Core
{

    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     * @throws Exception
     * @since     1.0.0
     *
     */
    public static function instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * @throws Exception
     * @since 1.0.0
     */
    public function __construct()
    {

        //autoload files from `/autoload`
        spl_autoload_register(__CLASS__.'::autoload');

        //include files from `/includes`
        self::includes();

        //enqueue css and js files
        Assets::enqueue();

        PostTypes::instance();

        Feed::instance();
		
        Routes::instance();
		
        API::instance();

        Fields::instance();
		
        Guide::instance();

        //if (wp_doing_ajax()) {
            //AJAX::instance();
        //}

    }


    /**
     * Include files
     *
     * @return void
     * @since 1.0.0
     */
    private static function includes()
    {

        if (COMPOSER) :
            include_once PLUGIN_DIR.'/vendor/autoload.php';
        endif;
    }


    /**
     * Check whether the required dependencies are met
     * also can show a notice message
     *
     * @param array $plugins - an array with `path => name` of the plugin
     * @param boolean $show_msg
     * @return boolean
     * @since 1.0.0
     */
    private static function has_dependency($plugins = array(), $show_msg = true)
    {

        if (empty($plugins)) {
            return true;
        }

        $valid          = true;
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (is_multisite()) {

            if (is_network_admin()) {

                $active_plugins          = [];
                $active_sitewide_plugins = get_site_option('active_sitewide_plugins');

                foreach ($active_sitewide_plugins as $path => $item) {
                    $active_plugins[] = $path;
                }

            } else {

                $active_plugins = get_blog_option(get_current_blog_id(), 'active_plugins');
            }
        }

        foreach ($plugins as $path => $name) {

            if (! in_array($path, $active_plugins)) {

                if ($show_msg) {
                    Utility::show_notice(sprintf(
                        __('%s plugin requires %s plugin to be installed and active.', 'blinkingrobots'),
                        '<b>Blinking Robots</b>',
                        "<b>{$name}</b>"
                    ), 'error');
                }

                $valid = false;
            }
        }

        return $valid;

    }


    /**
     * Init the action links available in plugins list page
     *
     * @return void
     * @since 1.0.0
     */
    public static function init_plugin_action_links()
    {

        //add plugin action and meta links
        self::plugin_links(array(
            'actions' => array(
                PLUGIN_SETTINGS_URL => __('Settings', 'blinkingrobots'),
                // admin_url('admin.php?page=wc-status&tab=logs') => __('Logs', 'blinkingrobots'),
                // admin_url('plugins.php?action='.PREFIX.'_check_updates') => __('Check for Updates', 'blinkingrobots')
            ),
            'meta'    => array(
                // '#1' => __('Docs', 'blinkingrobots'),
                // '#2' => __('Visit website', 'blinkingrobots')
            ),
        ));
    }


    /**
     * Add new gateway to WooCommerce payments
     *
     * @param array $gateways
     * @return array
     * @since 1.0.0
     */
    public static function add_payment_gateway($gateways)
    {

        $gateways[] = __NAMESPACE__.'\Gateway';

        return $gateways;
    }


    public static function autoload($filename)
    {

        $dir   = PLUGIN_DIR.'/autoload/class-*.php';
        $paths = glob($dir);

        //if (defined('GLOB_BRACE')) {
        //    $paths = glob('{'.$dir.'}', GLOB_BRACE);
        //}

        if (is_array($paths) && count($paths) > 0) {
            foreach ($paths as $file) {
                if (file_exists($file)) {
                    include_once $file;
                }
            }
        }
    }


    /**
     * Add plugin action and meta links
     *
     * @param array $sections
     * @return void
     * @since 1.0.0
     */
    private static function plugin_links($sections = array())
    {

        //actions
        if (isset($sections['actions'])) {

            $actions    = $sections['actions'];
            $links_hook = is_multisite() ? 'network_admin_plugin_action_links_' : 'plugin_action_links_';

            add_filter($links_hook.PLUGIN_BASENAME, function ($links) use ($actions) {

                foreach (array_reverse($actions) as $url => $label) {
                    $link = '<a href="'.$url.'">'.$label.'</a>';
                    array_unshift($links, $link);
                }

                return $links;

            });
        }

        //meta row
        if (isset($sections['meta'])) {

            $meta = $sections['meta'];

            add_filter('plugin_row_meta', function ($links, $file) use ($meta) {

                if (PLUGIN_BASENAME == $file) {

                    foreach ($meta as $url => $label) {
                        $link = '<a href="'.$url.'">'.$label.'</a>';
                        array_push($links, $link);
                    }
                }

                return $links;

            }, 10, 2);
        }

    }


    /**
     * Run on plugin activation
     *
     * @return void
     * @since 1.0.0
     */
    public static function on_activation()
    {

        if (version_compare(phpversion(), '7.0', '<')) {
            wp_die(sprintf(
                __('Hey! Your server must have at least PHP 7.0. Could you please upgrade. %sGo back%s', 'blinkingrobots'),
                '<a href="'.admin_url('plugins.php').'">',
                '</a>'
            ));
        }

        if (version_compare(get_bloginfo('version'), '5.0', '<')) {
            wp_die(sprintf(
                __('We need at least Wordpress 5.0. Could you please upgrade. %sGo back%s', 'blinkingrobots'),
                '<a href="'.admin_url('plugins.php').'">',
                '</a>'
            ));
        }
		
		// Create 32 digit length website key.
		add_option( PREFIX.'_website_key', substr(str_shuffle('01234567890123456789012345678901'), 0, 32) );
		
		delete_option('dont_show_guide');
		Guide::reset_steps_guide();
    }


    /**
     * Run on plugin deactivation
     *
     * @return void
     * @since 1.0.0
     */
    public static function on_deactivation()
    {
    }


    /**
     * Run when plugin is deleting
     *
     * @return void
     * @since 1.0.0
     */
    public static function on_uninstall()
    {

    }


}

Core::instance();
