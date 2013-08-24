<?php

require_once(__DIR__ . '/calendarClient.php');

class linticketCalendarClient implements calendarClient {

	/**
	 * Holds url of event server
	 */
	private $url;

	private $getContentMethod;

	public function __construct ($url) {
		$this->url = $url;

		if (function_exists('curl_init')) {
			$this->getContentMethod = 'curl';
		} else {
			$this->getContentMethod = 'file_get_contents';
		}
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

		$data = $this->getContent($urlComplete);

		if ($rawString) {
			return $data;
		} else {
			return json_decode($data);
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
	 * Return list of filtered events
	 * @param array $args Array of arguments to pass on to backend.
	 * @return array
	 */
	public function eventsList (array $filter, $fetchAll = false) {

	}

	public function event($id, array $args = array()) {
		return object();
	}
}

