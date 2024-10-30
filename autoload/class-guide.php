<?php
/**
 * Plugin usage guide.
 *
 * @since      1.0.0
 */

namespace BlinkingRobots;

class Guide
{

	function __construct()
    {
		add_action('admin_notices', __CLASS__ .'::guide_notice', 9);
        add_action('save_post_'. PREFIX, __CLASS__ .'::last_modified_post_set', 20, 2);

		// Scripts connection.
		add_action('admin_enqueue_scripts',  __CLASS__ .'::enqueue_scripts');
        add_action('wp_ajax_dont_show_notice', __CLASS__ .'::dont_show_notice_callback');
    }


	public static function guide_notice() {
		$screen = get_current_screen();

		if ( $screen->post_type != 'blinkingrobots' ) {
			return;
		}

		// Don't show guide notice if it was closed.
		if ( get_option('dont_show_guide') ) {
			return;
		}


		// 1. authorization plugin usage.
		$response = API::feeds_status();

		$auth_subscription = '';
		$auth_mail = '';
		if ( !is_wp_error($response) ) {
			if ( isset($response['allowed_feeds']) ) {
				$auth_subscription = 'completed';
				$auth_mail = 'completed';
			}

		} else {
			if ( $response->get_error_code() == 'missed_subscription' || $response->get_error_code() == 'missed_subscription_select' ) {
				$auth_mail = 'completed';
			}
		}


		// 2. add local new feed.
		$last_updated_post = get_posts([
			'numberposts' => 1,
			'post_type' => 'blinkingrobots',
			'orderby' => 'modified',
			'order' => 'DESC',
		]);

		if ( !$last_updated_post ) {
			update_option('feed_updated_date', '');
			self::reset_steps_guide();
		} else {
			update_option('feed_updated_date', $last_updated_post[0]->post_modified);
		}

		$post_create_guide = '';
        if (get_option('post_create_guide')) {
            $post_create_id    = get_option('post_create_guide');
            $title             = $post_create_id ? get_the_title($post_create_id) : '';
            $post_create_guide = $title && PostTypes::is_valid_url($title) ? 'completed' : '';
        }


		// 3. map and api collect new feed.
		$post_collect_guide = '';
		if ( get_option('post_collect_guide') ) {
			$post_collect_guide = 'completed';
		}
		
		// 4. populate posts on site from added api feed.
		$post_populate_guide = '';
		if ( get_option('post_populate_guide') ) {
			$post_populate_guide = 'completed';
		}
		
		
		$guide_not_completed = !$auth_subscription || !$auth_mail || !$post_create_guide || !$post_collect_guide || !$post_populate_guide;
		?>

		<div class="notice guide guide-progress-notice <?php echo $guide_not_completed ? 'notice-info' : 'notice-success is-dismissible'; ?>">
			<p class="title">Congratulations on installing Blinking Robots!</p>

			
			<p class="description">There are five simple steps left before it starts generating the posts for your website:</p>
			<p class="content">
				<ul>
					<li class="<?php echo esc_attr($auth_subscription); ?>">
						<input type="checkbox" disabled <?php echo $auth_subscription ? 'checked' : ''; ?>/>Visit <a href="<?php echo esc_url('https://feed.blinkingrobots.com/pricing'); ?>" target="_blank">our website</a>, select a plan and create a subscription.
					</li>
					<li class="<?php echo esc_attr($auth_mail); ?>">
						<input type="checkbox" disabled <?php echo $auth_mail ? 'checked' : ''; ?>/>On <a href="<?php echo esc_url('/wp-admin/edit.php?post_type=blinkingrobots&page=blinkingrobots-settings'); ?>">the settings page</a>, activate your subscription by specifying your email.
					</li>
					<li class="<?php echo esc_attr($post_create_guide); ?>">
						<input type="checkbox" disabled <?php echo $post_create_guide ? 'checked' : ''; ?>/>Open an <a href="/wp-admin/post-new.php?post_type=blinkingrobots">Add Feed</a>, page set the title the URL to your RSS feed and click “Publish“.
					</li>
					<li class="<?php echo esc_attr($post_collect_guide); ?>">
						<input type="checkbox" disabled <?php echo $post_collect_guide ? 'checked' : ''; ?>/>On <?php echo isset($post_create_id) ? '<a href="/wp-admin/post.php?post='. esc_html($post_create_id) .'&action=edit">the Feed page</a>' : 'the Feed page'; ?>, map the fields from the feed to Wordpress.
					</li>
					<li class="<?php echo esc_attr($post_populate_guide); ?>">
						<input type="checkbox" disabled <?php echo $post_populate_guide ? 'checked' : ''; ?>/>Wait for your posts to populate.
					</li>
				</ul>
			</p>
		</div>

		<?php
    }


	public static function last_modified_post_set( $post_id, $post) {

		if ( $post->post_status == 'publish' ) {
			$feed_updated_date = get_option('feed_updated_date');

			if ( $feed_updated_date < $post->post_modified ) {
				update_option('post_create_guide', $post_id);

				$feed_url = get_the_title($post_id);
				$title = get_post_meta( $post_id, PREFIX.'_title', true);
				$content = get_post_meta( $post_id, PREFIX.'_content', true);
				$publish_date_and_time = get_post_meta( $post_id, PREFIX.'_publish_date_and_time', true);
				$link = get_post_meta( $post_id, PREFIX.'_link', true);

				$response = API::post_feed( $feed_url, $title, $content, $publish_date_and_time, $link, $post_id );

				if ( !is_wp_error($response)) {
					update_option('post_collect_guide', $post_id);
				} else {
					update_option('post_collect_guide', '');
				}
			}
		}
	}


	public static function enqueue_scripts() {
		wp_enqueue_script( 'guide', PLUGIN_URL . '/assets/js/components/guide.js', array('jquery'), '', true );
    }


	public static function dont_show_notice_callback() {

		if( isset($_POST['dont_show_notice']) ){
			self::reset_steps_guide();
			add_option('dont_show_guide', true);
		}
		wp_die();
	}


	public static function reset_steps_guide() {
		delete_option('post_create_guide');
		delete_option('post_collect_guide');
		delete_option('post_populate_guide');
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
