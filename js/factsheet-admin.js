(function( $ ){
	var $form_container = $( ".factsheet-primary-inputs" );
	var deadlines_template = $( "#factsheet-deadline-template" ).html();
	var requirements_template = $( "#factsheet-requirement-template" ).html();
	var deadline_count = $( "#factsheet_deadline_form_count" ).val();
	var requirement_count = $( "#factsheet_requirement_form_count" ).val();

	$form_container.on( "click", ".add-factsheet-deadlines-field", function( el ) {
		var $button = $( el.target );
		var tpl = _.template( deadlines_template );

		$button.before( tpl( { form_count: deadline_count } ) );
		deadline_count++;
	} );

	$form_container.on( "click", ".remove-factsheet-deadlines-field", function( el ) {
		$( el.target ).parent( ".factsheet-deadlines-field" ).remove();
	} );

	$form_container.on( "click", ".add-factsheet-requirements-field", function( el ) {
		var $button = $( el.target );
		var tpl = _.template( requirements_template );

		$button.before( tpl( { form_count: requirement_count } ) );
		requirement_count++;
	} );

	$form_container.on( "click", ".remove-factsheet-requirements-field", function( el ) {
		$( el.target ).parent( ".factsheet-requirements-field" ).remove();
	} );
}( jQuery ));
