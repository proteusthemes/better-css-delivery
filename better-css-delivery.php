<?php
/*
Plugin Name: Better-css-delivery
Version: 0.1-alpha
Description: Improves loading of CSS assets, utilizing modern approaches like critical CSS and loadCSS
Author: Primoz Cigler
Author URI: https://www.proteusthemes.com/
Plugin URI: TODO
Text Domain: better-css-delivery
Domain Path: /languages
*/

class BetterCSSDelivery  {
	private static $instance;

	protected $handles_to_dequeue;

	protected function __construct() {
		$this->handles_to_dequeue = array( 'structurepress-main', 'structurepress-woocommerce' );

		add_action( 'wp_print_styles', array( $this, 'dequeue' ) );
		add_action( 'wp_footer', array( $this, 'footer_debug' ), 99 );
	}

	/**
	 * Returns the Singleton instance of this class.
	 * @return BetterCSSDelivery
	 */
	public static function get_instance() {
		if (null === static::$instance) {
				static::$instance = new static();
		}

		return static::$instance;
	}

	public function footer_debug() {
		print_r( wp_styles() );
	}

	public function dequeue() {
		foreach ( $this->handles_to_dequeue as $handle ) {
			wp_dequeue_style( $handle );
		}
	}
}

BetterCSSDelivery::get_instance();