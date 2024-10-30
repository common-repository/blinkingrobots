<?php
/**
 * This is responsible for processing AJAX or other requests
 *
 * @since 1.0.0
 */

namespace BlinkingRobots;


//prevent direct access data leaks
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') || exit;


class Routes
{

    private static $root = '';


    function __construct()
    {

        self::$root = get_bloginfo('url').'/wp-json/'.PREFIX.'/v1/';

        add_action('rest_api_init', __CLASS__.'::register_routes');
		
    }

    public static function get_path($endpoint)
    {
        return self::$root.trim($endpoint, '/');
    }


	/**
     * Register Routes.
     *
     * @since 1.0.0
     */
    public static function register_routes()
    {
		register_rest_route(PREFIX.'/v1', '/post_article', array(
            'methods'  => WP_REST_Server::ALLMETHODS,
            'callback' => __CLASS__.'::save_article',
			'permission_callback' => '__return_true',
        ));

		register_rest_route(PREFIX.'/v1', '/sync_feeds', array(
            'methods'  => WP_REST_Server::ALLMETHODS,
            'callback' => __CLASS__.'::sync_feeds',
			'permission_callback' => '__return_true',
        ));

		register_rest_route(PREFIX.'/v1', '/notices_feed', array(
            'methods'  => WP_REST_Server::ALLMETHODS,
            'callback' => __CLASS__.'::notices_feed',
			'permission_callback' => '__return_true',
        ));

		 register_rest_route(PREFIX.'/v1', '/subscription', array(
            'methods'  => WP_REST_Server::ALLMETHODS,
            'callback' => __CLASS__.'::update_subscription',
			'permission_callback' => '__return_true',
        ));
    }

    /**
     * Create post from requested data.
     *
     * @param object $request
     *
     * @return object WP_REST_Response
     * @return object WP_Error
     */
    public static function save_article($request)
    {

        $parameters = $request->get_params();
		
		$website_key = get_option( PREFIX.'_website_key' );

        if ( empty($parameters['website_key']) ) :
			return new WP_Error('missed_authToken', 'Missed authToken param', array('status' => 400));
        endif;

		if ( $parameters['website_key'] != $website_key ) :
			return new WP_Error('missed_authToken', 'Wrong value authToken param', array('status' => 400));
		endif;
		
		if ( empty($parameters['post_id']) ) :
			return new WP_Error('missed_param', 'Missed feed param: post_id', array('status' => 400));
		endif;

		if ( !empty($parameters['errors']) ) :
			$errors = new WP_Error('server_response', $parameters['errors']);
			update_post_meta( $parameters['post_id'], 'errors', $errors );
			return new WP_REST_Response([ 'status' => 'success' ], 200);
		else :
			delete_post_meta( $parameters['post_id'], 'errors' );
		endif;


		if ( $parameters['title'] && $parameters['content'] && $parameters['excerpt'] ) :
			$post_data = array(
				'post_title'    => sanitize_text_field( $parameters['title'] ),
				'post_content'  => $parameters['content'],
				'post_excerpt'  => $parameters['excerpt'],
				'post_status'   => 'publish',
				'post_author'   => 1,
				'meta_input'   => array(
					'article_source' => !empty($parameters['link']) ? $parameters['link'] : '', // Full feed article link.
				),
			);

		else :
            $missed_fields = empty($parameters['title']) ? 'Title' : '';
            $missed_fields .= empty($parameters['content']) ? ($missed_fields ? ', ' : '').'Content' : '';
            $missed_fields .= empty($parameters['excerpt']) ? ($missed_fields ? ', ' : '').'Excerpt' : '';
            $content   = "Missed OpenAI generated article fields: $missed_fields.";

			$post_data = array(
				'post_title'    => 'Corrupted Article',
				'post_content'  => $content,
				'post_status'   => 'draft',
				'post_author'   => 1,
			);
		endif;

		$post_id = wp_insert_post( $post_data );

		if ( $post_id ) :
			// Complete last step of plugin usage guide.
			if (!empty($parameters['post_id'])) :
				$post_create_guide = get_option('post_create_guide');
				$post_collect_guide = get_option('post_collect_guide');
				
				$feed_id = $parameters['post_id'];
				if ($post_create_guide == $feed_id && $post_collect_guide == $feed_id) :
					update_option('post_populate_guide', true);
				endif;
			endif;
			
			return new WP_REST_Response([ 'status' => 'success', ], 200);
			
		else:
			return new WP_Error('error_save', 'Error save', array('status' => 400));
		endif;
    }


	/**
	 * Synchronize site feeds post status with requested from URL.
     *
     * @param object $request
     *
     * @return object WP_REST_Response
     * @return object WP_Error
     */
    public static function sync_feeds($request)
    {

        $parameters = $request->get_params();

		$website_key = get_option( PREFIX.'_website_key' );

        if ( !$parameters['website_key'] ) :
			return new WP_Error('missed_authToken', 'Missed authToken param', array('status' => 400));
        endif;

		if ( $parameters['website_key'] != $website_key ) :
			return new WP_Error('missed_authToken', 'Wrong value authToken param', array('status' => 400));
		endif;

		if ( !$parameters['posts_ids'] ) :
			return new WP_Error('missed_params', 'Missed posts data', array('status' => 400));
		endif;


		$feeds = get_posts([
			'post_type' => 'blinkingrobots',
			'numberposts' => -1,
			'post_status' => 'publish',
		]);

		if ( $feeds ) :
			foreach ($feeds as $post ) :

				// Update local feed post status to Draft if it not exists in array of requested active feeds.
				if ( !in_array($post->ID,  $parameters['posts_ids']) ) :
					wp_update_post([
						'ID' => $post->ID,
						'post_status' => 'draft',
					]);
				endif;

			endforeach;
		endif;

		return new WP_REST_Response([ 'status' => 'success', ], 200);
    }


	 /**
     * @param $request
     * @return object
     */
    public static function update_subscription($request)
    {

        $parameters = $request->get_params();

        if (! isset($parameters['authToken'])) :
            return new WP_Error('missed_authToken', 'Missed value authToken', array('status' => 400));
        endif;

        return new WP_REST_Response([
            '$parameters' => $parameters,
        ], 200);

    }

    /**
     * The instance of this class
     *
     * @since 1.0.0
     * @var null|object
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
