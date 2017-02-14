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
	var $taxonomy_slug = 'gs-faculty';

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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( "{$this->taxonomy_slug}_edit_form_fields", array( $this, 'term_edit_form_fields' ), 10 );
		add_action( "edit_{$this->taxonomy_slug}", array( $this, 'save_term_form_fields' ) );
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
		);
		register_taxonomy( $this->taxonomy_slug, array( WSUWP_Graduate_Degree_Programs()->post_type_slug ), $args );
	}

	/**
	 * Enqueue scripts and styles used in the admin.
	 *
	 * @since 0.6.0
	 *
	 * @param string $hook_suffix
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( in_array( $hook_suffix, array( 'term.php', 'term-new.php' ), true ) && 'gs-faculty' === get_current_screen()->taxonomy ) {
			wp_enqueue_style( 'gsdp-faculty-admin', get_stylesheet_directory_uri() . '/css/faculty-admin.css', array(), WSUWP_Graduate_School_Theme()->theme_version() );
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

		if ( isset( $_POST['degree_abbreviation'] ) ) {
			update_term_meta( $term_id, 'gs_degree_abbreviation', sanitize_text_field( $_POST['degree_abbreviation'] ) );
		}

		if ( isset( $_POST['email'] ) ) {
			update_term_meta( $term_id, 'gs_faculty_email', sanitize_email( $_POST['email'] ) );
		}

		if ( isset( $_POST['faculty_url'] ) ) {
			update_term_meta( $term_id, 'gs_faculty_url', sanitize_text_field( $_POST['faculty_url'] ) );
		}

		if ( isset( $_POST['teaching_interests'] ) ) {
			update_term_meta( $term_id, 'gs_teaching_interests', wp_kses_post( $_POST['teaching_interests'] ) );
		}

		if ( isset( $_POST['research_interests'] ) ) {
			update_term_meta( $term_id, 'gs_research_interests', wp_kses_post( $_POST['research_interests'] ) );
		}

		return;
	}

	/**
	 * Captures information about a faculty member as term meta.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Term $term
	 */
	public function term_edit_form_fields( $term ) {
		$degree_abbreviation = get_term_meta( $term->term_id, 'gs_degree_abbreviation', true );
		$email = get_term_meta( $term->term_id, 'gs_faculty_email', true );
		$url = get_term_meta( $term->term_id, 'gs_faculty_url', true );
		$teaching_interests = get_term_meta( $term->term_id, 'gs_teaching_interests', true );
		$research_interests = get_term_meta( $term->term_id, 'gs_research_interests', true );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="degree-abbreviation">Degree abbreviation</label>
			</th>
			<td>
				<input type="text" name="degree_abbreviation" id="degree-abbreviation" value="<?php echo esc_attr( $degree_abbreviation ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="email">Email</label>
			</th>
			<td>
				<input type="text" name="email" id="email" value="<?php echo esc_attr( $email ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="url">URL</label>
			</th>
			<td>
				<input type="text" name="faculty_url" id="faculty_url" value="<?php echo esc_attr( $url ); ?>" />
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="teaching-interests">Teaching interests</label>
			</th>
			<td>
				<textarea name="teaching_interests" id="teaching-interests" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $teaching_interests ); ?></textarea>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="research-interests">Research interests</label>
			</th>
			<td>
				<textarea name="research_interests" id="research-interests" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $research_interests ); ?></textarea>
			</td>
		</tr>
		<?php
	}
}
