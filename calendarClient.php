<?php

interface calendarClient {

	/**
	 * Return list of filtered events
	 * @param array $args Array of arguments to pass on to backend.
	 * @return array
	 */
	public function eventsList (array $filter, $fetchAll = false);
	
	/**
	 * Returns a specific event with id $id
	 * @param integer $id Event id
	 * @param array $args Other arguments
	 * @return array
	 */
	public function event($id, array $args = array());

}
