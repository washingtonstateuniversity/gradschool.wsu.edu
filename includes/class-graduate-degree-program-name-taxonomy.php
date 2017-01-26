<?php

class WSUWP_Graduate_Degree_Program_Name_Taxonomy {
	/**
	 * @since 0.0.1
	 *
	 * @var WSUWP_Graduate_Degree_Program_Name_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the program name taxonomy.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	var $taxonomy_slug = 'gs-program-name';

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSUWP_Graduate_Degree_Program_Name_Taxonomy
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_Degree_Program_Name_Taxonomy();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.0.1
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 20 );
	}

	/**
	 * Registers the program name taxonomy that will track the programs a graduate
	 * degree is associated with.
	 *
	 * @since 0.0.1
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => 'Program Names',
			'singular_name' => 'Program Name',
			'search_items'  => 'Search program names',
			'all_items'     => 'All Program Names',
			'edit_item'     => 'Edit Program Name',
			'update_item'   => 'Update Program Name',
			'add_new_item'  => 'Add New Program name',
			'new_item_name' => 'New Program Name',
			'menu_name'     => 'Program Names',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'Programs assocated with degree factsheets.',
			'public'            => false,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'rewrite'           => false,
		);
		register_taxonomy( $this->taxonomy_slug, array( WSUWP_Graduate_Degree_Programs()->post_type_slug ), $args );
	}
}
