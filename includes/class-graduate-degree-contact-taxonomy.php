<?php

class WSUWP_Graduate_Degree_Contact_Taxonomy {
	/**
	 * @since 0.4.0
	 *
	 * @var WSUWP_Graduate_Degree_Contact_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the contact taxonomy.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	public $taxonomy_slug = 'gs-contact';

	/**
	 * A list of term meta keys associated with contacts.
	 *
	 * @since 0.7.0
	 *
	 * @var array
	 */
	public $term_meta_keys = array(
		'gs_contact_name' => array(
			'description' => 'Name',
		),
		'gs_contact_title' => array(
			'description' => 'Title',
		),
		'gs_contact_department' => array(
			'description' => 'Department',
		),
		'gs_contact_email' => array(
			'description' => 'Email',
		),
		'gs_contact_address_one' => array(
			'description' => 'Address Line 1',
		),
		'gs_contact_address_two' => array(
			'description' => 'Address Line 2',
		),
		'gs_contact_city' => array(
			'description' => 'City',
		),
		'gs_contact_state' => array(
			'description' => 'State',
		),
		'gs_contact_postal' => array(
			'description' => 'Postal Code',
		),
		'gs_contact_phone' => array(
			'description' => 'Phone',
		),
		'gs_contact_fax' => array(
			'description' => 'Fax',
		),
	);

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.4.0
	 *
	 * @return \WSUWP_Graduate_Degree_Contact_Taxonomy
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_Degree_Contact_Taxonomy();
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
	 * Registers the contact taxonomy that tracks one or more pieces of contact information
	 * displayed with a degree program's factsheet.
	 *
	 * @since 0.4.0
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => 'Contacts',
			'singular_name' => 'Contact',
			'search_items'  => 'Search contacts',
			'all_items'     => 'All Contacts',
			'edit_item'     => 'Edit Contact',
			'update_item'   => 'Update Contact',
			'add_new_item'  => 'Add New Contact',
			'new_item_name' => 'New Contact',
			'menu_name'     => 'Contacts',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'Contacts associated with degree program factsheets.',
			'public'            => false,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		);
		register_taxonomy( $this->taxonomy_slug, array( WSUWP_Graduate_Degree_Programs()->post_type_slug ), $args );
	}

	/**
	 * Registers the meta keys used with contact data.
	 *
	 * @since 0.7.0
	 */
	public function register_meta() {
		foreach ( $this->term_meta_keys as $key => $args ) {
			$args['type'] = 'string';
			$args['sanitize_callback'] = 'sanitize_text_field';
			$args['show_in_rest'] = true;
			$args['single'] = true;

			register_meta( 'term', $key, $args );
		}
	}

	/**
	 * Saves the additional form fields whenever a term is updated.
	 *
	 * @since 0.4.0
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
	 * Retrieves all of the expected contact meta assigned to a term.
	 *
	 * @since 0.7.0
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
	 * Captures information about a contact as term meta.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Term $term
	 */
	public function term_edit_form_fields( $term ) {
		$term_meta = self::get_all_term_meta( $term->term_id );

		foreach ( $this->term_meta_keys as $key => $meta ) {
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?></label>
				</th>
				<td>
					<input type="text"
							name="<?php echo esc_attr( $key ); ?>"
							id="<?php echo esc_attr( $key ); ?>"
							value="<?php if ( isset( $term_meta[ $key ][0] ) ) { echo esc_attr( $term_meta[ $key ][0] ); } ?>" />
				</td>
			</tr>
			<?php
		}
	}
}
