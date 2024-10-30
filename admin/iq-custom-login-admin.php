<?php

add_action( 'admin_init', 'custom_login_options_init' );
add_action( 'admin_menu', 'custom_login_options_add_page' );

/**
 * Define Options
 */
global $custom_login_options;

$custom_login_options = (
	array( 
		array(
			'', 
			array(
				array(
					'name' 		=> 'customlogin_heading', 
					'std' 		=> __('Login', 'customlogin'), 
					'label' 	=> __('Logged out heading', 'customlogin'),  
					'desc'		=> __('Heading for the widget when the user is logged out.', 'customlogin')
				),
				array(
					'name' 		=> 'customlogin_welcome_heading', 
					'std' 		=> __('Welcome %username%', 'customlogin'), 
					'label' 	=> __('Logged in heading', 'customlogin'),  
					'desc'		=> __('Heading for the widget when the user is logged in.', 'customlogin')
				),
			)
		),
		array(
			__('Links', 'customlogin'), 
			array(
				array(
					'name' 		=> 'customlogin_register_link', 
					'std' 		=> '1', 
					'label' 	=> __('Show Register Link', 'customlogin'),  
					'desc'		=> sprintf( __('The <a href="%s" target="_blank">\'Anyone can register\'</a> setting must be turned on for this option to work.', 'customlogin'), admin_url('options-general.php')),
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'customlogin_forgotton_link', 
					'std' 		=> '1', 
					'label' 	=> __('Show Lost Password Link', 'customlogin'),  
					'desc'		=> '',
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'customlogin_avatar', 
					'std' 		=> '1', 
					'label' 	=> __('Show Logged in Avatar', 'customlogin'),  
					'desc'		=> '',
					'type' 		=> 'checkbox'
				),
				array(
					'name' 		=> 'customlogin_logged_in_links', 
					'std' 		=> "<a href=\"".get_bloginfo('wpurl')."/wp-admin/\">".__('Dashboard','customlogin')."</a>\n<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\">".__('Profile','customlogin')."</a>", 
					'label' 	=> __('Logged in links', 'customlogin'),  
					'desc'		=> sprintf( __('One link per line. Note: Logout link will always show regardless. Tip: Add <code>|true</code> after a link to only show it to admin users or alternatively use a <code>|user_capability</code> and the link will only be shown to users with that capability (see <a href=\'http://codex.wordpress.org/Roles_and_Capabilities\' target=\'_blank\'>Roles and Capabilities</a>).<br/> You can also type <code>%%USERNAME%%</code> and <code>%%USERID%%</code> which will be replaced by the user\'s info. Default: <br/>&lt;a href="%s/wp-admin/"&gt;Dashboard&lt;/a&gt;<br/>&lt;a href="%s/wp-admin/profile.php"&gt;Profile&lt;/a&gt;', 'customlogin'), get_bloginfo('wpurl'), get_bloginfo('wpurl') ),
					'type' 		=> 'textarea'
				),
			)
		),
		array(
			__('Cube Points', 'customlogin'), 
			array(				
				array(
					'name'		=> 'customlogin_cubepoints',
					'std'		=> '0',
					'label'		=> __('Show Cube Points', 'customlogin'),
					'desc'		=> __('Note: You must have the cube points plugin installed', 'customlogin'),
					'type'		=> 'checkbox'
				),
				array(
					'name' 		=> 'customlogin_cubepoints_text', 
					'std' 		=> __('You have earned %points% points', 'customlogin'), 
					'label' 	=> __('Cube Points Text', 'customlogin'),  
					'desc'		=> __('Note: Use %points% to show the number of points', 'customlogin')
				),
			)
		)
	)
);
	
/**
 * Init plugin options to white list our options
 */
function custom_login_options_init() {

	global $custom_login_options;

	foreach($custom_login_options as $section) {
		foreach($section[1] as $option) {
			if (isset($option['std'])) add_option($option['name'], $option['std']);
			register_setting( 'custom-login', $option['name'] );
		}
	}

	
}

/**
 * Load up the menu page
 */
function custom_login_options_add_page() {
	add_options_page(__('Custom Login','customlogin'), __('Custom Login','custom'), 'manage_options', 'custom-login', 'custom_login_options');
}

/**
 * Create the options page
 */
function custom_login_options() {
	global $custom_login_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" .__( 'Custom Login Options','customlogin') . "</h2>"; ?>
		
		<form method="post" action="options.php">
		
			<?php settings_fields( 'custom-login' ); ?>
	
			<?php
			foreach($custom_login_options as $section) {
			
				if ($section[0]) echo '<h3 class="title">'.$section[0].'</h3>';
				
				echo '<table class="form-table">';
				
				foreach($section[1] as $option) {
					
					echo '<tr valign="top"><th scope="row">'.$option['label'].'</th><td>';
					
					if (!isset($option['type'])) $option['type'] = '';
					
					switch ($option['type']) {
						
						case "checkbox" :
						
							$value = get_option($option['name']);
							
							?><input id="<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /><?php
						
						break;
						case "textarea" :
							
							$value = get_option($option['name']);
							
							?><textarea id="<?php echo $option['name']; ?>" class="large-text" cols="50" rows="10" name="<?php echo $option['name']; ?>" placeholder="<?php if (isset($option['placeholder'])) echo $option['placeholder']; ?>"><?php echo esc_textarea( $value ); ?></textarea><?php
						
						break;
						default :
							
							$value = get_option($option['name']);
							
							?><input id="<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" placeholder="<?php if (isset($option['placeholder'])) echo $option['placeholder']; ?>" /><?php
						
						break;
						
					}
					
					if ($option['desc']) echo '<span class="description">'.$option['desc'].'</span>';
					
					echo '</td></tr>';
				}
				
				echo '</table>';
				
			}
			?>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'customlogin'); ?>" />
			</p>
		</form>
	</div>
	<?php
}