<?php
/**
 * Plugin Name: Blinking Robots
 * Plugin URI: https://blinkingrobots.com
 * Description: This pluign provides an ability to fetch articles from the given feeds and summarize them with OpenAI
 * Version: 1.1.7
 * Author: Blinking Robots
 * Author URI:  https://feed.blinkingrobots.com
 * Text Domain: blinkingrobots
 * Domain Path: /languages
 * Network: false
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * GitHub Plugin URI: hisaveliy/blinkingrobots
 */


namespace BlinkingRobots;


// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}


define(__NAMESPACE__.'\PREFIX', 'blinkingrobots');

define(__NAMESPACE__.'\PLUGIN_VERSION', '1.1.7');

define(__NAMESPACE__.'\PLUGIN_NAME', 'Blinking Robots');

define(__NAMESPACE__.'\PLUGIN_SHORTNAME', 'BlinkingRobots');

define(__NAMESPACE__.'\PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));

define(__NAMESPACE__.'\PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));

define(__NAMESPACE__.'\PLUGIN_BASENAME', plugin_basename(PLUGIN_DIR).'/blinkingrobots.php');

define(__NAMESPACE__.'\PLUGIN_FOLDER', plugin_basename(PLUGIN_DIR));

define(__NAMESPACE__.'\PLUGIN_INSTANCE', sanitize_title(crypt(sanitize_url($_SERVER['SERVER_NAME']), $salt = PLUGIN_FOLDER)));

define(__NAMESPACE__.'\PLUGIN_SETTINGS_URL', admin_url('admin.php?page='.PREFIX));

define(__NAMESPACE__.'\CHANGELOG_COVER', PLUGIN_URL.'/assets/images/plugin-cover.jpg');

define(__NAMESPACE__.'\ERROR_PATH', plugin_dir_path(__FILE__).'error.log');

define(__NAMESPACE__.'\TEXT_DOMAIN', 'blinkingrobots');

define(__NAMESPACE__.'\COMPOSER', false);

//init
if (! class_exists(__NAMESPACE__.'\Core')) {
    include_once PLUGIN_DIR.'/includes/class-core.php';
}

register_activation_hook(__FILE__, __NAMESPACE__.'\Core::on_activation');
register_deactivation_hook(__FILE__, __NAMESPACE__.'\Core::on_deactivation');

//load translation, make sure this hook runs before all, so we set priority to 1
add_action('init', function () {
    load_plugin_textdomain(__NAMESPACE__.'\blinkingrobots', false, dirname(plugin_basename(__FILE__)).'/languages/');
}, 1);
