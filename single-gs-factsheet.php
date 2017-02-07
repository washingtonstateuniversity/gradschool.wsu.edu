<?php get_header(); ?>

	<main class="degree-program-single">
		<?php get_template_part( 'parts/headers' ); ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<header>
				<hgroup>
					<h1><?php the_title(); ?></h1>
				</hgroup>
			</header>

			<section class="row single gutter pad-ends">

				<div class="column one">
					<?php $factsheet_data = WSUWP_Graduate_Degree_Programs::get_factsheet_data( get_the_id() ); ?>

					<div class="factsheet-url"><a href="<?php echo esc_url( $factsheet_data['degree_url'] ); ?>"><?php echo esc_html( $factsheet_data['degree_url'] ); ?></a></div>

					<div class="factsheet-statistics-wrapper">
						<div class="factsheet-stat">
							<span class="factsheet-label">Faculty working with Students:</span>
							<span class="factsheet-value"><?php echo count( $factsheet_data['faculty'] ); ?></span>
						</div>

						<div class="factsheet-stat">
							<span class="factsheet-label">Students:</span>
							<span class="factsheet-value"><?php echo absint( $factsheet_data['students'] ); ?></span>
						</div>

						<div class="factsheet-stat">
							<span class="factsheet-label">Students receiving assistantships or scholarships:</span>
							<span class="factsheet-value"><?php echo esc_html( $factsheet_data['aided'] ); ?>%</span>
						</div>

						<div class="factsheet-stat">
							<span class="factsheet-label">Priority deadline:</span>
							<div class="factsheet-set">
								<ul>
									<?php
									foreach ( $factsheet_data['deadlines'] as $fs_deadline ) {
										echo '<li>' . esc_html( $fs_deadline->semester ) . ' ' . esc_html( $fs_deadline->deadline ) . ' ' . esc_html( $fs_deadline->international ) . '</li>';
									}
									?>
								</ul>
							</div>
							<span class="factsheet-value"></span>
						</div>

						<div class="factsheet-stat">
							<span class="factsheet-label">Campus:</span>
							<div class="factsheet-set">
								<ul>
									<?php
									foreach ( $factsheet_data['locations'] as $fs_location => $fs_location_status ) {
										if ( 'No' === $fs_location_status ) {
											continue;
										}
										echo '<li>' . esc_html( $fs_location ) . ': ' . esc_html( $fs_location_status ) . '</li>';
									}
									?>
								</ul>
							</div>
						</div>

						<div class="factsheet-stat">
							<span class="factsheet-label">Tests required:</span>
							<div class="factsheet-set">
								<ul>
									<?php
									foreach ( $factsheet_data['requirements'] as $fs_requirement ) {
										echo '<li>' . esc_html( $fs_requirement->score ) . ' ' . esc_html( $fs_requirement->test ) . ' ' . esc_html( $fs_requirement->description ) . '</li>';
									}
									?>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</section>
			<section class="row side-right gutter pad-ends">

				<div class="column one">
					<?php if ( ! empty( $factsheet_data['description'] ) ) : ?>
						<div class="factsheet-description">
							<h2>Degree Description:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['description'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $factsheet_data['admission_requirements'] ) ) : ?>
						<div class="factsheet-admission-requirements">
							<h2>Admission Requirements:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['admission_requirements'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $factsheet_data['student_learning_outcome'] ) ) : ?>
						<div class="factsheet-student-learning-outcome">
							<h2>Student Learning Outcome:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['student_learning_outcome'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $factsheet_data['student_opportunities'] ) ) : ?>
						<div class="factsheet-student-opportunities">
							<h2>Student Opportunities:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['student_opportunities'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $factsheet_data['career_opportunities'] ) ) : ?>
						<div class="factsheet-career-opportunities">
							<h2>Career Opportunities:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['career_opportunities'] ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $factsheet_data['career_placements'] ) ) : ?>
						<div class="factsheet-career-placements">
							<h2>Career Placements:</h2>
							<?php echo wp_kses_post( apply_filters( 'the_content', $factsheet_data['career_placements'] ) ); ?>
						</div>
					<?php endif; ?>

				</div><!--/column-->
				<div>
					<h2>Contact Information:</h2>
				</div>
			</section>
			<?php
		endwhile;
		endif;

		get_template_part( 'parts/footers' );

		?>
	</main>
<?php get_footer();
