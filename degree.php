<?php

global $wsu_grad_degrees;

$output = $wsu_grad_degrees->get_degree_html( get_query_var( 'degree_id' ) );

get_header();

?>

	<main>

		<?php get_template_part( 'parts/headers' ); ?>

		<section class="row single gutter marginalize-ends">

			<div class="column one">

				<?php
				// @codingStandardsIgnoreStart
				echo $output;
				// @codingStandardsIgnoreEnd
				?>

			</div><!--/column-->

		</section>

	</main><!--/#page-->

<?php get_footer(); ?>
