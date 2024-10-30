<?php
/**
 * This is a utility class, it contains useful methods
 *
 * @since      1.0.0
 */

namespace BlinkingRobots;

class Utility
{


    /**
     * Get content of a given file
     *
     * @param string $file
     * @param mixed $vars
     * @return mixed
     * @since 1.0.0
     */
    public static function get_tpl($file = '', $vars = [])
    {
        extract($vars);

        $path = PLUGIN_DIR.'/'.$file.'.php';

        $c = '';

        if (file_exists($path)) {
            ob_start();

            include $path;

            $c = ob_get_clean();

        }

        return $c;
    }


    /**
     * Get content of a given file
     *
     * @param string $file
     * @param mixed $vars
     * @return mixed
     * @since 1.0.0
     */
    public static function get_tpl_format($file = '', $vars = [])
    {
        extract($vars);

        $path = PLUGIN_DIR.'/'.$file;

        $c = '';

        if (file_exists($path)) {
            ob_start();

            include $path;

            $c = ob_get_clean();

        }

        return $c;
    }


    /**
     * Get content of a given file
     *
     * @param string $file
     * @param mixed $vars
     * @return mixed
     * @since 1.0.0
     */
    public static function tpl($file = '', $vars = array())
    {
        echo esc_html( self::get_tpl($file, $vars) );
    }


    /**
     * @param $data
     * @param $echo
     * @return string|void
     */
    public static function pr($data, $echo = 1)
    {

        if ($echo)
            echo esc_html( '<pre>'.print_r($data, 1).'</pre>' );
        else
            return '<pre>'.print_r($data, 1).'</pre>';
    }


    /**
     * Get a specific property of an array without needing to check if that property exists.
     *
     * Provide a default value if you want to return a specific value if the property is not set.
     *
     * @param array $array Array from which the property's value should be retrieved.
     * @param string $prop Name of the property to be retrieved.
     * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
     *
     * @return null|string|mixed The value
     * @since  1.0.0
     */
    public static function rgar($array, $prop, $default = null)
    {

        if (! is_array($array) && ! (is_object($array) && $array instanceof ArrayAccess)) {
            return $default;
        }

        if (isset($array[$prop])) {
            $value = $array[$prop];
        } else {
            $value = '';
        }

        return empty($value) && $default !== null ? $default : $value;
    }


    /**
     * Gets a specific property within a multidimensional array.
     *
     * @param array $array The array to search in.
     * @param string $name The name of the property to find.
     * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
     *
     * @return null|string|mixed The value
     * @since  Unknown
     * @access public
     *
     */
    public static function rgars($array, $name, $default = null)
    {

        if (! is_array($array) && ! (is_object($array) && $array instanceof ArrayAccess)) {
            return $default;
        }

        $names = explode('/', $name);
        $val   = $array;
        foreach ($names as $current_name) {
            $val = self::rgar($val, $current_name, $default);
        }

        return $val;
    }


    /**
     * Display admin network notice
     *
     * @param string $msg
     * @param string $type
     * @return string
     * @since 1.0.0
     */
    public static function show_network_notice($msg, $type = 'error')
    {

        add_action('network_admin_notices', function () use ($msg, $type) {
            echo esc_html( '<div class="wsa-notice notice notice-'.$type.'"><p>'.$msg.'</p></div>' );
        });
    }


    /**
     * Display admin notice
     *
     * @param string $msg
     * @param string $type
     * @return string
     * @since 1.0.0
     */
    public static function show_notice($msg, $type = 'error')
    {

        add_action('admin_notices', function () use ($msg, $type) {
            echo esc_html( '<div class="wsa-notice notice notice-'. esc_attr($type) .'"><p>'. esc_html($msg) .'</p></div>' );
        });
    }


    /**
     * Log errors in a error.log file in the root of the plugin folder
     *
     * @param mixed $msg
     * @param string $code
     * @return void
     * @since 1.0.0
     */
    public static function error_log($msg, $code = '')
    {

        if (! is_string($msg)) {
            $msg = print_r($msg, true);
        }

        error_log('Error '.$code.' ['.date('Y-m-d h:m:i').']: '.$msg.PHP_EOL, 3, ERROR_PATH);
    }


    /**
     * Log errors in Woocommerce logs
     *
     * @param mixed $msg
     * @param string $code
     * @return void
     * @since 1.0.0
     */
    public static function woo_error_log($msg, $code = '')
    {

        if (class_exists('\WooCommerce')) {

            $msg = ! is_string($msg) ? print_r($msg, true) : $msg;

            $logger  = wc_get_logger();
            $context = array('source' => PLUGIN_FOLDER);
            $logger->error($code.' '.$msg, $context);

        } else {
            self::error_log($msg, $code);
        }
    }


    /**
     * @param $obj
     * @return array|mixed|object
     */
    public static function obj_to_arr($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * @param array $pairs
     * @param array $atts
     * @return array
     */
    public static function atts($pairs = array(), $atts = array())
    {
        $atts = (array)$atts;
        $out  = array();

        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }

        return $out;
    }



    public static function get_actual_link()
    {
        return sanitize_url( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"). sanitize_url("://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") );
    }

    public static function get_share_link($social_network)
    {
        if ($social_network === 'facebook') {
            $link = 'https://www.facebook.com/sharer/sharer.php?u='.Utility::get_actual_link();
        } elseif ($social_network === 'twitter') {
            $link  = 'https://twitter.com/intent/tweet?url='.Utility::get_actual_link();
            $title = get_the_title();
            $link  .= '&text='.urlencode($title);
        } elseif ($social_network === 'linkedin') {
            $link = 'https://www.linkedin.com/sharing/share-offsite/?url='.Utility::get_actual_link();
        } elseif ($social_network === 'whatsapp') {
            $link = 'whatsapp://send?text='.Utility::get_actual_link();
        } elseif ($social_network === 'letter') {
            $link = 'mailto:?subject='.__('I wanted you to see this site',
                    'blinkingrobots').'&amp;body='.__('Check out this site', 'blinkingrobots').' '.Utility::get_actual_link();
        } else {
            $link = '';
        }

        return $link;
    }

}
