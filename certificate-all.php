<?php

global $wsu_grad_degrees;

$output = $wsu_grad_degrees->get_certificates_html();

get_header();

?>
	<style>
		ul.list, ul.list ul {
			padding-right: 0;
		}
		.factsheet-group {
			color: #000;
			font-weight: bold;
			list-style-type: none;
		}
	</style>
	<main>

		<?php get_template_part('parts/headers'); ?>

		<section class="row thirds gutter marginalize-ends">

			<?php echo $output; ?>

		</section>

	</main><!--/#page-->

<?php get_footer(); ?>