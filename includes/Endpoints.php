<?php

namespace FakeNewsAlerter;

class Endpoints {

	public $base_endpoint   = 'urlchecker/v1';
	public $success_message = 'Link submitted successfully';
	public $error_message   = 'Unable to submit URL, link already exists or is invalid.';

	// Register any actions and hooks for this class.
	public function register() {
		add_action( 'rest_api_init', 		[ $this, 'register_endpoints' ] );
	}

	/**
	 * Register API endpoints.
	 */
	public function register_endpoints() {

		// /urlchecker/v1/checklinks/*
		register_rest_route(
			$this->base_endpoint,
			'/checklinks', [
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
			'/addlink', [
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

	/**
	 * Check a set of passed URLs to see if it matches results in the database
	 * @param \WP_REST_Request $request
	 */
	public function check_links( \WP_REST_Request $request ) {

		$parameters = $request->get_params();
		$results    = [];
		$urls       = ! empty( $parameters['urls'] ) ? (array) $parameters['urls'] : [];

		// Get the urls that were passed
		if( ! empty( $urls ) ) {
			$urls = array_filter( $urls, function( $url ) {
					return filter_var( $url, FILTER_VALIDATE_URL );
				}
			);
		}

		if( ! empty( $urls ) ) {
			// Check to see which URLs are in the database
			foreach( $urls as $url ) {
				if( true === $this->url_exists( $url ) ) {
					$results[] = $url;
				}
			}
		}

		if ( $results ) {
			wp_send_json_success( $results );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Add link to server
	 * @param \WP_REST_Request $request
	 */
	public function add_link( \WP_REST_Request $request ) {

		$link_added = false;
		$parameters = $request->get_params();
		$url        = isset( $parameters['url'] ) ? $parameters['url'] : '';

		// @TODO: Add additional checks to make sure users don't abuse this

		if( ! empty( $url ) &&
		    ! $this->url_exists( $url ) ) {

			$status = \wp_insert_post(
				[
					'post_title'    => esc_url( $url ),
					'post_status'   => 'draft',
					'post_type'     => 'fakeurl'
				]
			);

			// Check the status of the insert
			if( (int) $status > 0 ) {
				$link_added = true;
			}

		}

		if ( true === $link_added ) {
			\wp_send_json_success( $this->success_message );
		} else {
			\wp_send_json_error( $this->error_message );
		}

	}

	/**
	 * Check if a passed URL already exists
	 * @param $url
	 *
	 * @return bool
	 */
	public function url_exists( $url ) {

		if( empty( $url ) ) {
			return false;
		}

		return ! empty (
			get_page_by_title(
				esc_url( $url ),
				OBJECT,
				$this->get_slug()
			)
		);

	}

	/**
	 * Grab the custom post type slug
	 * @return string
	 */
	public function get_slug() {

		global $fake_news_server;

		return $fake_news_server->fakeurl->get_slug();
	}

}