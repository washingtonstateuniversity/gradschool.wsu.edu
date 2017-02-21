<?php

class WSUWP_Graduate_School_Theme {
	/**
	 * @since 0.5.0
	 *
	 * @var string String used for busting cache on scripts.
	 */
	var $script_version = '0.9.2';

	/**
	 * @since 0.5.0
	 *
	 * @var WSUWP_Graduate_School_Theme
	 */
	private static $instance;

	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @since 0.5.0
	 *
	 * @return \WSUWP_Graduate_School_Theme
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_School_Theme();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.5.0
	 */
	public function setup_hooks() {
		add_filter( 'spine_child_theme_version', array( $this, 'theme_version' ) );
	}

	/**
	 * Provide a theme version for use in cache busting.
	 *
	 * @since 0.5.0
	 *
	 * @return string
	 */
	public function theme_version() {
		return $this->script_version;
	}
}
