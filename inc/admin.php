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

	public function admin_menu() {
		add_options_page( 'My Plugin', 'My Plugin', 'manage_options', 'my-plugin', array( $this, 'my_options_page' ) );
	}

	public function admin_init() {
		register_setting( 'my-settings-group', 'my-setting' );
		add_settings_section( 'section-one', 'Section One', array( $this, 'section_one_callback' ), 'my-plugin' );
		add_settings_field( 'field-one', 'Field One', array( $this, 'field_one_callback' ), 'my-plugin', 'section-one' );
	}

	public function section_one_callback() {
		echo 'Some help text goes here.';
	}

	public function field_one_callback() {
		$setting = esc_attr( get_option( 'my-setting' ) );
		echo "<input type='text' name='my-setting' value='$setting' />";
	}

	public function my_options_page() {
		?>
		<div class="wrap">
			<h2>My Plugin Options</h2>
			<form action="options.php" method="POST">
				<?php settings_fields( 'my-settings-group' ); ?>
				<?php do_settings_sections( 'my-plugin' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

