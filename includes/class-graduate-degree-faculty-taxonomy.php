<?php

class WSUWP_Graduate_Degree_Faculty_Taxonomy {
	/**
	 * @since 0.4.0
	 *
	 * @var WSUWP_Graduate_Degree_Faculty_Taxonomy
	 */
	private static $instance;

	/**
	 * The slug used to register the faculty taxonomy.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	public $taxonomy_slug = 'gs-faculty';

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.4.0
	 *
	 * @return \WSUWP_Graduate_Degree_Faculty_Taxonomy
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_Degree_Faculty_Taxonomy();
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

		add_action( "{$this->taxonomy_slug}_add_form_fields", array( $this, 'term_add_form_fields' ), 10 );
		add_action( "created_{$this->taxonomy_slug}", array( $this, 'save_new_term_form_fields' ) );

		add_action( "{$this->taxonomy_slug}_edit_form_fields", array( $this, 'term_edit_form_fields' ), 10 );
		add_action( "edit_{$this->taxonomy_slug}", array( $this, 'save_term_form_fields' ) );

		add_filter( "manage_edit-{$this->taxonomy_slug}_columns", array( $this, 'add_custom_columns' ) );
		add_filter( "manage_edit-{$this->taxonomy_slug}_sortable_columns", array( $this, 'add_custom_columns' ) );
		add_filter( "manage_{$this->taxonomy_slug}_custom_column", array( $this, 'manage_custom_columns' ), 10, 3 );

		add_action( 'parse_term_query', array( $this, 'sort_custom_columns' ) );
	}

	/**
	 * Registers the faculty taxonomy that will track faculty members that should be
	 * displayed in a degree program's factsheet.
	 *
	 * @since 0.4.0
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => 'Faculty Members',
			'singular_name' => 'Faculty Member',
			'search_items'  => 'Search faculty members',
			'all_items'     => 'All Faculty',
			'edit_item'     => 'Edit Faculty Member',
			'update_item'   => 'Update Faculty Member',
			'add_new_item'  => 'Add New Faculty Member',
			'new_item_name' => 'New Faculty Member',
			'menu_name'     => 'Faculty Members',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'Faculty associated with degree program factsheets.',
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
	 * Generates a unique ID to maintain future relationships when a new
	 * faculty member is created.
	 *
	 * @since 0.11.0
	 *
	 * @param int $term_id
	 */
	public function save_new_term_form_fields( $term_id ) {
		check_admin_referer( 'add-tag', '_wpnonce_add-tag' );

		$uuid = wp_generate_uuid4();
		update_term_meta( $term_id, 'gs_relationship_id', $uuid );

		$this->save_common_term_fields( $term_id );
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

		$this->save_common_term_fields( $term_id );
	}

	/**
	 * Handles the saving of common fields used in the add and edit term forms.
	 *
	 * @since 0.11.0
	 *
	 * @param int $term_id
	 */
	private function save_common_term_fields( $term_id ) {

		if ( isset( $_POST['degree_abbreviation'] ) ) { // WPCS: CSRF ok. (Nonce already checked)
			update_term_meta( $term_id, 'gs_degree_abbreviation', sanitize_text_field( $_POST['degree_abbreviation'] ) ); // WPCS: CSRF ok. (Nonce already checked)
		}

		if ( isset( $_POST['email'] ) ) { // WPCS: CSRF ok. (Nonce already checked)
			update_term_meta( $term_id, 'gs_faculty_email', sanitize_email( $_POST['email'] ) ); // WPCS: CSRF ok. (Nonce already checked)
		}

		if ( isset( $_POST['faculty_location'] ) ) { // WPCS: CSRF ok. (Nonce already checked)
			update_term_meta( $term_id, 'gs_faculty_location', sanitize_text_field( $_POST['faculty_location'] ) ); // WPCS: CSRF ok. (Nonce already checked)
		}

		if ( isset( $_POST['faculty_url'] ) ) { // WPCS: CSRF ok. (Nonce already checked)
			update_term_meta( $term_id, 'gs_faculty_url', sanitize_text_field( $_POST['faculty_url'] ) ); // WPCS: CSRF ok. (Nonce already checked)
		}

		if ( isset( $_POST['research_interests'] ) ) { // WPCS: CSRF ok. (Nonce already checked)
			update_term_meta( $term_id, 'gs_research_interests', wp_kses_post( $_POST['research_interests'] ) ); // WPCS: CSRF ok. (Nonce already checked)
		}
	}


	/**
	 * Retrieves all of the expected faculty meta assigned to a term.
	 *
	 * @since 0.7.0
	 *
	 * @param int $term_id
	 *
	 * @return array
	 */
	public static function get_all_term_meta( $term_id ) {
		$term_meta = array();

		$term_meta['uuid'] = get_term_meta( $term_id, 'gs_relationship_id', true );
		$term_meta['degree_abbreviation'] = get_term_meta( $term_id, 'gs_degree_abbreviation', true );
		$term_meta['email'] = get_term_meta( $term_id, 'gs_faculty_email', true );
		$term_meta['faculty_location'] = get_term_meta( $term_id, 'gs_faculty_location', true );
		$term_meta['url'] = get_term_meta( $term_id, 'gs_faculty_url', true );
		$term_meta['research_interests'] = get_term_meta( $term_id, 'gs_research_interests', true );

		return $term_meta;
	}

	/**
	 * Captures information about a faculty member on the new term input screen.
	 *
	 * @since 0.11.0
	 */
	public function term_add_form_fields() {
		?>
		<div class="form-field">
			<label for="degree-abbreviation">Degree abbreviation</label>
			<input type="text" name="degree_abbreviation" id="degree-abbreviation" value="" />
		</div>
		<div class="form-field">
			<label for="email">Email</label>
			<input type="text" name="email" id="email" value="" />
		</div>
		<div class="form-field">
			<label for="faculty_location">Campus/Location</label>
			<input type="text" name="faculty_location" id="faculty_location" value="" />
		</div>
		<div class="form-field">
			<label for="faculty_url">URL</label>
			<input type="text" name="faculty_url" id="faculty_url" value="" />
		</div>
		<div class="form-field">
			<label for="research-interests">Research interests</label>
			<textarea name="research_interests" id="research-interests" rows="5" cols="50" class="large-text"></textarea>
		</div>
		<?php
	}

	/**
	 * Captures information about a faculty member as term meta.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Term $term
	 */
	public function term_edit_form_fields( $term ) {
		$term_meta = self::get_all_term_meta( $term->term_id );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="faculty-uuid">Unique ID</label>
			</th>
			<td>
				<input type="text" disabled id="faculty-uuid" value="<?php echo esc_attr( $term_meta['uuid'] ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="degree-abbreviation">Degree abbreviation</label>
			</th>
			<td>
				<input type="text" name="degree_abbreviation" id="degree-abbreviation" value="<?php echo esc_attr( $term_meta['degree_abbreviation'] ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="email">Email</label>
			</th>
			<td>
				<input type="text" name="email" id="email" value="<?php echo esc_attr( $term_meta['email'] ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="faculty_location">Campus/Location:</label>
			</th>
			<td>
				<input type="text" name="faculty_location" id="faculty_location" value="<?php echo esc_attr( $term_meta['faculty_location'] ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="url">URL</label>
			</th>
			<td>
				<input type="text" name="faculty_url" id="faculty_url" value="<?php echo esc_attr( $term_meta['url'] ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="research-interests">Research interests</label>
			</th>
			<td>
				<textarea name="research_interests" id="research-interests" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $term_meta['research_interests'] ); ?></textarea>
			</td>
		</tr>
		<?php
	}

	/**
	 * Modifies the columns displayed in the faculty member terms list table.
	 *
	 * @since 0.11.0
	 *
	 * @return array
	 */
	public function add_custom_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => 'Name',
			'email' => 'Email',
			'url' => 'URL',
			'slug' => 'Slug',
			'posts' => 'Count',
		);

		return $columns;
	}

	/**
	 * Displays additional data in the custom columns added to the faculty members
	 * list table.
	 *
	 * @since 0.11.0
	 *
	 * @param string $content     The current content for the column.
	 * @param string $column_slug The slug representing the column.
	 * @param int    $term_id     ID of the term being displayed.
	 *
	 * @return string Replacement content for the column.
	 */
	public function manage_custom_columns( $content, $column_slug, $term_id ) {
		if ( 'email' === $column_slug ) {
			$content = sanitize_email( get_term_meta( $term_id, 'gs_faculty_email', true ) );
		}

		if ( 'url' === $column_slug ) {
			$content = sanitize_text_field( get_term_meta( $term_id, 'gs_faculty_url', true ) );
		}

		return $content;
	}

	/**
	 * Sorts the term list table by custom column.
	 *
	 * @since 0.11.0
	 *
	 * @param WP_Term_Query $term_query
	 */
	public function sort_custom_columns( $term_query ) {
		if ( ! isset( $_REQUEST['orderby'] ) || ! isset( $_REQUEST['order'] ) ) { // WPCS: CSRF Ok.
			return;
		}

		if ( 'Email' === $_REQUEST['orderby'] ) { // WPCS: CSRF Ok.
			$term_query->query_vars['orderby'] = 'meta_value';
			$term_query->query_vars['meta_key'] = 'gs_faculty_email';
		} elseif ( 'URL' === $_REQUEST['orderby'] ) { // WPCS: CSRF Ok.
			$term_query->query_vars['orderby'] = 'meta_value';
			$term_query->query_vars['meta_key'] = 'gs_faculty_url';
		}
	}
}
