<?php
			wp_register_script( 'fpw-fhpl', plugins_url( '/fpw-honey-pot-links/js/fpw-fhpl.js' ), array( 'jquery' ) );
			wp_enqueue_script( 'fpw-fhpl' );
			wp_localize_script( 'fpw-fhpl', 'fpw_fhpl', array(
				'help_link_text'	=> esc_html( __( 'Help for FPW Honey Pot Links', 'fpw-fhpl' ) )
				));
?>