<?php

namespace FakeNewsAlerter;

class FakeURL {

	public $post_type;

	public function __construct() {
		$this->post_type = new FakeURLPostType();
	}

	public function register() {
		$this->post_type->register();

		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		add_action( 'save_post',      [ $this, 'save_field' ] );
	}

	public function register_meta_boxes() {

		add_meta_box(
			$this->get_slug() . 'meta-box',
			'URL Information',
			[ $this, 'display_fields' ]
		);

	}

	public function get_slug() {
		return $this->post_type->slug;
	}

	/**
	 * Display information about the URL
	 */
	public function display_fields() {
		global $post;

		$value = $this->get_value( $post );
		?>
		<label for="<?php echo esc_attr( $this->get_slug() );?>">
		URL: <input
			type = "url"
	        name = "<?php echo esc_attr( $this->get_slug() );?>"
			id = "<?php echo esc_attr( $this->get_slug() );?>"
	        size = "100"
	        maxlength = "200"
	        value = "<?php echo esc_attr( $value );?>" /></label><?php
	}

	public function get_value( $post ) {

		if( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		return get_the_title( $post );

	}

	public function save_field( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if( ! empty( $_POST[ $this->get_slug() ] ) ) {

			$url_clean = sanitize_text_field(  $_POST[ $this->get_slug() ] );

			if( false === filter_var( $url_clean, FILTER_VALIDATE_URL ) ) {
				return;
			}

			remove_action( 'save_post',      [ $this, 'save_field' ] );
			wp_update_post( [
				'ID'    => $post_id,
				'post_title'  => $url_clean
			] );
			add_action( 'save_post',      [ $this, 'save_field' ] );

		}

	}

}