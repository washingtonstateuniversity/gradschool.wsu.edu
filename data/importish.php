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
$csv = new parseCSV();
$csv->heading = false;
$csv->parse( dirname( __FILE__ ) . '/data/20170206-import.csv' );

foreach( $csv->data as $datum ) {
	if ( 'ID' === $datum[0] ) {
		continue;
	}

	$post_data = array(
		'post_type' => 'gs-factsheet',
		'post_title' => sanitize_text_field( $datum[1] ),
		'post_status' => 'publish',
	);
	$id = wp_insert_post( $post_data );

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

	update_post_meta( $id, 'gsdp_program_name_raw', $datum[13] );
	update_post_meta( $id, 'gsdp_oracle_program_name_raw', $datum[14] );
	update_post_meta( $id, 'gsdp_plan_name_raw', $datum[15] );
	update_post_meta( $id, 'gsdp_faculty_raw', $datum[16] );
	update_post_meta( $id, 'gsdp_location_raw', $datum[18] );
	update_post_meta( $id, 'gsdp_contact_info_raw', $datum[20] );

	echo "Added " . $datum[1] . " <br>";
}

die();

?>
