<?php
/*
Plugin Name: Custom Login Widget with Cubepoints Integration
Plugin URI: http://www.rogermacrae.com
Description: A customizable login widget that will allow you to add a welcome message, the users avatar, and custom links.
Version: 2.4
Author: Roger MacRae
Author URI: http://www.rogermacrae.com
License: GPL

This software comes without any warranty, express or otherwise.

*/

if (is_admin()) include( WP_PLUGIN_DIR.'/custom-login-widget-with-cube-points-integration/admin/iq-custom-login-admin.php' );

function iq_custom_login_load_widgets() {
	register_widget( 'IQ_Custom_Login' );
}

class IQ_Custom_Login extends WP_Widget {

	function IQ_Custom_Login() {
		$widget_ops = array( 'classname' => 'iqclwidget', 'description' => __('A custom login widget that has cube points integration, the ability to add custom links, the ability to show avatars, and can be styled via CSS.', 'iqclwidget') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'iq-cl-widget' );

		$this->WP_Widget( 'iq-cl-widget', __('Custom Login Widget', 'iqclwidget'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		if (!is_user_logged_in()) {
		$cltitle = get_option('customlogin_heading');
	
		echo $before_widget;

		if ( $cltitle )
			echo $before_title . $cltitle . $after_title;
			
			iq_custom_login_form();

		$links = '';	
		if (get_option('users_can_register') && get_option('customlogin_register_link')=='1') { 
			if (!is_multisite()) {
				$links .= '<li><a href="'.site_url().'/wp-login.php?action=register" rel="nofollow">Register</a></li>';
			} else {
				$links .= '<li><a href="'.site_url().'/wp-signup.php" rel="nofollow">Register</a></li>';
			}
		}
		if (get_option('customlogin_forgotton_link')=='1') { 
			$links .= '<li><a href="'.wp_lostpassword_url().'" rel="nofollow">Lost Password</a></li>';
		}
		if ($links) {
			echo '<ul class="customlogin_otherlinks">'.$links.'</ul>';
			}
		}
		else
		{
			$current_user = wp_get_current_user();
			$cltitle = str_replace('%username%', ucwords($current_user->display_name), get_option('customlogin_welcome_heading'));
		echo $before_widget . $before_title .$cltitle. $after_title;
		if (get_option('customlogin_avatar')=='1') {
			echo '<div class="avatar_container">'.get_avatar($current_user->ID, $size = '60').'</div>';
		}
		echo '<ul class="pagenav">';
		if(isset($current_user->user_level) && $current_user->user_level) $level = $current_user->user_level;
		$links = do_shortcode(trim(get_option('customlogin_logged_in_links')));
		$links = explode("\n", $links);
		if (sizeof($links)>0)
		foreach ($links as $l) {
			$l = trim($l);
			if (!empty($l)) {
				$link = explode('|',$l);
				if (isset($link[1])) {
					$cap = strtolower(trim($link[1]));
					if ($cap=='true') {
						if (!current_user_can( 'manage_options' )) continue;
					} else {
						if (!current_user_can( $cap )) continue;
					}
				}
				// Parse %USERNAME%
				$link[0] = str_replace('%USERNAME%',sanitize_title($current_user->user_login),$link[0]);
				$link[0] = str_replace('%username%',sanitize_title($current_user->user_login),$link[0]);
				// Parse %USERID%
				$link[0] = str_replace('%USERID%',$current_user->ID,$link[0]);
				$link[0] = str_replace('%userid%',$current_user->ID,$link[0]);
				echo '<li class="page_item">'.$link[0].'</li>';
			}
		}
		
		echo '<li class="page_item"><a href="'.esc_url( wp_logout_url($redirect)).'">Logout</a></li>';
		echo "</li>\n	</ul>\n";
		if ((get_option('customlogin_cubepoints')=='1') && (function_exists('cp_getPoints'))) {
			echo "<div class='custom-login-cp'><p>";
		$cubepointstext = get_option('customlogin_cubepoints_text');
			$cubepointstext = str_replace('%points%', cp_getPoints($current_user->ID), get_option('customlogin_cubepoints_text'));
			echo $cubepointstext;
			echo "</p></div>";
			}
		}
		echo $after_widget;
	}
}

function iq_custom_login_form() {
	?>
	<form name='loginform' id='loginform' action='<?php echo site_url(); ?>/wp-login.php' method='post'>
	<ul>
		<li class="iq-cl-field">
			<label class="iq-cl-user-label" for="log">Username</label>
			<div class="iq-cl-input">
				<input type='text' class='iq-cl-user' name='log' value='' />
			</div>
		</li>
		<li class="iq-cl-field">
			<label class="iq-cl-pass-label" for="pwd">Password</label>
			<div class="iq-cl-input">
				<input type='password' class='iq-cl-pass' name='pwd' value='' />
			</div>
		</li>
		<input type='submit' name='wp-submit' class='iq-cl-submit' value='Login' />
	</ul>
	</form>
<?php
}

function iq_custom_login_style(){
        wp_register_style('iq-custom-login', plugins_url('css/style.css', __FILE__));
		wp_enqueue_style('iq-custom-login');
}

add_action('wp_enqueue_scripts', 'iq_custom_login_style');
add_action( 'widgets_init', 'iq_custom_login_load_widgets' );

?>