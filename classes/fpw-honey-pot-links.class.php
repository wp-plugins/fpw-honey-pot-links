<?php
class fpwHoneyPotLinks {
	public	$pluginOptions;
	public	$pluginPath;
	public	$pluginUrl;
	public	$pluginVersion;
	public	$pluginPage;
	public	$wpVersion;
	public	$canActivate;
	
	//	constructor
	public	function __construct( $path, $version ) {
		global $wp_version;

		//	set plugin's path
		$this->pluginPath = $path;
		
		//	set plugin's url
		$this->pluginUrl = WP_PLUGIN_URL . '/fpw-honey-pot-links';
		
		//	set version
		$this->pluginVersion = $version;

		//	set WP version
		$this->wpVersion = $wp_version;
		
		//	set canActivate flag
		$this->canActivate = ( '3.3' <= $this->wpVersion ) ? true : false;
		
		//	actions and filters
		add_action( 'init', array( &$this, 'init' ) );
		
		//	actions below are not used in front end
		add_action( 'admin_menu', array( &$this, 'adminMenu' ) );
		add_action( 'after_plugin_row_fpw-honey-pot-links/fpw-honey-pot-links.php', array( &$this, 'afterPluginMeta' ), 10, 2 );

		add_filter( 'plugin_action_links_fpw-honey-pot-links/fpw-honey-pot-links.php', array( &$this, 'pluginLinks' ), 10, 2);
		add_filter( 'plugin_row_meta', array( &$this, 'pluginMetaLinks'), 10, 2 );

		register_activation_hook( $this->pluginPath . '/fpw-honey-pot-links.php', array( &$this, 'pluginActivate' ) );
		
		//	Read plugin's options
		$this->pluginOptions = $this->getOptions();
		if ( isset( $_POST[ 'buttonFHPLPressed' ] ) ) 
			$this->pluginOptions[ 'abar' ] = ( isset( $_POST[ 'abar' ] ) ) ? true : false;
		if ( $this->pluginOptions[ 'abar' ] ) 
			add_action( 'admin_bar_menu', array( &$this, 'pluginToAdminBar' ), 1030 );
		
		//	register filter for 'wp_insert_post_data'
		add_filter( 'wp_insert_post_data', array( &$this, 'insertHoneyPotLink' ), 10, 2 );
	}

	//	register plugin's textdomain
	public function init() {
		load_plugin_textdomain( 'fpw-fhpl', false, 'fpw-honey-pot-links/languages/' );
	} 

	//	register admin menu
	public function adminMenu() {
		$page_title = __( 'FPW Honey Pot Links', 'fpw-fhpl' ) . ' (' . $this->pluginVersion . ')';
		$menu_title = __( 'FPW Honey Pot Links', 'fpw-fhpl' );
		$this->pluginPage = add_options_page( $page_title, $menu_title, 'manage_options', 'fpw-honey-pot-links', array( &$this, 'pluginSettings' ) );
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueueScripts' ) );
	
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueuePointerScripts' ) );
		add_action( 'load-' . $this->pluginPage, array( &$this, 'help33' ) );
	}

	//	register styles, scripts, and localize javascript
	public function enqueueScripts( $hook ) {
		if ( 'settings_page_fpw-honey-pot-links' == $hook ) {
			include $this->pluginPath . '/code/enqueuescripts.php';
		}
	}
	
	//	enqueue pointer scripts
	public function enqueuePointerScripts( $hook ) {
		$proceed = false;
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		if ( !in_array( 'fpwfhpl100', $dismissed ) && apply_filters( 'show_wp_pointer_admin_bar', TRUE ) ) {
			$proceed = true;
			add_action( 'admin_print_footer_scripts', array( &$this, 'custom_print_footer_scripts' ) );
		}
		if ( $proceed ) {
    		wp_enqueue_style('wp-pointer');
    		wp_enqueue_script('wp-pointer');
    		wp_enqueue_script('utils');
		}
	}

	// 	handle pointer
	public function custom_print_footer_scripts() {
    	$pointerContent  = '<h3>' . esc_js( __( "What's new in this version?", 'fpw-fhpl' ) ) . '</h3>';
		$pointerContent .= '<li style="margin-left:25px;margin-top:20px;list-style:square">' . __( 'First release of the plugin', 'fpw-fhpl' ) . '</li>';
    	?>
    	<script type="text/javascript">
    	// <![CDATA[
    		jQuery(document).ready( function($) {
        		$('#fhpl-settings-title').pointer({
        			content: '<?php echo $pointerContent; ?>',
        			position: 'top',
            		close: function() {
						jQuery.post( ajaxurl, {
							pointer: 'fpwfhpl100',
							action: 'dismiss-wp-pointer'
						});
            		}
				}).pointer('open');
			});
    	// ]]>
    	</script>
    	<?php
	}

	//	contextual help for WordPress 3.3+
	public function help33() {
		include $this->pluginPath . '/help/help33.php';
	}
	
	//	add update information after plugin meta
	public function afterPluginMeta( $file, $plugin_data ) {
		$current = get_site_transient( 'update_plugins' );
		if ( !isset( $current -> response[ $file ] ) ) 
			return false;
		$url = "http://fw2s.com/fpwfhplupdate.txt";
		$update = wp_remote_fopen( $url );
		echo '<tr class="plugin-update-tr"><td></td><td></td><td class="plugin-update"><div class="update-message">' . 
			'<img class="alignleft" src="' . $this->pluginUrl . '/images/Thumbs_Up.png" width="64">' . $update . '</div></td></tr>';
	}

	//	add link to Donation to plugins meta
	public function pluginMetaLinks( $links, $file ) {
		if ( 'fpw-honey-pot-links/fpw-honey-pot-links.php' == $file ) 
			$links[] = '<a href="http://fw2s.com/payments-and-donations/" target="_blank">' . __( "Donate", "fpw-fhpl" ) . '</a>';
		return $links;
	}
	
	//	add link to settings page in plugins list
	public function pluginLinks( $links, $file ) {
   		$settings_link = '<a href="' . site_url( '/wp-admin/' ) . 'options-general.php?page=fpw-honey-pot-links">' . __( 'Settings', 'fpw-fhpl' ) . '</a>';
		array_unshift( $links, $settings_link );
    	return $links;
	}
	
	//	activation and uninstall file maintenance
	public function pluginActivate() {
		//	check if activation is possible
		if ( $this->canActivate ) {
			//	if cleanup requested make uninstall.php otherwise make uninstall.txt
			if ( $this->pluginOptions[ 'clean' ] ) {
				if ( file_exists( $this->pluginPath . '/uninstall.txt' ) ) 
					rename( $this->pluginPath . '/uninstall.txt', $this->pluginPath . '/uninstall.php' );
			} else {
				if ( file_exists( $this->pluginPath . '/uninstall.php' ) ) 
					rename( $this->pluginPath . '/uninstall.php', $this->pluginPath . '/uninstall.txt' );
			}
		} else {
			deactivate_plugins( $this->pluginPath . '/fpw-honey-pot-links.php' );
			wp_die( '<center><strong>CANNOT ACTIVATE<br />&nbsp;<br />' . 
					'FPW Honey Pot Links</strong> requires <strong>WordPress 3.3 or higher</strong><br />&nbsp;<br />' . 
					'Press your browser\'s <em>Back</em> button</center>' );		
		}
	}	
	
	//	add plugin to admin bar ( WordPress 3.1+ )	
	public function pluginToAdminBar() {
		if ( current_user_can( 'manage_options' ) ) {
			global 	$wp_admin_bar;
			
			$main = array(
				'id' => 'fpw_plugins',
				'title' => __( 'FPW Plugins', 'fpw-fhpl' ),
				'href' => '#' );

			$subm = array(
				'id' => 'fpw_bar_honey_pot_links',
				'parent' => 'fpw_plugins',
				'title' => __( 'FPW Honey Pot Links', 'fpw-fhpl' ),
				'href' => get_admin_url() . 'options-general.php?page=fpw-honey-pot-links' );

			$addmain = ( is_array( $wp_admin_bar->get_node( 'fpw_plugins' ) ) ) ? false : true;

			if ( $addmain )
				$wp_admin_bar->add_menu( $main );
			$wp_admin_bar->add_menu( $subm );
		}
	}
	
	//	plugin's Settings page
	public function pluginSettings() {
		//	initialize update flags
		$update_options_ok = FALSE;
	
		//	check nonce if any of buttons was pressed
		if ( isset( $_POST[ 'buttonFHPLPressed' ] ) ) {
			if ( !isset( $_POST[ 'fpw-fhpl-nonce' ] ) ) 
				die( '<br />&nbsp;<br /><p style="padding-left: 20px; color: red"><strong>' . __( 'You did not send any credentials!', 'fpw-fhpl' ) . '</strong></p>' );
			if ( !wp_verify_nonce( $_POST[ 'fpw-fhpl-nonce' ], 'fpw-fhpl-nonce' ) ) 
				die( '<br />&nbsp;<br /><p style="padding-left: 20px; color: red;"><strong>' . __( 'You did not send the right credentials!', 'fpw-fhpl' ) . '</strong></p>' );

			//	check ok - update options
			$this->pluginOptions[ 'clean' ] = ( isset( $_POST[ 'cleanup' ] ) ) ? true : false;
			$this->pluginOptions[ 'abar' ] = ( isset( $_POST[ 'abar' ] ) ) ? true : false;
			$this->pluginOptions[ 'lpos' ] = $_POST[ 'fpw-radio-lpos' ];
			$this->pluginOptions[ 'link' ] = esc_html( $_POST[ 'fpwfhpllink' ] );
		
			$update_options_ok = ( update_option( 'fpw_honey_pot_links_opt', $this->pluginOptions ) );
		
			// 	if any changes to options then check uninstall file's extension
			if ( $update_options_ok ) 
				$this->pluginActivate();

			//	check if remove button was pressed
			if ( 'Remove' == $_POST[ 'buttonFHPLPressed' ] ) {
				$debut = 0; //The first article to be displayed
				$myposts = get_posts('numberposts=-1&offset=$debut&post_type=any');
				remove_filter( 'wp_insert_post_data', array( &$this, 'insertHoneyPotLink'), 10, 2 );
				foreach ( $myposts as $post ) 
					if ( ( ( 'post' == $post->post_type ) || ( 'page' == $post->post_type ) ) && ( 'publish' == $post->post_status ) ) {
						$pos = strpos( $post->post_content, html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) ) );
						if ( !( FALSE === $pos ) ) {
							$p = array();
							$p[ 'ID' ] = $post->ID;
							$p[ 'post_content' ] = str_replace( html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) ), '', $post->post_content );
							wp_update_post( $p );
						}
					}
				add_filter( 'wp_insert_post_data', array( &$this, 'insertHoneyPotLink' ), 10, 2 );
			}

			//	check if add button was pressed
			if ( 'Add' == $_POST[ 'buttonFHPLPressed' ] ) {
				$debut = 0; //The first article to be displayed
				$myposts = get_posts('numberposts=-1&offset=$debut&post_type=any');
				remove_filter( 'wp_insert_post_data', array( &$this, 'insertHoneyPotLink'), 10, 2 );
				foreach ( $myposts as $post ) 
					if ( ( ( 'post' == $post->post_type ) || ( 'page' == $post->post_type ) ) && ( 'publish' == $post->post_status ) ) {
						$pos = strpos( $post->post_content, html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) ) );
						if ( FALSE === $pos ) {
							$p = array();
							$p[ 'ID' ] = $post->ID;
							if ( 'before' == $this->pluginOptions[ 'lpos' ] ) {
								$p[ 'post_content' ] = html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) ) . $post->post_content;
							} else {
								$p[ 'post_content' ] = $post->post_content . html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) );
							}
							wp_update_post( $p );
						}
					}
				add_filter( 'wp_insert_post_data', array( &$this, 'insertHoneyPotLink' ), 10, 2 );
			}
		}

		/*	------------------------------
		Settings page HTML starts here
		--------------------------- */
		
		echo '<div class="wrap">' . PHP_EOL;
		echo '<div id="icon-options-general" class="icon32"></div><h2 id="fhpl-settings-title">' . __( 'FPW Honey Pot Links', 'fpw-fhpl' ) . ' (' . $this->pluginVersion . ')</h2>';

		//	check if any of submit buttons was pressed
		if ( isset( $_POST[ 'buttonFHPLPressed' ] ) ) { 
			//	display message about update status
			if ( 'Update' == $_POST[ 'buttonFHPLPressed' ] )
				if ( $update_options_ok ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Updated successfully.', 'fpw-fhpl' ) . '</strong></p></div>';
				} else {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'No changes detected. Nothing to update.', 'fpw-fhpl' ) . '</strong></p></div>';
				}

			//	display message about add status
			if ( 'Add' == $_POST[ 'buttonFHPLPressed' ] )
				echo '<div id="message" class="updated fade"><p><strong>' . __( 'Added Honey Pot links to existing posts / pages successfully.', 'fpw-fhpl' ) . '</strong></p></div>';

			//	display message about remove status
			if ( 'Remove' == $_POST[ 'buttonFHPLPressed' ] )
				echo '<div id="message" class="updated fade"><p><strong>' . __( 'All Honey Pot links removed successfully.', 'fpw-fhpl' ) . '</strong></p></div>';
		}
		
		//	the form starts here
		echo '<p>';
		echo '<form name="fpw_fhpl_form" action="';
		print '?page=' . basename( __FILE__, '.class.php' );
		echo '" method="post">';

		//	protect this form with nonce
		echo '<input name="fpw-fhpl-nonce" type="hidden" value="' . wp_create_nonce( 'fpw-fhpl-nonce' ) . '" />';

		//	cleanup checkbox
		echo '<input type="checkbox" name="cleanup" value="yes"';
		if ( $this->pluginOptions[ 'clean' ] ) 
			echo ' checked';
		echo '> ' . __( "Remove plugin's data from database on uninstall", 'fpw-fhpl' ) . '<br />';

		//	add plugin to admin bar checkbox
		echo '<input type="checkbox" name="abar" value="yes"';
		if ( $this->pluginOptions[ 'abar' ] ) 
			echo ' checked';
		echo '> ' . __( 'Add this plugin to the Admin Bar', 'fpw-fhpl' ) . '<br /><br />';
		
		//	link position
		echo __( 'Link position relative to content:' , 'fpw-fhpl' ) . ' ';
		echo '<strong>' . __( 'before', 'fpw-fhpl' ) . '</strong> <input type="radio" name="fpw-radio-lpos" value="before"';
		if ( 'before' == $this->pluginOptions[ 'lpos' ] ) echo ' checked';
		echo '> | ';
		echo '<strong>' . __( 'after', 'fpw-fhpl' ) . '</strong> <input type="radio" name="fpw-radio-lpos" value="after"';
		if ( 'after' == $this->pluginOptions[ 'lpos' ] ) echo ' checked';
		echo '><br />';
		
		//	link
		echo __( 'Link', 'fpw-fhpl' ) . ': ';
		echo '<input type="text" name="fpwfhpllink" size="80" maxlength="255" value="'; 
		echo str_replace( '\\', '', $this->pluginOptions[ 'link' ] );
		echo '" style="text-align:left"><br /><br />';

		//	submit buttons
		echo '<br /><div class="inputbutton"><input title="' . 
			 __( 'Writes modified settings to the database', 'fpw-fhpl' ) . 
		 	 '" onclick="confirmUpdate();" id="update" class="button-primary fpw-submit" type="button" name="fpw_fhpl_submit" value="' . __( 'Update', 'fpw-fhpl' ) . '" /> ';
		echo '<input onclick="confirmAdd();" title="' . 
		 	 __( 'Adds Honey Pot links to all existing posts / pages', 'fpw-fhpl' ) . 
		 	 '" id="add" class="button-primary fpw-submit" type="button" name="fpw_fhpl_submit_apply" value="' . __( 'Add Links', 'fpw-fhpl' ) . '" /> ';
		echo '<input onclick="confirmRemove();" title="' . 
			 __( 'Removes Honey Pot links from all existing posts / pages', 'fpw-fhpl' ) . 
		 	 '" id="remove" class="button-primary fpw-submit" type="button" name="fpw_fhpl_submit_remove" value="' . __( 'Remove Links', 'fpw-fhpl' ) . '" />';
		echo '<input id="buttonFHPLPressed" type="hidden" value="" name="buttonFHPLPressed" /></div>';

		//	end of form
		echo '</form>';
		echo '</p>';
		echo '</div>';
	}

	//	get plugin's options ( build if not exists )
	private function getOptions() {
	
		$needs_update = FALSE;
		$opt = get_option( 'fpw_honey_pot_links_opt' );
	
		if ( !is_array( $opt ) ) {
			$needs_update = TRUE;
			$opt = array( 
				'clean'		=> FALSE,
				'abar'		=> FALSE,
				'lpos'		=> 'before',
				'link'		=> '' );
		} else {
			if ( !array_key_exists( 'clean', $opt ) || !is_bool( $opt[ 'clean' ] ) ) { 
				$needs_update = TRUE;
				$opt[ 'clean' ] = FALSE;
			}
			if ( !array_key_exists( 'abar', $opt ) || !is_bool( $opt[ 'abar' ] ) ) { 
				$needs_update = TRUE;
				$opt[ 'abar' ] = FALSE;
			}
			if ( $needs_update ) 
				update_option( 'fpw_honey_pot_links_opt', $opt );
		}
		return $opt;
	}
	
	//	add Honey Pot link to post / page before updating database
	public function insertHoneyPotLink( $data , $postarr ) {
		$pos = strpos( esc_html( $data[ 'post_content' ] ), $this->pluginOptions[ 'link' ] );
		if ( FALSE === $pos )  
			if ( 'before' == $this->pluginOptions[ 'lpos' ] ) {
				$data[ 'post_content' ] = html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) ) . $data[ 'post_content' ];
			} else {
				$data[ 'post_content' ] = $data[ 'post_content' ] . html_entity_decode( str_replace( '\\', '', $this->pluginOptions[ 'link' ] ) );
			}
		return $data;
	}
}
?>