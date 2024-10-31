<?php

if ( ! defined( 'ABSPATH' ) ) exit;

# First we define some variables
define('KBP_VERSION', 	'1.0.6' );
define('KBP_PATH', 		plugin_dir_path(KBP_FILE) );
define('KBP_BASENAME', 	plugin_basename(KBP_FILE) );
define('KBP_DIR',		plugin_dir_url(__FILE__) );
define('KBP_FOLDER',	dirname(KBP_BASENAME) );
define('KBP_THEME',		'KBP' );

/********* Autoloading classes *********/

/**
 * Load the classes
 *
 * @param 	string $class Class name
 * @return 	void
 */
function KBP_autoload( $class ) {

	static $classes = null;

	if ( $classes === null ) {

		$classes = array(
			'kbp_functions'          => KBP_PATH . 'class-functions.php',
			'kbp_shortcode'          => KBP_PATH . 'class-shortcode.php',
			'kbp_init'               => KBP_PATH . 'class-init.php',
			'klantenvertellenwidget' => KBP_PATH . 'widgets/KlantenvertellenWidget.php',
			'kbp_admin'              => KBP_PATH . 'admin/class-admin.php',
			'kbp_fetchxml'           => KBP_PATH . 'xml/class-fetchXML.php',
		);

	}

	$cn = strtolower( $class );

	if ( isset( $classes[ $cn ] ) ) {
		# Load our class
		require_once( $classes[ $cn ] );

	}
}

add_action( 'widgets_init', 'KBP_register_widgets' );

function KBP_register_widgets() {

	register_widget('KlantenvertellenWidget');

}


if ( function_exists( 'spl_autoload_register' ) ) {

	spl_autoload_register( 'KBP_autoload' );

}



if ( ! function_exists( 'spl_autoload_register' ) ) {

	//add_action( 'admin_init', 'KBP_deactivate', 1 );

} else {

	$GLOBALS['KBP_init'] = new KBP_init;
	$GLOBALS['KBP_functions'] = new KBP_functions;

	$GLOBALS['KBP_shortcode'] = new KBP_shortcode;


	if ( is_admin() ) {

		add_action( 'plugins_loaded', 'KBP_admin_init', 15 );

	} else {

		add_action( 'plugins_loaded', 'KBP_frontend_init', 15 );

	}

}

# This function fires every time the Wordpress admin is loaded
function KBP_admin_init() {

	$GLOBALS['KBP_admin'] = new KBP_admin;
	$GLOBALS['KBP_fetchXML'] = new KBP_fetchXML;

}

# This function fires every time the Wordpress frontend is loaded
function KBP_frontend_init() {

	$GLOBALS['KBP_fetchXML'] = new KBP_fetchXML;

}

# This function is fired on activation
function KBP_activate() {

	# Register our translation
	KBP_register_translation();

	# Check if there is already some HTML entered, if not, we give them some default HTML to work with.
	KBP_set_standard_settings();

}


function KBP_set_standard_settings() {

    if (get_option( 'kbp_html' ) === false) {

        # Get the contents from the default HTML
        $contents = file_get_contents( KBP_PATH . '/templates/default-html.txt' );

        # Unserialize the defaults
	    $default = unserialize($contents);

        # Set our defaults
		update_option( 'kbp_html', $default );

	}

}


# This function registers the language files
function KBP_register_translation() {
	load_plugin_textdomain( KBP_THEME, false, basename( dirname( KBP_FILE ) ) . '/languages' );
}

add_action('init', 'KBP_register_translation');

register_activation_hook( KBP_FILE, 'KBP_activate' );
