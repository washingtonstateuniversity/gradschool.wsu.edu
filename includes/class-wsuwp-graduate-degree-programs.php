<?php

class WSUWP_Graduate_Degree_Programs {
	/**
	 * @since 0.4.0
	 *
	 * @var WSUWP_Graduate_Degree_Programs
	 */
	private static $instance;

	/*
	 * Track a version number for the scripts registered in
	 * this object to enable cache busting.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	var $script_version = '0001';

	/**
	 * The slug used to register the factsheet post type.
	 *
	 * @since 0.4.0
	 *
	 * @var string
	 */
	var $post_type_slug = 'gs-factsheet';

	/**
	 * A list of post meta keys associated with factsheets.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	var $post_meta_keys = array(
		'gsdp_degree_id' => array(
			'description' => 'Factsheet degree ID',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'pre_html' => '<div class="factsheet-group">',
			'location' => 'primary',
		),
		'gsdp_accepting_applications' => array(
			'description' => 'Accepting applications',
			'type' => 'bool',
			'sanitize_callback' => 'absint',
			'location' => 'primary',
		),
		'gsdp_include_in_programs' => array(
			'description' => 'Include in programs list',
			'type' => 'bool',
			'sanitize_callback' => 'absint',
			'location' => 'primary',
		),
		'gsdp_grad_students_total' => array(
			'description' => 'Total number of grad students',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'location' => 'primary',
		),
		'gsdp_grad_students_aided' => array(
			'description' => 'Number of aided grad students',
			'type' => 'int',
			'sanitize_callback' => 'absint',
			'location' => 'primary',
		),
		'gsdp_admission_gpa' => array(
			'description' => 'Admission GPA',
			'type' => 'float',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_gpa',
			'location' => 'primary',
		),
		'gsdp_degree_url' => array(
			'description' => 'Degree home page',
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_deadlines' => array(
			'description' => 'Deadlines',
			'type' => 'deadlines',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_deadlines',
			'pre_html' => '<div class="factsheet-group">',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_requirements' => array(
			'description' => 'Requirements',
			'type' => 'requirements',
			'sanitize_callback' => 'WSUWP_Graduate_Degree_Programs::sanitize_requirements',
			'pre_html' => '<div class="factsheet-group">',
			'post_html' => '</div>',
			'location' => 'primary',
		),
		'gsdp_degree_description' => array(
			'description' => 'Description of the graduate degree',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'location' => 'secondary',
		),
		'gsdp_admission_requirements' => array(
			'description' => 'Admission requirements',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'location' => 'secondary',
		),
		'gsdp_student_opportunities' => array(
			'description' => 'Student opportunities',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'location' => 'secondary',
		),
		'gsdp_career_opportunities' => array(
			'description' => 'Career opportunities',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'location' => 'secondary',
		),
		'gsdp_career_placements' => array(
			'description' => 'Career placements',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
			'location' => 'secondary',
		),
		'gsdp_student_learning_outcome' => array(
			'description' => 'Student learning outcome',
			'type' => 'textarea',
			'sanitize_callback' => 'wp_kses_post',
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
		require_once( dirname( __FILE__ ) . '/class-graduate-degree-faculty-taxonomy.php' );
		require_once( dirname( __FILE__ ) . '/class-graduate-degree-program-name-taxonomy.php' );
		require_once( dirname( __FILE__ ) . '/class-graduate-degree-degree-type-taxonomy.php' );
		require_once( dirname( __FILE__ ) . '/class-graduate-degree-contact-taxonomy.php' );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'init', array( $this, 'register_post_type' ), 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Faculty_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Program_Name_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Degree_Type_Taxonomy', 15 );
		add_action( 'init', 'WSUWP_Graduate_Degree_Contact_Taxonomy', 15 );

		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 99 );
		add_action( "save_post_{$this->post_type_slug}", array( $this, 'save_factsheet' ), 10, 2 );
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
			wp_enqueue_style( 'gsdp-admin', get_stylesheet_directory_uri() . '/css/factsheet-admin.css', array(), $this->script_version );
			wp_register_script( 'gsdp-factsheet-admin', get_stylesheet_directory_uri() . '/js/factsheet-admin.min.js', array( 'jquery', 'underscore', 'jquery-ui-autocomplete' ), $this->script_version, true );

			$rest_api_data = array(
				'contact_rest_url' => rest_url( 'wp/v2/gs-contact/' ),
				'faculty_rest_url' => rest_url( 'wp/v2/gs-faculty/' ),
			);
			wp_localize_script( 'gsdp-factsheet-admin', 'gs_factsheet', $rest_api_data );

			wp_enqueue_script( 'gsdp-factsheet-admin' );
		}

		if ( in_array( $hook_suffix, array( 'term.php', 'term-new.php' ), true ) && in_array( get_current_screen()->taxonomy, array( 'gs-contact', 'gs-faculty' ), true ) ) {
			wp_enqueue_style( 'gsdp-faculty-admin', get_stylesheet_directory_uri() . '/css/faculty-admin.css', array(), WSUWP_Graduate_School_Theme()->theme_version() );
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
			'has_archive' => 'factsheets-beta',
			'rewrite' => array( 'slug' => 'factsheets-beta/factsheet', 'with_front' => false ),
		);
		register_post_type( $this->post_type_slug, $args );
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
			$unique_id = md5( $faculty_member->name );
			// Convert old relationship data structure to new structure.
			if ( isset( $faculty_relationships[ $unique_id ] ) ) {
				$faculty_relationships[ $faculty_member->term_id ]['chair'] = $faculty_relationships[ $unique_id ]['chair'];
				$faculty_relationships[ $faculty_member->term_id ]['cochair'] = $faculty_relationships[ $unique_id ]['cochair'];
			}

			if ( ! isset( $faculty_relationships[ $faculty_member->term_id ] ) ) {
				$faculty_relationships[ $faculty_member->term_id ]['chair'] = 'false';
				$faculty_relationships[ $faculty_member->term_id ]['cochair'] = 'false';
			}

			if ( ! isset( $faculty_relationships[ $faculty_member->term_id ]['chair'] ) ) {
				$faculty_relationships[ $faculty_member->term_id ]['chair'] = 'false';
			}

			if ( ! isset( $faculty_relationships[ $faculty_member->term_id ]['cochair'] ) ) {
				$faculty_relationships[ $faculty_member->term_id ]['cochair'] = 'false';
			}

			?>
			<div class="factsheet-faculty">
				<h3><?php echo esc_html( $faculty_member->name ); ?></h3>
				<div class="select-chair">
					<label for="program_chair">Can chair committee:</label>
					<select name="faculty[<?php echo esc_attr( $faculty_member->term_id ); ?>][program_chair]" id="program_chair">
						<option value="false" <?php selected( 'false', $faculty_relationships[ $faculty_member->term_id ]['chair'] ); ?>>No</option>
						<option value="true" <?php selected( 'true', $faculty_relationships[ $faculty_member->term_id ]['chair'] ); ?>>Yes</option>
					</select>
				</div>
				<div class="select-cochair">
					<label for="program_cochair">Can co-chair committee:</label>
					<select name="faculty[<?php echo esc_attr( $faculty_member->term_id ); ?>][program_cochair]" id="program_cochair">
						<option value="false" <?php selected( 'false', $faculty_relationships[ $faculty_member->term_id ]['cochair'] ); ?>>No</option>
						<option value="true" <?php selected( 'true', $faculty_relationships[ $faculty_member->term_id ]['cochair'] ); ?>>Yes</option>
					</select>
				</div>
				<span class="remove-factsheet-faculty">Remove</span>
			</div>
			<?php
		}

		echo '</div>'; // End of factsheet-faculty-wrapper.

		?>
		<script type="text/template" id="factsheet-faculty-template">
			<div class="factsheet-faculty">
				<h3><%= faculty_name %></h3>
				<div class="select-chair">
					<label for="program_chair">Can chair committee:</label>
					<select name="faculty[<%= term_id %>][program_chair]" id="program_chair">
						<option value="false">No</option>
						<option value="true">Yes</option>
					</select>
				</div>
				<div class="select-cochair">
					<label for="program_cochair">Can co-chair committee:</label>
					<select name="faculty[<%= term_id %>][program_cochair]" id="program_cochair">
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
			foreach( $contacts as $contact ) {
				$contact_meta = WSUWP_Graduate_Degree_Contact_Taxonomy::get_all_term_meta( $contact->term_id );
				$contact_meta['term_id'] = $contact->term_id;
				$data['contacts'][] = $contact_meta;
			}
		}

		echo '<div class="factsheet-contact-wrapper">';

		foreach( $data['contacts'] as $contact ) {
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
		$wp_editor_settings = array(
			'textarea_rows' => 10,
			'media_buttons' => false,
			'teeny' => true,
		);

		if ( isset( $meta['pre_html'] ) ) {
			echo $meta['pre_html']; // @codingStandardsIgnoreLine (HTML is static in code)
		}
		?>
		<div class="factsheet-primary-input factsheet-<?php echo esc_attr( $meta['type'] ); ?>"">
		<?php
		if ( 'int' === $meta['type'] ) {
			?>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
			<input type="text" name="<?php echo esc_attr( $key ); ?>" value="<?php echo absint( $data[ $key ][0] ); ?>" />
			<?php
		} elseif ( 'string' === $meta['type'] || 'float' === $meta['type'] ) {
			?>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
			<input type="text" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $data[ $key ][0] ); ?>" />
			<?php
		} elseif ( 'textarea' === $meta['type'] ) {
			?>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
			<?php
			wp_editor( $data[ $key ][0], esc_attr( $key ), $wp_editor_settings );
		} elseif ( 'bool' === $meta['type'] ) {
			?>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $meta['description'] ); ?>:</label>
			<select name="<?php echo esc_attr( $key ); ?>">
				<option value="0" <?php selected( 0, absint( $data[ $key ][0] ) ); ?>>No</option>
				<option value="1" <?php selected( 1, absint( $data[ $key ][0] ) ); ?>>Yes</option>
			</select>
			<?php
		} elseif ( 'deadlines' === $meta['type'] ) {
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

		} elseif ( 'requirements' === $meta['type'] ) {
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

		}

		echo '</div>'; // End factsheet-primary-input

		if ( isset( $meta['post_html'] ) ) {
			echo $meta['post_html']; // @codingStandardsIgnoreLine (HTML is static in code)
		}
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
				// Each piece of meta is registered with sanitization.
				update_post_meta( $post_id, $key, $_POST[ $key ] );
			}
		}

		if ( isset( $_POST['faculty'] ) ) {
			$faculty_relationships = array();
			$assigned_faculty = wp_get_object_terms( $post_id, 'gs-faculty' );
			$assigned_faculty = wp_list_pluck( $assigned_faculty, 'term_id' );

			foreach( $assigned_faculty as $assigned ) {
				if ( ! isset( $_POST['faculty'][ $assigned ] ) ) {
					wp_remove_object_terms( $post_id, $assigned, 'gs-faculty' );
				}
			}

			foreach( $_POST['faculty'] as $term_id => $chair_selection ) {
				if ( ! in_array( $term_id, $assigned_faculty, true ) ) {
					wp_add_object_terms( $post_id, $term_id, 'gs-faculty' );
				}

				if ( in_array( $chair_selection['program_chair'], array( 'true', 'false' ), true ) ) {
					$faculty_relationships[ $term_id ]['chair'] = $chair_selection['program_chair'];
				} else {
					$faculty_relationships[ $term_id ]['chair'] = 'false';
				}

				if ( in_array( $chair_selection['program_cochair'], array( 'true', 'false' ), true ) ) {
					$faculty_relationships[ $term_id ]['cochair'] = $chair_selection['program_cochair'];
				} else {
					$faculty_relationships[ $term_id ]['chair'] = 'false';
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
		);

		if ( isset( $factsheet_data['gsdp_degree_description'][0] ) ) {
			$data['description'] = $factsheet_data['gsdp_degree_description'][0];
		}

		if ( isset( $factsheet_data['gsdp_degree_id'][0] ) ) {
			$data['degree_id'] = $factsheet_data['gsdp_degree_id'][0];
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

		$locations = get_post_meta( $post_id, 'gsdp_locations', true );
		if ( ! empty( $locations ) ) {
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
			foreach( $contacts as $contact ) {
				$contact_meta = WSUWP_Graduate_Degree_Contact_Taxonomy::get_all_term_meta( $contact->term_id );
				$data['contacts'][] = $contact_meta;
			}
		}

		$faculty_relationships = get_post_meta( $post_id, 'gsdp_faculty_relationships', true );
		$faculty = wp_get_object_terms( $post_id, 'gs-faculty' );
		$data['faculty'] = array();
		if ( ! is_wp_error( $faculty ) ) {
			foreach( $faculty as $person ) {
				$faculty_meta = WSUWP_Graduate_Degree_Faculty_Taxonomy::get_all_term_meta( $person->term_id );
				$faculty_meta['name'] = $person->name;

				$unique_id = md5( $person->name );
				if ( isset( $faculty_relationships[ $unique_id ] ) ) {
					if ( 'true' === $faculty_relationships[ $unique_id ]['chair'] && 'true' === $faculty_relationships[ $unique_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Can chair or co-chair graduate committee.';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['chair'] ) {
						$faculty_meta['relationship'] = 'Can chair graduate committee.';
					} elseif ( 'true' === $faculty_relationships[ $unique_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Can co-chair graduate committee.';
					} else {
						$faculty_meta['relationship'] = 'Can sit as a graduate committee member.';
					}
				} elseif ( isset( $faculty_relationships[ $person->term_id ] ) ) {
					if ( 'true' === $faculty_relationships[ $person->term_id ]['chair'] && 'true' === $faculty_relationships[ $person->term_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Can chair or co-chair graduate committee.';
					} elseif ( 'true' === $faculty_relationships[ $person->term_id ]['chair'] ) {
						$faculty_meta['relationship'] = 'Can chair graduate committee.';
					} elseif ( 'true' === $faculty_relationships[ $person->term_id ]['cochair'] ) {
						$faculty_meta['relationship'] = 'Can co-chair graduate committee.';
					} else {
						$faculty_meta['relationship'] = 'Can sit as a graduate committee member.';
					}
				} else {
					$faculty_meta['relationship'] = 'Can sit as a graduate committee member.';
				}

				$data['faculty'][] = $faculty_meta;
			}
		}

		return $data;
	}
}
