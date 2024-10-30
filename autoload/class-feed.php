<?php
namespace BlinkingRobots;

class Feed
{

    /**
     * Fields constructor.
     */
    function __construct()
    {



    }

    /**
     * @param $url
     * @return array|null https://techcrunch.com/feed/
     */
    public static function fetch($url = '') {

        /**
         * Check if that's WP rest api
         */
        if (strpos($url, '/wp-json/wp/v2/') !== false) {
            $url = $url.'?per_page=100';
        }

        // Fetch the data from the URL
        $data = file_get_contents($url);
        if (!$data) {
            return null;
        }

        // Check if data is in JSON format
        $jsonData = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData;
        }

        // Check if data is in XML format
        libxml_use_internal_errors(true);
        $xmlData = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
        if ($xmlData !== false) {
            $jsonXmlData = json_encode($xmlData);
            return json_decode($jsonXmlData, true);
        }

        // If neither JSON nor XML, return null
        return null;
    }

    public static function get_items($url = '') {
        if (!$url)
            return null;

        $feed = self::fetch($url);

        if (
            is_array($feed)
            && array_key_exists('channel', $feed)
            && array_key_exists('item', $feed['channel'])
        ) :
            return $feed['channel']['item'];
        endif;

        return $feed;
    }
	
	
	/**
	 * Pull website favicon url from provided feed url.
	 * 
	 * @param $feed_url
	 * 
     * @return string|boolean
     */
    public static function get_favicon($feed_url = '') {
       
		$elems = parse_url($feed_url);
		
		if (empty($elems['scheme']) || empty($elems['host'])) {
			return false;
		}
		
		$site_url = $elems['scheme'] .'://'. str_replace( array('rss.', 'feeds.'), '', $elems['host'] );

		$doc = new \DOMDocument();
		$doc->strictErrorChecking = FALSE;

		$doc->loadHTML(file_get_contents($site_url));
		$xml = simplexml_import_dom($doc);

		if ($xml) {
			$masks = array(
				'//link[@rel="icon"]',
				'//link[@rel="shortcut icon"]',
			);

			foreach ($masks as $mask) {
				$matches = $xml->xpath($mask);
				$favicon_url = (string) $matches[0]['href'];
				
				if ($favicon_url) {
					if (strpos($favicon_url, 'http') === false) {
						$favicon_url = $site_url .'/'. trim($favicon_url, '/');
					}
					return $favicon_url;
				}
			}
		}
		return false;
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
