<?php

namespace BlinkingRobots;

class PostTypes
{

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

    /**
     * Fields constructor.
     */
    function __construct()
    {

        add_action('init', __CLASS__.'::register_feed_post_type');

        add_action('save_post_'.PREFIX, function ($post_id = 0) {

            if (! empty($_POST['post_title'])) {
                $url = sanitize_url($_POST['post_title']);

                if (filter_var($url, FILTER_VALIDATE_URL) !== false) :
                    $feed = Feed::get_items($url);

                    update_post_meta($post_id, PREFIX.'_structure', $feed[0]);
                endif;
            }


        });

        add_action('admin_menu', __CLASS__.'::register_settings_submenu_page');
        add_action('admin_init', __CLASS__.'::register_settings_fields');
        add_filter('enter_title_here', __CLASS__.'::feed_custom_title_placeholder', 10, 2);
        add_action('save_post', __CLASS__.'::validate_feed_title', 10, 3);
        add_filter('wp_insert_post_data', __CLASS__.'::validate_create_feed_title', 10, 2);

    }

    /**
     * Register post type
     */
    public static function register_feed_post_type()
    {

        /**
         * @link https://wp-kama.ru/function/register_post_type
         */
        register_post_type(PREFIX, array(
            'label'               => null,
            'labels'              => array(
                'name'               => __('Feeds', 'blinkingrobots'),
                'singular_name'      => __('Feed', 'blinkingrobotsi'),
                'add_new'            => __('Add Feed', 'blinkingrobots'),
                'add_new_item'       => __('Add new Feed', 'blinkingrobots'),
                'edit_item'          => __('Edit Feed', 'blinkingrobots'),
                'new_item'           => __('New Feed', 'blinkingrobots'),
                'view_item'          => __('See Feed', 'blinkingrobots'),
                'search_items'       => __('Search Feeds', 'blinkingrobots'),
                'not_found'          => __('Not Feed', 'blinkingrobots'),
                'not_found_in_trash' => __('Not Feed in Trash', 'blinkingrobots'),
                'parent_item_colon'  => '',
                'menu_name'          => __('Feeds', 'blinkingrobots'),
            ),
            'description'         => '',
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'show_in_rest'        => false,
            'rest_base'           => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-book-alt',
            'hierarchical'        => false,
            'supports'            => array('title'),
            // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
            'has_archive'         => false,
            'rewrite'             => false,
            'query_var'           => false,
        ));


        /**
         * @link https://wp-kama.ru/function/register_taxonomy
         */
        register_taxonomy('guide-cat', array('guide'), array(
            'label'                 => '',
            'labels'                => array(
                'name'              => __('Categories', 'blinkingrobots'),
                'singular_name'     => __('Category', 'blinkingrobots'),
                'search_items'      => __('Search Categories', 'blinkingrobots'),
                'all_items'         => __('All Categories', 'blinkingrobots'),
                'view_item '        => __('View Category', 'blinkingrobots'),
                'parent_item'       => __('Parent Category', 'blinkingrobots'),
                'parent_item_colon' => __('Parent Category:', 'blinkingrobots'),
                'edit_item'         => __('Edit Category', 'blinkingrobots'),
                'update_item'       => __('Update Category', 'blinkingrobots'),
                'add_new_item'      => __('Add New Category', 'blinkingrobots'),
                'new_item_name'     => __('New Category Name', 'blinkingrobots'),
                'menu_name'         => __('Categories', 'blinkingrobots'),
            ),
            'description'           => '',
            'public'                => true,
            'publicly_queryable'    => null,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_tagcloud'         => true,
            'show_in_rest'          => null,
            'rest_base'             => null,
            'hierarchical'          => false,
            'update_count_callback' => '',
            'rewrite'               => true,
            'capabilities'          => array(),
            'meta_box_cb'           => null,
            'show_admin_column'     => true,
            '_builtin'              => false,
            'show_in_quick_edit'    => null,
        ));
    }


    /**
     * Settings submenu page register for blinkingrobots post_type.
     */
    public static function register_settings_submenu_page()
    {
        add_submenu_page(
            'edit.php?post_type='.PREFIX,
            __('Settings BlinkingRobots', 'blinkingrobots'),
            __('Settings', 'blinkingrobots'),
            'manage_options',
            PREFIX.'-settings',
            __CLASS__.'::settings_submenu_page_content'
        );
    }


    /**
     * Settings submenu page content print.
     */
    public static function settings_submenu_page_content()
    {
        ?>
        <div class="wrap">
            <h2><? echo esc_html(get_admin_page_title()); ?></h2>

            <form action="options.php" method="POST">

                <?php
                settings_errors();
                settings_fields(PREFIX.'_settings_general');
                do_settings_sections(PREFIX.'-settings');
                submit_button('Save Changes');
                ?>
            </form>
        </div>
        <?php
    }


    /**
     * Settings submenu page content fields.
     */
    public static function register_settings_fields()
    {

        add_settings_section(
            PREFIX.'_setting_section', // id
            'Authorization', // title
            __CLASS__.'::authorization_setting_section_callback', // callback function
            PREFIX.'-settings' // slug from add_menu_page()
        );
        add_settings_field(
            PREFIX.'_email', // id
            'Email', // title
            __CLASS__.'::email_field_print_callback', // callback function
            PREFIX.'-settings', // slug from add_menu_page()
            PREFIX.'_setting_section', // section id
        );

        register_setting(PREFIX.'_settings_general', PREFIX.'_email', array(
                'sanitize_callback' => __CLASS__.'::email_field_sanitize_callback',
                'default'           => null,
            )
        );
    }


    /**
     * Authorization section content print.
     */
    public static function authorization_setting_section_callback()
    {
        // echo '<p>Enter the email was used while purchase subscription on <a href="'. esc_url("https://feed.blinkingrobots.com/pricing") .'" target="_blank">BlinkingRobots</a>.</p>';
    }


    /**
     * Field Email print.
     */
    public static function email_field_print_callback()
    {
        ?>
        <input
                type="email"
                name="blinkingrobots_email"
                value="<?php echo esc_html(get_option(PREFIX.'_email')); ?>"
        />
        <p>Enter the email was used while purchase a subscription on <a
                    href="<?php echo esc_url("https://feed.blinkingrobots.com/pricing"); ?>" target="_blank">BlinkingRobots</a>.
        </p>

        <?php
    }


    /**
     * Field Email sanitize.
     */
    public static function email_field_sanitize_callback($email)
    {

        $option_name = PREFIX.'_email';

        $email = sanitize_email($email);
        add_settings_error($option_name, 'settings_updated_', 'Email saved successfully!', 'updated');

        return $email;
    }

    public static function feed_custom_title_placeholder($title_placeholder, $post)
    {
        // Check post type
        if ($post->post_type == PREFIX) {
            // Change the placeholder text
            $title_placeholder = 'Add RSS/XML Feed URL Here';
        }

        return $title_placeholder;
    }

    public static function get_invalid_title_error()
    {
        return 'Error: The RSS/XML Feed must be a valid URL.';
    }

    public static function is_valid_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function validate_feed_title($post_id, $post, $update)
    {
        $action = $_GET['action'] ?? '';
        if (! $update || $action === 'trash') {
            return;
        }
        // Check post type
        if ($post->post_type == PREFIX) {
            $title = get_the_title($post_id);

            // Check if the title is a valid URL
            if (!self::is_valid_url($title)) {
                // If not a valid URL, prevent saving
                wp_die(self::get_invalid_title_error());
            }
        }
    }

    public static function validate_create_feed_title($data, $postarr)
    {
        // Check if the post data is empty (indicating that the form hasn't been submitted)
        if (empty($_POST)) {
            return $data;
        }
        $post_status = $data['post_status'] ?? '';
        if ($post_status === 'auto-draft') {
            return $data;
        }

        // Check post type
        if ($data['post_type'] == PREFIX) {
            // Get the title
            $title = $data['post_title'];

            // Check if the title is a valid URL
            if (!self::is_valid_url($title)) {
                // If not a valid URL, prevent saving
                wp_die(self::get_invalid_title_error());
            }
        }

        return $data;
    }
}
