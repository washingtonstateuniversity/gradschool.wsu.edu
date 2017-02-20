( function( $ ) {
	$( document ).ready( function() {
		$( ".degree-list" ).on( "click", ".degree-row-multiple .degree-name", function( el ) {
			$( el.target ).closest( ".degree-row-wrapper" ).toggleClass( "degree-row-open" );
		} );

		$( "#degree-search-input" ).on( "change blur keyup", function() {
			doSearch( $( "#degree-search-input" ).val() );
		} );

		$( ".degree-list .pagination a" ).on( "click", function() {
			$( ".degree-list .pagination a.active" ).removeClass( "active" );
			$( this ).addClass( "active" );
		} );
	} );

	function scrollToElement( elem ) {
		$( "html, body" ).animate( {
			scrollTop: $( elem ).offset().top - 60
		}, 800 );
	}

	function doSearch() {
		var terms = $( ".lettergroup li .degree-name" ).map( function() { return this.innerText; } ).get();

		$( "#degree-search-input" ).autocomplete( {
			source: terms,
			open: function() {
				$( "body" ).addClass( "searching" );
			},
			close: function() { $( "body" ).removeClass( "searching" ); },
			select: function( event, ui ) {
				$( "body" ).removeClass( "searching" );
				var text = ui.item.value.replace( /(\r\n|\n|\r)/gm, "" );
				var degrees = $( ".degree-name a:contains('" + text + "')" );
				if ( degrees ) {
					scrollToElement( degrees );
				}
			}
		} ).data( "ui-autocomplete" )._renderItem = customItemRenderer;
	}

	function customItemRenderer( ul, item ) {
		var newText = String( item.value ).replace(
			new RegExp( this.term, "gi" ),
			"<span class='ui-state-highlight'>$&</span>" );

		return $( "<li></li>" ).data( "item.autocomplete", item ).append( "<a>" + newText + "</a>" ).appendTo( ul );
	}
}( jQuery ) );
