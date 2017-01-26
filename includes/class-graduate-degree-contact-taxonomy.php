<?php

class WSUWP_Graduate_Degree_Contact_Taxonomy {
	/**
	 * @since 0.0.1
	 *
	 * @var WSUWP_Graduate_Degree_Contact_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the contact taxonomy.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	var $taxonomy_slug = 'gs-contact';

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
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
	 * @since 0.0.1
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 20 );
		add_action( "{$this->taxonomy_slug}_edit_form_fields", array( $this, 'term_edit_form_fields' ), 10 );
		add_action( "edit_{$this->taxonomy_slug}", array( $this, 'save_term_form_fields' ) );
	}

	/**
	 * Registers the contact taxonomy that tracks one or more pieces of contact information
	 * displayed with a degree program's factsheet.
	 *
	 * @since 0.0.1
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
		);
		register_taxonomy( $this->taxonomy_slug, array( WSUWP_Graduate_Degree_Programs()->post_type_slug ), $args );
	}

	/**
	 * Saves the additional form fields whenever a term is updated.
	 *
	 * @since 0.0.1
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

		if ( isset( $_POST['contact_name'] ) ) {
			update_term_meta( $term_id, 'gs_contact_name', sanitize_text_field( $_POST['contact_name'] ) );
		}

		if ( isset( $_POST['contact_title'] ) ) {
			update_term_meta( $term_id, 'gs_contact_title', sanitize_text_field( $_POST['contact_title'] ) );
		}

		if ( isset( $_POST['contact_department'] ) ) {
			update_term_meta( $term_id, 'gs_contact_department', sanitize_text_field( $_POST['contact_department'] ) );
		}

		if ( isset( $_POST['contact_email'] ) ) {
			update_term_meta( $term_id, 'gs_contact_email', sanitize_text_field( $_POST['contact_email'] ) );
		}

		if ( isset( $_POST['contact_address_one'] ) ) {
			update_term_meta( $term_id, 'gs_contact_address_one', sanitize_text_field( $_POST['contact_address_one'] ) );
		}

		if ( isset( $_POST['contact_address_two'] ) ) {
			update_term_meta( $term_id, 'gs_contact_address_two', sanitize_text_field( $_POST['contact_address_two'] ) );
		}

		if ( isset( $_POST['contact_city'] ) ) {
			update_term_meta( $term_id, 'gs_contact_city', sanitize_text_field( $_POST['contact_city'] ) );
		}

		if ( isset( $_POST['contact_state'] ) ) {
			update_term_meta( $term_id, 'gs_contact_state', sanitize_text_field( $_POST['contact_state'] ) );
		}

		if ( isset( $_POST['contact_postal'] ) ) {
			update_term_meta( $term_id, 'gs_contact_postal', sanitize_text_field( $_POST['contact_postal'] ) );
		}

		if ( isset( $_POST['contact_phone'] ) ) {
			update_term_meta( $term_id, 'gs_contact_phone', sanitize_text_field( $_POST['contact_phone'] ) );
		}

		if ( isset( $_POST['contact_fax'] ) ) {
			update_term_meta( $term_id, 'gs_contact_fax', sanitize_text_field( $_POST['contact_fax'] ) );
		}

		return;
	}

	/**
	 * Captures information about a contact as term meta.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Term $term
	 */
	public function term_edit_form_fields( $term ) {
		$name = get_term_meta( $term->term_id, 'gs_contact_name', true );
		$title = get_term_meta( $term->term_id, 'gs_contact_title', true );
		$department = get_term_meta( $term->term_id, 'gs_contact_department', true );
		$email = get_term_meta( $term->term_id, 'gs_contact_email', true );
		$address_one = get_term_meta( $term->term_id, 'gs_contact_address_one', true );
		$address_two = get_term_meta( $term->term_id, 'gs_contact_address_two', true );
		$city = get_term_meta( $term->term_id, 'gs_contact_city', true );
		$state = get_term_meta( $term->term_id, 'gs_contact_state', true );
		$postal = get_term_meta( $term->term_id, 'gs_contact_postal', true );
		$phone = get_term_meta( $term->term_id, 'gs_contact_phone', true );
		$fax = get_term_meta( $term->term_id, 'gs_contact_fax', true );

		?>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_name">Name</label>
			</th>
			<td>
				<input type="text" name="contact_name" id="contact_name" value="<?php echo esc_attr( $name ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_title">Title</label>
			</th>
			<td>
				<input type="text" name="contact_title" id="contact_title" value="<?php echo esc_attr( $title ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_department">Department</label>
			</th>
			<td>
				<input type="text" name="contact_department" id="contact_department" value="<?php echo esc_attr( $department ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_email">Email</label>
			</th>
			<td>
				<input type="text" name="contact_email" id="contact_email" value="<?php echo esc_attr( $email ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_address_one">Address 1</label>
			</th>
			<td>
				<input type="text" name="contact_address_one" id="contact_address_one" value="<?php echo esc_attr( $address_one ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_address_two">Address 2</label>
			</th>
			<td>
				<input type="text" name="contact_address_two" id="contact_address_two" value="<?php echo esc_attr( $address_two ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_city">City</label>
			</th>
			<td>
				<input type="text" name="contact_city" id="contact_city" value="<?php echo esc_attr( $city ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_state">State</label>
			</th>
			<td>
				<input type="text" name="contact_state" id="contact_state" value="<?php echo esc_attr( $state ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_postal">Zip Code</label>
			</th>
			<td>
				<input type="text" name="contact_postal" id="contact_postal" value="<?php echo esc_attr( $postal ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_phone">Phone</label>
			</th>
			<td>
				<input type="text" name="contact_phone" id="contact_phone" value="<?php echo esc_attr( $phone ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="contact_fax">Fax</label>
			</th>
			<td>
				<input type="text" name="contact_fax" id="contact_fax" value="<?php echo esc_attr( $fax ); ?>" />
			</td>
		</tr>
		<?php
	}
}
