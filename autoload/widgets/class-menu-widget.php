<?php
/**
 * This is a utility class, it contains useful methods
 *
 * Created by PhpStorm.
 * User: apple
 * Date: 2019-06-29
 * Time: 17:59
 *
 * @since      1.0.0
 */

namespace BlinkingRobots;

use WP_Widget;

class MenuWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'menu-widget',
            __('Menu', 'blinkingrobots'),
            ['description' => __('Choose the menu.', 'blinkingrobots')]
        );

    }

    // Widget output
    public function widget($args, $instance)
    {
        $widget_id  = $args['widget_id'];
        $title      = array_key_exists('title', $instance) && $instance['title'] ? $instance['title'] : false;
        $menu       = array_key_exists('menu', $instance) && $instance['menu'] ? $instance['menu'] : false;
        $menu_items = wp_get_nav_menu_items($menu);

        $queried_object = get_queried_object();
        if ($queried_object) {
            $post_id = $queried_object->ID;
        }

        if ($menu) {
            $menu_items = wp_get_nav_menu_items($menu);
            if ($menu_items) {
                echo "<div id='". esc_attr($widget_id) ."' class='widget widget_pb-sm widget_". esc_attr($widget_id) ."'>";

                if ($title) {
                    echo "<h3 class='widget__title widget__title_decor'><b>". esc_html($title) ."</b></h3>";
                }

                echo '<ul class="widget-links">';
                foreach ($menu_items as $menu_item) {
                    $id              = $menu_item->object_id;
                    $class_mod       = isset($post_id) && (string)$post_id === (string)$id ? 'is-active' : false;
                    $link            = get_the_permalink($id);
                    $menu_item_title = get_the_title($id);
                    echo "<li><a href='". esc_url($link) ."' class='widget-links__button ". esc_html($class_mod) ."'>". esc_html($menu_item_title) ."</a></li>";
                }
                echo '</div>';

                echo '</div>';
            }
        }

    }

    /**
     * Handles updating settings for the current Trending posts widget instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            TrendingPostsWidget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     * @since 1.9.0
     *
     */
    public function update($new_instance, $old_instance)
    {
        $instance          = $old_instance;
        $new_instance      = wp_parse_args(
            (array)$new_instance,
            array(
                'title' => '',
                'menu'  => '',
            )
        );
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['menu']  = $new_instance['menu'];

        return $instance;
    }

    /**
     * Outputs the settings form for the Trending posts widget.
     *
     * @param array $instance Current settings.
     * @since 1.9.0
     *
     */
    public function form($instance)
    {
        $instance = wp_parse_args(
            (array)$instance,
            array(
                'title' => '',
                'menu'  => '',
            )
        );
        $menus    = wp_get_nav_menus(['hide_empty' => false, 'orderby' => 'name']);

        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:'); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($instance['title']); ?>"/>
        </p>
        <?php if (! empty($menus)) { ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('menu')); ?>"><?php esc_html_e('Select Menu:'); ?></label>
            <select class="widefat"
                    id="<?php echo esc_attr($this->get_field_id('menu')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('menu')); ?>"
            >
                <?php foreach ($menus as $menu) {
                    $selected = $instance['menu'] === (string)$menu->term_id ? 'selected="selected"' : false;
                    echo "<option value='". esc_attr($menu->term_id) ."' ". esc_attr($selected) .">". esc_html($menu->name) ."</option>";
                } ?>
            </select>
        </p>
        <?php
    }
    }
}
