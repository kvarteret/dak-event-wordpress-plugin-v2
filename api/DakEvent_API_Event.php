<?php

class DakEvent_API_Event extends WP_JSON_CustomPostType {
	protected $base = '/dakevent/events';
	protected $type = 'dak_event';

	/**
	 * @override
	 */
	public function registerRoutes( $routes ) {
		//$routes = parent::registerRoutes( $routes );
		// $routes = parent::registerRevisionRoutes( $routes );
		// $routes = parent::registerCommentRoutes( $routes );

		// Add more custom routes here

		$routes[$this->base] = array(
			array(array($this, 'getPosts'), WP_JSON_Server::READABLE)
		);
		$routes[$this->base . '/(?P<id>\d+)'] = array(
			array(array($this, 'getPost'), WP_JSON_Server::READABLE)
		);

		return $routes;
	}

	/**
	 * @override
	 */
	public function getPosts($filter = array(), $context = 'view', $type = null, $page = 1) {
		$posts = parent::getPosts( $filter, $context, $this->type, $page );

		// do remapping;
		$mappedPosts = array();
		foreach ($posts as $eventObject) {
			$eventObject['post_meta'] = dak_event_convert_data($eventObject['post_meta']);
			$mappedPosts[] = $this->remapEvent($eventObject);
		}

		return $mappedPosts;
	}

	/**
	 * @override
	 */
	public function getPost($id, $context = 'view') {
		$event = parent::getPost( $id, $context );

		$event['post_meta'] = dak_event_convert_data($event['post_meta']);
		$event = $this->remapEvent($event);

		return $event;
	}

	public function remapEvent($eventObject) {
		$remapped = array();

		$remapped['ID'] = $eventObject['ID'];
		$remapped['title'] = $eventObject['title'];
		$remapped['excerpt'] = $eventObject['excerpt'];
		$remapped['content'] = $eventObject['content'];

		foreach ($eventObject['post_meta'] as $meta) {
			if (strpos($meta['key'], 'dak_event_') === 0) {
				$key = substr($meta['key'], strlen('dak_event_'));
				$remapped['dak_event'][$key] = $meta['value'];
			}
		}

		return $remapped;
		//return $eventObject;
	}

	/**
	 * @override
	 */
	protected function prepare_meta( $post_id ) {
		$post_id = (int) $post_id;

		$custom_fields = array();

		foreach ( (array) has_meta( $post_id ) as $meta ) {
			if ( in_array($meta['meta_key'], array('_id')) )
				continue;

			$custom_fields[] = array(
				'id'	=> $meta['meta_id'],
				'key'   => $meta['meta_key'],
				'value' => $meta['meta_value'],
			);
		}

		return apply_filters( 'json_prepare_meta', $custom_fields );
	}
}
