<?php

namespace BlinkingRobots;

use Elementor\Icons_Manager;

$elementor_widget           = $elementor_widget ?? false;
$type                       = $type ?? '';
$size                       = $size ?? 'md';
$text                       = $text ?? false;
$full_width                 = $full_width ?? false;
$link                       = $link ?? false;
$icon                       = $icon ?? false;
$selected_icon              = $selected_icon ?? false;
$migrated                   = $migrated ?? false;
$icon_align                 = $icon_align ?? 'left';
$class_mod                  = $class_mod ?? '';
$widget_class_mod           = $widget_class_mod ?? false;
$widget_container_class_mod = $widget_container_class_mod ?? false;
$icon_size                  = $icon_size ?? false;
$href_attr                  = $href_attr ?? 'href';

$url    = is_array($link) && array_key_exists('url', $link) ? $link['url'] : '';
$target = is_array($link) && array_key_exists('is_external', $link) && $link['is_external'] ? '_blank' : '_self';
$tag    = isset($tag) ? $tag : 'a';
$atts   = isset($atts) ? $atts : false;

if ($full_width) {
    $class_mod .= ' w-100';
}
if ($icon_size) {
    $class_mod .= "elementor-icon-size-{$icon_size}";
}
if ($type) {
    $widget_class_mod .= "elementor-button_{$type}";
}

$is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();
?>

<?php if (!$elementor_widget) { ?>
    <div class="elementor-element elementor-widget elementor-widget-button <?php echo esc_attr($widget_class_mod); ?>"
         data-element_type="widget">
        <div class="elementor-widget-container <?php echo esc_attr($widget_container_class_mod); ?>">
            <?php } ?>
                <div class="elementor-button-wrapper">
                <<?php echo esc_html($tag); ?> <?php if ($url) : echo esc_url("{$href_attr}='{$url}'"); endif; ?>
                <?php if ($tag === 'a') { ?>
                    target="<?php echo esc_attr($target); ?>"
                <?php } ?>
                class="elementor-button-link elementor-button
                elementor-size-<?php echo esc_attr($size); ?>
                <?php echo esc_attr($class_mod); ?>"
                role="button" <?php echo esc_attr($atts); ?>>
                    <span class="elementor-button-content-wrapper">
                        <?php if (! empty($icon) || ! empty($selected_icon['value'])) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php echo esc_attr($icon_align); ?>">
                                <?php if ($is_new || $migrated) :
                                    Icons_Manager::render_icon($selected_icon, ['aria-hidden' => 'true']);
                                else : ?>
                                    <i class="<?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                            </span>
                        <?php } ?>
                        <?php if ($text) { ?>
                            <span class="elementor-button-text"><?php echo esc_html($text); ?></span>
                        <?php } ?>
                    </span>
                </<?php echo esc_html($tag); ?>>
            </div>
            <?php if (!$elementor_widget) { ?>
        </div>
    </div>
<?php }
