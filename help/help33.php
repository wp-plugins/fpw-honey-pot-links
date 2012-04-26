<?php
		global	$current_screen;
		
		$sidebar =	'<p style="font-size: larger">' . __( 'More information', 'fpw-fhpl' ) . '</p>' . 
					'<blockquote><a href="http://fw2s.com/" target="_blank">' . __( 'Plugin\' site', 'fpw-fhpl' ) . '</a></blockquote>' . 
					'<p style="font-size: larger">' . __( 'Support', 'fpw-fhpl' ) . '</p>' . 
					'<blockquote><a href="http://fw2s.com/support/fpw-honey-pot-links/" target="_blank">FWSS</a></blockquote>'; 
			
		$current_screen->set_help_sidebar( $sidebar );
			
		$intro =	'<p style="font-size: larger">' . __( 'Introduction', 'fpw-fhpl' ) . '</p>' . 
					'<blockquote style="text-align: justify">' . __( 'Project Honey Pot makes possible to install traps', 'fpw-fhpl' ) . ' ' . 
					__( 'against comment spammers and e-mail harvesters.', 'fpw-fhpl' ) . ' ' . 
					__( 'These traps have to be activated by adding special links to posts / pages.', 'fpw-fhpl' ) . ' ' . 
					__( 'This plugin automates the process by inserting such links to all existing posts / pages.', 'fpw-fhpl' ) . '</blockquote>'; 

		$current_screen->add_help_tab( array(
   			'title'   => __( 'Introduction', 'fpw-fhpl' ),
    		'id'      => 'fpw-fhpl-help-introduction',
   			'content' => $intro,
		) );
			
		$opts =		'<p style="font-size: larger">' . __( 'Available Options', 'fpw-fhpl' ) . '</p>' . 
					'<blockquote style="text-align: justify">' . 
					'<strong>' . __( 'Remove plugin\'s data from database on uninstall', 'fpw-fhpl' ) . '</strong> ' . 
					'( ' . __( 'checked', 'fpw-fhpl' ) . ' ) - ' . __( 'during uninstall procedure all plugin\'s information ( options, mappings ) will be removed from the database', 'fpw-fhpl' ) . '<br />' . 
					'<strong>' . __( 'Add this plugin to the Admin Bar', 'fpw-fhpl' ) . '</strong> ' . 
					'( ' . __( 'checked', 'fpw-fhpl' ) . ' ) - ' . __( 'the plugin\'s link to its settings page will be added to the Admin Bar', 'fpw-fhpl' ) . 
					'</blockquote>';

		$current_screen->add_help_tab( array(
   			'title'   => __( 'Options', 'fpw-fhpl' ),
    		'id'      => 'fpw-fhpl-help-options',
	   		'content' => $opts,
		) );

		$actions =	'<p style="font-size: larger">' . __( 'Action Buttons', 'fpw-fhpl' ) . '</p><blockquote>' . 
					'<table style="width: 100%;"><tr><td style="text-align: left; vertical-align: middle;">' . 
					'<input class="button-primary" type="button" title="' . __( 'Inactive button - presentation only', 'fpw-fhpl' ) . '" value="' . 
					__( 'Update', 'fpw-fhpl' ) . '" /></td><td>' . __( 'saves modified settings to the database', 'fpw-fhpl' ) .  
					'</td></tr><tr><td style="text-align: left; vertical-align: middle;">' . 
					'<input class="button-primary" type="button" title="' . __( 'Inactive button - presentation only', 'fpw-fhpl' ) . '" value="' . 
					__( 'Add Links', 'fpw-fhpl' ) . '" /></td><td>' . __( 'adds Honey Pot links to all existing posts / pages', 'fpw-fhpl' ) . 
					'</td></tr><tr><td style="text-align: left; vertical-align: middle;">' . 
					'<input class="button-primary" type="button" title="' . __( 'Inactive button - presentation only', 'fpw-fhpl' ) . '" value="' . 
					__( 'Remove Links', 'fpw-fhpl' ) . '" /></td><td>' . __( 'removes Honey Pot links from all existing posts /pages', 'fpw-fhpl' ) . '
					</td></tr></table></blockquote>';
			
		$current_screen->add_help_tab( array(
   			'title'   => __( 'Actions', 'fpw-fhpl' ),
    		'id'      => 'fpw-fhpl-help-actions',
	   		'content' => $actions,
		) );
			
?>