function confirmUpdate() {
	jQuery( '#buttonFHPLPressed' ).val( 'Update' );
	jQuery("form[name='fpw_fhpl_form']").submit();
}

function confirmAdd() {
	jQuery( '#buttonFHPLPressed' ).val( 'Add' );
	jQuery("form[name='fpw_fhpl_form']").submit();
}

function confirmRemove() {
	jQuery( '#buttonFHPLPressed' ).val( 'Remove' );
	jQuery("form[name='fpw_fhpl_form']").submit();
}

jQuery( document ).ready( function( $ ) {

	jQuery("#contextual-help-link").html(fpw_fhpl.help_link_text);

	// Fade out update message
	setTimeout(function(){
  		$("div.updated").fadeOut("slow", function () {
  			$("div.updated").remove();
      	});
	}, 5000);
	
});
