<?php
namespace BlinkingRobots;

class Fields
{

    /**
     * @var null
     */
    protected static $instance = null;

    protected static $fields = [
        'title',
        'content',
        'categories',
        'publish_date_and_time',
        'link',
    ];

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

    /**
     * Fields constructor.
     */
    function __construct()
    {

        // Add meta box
        add_action('add_meta_boxes', array(__CLASS__, 'add_custom_meta_box'));

        // Save post meta data
        add_action('save_post_'.PREFIX, array(__CLASS__, 'save_custom_field_data'));
		
		// Post saved feed to OpenAI
        add_action('save_post_'.PREFIX, array(__CLASS__, 'post_feed_data'));		
		
		// Delete feed
		add_action('save_post_'.PREFIX, array(__CLASS__, 'post_feed_delete'));		
		
		// ShowingNotice status of subscriber feeds.
		add_action('admin_notices', array(__CLASS__, 'feeds_status_notice'));
		
		// Showing error notice
        add_action('admin_notices', array(__CLASS__, 'error_notice'));
		
        add_filter('the_content', array(__CLASS__, 'article_content_filter'));
		
		
		// Manage BlinkingRobots feeds list column.
		add_filter( 'manage_blinkingrobots_posts_columns', array(__CLASS__, 'manage_columns'));
		
		add_action( 'manage_blinkingrobots_posts_custom_column', array(__CLASS__, 'manage_column_output'), 10, 2);
		
		add_action( 'admin_head', array(__CLASS__, 'column_styles'));	
    }

    public static function add_custom_meta_box() {
        add_meta_box(
            PREFIX.'_meta_box',
            'Feed Custom Fields',
            array(__CLASS__, 'render_meta_box_content'),
            PREFIX
        );
    }

    public static function render_meta_box_content($post) {

        wp_nonce_field(PREFIX.'_custom_fields_nonce', PREFIX.'_custom_field_nonce');

        $structure = get_post_meta($post->ID, PREFIX.'_structure', true);

        if ($structure) :
            $options = [''];
            ?>

            <style>
                /* General table styles */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px auto;
                    font-family: Arial, sans-serif;
                    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.15);
                }

                th, td {
                    border: 1px solid #ddd;
                    padding: 8px 12px;
                    text-align: left;
                }

                th {
                    background-color: #f5f5f5;
                    color: #333;
                }

                tbody tr:hover {
                    background-color: #f6f6f6;
                }

                /* Stripe rows for better readability */
                tbody tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
            </style>

            <h4>Fields provided by the Feed</h4>

            <table>
                <!-- Headlines (headers) -->
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Data</th>
                    </tr>
                </thead>

                <!-- Rows with data -->
                <tbody>
                    <?php
                    foreach ($structure as $name => $value) :
                        $options[] = $name;
                        ?>
                        <tr>
                            <td><?php echo esc_html( $name ); ?></td>
                            <td><?php echo esc_html( self::formatValue($value) ); ?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>

            <h4>Match them fields on your website</h4>

            <table>
                <!-- Headlines (headers) -->
                <thead>
                <tr>
                    <th>Field</th>
                    <th>Key</th>
                </tr>
                </thead>

                <!-- Rows with data -->
                <tbody>
                <?php
                foreach (self::$fields as $name) :
                    ?>
                    <tr>
                        <td><label for="<?php echo esc_attr(PREFIX); ?>_<?php echo esc_attr($name); ?>"><?php echo esc_html($name); ?></label></td>
                        <td>
                            <select name="<?php echo esc_attr(PREFIX); ?>_<?php echo esc_attr($name); ?>" id="<?php echo esc_attr(PREFIX); ?>_<?php echo esc_attr($name); ?>">
                                <?php
                                foreach ($options as $option) :
                                    echo '<option value="' . esc_attr($option) . '" ' . selected(get_post_meta($post->ID, PREFIX.'_'.$name, true), $option, false) . '>' . esc_html($option) . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
                </tbody>
            </table>

            <?php
        endif;
    }

    public static function save_custom_field_data($post_id) {
        if (!isset($_POST[PREFIX.'_custom_field_nonce']) || !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST[PREFIX.'_custom_field_nonce'])), PREFIX.'_custom_fields_nonce') ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach (self::$fields as $name) :
            if (isset($_POST[PREFIX.'_'.$name])) :
                update_post_meta($post_id, PREFIX.'_'.$name, sanitize_text_field($_POST[PREFIX.'_'.$name]));
            endif;
        endforeach;
		
		// Pull feed favicon.
		update_post_meta($post_id, PREFIX.'_feed_logo', sanitize_url(Feed::get_favicon( get_post($post_id)->post_title )));
		
        $website_key = get_option(PREFIX.'_website_key', '');

        if (!$website_key) :
            update_option(PREFIX.'_website_key', '');
        endif;
    }

    public static function formatValue($input) {
        if (is_array($input)) :
            return Utility::pr($input, 0);
        else :
            return self::truncateString($input);
        endif;
    }

    public static function truncateString($input) {
        if (strlen($input) > 300) {
            return substr($input, 0, 100) . '[...]';
        }
        return $input;
    }
	
	
	/**
     * Post feed data to remote REST API.
     *
     * @param   int  $post_id
     *
     * @return  void
     * @since   1.0.0
     */
	public static function post_feed_data( $post_id ) {
	
	
	
		if ( wp_is_post_revision( $post_id ) ){
			return;
		}
	
		if ( get_post($post_id)->post_status == 'publish' ) :
		
			$feed_url = get_the_title($post_id);
			$title = get_post_meta( $post_id, PREFIX.'_title', true);
			$content = get_post_meta( $post_id, PREFIX.'_content', true);
			$publish_date_and_time = get_post_meta( $post_id, PREFIX.'_publish_date_and_time', true);
			$link = get_post_meta( $post_id, PREFIX.'_link', true);
		
			$response = API::post_feed( $feed_url, $title, $content, $publish_date_and_time, $link, $post_id );

			// Save errors.
			if (is_wp_error($response)) :
				update_post_meta( $post_id, 'errors', $response );
			else:
				delete_post_meta( $post_id, 'errors' );
			endif;
		endif;
		
		
    }
	
	
	/**
     * Delete post of feed and send remote request to delete feed from REST API.
     *
     * @param   int  $post_id
     *
     * @since   1.0.0
     */
	public static function post_feed_delete( $post_id ) {
	
		if ( wp_is_post_revision( $post_id ) ){
			return;
		}
		
		if ( get_post_status($post_id) == 'draft' || get_post_status($post_id) == 'trash' ) :
		
			$response = API::delete_feed( $post_id );

			// Save errors.
			if (is_wp_error($response)) :
				update_post_meta( $post_id, 'errors', $response );
			else:
				delete_post_meta( $post_id, 'errors' );
			endif;

		endif;
    }


	/**
     * Showing error notices on edit post screen of Feeds post type
     *
     * @return  void
     * @since   1.0.0
     */
	public static function error_notice() {
		
		$screen = get_current_screen();
		
		if ( $screen->parent_base == 'edit' && $screen->base == 'post' ) :
		
			global $post;
			$errors = get_post_meta($post->ID, 'errors', true);
		
			if ($errors) :
			
				foreach ($errors->get_error_messages() as $error_message) :
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo nl2br(esc_html($error_message)); ?></p>
					</div>
					<?php
				endforeach;
				
			endif;
		endif;
    }
	
	/**
     * Notice status of subscriber feeds.
     *
     * @return  void
     * @since   1.0.0
     */
	public static function feeds_status_notice() {
		
		$screen = get_current_screen();
		
		if ( $screen->post_type != 'blinkingrobots' ) {
			return;
		}
		
		$response = API::feeds_status();
	
		if (is_wp_error( $response )) :
			echo '<div class="notice notice-error">';
				$error_code = $response->get_error_code();
				
				if ($error_code == 'missed_user') :
					echo '<p><b>'. esc_html($response->get_error_message($error_code)) .'.</b><br>Try it for free or buy a paid plan from getting a subscription on <a href="'. esc_url("https://feed.blinkingrobots.com/pricing") .'" target="_blank">BlinkingRobots</a>.</p>';			
				elseif ($error_code == 'missed_subscription') :
					echo '<p><b>'. esc_html($response->get_error_message($error_code)) .'.</b><br>Sign In for getting a free or paid subscription plan on <a href="'. esc_url("https://feed.blinkingrobots.com/pricing") .'" target="_blank">BlinkingRobots</a>.</p>';
				elseif ($error_code == 'missed_subscription_select') :
					echo '<p><b>'. esc_html($response->get_error_message($error_code)) .'.</b><br>Please, contact to BlinkingRobots support <a href="mailto:phil@doejo.com">phil@doejo.com</a>.</p>';
				elseif ($error_code == 'missed_requested_params'):
					echo '<p><b>Missed blinkingrobots authorization parameters:</b><br>';
						foreach ($response->get_error_messages($error_code) as $error_messages) :
							echo esc_html($error_messages) . '<br>';
						endforeach;	
					echo '</p>';
				endif;
				
			echo '</div>';
				
		else :
			echo '<div class="notice notice-info">';
				echo '<p><b>Feeds usage: '. esc_html($response['used_feeds']) .'/'. esc_html($response['allowed_feeds']) .'.</b></p>';
			echo '</div>';
		endif;
	
    }
	
	
	/**
     * Notice status of subscriber feeds.
     *
     * @return $content - post content
     * @since   1.0.0
     */
	public static function article_content_filter( $content ) {
		if ( !is_single() ) {
			return $content;
		}
		
		global $post;
		$article_source = get_post_meta($post->ID, 'article_source', true);
		
		if ( $article_source ) {
			$content .= '<div class="article-source"><a href="'. esc_url($article_source) .'" target="_blank">View Source</a></div>';
		}
		return $content;
	}
	
	
	public static function manage_columns( $columns ) {
		
		$sync_column['feed_logo'] = '';

		// $columns =  array_slice($columns, 0, -1) + $sync_column + array_slice($columns, -1);
		$columns =  array_slice($columns, 0, 1) + $sync_column + array_slice($columns, 1, 2);
		
		return $columns;
	}

	public static function manage_column_output( $column_name, $post_id ) {

		if( $column_name == 'feed_logo' ) {
			$feed_logo = get_post_meta( $post_id, PREFIX.'_feed_logo', true );
			
			if ( $feed_logo ){
				echo '<img src="'. $feed_logo .'">';
			}	
		}
	}
	
	public static function column_styles() {
		echo '<style type="text/css">';
		echo '.column-feed_logo { width: 12px !important; }';
		echo '.column-feed_logo img { height: 16px; text-align: center; padding-top: 3px; }';
		echo '</style>';
	}
		
}
