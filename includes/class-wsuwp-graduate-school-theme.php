<?php

class WSUWP_Graduate_School_Theme {
	/**
	 * @since 0.5.0
	 *
	 * @var string String used for busting cache on scripts.
	 */
	public $script_version = '1.4.1';

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
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'template_redirect', array( $this, 'redirect_certificate_urls' ) );
		add_action( 'spine_enqueue_styles', array( $this, 'enqueue_print_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
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

	/**
	 * Maintains custom rewrite rules.
	 *
	 * Certificates were managed directly in previous versions of the theme. Now we detect
	 * these URLs and redirect them to their factsheet equivalent.
	 *
	 * @since 0.0.1
	 * @since 1.0.0 Moved to theme class, reduced to certificates.
	 */
	public function rewrite_rules() {
		add_rewrite_rule( '^certificates/factsheet/([^/]*)/?', 'index.php?degree_id=$matches[1]', 'top' );
		add_rewrite_rule( '^certificates/?', 'index.php?certificates_load=1', 'top' );
	}

	/**
	 * Makes WordPress aware of the degree_id and certificates_load query
	 * variables handled by rewrite rules.
	 *
	 * @since 0.0.1
	 * @since 1.0.0 Moved to theme class, reduced to certificates.
	 *
	 * @param array $query_vars Current list of query_vars passed.
	 *
	 * @return array Modified list of query_vars.
	 */
	public function query_vars( $query_vars ) {
		$query_vars[] = 'degree_id';
		$query_vars[] = 'certificates_load';

		return $query_vars;
	}

	/**
	 * Redirects old certificate URLs to their new URL or to the factsheets
	 * landing page.
	 *
	 * @since 1.0.0
	 *
	 * global WP_Query $wp_query
	 */
	public function redirect_certificate_urls() {
		global $wp_query;

		if ( isset( $wp_query->query_vars['degree_id'] ) && is_numeric( $wp_query->query_vars['degree_id'] ) ) {
			$degree_id = absint( $wp_query->query_vars['degree_id'] );
			WSUWP_Graduate_Degree_Programs()->redirect_factsheet_id( $degree_id );
		}

		if ( isset( $wp_query->query_vars['certificates_load'] ) ) {
			wp_safe_redirect( home_url( '/' . WSUWP_Graduate_Degree_Programs()->archive_slug . '/' ) );
			exit();
		}
	}

	/**
	 * Enqueue a print stylesheet for degree programs.
	 *
	 * @since 1.3.0
	 */
	public function enqueue_print_styles() {
		wp_enqueue_style( 'gradschool-print', get_stylesheet_directory_uri() . '/css/print.css', array(), WSUWP_Graduate_School_Theme()->theme_version(), 'print' );
	}

	/**
	 * Enqueue JavaScript used throughout the theme front-end.
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'gradschool-primary', get_stylesheet_directory_uri() . '/js/script.min.js', array( 'jquery' ), WSUWP_Graduate_School_Theme()->theme_version() );
	}
}
