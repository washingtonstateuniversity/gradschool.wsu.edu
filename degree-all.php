<?php

global $wsu_grad_degrees;

$output = $wsu_grad_degrees->get_degrees_html();

get_header();

?>
<script>
	(function ($) {
		// custom css expression for a case-insensitive contains()
		jQuery.expr[':'].Contains = function (a, i, m) {
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
		};


		function listFilter(header, list) { // header is any element, list is an unordered list
			// create and add the filter form to the header
			var form = $("<form>").attr({ "class": "filterform", "action": "#" }),
				input = $("<input>").attr({ "class": "filterinput", "type": "text" });
			$(form).append(input).appendTo(header);

			$(input)
				.change(function () {
					var filter = $(this).val();
					if (filter) {
						// this finds all links in a list that contain the input,
						// and hide the ones not containing the input while showing the ones that do
						$(list).find("a:not(:Contains(" + filter + "))").parent().slideUp();
						$(list).find("a:Contains(" + filter + ")").parent().slideDown();
					} else {
						$(list).find("li").slideDown();
					}
					return false;
				})
				.keyup(function () {
					// fire the above change event after every letter
					$(this).change();
				});
		}


		//ondomready
		$(function () {
			listFilter($(".list-header"), $(".list"));
		});
	} (jQuery));
</script>
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

		<?php get_template_part( 'parts/headers' ); ?>

		<section class="row thirds gutter marginalize-ends">

			<?php echo $output; ?>

		</section>

	</main><!--/#page-->

<?php get_footer();
