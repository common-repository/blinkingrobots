<?php
/**
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

namespace BlinkingRobots;

class AJAX
{

    /**
     * Fields constructor.
     */
    function __construct()
    {

        /**
         * @example request handler
         */
        add_action('wp_ajax_handle_ping', __CLASS__.'::handle_ping');
        add_action('wp_ajax_nopriv_handle_ping', __CLASS__.'::handle_ping');

    }

	/**
     * Debug using function.
     */
    public static function handle_ping()
    {

        if (! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), PREFIX) ) {
            echo wp_json_encode([
                'notification' => esc_html__('403. Nonce is not verified.', 'blinkingrobots')
            ]);
            wp_die();
        }

        /**
         * Handle request
         */
        $response = sanitize_post($_POST, 'raw');

        // code goes here

        /**
         * Response
         */
        echo esc_js( json_encode($response) );
        wp_die();
    }

    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
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

}


