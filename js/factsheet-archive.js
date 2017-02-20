( function( $ ) {
	$( document ).ready( function() {
		$( ".degree-list" ).on( "click", ".degree-row-multiple .degree-name", function( el ) {
			$( el.target ).closest( ".degree-row-wrapper" ).toggleClass( "degree-row-open" );
		} );
	} );
}( jQuery ) );
