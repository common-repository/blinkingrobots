<?php
/**
 * This is responsible for processing AJAX or other requests
 *
 * @since 1.0.0
 */

namespace BlinkingRobots;

use WP_Error;

//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class API {
	
	
	const URL_LOCAL = 'http://feed/wp-json/blinkingrobotsapi/v1/';	
	const URL_STAGING = 'https://feed.blinkingrobots.com/wp-json/blinkingrobotsapi/v1/';	
	
	
	 /**
	 * Define URI for REST API requests.
	 * 
	 * @since     1.0.0
	 * @return string
     */
    public static function get_url() {
		
		$environment = get_bloginfo('url');
		
		if( $environment == 'http://feed' ) {
			return self::URL_LOCAL;
		}
		
		return self::URL_STAGING;
    }
	
	
	/**
	 * Build URI address from given endpoint for REST API requests.
	 * 
	 * @since     1.0.0
	 * @return string
     */
    public static function get_path( $endpoint ) {
        return self::get_url() . trim($endpoint, '/');
    }
	
	
	/**
	 * Get this website key.
	 * 
	 * @since     1.0.0
	 * @return string
     */
	public static function get_website_key() {
        return get_option( PREFIX.'_website_key' );
    }
	
	
	/**
	 * Get settings email used in subscription purchase on https://feed.blinkingrobots.com
	 * 
	 * @since     1.0.0
	 * @return string
     */
	public static function get_email() {
        return get_option( PREFIX.'_email' );
    }
	

	/**
	 * Post feed data to REST API.
	 * 
	 * @param string $feed_url
	 * @param string $title
	 * @param string $content
	 * @param string $publish_date_and_time
	 * @param string $link
	 * @param string $post_id
	 * 
	 * @since     1.0.0
	 * @return array   
	 * @return object WP_Error
     */
    public static function post_feed( $feed_url = '', $title = '', $content = '', $publish_date_and_time = '', $link = '', $post_id = '' ) {
	
		$request_url = self::get_path( 'feed' );
		
		$response = self::post( $request_url, [
			'email'					  => self::get_email(), 
			'feed_url'				  => $feed_url, 
			'title'		 			  => $title, 
			'content'	 			  => $content, 
			'publish_date_and_time'	  => $publish_date_and_time, 
			'link'					  => $link, 
			'post_id'	 			  => $post_id, 
			'website_key' 		 	  => self::get_website_key(), 
			'website_url' 			  => get_bloginfo('url'),
		]);

		return $response;
    }


	/**
	 * Delete feed from REST API.
	 * 
	 * @param string $post_id
	 * 
	 * @since     1.0.0
	 * @return array   
	 * @return object WP_Error
     */
    public static function delete_feed( $post_id = '' ) {
	
		$request_url = self::get_path( 'delete' );
		
		$response = self::post( $request_url, [
			'email'					  => self::get_email(),
			'website_key' 		 	  => self::get_website_key(), 
			'website_url' 			  => get_bloginfo('url'),
			'post_id'	 			  => $post_id, 
		]);

		return $response;
    }

	/**
	 * Get feeds usage status.
	 * 
	 * @param string $post_id
	 * 
	 * @since     1.0.0
	 * @return array   
	 * @return object WP_Error
     */
    public static function feeds_status() {
	
		$request_url = self::get_path( 'feeds_status' );
		
		$response = self::post( $request_url, [
			'email'					  => self::get_email(),
			'website_key' 		 	  => self::get_website_key(), 
			'website_url' 			  => get_bloginfo('url'),
		]);

		return $response;
    }

	/**
	 * Post data to remote REST API.
	 * 
	 * @param string $url
	 * @param string $fields
	 * @param string $method
	 * 
	 * @since     1.0.0
	 * @return array   
	 * @return object WP_Error
     */
    public static function post($url = '', $fields = '',  $method = 'POST' ) {
	
	 	$args = array(
			'method'      => $method,
			'body'        => $fields,
			'blocking'    => true,
			'timeout'     => 5,
			'redirection' => 5,
		);
		
		$server_output = wp_remote_post( $url, $args );
		
		if (is_wp_error($server_output)) :
		
			return $server_output;
		elseif (wp_remote_retrieve_response_code($server_output) == 400) :
	
			$output_body = json_decode( wp_remote_retrieve_body($server_output), true );
			
			$errors = new WP_Error( $output_body['code'],  $output_body['message'] );
		
			if( isset($output_body['additional_errors']) ):
				foreach( $output_body['additional_errors'] as $error ):
					$errors->add( $error['code'], $error['message'] );
				endforeach;
			endif;

			return $errors;
		else :
		
			$decoded_output = json_decode( wp_remote_retrieve_body($server_output), true ); // convert json into array
			return $decoded_output;
		endif;
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

