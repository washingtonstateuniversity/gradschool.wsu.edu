<?php

$factsheets = array();
if ( have_posts() ) {
	while( have_posts() ) {
		the_post();
		$factsheet_data = WSUWP_Graduate_Degree_Programs::get_factsheet_data( get_the_ID() );
		$factsheet_data['permalink'] = get_the_permalink();
		$factsheets[ get_the_title() ] = $factsheet_data;
	}
}
ksort( $factsheets );

get_header();

?>

<main id="wsuwp-main" class="spine-blank-template">

	<?php get_template_part( 'parts/headers' ); ?>

	<section class="single gutter pad-top degreeprograms hero-med">
		<div class="column one centervertically">
			<div class="flexwrap left">
				<h1>Degree Programs</h1>
			</div>
		</div>
	</section>

	<section class="single gutter pad-top degreesearchsection">
		<div class="column one centervertically">
			<div class="searchdegreeswrapper"></div>
		</div>
	</section>

	<section class="single gutter pad-top whitesection degreelist">
		<div class="column one">

			<div class="toparea">
				<div class="pagination"><a class="active" href="#a">A</a> <a href="#b">B</a> <a href="#c">C</a> <a href="#d">D</a> <a href="#e">E</a> <a href="#f">F</a> <a href="#g">G</a> <a href="#h">H</a> <a href="#i">I</a> <a href="#j">J</a> <a href="#k">K</a> <a href="#l">L</a> <a href="#m">M</a> <a href="#n">N</a> <a href="#o">O</a> <a href="#p">P</a> <a href="#q">Q</a> <a href="#r">R</a> <a href="#s">S</a> <a href="#t">T</a> <a href="#u">U</a> <a href="#v">V</a> <a href="#w">W</a> <a href="#x">X</a> <a href="#y">Y</a> <a href="#z">Z</a></div>
				<div class="key">

					KEY: Doctorate
					<div class="doctorate exists">D</div>
					Master's
					<div class="masters exists">M</div>
				</div>
			</div>

			<div class="lettergroup">
				<a id="a" name="a"></a>
				<div class="bigletter active">A</div>
				<div class="bigletterline"></div>
				<ul>
				<?php
				$letter = 'a';
				foreach( $factsheets as $factsheet_name => $factsheet ) {
					while ( 0 !== strpos( strtolower( $factsheet_name ), $letter ) ) {
						echo '</ul></div>';
						$letter++;
						?>
						<div class="lettergroup">
							<a id="<?php echo esc_attr( $letter ); ?>" name="<?php echo esc_attr( $letter ); ?>"></a>
							<div class="bigletter active"><?php echo strtoupper( $letter ); ?></div>
							<div class="bigletterline"></div>
							<ul>
						<?php
					}
					?>
					<li>
						<div class="degreename flexleft"><a href="<?php echo esc_url( $factsheet['permalink'] ); ?>"><?php echo esc_html( $factsheet_name ); ?></a></div>
						<div class="doctorate flexright exists">D</div>
						<div class="masters flexright exists">M</div>
					</li>
					<?php

				}
				?>
				</ul>
			</div>
			<?php
				$letter++;

				if ( 'aa' !== $letter ) {
					while ( 'aa' !== $letter ) {
						?>
						<div class="lettergroup">
							<a id="<?php echo esc_attr( $letter ); ?>" name="<?php echo esc_attr( $letter ); ?>"></a>
							<div class="bigletter active"><?php echo strtoupper( $letter ); ?></div>
							<div class="bigletterline"></div>
						</div>
						<?php
						$letter++;
					}
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

	<?php get_template_part( 'parts/footers' ); ?>
</main>

<?php get_footer();
