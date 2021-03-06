<?php

$ct_plugin_basename = 'cleantalk-spam-protect/cleantalk.php';
$ct_options=ct_get_options();
$ct_data=ct_get_data();

// How many days we use an IP to detect spam.
$ct_ip_penalty_days = 30;

// Timeout to get app server
$ct_server_timeout = 10;

//Adding widjet
function ct_dashboard_statistics_widget() {
	if(current_user_can('activate_plugins')){
		$plugin_settings_link = (is_network_admin() ? "settings.php" : "options-general.php" )."?page=cleantalk";
		wp_add_dashboard_widget(
			'ct_dashboard_statistics_widget',
			__("CleanTalk Anti-Spam Statistics", 'cleantalk')
			."<div class='ct_widget_top_links'>"
				."<img src='".plugins_url('/cleantalk-spam-protect/inc/images/preloader.gif')."' class='ct_preloader'>"
				.sprintf(__("%sRefresh%s", 'cleantalk'),    "<a href='#ct_widget' class='ct_widget_refresh_link'>", "</a>")
				.sprintf(__("%sConfigure%s", 'cleantalk'), "<a href='$plugin_settings_link' class='ct_widget_settings_link'>", "</a>")
			."</div>",
			'ct_dashboard_statistics_widget_output'
		);
	}
}

// Outputs statistics widget content
function ct_dashboard_statistics_widget_output( $post, $callback_args ) {	
	global $ct_data, $ct_options, $current_user;
		
	if(empty($ct_data['brief_data']) || !empty($_POST['ct_brief_refresh'])){
		require_once('cleantalk.class.php');
		$brief_data = getAntispamReportBreif($ct_options['apikey']);
		
		$ct_data['brief_data'] = $brief_data;
		update_option('cleantalk_data', $ct_data);
	}else{
		$brief_data = $ct_data['brief_data'];
	}
			
	if(ct_valid_key()){
		$ct_error = is_string($brief_data) ? true : false;
		$key_is_good = is_string($brief_data) && $brief_data == 'Unknown access key.' ? false : true;
	}else{
		$ct_error = true;
		$key_is_good = false;
		$brief_data = 'Unknown access key.';
	}
	
	if($ct_error){
		$tmp = array();
		for($i=0; $i<7; $i++)
			$tmp[date("Y-m-d", time()-86400*7+86400*$i)] = 0;
		$spam_stat = $tmp;
	}else{
		$spam_stat = $brief_data['spam_stat'];
	}
	
	echo "<div id='ct_widget_wrapper'>";
		// Parsing brief data 'spam_stat' {"yyyy-mm-dd": spam_count, "yyyy-mm-dd": spam_count} to [["yyyy-mm-dd", "spam_count"], ["yyyy-mm-dd", "spam_count"]]
		$to_chart = array();
		foreach($spam_stat as $key => $value)
			$to_chart[] = array($key, $value);
		unset($key, $value);
		$to_chart = json_encode($to_chart);
?>
		<form id='ct_refresh_form' method='POST' action='#ct_widget'>
			<input type='hidden' name='ct_brief_refresh' value='1'>
		</form>
		<h4 class='ct_widget_block_header' style='margin-left: 12px;'><?php _e('7 days anti-spam stats', 'cleantalk'); ?></h4>
		<div class='ct_widget_block ct_widget_chart_wrapper'>
		
			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			<script>
				var ct_chart_data = <?php echo $to_chart; ?>;
			</script>
			<div id='ct_widget_chart'></div>
		</div>
		<h4 class='ct_widget_block_header'><?php _e('Top 5 spam IPs blocked', 'cleantalk'); ?></h4>
		<hr class='ct_widget_hr'>
<?php
	if($key_is_good && !$ct_error){
?>
		<div class='ct_widget_block'>
			<table cellspacing="0">
				<tr>
					<th><?php _e('IP', 'cleantalk'); ?></th>
					<th><?php _e('Country', 'cleantalk'); ?></th>
					<th><?php _e('Block Count', 'cleantalk'); ?></th>
				</tr>
<?php
				foreach($brief_data['top5_spam_ip'] as $val){			
?>				
				<tr>
					<td><?php echo $val[0]; ?></td>
					<td><?php echo $val[1] ? "<img src='https://cleantalk.org/images/flags/".strtolower($val[1]).".png'>" : ''; ?>&nbsp;<?php 
						echo $val[1]
							? (function_exists('locale_get_display_region') 
								? locale_get_display_region('sl-Latn-'.$val[1].'-nedis', substr(get_locale(), 0, 2))
								: $val[1]) 
							: 'Unknown'; ?></td>
					<td style='text-align: center;'><?php echo $val[2]; ?></td>
				</tr>
<?php
				}
?>
			</table>
<?php
			if(!empty($ct_data['user_token'])){
?>
				<a target='_blank' href='https://cleantalk.org/my?user_token=<?php echo $ct_data['user_token']; ?>&cp_mode=antispam'>
					<input class='ct_widget_button' id='ct_widget_button_view_all' type='button' value='View all'>
				</a>
<?php
			}
?>
		</div>

<?php
	}else{
		$plugin_settings_link = (is_network_admin() ? "settings.php" : "options-general.php" )."?page=cleantalk";
		
		if(!$key_is_good){
?>			<div class='ct_widget_block'>
				<form action='<? echo $plugin_settings_link; ?>' method='POST'>
				<h2 class='ct_widget_activate_header'><?php _e('Get Access key to activate Anti-Spam protection!', 'cleantalk'); ?></h2>
					<input class='ct_widget_button ct_widget_activate_button' type='submit' name='get_apikey_auto' value='ACTIVATE'>
				</form>
			</div>
<?php
		}else{
?>
			<div class='ct_widget_block'>
				<h2 class='ct_widget_activate_header'><?php _e('Something went wrong! Server response is: "', 'cleantalk'); ?><u><? echo $brief_data ?></u>".</h2>
<?php
				if(!empty($ct_data['user_token'])){
?>
					<h2 class='ct_widget_activate_header'><?php _e('Please, visit your dashboard.', 'cleantalk'); ?></h2>
					<a target='_blank' href='https://cleantalk.org/my?user_token=<?php echo $ct_data['user_token']; ?>&cp_mode=antispam'>
						<input class='ct_widget_button ct_widget_activate_button ct_widget_resolve_button' type='button' value='VISIT CONTROL PANEL'>
					</a>
<?php
				}
?>
			</div>
<?php	}
	}
	// Notice at the bottom
	if(isset($current_user) && in_array("administrator", $current_user->roles)){
		if(!isset($ct_data['admin_blocked'])){
			$blocked=0;
		}else{
			$blocked=$ct_data['admin_blocked'];
		}
		if($blocked>0){
			$blocked = number_format($blocked, 0, ',', ' ');
			print "<div class='ct_widget_wprapper_total_blocked'>
				<img src='".plugins_url('/cleantalk-spam-protect/inc/images/logo_color.png')."' class='ct_widget_small_logo'/>
				<span>
					<a href='https://cleantalk.org/my/?user_token=".@$ct_data['user_token']."&utm_source=wp-backend&utm_medium=dashboard_widget&cp_mode=antispam' target='_blank'>CleanTalk</a> ";
			printf(
				/* translators: %s: Number of spam messages */
				__( 'has blocked %s spam. The statistics is automatically updated every 24 hours.', 'cleantalk' ), 
				$blocked
			);
			print "</span></div>";
		}
	}
	
echo "</div>";
}

/**
 * Admin action 'wp_ajax_ajax_get_timezone' - Ajax method for getting timezone offset
 */ 
function ct_ajax_get_timezone()
{
	global $ct_data;
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	$ct_data = ct_get_data();
	if(isset($_POST['offset']))
	{
		$ct_data['timezone'] = intval($_POST['offset']);
		update_option('cleantalk_data', $ct_data);
	}
}
 
add_action( 'wp_ajax_ajax_get_timezone', 'ct_ajax_get_timezone' );


/**
 * Admin action 'admin_enqueue_scripts' - Enqueue admin script of reloading admin page after needed AJAX events
 * @param 	string $hook URL of hooked page
 */
function ct_enqueue_scripts($hook) {
	
	global $cleantalk_plugin_version, $ct_data, $ct_options;
	
	// Scripts to all admin pages
	wp_enqueue_script('ct_admin_js_notices',                  plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-admin.js'),                  array(), $cleantalk_plugin_version);
	wp_enqueue_style ('ct_admin_css',                         plugins_url('/cleantalk-spam-protect/assets/css/cleantalk-admin.css'),                array(), $cleantalk_plugin_version, 'all');
	
	// Scripts & Styles to main dashboard page
	if($hook == 'index.php'){
		wp_enqueue_script('ct_admin_js_widget_dashboard', 	  plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-dashboard-widget.js'),  	    array(), $cleantalk_plugin_version);
		wp_enqueue_style('ct_admin_css_widget_dashboard',     plugins_url('/cleantalk-spam-protect/assets/css/cleantalk-dashboard-widget.css'),     array(), $cleantalk_plugin_version, 'all');
	}
	
	// Scripts & Styles for CleanTalk's settings page
	if( $hook == 'settings_page_cleantalk' ){
		wp_enqueue_script('cleantalk_admin_js_settings_page', plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-admin-settings-page.js'),    array(), $cleantalk_plugin_version);
		wp_enqueue_style('cleantalk_admin_css_settings_page', plugins_url().'/cleantalk-spam-protect/assets/css/cleantalk-admin-settings-page.css', array(), $cleantalk_plugin_version, 'all');
		
		$ajax_nonce = wp_create_nonce( "ct_secret_nonce" );
		wp_localize_script( 'jquery', 'ctSettingsPage', array(
			'ct_ajax_nonce' => $ajax_nonce
		));
	}
	
	// Scripts for comments check
	if( $hook == 'comments_page_ct_check_spam' || $hook == 'edit-comments.php'){
		
		wp_enqueue_style('cleantalk_admin_css_settings_page', plugins_url().'/cleantalk-spam-protect/assets/css/cleantalk-spam-check.css', array(), $cleantalk_plugin_version, 'all');
		
		$ajax_nonce = wp_create_nonce( "ct_secret_nonce" );
		$user_token = !empty($ct_data['user_token']) ? $ct_data['user_token'] : '';
		$show_check_links = !empty($ct_options['show_check_links']) ? $ct_options['show_check_links'] : 0; 
		
		wp_enqueue_script('ct_comments_checkspam',            plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-comments-checkspam.js'),           array(), $cleantalk_plugin_version);
		wp_enqueue_script('ct_comments_editscreen',           plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-comments-editscreen.js'),          array(), $cleantalk_plugin_version.time());
		
		wp_localize_script( 'jquery', 'ctCommentsCheck', array(
			'ct_ajax_nonce' => $ajax_nonce,
			'ct_timeout_confirm'          => __('Failed from timeout. Going to check comments again.', 'cleantalk'),
			'ct_comments_added'           => __('Added', 'cleantalk'),
			'ct_comments_added_after'     => __('comments', 'cleantalk'),
			'ct_confirm_deletion_all'     => __('Delete all spam comments?', 'cleantalk'),
			'ct_confirm_deletion_checked' => __('Delete checked comments?', 'cleantalk')
		));
		wp_localize_script( 'jquery', 'ctCommentsScreen', array(
			'ct_ajax_nonce'               => $ajax_nonce,
			'spambutton_text'             => __("Find spam-comments", 'cleantalk'),
			'spambutton_text_show'        => __("Show spam-comments", 'cleantalk'),
			'ct_feedback_msg_whitelisted' => __("The sender has been whitelisted.", 'cleantalk'),
			'ct_feedback_msg_blacklisted' => __("The sender has been blacklisted.", 'cleantalk'),
			'ct_feedback_msg'             => sprintf(__("Feedback has been sent to %sCleanTalk Dashboard%s.", 'cleantalk'), $user_token ? "<a target='_blank' href=https://cleantalk.org/my?user_token={$user_token}&cp_mode=antispam>" : '', $user_token ? "</a>" : ''),
			'ct_show_check_links'		  => $show_check_links,
			'ct_img_src_new_tab'          => plugin_dir_url(__FILE__)."images/new_window.gif",
			
		));
	}
	
	// Scripts for users check
	if( $hook == 'users_page_ct_check_users' || $hook == 'users.php'){
		
		wp_enqueue_style('cleantalk_admin_css_settings_page', plugins_url().'/cleantalk-spam-protect/assets/css/cleantalk-spam-check.css', array(), $cleantalk_plugin_version, 'all');
		
		$current_user = wp_get_current_user();
		$ajax_nonce = wp_create_nonce( "ct_secret_nonce" );
		$show_check_links = !empty($ct_options['show_check_links']) ? $ct_options['show_check_links'] : 0; 
		
		wp_enqueue_script('ct_users_checkspam',               plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-users-checkspam.js'),              array(), $cleantalk_plugin_version);
		wp_enqueue_script('ct_users_editscreen',              plugins_url('/cleantalk-spam-protect/assets/js/cleantalk-users-editscreen.js'),             array(), $cleantalk_plugin_version);
		
		wp_localize_script( 'jquery', 'ctUsersCheck', array(
			'ct_ajax_nonce'               => $ajax_nonce,
			'ct_timeout'                  => __('Failed from timeout. Going to check users again.', 'cleantalk'),
			'ct_timeout_delete'           => __('Failed from timeout. Going to run a new attempt to delete spam users.', 'cleantalk'),
			'ct_inserted'                 => __('Inserted', 'cleantalk'),
			'ct_iusers'                   => __('users.', 'cleantalk'),
			'ct_confirm_deletion_all'     => __('Delete all spam users?', 'cleantalk'),
			'ct_confirm_deletion_checked' => __('Delete checked users?', 'cleantalk'),
			'ct_csv_filename'             => "user_check_by_".$current_user->user_login,
			'ct_bad_csv'                  => __("File doesn't exist. File will be generated while checking. Please, press \"Check for spam\".")
		));
		wp_localize_script( 'jquery', 'ctUsersScreen', array(
			'spambutton_users_text'       => __("Find spam-users", 'cleantalk'),
			'spambutton_users_text_show'  => __("Show spam-users", 'cleantalk'),
			'ct_show_check_links'		  => $show_check_links,
			'ct_img_src_new_tab'          => plugin_dir_url(__FILE__)."images/new_window.gif"
		));
	}	
}

/**
 * Admin action 'admin_menu' - Add the admin options page
 */
function ct_admin_add_page() {
	global $ct_plugin_name;	
	
	if(is_network_admin())
		add_submenu_page("settings.php", __('CleanTalk settings', 'cleantalk'), $ct_plugin_name, 'manage_options', 'cleantalk', 'ct_settings_page');
	else
		add_options_page(__('CleanTalk settings', 'cleantalk'), $ct_plugin_name, 'manage_options', 'cleantalk', 'ct_settings_page');
	
}

/**
 * Admin action 'admin_init' - Add the admin settings and such
 */
function ct_admin_init(){

	global $ct_server_timeout, $show_ct_notice_autokey, $ct_notice_autokey_label, $ct_notice_autokey_value, $show_ct_notice_renew, $ct_notice_renew_label, $show_ct_notice_trial, $ct_notice_trial_label, $show_ct_notice_online, $ct_notice_online_label, $renew_notice_showtime, $trial_notice_showtime, $ct_plugin_name, $ct_options, $ct_data, $trial_notice_check_timeout, $account_notice_check_timeout, $ct_user_token_label, $cleantalk_plugin_version, $notice_check_timeout, $renew_notice_check_timeout, $ct_agent_version;
		
    $ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	//Update logic
	$is_installing = get_option('ct_installing');
	if($is_installing){
		delete_option('ct_installing');
		$ct_data['plugin_version'] = $cleantalk_plugin_version;
		$ct_data['next_account_status_check'] = 0; // Initilazing Notice_paid_till
		update_option( 'cleantalk_data' , $ct_data);
	}else{
		$current_version = (isset($ct_data['plugin_version']) ? $ct_data['plugin_version'] : '5.66');
		if($current_version != $cleantalk_plugin_version){
			if(is_main_site()){
				require_once('cleantalk-updater.php');
				$result = ct_run_update_actions($current_version, $cleantalk_plugin_version);
				//If update is successfull
				if($result === true){
					ct_send_feedback(
						'0:' . $ct_agent_version // 0 - request_id, agent version.
					);
					$ct_data['plugin_version'] = $cleantalk_plugin_version;
					update_option( 'cleantalk_data' , $ct_data);
				}
			}
			
		}
	}	
	
	// Set notice if came from report
	if(isset($_GET['from_report']) && $_GET['from_report']){
		$ct_data['ct_show_notice_from_report'] = true;
		update_option('cleantalk_data', $ct_data);
	}
	
	// Drop debug data
	if(isset($_POST['ct_debug_reset']) && $_POST['ct_debug_reset']){
		$ct_data['ct_debug_reset'] = true;
		update_option('cleantalk_data', $ct_data);
	}
	
	// Feedback when saving settings
	if(isset($_POST['option_page']) && $_POST['option_page'] == 'cleantalk_settings' && isset($_POST['cleantalk_settings']['apikey'])){
		$ct_options['apikey']=$_POST['cleantalk_settings']['apikey'];
		update_option('cleantalk_settings', $ct_options);
        ct_send_feedback(
            '0:' . $ct_agent_version // 0 - request_id, agent version.
        );
	}

	/*$show_ct_notice_trial = false;
	if (isset($_COOKIE[$ct_notice_trial_label]))
	{
		if ($_COOKIE[$ct_notice_trial_label] == 1)
		{
			$show_ct_notice_trial = true;
		}
	}
	$show_ct_notice_renew = false;
	if (isset($_COOKIE[$ct_notice_renew_label]))
	{
		if ($_COOKIE[$ct_notice_renew_label] == 1)
		{
			$show_ct_notice_renew = true;
		}
	}*/
	$show_ct_notice_autokey = false;
	if (isset($_COOKIE[$ct_notice_autokey_label]) && !empty($_COOKIE[$ct_notice_autokey_label]))
	{
		$show_ct_notice_autokey = true;
		$ct_notice_autokey_value = base64_decode($_COOKIE[$ct_notice_autokey_label]);
		setcookie($ct_notice_autokey_label, '', 1, '/');
	}
	
	//Auto getting key
	if (isset($_POST['get_apikey_auto']))
	{
		$website = parse_url(get_option('siteurl'),PHP_URL_HOST);
		$platform = 'wordpress';
		$timezone = $_POST['ct_admin_timezone'];
		
		if(!function_exists('getAutoKey'))
			require_once('cleantalk.class.php');
		
		$result = getAutoKey(ct_get_admin_email(), $website, $platform, $timezone);

		if ($result)
		{
			$ct_data['next_account_status_check']=0;
			update_option('cleantalk_data', $ct_data);
			$result = json_decode($result, true);
			
			if (isset($result['data']) && is_array($result['data']))
				$result = $result['data'];
			
			if(isset($result['user_token'])){
				$ct_data['user_token'] = $result['user_token'];
				update_option('cleantalk_data', $ct_data);
			}
			
			if (isset($result['auth_key']) && !empty($result['auth_key'])){
				$_POST['cleantalk_settings']['apikey'] = $result['auth_key'];
				$ct_options['apikey']=$result['auth_key'];
				update_option('cleantalk_settings', $ct_options);
			}else{
				setcookie($ct_notice_autokey_label, (string) base64_encode($result['error_message']), 0, '/');
			}
		}else{
			setcookie($ct_notice_autokey_label, (string) base64_encode(sprintf(__('Unable to connect to %s.', 'cleantalk'),  'api.cleantalk.org')), 0, '/');
		}
	}
	
	//Account's status check cron job
	if (time() > $ct_data['next_account_status_check'] || isset($_POST['cleantalk_settings']['apikey'])){
		
        $notice_check_timeout = $account_notice_check_timeout; 
		
		$api_key = isset($_POST['cleantalk_settings']['apikey']) ? $_POST['cleantalk_settings']['apikey'] : $ct_options['apikey'];
		
		if(!function_exists('noticePaidTill'))
			require_once('cleantalk.class.php');
		
		$result = noticePaidTill($api_key);			
			
		if ($result){
			
			$result = json_decode($result, true);
			
			if (isset($result['data']) && is_array($result['data']))
				$result = $result['data'];

			if(isset($result['spam_count']))
				$ct_data['admin_blocked']=$result['spam_count'];

			if (isset($result['show_notice'])){
				
				if ($result['show_notice'] == 1 && isset($result['trial']) && $result['trial'] == 1){
					$notice_check_timeout = $trial_notice_check_timeout;
					$show_ct_notice_trial = true;
					$ct_data['show_ct_notice_trial']=1;
				}
				
				if ($result['show_notice'] == 1 && isset($result['renew']) && $result['renew'] == 1){
					$notice_check_timeout = $renew_notice_check_timeout;
					$show_ct_notice_renew = true;
					$ct_data['show_ct_notice_renew']=1;
				}
				
				if (isset($result['show_review']) && $result['show_review'] == 1)
					$ct_data['show_ct_notice_review'] = 1;
				
				if ($result['show_notice'] == 0)
					$notice_check_timeout = $account_notice_check_timeout;
				
				$ct_data['show_ct_notice_trial']=(int) $show_ct_notice_trial;
				$ct_data['show_ct_notice_renew']= (int) $show_ct_notice_renew;
			}
			
			if (isset($result['moderate_ip']) && $result['moderate_ip'] == 1){
				$ct_data['moderate_ip'] = 1;
				$ct_data['ip_license'] = $result['ip_license'];
			}else{
				$ct_data['moderate_ip'] = 0;
				$ct_data['ip_license'] = 0;
			}
			
			if (isset($result['user_token']))
				$ct_data['user_token'] = $result['user_token'];
		}
		// Save next status request time
		$ct_data['next_account_status_check'] = time() + $notice_check_timeout * 3600;
		update_option('cleantalk_data', $ct_data);
		
		/*if ($result)
		{
			if($show_ct_notice_trial == true)
			{
				setcookie($ct_notice_trial_label, (string) $show_ct_notice_trial, strtotime("+$trial_notice_showtime minutes"), '/');
			}
			if($show_ct_notice_renew == true)
			{
				setcookie($ct_notice_renew_label, (string) $show_ct_notice_renew, strtotime("+$renew_notice_showtime minutes"), '/');
			}
		}*/
	}

	$show_ct_notice_online = '';
	if (isset($_COOKIE[$ct_notice_online_label]))
	{
		if ($_COOKIE[$ct_notice_online_label] === 'BAD_KEY')
		{
			$show_ct_notice_online = 'N';
		}
		else if (time() - $_COOKIE[$ct_notice_online_label] <= 5)
		{
			$show_ct_notice_online = 'Y';
		}
	}

	//ct_init_session();
	
	if(stripos($_SERVER['REQUEST_URI'],'options.php')!==false || stripos($_SERVER['REQUEST_URI'],'options-general.php')!==false || stripos($_SERVER['REQUEST_URI'],'network/settings.php')!==false)
	{
	
		register_setting('cleantalk_settings', 'cleantalk_settings', 'ct_settings_validate');
		add_settings_section('cleantalk_settings_main', __($ct_plugin_name, 'cleantalk'), 'ct_section_settings_main', 'cleantalk');

		if(!empty($ct_data['debug']))
			add_settings_section('cleantalk_debug_section', '<hr>Debug', 'ct_section_debug', 'cleantalk');
		add_settings_section('cleantalk_settings_state', "<hr>".__('Protection is active', 'cleantalk'), 'ct_section_settings_state', 'cleantalk');
		add_settings_section('cleantalk_settings_banner', "<hr>", '', 'cleantalk');
		add_settings_section('cleantalk_settings_anti_spam', "<a href='#' class='ct_support_link'>".__('Advanced settings', 'cleantalk')."</a>", 'ct_section_settings_anti_spam', 'cleantalk');
		
		if(!defined('CLEANTALK_ACCESS_KEY'))
		{
			add_settings_field('cleantalk_apikey', __('Access key', 'cleantalk'), 'ct_input_apikey', 'cleantalk', 'cleantalk_settings_main');
		}
		else
		{
			add_settings_field('cleantalk_apikey', '', 'ct_input_apikey', 'cleantalk', 'cleantalk_settings_main');
		}
		
		//Forms for protection
		add_settings_field('cleantalk_title_fiels_for_protect', "", 'ct_input_what_fields_should_be_protected', 'cleantalk', 'cleantalk_settings_anti_spam');//Title settings
		add_settings_field('cleantalk_registrations_test', __('Registration forms', 'cleantalk'), 'ct_input_registrations_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_comments_test', __('Comments form', 'cleantalk'), 'ct_input_comments_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_contact_forms_test', __('Contact forms', 'cleantalk'), 'ct_input_contact_forms_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_general_contact_forms_test', __('Custom contact forms', 'cleantalk'), 'ct_input_general_contact_forms_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_wc_checkout_test', __('WooCommerce checkout form', 'cleantalk'), 'ct_input_wc_chekout_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_check_external', __('Protect external forms', 'cleantalk'), 'ct_input_check_external', 'cleantalk', 'cleantalk_settings_anti_spam');
        add_settings_field('cleantalk_check_internal', __('Protect internal forms', 'cleantalk'), 'ct_input_check_internal', 'cleantalk', 'cleantalk_settings_anti_spam');
		
		//Comments and messages
		add_settings_field('cleantalk_title_comments_and_messages', "", 'ct_input_comments_and_messages', 'cleantalk', 'cleantalk_settings_anti_spam');//Title settings
		add_settings_field('cleantalk_bp_private_messages', __('buddyPress private messages', 'cleantalk'), 'ct_input_bp_private_messages', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_check_comments_number', __("Don't check trusted user's comments", 'cleantalk'), 'ct_input_check_comments_number', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_remove_old_spam', __('Automatically delete spam comments', 'cleantalk'), 'ct_input_remove_old_spam', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_remove_links_from_comments', __('Remove links from approved comments', 'cleantalk'), 'ct_input_remove_links_from_approved_comments', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_show_check_links', __('Show links to check Emails, IPs for spam.', 'cleantalk'), 'ct_input_show_check_links', 'cleantalk', 'cleantalk_settings_anti_spam');
		
		//Data processing
		add_settings_field('cleantalk_title_data_processing', "", 'ct_input_data_processing', 'cleantalk', 'cleantalk_settings_anti_spam');//Title settings
		add_settings_field('cleantalk_protect_logged_in', __("Protect logged in Users", 'cleantalk'), 'ct_input_protect_logged_in', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_use_ajax', __('Use AJAX for JavaScript check', 'cleantalk'), 'ct_input_use_ajax', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_general_postdata_test', __('Check all post data', 'cleantalk'), 'ct_input_general_postdata_test', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_set_cookies', __("Set cookies", 'cleantalk'), 'ct_input_set_cookies', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_ssl_on', __("Use SSL", 'cleantalk'), 'ct_input_ssl_on', 'cleantalk', 'cleantalk_settings_anti_spam');
		
		//Administrator Panel
		add_settings_field('cleantalk_title_administrator_panel', "", 'ct_input_administrator_panel', 'cleantalk', 'cleantalk_settings_anti_spam');//Title settings
		add_settings_field('cleantalk_show_adminbar', __('Show statistics in admin bar', 'cleantalk'), 'ct_input_show_adminbar', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_all_time_counter', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".__('Show All-time counter', 'cleantalk'), 'ct_input_all_time_counter', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_daily_conter', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".__('Show 24 hours counter', 'cleantalk'), 'ct_input_daily_counter', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_sfw_counter', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".__('SpamFireWall counter', 'cleantalk'), 'ct_input_sfw_counter', 'cleantalk', 'cleantalk_settings_anti_spam');
		

		add_settings_field('cleantalk_collect_details', __('Collect details about browsers', 'cleantalk'), 'ct_input_collect_details', 'cleantalk', 'cleantalk_settings_anti_spam');
		add_settings_field('cleantalk_show_link', __('Tell others about CleanTalk', 'cleantalk'), 'ct_input_show_link', 'cleantalk', 'cleantalk_settings_banner');
		add_settings_field('cleantalk_spam_firewall', __('SpamFireWall', 'cleantalk'), 'ct_input_spam_firewall', 'cleantalk', 'cleantalk_settings_banner');
	}
}

/**
 * Admin callback function - Displays description of 'main' plugin parameters section
 */
function ct_section_settings_main() {
/*
	$ct_options=ct_get_options();

	$is_wpmu = false;
	if(defined('CLEANTALK_ACCESS_KEY')) {
	    $is_wpmu = true;
	}

	if (ct_valid_key($ct_options['apikey']) !== false || $is_wpmu) {
        return true;
    }
    $message = "<p>Please wait we are registering account welcome@cleantalk.org to finish plugin setup...</p>";
    echo $message;

	?>
	<script type="text/javascript">
        var api_url = 'https://localhost/test.php';

        var req ;

        // Browser compatibility check  		
        if (window.XMLHttpRequest) {
           req = new XMLHttpRequest();
            } else if (window.ActiveXObject) {

         try {
           req = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {

           try {
             req = new ActiveXObject("Microsoft.XMLHTTP");
           } catch (e) {}
         }

        }


        var req = new XMLHttpRequest();
        req.open("GET", api_url, true);
        req.onreadystatechange = function () {
            console.log(req.getResponseHeader('HTTP_COOKIE'));
        }

        req.send(null);
    </script>
	
	<?php
*/
    return true;
}

/**
 * Admin callback function - Displays description of 'anti-spam' plugin parameters section
 */
function ct_section_settings_anti_spam() {
	return true;
}

add_action( 'admin_bar_menu', 'ct_add_admin_menu', 999 );

function ct_input_all_time_counter() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	$value=(isset($ct_options['all_time_counter']) ? @intval($ct_options['all_time_counter']) : 0);
	$value2=(isset($ct_options['show_adminbar']) ? @intval($ct_options['show_adminbar']) : 0);

	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_all_time_counter1' name='cleantalk_settings[all_time_counter]' value='1' ".($value=='1'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_all_time_counter1'> ".__('Yes')."</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_all_time_counter0' name='cleantalk_settings[all_time_counter]' value='0' ".($value=='0'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_all_time_counter0'> ".__('No')."</label>";
	@admin_addDescriptionsFields(sprintf(__('Display all-time requests counter in the admin bar. Counter displays number of requests since plugin installation.', 'cleantalk'),  $ct_options['all_time_counter']));
}

function ct_input_daily_counter() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	$value=(isset($ct_options['daily_counter']) ? @intval($ct_options['daily_counter']) : 0);
	$value2=(isset($ct_options['show_adminbar']) ? @intval($ct_options['show_adminbar']) : 0);
	
	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_daily_counter1' name='cleantalk_settings[daily_counter]' value='1' ".($value=='1'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_daily_counter1'> ".__('Yes')."</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_daily_counter0' name='cleantalk_settings[daily_counter]' value='0' ".($value=='0'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_daily_counter0'> ".__('No')."</label>";
	@admin_addDescriptionsFields(sprintf(__('Display daily requests counter in the admin bar. Counter displays number of requests of the past 24 hours.', 'cleantalk'),  $ct_options['all_time_counter']));
}

function ct_input_sfw_counter() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	$value=(isset($ct_options['sfw_counter']) ? @intval($ct_options['sfw_counter']) : 0);
	$value2=(isset($ct_options['show_adminbar']) ? @intval($ct_options['show_adminbar']) : 0);

	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_sfw_counter1' name='cleantalk_settings[sfw_counter]' value='1' ".($value=='1'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_sfw_counter1'> ".__('Yes')."</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' class='ct-depends-of-show-adminbar' id='cleantalk_sfw_counter0' name='cleantalk_settings[sfw_counter]' value='0' ".($value=='0'?'checked':'').($value2=='0'?' disabled':'')." /><label for='cleantalk_sfw_counter0'> ".__('No')."</label>";
	@admin_addDescriptionsFields(sprintf(__('Display all-time requests counter in the admin bar. Counter displays number of requests since plugin installation.', 'cleantalk'),  $ct_options['sfw_counter']));
}

function ct_add_admin_menu( $wp_admin_bar ) {
// add a parent item
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	if(isset($ct_options['show_adminbar']))
	{
		$value = @intval($ct_options['show_adminbar']);
	}
	else
	{
		$value=1;
	}
	
	if (current_user_can('activate_plugins')&&$value==1 && ct_valid_key($ct_options['apikey']) !== false) {
        $ct_data=ct_get_data();
        
		//Create daily counter
		if(!isset($ct_data['array_accepted'])){
			$ct_data['array_accepted']=Array();
			$ct_data['array_blocked']=Array();
			$ct_data['current_hour']=0;
			update_option('cleantalk_data', $ct_data);
		}
		
		//Create all time counter
		if(!isset($ct_data['all_time_counter'])){
			$ct_data['all_time_counter']['accepted']=0;
			$ct_data['all_time_counter']['blocked']=0;
			update_option('cleantalk_data', $ct_data);
		}
		
		//Reset or create user counter
		if(!isset($ct_data['user_counter']) || (isset($_GET['ct_reset_user_counter']) && $_GET['ct_reset_user_counter'] == 1)){
			$ct_data['user_counter']['accepted']=0;
			$ct_data['user_counter']['blocked']=0;
			$ct_data['user_counter']['since']=date('d M');
            update_option('cleantalk_data', $ct_data);
        }
		
		if(!isset($ct_data['sfw_counter'])){
			$ct_data['sfw_counter']['all'] = 0;
			$ct_data['sfw_counter']['blocked'] = 0;
		}
		
		//Compile user's counter string
		$user_counter=Array('accepted'=>$ct_data['user_counter']['accepted'], 'blocked'=>$ct_data['user_counter']['blocked'], 'all'=>$ct_data['user_counter']['accepted'] + $ct_data['user_counter']['blocked'], 'since'=>$ct_data['user_counter']['since']);
		//Previous version $user_counter_str='<span style="color: white;">Since '.$user_counter['since'].': ' .$user_counter['all']*/. '</span> / <span style="color: green;">' .$user_counter['accepted']. '</span> / <span style="color: red;">' .$user_counter['blocked']. '</span>';
		$user_counter_str='<span style="color: white;">' . __('Since', 'cleantalk') . '&nbsp;' . $user_counter['since'].':  </span><span style="color: green;">' .$user_counter['accepted']. '</span> / <span style="color: red;">' .$user_counter['blocked']. '</span>';
		
		$all_time_counter_str='';
		//Don't compile if all time counter disabled
		if(isset($ct_options['all_time_counter']) && $ct_options['all_time_counter']=='1'){
			$all_time_counter=Array('accepted'=>$ct_data['all_time_counter']['accepted'], 'blocked'=>$ct_data['all_time_counter']['blocked'], 'all'=>$ct_data['all_time_counter']['accepted'] + $ct_data['all_time_counter']['blocked']);
			$all_time_counter_str='<span style="color: white;" title="'.__('All / Allowed / Blocked submissions. The number of submissions is being counted since CleanTalk plugin installation.', 'cleantalk').'"><span style="color: white;"> | ' . __('All', 'cleantalk') . ': ' .$all_time_counter['all']. '</span> / <span style="color: green;">' .$all_time_counter['accepted']. '</span> / <span style="color: red;">' .$all_time_counter['blocked']. '</span></span>';
		}
		
		$daily_counter_str='';
		//Don't compile if daily counter disabled
		if(isset($ct_options['daily_counter']) && $ct_options['daily_counter']=='1'){
			$daily_counter=Array('accepted'=>array_sum($ct_data['array_accepted']), 'blocked'=>array_sum($ct_data['array_blocked']), 'all'=>array_sum($ct_data['array_accepted']) + array_sum($ct_data['array_blocked']));
			//Previous version $daily_counter_str='<span style="color: white;" title="'.__('All / Allowed / Blocked submissions. The number of submissions for past 24 hours. ', 'cleantalk').'"><span style="color: white;"> | Day: ' .$daily_counter['all']. '</span> / <span style="color: green;">' .$daily_counter['accepted']. '</span> / <span style="color: red;">' .$daily_counter['blocked']. '</span></span>';
			$daily_counter_str='<span style="color: white;" title="'.__('Allowed / Blocked submissions. The number of submissions for past 24 hours. ', 'cleantalk').'"><span style="color: white;"> | ' . __('Day', 'cleantalk') . ': </span><span style="color: green;">' .$daily_counter['accepted']. '</span> / <span style="color: red;">' .$daily_counter['blocked']. '</span></span>';
		}
		$sfw_counter_str='';
		//Don't compile if SFW counter disabled
		if(isset($ct_options['sfw_counter']) && intval($ct_options['sfw_counter']) == 1 && isset($ct_options['spam_firewall']) && intval($ct_options['spam_firewall']) == 1){
			$sfw_counter=Array('all'=>$ct_data['sfw_counter']['all'], 'blocked'=>$ct_data['sfw_counter']['blocked']);
			$sfw_counter_str='<span style="color: white;" title="'.__('All / Blocked events. Access attempts regitred by SpamFireWall counted since the last plugin activation.', 'cleantalk').'"><span style="color: white;"> | SpamFireWall: ' .$sfw_counter['all']. '</span> / <span style="color: red;">' .$sfw_counter['blocked']. '</span></span>';
		}
		
		$args = array(
			'id'	=> 'ct_parent_node',
			'title' => '<img src="' . plugin_dir_url(__FILE__) . 'images/logo_small1.png" alt=""  height="" style="margin-top:9px; float: left;" /><div style="margin: auto 7px;" class="ab-item alignright"><div class="ab-label" id="ct_stats"><span style="color: white;" title="'.__('Allowed / Blocked submissions. The number of submissions is being counted since ', 'cleantalk').' '.$user_counter['since'].'">'.$user_counter_str.'</span>	'.$daily_counter_str.$all_time_counter_str.$sfw_counter_str.'</div></div>' //You could change widget string here by simply deleting variables
		);
		$wp_admin_bar->add_node( $args );
	
		// add a child item to our parent item
		$args = array(
			'id'	 => 'ct_dashboard_link',
			'title'  => '<a href="https://cleantalk.org/my/?user_token='.@$ct_data['user_token'].'&utm_source=wp-backend&utm_medium=admin-bar&cp_mode=antispam " target="_blank">CleanTalk '.__('dashboard', 'cleantalk').'</a>',
			'parent' => 'ct_parent_node'
		);
		$wp_admin_bar->add_node( $args );
	
		// add another child item to our parent item (not to our first group)
		if(!is_network_admin()){
			$args = array(
				'id'	 => 'ct_settings_link',
				'title'  => '<a href="options-general.php?page=cleantalk">'.__('Settings', 'cleantalk').'</a>',
				'parent' => 'ct_parent_node'
			);
		}else{
			$args = array(
				'id'	 => 'ct_settings_link',
				'title'  => '<a href="settings.php?page=cleantalk">'.__('Settings', 'cleantalk').'</a>',
				'parent' => 'ct_parent_node'
			);
		}
		$wp_admin_bar->add_node( $args );

		// add a child item to our parent item. Support link.
		$args = array(
			'id'	 => 'ct_admin_bar_support_link',
			'title'  => '<hr style="margin-top: 7px;" /><a target="_blank" href="https://wordpress.org/support/plugin/cleantalk-spam-protect">'.__('Support', 'cleantalk').'</a>',
			'parent' => 'ct_parent_node'
		);
		$wp_admin_bar->add_node( $args );
		
		// add a child item to our parent item. Bulk checks.
		if(!is_network_admin()){
			$args = array(
				'id'	 => 'ct_settings_bulk_comments',
				'title'  => '<hr style="margin-top: 7px;" /><a href="edit-comments.php?page=ct_check_spam" title="'.__('Bulk spam comments removal tool.', 'cleantalk').'">'.__('Check comments for spam', 'cleantalk').'</a>',
				'parent' => 'ct_parent_node'
			);
		}
		$wp_admin_bar->add_node( $args );
		
		// add a child item to our parent item. Bulk checks.
		if(!is_network_admin()){
			$args = array(
				'id'	 => 'ct_settings_bulk_users',
				'title'  => '<a href="users.php?page=ct_check_users" title="Bulk spam users removal tool.">'.__('Check users for spam', 'cleantalk').'</a>',
				'parent' => 'ct_parent_node'
			);
		}
		$wp_admin_bar->add_node( $args );
		
        // add a child item to our parent item. Counter reset.
		$args = array(
			'id'	 => 'ct_reset_counter',
			'title'  => '<hr style="margin-top: 7px;"><a href="?ct_reset_user_counter=1" title="Reset your personal counter of submissions.">'.__('Reset counter', 'cleantalk').'</a>',
			'parent' => 'ct_parent_node'
		);
		$wp_admin_bar->add_node( $args );
	}
}


// Prints debug information. Support function.
function ct_section_debug(){
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
		
	if(isset($ct_data['ct_debug_reset']) && $ct_data['ct_debug_reset']){
		unset($ct_data['debug'], $ct_data['ct_debug_reset']);
		update_option('cleantalk_data', $ct_data);
		return;
	}
	

	echo "<h4>Constants:</h4>";
	echo 'CLEANTALK_AJAX_USE_BUFFER '.		 	(defined('CLEANTALK_AJAX_USE_BUFFER') ? 		(CLEANTALK_AJAX_USE_BUFFER ? 		'true' : 'flase') : 'NOT_DEFINED')."<br>";
	echo 'CLEANTALK_AJAX_USE_FOOTER_HEADER '.	(defined('CLEANTALK_AJAX_USE_FOOTER_HEADER') ? 	(CLEANTALK_AJAX_USE_FOOTER_HEADER ? 'true' : 'flase') : 'NOT_DEFINED')."<br>";
	echo 'CLEANTALK_ACCESS_KEY '.				(defined('CLEANTALK_ACCESS_KEY') ? 				(CLEANTALK_ACCESS_KEY ? 			CLEANTALK_ACCESS_KEY : 'flase') : 'NOT_DEFINED')."<br>";
	echo 'CLEANTALK_CHECK_COMMENTS_NUMBER '.	(defined('CLEANTALK_CHECK_COMMENTS_NUMBER') ? 	(CLEANTALK_CHECK_COMMENTS_NUMBER ? 	CLEANTALK_CHECK_COMMENTS_NUMBER : 0) : 'NOT_DEFINED')."<br>";
	echo 'CLEANTALK_CHECK_MESSAGES_NUMBER '.	(defined('CLEANTALK_CHECK_MESSAGES_NUMBER') ? 	(CLEANTALK_CHECK_MESSAGES_NUMBER ? 	CLEANTALK_CHECK_MESSAGES_NUMBER : 0) : 'NOT_DEFINED')."<br>";
	echo 'CLEANTALK_PLUGIN_DIR '.				(defined('CLEANTALK_PLUGIN_DIR') ? 				(CLEANTALK_PLUGIN_DIR ? 			CLEANTALK_PLUGIN_DIR : 'flase') : 'NOT_DEFINED')."<br>";
	echo 'WP_ALLOW_MULTISITE '.					(defined('WP_ALLOW_MULTISITE') ? 				(WP_ALLOW_MULTISITE ?				'true' : 'flase') : 'NOT_DEFINED');
	
	echo "<h4>Debug log: <input type='submit' value='Drop debug data' name='ct_debug_reset' style='font-size: 11px; padding: 1px;'></h4>";
	echo "<div style='height: 500px; width: 80%; overflow: auto;'>";
		
		$output = print_r($ct_data['debug'], true);
		$output = str_replace("\n", "<br>", $output);
		$output = preg_replace("/[^\S]{4}/", "&nbsp;&nbsp;&nbsp;&nbsp;", $output);
		echo "$output";
		
	echo "</div>";
}

/**
 * Admin callback function - Displays description of 'state' plugin parameters section
 */
function ct_section_settings_state() {
	global $ct_options, $ct_data;
		
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
		
	if(!isset($ct_data['moderate_ip']))
	{
		$ct_data['moderate_ip'] = 0;
	}

	$path_to_img = plugin_dir_url(__FILE__) . "images/";
	
	$img = $path_to_img."yes.png";
	$img_no = $path_to_img."no.png";
	$color="black";
	$test_failed=false;

	if(trim($ct_options['apikey'])=='')
	{
		$img = $path_to_img."yes_gray.png";
		$img_no = $path_to_img."no_gray.png";
		$color="gray";
	}
	if(isset($ct_data['testing_failed'])&&$ct_data['testing_failed']==1)
	{
		$img=$path_to_img."no.png";
		$img_no=$path_to_img."no.png";
		$color="black";
		$test_failed=true;
	}
	if($ct_data['moderate_ip'] == 1)
	{
		$img = $path_to_img."yes.png";
		$img_no = $path_to_img."no.png";
		$color="black";
		$test_failed=false;
	}
	print "<div style='color:$color'>";
	
		if(isset($ct_data['moderate_ip']) && $ct_data['moderate_ip'] == 1)
			$ct_moderate = true;
		else
			$ct_moderate = false;
		
		print '<img src="'.		   (($ct_options['registrations_test']==1 || $ct_moderate) 			? $img : $img_no).'" alt=""  height="" /> '.__('Registration forms', 'cleantalk');
		print ' &nbsp; <img src="'.(($ct_options['comments_test']==1 || 	 $ct_moderate) 			? $img : $img_no).'" alt=""  height="" /> '.__('Comments forms', 'cleantalk');
		print ' &nbsp; <img src="'.(($ct_options['contact_forms_test']==1 || $ct_moderate) 			? $img : $img_no).'" alt=""  height="" /> '.__('Contact forms', 'cleantalk');
		print ' &nbsp; <img src="'.(($ct_options['general_contact_forms_test']==1 || $ct_moderate) 	? $img : $img_no).'" alt=""  height="" /> '.__('Custom contact forms', 'cleantalk');
		print ' &nbsp; <img src="'.(($ct_options['spam_firewall']==1 || 	$ct_moderate) 			? $img : $img_no).'" alt=""  height="" /> '.__('SpamFireWall', 'cleantalk');

		if(class_exists('WooCommerce'))
			print ' &nbsp; <img src="'.(($ct_options['wc_checkout_test']==1 || $ct_moderate) ? $img : $img_no).'" alt=""  height="" /> '.__('WooCommerce checkout form', 'cleantalk');
		
		if($ct_data['moderate_ip'] == 1)
			print "<br /><br />The anti-spam service is paid by your hosting provider. License #".$ct_data['ip_license'].".<br />";
	
	print "</div>";
	
	return true;
}

/**
 * Admin callback function - Displays description of 'autodel' plugin parameters section
 */
function ct_section_settings_autodel() {
	return true;
}

/**
 * Admin callback function - Displays inputs of 'apikey' plugin parameter
 */
function ct_input_apikey() {
	global $ct_options, $ct_data, $ct_notice_online_label;
	$ct_options=ct_get_options();
	$ct_data=ct_get_data();
	
	if(!isset($ct_data['admin_blocked']))
	{
		$blocked=0;
	}
	else
	{
		$blocked=$ct_data['admin_blocked'];
	}
    
    echo "<style>a.ct_support_link{color: #666; margin-right: 0.5em; font-size: 10pt; font-weight: normal;}</style>";
	
	if($blocked>0)
	{
		$blocked = number_format($blocked, 0, ',', ' ');
	
		echo "<script>var cleantalk_blocked_message=\"<div style='height:24px;width:100%;display:table-cell; vertical-align:middle;'><span>CleanTalk ";
		printf(
			/* translators: %s: Number of spam messages */
			__( 'has blocked <b>%s</b>  spam.', 'cleantalk' ),
			$blocked
		);
		print "</span></div><br />\";\n";
	}
	else
	{
		echo "<script>var cleantalk_blocked_message=\"\";\n";
	}
		echo "var cleantalk_statistics_link=\"<a class='cleantalk_manual_link' target='__blank' href='https://cleantalk.org/my?user_token=".@$ct_data['user_token']."&cp_mode=antispam'>".__('Click here to get anti-spam statistics', 'cleantalk')."</a>\";";
		echo "var cleantalk_support_link=\"<a class='cleantalk_auto_link' target='__blank' href='https://wordpress.org/support/plugin/cleantalk-spam-protect'>"
			.__('Support', 'cleantalk')."</a>\";
	</script>";
	
	$value = $ct_options['apikey'];
	$def_value = ''; 
	$is_wpmu=false;
	if(!defined('CLEANTALK_ACCESS_KEY'))
	{
		echo "<input id='cleantalk_apikey' name='cleantalk_settings[apikey]' size='20' type='text' value='$value' style=\"font-size: 14pt;\" placeholder='" . __('Enter the key', 'cleantalk') . "' />";
		echo "<script>var cleantalk_wpmu=false;</script>";
	}
	else
	{
		echo "<script>var cleantalk_wpmu=true;</script>";
		$is_wpmu=true;
	}
	
	//echo "<script src='".plugins_url( 'cleantalk-admin.js', __FILE__ )."?ver=".$cleantalk_plugin_version."'></script>\n";
	if (ct_valid_key($value) === false && !$is_wpmu) {
		echo "<script>var cleantalk_good_key=false;</script>";
		if (function_exists('curl_init') && function_exists('json_decode')) {
			echo '<br /><br />';
			echo "<a target='__blank' style='' href='https://cleantalk.org/register?platform=wordpress&email=".urlencode(ct_get_admin_email())."&website=".urlencode(parse_url(get_option('siteurl'),PHP_URL_HOST))."'><input type='button' class='cleantalk_auto_link' value='".__('Get access key manually', 'cleantalk')."' /></a>";
            echo "&nbsp;" .  __("or") . "&nbsp;";
			echo '<input name="get_apikey_auto" type="submit" class="cleantalk_manual_link" value="' . __('Get access key automatically', 'cleantalk') . '" />';
			echo '<input id="ct_admin_timezone" name="ct_admin_timezone" type="hidden" value="null" />';
            echo "<br />";
            echo "<br />";
			
			admin_addDescriptionsFields(sprintf(__('Admin e-mail (%s) will be used for registration', 'cleantalk'), ct_get_admin_email()));
			admin_addDescriptionsFields(sprintf('<a target="__blank" style="color:#BBB;" href="https://cleantalk.org/publicoffer">%s</a>', __('License agreement', 'cleantalk')));
		}
	} else {
        $cleantalk_support_links = "<br /><div>";
        $cleantalk_support_links .= "<style>a.ct_support_link{color: #666; margin-right: 0.5em; font-size: 10pt; font-weight: normal;}</style>";
        $cleantalk_support_links .= "<a href='#' id='cleantalk_access_key_link' class='ct_support_link'>" . __("Show the access key", 'cleantalk') . "</a>";
        $cleantalk_support_links .= "&nbsp;&nbsp;";
        $cleantalk_support_links .= "&nbsp;&nbsp;";
        $cleantalk_support_links .= "<a href='edit-comments.php?page=ct_check_spam' class='ct_support_link'>" . __("Check comments for spam", 'cleantalk') . "</a>";
        $cleantalk_support_links .= "<a href='users.php?page=ct_check_users' class='ct_support_link'>" . __("Check users for spam", 'cleantalk') . "</a>";
        $cleantalk_support_links .= "</div>";
		echo "<script type=\"text/javascript\">var cleantalk_good_key=true; var cleantalk_support_links = \"$cleantalk_support_links\";</script>";
	}
	
	$test_failed = (!empty($ct_data['testing_failed']) 	? true : false);
	$modearte_ip = (!empty($ct_data['moderate_ip'])		? true : false);
	
	//Testing failed output
	if($test_failed && !$modearte_ip){
		echo "<script type=\"text/javascript\">var cleantalk_testing_failed = true;</script>";
		echo "<br>";
		echo "<div class='ct-warning-test-failed'>";
			printf(__("Testing is failed, please, check the settings! Tech support %ssupport@cleantalk.org%s", 'cleantalk'), "<a target=_blank href='mailto:support@cleantalk.org'>", "</a>");
		echo "</div>";
	}else{
		echo "<script type=\"text/javascript\">var cleantalk_testing_failed = false;</script>";
	}
}

/**
 * Admin callback function - Displays inputs of 'comments_test' plugin parameter
 */
function ct_input_comments_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['comments_test'];
	echo "<input type='radio' id='cleantalk_comments_test1' name='cleantalk_settings[comments_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_comments_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_comments_test0' name='cleantalk_settings[comments_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_comments_test0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('WordPress, JetPack, WooCommerce.', 'cleantalk'));
}

//Titles for advanced settings.
function ct_input_what_fields_should_be_protected(){
	echo "<h3>".__('Forms to protect', 'cleantalk')."</h3>";
}

function ct_input_comments_and_messages(){
	echo "<h3>".__('Comments and messages', 'cleantalk')."</h3>";
}

function ct_input_data_processing(){
	echo "<h3>".__('Data processing', 'cleantalk')."</h3>";
}

function ct_input_administrator_panel(){
	echo "<h3>".__('Admin bar', 'cleantalk')."</h3>";
}

/**
 * Admin callback function - Displays inputs of 'comments_test' plugin parameter
 */
function ct_input_remove_links_from_approved_comments() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['remove_comments_links'];
	echo "<input type='radio' id='cleantalk_remove_links_from_comments1' name='cleantalk_settings[remove_comments_links]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_remove_links_from_comments1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_remove_links_from_comments0' name='cleantalk_settings[remove_comments_links]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_remove_links_from_comments0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Remove links from approved comments. Replace it with "[Link deleted]"', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'comments_test' plugin parameter
 */
function ct_input_show_check_links() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['show_check_links'];
	echo "<input type='radio' id='cleantalk_show_check_links1' name='cleantalk_settings[show_check_links]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_show_check_links1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_show_check_links1' name='cleantalk_settings[show_check_links]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_show_check_links1'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Shows little icon near IP addresses and Emails allowing you to check it via CleanTalk\'s database. Also allowing you to manage comments from the public post\'s page.', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'comments_test' plugin parameter
 */
function ct_input_registrations_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['registrations_test'];
	echo "<input type='radio' id='cleantalk_registrations_test1' name='cleantalk_settings[registrations_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_registrations_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_registrations_test0' name='cleantalk_settings[registrations_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_registrations_test0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('WordPress, BuddyPress, bbPress, S2Member, WooCommerce.', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'contact_forms_test' plugin parameter
 */
function ct_input_contact_forms_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['contact_forms_test'];
	echo "<input type='radio' id='cleantalk_contact_forms_test1' name='cleantalk_settings[contact_forms_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_contact_forms_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_contact_forms_test0' name='cleantalk_settings[contact_forms_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_contact_forms_test0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Contact Form 7, Formidable forms, JetPack, Fast Secure Contact Form, WordPress Landing Pages, Gravity Forms.', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'general_contact_forms_test' plugin parameter
 */
function ct_input_general_contact_forms_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['general_contact_forms_test'];
	echo "<input type='radio' id='cleantalk_general_contact_forms_test1' name='cleantalk_settings[general_contact_forms_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_general_contact_forms_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_general_contact_forms_test0' name='cleantalk_settings[general_contact_forms_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_general_contact_forms_test0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Anti spam test for any WordPress themes or contacts forms.', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'wc_checkout_test' plugin parameter
 */
function ct_input_wc_chekout_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['wc_checkout_test'];
	echo "<input type='radio' id='cleantalk_wc_checkout_test1' name='cleantalk_settings[wc_checkout_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_wc_checkout_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_wc_checkout_test0' name='cleantalk_settings[wc_checkout_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_wc_checkout_test0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Anti spam test for WooCommerce checkout form.', 'cleantalk'));
}

/**
 * Admin callback function - Displays inputs of 'bp_private_messages' plugin parameter
 */
function ct_input_bp_private_messages() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	
	$value = $ct_options['bp_private_messages'];
	echo "<input type='radio' id='bp_private_messages1' name='cleantalk_settings[bp_private_messages]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='bp_private_messages1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='bp_private_messages0' name='cleantalk_settings[bp_private_messages]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='bp_private_messages0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(__('Check buddyPress private messages.', 'cleantalk'));
}

/**
 * @author Artem Leontiev
 * Admin callback function - Displays inputs of 'Publicate relevant comments' plugin parameter
 *
 * @return null
 */
function ct_input_remove_old_spam() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	$value = $ct_options['remove_old_spam'];
	echo "<input type='radio' id='cleantalk_remove_old_spam1' name='cleantalk_settings[remove_old_spam]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_remove_old_spam1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_remove_old_spam0' name='cleantalk_settings[remove_old_spam]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_remove_old_spam0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(sprintf(__('Delete spam comments older than %d days.', 'cleantalk'),  $ct_options['spam_store_days']));
}

/**
 * Admin callback function - Displays inputs of 'Show statistics in adminbar' plugin parameter
 *
 * @return null
 */
function ct_input_show_adminbar() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['show_adminbar']))
	{
		$value = @intval($ct_options['show_adminbar']);
	}
	else
	{
		$value=1;
	}
	echo "<input type='radio' id='cleantalk_show_adminbar1' name='cleantalk_settings[show_adminbar]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_show_adminbar1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_show_adminbar0' name='cleantalk_settings[show_adminbar]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_show_adminbar0'> " . __('No') . "</label>";
	admin_addDescriptionsFields(sprintf(__('Show/hide CleanTalk icon in top level menu in WordPress backend. The number of submissions is being counted for past 24 hours.', 'cleantalk'),  $ct_options['show_adminbar']));
}

/**
 * Admin callback function - Displays inputs of 'Show statistics in adminbar' plugin parameter
 *
 * @return null
 */
function ct_input_general_postdata_test() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['general_postdata_test']))
	{
		$value = @intval($ct_options['general_postdata_test']);
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_general_postdata_test1' name='cleantalk_settings[general_postdata_test]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_general_postdata_test1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_general_postdata_test0' name='cleantalk_settings[general_postdata_test]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_general_postdata_test0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Check all POST submissions from website visitors. Enable this option if you have spam misses on website or you don`t have records about missed spam here:', 'cleantalk') . '&nbsp;' . '<a href="https://cleantalk.org/my/?user_token='.@$ct_data['user_token'].'&utm_source=wp-backend&utm_medium=admin-bar&cp_mode=antispam" target="_blank">' . __('CleanTalk dashboard', 'cleantalk') . '</a>.<br />' . __('СAUTION! Option can catch POST requests in WordPress backend', 'cleantalk'),  $ct_options['general_postdata_test']));
}

function ct_input_use_ajax() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['use_ajax']))
	{
		$value = @intval($ct_options['use_ajax']);
	}
	else
	{
		$value=1;
	}
	echo "<input type='radio' id='cleantalk_use_ajax1' name='cleantalk_settings[use_ajax]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_use_ajax1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_use_ajax0' name='cleantalk_settings[use_ajax]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_use_ajax0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Options helps protect WordPress against spam with any caching plugins. Turn this option on to avoid issues with caching plugins.', 'cleantalk')."<strong> ".__('Attention! Incompatible with AMP plugins!', 'cleantalk')."</strong>",  $ct_options['use_ajax']));
}

function ct_input_check_comments_number() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['check_comments_number']))
	{
		$value = @intval($ct_options['check_comments_number']);
	}
	else
	{
		$value=1;
	}
	
	if(defined('CLEANTALK_CHECK_COMMENTS_NUMBER'))
	{
		$comments_check_number = CLEANTALK_CHECK_COMMENTS_NUMBER;
	}
	else
	{
		$comments_check_number = 3;
	}
	
	echo "<input type='radio' id='cleantalk_check_comments_number1' name='cleantalk_settings[check_comments_number]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_check_comments_number1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_check_comments_number0' name='cleantalk_settings[check_comments_number]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_check_comments_number0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__("Dont't check comments for users with above", 'cleantalk') . $comments_check_number . __("comments.", 'cleantalk'),  $ct_options['check_comments_number']));
}

function ct_input_collect_details() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['collect_details']))
	{
		$value = @intval($ct_options['collect_details']);
	}
	else
	{
		$value=0;
	}
	
	echo "<div id='cleantalk_anchor2' style='display:none'></div><input type=hidden name='cleantalk_settings[collect_details]' value='0' />";
	echo "<input type='checkbox' id='collect_details1' name='cleantalk_settings[collect_details]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='collect_details1'> " . __('Collect details about browsers', 'cleantalk') . "</label>";
	@admin_addDescriptionsFields(sprintf(__("Checking this box you allow plugin store information about screen size and browser plugins of website visitors. The option in a beta state.", 'cleantalk'),  $ct_options['spam_firewall']));
	echo "<script>
		jQuery(document).ready(function(){
			jQuery('#cleantalk_anchor2').parent().parent().children().first().hide();
			jQuery('#cleantalk_anchor2').parent().css('padding-left','0px');
			jQuery('#cleantalk_anchor2').parent().attr('colspan', '2');
		});
	</script>";
}

function ct_input_check_messages_number() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['check_messages_number']))
	{
		$value = @intval($ct_options['check_messages_number']);
	}
	else
	{
		$value=0;
	}
	
	if(defined('CLEANTALK_CHECK_MESSAGES_NUMBER'))
	{
		$messages_check_number = CLEANTALK_CHECK_MESSAGES_NUMBER;
	}
	else
	{
		$messages_check_number = 3;
	}
	
	echo "<input type='radio' id='cleantalk_check_messages_number1' name='cleantalk_settings[check_messages_number]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_check_messages_number1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_check_messages_number0' name='cleantalk_settings[check_messages_number]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_check_messages_number0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__("Dont't check messages for users with above $messages_check_number messages", 'cleantalk'),  $ct_options['check_messages_number']));
}

function ct_input_check_external() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['check_external']))
	{
		$value = @intval($ct_options['check_external']);
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_check_external1' name='cleantalk_settings[check_external]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_check_external1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_check_external0' name='cleantalk_settings[check_external]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_check_external0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Turn this option on to protect forms on your WordPress that send data to third-part servers (like MailChimp).', 'cleantalk'),  $ct_options['check_external']));
}

function ct_input_check_internal() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['check_internal']))
	{
		$value = @intval($ct_options['check_internal']);
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_check_internal1' name='cleantalk_settings[check_internal]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_check_internal1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_check_internal0' name='cleantalk_settings[check_internal]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_check_internal0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('This option will enable protection for custom (hand-made) AJAX forms with PHP scripts handlers on your WordPress.', 'cleantalk'),  $ct_options['check_internal']));
}

function ct_input_set_cookies() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['set_cookies']))
	{
		$value = @intval($ct_options['set_cookies']);
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_set_cookies1' name='cleantalk_settings[set_cookies]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_set_cookies1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_set_cookies0' name='cleantalk_settings[set_cookies]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_set_cookies0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Turn this option off to deny plugin generates any cookies on website front-end. This option is helpful if you use Varnish. But most of contact forms will not be protected by CleanTalk if the option is turned off!', 'cleantalk')));
}

function ct_input_ssl_on() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['ssl_on']))
	{
		$value = @intval($ct_options['ssl_on']);
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_ssl_on1' name='cleantalk_settings[ssl_on]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_ssl_on1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_ssl_on0' name='cleantalk_settings[ssl_on]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_ssl_on0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Turn this option on to use encrypted (SSL) connection with CleanTalk servers.', 'cleantalk')));
}

function ct_input_protect_logged_in() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['protect_logged_in']))
	{
		$value = @intval($ct_options['protect_logged_in']);
		$value = $value == 1 ? $value : 0;
	}
	else
	{
		$value=0;
	}
	echo "<input type='radio' id='cleantalk_protect_logged_in1' name='cleantalk_settings[protect_logged_in]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_protect_logged_in1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_protect_logged_in0' name='cleantalk_settings[protect_logged_in]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_protect_logged_in0'> " . __('No') . "</label>";
	@admin_addDescriptionsFields(sprintf(__('Turn this option on to check for spam any submissions (comments, contact forms and etc.) from registered Users.', 'cleantalk')));

    return null;
}

function ct_input_show_link() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['show_link']))
	{
		$value = @intval($ct_options['show_link']);
	}
	else
	{
		$value=0;
	}
	
   /* echo "<input type='radio' id='cleantalk_show_link1' name='cleantalk_settings[show_link]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_show_link1'> " . __('Yes') . "</label>";
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo "<input type='radio' id='cleantalk_show_link0' name='cleantalk_settings[show_link]' value='0' " . ($value == '0' ? 'checked' : '') . " /><label for='cleantalk_show_link0'> " . __('No') . "</label>";*/
	
	echo "<div id='cleantalk_anchor' style='display:none'></div><input type=hidden name='cleantalk_settings[show_link]' value='0' />";
	echo "<input type='checkbox' id='cleantalk_show_link1' name='cleantalk_settings[show_link]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_show_link1'> " . __('Tell others about CleanTalk', 'cleantalk') . "</label>";
	@admin_addDescriptionsFields(sprintf(__("Checking this box places a small link under the comment form that lets others know what anti-spam tool protects your site.", 'cleantalk'),  $ct_options['show_link']));
	echo "<script>
		jQuery(document).ready(function(){
			jQuery('#cleantalk_anchor').parent().parent().children().first().hide();
			jQuery('#cleantalk_anchor').parent().css('padding-left','0px');
		});
	</script>";
}

function ct_input_spam_firewall() {
	global $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();

	if(isset($ct_options['spam_firewall']))
	{
		$value = @intval($ct_options['spam_firewall']);
	}
	else
	{
		$value=0;
	}
	
	echo "<div id='cleantalk_anchor1' style='display:none'></div><input type=hidden name='cleantalk_settings[spam_firewall]' value='0' />";
	echo "<input type='checkbox' id='cleantalk_spam_firewall1' name='cleantalk_settings[spam_firewall]' value='1' " . ($value == '1' ? 'checked' : '') . " /><label for='cleantalk_spam_firewall1'> " . __('SpamFireWall') . "</label>";
	@admin_addDescriptionsFields(sprintf(__("This option allows to filter spam bots before they access website. Also reduces CPU usage on hosting server and accelerates pages load time.", 'cleantalk'),  $ct_options['spam_firewall']) .
        " " .
        '<a href="https://cleantalk.org/cleantalk-spam-firewall" style="font-size: 10pt; color: #666 !important" target="_blank">' . __('Learn more', 'cleantalk') . '</a>.'
    );
	echo "<script>
		jQuery(document).ready(function(){
			jQuery('#cleantalk_anchor1').parent().parent().children().first().hide();
			jQuery('#cleantalk_anchor1').parent().css('padding-left','0px');
			jQuery('#cleantalk_anchor1').parent().attr('colspan', '2');
		});
	</script>";
}


/**
 * Admin callback function - Plugin parameters validator
 */
function ct_settings_validate($input) {
	return $input;
}


/**
 * Admin callback function - Displays plugin options page
 */
function ct_settings_page() {
	?>
<style type="text/css">
 .cleantalk_manual_link {padding: 10px; background: #3399FF; color: #fff; border:0 none;
	cursor:pointer;
	-webkit-border-radius: 5px;
	border-radius: 5px; 
	font-size: 12pt;
}
.cleantalk_auto_link{
	background: #ccc;
	border-color: #999;
	-webkit-box-shadow: inset 0 1px 0 rgba(200,200,200,.5),0 1px 0 rgba(0,0,0,.15);
	box-shadow: inset 0 1px 0 rgba(200,200,200,.5),0 1px 0 rgba(0,0,0,.15);
	color: #000;
	text-decoration: none;
	display: inline-block;
	text-decoration: none;
	font-size: 13px;
	line-height: 26px;
	height: 28px;
	margin: 0;
	padding: 0 10px 1px;
	cursor: pointer;
	border-width: 1px;
	border-style: solid;
	-webkit-appearance: none;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	white-space: nowrap;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.cleantalk_auto_link:hover{
	color: #fff;
}
.cleantalk_manual_link 
{
	background: #2ea2cc;
	border-color: #0074a2;
	-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
	box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
	color: #fff;
	text-decoration: none;
		display: inline-block;
	text-decoration: none;
	font-size: 13px;
	line-height: 26px;
	height: 28px;
	margin: 0;
	padding: 0 10px 1px;
	cursor: pointer;
	border-width: 1px;
	border-style: solid;
	-webkit-appearance: none;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	white-space: nowrap;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.cleantalk_manual_link:hover
{
	color:black;
}

</style>

	<div>
		<?php				
		if(is_network_admin())
		{	
			print '<form method="post">';
			if(defined('CLEANTALK_ACCESS_KEY'))
			{
				print "<br />Your CleanTalk access key is: <b>".CLEANTALK_ACCESS_KEY."</b><br />
						You can change it in your wp-config.php file.<br />";
			}
			else
			{
				print "<br />To set up global CleanTalk access key for all websites, define constant in your wp-config.php file before defining database constants:<br />
						<pre>define('CLEANTALK_ACCESS_KEY', 'place your key here');</pre>";
			}
		}
		else
		{
			?>
			<form action="options.php" method="post">
			<?php settings_fields('cleantalk_settings'); ?>
			<?php do_settings_sections('cleantalk'); ?>
			<br>
			<input name="Submit" type="submit" class='cleantalk_manual_link' value="<?php esc_attr_e('Save Changes'); ?>" />
			<?php
		}
		?>
		
		</form>
	</div>
	<?php
	
	$ct_data = get_option('cleantalk_data');
	
	if (ct_valid_key() === false){
		
		$page = get_current_screen();
		$trial_time = get_option('cleantalk_sends_reports_till');
		$trial_days_left = ($trial_time ? ceil(($trial_time - time()) / 86400) : false);
		
		// Trial days
		/* INACTIVE
		if ((is_network_admin() || is_admin()) && $trial_days_left && $page->id == 'settings_page_cleantalk' && $ct_data['moderate_ip'] == 0){
			$trial_days_left = 7; //Magic number!
			echo ($trial_days_left == 1 ? "<span style='color:red;'>" : "");
			echo '<br>' . sprintf(__("You have <b>%d</b> days free trial to test the anti-spam protection.", 'cleantalk'), $trial_days_left) . '';
			echo ($trial_days_left == 1 ? "</span>" : "");
		}
		//*/
		
	}else{
		
		$user_token = (!empty($ct_data['user_token']) ? $ct_data['user_token'] : false);
				
		// Translate banner for non EN locale
		if(substr(get_locale(), 0, 2) != 'en'){
			require_once(CLEANTALK_PLUGIN_DIR.'templates/translate_banner.php');
			printf($ct_translate_banner_template, substr(get_locale(), 0, 2));
		}else
			echo "<br /><br /><br />";
		
		echo "<div>";
		
			echo __("CleanTalk's tech support:", 'cleantalk')
				."&nbsp;"
				.'<a target="_blank" href="https://wordpress.org/support/plugin/cleantalk-spam-protect">Wordpress.org</a>.'
			// .' <a href="https://community.cleantalk.org/viewforum.php?f=25" target="_blank">'.__("Tech forum", 'cleantalk').'</a>'
			// .($user_token ? ", <a href='https://cleantalk.org/my/support?user_token=$user_token&cp_mode=antispam' target='_blank'>".__("Service support ", 'cleantalk').'</a>' : '').
				.'<br>';
			echo __('Plugin Homepage at', 'cleantalk').' <a href="http://cleantalk.org" target="_blank">cleantalk.org</a>.<br />';
			echo __('Use s@cleantalk.org to test plugin in any WordPress form.', 'cleantalk').'<br>';
			echo __('CleanTalk is registered Trademark. All rights reserved.', 'cleantalk'); 
			
		echo "</div>";
		
	}
}

/**
 * Notice blog owner if plugin is used without Access key 
 * @return bool 
 */
function cleantalk_admin_notice_message(){
	global $show_ct_notice_trial, $show_ct_notice_renew, $show_ct_notice_online, $show_ct_notice_autokey, $ct_notice_autokey_value, $ct_plugin_name, $ct_options, $ct_data;
	
	$ct_options = ct_get_options();
	$ct_data = ct_get_data();
	$page = get_current_screen();
	$activation_timestapm = get_option('cleantalk_activation_timestamp', false);
	
	
	//General notice control flags
	$moderate_ip =		(empty($ct_data['moderate_ip']) ? 0 : $ct_data['moderate_ip']);
	$self_owned_key = 	(!$moderate_ip && !defined('CLEANTALK_ACCESS_KEY') ? true : false);
	$is_admin = 		(is_network_admin() || is_admin() ? true : false);
	$value = 1; //(current_user_can('activate_plugins') ? 1 : 0); 				//inactive
	$show_notice = true;
	
	//Notice control flags
	$show_ct_notice_trial = 		(isset($ct_data['show_ct_notice_trial']) 				? intval($ct_data['show_ct_notice_trial']) 			: 0);
	$show_ct_notice_renew = 		(isset($ct_data['show_ct_notice_renew']) 				? intval($ct_data['show_ct_notice_renew']) 			: 0);
	$show_ct_notice_review = 		(isset($ct_data['show_ct_notice_review'])				? intval($ct_data['show_ct_notice_review'])			: 0);
	$next_notice_show = 			(isset($ct_data['next_notice_show']) 	 				? intval($ct_data['next_notice_show']) 				: 0);	//inactive
	$notice_from_report = 			(isset($ct_data['ct_show_notice_from_report'])			? intval($ct_data['ct_show_notice_from_report'])	: 0);
	$page_is_ct_settings = 			($page->id == 'settings_page_cleantalk' || $page->id == 'settings_page_cleantalk-network' ? true 	: false);
	$three_days_since_activation =	(intval($activation_timestapm)+(3*24*60*60) < time() 	? true 												: false);
		
	//Misc
	$user_token =    (isset($ct_data['user_token']) && $ct_data['user_token'] != '' ? "&user_token={$ct_data['user_token']}" : "");
	$settings_link = (is_network_admin() ? "settings.php?page=cleantalk" : "options-general.php?page=cleantalk");
		
	if($self_owned_key && $is_admin && $value == 1){
		
		//Notice when users came from the daily report letter
		if ($show_notice &&	$notice_from_report && !ct_valid_key()){

			if($page_is_ct_settings)
				echo '<div class="error">
					<h3>' .__("To see the anti-spam log and complete the setup, please get the Access key.", 'cleantalk'). '</h3>
				</div>';
			$show_notice = false;			
		}
			
		//Unable to get key automatically (if ct_admin_init().getAutoKey() returns error)
		if ($show_notice && $show_ct_notice_autokey){
			echo '<div class="error">
				<h3>' . sprintf(__("Unable to get Access key automatically: %s", 'cleantalk'), $ct_notice_autokey_value).
					"<a target='__blank' style='margin-left: 10px' href='https://cleantalk.org/register?platform=wordpress&email=" . urlencode(ct_get_admin_email())."&website=" . urlencode(parse_url(get_option('siteurl'),PHP_URL_HOST))."'>".__('Get the Access key', 'cleantalk').'</a>
				</h3>
			</div>';
		}
		
		//key == "" || "enter key"
		if ($show_notice && !ct_valid_key()){
			
			// if($page_is_ct_settings || $three_days_since_activation) //INACTIVE
				echo "<div class='error'>
					<h3>"
						.sprintf(__("Please enter Access Key in %s settings to enable anti spam protection!", 'cleantalk'), "<a href='{$settings_link}'>CleanTalk plugin</a>")
					."</h3>
				</div>";
			$show_notice = false;
		}
		
		//"Trial period ends" notice from ct_admin_init().noticePaidTill()
		if ($show_notice && $show_ct_notice_trial == 1 && empty($_COOKIE['ct_trial_banner_closed'])) {
			echo '<div class="error notice is-dismissible ct-trial-notice">
				<h3>' . sprintf(__("%s trial period ends, please upgrade to %s!", 'cleantalk'), 
					"<a href='{$settings_link}'>$ct_plugin_name</a>", 
					"<a href=\"http://cleantalk.org/my/bill/recharge?utm_source=wp-backend&utm_medium=cpc&utm_campaign=WP%20backend%20trial$user_token&cp_mode=antispam\" target=\"_blank\"><b>premium version</b></a>") .
				'</h3>
			</div>';
			$show_notice = false;
		}
		
		//Renew notice from ct_admin_init().noticePaidTill()
		if ($show_notice && $show_ct_notice_renew == 1) {
			$renew_link = "<a href=\"http://cleantalk.org/my/bill/recharge?utm_source=wp-backend&utm_medium=cpc&utm_campaign=WP%%20backend%%20renew$user_token&cp_mode=antispam\" target=\"_blank\">%s</a>";
			$button_html 	= sprintf($renew_link, '<input type="button" class="button button-primary" value="'.__('RENEW ANTI-SPAM', 'cleantalk').'"  />');
			$link_html 		= sprintf($renew_link, "<b>".__('next year', 'cleantalk')."</b>");
			
			echo '<div class="updated">
				<h3>'. 
					sprintf(__("Please renew your anti-spam license for %s.", 'cleantalk'), $link_html). '<br /><br />' . $button_html . 
				'</h3>
			</div>';
			$show_notice = false;
		}
		
		//"Wrong access key" notice (if ct_update_option().METHOD_notice_validate_key returns a error)
		if ($show_notice && $show_ct_notice_online === 'N'){
			echo '<div class="error">
				<h3><b>'.
					__("Wrong <a href='{$settings_link}'><b style=\"color: #49C73B;\">Clean</b><b style=\"color: #349ebf;\">Talk</b> access key</a>! Please check it or ask <a target=\"_blank\" href=\"https://cleantalk.org/forum/\">support</a>.", 'cleantalk').
				'</b></h3>
			</div>';
		}
	}

	return true;
}

/**
 * @author Artem Leontiev
 *
 * Add descriptions for field
 */
function admin_addDescriptionsFields($descr = '') {
	echo "<div style='font-size: 10pt; color: #666 !important'>$descr</div>";
}

/**
* Test API key 
*/
function ct_valid_key($apikey = null) {
	global $ct_options, $ct_data;
	
	if ($apikey === null) {
	    $ct_options = ct_get_options();
		$apikey = $ct_options['apikey'];
	}
	
    return ($apikey === 'enter key' || $apikey === '') ? false : true;
}

// Ajax action feedback form comments page.
function ct_comment_send_feedback($comment_id = null, $comment_status = null, $change_status = false, $direct_call = null){
	
	// For AJAX call
	check_ajax_referer('ct_secret_nonce', 'security');
	$comment_id     = !empty($_POST['comment_id'])     ? $_POST['comment_id']     : false;
	$comment_status = !empty($_POST['comment_status']) ? $_POST['comment_status'] : false;
	$change_status  = !empty($_POST['change_status'])  ? $_POST['change_status']  : false;
	
	// If enter params is empty exit
	if(!$comment_id || !$comment_status)
		die();
	
	// $comment = get_comment($comment_id, 'ARRAY_A');
	$hash = get_comment_meta($comment_id, 'ct_hash', true);
	
	// If we can send the feedback
	if($hash){
		
		// Approving
		if($comment_status == '1' || $comment_status == 'approve'){
			$result = ct_send_feedback($hash.":1");
			// $comment['comment_content'] = ct_unmark_red($comment['comment_content']);
			// wp_update_comment($comment);
			$result === true ? 1 : 0;
		}
		
		// Disapproving	
		if($comment_status == 'spam'){
			$result = ct_send_feedback($hash.":0");
			$result === true ? 1 : 0;
		}
	}else{
		$result = 'no_hash';
	}
	
	// Changing comment status(folder) if flag is set. spam || approve
	if($change_status !== false)
		wp_set_comment_status($comment_id, $comment_status);
		
	if(!$direct_call){
		echo !empty($result) ? $result : 0;
		die();
	}else{
		
	}
}

// Ajax action feedback form user page.
function ct_user_send_feedback($user_id = null, $status = null, $direct_call = null){
	
	check_ajax_referer('ct_secret_nonce', 'security');
	
	if(!$direct_call){
		$user_id = $_POST['user_id'];
		$status  = $_POST['status'];
	}
		
	$hash = get_user_meta($user_id, 'ct_hash', true);
	
	if($hash){
		if($status == 'approve' || $status == 1){
			$result = ct_send_feedback($hash.":1");
			$result === true ? 1 : 0;
		}
		if($status == 'spam' || $status == 'disapprove' || $status == 0){
			$result = ct_send_feedback($hash.":0");
			$result === true ? 1 : 0;
		}
	}else{
		$result = 'no_hash';
	}
	
	if(!$direct_call){
		echo !empty($result) ? $result : 0;
		die();
	}else{
		
	}
	
}

/**
 * Admin filter 'get_comment_text' - Adds some info to comment text to display
 * @param 	string $current_text Current comment text
 * @return	string New comment text
 */
function ct_get_comment_text($current_text) {
	global $comment;
	$new_text = $current_text;
	if (isset($comment) && is_object($comment)) {
		$hash = get_comment_meta($comment->comment_ID, 'ct_hash', true);
		if (!empty($hash)) {
			$new_text .= '<hr>Cleantalk ID = ' . $hash;
		}
	}
	return $new_text;
}

/**
 * Send feedback for user deletion 
 * @return null 
 */
function ct_delete_user($user_id, $reassign = null){
	
	$hash = get_user_meta($user_id, 'ct_hash', true);
	if ($hash !== '') {
		ct_feedback($hash, null, 0);
	}
}

/**
 * Manage links in plugins list
 * @return array
*/
function ct_plugin_action_links($links, $file) {
	
	$settings_link = is_network_admin()
		? '<a href="settings.php?page=cleantalk">' . __( 'Settings' ) . '</a>'
		: '<a href="options-general.php?page=cleantalk">' . __( 'Settings' ) . '</a>';
		
	array_unshift( $links, $settings_link ); // before other links
	return $links;
}

/**
 * Manage links and plugins page
 * @return array
*/
function ct_register_plugin_links($links, $file) {
	global $ct_plugin_basename;
	//Return if it's not our plugin
	if ($file != $ct_plugin_basename )
		return $links;
		
	// $links[] = is_network_admin()
		// ? '<a class="ct_meta_links ct_setting_links" href="settings.php?page=cleantalk">' . __( 'Settings' ) . '</a>'
		// : '<a class="ct_meta_links ct_setting_links" href="options-general.php?page=cleantalk">' . __( 'Settings' ) . '</a>';
	
	if(substr(get_locale(), 0, 2) != 'en')
		$links[] = '<a class="ct_meta_links ct_translate_links" href="'
				.sprintf('https://translate.wordpress.org/locale/%s/default/wp-plugins/cleantalk-spam-protect', substr(get_locale(), 0, 2))
				.'" target="_blank">'
				.__('Translate', 'cleantalk')
			.'</a>';
			
	$links[] = '<a class="ct_meta_links ct_faq_links" href="http://wordpress.org/plugins/cleantalk-spam-protect/faq/" target="_blank">' . __( 'FAQ','cleantalk' ) . '</a>';
	$links[] = '<a class="ct_meta_links ct_support_links"href="https://wordpress.org/support/plugin/cleantalk-spam-protect" target="_blank">' . __( 'Support','cleantalk' ) . '</a>';
	
	return $links;
}

/**
 * After options update
 * @return array
*/
function ct_update_option($option_name) {
	global $show_ct_notice_online, $ct_notice_online_label, $ct_notice_trial_label, $trial_notice_showtime, $ct_options, $ct_data, $ct_server_timeout;
	$ct_options = ct_get_options(true);
	$ct_data = ct_get_data(true);

	if($option_name !== 'cleantalk_settings') {
		return;
	}

	$api_key = $ct_options['apikey'];
	if (isset($_POST['cleantalk_settings']['apikey'])) {
		$api_key = trim($_POST['cleantalk_settings']['apikey']);
		$ct_options['apikey'] = $api_key;
	}
	
	if (!ct_valid_key($api_key)) {
		return;
	}
	
	if (isset($_POST['cleantalk_settings']['spam_firewall'])) {
        if ($_POST['cleantalk_settings']['spam_firewall'] == 1) {
            cleantalk_update_sfw();
			ct_send_sfw_log();
        } else {
            // Reseting SFW logs to do not keep huge ammount of data.
			$ct_data['sfw_log']= array();
        }
    }

	$key_valid = true;
	$app_server_error = false;
	$ct_data['testing_failed']=0;
	
	$request=Array();
	$request['method_name'] = 'notice_validate_key'; 
	$request['auth_key'] = $api_key;
	$request['path_to_cms'] = preg_replace('/http[s]?:\/\//', '', get_option('siteurl'), 1);
	$url='https://api.cleantalk.org';
	if(!function_exists('sendRawRequest'))
	{
		require_once('cleantalk.class.php');
	}
	$result=sendRawRequest($url, $request);

	if ($result)
	{
		$result = json_decode($result, true);
		if (isset($result['valid']) && $result['valid'] == 0) {
			$key_valid = false;
			$ct_data['testing_failed']=1;
		}
	}
	if (!$result || !isset($result['valid']))
	{
		$app_server_error = true;
		$ct_data['testing_failed']=1;
	}
	
	if ($key_valid) {
		// Removes cookie for server errors
		if ($app_server_error) {
			setcookie($ct_notice_online_label, '', 1, '/'); // time 1 is exactly in past even clients time() is wrong
			unset($_COOKIE[$ct_notice_online_label]);
		} else {
			setcookie($ct_notice_online_label, (string) time(), strtotime("+14 days"), '/');
		}
		setcookie($ct_notice_trial_label, '0', strtotime("+$trial_notice_showtime minutes"), '/');
		
		//Deleting update flag
		unset($ct_data['ct_show_notice_from_report']);
		
	} else {
		setcookie($ct_notice_online_label, 'BAD_KEY', 0, '/');
	}
	
	update_option('cleantalk_data', $ct_data);
	
    return null;
}

/**
 * Unmark bad words
 * @param string $message
 * @return string Cleat comment
 */
function ct_unmark_red($message) {
	$message = preg_replace("/\<font rel\=\"cleantalk\" color\=\"\#FF1000\"\>(\S+)\<\/font>/iu", '$1', $message);

	return $message;
}

?>
