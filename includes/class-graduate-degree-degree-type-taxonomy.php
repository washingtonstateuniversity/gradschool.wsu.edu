<?php

class WSUWP_Graduate_Degree_Degree_Type_Taxonomy {
	/**
	 * @since 0.4.0
	 *
	 * @var WSUWP_Graduate_Degree_Degree_Type_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the program name taxonomy.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	public $taxonomy_slug = 'gs-degree-type';

	/**
	 * A list of term meta keys associated with degree types.
	 *
	 * @since 0.8.0
	 *
	 * @var array
	 */
	public $term_meta_keys = array(
		'gs_degree_type_classification' => array(
			'description' => 'Degree Classification',
			'type' => 'string',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Degree_Type_Taxonomy::sanitize_degree_classification',
		),
	);

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.4.0
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
	 * @since 0.4.0
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 20 );
		add_action( 'init', array( $this, 'register_meta' ), 25 );
		add_action( "{$this->taxonomy_slug}_edit_form_fields", array( $this, 'term_edit_form_fields' ), 10 );
		add_action( "edit_{$this->taxonomy_slug}", array( $this, 'save_term_form_fields' ) );
	}

	/**
	 * Registers the degree type taxonomy that will track the types of degrees listed
	 * as degree programs.
	 *
	 * @since 0.4.0
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


	/**
	 * Registers the meta keys used with degree type data.
	 *
	 * @since 0.8.0
	 */
	public function register_meta() {
		foreach ( $this->term_meta_keys as $key => $args ) {
			$args['show_in_rest'] = true;
			$args['single'] = true;

			register_meta( 'term', $key, $args );
		}
	}

	/**
	 * Saves the additional form fields whenever a term is updated.
	 *
	 * @since 0.8.0
	 *
	 * @param int $term_id The ID of the term being edited.
	 */
	public function save_term_form_fields( $term_id ) {
		global $wp_list_table;

		if ( 'editedtag' !== $wp_list_table->current_action() ) {
			return;
		}

		// Reuse the default nonce that is checked in `edit-tags.php`.
		check_admin_referer( 'update-tag_' . $term_id );

		$keys = get_registered_meta_keys( 'term' );

		foreach ( $this->term_meta_keys as $key => $meta ) {
			if ( isset( $_POST[ $key ] ) && isset( $keys[ $key ] ) && isset( $keys[ $key ]['sanitize_callback'] ) ) {
				// Each piece of meta is registered with sanitization.
				update_term_meta( $term_id, $key, $_POST[ $key ] );
			}
		}
	}

	/**
	 * Retrieves all of the expected degree type meta assigned to a term.
	 *
	 * @since 0.8.0
	 *
	 * @param int $term_id
	 *
	 * @return array
	 */
	public static function get_all_term_meta( $term_id ) {
		$term_meta = get_registered_metadata( 'term', $term_id );

		return $term_meta;
	}

	/**
	 * Captures information about a degree type as term meta.
	 *
	 * @since 0.8.0
	 *
	 * @param WP_Term $term
	 */
	public function term_edit_form_fields( $term ) {
		$term_meta = self::get_all_term_meta( $term->term_id );

		foreach ( $this->term_meta_keys as $key => $meta ) {
			if ( 'gs_degree_type_classification' !== $key ) {
				continue;
			}

			if ( isset( $term_meta[ $key ][0] ) ) {
				$selected_value = $term_meta[ $key ][0];
			} else {
				$selected_value = 'Other';
			}

			?>
			<tr class="form-field">
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?></label>
				</th>
				<td>
					<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>">
						<option value="other" <?php selected( 'other', $selected_value ); ?>>Other</option>
						<option value="doctorate" <?php selected( 'doctorate', $selected_value ); ?>>Doctorate</option>
						<option value="masters" <?php selected( 'masters', $selected_value ); ?>>Master</option>
						<option value="graduate-certificate" <?php selected( 'graduate-certificate', $selected_value ); ?>>Graduate Certificate</option>
					</select>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Sanitizes a classification for a degree type.
	 *
	 * @since 0.8.0
	 *
	 * @param string $classification
	 *
	 * @return string
	 */
	public static function sanitize_degree_classification( $classification ) {
		if ( empty( $classification ) ) {
			$classification = 'other';
		}

		if ( ! in_array( $classification, array( 'other', 'masters', 'doctorate', 'graduate-certificate' ), true ) ) {
			$classification = 'other';
		}

		return $classification;
	}
}
