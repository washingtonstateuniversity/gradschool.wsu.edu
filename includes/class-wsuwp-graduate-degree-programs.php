<?php

class WSUWP_Graduate_Degree_Programs {
	/**
	 * @since 0.4.0
	 *
	 * @var WSUWP_Graduate_Degree_Programs
	 */
	private static $instance;

	/**
	 * The slug used to register the factsheet post type.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	public $post_type_slug = 'gs-factsheet';

	/**
	 * The slug used in pretty URLs.
	 *
	 * @since 0.10.0
	 *
	 * @var string
	 */
	public $archive_slug = 'degrees';

	/**
	 * A list of post meta keys associated with factsheets.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	public $post_meta_keys = array(
		'gsdp_degree_shortname' => array(
			'description' => 'Factsheet display name',
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'meta_field_callback' => array( __CLASS__, 'display_string_meta_field' ),
			'restricted' => true,
			'pre_html' => '<div class="factsheet-group">',
			'location' => 'primary',
		),
		'gsdp_degree_id' => array(
			'description' => 'Factsheet degree ID',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'meta_field_callback' => array( __CLASS__, 'display_int_meta_field' ),
			'restricted' => true,
			'location' => 'primary',
		),
		'gsdp_accepting_applications' => array(
			'description' => 'Accepting applications',
			'type' => 'bool',
			'sanitize_callback' => 'absint',
			'meta_field_callback' => array( __CLASS__, 'display_bool_meta_field' ),
			'location' => 'primary',
		),
		'gsdp_include_in_programs' => array(
			'description' => 'Include in programs list',
			'type' => 'bool',
			'sanitize_callback' => 'absint',
			'meta_field_callback' => array( __CLASS__, 'display_bool_meta_field' ),
			'location' => 'primary',
		),
		'gsdp_grad_students_total' => array(
			'description' => 'Total grad students',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'meta_field_callback' => array( __CLASS__, 'display_int_meta_field' ),
			'location' => 'primary',
		),
		'gsdp_grad_students_aided' => array(
			'description' => 'Aided grad students',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'meta_field_callback' => array( __CLASS__, 'display_int_meta_field' ),
			'location' => 'primary',
		),
		'gsdp_admission_gpa' => array(
			'description' => 'Admission GPA',
			'type' => 'float',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_gpa',
			'meta_field_callback' => array( __CLASS__, 'display_string_meta_field' ),
			'location' => 'primary',
		),
		'gsdp_degree_url' => array(
			'description' => 'Degree home page',
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'meta_field_callback' => array( __CLASS__, 'display_string_meta_field' ),
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_locations' => array(
			'description' => 'Locations',
			'type' => 'locations',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_locations',
			'meta_field_callback' => array( __CLASS__, 'display_locations_meta_field' ),
			'restricted' => true,
			'pre_html' => '<div class="factsheet-group">',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_deadlines' => array(
			'description' => 'Deadlines',
			'type' => 'deadlines',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_deadlines',
			'meta_field_callback' => array( __CLASS__, 'display_deadlines_meta_field' ),
			'pre_html' => '<div class="factsheet-group">',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_requirements' => array(
			'description' => 'Requirements',
			'type' => 'requirements',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_requirements',
			'meta_field_callback' => array( __CLASS__, 'display_requirements_meta_field' ),
			'pre_html' => '<div class="factsheet-group">',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_degree_description' => array(
			'description' => 'Description of the graduate degree',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'location' => 'secondary',
		),
		'gsdp_admission_requirements' => array(
			'description' => 'Admission requirements',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'location' => 'secondary',
		),
		'gsdp_student_opportunities' => array(
			'description' => 'Student opportunities',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'location' => 'secondary',
		),
		'gsdp_career_opportunities' => array(
			'description' => 'Career opportunities',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'location' => 'secondary',
		),
		'gsdp_career_placements' => array(
			'description' => 'Career placements',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'location' => 'secondary',
		),
		'gsdp_student_learning_outcome' => array(
			'description' => 'Student learning outcomes',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'meta_field_callback' => array( __CLASS__, 'display_textarea_meta_field' ),
			'restricted' => true,
			'location' => 'secondary',
		),
	);

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.4.0
	 *
	 * @return \WSUWP_Graduate_Degree_Programs
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Graduate_Degree_Programs();
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
		require_once dirname( __FILE__ ) . '/class-graduate-degree-faculty-taxonomy.php';
		require_once dirname( __FILE__ ) . '/class-graduate-degree-program-name-taxonomy.php';
		require_once dirname( __FILE__ ) . '/class-graduate-degree-degree-type-taxonomy.php';
		require_once dirname( __FILE__ ) . '/class-graduate-degree-contact-taxonomy.php';

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'init', array( $this, 'register_post_type' ), 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Faculty_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Program_Name_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Degree_Type_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Contact_Taxonomy', 15 );

		add_action( 'init', array( $this, 'add_mirror_grad_fair_rewrites' ) );
		add_filter( 'query_vars', array( $this, 'add_gradfair_query_var' ) );
		add_action( 'init', array( $this, 'register_mirror_menu' ) );

		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 99 );
		add_action( "save_post_{$this->post_type_slug}", array( $this, 'save_factsheet' ), 10, 2 );

		// This should fire after the filter in Editorial Access Manager.
		add_filter( 'map_meta_cap', array( $this, 'filter_map_meta_cap' ), 200, 4 );
		add_filter( 'user_has_cap', array( $this, 'allow_edit_faculty_member' ), 20, 4 );

		// Several fields are restricted to full editors or admins.
		add_filter( "auth_post_{$this->post_type_slug}_meta_gsdp_degree_id", array( $this, 'can_edit_restricted_field' ), 100, 4 );
		add_filter( "auth_post_{$this->post_type_slug}_meta_gsdp_degree_shortname", array( $this, 'can_edit_restricted_field' ), 100, 4 );
		add_filter( "auth_post_{$this->post_type_slug}_meta_gsdp_student_learning_outcome", array( $this, 'can_edit_restricted_field' ), 100, 4 );

		add_filter( 'wp_insert_post_data', array( $this, 'manage_factsheet_title_update' ), 10, 2 );

		add_action( 'pre_get_posts', array( $this, 'adjust_factsheet_archive_query' ) );
		add_action( 'template_redirect', array( $this, 'redirect_old_factsheet_urls' ) );
		add_action( 'template_redirect', array( $this, 'redirect_private_factsheets' ) );

		add_filter( 'spine_get_title', array( $this, 'filter_factsheet_archive_title' ), 10, 3 );
	}

	/**
	 * Enqueue scripts and styles used in the admin.
	 *
	 * @since 0.4.0
	 *
	 * @param string $hook_suffix
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) && 'gs-factsheet' === get_current_screen()->id ) {
			wp_deregister_script( 'yoast-seo-post-scraper' );
			wp_deregister_script( 'yoast-seo-term-scraper' );
			wp_deregister_script( 'yoast-seo-featured-image' );

			wp_enqueue_style( 'gsdp-admin', get_stylesheet_directory_uri() . '/css/factsheet-admin.css', array(), WSUWP_Graduate_School_Theme()->theme_version() );
			wp_register_script( 'gsdp-factsheet-admin', get_stylesheet_directory_uri() . '/js/factsheet-admin.min.js', array( 'jquery', 'underscore', 'jquery-ui-autocomplete' ), WSUWP_Graduate_School_Theme()->theme_version(), true );

			$rest_api_data = array(
				'contact_rest_url' => rest_url( 'wp/v2/gs-contact/' ),
				'faculty_rest_url' => rest_url( 'wp/v2/gs-faculty/' ),
			);
			wp_localize_script( 'gsdp-factsheet-admin', 'gs_factsheet', $rest_api_data );

			wp_enqueue_script( 'gsdp-factsheet-admin' );
		}

		if ( in_array( $hook_suffix, array( 'edit-tags.php', 'term.php', 'term-new.php' ), true ) && in_array( get_current_screen()->taxonomy, array( 'gs-contact', 'gs-faculty', 'gs-degree-type' ), true ) ) {
			wp_enqueue_style( 'gsdp-faculty-admin', get_stylesheet_directory_uri() . '/css/faculty-admin.css', array(), WSUWP_Graduate_School_Theme()->theme_version() );
		}
	}

	/**
	 * Enqueue JavaScript used for factsheets on the front end.
	 *
	 * @since 0.9.0
	 */
	public function wp_enqueue_scripts() {
		if ( is_post_type_archive( $this->post_type_slug ) ) {
			wp_enqueue_script( 'factsheet-archive', get_stylesheet_directory_uri() . '/js/factsheet-archive.js', array( 'jquery' ), WSUWP_Graduate_School_Theme()->theme_version() );
		}
	}

	/**
	 * Register the degree program factsheet post type.
	 *
	 * @since 0.4.0
	 */
	public function register_post_type() {
		$labels = array(
			'name' => 'Factsheets',
			'singular_name' => 'Factsheet',
			'all_items' => 'All Factsheets',
			'add_new_item' => 'Add Factsheet',
			'edit_item' => 'Edit Factsheet',
			'new_item' => 'New Factsheet',
			'view_item' => 'View Factsheet',
			'search_items' => 'Search Factsheets',
			'not_found' => 'No factsheets found',
			'not_found_in_trash' => 'No factsheets found in trash',
		);

		$args = array(
			'labels' => $labels,
			'description' => 'Graduate degree program factsheets',
			'public' => true,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-groups',
			'supports' => array(
				'title',
				'revisions',
			),
			'has_archive' => $this->archive_slug,
			'rewrite' => array(
				'slug' => $this->archive_slug . '/factsheet',
				'with_front' => false,
			),
		);
		register_post_type( $this->post_type_slug, $args );
	}

	/**
	 * Add custom rewrite rules to duplicate degree programs without navigation
	 * for the graduate fair.
	 *
	 * @since 1.4.0
	 */
	public function add_mirror_grad_fair_rewrites() {
		add_rewrite_rule( '^wsugradfair/degrees/?$', 'index.php?post_type=gs-factsheet&gradfair=1', 'top' );
		add_rewrite_rule( '^wsugradfair/degrees/factsheet/([^/]+)(?:/([0-9]+))?/?$', 'index.php?gs-factsheet=$matches[1]&page=$matches[2]&gradfair=1', 'top' );
	}

	/**
	 * Add our custom query variable to the set of default query variables.
	 *
	 * @since 1.4.0
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_gradfair_query_var( $vars ) {
		$vars[] = 'gradfair';

		return $vars;
	}

	/**
	 * Register a mirror navigation area for grad fair usage.
	 *
	 * @since 1.4.0
	 */
	public function register_mirror_menu() {
		register_nav_menus(
			array(
				'gradfair'    => 'WSU Grad Fair',
			)
		);
	}

	/**
	 * Register the meta keys used to store degree factsheet data.
	 *
	 * @since 0.4.0
	 */
	public function register_meta() {
		foreach ( $this->post_meta_keys as $key => $args ) {
			// We have several data types that are stored as strings.
			if ( 'float' === $args['type'] || 'deadlines' === $args['type'] || 'requirements' === $args['type'] ) {
				$args['type'] = 'string';
			}

			$args['show_in_rest'] = true;
			$args['single'] = true;
			register_meta( 'post', $key, $args );
		}
	}

	/**
	 * Add the meta boxes used to capture information about a degree factsheet.
	 *
	 * @since 0.4.0
	 *
	 * @param string $post_type
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this->post_type_slug !== $post_type ) {
			return;
		}

		add_meta_box( 'factsheet-primary', 'Factsheet Data', array( $this, 'display_factsheet_primary_meta_box' ), null, 'normal', 'high' );
		add_meta_box( 'factsheet-faculty', 'Faculty Members', array( $this, 'display_faculty_meta_box' ), null, 'normal', 'default' );
		add_meta_box( 'factsheet-contact', 'Contact Information', array( $this, 'display_contact_meta_box' ), null, 'normal', 'default' );
		add_meta_box( 'factsheet-secondary', 'Factsheet Text Blocks', array( $this, 'display_factsheet_secondary_meta_box' ), null, 'normal', 'default' );
	}

	/**
	 * Removes the faculty member and contact info taxonomy boxes from the
	 * factsheet screen. This data is managed via custom input in the primary
	 * meta box.
	 *
	 * @since 0.7.0
	 *
	 * @param string $post_type
	 */
	public function remove_meta_boxes( $post_type ) {
		if ( $this->post_type_slug !== $post_type ) {
			return;
		}

		remove_meta_box( 'tagsdiv-gs-faculty', $this->post_type_slug, 'side' );
		remove_meta_box( 'tagsdiv-gs-contact', $this->post_type_slug, 'side' );
		remove_meta_box( 'wpseo_meta', $this->post_type_slug, 'normal' );
	}

	/**
	 * Captures the main set of data about a degree factsheet.
	 *
	 * @since 0.4.0
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function display_factsheet_primary_meta_box( $post ) {
		$data = get_registered_metadata( 'post', $post->ID );

		wp_nonce_field( 'save-gsdp-primary', '_gsdp_primary_nonce' );

		echo '<div class="factsheet-primary-inputs">';

		foreach ( $this->post_meta_keys as $key => $meta ) {
			if ( ! isset( $data[ $key ] ) || ! isset( $data[ $key ][0] ) ) {
				$data[ $key ] = array( false );
			}

			if ( 'primary' !== $meta['location'] ) {
				continue;
			}

			$this->output_meta_box_html( $meta, $data, $key );
		}

		echo '</div>'; // End factsheet-primary-inputs.
	}

	/**
	 * Converts an old faculty relationship structure to one that uses a generated
	 * unique ID to track uniqueness.
	 *
	 * @since 1.2.0
	 *
	 * @param array   $faculty_relationships
	 * @param WP_Term $faculty_member
	 * @param string  $unique_id
	 *
	 * @return array
	 */
	private function convert_old_faculty_relationship_structure( $faculty_relationships, $faculty_member, $unique_id ) {
		$old_hash = md5( $faculty_member->name );

		if ( isset( $faculty_relationships[ $old_hash ] ) ) {
			$faculty_relationships[ $unique_id ] = $faculty_relationships[ $old_hash ];
		} elseif ( isset( $faculty_relationships[ $faculty_member->term_id ] ) ) {
			$faculty_relationships[ $unique_id ] = $faculty_relationships[ $faculty_member->term_id ];
		} else {
			$faculty_relationships[ $unique_id ] = array();
		}

		return $faculty_relationships[ $unique_id ];
	}

	/**
	 * Displays a meta box to capture faculty member information for
	 * a factsheet.
	 *
	 * @since 0.7.0
	 *
	 * @param WP_Post $post
	 */
	public function display_faculty_meta_box( $post ) {
		$faculty_members = wp_get_object_terms( $post->ID, 'gs-faculty' );
		$faculty_relationships = get_post_meta( $post->ID, 'gsdp_faculty_relationships', true );

		echo '<div class="factsheet-faculty-wrapper">';

		foreach ( $faculty_members as $faculty_member ) {
			$unique_id = get_term_meta( $faculty_member->term_id, 'gs_relationship_id', true );

			// In a rare case where a faculty member does not have a unique relationship ID, create one.
			if ( empty( $unique_id ) ) {
				$unique_id = wp_generate_uuid4();
				update_term_meta( $faculty_member->term_id, 'gs_relationship_id', $unique_id );
			}

			$faculty_relationship_defaults = array(
				'chair' => 'false',
				'cochair' => 'false',
				'sit' => 'false',
			);

			if ( ! isset( $faculty_relationships[ $unique_id ] ) ) {
				$faculty_relationships[ $unique_id ] = $this->convert_old_faculty_relationship_structure( $faculty_relationships, $faculty_member, $unique_id );
			}

			$faculty_relationships[ $unique_id ] = wp_parse_args( $faculty_relationships[ $unique_id ], $faculty_relationship_defaults );

			?>
			<div class="factsheet-faculty">
				<div class="faculty-name"><?php echo esc_html( $faculty_member->name ); ?></div>
				<div class="select-chair">
					<label for="program_chair">Chair:</label>
					<select name="faculty[<?php echo esc_attr( $faculty_member->term_id ); ?>][program_chair]" id="program_chair">
						<option value="false" <?php selected( 'false', $faculty_relationships[ $unique_id ]['chair'] ); ?>>No</option>
						<option value="true" <?php selected( 'true', $faculty_relationships[ $unique_id ]['chair'] ); ?>>Yes</option>
					</select>
				</div>
				<div class="select-cochair">
					<label for="program_cochair">Co-chair:</label>
					<select name="faculty[<?php echo esc_attr( $faculty_member->term_id ); ?>][program_cochair]" id="program_cochair">
						<option value="false" <?php selected( 'false', $faculty_relationships[ $unique_id ]['cochair'] ); ?>>No</option>
						<option value="true" <?php selected( 'true', $faculty_relationships[ $unique_id ]['cochair'] ); ?>>Yes</option>
					</select>
				</div>
				<div class="select-sit">
					<label for="program_sit">Sit:</label>
					<select name="faculty[<?php echo esc_attr( $faculty_member->term_id ); ?>][program_sit]" id="program_sit">
						<option value="false" <?php selected( 'false', $faculty_relationships[ $unique_id ]['sit'] ); ?>>No</option>
						<option value="true" <?php selected( 'true', $faculty_relationships[ $unique_id ]['sit'] ); ?>>Yes</option>
					</select>
				</div>
				<span class="edit-factsheet-faculty"><a href="<?php echo esc_url( get_edit_term_link( $faculty_member->term_id, 'gs-faculty' ) ); ?>">Edit</a></span>
				<span class="remove-factsheet-faculty">Remove</span>
			</div>
			<?php
		}

		echo '</div>'; // End of factsheet-faculty-wrapper.

		// @codingStandardsIgnoreStart
		?>
		<script type="text/template" id="factsheet-faculty-template">
			<div class="factsheet-faculty">
				<div class="faculty-name"><%= faculty_name %></div>
				<div class="select-chair">
					<label for="program_chair">Chair:</label>
					<select name="faculty[<%= term_id %>][program_chair]" id="program_chair">
						<option value="false">No</option>
						<option value="true">Yes</option>
					</select>
				</div>
				<div class="select-cochair">
					<label for="program_cochair">Co-chair:</label>
					<select name="faculty[<%= term_id %>][program_cochair]" id="program_cochair">
						<option value="false">No</option>
						<option value="true">Yes</option>
					</select>
				</div>
				<div class="select-sit">
					<label for="program_sit">Sit:</label>
					<select name="faculty[<%= term_id %>][program_sit]" id="program_sit">
						<option value="false">No</option>
						<option value="true">Yes</option>
					</select>
				</div>
				<span class="remove-factsheet-faculty">Remove</span>
			</div>
		</script>
		<div class="add-faculty-wrapper">
			<label for="faculty-entry">Add Faculty Member:</label>
			<input type="text" id="faculty-entry" value="" />
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Displays a meta box to capture contact information for a factsheet.
	 *
	 * @since 0.7.0
	 *
	 * @param WP_Post $post
	 */
	public function display_contact_meta_box( $post ) {
		$contacts = wp_get_object_terms( $post->ID, 'gs-contact' );
		$data['contacts'] = array();
		if ( ! is_wp_error( $contacts ) ) {
			foreach ( $contacts as $contact ) {
				$contact_meta = WSUWP_Graduate_Degree_Contact_Taxonomy::get_all_term_meta( $contact->term_id );
				$contact_meta['term_id'] = $contact->term_id;
				$data['contacts'][] = $contact_meta;
			}
		}

		echo '<div class="factsheet-contact-wrapper">';

		foreach ( $data['contacts'] as $contact ) {
			if ( empty( $contact ) ) {
				continue;
			}

			?>
			<div class="factsheet-contact">
				<input type="hidden" name="contacts[]" value="<?php echo esc_attr( $contact['term_id'] ); ?>" />
				<address>
					<?php if ( ! empty( $contact['gs_contact_name'][0] ) ) : ?>
						<div><?php echo esc_html( $contact['gs_contact_name'][0] ); ?></div>
					<?php endif; ?>
					<div>
						<?php if ( ! empty( $contact['gs_contact_address_one'][0] ) ) : ?>
							<div><?php echo esc_html( $contact['gs_contact_address_one'][0] ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $contact['gs_contact_address_two'][0] ) ) : ?>
							<div><?php echo esc_html( $contact['gs_contact_address_two'][0] ); ?></div>
						<?php endif; ?>
						<div>
							<?php if ( ! empty( $contact['gs_contact_city'][0] ) && ! empty( $contact['gs_contact_state'][0] ) ) : ?>
								<span><?php echo esc_html( $contact['gs_contact_city'][0] ); ?>, <?php echo esc_html( $contact['gs_contact_state'][0] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $contact['gs_contact_postal'][0] ) ) : ?>
								<span><?php echo esc_html( $contact['gs_contact_postal'][0] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php if ( ! empty( $contact['gs_contact_phone'][0] ) ) : ?>
						<div><?php echo esc_html( $contact['gs_contact_phone'][0] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $contact['gs_contact_fax'][0] ) ) : ?>
						<div><?php echo esc_html( $contact['gs_contact_fax'][0] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $contact['gs_contact_email'][0] ) ) : ?>
						<div><a href="mailto:<?php echo esc_attr( $contact['gs_contact_email'][0] ); ?>"><?php echo esc_html( $contact['gs_contact_email'][0] ); ?></a></div>
					<?php endif; ?>
				</address>
				<span class="remove-factsheet-contact">Remove</span>
			</div>
			<?php
		}

		echo '</div>' // End factsheet-contact-wrapper.

		// @codingStandardsIgnoreStart
		?>
		<script type="text/template" id="factsheet-contact-template">
			<div class="factsheet-contact">
				<input type="hidden" name="contacts[]" value="<%= contact_term_id %>" />
				<address>
					<div><%= contact_name %></div>
					<div>
						<div><%= contact_address_one %></div>
						<div><%= contact_address_two %></div>
						<div>
							<span><%= contact_city %>, <%= contact_state %></span>
							<span><%= contact_postal %></span>
						</div>
					</div>
					<div><%= contact_phone %></div>
					<div><%= contact_fax %></div>
					<div><%= contact_email %></div>
				</address>
				<span class="remove-factsheet-contact">Remove</span>
			</div>
		</script>
		<div class="add-contact-wrapper">
			<label for="contact-entry">Add Contact:</label>
			<input type="text" id="contact-entry" value="" />
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Captures the secondary set of data about a degree factsheet.
	 *
	 * @since 0.7.0
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function display_factsheet_secondary_meta_box( $post ) {
		$data = get_registered_metadata( 'post', $post->ID );

		echo '<div class="factsheet-primary-inputs">';

		foreach ( $this->post_meta_keys as $key => $meta ) {
			if ( ! isset( $data[ $key ] ) || ! isset( $data[ $key ][0] ) ) {
				$data[ $key ] = array( false );
			}

			if ( 'secondary' !== $meta['location'] ) {
				continue;
			}

			$this->output_meta_box_html( $meta, $data, $key );
		}

		echo '</div>'; // End factsheet-primary-inputs.
	}

	/**
	 * Outputs the HTML associated with the primary and secondary meta boxes.
	 *
	 * @since 0.7.0
	 *
	 * @param $meta
	 * @param $data
	 * @param $key
	 */
	public function output_meta_box_html( $meta, $data, $key ) {
		if ( isset( $meta['pre_html'] ) ) {
			echo $meta['pre_html']; // @codingStandardsIgnoreLine (HTML is static in code)
		}
		?>
		<div class="factsheet-primary-input factsheet-<?php echo esc_attr( $meta['type'] ); ?>">
		<?php

		if ( isset( $meta['meta_field_callback'] ) && is_callable( $meta['meta_field_callback'] ) ) {
			call_user_func( $meta['meta_field_callback'], $meta, $key, $data );
		}

		echo '</div>'; // End factsheet-primary-input

		if ( isset( $meta['post_html'] ) ) {
			echo $meta['post_html']; // @codingStandardsIgnoreLine (HTML is static in code)
		}
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as strings.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_string_meta_field( $meta, $key, $data ) {
		?>
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
		<?php

		if ( isset( $meta['restricted'] ) && $meta['restricted'] && $this->user_is_eam_user( wp_get_current_user()->ID, get_the_ID() ) ) {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}

		?>
		<input type="text" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $data[ $key ][0] ); ?>" <?php echo $disabled; // @codingStandardsIgnoreLine (HTML is static in code) ?> />
		<?php
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as an integer.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_int_meta_field( $meta, $key, $data ) {
		?>
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
		<?php

		if ( isset( $meta['restricted'] ) && $meta['restricted'] && $this->user_is_eam_user( wp_get_current_user()->ID, get_the_ID() ) ) {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}

		?>
		<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo absint( $data[ $key ][0] ); ?>" <?php echo $disabled; // @codingStandardsIgnoreLine (HTML is static in code) ?> />
		<?php
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as boolean.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_bool_meta_field( $meta, $key, $data ) {
		?>
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
		<select name="<?php echo esc_attr( $key ); ?>">
			<option value="0" <?php selected( 0, absint( $data[ $key ][0] ) ); ?>>No</option>
			<option value="1" <?php selected( 1, absint( $data[ $key ][0] ) ); ?>>Yes</option>
		</select>
		<?php
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as strings.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_textarea_meta_field( $meta, $key, $data ) {
		?>
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
		<?php

		if ( isset( $meta['restricted'] ) && $meta['restricted'] && $this->user_is_eam_user( wp_get_current_user()->ID, get_the_ID() ) ) {
			echo '<div id="' . esc_attr( $key ) . '" class="field-content">' . wp_kses_post( apply_filters( 'the_content', $data[ $key ][0] ) ) . '</div>';
			return;
		}

		$wp_editor_settings = array(
			'textarea_rows' => 10,
			'media_buttons' => false,
			'teeny' => true,
		);

		wp_editor( $data[ $key ][0], esc_attr( $key ), $wp_editor_settings );
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as strings.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_deadlines_meta_field( $meta, $key, $data ) {
		$field_data = maybe_unserialize( $data[ $key ][0] );

		if ( empty( $field_data ) ) {
			$field_data = array();
		}

		$default_field_data = array(
			'semester' => 'None',
			'deadline' => '',
			'international' => '',
		);
		$field_count = 0;

		?>
		<div class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-wrapper">
			<span class="factsheet-label">Deadlines:</span>
			<?php

			foreach ( $field_data as $field_datum ) {
				$field_datum = wp_parse_args( $field_datum, $default_field_data );

				?>
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<select name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][semester]">
						<option value="None" <?php selected( 'None', $field_datum['semester'] ); ?>>Not selected</option>
						<option value="Fall" <?php selected( 'Fall', $field_datum['semester'] ); ?>>Fall</option>
						<option value="Spring" <?php selected( 'Spring', $field_datum['semester'] ); ?>>Spring</option>
						<option value="Summer" <?php selected( 'Summer', $field_datum['semester'] ); ?>>Summer</option>
					</select>
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][deadline]" value="<?php echo esc_attr( $field_datum['deadline'] ); ?>" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][international]" value="<?php echo esc_attr( $field_datum['international'] ); ?>" />
					<span class="remove-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">Remove</span>
				</span>
				<?php
				$field_count++;
			}

			// If no fields have been added, provide an empty field by default.
			if ( 0 === count( $field_data ) ) {
				?>
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<select name="<?php echo esc_attr( $key ); ?>[0][semester]">
						<option value="None">Not selected</option>
						<option value="Fall">Fall</option>
						<option value="Spring">Spring</option>
						<option value="Summer">Summer</option>
					</select>
					<input type="text" name="<?php echo esc_attr( $key ); ?>[0][deadline]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[0][international]" value="" />
				</span>
				<?php
			}

			// @codingStandardsIgnoreStart
			?>
			<script type="text/template" id="factsheet-deadline-template">
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<select name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][semester]">
						<option value="None">Not selected</option>
						<option value="Fall">Fall</option>
						<option value="Spring">Spring</option>
						<option value="Summer">Summer</option>
					</select>
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][deadline]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][international]" value="" />
					<span class="remove-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">Remove</span>
				</span>
			</script>
			<input type="button" class="add-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field button" value="Add" />
			<input type="hidden" name="factsheet_deadline_form_count" id="factsheet_deadline_form_count" value="<?php echo esc_attr( $field_count ); ?>" />
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as strings.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_requirements_meta_field( $meta, $key, $data ) {
		$field_data = maybe_unserialize( $data[ $key ][0] );

		if ( empty( $field_data ) ) {
			$field_data = array();
		}

		$default_field_data = array(
			'score' => '',
			'test' => '',
			'description' => '',
		);
		$field_count = 0;

		?>
		<div class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-wrapper">
			<span class="factsheet-label">Requirements:</span>
			<?php

			foreach ( $field_data as $field_datum ) {
				$field_datum = wp_parse_args( $field_datum, $default_field_data );

				?>
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][score]" value="<?php echo esc_attr( $field_datum['score'] ); ?>" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][test]" value="<?php echo esc_attr( $field_datum['test'] ); ?>" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $field_count ); ?>][description]" value="<?php echo esc_attr( $field_datum['description'] ); ?>" />
					<span class="remove-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">Remove</span>
				</span>
				<?php
				$field_count++;
			}

			// If no fields have been added, provide an empty field by default.
			if ( 0 === count( $field_data ) ) {
				?>
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<input type="text" name="<?php echo esc_attr( $key ); ?>[0][score]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[0][test]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[0][description]" value="" />
				</span>
				<?php
			}

			// @codingStandardsIgnoreStart
			?>
			<script type="text/template" id="factsheet-requirement-template">
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][score]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][test]" value="" />
					<input type="text" name="<?php echo esc_attr( $key ); ?>[<%= form_count %>][description]" value="" />
					<span class="remove-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">Remove</span>
				</span>
			</script>
			<input type="button" class="add-factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field button" value="Add" />
			<input type="hidden" name="factsheet_requirement_form_count" id="factsheet_requirement_form_count" value="<?php echo esc_attr( $field_count ); ?>" />
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Outputs the meta field HTML used to capture meta data stored as strings.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $meta
	 * @param string $key
	 * @param array  $data
	 */
	public function display_locations_meta_field( $meta, $key, $data ) {
		$field_data = maybe_unserialize( $data[ $key ][0] );

		if ( empty( $field_data ) ) {
			$field_data = array();
		}

		$default_field_data = array(
			'Pullman' => 'No',
			'Spokane' => 'No',
			'Tri-Cities' => 'No',
			'Vancouver' => 'No',
			'Global Campus' => 'No',
		);
		$field_data = wp_parse_args( $field_data, $default_field_data );

		if ( isset( $meta['restricted'] ) && $meta['restricted'] && $this->user_is_eam_user( wp_get_current_user()->ID, get_the_ID() ) ) {
			$restricted = true;
		} else {
			$restricted = false;
		}

		?>
		<div class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-wrapper">
			<span class="factsheet-label">Locations:</span>
			<?php

			foreach ( $field_data as $location => $location_status ) {
				?>
				<span class="factsheet-<?php echo esc_attr( $meta['type'] ); ?>-field">
					<label for="location-<?php echo esc_attr( sanitize_key( $location ) ); ?>"><?php echo esc_html( $location ); ?></label>
					<?php
					if ( $restricted ) {
						echo '<span id="location-' . esc_attr( sanitize_key( $location ) ) . '" class="field-value">' . esc_attr( $location_status ) . '</span>';
					} else {
						?>
						<select id="location-<?php echo esc_attr( sanitize_key( $location ) ); ?>"
							name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $location ); ?>]">
							<option value="No" <?php selected( 'No', $location_status ); ?>>No</option>
							<option value="Yes" <?php selected( 'Yes', $location_status ); ?>>Yes</option>
							<option value="By Exception" <?php selected( 'By Exception', $location_status ); ?>>By Exception</option>
						</select>
						<?php
					}
					?>
				</span>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Determines if a user has been assigned to a factsheet through Editorial Access Manager.
	 *
	 * @since 1.1.0
	 *
	 * @param int $user_id
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function user_is_eam_user( $user_id, $post_id ) {
		$enable_custom_access = get_post_meta( $post_id, 'eam_enable_custom_access', true );

		if ( 'users' === $enable_custom_access ) {
			$allowed_users = (array) get_post_meta( $post_id, 'eam_allowed_users', true );

			if ( in_array( $user_id, $allowed_users ) ) { // @codingStandardsIgnoreLine (Converting user IDs to ints is not worth it)
				return true;
			}
		}

		return false;
	}

	/**
	 * Ensures that users assigned via Editorial Access Manager are not allowed to change
	 * restricted fields.
	 *
	 * @since 1.1.0
	 * @since 1.3.0 Updated to handle multiple restricted fields.
	 *
	 * @param bool   $allowed
	 * @param string $meta_key
	 * @param int    $object_id
	 * @param int    $user_id
	 *
	 * @return bool
	 */
	public function can_edit_restricted_field( $allowed, $meta_key, $object_id, $user_id ) {
		if ( $this->user_is_eam_user( $user_id, $object_id ) ) {
			return false;
		}

		return $allowed;
	}

	/**
	 * Prevents a user, assigned via Editorial Access Manager, from editing a factsheet's
	 * title.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data
	 * @param array $postarr
	 *
	 * @return array
	 */
	public function manage_factsheet_title_update( $data, $postarr ) {
		$user = wp_get_current_user();

		if ( isset( $postarr['ID'] ) && $this->user_is_eam_user( $user->ID, $postarr['ID'] ) ) {
			$existing_title = get_post_field( 'post_title', absint( $postarr['ID'] ) );

			if ( ! empty( $existing_title ) && $data['post_title'] !== $existing_title ) {
				$data['post_title'] = $existing_title;
			}
		}

		return $data;
	}

	/**
	 * Sanitizes a GPA value.
	 *
	 * @since 0.4.0
	 *
	 * @param string $gpa The unsanitized GPA.
	 *
	 * @return string The sanitized GPA.
	 */
	public static function sanitize_gpa( $gpa ) {
		$dot_count = substr_count( $gpa, '.' );

		if ( 0 === $dot_count ) {
			$gpa = absint( $gpa ) . '.0';
		} elseif ( 1 === $dot_count ) {
			$gpa = explode( '.', $gpa );
			$gpa = absint( $gpa[0] ) . '.' . absint( $gpa[1] );
		} else {
			$gpa = '0.0';
		}

		return $gpa;
	}

	/**
	 * Sanitizes a set of locations stored in a string.
	 *
	 * @since 0.10.0
	 *
	 * @param array $locations
	 *
	 * @return array
	 */
	public static function sanitize_locations( $locations ) {
		if ( ! is_array( $locations ) || 0 === count( $locations ) ) {
			$locations = array();
		}

		$location_names = array( 'Pullman', 'Spokane', 'Tri-Cities', 'Vancouver', 'Global Campus' );
		$clean_locations = array();

		foreach ( $location_names as $location_name ) {
			if ( ! isset( $locations[ $location_name ] ) || ! in_array( $locations[ $location_name ], array( 'No', 'Yes', 'By Exception' ), true ) ) {
				$clean_locations[ $location_name ] = 'No';
			} else {
				$clean_locations[ $location_name ] = $locations[ $location_name ];
			}
		}

		return $clean_locations;
	}

	/**
	 * Sanitizes a set of deadlines stored in a string.
	 *
	 * @since 0.4.0
	 *
	 * @param array $deadlines
	 *
	 * @return string
	 */
	public static function sanitize_deadlines( $deadlines ) {
		if ( ! is_array( $deadlines ) || 0 === count( $deadlines ) ) {
			return '';
		}

		$clean_deadlines = array();

		foreach ( $deadlines as $deadline ) {
			$clean_deadline = array();

			if ( isset( $deadline['semester'] ) && in_array( $deadline['semester'], array( 'None', 'Fall', 'Spring', 'Summer' ), true ) ) {
				$clean_deadline['semester'] = $deadline['semester'];
			} else {
				$clean_deadline['semester'] = 'None';
			}

			if ( isset( $deadline['deadline'] ) ) {
				$clean_deadline['deadline'] = sanitize_text_field( $deadline['deadline'] );
			} else {
				$clean_deadline['deadline'] = '';
			}

			if ( isset( $deadline['international'] ) ) {
				$clean_deadline['international'] = sanitize_text_field( $deadline['international'] );
			} else {
				$clean_deadline['international'] = '';
			}

			$clean_deadlines[] = $clean_deadline;
		}

		return $deadlines;
	}

	/**
	 * Sanitizes a set of requirements stored in a string.
	 *
	 * @since 0.4.0
	 *
	 * @param array $requirements
	 *
	 * @return string
	 */
	public static function sanitize_requirements( $requirements ) {
		if ( ! is_array( $requirements ) || 0 === count( $requirements ) ) {
			return '';
		}

		$clean_requirements = array();

		foreach ( $requirements as $requirement ) {
			$clean_requirement = array();

			if ( isset( $requirement['score'] ) ) {
				$clean_requirement['score'] = sanitize_text_field( $requirement['score'] );
			} else {
				$clean_requirement['score'] = '';
			}

			if ( isset( $requirement['test'] ) ) {
				$clean_requirement['test'] = sanitize_text_field( $requirement['test'] );
			} else {
				$clean_requirement['test'] = '';
			}

			if ( isset( $requirement['description'] ) ) {
				$clean_requirement['description'] = sanitize_text_field( $requirement['description'] );
			} else {
				$clean_requirement['description'] = '';
			}

			$clean_requirements[] = $clean_requirement;
		}

		return $clean_requirements;
	}

	/**
	 * Save additional data associated with a factsheet.
	 *
	 * @since 0.4.0
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public function save_factsheet( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		// Do not overwrite existing information during an import.
		if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
			return;
		}

		if ( ! isset( $_POST['_gsdp_primary_nonce'] ) || ! wp_verify_nonce( $_POST['_gsdp_primary_nonce'], 'save-gsdp-primary' ) ) {
			return;
		}

		$keys = get_registered_meta_keys( 'post' );

		foreach ( $this->post_meta_keys as $key => $meta ) {
			if ( isset( $_POST[ $key ] ) && isset( $keys[ $key ] ) && isset( $keys[ $key ]['sanitize_callback'] ) ) {
				if ( current_user_can( 'edit_post_meta', $post_id, $key ) ) {
					// Each piece of meta is registered with sanitization.
					update_post_meta( $post_id, $key, $_POST[ $key ] );
				}
			}
		}

		/**
		 * Added the following to force update the last modified date since that doesn't happen
		 * when you are updating post meta.
		 */
		remove_action( "save_post_{$this->post_type_slug}", array( $this, 'save_factsheet' ), 10, 2 );

		global $wpdb;

		//eg. time one year ago..
		$time = time();

		$mysql_time_format = 'Y-m-d H:i:s';

		$post_modified = gmdate( $mysql_time_format, $time );

		$post_modified_gmt = gmdate( $mysql_time_format, ( $time + get_option( 'gmt_offset' ) ) );

		$wpdb->query( $wpdb->prepare( "UPDATE %s SET post_modified = %s, post_modified_gmt = %s  WHERE ID = %d", array( $wpdb->posts, $post_modified, $post_modified_gmt, $post_id ) ) );

		// end last modified update.

		if ( isset( $_POST['faculty'] ) ) {
			$faculty_relationships = array();
			$assigned_faculty = wp_get_object_terms( $post_id, 'gs-faculty' );
			$assigned_faculty = wp_list_pluck( $assigned_faculty, 'term_id' );

			foreach ( $assigned_faculty as $assigned ) {
				if ( ! isset( $_POST['faculty'][ $assigned ] ) ) {
					wp_remove_object_terms( $post_id, $assigned, 'gs-faculty' );
				}
			}

			foreach ( $_POST['faculty'] as $term_id => $chair_selection ) {
				if ( ! in_array( $term_id, $assigned_faculty, true ) ) {
					wp_add_object_terms( $post_id, $term_id, 'gs-faculty' );
				}

				$unique_id = get_term_meta( $term_id, 'gs_relationship_id', true );
				if ( empty( $unique_id ) ) {
					$unique_id = wp_generate_uuid4();
					update_term_meta( $term_id, 'gs_relationship_id', $unique_id );
				}

				if ( in_array( $chair_selection['program_chair'], array( 'true', 'false' ), true ) ) {
					$faculty_relationships[ $unique_id ]['chair'] = $chair_selection['program_chair'];
				} else {
					$faculty_relationships[ $unique_id ]['chair'] = 'false';
				}

				if ( in_array( $chair_selection['program_cochair'], array( 'true', 'false' ), true ) ) {
					$faculty_relationships[ $unique_id ]['cochair'] = $chair_selection['program_cochair'];
				} else {
					$faculty_relationships[ $unique_id ]['cochair'] = 'false';
				}

				if ( in_array( $chair_selection['program_sit'], array( 'true', 'false' ), true ) ) {
					$faculty_relationships[ $unique_id ]['sit'] = $chair_selection['program_sit'];
				} else {
					$faculty_relationships[ $unique_id ]['sit'] = 'false';
				}
			}

			update_post_meta( $post_id, 'gsdp_faculty_relationships', $faculty_relationships );
		}

		if ( isset( $_POST['contacts'] ) ) {
			$full_contacts = array();
			foreach ( $_POST['contacts'] as $contact ) {
				if ( 0 !== absint( $contact ) ) {
					$full_contacts[] = absint( $contact );
				}
			}
			wp_set_object_terms( $post_id, $full_contacts, 'gs-contact' );
		} else {
			wp_set_object_terms( $post_id, array(), 'gs-contact' );
		}
	}

	/**
	 * Filters a user's ability to delete factsheets when they have been added as an
	 * authorized user via Editorial Access Manager.
	 *
	 * @since 1.1.0
	 *
	 * @param array $caps
	 * @param string $cap
	 * @param int $user_id
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_map_meta_cap( $caps, $cap, $user_id, $args ) {
		$eam_caps = array(
			'delete_page',
			'delete_post',
		);

		if ( in_array( $cap, $eam_caps, true ) ) {

			$post_id = ( isset( $args[0] ) ) ? (int) $args[0] : null;
			if ( ! $post_id && ! empty( $_GET['post'] ) ) { // WPCS: CSRF Ok.
				$post_id = (int) $_GET['post'];
			}

			if ( ! $post_id && ! empty( $_POST['post_ID'] ) ) { // @codingStandardsIgnoreLine (No reason to check a nonce here)
				$post_id = (int) $_POST['post_ID'];
			}

			if ( ! $post_id ) {
				return $caps;
			}

			$enable_custom_access = get_post_meta( $post_id, 'eam_enable_custom_access', true );

			if ( ! empty( $enable_custom_access ) ) {
				$user = new WP_User( $user_id );

				// If user is admin, we do nothing
				if ( ! in_array( 'administrator', $user->roles, true ) ) {

					if ( 'users' === $enable_custom_access ) {
						// Reset caps for allowed users to do_not_allow.
						$allowed_users = (array) get_post_meta( $post_id, 'eam_allowed_users', true );

						if ( in_array( $user_id, $allowed_users ) ) { // @codingStandardsIgnoreLine (Converting user IDs to ints is not worth it)
							$caps[] = 'do_not_allow';
						}
					}
				}
			}
		}

		return $caps;
	}

	/**
	 * Manage capabilities allowing EAM users to edit faculty members.
	 *
	 * @since 1.3.0
	 *
	 * @param array   $allcaps An array of all the user's capabilities.
	 * @param array   $caps    Actual capabilities for meta capability.
	 * @param array   $args    Optional parameters passed to has_cap(), typically object ID.
	 * @param WP_User $user    The user object.
	 * @return array Updated list of capabilities.
	 */
	public function allow_edit_faculty_member( $allcaps, $caps, $args, $user ) {
		if ( 'manage_categories' === $args[0] ) {
			if ( isset( $_POST['action'] ) && 'editedtag' === $_POST['action'] && isset( $_POST['tag_ID'] ) && 'gs-faculty' === $_POST['taxonomy'] ) { // @codingStandardsIgnoreLine (No reason to check a nonce here)
				$term_id = absint( $_POST['tag_ID'] );
			} else {
				return $allcaps;
			}
		} elseif ( 'edit_term' === $args[0] ) {
			$term_id = $args[2];
		} else {
			return $allcaps;
		}

		// Administrators always have access.
		if ( in_array( 'administrator', $user->roles, true ) ) {
			return $allcaps;
		}

		$factsheets = get_objects_in_term( $term_id, 'gs-faculty' );
		if ( empty( $factsheets ) || is_wp_error( $factsheets ) ) {
			return $allcaps;
		}

		$taxonomy = get_taxonomy( 'gs-faculty' );
		$allowed = false;

		foreach ( $factsheets as $post_id ) {
			$enable_custom_access = get_post_meta( $post_id, 'eam_enable_custom_access', true );

			if ( ! empty( $enable_custom_access ) ) {
				if ( 'users' === $enable_custom_access ) {

					// Reset caps for allowed users to do_not_allow.
					$allowed_users = (array) get_post_meta( $post_id, 'eam_allowed_users', true );

					if ( in_array( $user->ID, $allowed_users ) ) { // @codingStandardsIgnoreLine (Converting user IDs to ints is not worth it)
						$allowed = true;
						break;
					}
				}
			}
		}

		if ( $allowed ) {
			$allcaps[ $taxonomy->cap->edit_terms ] = true;
			$allcaps[ $taxonomy->cap->manage_terms ] = true;
		}

		return $allcaps;
	}

	/**
	 * Returns a usable subset of data for displaying a factsheet.
	 *
	 * @since 0.4.0
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public static function get_factsheet_data( $post_id ) {
		$factsheet_data = get_registered_metadata( 'post', $post_id );

		$data = array(
			'degree_id' => 0,
			'shortname' => '',
			'description' => '',
			'accepting_applications' => 'No',
			'faculty' => array(),
			'students' => 0,
			'aided' => 0,
			'degree_url' => 'Not available',
			'deadlines' => array(),
			'requirements' => array(),
			'locations' => array(
				'Pullman' => 'No',
				'Spokane' => 'No',
				'Tri-Cities' => 'No',
				'Vancouver' => 'No',
				'Global Campus' => 'No',
			),
			'admission_requirements',
			'student_opportunities',
			'career_opportunities',
			'career_placements',
			'student_learning_outcome',
			'public' => 'No',
		);

		if ( isset( $factsheet_data['gsdp_degree_description'][0] ) ) {
			$data['description'] = $factsheet_data['gsdp_degree_description'][0];
		}

		if ( isset( $factsheet_data['gsdp_degree_id'][0] ) ) {
			$data['degree_id'] = $factsheet_data['gsdp_degree_id'][0];
		}

		if ( isset( $factsheet_data['gsdp_include_in_programs'][0] ) && 1 === absint( $factsheet_data['gsdp_include_in_programs'][0] ) ) {
			$data['public'] = 'Yes';
		}

		if ( isset( $factsheet_data['gsdp_degree_shortname'][0] ) ) {
			$data['shortname'] = $factsheet_data['gsdp_degree_shortname'][0];
		}

		if ( isset( $factsheet_data['gsdp_accepting_applications'][0] ) && 1 === absint( $factsheet_data['gsdp_accepting_applications'][0] ) ) {
			$data['accepting_applications'] = 'Yes';
		}

		$faculty = wp_get_object_terms( $post_id, 'gs-faculty' );
		if ( ! is_wp_error( $faculty ) ) {
			$data['faculty'] = $faculty;
		}

		if ( isset( $factsheet_data['gsdp_grad_students_total'][0] ) ) {
			$data['students'] = $factsheet_data['gsdp_grad_students_total'][0];
		}

		if ( isset( $factsheet_data['gsdp_grad_students_aided'][0] ) ) {
			if ( 0 === absint( $data['students'] ) ) {
				$data['aided'] = '0.00';
			} else {
				$data['aided'] = round( ( $factsheet_data['gsdp_grad_students_aided'][0] / $data['students'] ) * 100, 2 );
			}
		}

		if ( isset( $factsheet_data['gsdp_degree_url'][0] ) ) {
			$data['degree_url'] = $factsheet_data['gsdp_degree_url'][0];
		}

		if ( isset( $factsheet_data['gsdp_deadlines'][0] ) ) {
			$data['deadlines'] = maybe_unserialize( $factsheet_data['gsdp_deadlines'][0] );

			if ( ! is_array( $data['deadlines'] ) ) {
				$data['deadlines'] = array();
			}
		}

		if ( isset( $factsheet_data['gsdp_requirements'][0] ) ) {
			$data['requirements'] = maybe_unserialize( $factsheet_data['gsdp_requirements'][0] );

			if ( ! is_array( $data['requirements'] ) ) {
				$data['requirements'] = array();
			}
		}

		if ( isset( $factsheet_data['gsdp_locations'][0] ) ) {
			$locations = maybe_unserialize( $factsheet_data['gsdp_locations'][0] );
			$data['locations'] = wp_parse_args( $locations, $data['locations'] );
		}

		if ( isset( $factsheet_data['gsdp_admission_requirements'][0] ) ) {
			$data['admission_requirements'] = $factsheet_data['gsdp_admission_requirements'][0];
		}

		if ( isset( $factsheet_data['gsdp_student_opportunities'][0] ) ) {
			$data['student_opportunities'] = $factsheet_data['gsdp_student_opportunities'][0];
		}

		if ( isset( $factsheet_data['gsdp_career_opportunities'][0] ) ) {
			$data['career_opportunities'] = $factsheet_data['gsdp_career_opportunities'][0];
		}

		if ( isset( $factsheet_data['gsdp_career_placements'][0] ) ) {
			$data['career_placements'] = $factsheet_data['gsdp_career_placements'][0];
		}

		if ( isset( $factsheet_data['gsdp_student_learning_outcome'][0] ) ) {
			$data['student_learning_outcome'] = $factsheet_data['gsdp_student_learning_outcome'][0];
		}

		$contacts = wp_get_object_terms( $post_id, 'gs-contact' );
		$data['contacts'] = array();
		if ( ! is_wp_error( $contacts ) ) {
			foreach ( $contacts as $contact ) {
				$contact_meta = WSUWP_Graduate_Degree_Contact_Taxonomy::get_all_term_meta( $contact->term_id );
				$data['contacts'][] = $contact_meta;
			}
		}

		$faculty_relationships = get_post_meta( $post_id, 'gsdp_faculty_relationships', true );
		$faculty = wp_get_object_terms( $post_id, 'gs-faculty' );
		$data['faculty'] = array();
		if ( ! is_wp_error( $faculty ) ) {
			foreach ( $faculty as $person ) {
				$faculty_meta = WSUWP_Graduate_Degree_Faculty_Taxonomy::get_all_term_meta( $person->term_id );
				$faculty_meta['name'] = $person->name;

				// Provide a way to display last name first.
				$display_name = explode( ' ', $person->name );
				if ( 1 < count( $display_name ) ) {
					$faculty_meta['display_name'] = array_pop( $display_name );
					$faculty_meta['display_name'] .= ', ' . implode( ' ', $display_name );
				} else {
					$faculty_meta['display_name'] = $person->name;
				}

				$unique_id = get_term_meta( $person->term_id, 'gs_relationship_id', true );
				if ( empty( $unique_id ) ) {
					// Generate something that won't actually cause a result to output.
					$unique_id = wp_generate_uuid4();
				}

				if ( isset( $faculty_relationships[ $unique_id ] ) ) {
					$faculty_relationship_defaults = array(
						'chair' => 'false',
						'cochair' => 'false',
						'sit' => 'false',
					);
					$faculty_relationships[ $unique_id ] = wp_parse_args( $faculty_relationships[ $unique_id ], $faculty_relationship_defaults );

					if ( 'true' === $faculty_relationships[ $unique_id ]['chair'] && 'true' === $faculty_relationships[ $unique_id ]['cochair'] && 'true' === $faculty_relationships[ $unique_id ]['sit'] ) {
						$faculty_meta['relationship'] = 'Serves as: chair, co-chair, or member of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['chair'] && 'true' === $faculty_relationships[ $unique_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Serves as: chair or co-chair of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['chair'] && 'true' === $faculty_relationships[ $unique_id ]['sit'] ) {
						$faculty_meta['relationship'] = 'Serves as: chair or member of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['chair'] ) {
						$faculty_meta['relationship'] = 'Serves as: chair of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['cochair'] && 'true' === $faculty_relationships[ $unique_id ]['sit'] ) {
						$faculty_meta['relationship'] = 'Serves as: co-chair or member of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Serves as: co-chair of graduate committee';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['sit'] ) {
						$faculty_meta['relationship'] = 'Serves as: member only of graduate committee';
					} else {
						$faculty_meta['relationship'] = '';
					}
				} else {
					$faculty_meta['relationship'] = '';
				}

				$data['faculty'][ $faculty_meta['display_name'] . time() ] = $faculty_meta;
			}
		}
		ksort( $data['faculty'] );

		return $data;
	}

	/**
	 * Adjusts the archive query for factsheets to show all factsheets.
	 *
	 * @since 0.8.0
	 *
	 * @param WP_Query $query
	 */
	public function adjust_factsheet_archive_query( $query ) {
		if ( is_post_type_archive( $this->post_type_slug ) ) {
			$query->set( 'posts_per_page', -1 );
		}
	}

	/**
	 * Redirects a given degree ID to either the current degree URL or
	 * to the degrees landing page.
	 *
	 * @param int $degree_id
	 */
	public function redirect_factsheet_id( $degree_id ) {
		$matches = get_posts( array(
			'post_type' => $this->post_type_slug,
			'meta_key' => 'gsdp_degree_id',
			'meta_value' => $degree_id,
		) );

		if ( 0 !== count( $matches ) ) {
			$redirect_url = get_permalink( $matches[0]->ID );
			wp_safe_redirect( $redirect_url, 301 );
			exit();
		} else {
			wp_safe_redirect( home_url( '/' . $this->archive_slug . '/' ), 302 );
			exit();
		}
	}

	/**
	 * Redirects old factsheet ID URLs to their new URL or to the
	 * factsheets landing page.
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Query $wp_query
	 */
	public function redirect_old_factsheet_urls() {
		global $wp_query;

		if ( $wp_query->is_404() && isset( $wp_query->query['post_type'] ) && $this->post_type_slug === $wp_query->query['post_type'] ) {
			if ( is_numeric( $wp_query->query[ $this->post_type_slug ] ) ) {
				$degree_id = absint( $wp_query->query[ $this->post_type_slug ] );
				$this->redirect_factsheet_id( $degree_id );
			}
		}
	}

	/**
	 * Redirects published factsheets that are set to not be included in the
	 * program list. If the factsheet is a draft, then it can be previewed by
	 * those who have access.
	 *
	 * @since 0.10.0
	 */
	public function redirect_private_factsheets() {
		if ( ! is_singular( $this->post_type_slug ) ) {
			return;
		}

		if ( 'draft' === get_post_status( get_the_ID() ) ) {
			return;
		}

		if ( 1 !== absint( get_post_meta( get_the_ID(), 'gsdp_include_in_programs', true ) ) ) {
			wp_redirect( home_url( '/' . $this->archive_slug . '/' ) );
			exit();
		}
	}

	/**
	 * Alters the title displayed for the factsheets landing page.
	 *
	 * @since 1.1.0
	 *
	 * @param string $view_title
	 * @param string $site_title
	 * @param string $global_title
	 *
	 * @return string
	 */
	public function filter_factsheet_archive_title( $view_title, $site_title, $global_title ) {
		if ( is_post_type_archive( $this->post_type_slug ) ) {
			return 'Graduate Degree Programs | ' . $site_title . $global_title;
		}

		return $view_title;
	}
}
