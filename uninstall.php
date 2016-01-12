<?php

// https://developer.wordpress.org/plugins/the-basics/uninstall-methods/

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$bcd_options = array(
	'bcd-css-handles',
	'bcd-critical-css',
);

foreach ( $bcd_options as $option_name ) {
	delete_option( $option_name );
	delete_site_option( $option_name );
}
