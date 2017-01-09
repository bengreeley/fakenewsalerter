<?php

namespace FakeNewsAlerter;

class Endpoints {

	public $base_endpoint = 'urlchecker/v1/';

	public function __construct() {

	}

	// Register any actions and hooks for this class.
	public function register() {
		add_action( 'wp_enqueue_scripts', 	[ $this, 'enqueue_scripts' ] );
		add_action( 'rest_api_init', 		[ $this, 'register_endpoints' ] );
	}

	/**
	 * Register API endpoints.
	 */
	public function register_endpoints() {

		// /urlchecker/v1/checklinks/*
		register_rest_route(
			$this->base_endpoint,
			'checklinks', [
				'methods' 				=> 'GET',
				'callback' 				=> [ $this, 'check_links' ],
				'args' 					=> [
					'urls' => [
						'required' 			=> true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Endpoint to submit a new link
		// /urlchecker/v1/addlink/*
		register_rest_route(
			$this->base_endpoint,
			'addlink', [
				'methods' 				=> 'GET',
				'callback' 				=> [ $this, 'add_link' ],
				'args' 					=> [
					'url' => [
						'required' 			=> true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	public function check_links( \WP_REST_Request $request ) {

		$parameters = $request->get_params();
		$results    = [];
		$urls       = [];

		// Get the urls that were passed
		if( ! empty( $parameters['urls'] ) ) {
			$urls = array_filter( $urls, FILTER_VALIDATE_URL );
		}

		// Check to see which URLs are in the database
		foreach( $urls as $url ) {

		}

		if ( $results ) {
			wp_send_json_success( $results );
		} else {
			wp_send_json_error();
		}
	}

	public function add_link( \WP_REST_Request $request ) {

	}

}