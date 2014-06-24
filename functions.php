<?php

add_action( 'wp_enqueue_scripts', 'gradschool_enqueue_scripts' );
/**
 * Enqueue the script required by the Grad School theme.
 */
function gradschool_enqueue_scripts() {
	// Script only contains document.ready calls, load in the footer.
	wp_enqueue_script( 'gradschool-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), spine_get_script_version(), true );
}
