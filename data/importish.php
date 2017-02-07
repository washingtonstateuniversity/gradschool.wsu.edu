<?php

/*
 *  4    'gsdp_degree_description' => array(
	0	'gsdp_degree_id' => array(
	11	'gsdp_accepting_applications' => array(
	12	'gsdp_include_in_programs' => array(
	2	'gsdp_grad_students_total' => array(
	3	'gsdp_grad_students_aided' => array(
	5	'gsdp_admission_gpa' => array(
	7	'gsdp_degree_url' => array(
	17	'gsdp_deadlines' => array(
	19	'gsdp_requirements' => array(
	6	'gsdp_admission_requirements' => array(
	8	'gsdp_student_opportunities' => array(
	9	'gsdp_career_opportunities' => array(
	10	'gsdp_career_placements' => array(
		'gsdp_student_learning_outcome' => array(
 */
$time_start = microtime( true );
$csv = new parseCSV();
$csv->heading = false;
$csv->parse( dirname( __FILE__ ) . '/data/20170206-import-02.csv' );

$collected_faculty = array();
$collected_contact = array();
$collected_contact_names = array();
$collected_program_names = array();
$location_default = array(
	'Pullman' => 'No',
	'Vancouver' => 'No',
	'Tri-Cities' => 'No',
	'Spokane' => 'No',
	'Global Campus' => 'No',
);
$contact_count = 0;
$skip = true;

foreach( $csv->data as $datum ) {
	if ( $skip ) {
		$skip = false;
		continue;
	}

	$post_data = array(
		'post_type' => 'gs-factsheet',
		'post_title' => sanitize_text_field( $datum[1] ),
		'post_status' => 'publish',
	);
	$id = wp_insert_post( $post_data );

	$contact_info = explode( '|', $datum[20] );
	$contact_info = array_filter( $contact_info );

	foreach ( $contact_info as $item ) {
		$item = trim( $item );
		if ( empty( $item ) ) {
			continue;
		}

		$unique_cid = md5( strtolower( $item ) );

		if ( isset( $collected_contact[ $unique_cid ] ) ) {
			wp_add_object_terms( $id, $collected_contact[ $unique_cid ], 'gs-contact' );
			continue;
		}

		$item = explode( ',', $item );
		$item = array_map( 'trim', $item );
		$order = array_shift( $item );
		$name = array_shift( $item );

		if ( 'PHD' === strtoupper( $item[0] ) || 'Ph.D.' === $item[0] || 'MBA' === $item[0] ) {
			$name .= ', ' . $item[0];
			array_shift( $item );
		}

		if ( empty( $name ) ) {
			$term_name = 'BLANK';
		} else {
			$term_name = $name;
		}

		while ( isset( $collected_contact_names[ $term_name ] ) ) {
			$term_name .= ' Dup';
		}

		$title = array_shift( $item );

		$dept = array_shift( $item );


		if ( 'Associate Dean' === $title ) {
			$title .= ', ' . $dept;
			$dept = array_shift( $item );
		}

		if ( 'Department of Management' === $dept || 'Apparel' === $dept ) {
			$dept .= ', ' . $item[0] . ',' . $item[1];
			array_shift( $item );
			array_shift( $item );
		} elseif ( 'DNP Program' === $dept ) {
			$dept .= ', ' . $item[0];
			array_shift( $item );
		} elseif ( 'Graduate Studies Committee' === $dept ) {
			$title .= ', ' . $dept;
			$dept = array_shift( $item );
		}

		$email = array_shift( $item );

		if ( 'Philosophy and Public Affairs' === $email ) {
			$dept .= ', ' . $email;
			$email = array_shift( $item );
		}

		if ( 'Program in Neuroscience' === $email || 'WSU College of Nursing' === $email ) {
			$email = array_shift( $item );
		}

		$add1 = array_shift( $item );

		if ( empty( $email ) && is_email( $add1 ) ) {
			$email = $add1;
			$add1 = array_shift( $item );
		}

		if ( 'The Edward R. Murrow College of Communication' === $add1 ) {
			$title = $dept;
			$dept = $add1;
			$add1 = array_shift( $item );
		}

		$add2 = array_shift( $item );

		if ( 0 === strpos( $add2, 'Room' ) ) {
			$add1 .= ' ' . $add2;
			$add2 = array_shift( $item );
		}

		if ( 'Stadium Way' === $add2 ) {
			$add2 = array_shift( $item );
		}

		$city = array_shift( $item );
		$state = array_shift( $item );

		if ( 'Washington' === $state || 'WASHINGTON' === $state ) {
			$state = 'WA';
		}

		$zip = array_shift( $item );

		if ( 'Pullman' === $state ) {
			$city = 'Pullman';
			$state = 'WA';
			$zip = array_shift( $item );
		}

		$phone = array_shift( $item );
		$fax = array_shift( $item );

		$term = wp_insert_term( $term_name, 'gs-contact' );

		if ( ! is_wp_error( $term ) ) {
			$collected_contact_names[ $term_name ] = true;
			update_term_meta( $term['term_id'], 'gs_contact_name', $name );
			update_term_meta( $term['term_id'], 'gs_contact_title', $title );
			update_term_meta( $term['term_id'], 'gs_contact_department', $dept );
			update_term_meta( $term['term_id'], 'gs_contact_email', $email );
			update_term_meta( $term['term_id'], 'gs_contact_address_one', $add1 );
			update_term_meta( $term['term_id'], 'gs_contact_address_two', $add2 );
			update_term_meta( $term['term_id'], 'gs_contact_city', $city );
			update_term_meta( $term['term_id'], 'gs_contact_state', $state );
			update_term_meta( $term['term_id'], 'gs_contact_postal', $zip );
			update_term_meta( $term['term_id'], 'gs_contact_phone', $phone );
			update_term_meta( $term['term_id'], 'gs_contact_fax', $fax );
			update_term_meta( $term['term_id'], 'gs_contact_id', $unique_cid );
		}

		wp_add_object_terms( $id, $term['term_id'], 'gs-contact' );
		$collected_contact[ $unique_cid ] = $term['term_id'];
	}

	// Process faculty members
	$faculty = explode( '|', $datum[16] );
	$faculty = array_map( 'trim', $faculty );
	$faculty = array_filter( $faculty );

	$faculty_relationships = array();

	foreach( $faculty as $ind ) {
		if ( 'NULL' === $ind ) {
			continue;
		}

		$ind_csv = new parseCSV();
		$ind_csv->heading = false;
		$ind_csv->parse( $ind );
		$record = $ind_csv->data[0];
		$ind_csv = null;

		if ( 1 === count( $record ) ) {
			continue;
		}

		$chair = trim( array_shift( $record ) );
		$cochair = trim( array_shift( $record ) );
		$sit = trim( array_shift( $record ) );
		$name = trim( array_shift( $record ) );

		$unique_id = md5( $name );

		$faculty_relationships[ $unique_id ] = array(
			'chair' => $chair,
			'cochair' => $cochair,
			'site' => $sit,
		);

		// Skip additional processing if faculty has already been stored globally.
		if ( isset( $collected_faculty[ $unique_id ] ) ) {
			wp_add_object_terms( $id, $collected_faculty[ $unique_id ], 'gs-faculty' );
			continue;
		}

		$degree = trim( array_shift( $record ) );

		if ( 'DVM' === $degree || 'BVSc' === $degree ) {
			$degree = $degree . ', ' . trim( array_shift( $record ) );
		}
		if ( 'NULL' === $degree ) {
			$degree = '';
		}

		$email = trim( array_pop( $record ) );
		if ( 'NULL' === $email ) {
			$email = '';
		}

		$record = implode( ',', $record );
		$record = str_replace( '&#x0D;', "\r", $record );
		$record = str_replace( '.,', '.,,,', $record );
		$record = explode( ',,,', $record );
		$record = array_map( 'trim', $record );
		$record = array_filter( $record );

		if ( ! isset( $record[0] ) ) {
			$first = '';
			$second = '';
		} elseif ( 'NULL,NULL' === $record[0] ) {
			$first = '';
			$second = '';
		} elseif ( 0 === strpos( $record[0], 'NULL,' ) ) {
			$first = '';
			$second = str_replace( 'NULL,', '', $record[0] );
		} elseif ( 0 === substr_compare( $record[0], ',NULL', strlen( $record[0] ) - 5, 5 ) ) {
			$first = substr( $record[0], 0, strlen( $record[0] ) - 5 );
			$second = '';
		} else {
			$first = $record[0];
			if ( ! isset( $record[1] ) ) {
				$second = '';
			} else {
				$second = $record[1];
			}
		}

		$record = null;

		if ( 'NULL' === $second ) {
			$second = '';
		}

		$first = ltrim( $first, ',' );
		$first = str_replace( ',', ', ', $first );
		$first = str_replace( '  ', ' ', $first );
		$second = str_replace( ',', ', ', $second );
		$second = str_replace( '  ', ' ', $second );

		$term = wp_insert_term( $name, 'gs-faculty' );

		if ( ! is_wp_error( $term ) ) {
			update_term_meta( $term['term_id'], 'gs_degree_abbreviation', $degree );
			update_term_meta( $term['term_id'], 'gs_faculty_email', $email );
			update_term_meta( $term['term_id'], 'gs_teaching_interests', $first );
			update_term_meta( $term['term_id'], 'gs_research_interests', $second );
			update_term_meta( $term['term_id'], 'gs_relationship_id', $unique_id );
		}

		wp_add_object_terms( $id, $term['term_id'], 'gs-faculty' );

		$collected_faculty[ $unique_id ] = $term['term_id'];
	}

	$degree = null;
	$email = null;
	$first = null;
	$second = null;
	$unique_id = null;

	// End process faculty members

	update_post_meta( $id, 'gsdp_degree_id', $datum[0] );
	update_post_meta( $id, 'gsdp_grad_students_total', $datum[2] );
	update_post_meta( $id, 'gsdp_grad_students_aided', $datum[3] );
	update_post_meta( $id, 'gsdp_degree_description', $datum[4] );
	update_post_meta( $id, 'gsdp_admission_gpa', $datum[5] );
	update_post_meta( $id, 'gsdp_admission_requirements', $datum[6] );
	update_post_meta( $id, 'gsdp_degree_url', $datum[7] );
	update_post_meta( $id, 'gsdp_student_opportunities', $datum[8] );
	update_post_meta( $id, 'gsdp_career_opportunities', $datum[9] );
	update_post_meta( $id, 'gsdp_career_placements', $datum[10] );
	update_post_meta( $id, 'gsdp_accepting_applications', $datum[11] );
	update_post_meta( $id, 'gsdp_include_in_programs', $datum[12] );

	update_post_meta( $id, 'gsdp_faculty_relationships', $faculty_relationships );

	$deadlines = explode( '|', $datum[17] );
	$deadlines = array_map( 'trim', $deadlines );
	$deadlines = array_filter( $deadlines );

	$clean_deadlines = array();
	foreach( $deadlines as $deadline ) {
		$deadline = explode( ',', $deadline );
		$deadline = array_map( 'trim', $deadline );
		$deadline = array_filter( $deadline );
		$clean_deadline = array( 'semester' => 'None', 'deadline' => '', 'international' => '' );

		if ( isset( $deadline[0] ) ) {
			$clean_deadline['semester'] = $deadline[0];
		}

		if ( isset( $deadline[1] ) ) {
			$clean_deadline['deadline'] = $deadline[1];
		}

		if ( isset( $deadline[2] ) ) {
			$clean_deadline['international'] = $deadline[2];
		}

		$clean_deadlines[] = $clean_deadline;
	}

	update_post_meta( $id, 'gsdp_deadlines', $clean_deadlines );

	$requirements = explode( '|', $datum[19] );
	$requirements = array_map( 'trim', $requirements );
	$requirements = array_filter( $requirements );

	$clean_requirements = array();
	foreach( $requirements as $requirement ) {
		$requirement = explode( ',', $requirement );
		$requirement = array_map( 'trim', $requirement );
		$requirement = array_filter( $requirement );
		$clean_requirement = array( 'score' => '', 'test' => '', 'description' => '' );

		if ( isset( $requirement[0] ) && 'NULL' !== $requirement[0] ) {
			$clean_requirement['score'] = $requirement[0];
		}

		if ( isset( $requirement[1] ) ) {
			$clean_requirement['test'] = $requirement[1];
		}

		if ( isset( $requirement[2] ) ) {
			$clean_requirement['description'] = $requirement[2];
		}

		$clean_requirements[] = $clean_requirement;
	}

	update_post_meta( $id, 'gsdp_requirements', $clean_requirements );

	if ( ! empty( $datum[13] ) ) {
		$program_term = wp_insert_term( $datum[13], 'gs-program-name' );
		if ( ! is_wp_error( $program_term ) ) {
			$collected_program_names[ $datum[13] ] = $program_term['term_id'];
		}
	}

	if ( isset( $collected_program_names[ $datum[13] ] ) ) {
		wp_add_object_terms( $id, $collected_program_names[ $datum[13] ], 'gs-program-name' );
	}

	$location_info = explode( '|', $datum[18] );
	$location_info = array_map( 'trim', $location_info );
	$location_info = array_filter( $location_info );
	$item_locations = $location_default;
	foreach( $location_info as $location ) {
		$location = explode( ',', $location );
		$location = array_map( 'trim', $location );
		if ( ! isset( $location[1] ) ) {
			continue;
		}
		$item_locations[ $location[1] ] = $location[0];
	}
	update_post_meta( $id, 'gsdp_locations', $item_locations );

	update_post_meta( $id, 'gsdp_oracle_program_name_raw', $datum[14] );
	update_post_meta( $id, 'gsdp_plan_name_raw', $datum[15] );

	echo "Added " . $datum[1];
	$datum = null;
	echo ' ' . memory_get_usage() . " <br>";
}
$time = microtime( true ) - $time_start;
echo '<br><br>Time: ' . $time;
echo '<br>Final Memory: ' . memory_get_usage();
echo '<br><br>';
die();

?>
