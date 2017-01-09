<?php

namespace FakeNewsAlerter;

class FakeURLPostType {

	public $slug            = 'fakeurl';
	public $name_singular   = 'Fake URL';
	public $name_plural     = 'Fake URLs';

	public function register() {
		add_action( 'init', [ $this, 'create_post_type' ] );
	}

	public function create_post_type() {

		register_post_type(
			$this->slug,
			[
				'labels' => [
					'name'          => $this->name_plural,
					'singular_name' => $this->name_singular,
					'plural_name'   => $this->name_plural
				],
				'public'              => true,
				'has_archive'         => false,
				'show_in_nav_menus'   => true,
				'exclude_from_search' => true,
				'supports'            => false
			]
		);

	}

}