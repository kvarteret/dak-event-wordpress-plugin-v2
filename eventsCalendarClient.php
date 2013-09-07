<?php

require_once(__DIR__ . '/calendarClient.php');

class eventsCalendarClient extends calendarClient {

	/**
	 * Holds url of event server
	 */
	private $url;

	/**
	 * Whether or not to enable cache (apc or wordpress transients)
	 */
	private $enableCache;

	const CACHE_NONE = 0;
	const CACHE_APC = 1;
	const CACHE_WP = 2;

	/**
	 * If cache is enabled, for how long should it stay? Seconds
	 */
	private $cacheTime;

	/**
	 * API key, for use when adding events remotely.
	 * Currently not in use.
	 */
	private $apiKey;

	/**
	 * Will hold all used cache keys.
	 * Useful if you want to erase certain or all elements 
	 * used in this 
	 */
	static private $keyCollection = null;
	static private $keyCollectionChanged = false;
	static private $keyCollectionInstances = 0;

	public function __construct ($url, $apiKey = null, $enableCache = self::CACHE_NONE, $cacheTime = 5) {
		$this->url = strval($url) . '/api/json/';
		$this->apiKey = $apiKey;
		$this->cacheTime = intval($cacheTime);
		$this->enableCache = intval($enableCache);

		if (function_exists('curl_init')) {
			$this->getContentMethod = 'curl';
		} else {
			$this->getContentMethod = 'file_get_contents';
		}

		self::$keyCollectionInstances++;
	}

	public function __destruct () {
		$this->saveCacheKeyCollection();
		self::$keyCollectionInstances--;
	}

	/**
	 * Records a cache key if it doesn't exist
	 */
	protected function setCacheKey ($key) {
		if (is_null(self::$keyCollection)) {
			self::$keyCollection = $this->getCache('eCC_keyCollection'); // eCC is an abbreviation of eventsCalendarClient
			if (!is_array(self::$keyCollection)) {
				self::$keyCollection = array();
				self::$keyCollectionChanged = true;
			}
		}

		if ( ! in_array($key, self::$keyCollection) ) {
			self::$keyCollection[] = $key;
			self::$keyCollectionChanged = true;
		}
	}

	/**
	 * Saves a cache key collection if this is the last instance to be "alive"
	 */
	private function saveCacheKeyCollection () {
		if ( ! is_null(self::$keyCollection) && (self::$keyCollectionInstances == 1)) {
			$this->setCache('eCC_keyCollection', self::$keyCollection, 0);
		}
	}

	public function getCacheKeyCollection () {
		if (is_null(self::$keyCollection)) {
			self::$keyCollection = $this->getCache('eCC_keyCollection');
		}

		return self::$keyCollection;
	}

	/**
	 * Will set an entry  with value $value in a cache identified by the key $key
	 * with cache lifetime $cacheTime.
	 * You should not store pure bools in this one, convert to integer or pack it
	 * in object or array.
	 *
	 * @param string $key identifier
	 * @param mixed $value the value to be stored
	 * @param $cacheTime integer lifetime, seconds
	 * @return bool
	 */
	public function setCache ($key, $value, $cacheTime = null) {
		if (is_null($cacheTime)) {
			$cacheTime = $this->cacheTime;
		}

		if ($this->enableCache == self::CACHE_APC) {
			return apc_store($key, $value, $cacheTime);
		} else if ($this->enableCache == self::CACHE_WP) {
			return set_transient($key, $value, $cacheTime);
		} else {
			return false;
		}
	}

	/**
	 * Looks up cache entry with identifier $key
	 * returns data upon success
	 * returns bool false if not found
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getCache ($key) {
		if ($this->enableCache == self::CACHE_APC) {
			$data = apc_fetch($key, $success);

			if ( ! $success ) {
				return false;
			} else {
				return $data;
			}
		} else if ($this->enableCache == self::CACHE_WP) {
			return get_transient($key);
		} else {
			return false;
		}
	}

	public function clearCache () {
		if ($this->enableCache == self::CACHE_NONE) {
			return false;
		}

		$this->getCacheKeyCollection();

		if ($this->enableCache == self::CACHE_APC) {

			foreach (self::$keyCollection as $k) {
				apc_delete($k);
			}

		} else if ($this->enableCache == self::CACHE_WP) {

			foreach (self::$keyCollection as $k) {
				delete_transient($k);
			}

		}

		self::$keyCollection = array();

		$this->saveCacheKeyCollection();
	}

	/**
	 * This function will get the data from the server
	 * and cache it for a period, say 5 seconds.
	 * It will handle the data as json.
	 * @param string $action At what action should the query be used on
	 * @param array $arguments Arguments used in the query
	 * @param bool $rawString Whether the function should return the result via json_decode() or not (raw string)
	 * @param bool $enableCache Whether the function should use or not use cache, if cache is turned on.
	 * @param integer $cacheTime seconds, if this particular request should have a different cache lifetime than ordinary.
	 * @return mixed
	 */
	private function getData ($action, array $arguments, $rawString = false, $enableCache = true, $cacheTime = null) {
		$query_args = '?';

		// Putting together the query string
		foreach ($arguments as $k => $v) {
			if (!empty($v)) {
				if (is_array($v)) {
					$v = $this->makeCommaList($v);
				}

				$query_args .= $k . '=' . $v . '&';
			}
		}
		
		$query_args = substr($query_args, 0, -1);

		$urlComplete = $this->url . $action .  $query_args;

		//echo $urlComplete . "\n";

		if ($this->enableCache && $enableCache) {
			// if we've enabled the cache, we check if the key exists for this query.
			$cache_key = 'eCC_' . md5($urlComplete);

			$cache_data = $this->getCache($cache_key);

			if ($cache_data === false) {
				$cache_data = $this->getContent($urlComplete);

				if ($cache_data !== false) {
					$this->setCache($cache_key, $cache_data, (is_null($cacheTime) ? $this->cacheTime : $cacheTime));
					// This entry is for backup if the backend doesn't respond. Store nearly forever (1 year), avoid autoloading of past events.
					$this->setCache($cache_key . '_backup', $cache_data, 365 * 24 * 60 * 60);

					$this->setCacheKey($cache_key);
					$this->setCacheKey($cache_key . '_backup');
				} else {
					$cache_data = $this->getCache($cache_key . '_backup');
				}
			}
		} else {
			$cache_data = $this->getContent($urlComplete);
		}

		if ($rawString) {
			return $cache_data;
		} else {
			return json_decode($cache_data);
		}
	}

	/**
	 * This function will take an array and return it's components as
	 * a string, where each component is separated by a comma.
	 * Should primarily be used for numbers.
	 * @param array $args Array, eg array(1 ,2, 3, 4)
	 * @return string
	 */
	private function makeCommaList (array $args) {
		$list = '';

		foreach ($args as $v) {
			if (!is_object($v) && !is_array($v)) {
				// Replace all occurrenceses of commas and ampersands.
				$v = str_replace(array(',', '&'), array('', ''), $v);
				$list .= urlencode($v) . ',';
			}
		}

		if (strlen($list) > 0) {
			return substr($list, 0, -1);
		} else {
			return '';
		}
	}

	/**
	 * Returns list of arrangers
	 * @return array
	 */
	public function arrangerList () {
		$args = array();

		return $this->getData('arranger/list', $args);
	}

	/**
	 * Returns an arranger with id $id
	 * @param $id int arranger's id
	 * @return array
	 */
	public function arranger ($id = 0) {
		$args = array();
		if ($id > 0) {
			$args['id'] = $id;
		}

		return $this->getData('arranger/get', $args);
	}

	/**
	 * Returns list of locations
	 * @return array
	 */
	public function locationList () {
		$args = array();

		return $this->getData('location/list', $args);
	}

	/**
	 * Returns list of locations
	 * @return array
	 */
	public function categoryList () {
		$args = array();

		return $this->getData('category/list', $args);
	}

	/**
	 * Returns list of festivals
	 * @return array
	 */
	public function festivalList (array $args = array()) {
		return $this->getData('festival/list', $args);
	}

	/**
	 * Returns list of upcoming events, default maximum 20
	 * @param integer $limit Maximum number of events to pull
	 * @return array
	 */
	public function upcomingEvents ($limit = 20) {
		return $this->getData('upcomingEvents', array('limit' => intval($limit)));
	}

	/**
	 * Return list of filtered events
	 * @param array $args Array of arguments to pass on to backend.
	 * @return array
	 */
	public function eventList (array $filter, $limit = 100, $offset = 0) {
		if (empty($filter['limit'])) {
			$filter['limit'] = $limit;
		}

		if (empty($filter['offset'])) {
			$filter['offset'] = $offset;
		}

		return $this->getData('filteredEvents' , $filter );
	}
	
	/**
	 * Return list of filtered events
	 * @param array $filter Array of eventual filters.
	 * @return array
	 */
	public function filteredHistoryList (array $filter = array()) {
		/**
		 * $filter can contain arranger_id, category_id, location_id, master_location_id
		 * example: array(
		 *  'arranger_id' => array(1,2,3,4),
		 *  'category_id' => 1,
		 *  'location_id' => '1,2,3,4'
		 * );
		 */
		return $this->getData('historyList' , $filter );
	}

	/**
	 * Returns a specific event with id $id
	 * @param integer $id Event id
	 * @param array $args Other arguments
	 * @return array
	 */
	public function event($id, array $args = array()) {
		$args['id'] = intval($id);
		$data = $this->getData('event/get', $args);

		if ($data->count > 0) {
			return $data->data[0];
		} else {
			return array();
		}
	}

	/**
	 * Returns a specific festival with id $id
	 * @param integer $id Festival id
	 * @param array $args Other arguments
	 * @return array
	 */
	public function festival($id, array $args = array()) {
		$args['id'] = intval($id);
		return $this->getData('festival/get', $args);
	}

	public function translate($eventObject) {
		$metaNames = array(
			"dak_event_id" => "id",
			"dak_event_provider" => "provider",

			"dak_event_title" => "title", // not to be stored as actual meta data
			"dak_event_leadParaph" => "lead_paragraph", // not to be stored as actual meta data
			"dak_event_description" => "description", // not to be stored as actual meta data

			"dak_event_url" => "url",
			"dak_event_ical" => "ical",
			"dak_event_linkout" => "linkout",
			"dak_event_startDate" => "start_date",
			"dak_event_startTime" => "start_time",
			"dak_event_endDate" => "end_date",
			"dak_event_endTime" => "end_time",
			"dak_event_is_accepted" => "is_accepted",
			"dak_event_is_public" => "is_public",
			"dak_event_customLocation" => "custom_location",
			"dak_event_commonLocation_id" => "common_location_id",
			"dak_event_commonLocation_name" => "common_location_name",
			"dak_event_location_id" => "location_id",
			"dak_event_arranger_id" => "arranger_id",
			"dak_event_arranger_name" => "arranger_name",
			"dak_event_arranger_logo" => "arranger_logo",
			"dak_event_arranger_description" => "arranger_description",
			"dak_event_festival_id" => "festival_id",
			"dak_event_primaryPicture_url" => "primary_picture_url",
			"dak_event_primaryPicture_desc" => "primary_picture_desc",
			"dak_event_covercharge" => "covercharge",
			"dak_event_age_limit" => "age_limit",
			"dak_event_created_at" => "created_at",
			"dak_event_updated_at" => "updated_at",
			"dak_event_arranger" => "arranger",
			"dak_event_categories" => "categories",
			"dak_event_festival" => "festival",
		);

		$metaData = array();

		$this->addMetaToPostArray($metaNames, $eventObject, $metaData, 'dak_event');

		return $metaData;
	}

	public function extractCategories($eventObject) {
		$categories = array();		

		foreach ($eventObject->categories as $category) {
			$categories[] = $category->name;
		}

		return $categories;
	}
}

//$eventsCalendar = new eventsCalendarClient($url, null, 0);

//print_r ( $eventsCalendar->upcomingEvents() );
//print_r ( $eventsCalendar->filteredEventsList(array('arranger_id' => 1)) );
