<?php

// Include functionality for handling degrees
include_once( __DIR__ . '/includes/class-grad-degrees.php' );

require_once( dirname( __FILE__ ) . '/includes/class-wsuwp-graduate-school-theme.php' );
require_once( dirname( __FILE__ ) . '/includes/class-wsuwp-graduate-degree-programs.php' );

add_action( 'after_setup_theme', 'WSUWP_Graduate_School_Theme' );
/**
 * Starts the main class controlling the theme.
 *
 * @since 0.5.0
 *
 * @return \WSUWP_Graduate_School_Theme
 */
function WSUWP_Graduate_School_Theme() {
	return WSUWP_Graduate_School_Theme::get_instance();
}

add_action( 'after_setup_theme', 'WSUWP_Graduate_Degree_Programs' );
/**
 * Starts the Graduate School degree programs functionality.
 *
 * @since 0.4.0
 *
 * @return \WSUWP_Graduate_Degree_Programs
 */
function WSUWP_Graduate_Degree_Programs() {
	return WSUWP_Graduate_Degree_Programs::get_instance();
}

/**
 * Retrieve the instance of the graduate degree faculty taxonomy.
 *
 * @since 0.4.0
 *
 * @return WSUWP_Graduate_Degree_Faculty_Taxonomy
 */
function WSUWP_Graduate_Degree_Faculty_Taxonomy() {
	return WSUWP_Graduate_Degree_Faculty_Taxonomy::get_instance();
}

/**
 * Retrieves the instance of the graduate degree program name taxonomy.
 *
 * @since 0.4.0
 *
 * @return WSUWP_Graduate_Degree_Program_Name_Taxonomy
 */
function WSUWP_Graduate_Degree_Program_Name_Taxonomy() {
	return WSUWP_Graduate_Degree_Program_Name_Taxonomy::get_instance();
}

/**
 * Retrieves the instance of the graduate degree degree type taxonomy.
 *
 * @since 0.4.0
 *
 * @return WSUWP_Graduate_Degree_Degree_Type_Taxonomy
 */
function WSUWP_Graduate_Degree_Degree_Type_Taxonomy() {
	return WSUWP_Graduate_Degree_Degree_Type_Taxonomy::get_instance();
}

/**
 * Retrieves the instance of the contact taxonomy.
 *
 * @since 0.4.0
 *
 * @return WSUWP_Graduate_Degree_Contact_Taxonomy
 */
function WSUWP_Graduate_Degree_Contact_Taxonomy() {
	return WSUWP_Graduate_Degree_Contact_Taxonomy::get_instance();
}
