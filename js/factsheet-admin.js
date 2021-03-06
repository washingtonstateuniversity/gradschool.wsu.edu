/* global _ */
( function( $, window ) {
	var $form_container = $( ".factsheet-primary-inputs" );
	var deadlines_template = $( "#factsheet-deadline-template" ).html();
	var requirements_template = $( "#factsheet-requirement-template" ).html();
	var deadline_count = $( "#factsheet_deadline_form_count" ).val();
	var requirement_count = $( "#factsheet_requirement_form_count" ).val();
	var contact_template = $( "#factsheet-contact-template" ).html();
	var faculty_template = $( "#factsheet-faculty-template" ).html();

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

	$( "#factsheet-contact" ).on( "click", ".remove-factsheet-contact", function( el ) {
		$( el.target ).parent( ".factsheet-contact" ).remove();
	} );

	$( "#factsheet-faculty" ).on( "click", ".remove-factsheet-faculty", function( el ) {
		$( el.target ).parent( ".factsheet-faculty" ).remove();
	} );

	var searchRequest;
	var search_objects;

	jQuery( "#contact-entry" ).autocomplete( {
		minLength: 2,
		source: function( term, suggest ) {
			try { searchRequest.abort(); } catch ( e ) {}
			searchRequest = jQuery.get( window.gs_factsheet.contact_rest_url, { search: term.term }, function( res ) {
				if ( res !== null ) {
					var results = [];
					search_objects = [];
					for ( var i = 0; i < res.length; i++ ) {
						if ( res[ i ] !== 0 ) {
							search_objects[ res[ i ].id ] = res[ i ];
							results.push( { label: res[ i ].name, value: res[ i ].id } );
						}
					}
					suggest( results );
				}
			} );
		},
		select: function( event, ui ) {
			var tpl = _.template( contact_template );
			var contact = search_objects[ ui.item.value ];

			$( ".factsheet-contact-wrapper" ).append( tpl( {
				contact_name: ui.item.label,
				contact_term_id: ui.item.value,
				contact_address_one: contact.meta.gs_contact_address_one,
				contact_address_two: contact.meta.gs_contact_address_two,
				contact_city: contact.meta.gs_contact_city,
				contact_state: contact.meta.gs_contact_state,
				contact_postal: contact.meta.gs_contact_postal,
				contact_phone: contact.meta.gs_contact_phone,
				contact_fax: contact.meta.gs_contact_fax,
				contact_email: contact.meta.gs_contact_email
			} ) );
		},
		close: function() {
			$( "#contact-entry" ).val( "" );
		}
	} );

	jQuery( "#faculty-entry" ).autocomplete( {
		minLength: 2,
		source: function( term, suggest ) {
			try { searchRequest.abort(); } catch ( e ) {}
			searchRequest = jQuery.get( window.gs_factsheet.faculty_rest_url, { search: term.term }, function( res ) {
				if ( res !== null ) {
					var results = [];
					for ( var i = 0; i < res.length; i++ ) {
						if ( res[ i ] !== 0 ) {
							results.push( { label: res[ i ].name, value: res[ i ].id } );
						}
					}
					suggest( results );
				}
			} );
		},
		select: function( event, ui ) {
			var tpl = _.template( faculty_template );

			$( ".factsheet-faculty-wrapper" ).append( tpl( {
				faculty_name: ui.item.label,
				term_id: ui.item.value
			} ) );
		},
		close: function() {
			$( "#faculty-entry" ).val( "" );
		}
	} );
}( jQuery, window ) );
