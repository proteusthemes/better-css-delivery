<?php

/**
* Admin settings
*/
class BetterCSSDeliveryAdmin {
	/**
	 * Single instance of this class
	 * @var BetterCSSDeliveryAdmin
	 */
	private static $instance;

	/**
	 * Init
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Returns the Singleton instance of this class.
	 * @return BetterCSSDeliveryAdmin
	 */
	public static function get_instance() {
		if (null === static::$instance) {
				static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Add menu to the options page
	 */
	public function admin_menu() {
		add_options_page( esc_html__( 'Better CSS delivery', 'better-css-delivery' ), esc_html__( 'Better CSS delivery', 'better-css-delivery' ), 'manage_options', 'better-css-delivery', array( $this, 'options_page' ) );
	}

	/**
	 * Register settings, sections and fields
	 */
	public function admin_init() {
		// register settings
		register_setting( 'bcd', 'bcd-css-handles', function ( $csv_handles ) {
			$handles = explode( ',', $csv_handles );

			$handles = array_map( function ( $handle ) {
				return sanitize_key( $handle );
			}, $handles );

			return implode( ',', $handles );
		} );

		register_setting( 'bcd', 'bcd-critical-css' );

		// add sections
		add_settings_section( 'section-bcd-general', esc_html__( 'General' ), '__return_false', 'better-css-delivery' );
		add_settings_section( 'section-bcd-advanced', esc_html__( 'Advanced' ), '__return_false', 'better-css-delivery' );

		// add fields
		add_settings_field( 'bcd-css-handles', 'WordPress CSS Handles', array( $this, 'input_text_field' ), 'better-css-delivery', 'section-bcd-general', array(
			'id' => 'bcd-css-handles',
			/* translators: first and second %s: <a> tags around the text, third %s: <code>?debugBCD=true</code> */
			'description' => sprintf( __( 'Comma separated list of CSS handles you want to load asynchronously. Get them from the %ssource of the first page%s (add the %s to the end of URL and scroll to the bottom of the source code).' ), sprintf( '<a href="view-source:%s?debugBCD=true" target="_blank">', home_url() ), '</a>', '<code>?debugBCD=true</code>' ),
		) );

		add_settings_field( 'bcd-critical-css', esc_html__( 'Critical inline CSS' ), array( $this, 'input_textarea' ), 'better-css-delivery', 'section-bcd-advanced', array(
			'id'          => 'bcd-critical-css',
			'description' => sprintf( __( 'Optional critical CSS that will be inlined in %s. %sHere%s is one online tool where you can generate your critical CSS.' ), '<code>&lt;head&gt;</code>', '<a href="https://jonassebastianohlsson.com/criticalpathcssgenerator/" target="_blank">', '</a>' ),
		) );
	}

	/**
	 * Text field helper
	 */
	public function input_text_field( $args ) {
		printf( '<input type="text" placeholder="e.g. woocommerce-layout,woocommerce-smallscreen" name="%2$s" class="large-text code" value="%1$s" id="%2$s">', esc_attr( get_option( $args['id'], '' ) ), esc_attr( $args['id'] ) );

		if ( isset( $args['description'] ) ) {
			echo $this->help_text( $args['description'] );
		}
	}

	/**
	 * Textarea helper
	 */
	public function input_textarea( $args ) {
		printf( '<textarea placeholder=".header{width:200px;font-size:16px}.nav{background-color:#bada55}" rows="8" name="%2$s" class="large-text code" id="%2$s">%1$s</textarea>', esc_textarea( get_option( $args['id'], '' ) ), esc_attr( $args['id'] ) );

		if ( isset( $args['description'] ) ) {
			echo $this->help_text( $args['description'] );
		}
	}

	/**
	 * Helper function to ouput help text
	 */
	protected function help_text( $desc ) {
		return sprintf( '<p class="description">%s</p>', $desc );
	}

	/**
	 * Options page output
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<form action="options.php" method="POST">
				<h1><?php esc_html_e( 'Better CSS Delivery Settings' ); ?></h1>
				<?php settings_fields( 'bcd' ); ?>
				<?php do_settings_sections( 'better-css-delivery' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
