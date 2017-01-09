<?php

/**
 * Plugin Name: Fake News Alerter Server
 * Description: Server for a theoretical fake news detector
 * Version:     .2
 * Author:      Ben Greeley
 * Author URI:  http://www.bengreeley.com/
 */

namespace FakeNewsAlerter;

// Include required files
require_once( __DIR__ . '/includes/FakeURL.php' );
require_once( __DIR__ . '/includes/FakeURLPostType.php' );
require_once( __DIR__ . '/includes/Endpoints.php' );


class FakeNewsAlerterServer {

	public $fakeurl;
	public $endpoints;

	public function __construct() {
		$this->endpoints = new Endpoints();
		$this->fakeurl   = new FakeURL();
	}

	public function register() {
		$this->fakeurl->register();
		$this->endpoints->register();
	}

}

$fake_news_server = new FakeNewsAlerterServer();
$fake_news_server->register();