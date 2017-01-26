<?php

class WSUWP_Graduate_Degree_Degree_Type_Taxonomy {
	/**
	 * @since 0.0.1
	 *
	 * @var WSUWP_Graduate_Degree_Degree_Type_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the program name taxonomy.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	var $taxonomy_slug = 'gs-degree-type';

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSUWP_Graduate_Degree_Degree_Type_Taxonomy
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_Degree_Degree_Type_Taxonomy();
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
	 * Registers the degree type taxonomy that will track the types of degrees listed
	 * as degree programs.
	 *
	 * @since 0.0.1
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => 'Degree Types',
			'singular_name' => 'Degree Type',
			'search_items'  => 'Search degree types',
			'all_items'     => 'All Degree Types',
			'edit_item'     => 'Edit Degree Type',
			'update_item'   => 'Update Degree Type',
			'add_new_item'  => 'Add New Degree Type',
			'new_item_name' => 'New Degree Type',
			'menu_name'     => 'Degree Types',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'Types of degrees offered in degree programs.',
			'public'            => false,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'rewrite'           => false,
		);
		register_taxonomy( $this->taxonomy_slug, array( WSUWP_Graduate_Degree_Programs()->post_type_slug ), $args );
	}
}
