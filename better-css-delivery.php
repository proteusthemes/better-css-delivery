<?php
/*
Plugin Name: Better CSS Delivery
Version: 0.1.0
Description: Improves loading of CSS assets, utilizing modern approaches like critical CSS and loadCSS
Author: Primoz Cigler
Author URI: https://www.proteusthemes.com/
Plugin URI: TODO
Text Domain: better-css-delivery
Domain Path: /languages
*/


if ( is_admin() ) {
	require_once __DIR__ . '/inc/better-css-delivery-admin.php';
	BetterCSSDeliveryAdmin::get_instance();
}

class BetterCSSDelivery {
	/**
	 * Single instance of this class
	 * @var BetterCSSDelivery
	 */
	private static $instance;

	/**
	 * Reference to WP_Styles
	 * @var WP_Styles
	 */
	private $wp_styles;

	/**
	 * Array of handles which should be dequeued and loaded async with loadCSS
	 * @var array
	 */
	protected $handles_loaded_async;

	/**
	 * Init
	 */
	protected function __construct() {
		// init properties
		$this->wp_styles = wp_styles();

		$this->handles_loaded_async = get_option( 'bcd-css-handles', array() );
		$this->critical_css         = get_option( 'bcd-critical-css', '' );

		// add wp hooks
		add_action( 'wp_print_styles', array( $this, 'loadCSS' ) );
		add_filter( 'style_loader_tag', array( $this, 'style_loader_tag' ), 10, 3 );

		if ( strlen( $this->critical_css ) ) {
			add_action( 'wp_print_styles', array( $this, 'print_critical_css' ) );
		}

		// conditionally print debug information in the foot
		if ( true === filter_input( INPUT_GET, 'debugBCD', FILTER_VALIDATE_BOOLEAN ) ) {
			add_action( 'wp_footer', array( $this, 'footer_debug' ), 9999 );
		}
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

	/**
	 * Print debug information in the footer as HTML comment
	 *
	 * https://developer.wordpress.org/reference/functions/wp_styles/
	 */
	public function footer_debug() {
		printf( '<!--%1$sRegistered and enqueued styles.%1$sFormat:%1$s| Enqueued? | <handle> | <URL>%1$s', PHP_EOL );

		foreach ( $this->wp_styles->registered as $handle => $style ) {
			printf( '| %s | %-30s | %s%s', in_array( $handle, $this->wp_styles->queue ) ? 'x' : ' ', $handle, $this->css_href( $style->src, $style->ver, $this->wp_styles->base_url ), PHP_EOL );
		}

		echo '-->' . PHP_EOL;
	}

	/**
	 * Where the magic happens. Prints loadCSS JS function in the head of the page
	 * and loads the stylesheets asynchronously.
	 */
	public function loadCSS() {
		echo PHP_EOL;
		?><script type="text/javascript">!function(a){"use strict";var b=function(b,c,d){var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",g.parentNode.insertBefore(f,c?g:g.nextSibling);var j=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){j(a)})};return f.onloadcssdefined=j,j(function(){f.media=d||"all"}),f};"undefined"!=typeof module?module.exports=b:a.loadCSS=b}("undefined"!=typeof global?global:this);</script><?php
		echo PHP_EOL;
	}

	public function style_loader_tag( $tag, $handle, $href ) {
		if ( in_array( $handle, $this->handles_loaded_async ) ) {
			if ( isset( $this->wp_styles->registered[ $handle ]->args ) ) {
				$media = esc_attr( $this->wp_styles->registered[ $handle ]->args );
			} else {
				$media = 'all';
			}
			return "<script id='$handle-loadcss'>loadCSS('$href', false, '$media' );</script>\n";
		}

		return $tag;
	}

	public function print_critical_css() {
		printf( '<style type="text/css">%s</style>', $this->critical_css );
		echo PHP_EOL;
	}

	/**
	 * Similar to https://developer.wordpress.org/reference/classes/wp_styles/_css_href/
	 *
	 * @return url
	 */
	protected function css_href( $src, $ver, $base_url ) {
		if ( ! is_bool( $src ) && ! preg_match( '|^(https?:)?//|', $src ) && ! ( $this->wp_styles->content_url && 0 === strpos( $src, $this->wp_styles->content_url ) ) ) {
			$src = $base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		return esc_url( $src );
	}
}

BetterCSSDelivery::get_instance();
