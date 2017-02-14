<?php get_header(); ?>

	<main class="degree-program-single">
		<?php get_template_part( 'parts/headers' ); ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<section class="row single gutter pad-top">

				<div class="column one">
					<h1><?php the_title(); ?></h1>
				</div>

			</section>

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
										if ( 'NULL' === $fs_deadline['semester'] ) {
											continue;
										}
										echo '<li>' . esc_html( $fs_deadline['semester'] ) . ' ' . esc_html( $fs_deadline['deadline'] ) . ' ' . esc_html( $fs_deadline['international'] ) . '</li>';
									}
									?>
								</ul>
							</div>
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
										echo '<li>' . esc_html( $fs_requirement['score'] ) . ' ' . esc_html( $fs_requirement['test'] ) . ' ' . esc_html( $fs_requirement['description'] ) . '</li>';
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

						<div class="factsheet-faculty-wrapper">
							<h2>Faculty Members:</h2>
					<?php
					foreach ( $factsheet_data['faculty'] as $faculty ) {
						?>
						<div class="factsheet-faculty">
							<h3><?php echo esc_html( $faculty['name'] ); ?><?php if ( ! empty( $faculty['degree_abbreviation'] ) ) : ?>, <?php echo esc_html( $faculty['degree_abbreviation'] ); ?><?php endif; ?></h3>
							<?php if ( ! empty( $faculty['email'] ) ) : ?>
							<div><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( $faculty['email'] ); ?>"><?php echo esc_html( $faculty['email'] ); ?></a></div>
							<?php endif; ?>
							<?php if ( ! empty( $faculty['url'] ) ) : ?>
							<div><strong>URL:</strong> <a href="<?php echo esc_url( $faculty['url'] ); ?>"><?php echo esc_html( $faculty['url'] ); ?></a></div>
							<?php endif; ?>
							<?php if ( ! empty( $faculty['teaching_interests'] ) ) : ?>
							<div>
								<h4>Teaching Interests</h4>
								<?php echo wp_kses_post( apply_filters( 'the_content', $faculty['teaching_interests'] ) ); ?>
							</div>
							<?php endif; ?>
							<?php if ( ! empty( $faculty['research_interests'] ) ) : ?>
							<div>
								<h4>Research Interests</h4>
								<?php echo wp_kses_post( apply_filters( 'the_content', $faculty['research_interests'] ) ); ?>
							</div>
							<?php endif; ?>
						</div>
						<?php
					}
					?>
						</div>
				</div><!--/column-->
				<div class="column two">
					<h2>Contact Information:</h2>
					<?php
					foreach( $factsheet_data['contacts'] as $contact ) {
						?>
						<address class="factsheet-contact" itemscope itemtype="http://schema.org/Organization">
							<?php if ( ! empty( $contact['name'] ) ) : ?>
							<div itemprop="contactPoint" itemscope itemtype="http://schema.org/Person"><?php echo esc_html( $contact['name'] ); ?></div>
							<?php endif; ?>
							<div class="address">
								<?php if ( ! empty( $contact['address_one'] ) ) : ?>
								<div itemprop="streetAddress"><?php echo esc_html( $contact['address_one'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $contact['address_two'] ) ) : ?>
								<div itemprop="streetAddress"><?php echo esc_html( $contact['address_two'] ); ?></div>
								<?php endif; ?>
								<div>
									<?php if ( ! empty( $contact['city'] ) && ! empty( $contact['state'] ) ) : ?>
									<span itemprop="addressLocality"><?php echo esc_html( $contact['city'] ); ?>, <?php echo esc_html( $contact['state'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $contact['postal'] ) ) : ?>
									<span itemprop="postalcode"><?php echo esc_html( $contact['postal'] ); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php if ( ! empty( $contact['phone'] ) ) : ?>
							<div itemprop="telephone"><?php echo esc_html( $contact['phone'] ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $contact['fax'] ) ) : ?>
							<div itemprop="faxNumber"><?php echo esc_html( $contact['fax'] ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $contact['email'] ) ) : ?>
							<div itemprop="email"><a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><?php echo esc_html( $contact['email'] ); ?></a></div>
							<?php endif; ?>
						</address>
						<?php
					}
					?>
				</div>
			</section>

			<section class="row single lastsectionwithfooter">
				<div class="column one">
					<div class="progressbar"></div>
					<div class="footer">
						<ul>
							<li><strong><a href="https://gradschool.wsu.edu">Graduate School</a></strong></li>
							<li>Washington State University</li>
							<li>Stadium Way, 324 French Administration Building</li>
							<li>P.O. Box 641030</li>
							<li>Pullman, WA 99164-1030</li>
							<li><a href="mailto:gradschool@wsu.edu">gradschool@wsu.edu</a></li>
							<li><a href="tel:15093356424">P: 509-335-6424</a>, F: 509-335-1949</li>
						</ul>
					</div>
				</div>
			</section>
			<?php
		endwhile;
		endif;

		get_template_part( 'parts/footers' );

		?>
	</main>
<?php get_footer();
