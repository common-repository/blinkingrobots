<?php
/**
 * This contains all CSS and JS files that will be enqueued
 *
 * @since      1.0.0
 */

namespace BlinkingRobots;


class Assets
{


    /**
     * Enqueue files
     *
     * @return void
     * @since 1.0.0
     */
    public static function enqueue()
    {

        //---- CSS
        add_action('admin_enqueue_scripts', __CLASS__.'::admin_styles', 9999);
        add_action('wp_enqueue_scripts', __CLASS__.'::frontend_styles', 100);

        //---- JS
        //add_action('admin_enqueue_scripts', __CLASS__.'::admin_scripts', 9999);
        // add_action('wp_enqueue_scripts', __CLASS__.'::frontend_scripts', 9999);
    }


    /**
     * Enqueue styles in admin area
     *
     * @return void
     * @since 1.0.0
     */
    public static function admin_styles()
    {
		// local enqueue.
        wp_enqueue_style(
            PREFIX,
            PLUGIN_URL.'/assets/css/admin.min.css',
            array(),
            PLUGIN_VERSION
        );

    }


    /**
     * Enqueue scripts in admin area
     *
     * @return void
     * @since 1.0.0
     */
    public static function admin_scripts()
    {
		// local enqueue.
        wp_enqueue_script(
            PREFIX,
            PLUGIN_URL.'/assets/js/admin.min.js',
            array('jquery'),
            PLUGIN_VERSION,
            true
        );
    }

	/**
     * Enqueue styles in frontend
     *
     * @return void
     * @since 1.0.0
     */
    public static function frontend_styles()
    {
		// local enqueue.
        wp_enqueue_style(
            PREFIX,
            PLUGIN_URL.'/assets/css/frontend.min.css',
            array(),
            PLUGIN_VERSION,
        );


        wp_localize_script(PREFIX, PREFIX, array(
            'ajax_url'      => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce(PREFIX),
            'theme_path'    => PLUGIN_URL,
            'theme_version' => PLUGIN_VERSION,
        ));
    }
	

    /**
     * Enqueue scripts in frontend
     *
     * @return void
     * @since 1.0.0
     */
    public static function frontend_scripts()
    {
		// local enqueue.
        wp_enqueue_script(
            PREFIX,
            PLUGIN_URL.'/assets/js/frontend.min.js',
            array('jquery'),
            PLUGIN_VERSION,
            true
        );


        wp_localize_script(PREFIX, PREFIX, array(
            'ajax_url'      => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce(PREFIX),
            'theme_path'    => PLUGIN_URL,
            'theme_version' => PLUGIN_VERSION,
        ));
    }

}
