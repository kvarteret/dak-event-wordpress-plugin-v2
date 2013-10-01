<?php

require_once(__DIR__ . '/calendarClient.php');

class linticketCalendarClient extends calendarClient {

	/**
	 * Holds url of event server
	 */
	private $url;

	public function __construct ($url) {
		parent::__construct();
		$this->url = $url;
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
	private function getData (array $arguments, $rawString = false, $enableCache = true, $cacheTime = null) {
		$queryArgs = '?';

		// Putting together the query string

		$singleValueParams = array('Maks', 'StartFra', 'StartDato');

		foreach ($singleValueParams as $singleValue) {
			if (array_key_exists($singleValue, $arguments)) {
				$queryArgs .= $singleValue . '=' . $arguments[$singleValue] . '&';
				unset($arguments[$singleValue]);
			}
		}

		foreach ($arguments as $key => $valueArray) {
			if (!empty($v)) {
				if (!is_array($valueArray)) {
					$valueArray = array($valueArray);
				}

				foreach ($valueArray as $value) {
					$queryArgs .= $key . '[' . $value . ']=on&';
				}
			}
		}
		
		$queryArgs = substr($queryArgs, 0, -1);

		$urlComplete = $this->url .  $queryArgs;

		error_log(__CLASS__ . '->' . __FUNCTION__ . ':' . $urlComplete);

		$data = $this->getContent($urlComplete);

		if ($rawString) {
			return $data;
		} else {
			return json_decode($data);
		}
	}

	/**
	 * This implementation of eventList will interpret limit and offset as unix timestamps.
	 * This is because of LinTicket's method of query which is based around start and end dates.
	 * It appears the query is not limited by limit or offset in the actual query, but the limit and offset
	 * is only applied after the query has been executed. If you want to get all events 
	 * it is recommended you use limit and offset as unix timestamps.
	 *
	 * Return list of filtered events
	 * @param array $args Array of arguments to pass on to backend.
	 * @param limit will be interpreted as seconds
	 * @param offset will be interpreted as unix timestamp
	 * @return array
	 */
	public function eventList (array $filter, $limit = 10, $offset = 0) {

		if (empty($filter['Maks'])) {
			$filter['Maks'] = $limit;
		}

		if (empty($filter['StartFra'])) {
			$filter['StartFra'] = $offset;
		}

		if (!empty($filter['noCurrentEvents']) && $filter['noCurrentEvents'] == 1) {
			unset($filter['noCurrentEvents']);

			$filter['StartDato'] = '1990-01-01';
		}

		$res = $this->getData($filter);

		$findings = (object) array(
			'data' => $res,
			'count' => count($res),
			'limit' => $limit,
			'offset' => $offset,
			'totalCount' => 0
		);

		if ($findings->count == $findings->limit) {
			$findings->totalCount = $findings->offset + 2*$findings->limit;
		} else {
			$findings->totalCount = $findings->offset + $findings->limit;
		}

		return $findings;
	}

	public function event($id, array $args = array()) {
		$res = $this->getData(array('Arr' => $id));

		if (count($res) > 0) {
			return $res[0];
		} else {
			return array();
		}
	}

	public function translate($eventObject) {
		$metaNames = array(
			"linticket_arrid" => "id",
			"linticket_link" => "url",
			//"linticket_ical" => "ical",

			"linticket_navn" => "title", // not to be stored as actual meta data
			"linticket_teaser" => "lead_paragraph", // not to be stored as actual meta data
			"linticket_tekst" => "description", // not to be stored as actual meta data

			"linticket_dato" => "start_date",
			"linticket_starttid" => "start_time",
			"linticket_datoslutt" => "end_date",
			"linticket_slutttid" => "end_time",

			"linticket_stedid" => "common_location_id",
			"linticket_sted" => "common_location_name",
			//"linticket_location_id" => "location_id",
			"linticket_arrangoerid" => "arranger_id",
			"linticket_arrangoernavn" => "arranger_name",
			"linticket_arrangerbilde" => "arranger_logo",
			"linticket_bilde" => "primary_picture_url",
			"linticket_priser" => "covercharge",
			"linticket_eventlastchanged" => "updated_at",
		);

		$metaData = array();

		$this->addMetaToPostArray($metaNames, $eventObject, $metaData, 'linticket');

		if (!isset($metaData['dak_event_covercharge'])) {
			$metaData['dak_event_covercharge'] = "";
		}

		$metaData['dak_event_ical'] = str_replace("json", "ical", $metaData['dak_event_url']);

		return $metaData;
	}

	public function extractCategories($eventObject) {
		return array();
	}
}

