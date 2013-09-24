<?php

abstract class calendarClient {

	protected $getContentMethod;

	protected function __construct() {
		if (function_exists('curl_init')) {
			$this->getContentMethod = 'curl';
		} else {
			$this->getContentMethod = 'file_get_contents';
		}
	}

	/**
	 * Return list of filtered events
	 * @param array $args Array of arguments to pass on to backend.
	 * @return array
	 */
	abstract public function eventList (array $filter, $limit = 100, $offset = 0);
	
	/**
	 * Returns a specific event with id $id
	 * @param integer $id Event id
	 * @param array $args Other arguments
	 * @return object
	 */
	abstract public function event($id, array $args = array());

	abstract public function translate($eventObject);

	/**
	 * @return array of category or categories
	 */
	abstract public function extractCategories($eventObject);

	public function addMetaToPostArray($metaNames, $object, &$array, $prepend='') {
	    foreach($object as $attrib => $value) {
		//error_log("Attrib name: ".$attrib);
		if(is_object($value)) {
		    $this->addMetaToPostArray($metaNames, $value, $array, $prepend.'_'.$attrib);
		} elseif (is_array($value)) {
		    # Nothing to do here
		} else {
		    if (isset($metaNames[$prepend . '_' . $attrib])) {
		        $metaBoxName = $metaNames[$prepend.'_'.$attrib];
		        //error_log(print_r('meta box name of attrib '.$prepend.'_'.$attrib. ' and found: '.$meta_box_name, true));
		        $array['dak_event_'.$metaBoxName] = $value;
		    }
		}
	    }
	}

	protected function getContent ($url) {
		if ($this->getContentMethod == 'curl') {
			//Initialize the Curl session
			$ch = curl_init();
			//Set curl to return the data instead of printing it to the browser.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// Do not verify SSL-certificate, use with care.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			//Set the URL
			curl_setopt($ch, CURLOPT_URL, $url);

			//Execute the fetch
			$data = curl_exec($ch);

			//Close the connection
			curl_close($ch);

			return $data;
		} else if ($this->getContentMethod == 'file_get_contents') {
			return file_get_contents($url);
		} else {
			return false;
		}
	}
}
