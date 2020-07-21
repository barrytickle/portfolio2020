<?php
add_action('admin_menu', 'ct_add_comments_menu');
add_action( 'wp_ajax_ajax_check_comments', 'ct_ajax_check_comments' );
add_action( 'wp_ajax_ajax_info_comments', 'ct_ajax_info_comments' );
add_action( 'wp_ajax_ajax_insert_comments', 'ct_ajax_insert_comments' );
add_action( 'wp_ajax_ajax_delete_checked', 'ct_ajax_delete_checked' );
add_action( 'wp_ajax_ajax_delete_all', 'ct_ajax_delete_all' );
add_action( 'wp_ajax_ajax_clear_comments', 'ct_ajax_clear_comments' );
add_action( 'wp_ajax_ajax_ct_approve_comment', 'ct_comment_check_approve_comment' );


function ct_add_comments_menu(){
	if(current_user_can('activate_plugins'))
		add_comments_page( __("Check for spam", 'cleantalk'), __("Check for spam", 'cleantalk'), 'read', 'ct_check_spam', 'ct_show_checkspam_page');
}

function ct_show_checkspam_page(){
    global $ct_plugin_name;
	
	// Getting total spam comments
	$args_spam = array(
		'meta_query' => array(
			Array(
				'key' => 'ct_marked_as_spam',
				'compare' => 'EXISTS'
			)
		),
		'count'=>true
	);
	$cnt_spam = get_comments($args_spam);
	
?>
	<div class="wrap">
		<h2><?php echo $ct_plugin_name; ?></h2><br />
		
		<!-- AJAX error message --> 
		<div id="ct_error_message" style="display:none">
			<h3>
				<?php _e("Ajax error. Process will be automatically restarted in 3 seconds. Status: ", 'cleantalk'); ?><span id="cleantalk_ajax_error"></span> (<span id="cleantalk_js_func"></span>)
			</h3>
			<h4 style="text-align:center;width:90%;">Please, check for JavaScript errors in your dashboard and and repair it.</h4>
		</div>
		
		<!-- Deleting message --> 
		<div id="ct_deleting_message" style="display:none;">
			<?php _e("Please wait for a while. CleanTalk is deleting spam comments. Comments left: ", 'cleantalk'); ?> <span id="cleantalk_comments_left">
            <?php echo $cnt_spam;?>
            </span>
		</div>
		
		<!-- Main info --> 
		<h3 id="ct_checking_status"><?php ct_ajax_info_comments(true);?></h3>
		
		<!-- Cooling notice --> 
		<h3 id="ct_cooling_notice"></h3>
		
		<!-- Preloader and working message -->
		<div id="ct_preloader">
			<img border=0 src="<?php print plugin_dir_url(__FILE__); ?>images/preloader.gif" />
        </div>
		<div id="ct_working_message">
			<?php _e("Please wait! CleanTalk is checking all approved and pending comments via blacklist database at cleantalk.org. You will have option to delete found spam comments after plugin finish.", 'cleantalk'); ?>
		</div>
		<?php
			
			// Pagination			
			$page = !empty($_GET['spam_page']) ? intval($_GET['spam_page']) : 1;
			$on_page = 20;
			
			$args_spam = array(
				'meta_query' => array(
					Array(
						'key' => 'ct_marked_as_spam',
						'value' => '1',
						'compare' => 'NUMERIC'
					)
				),
				'number'=>$on_page,
				'offset'=>($page-1)*$on_page
			);
			
			$c_spam = get_comments($args_spam);
			if($cnt_spam>0){
				
				$pages = ceil(intval($cnt_spam)/$on_page);
				if($pages && $pages != 1){
					echo "<div class='pagination'>"
							."<b>Pages:</b>"
							."<ul class='pagination'>";
								for($i = 1; $i <= $pages; $i++){
									echo "<li class='pagination'>"
											."<a href='edit-comments.php?page=ct_check_spam&spam_page=$i&ct_worked=1'>"
												.($i == $page ? "<span class='current_page'>$i</span>" : $i)
											."</a>"
										."</li>";
								}
						echo "</ul>";
					echo "</div>";
				}
		?>
				<table class="widefat fixed comments" id="ct_check_comments_table">
					<thead>
						<th scope="col" id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
							<input id="cb-select-all-1" type="checkbox" style="margin-top:0;"/>
						</th>
						<th scope="col" id="author" class="manage-column column-slug"><?php print _e( 'Author' );?></th>
						<th scope="col" id="comment" class="manage-column column-comment"><?php _e( 'Comment', 'cleantalk');;?></th>
						<th scope="col" id="response" class="manage-column column-response sortable desc"><?php _e( 'In Response To', 'cleantalk' );?></th>
					</thead>
					<tbody id="the-comment-list" data-wp-lists="list:comment">
						<?php
							for($i=0;$i<sizeof($c_spam);$i++){
								$id      = $c_spam[$i]->comment_ID;
								$post_id = $c_spam[$i]->comment_post_ID;
								$login   = $c_spam[$i]->comment_author;
								$email   = $c_spam[$i]->comment_author_email;
								$ip      = $c_spam[$i]->comment_author_IP;
								
								echo "<tr id='comment-$id' class='comment even thread-even depth-1 approved  cleantalk_comment' data-id='$id'>"
									."<th scope='row' class='check-column'>"
										."<label class='screen-reader-text' for='cb-select-$id'>Select comment</label>"
										."<input id='cb-select-$id' type='checkbox' name='del_comments[]' value='$id'/>"
									."</th>"
									."<td class='author column-author' nowrap>"
									."<strong>"
										.get_avatar( $c_spam[$i]->user_id , 32)
										."$login"
									."</strong>"
									."<br />"
									."<br />";
									
									// Outputs email if exists
									if($email)
										echo "<a href='mailto:$email'>$email</a>"
										."<a href='https://cleantalk.org/blacklists/$email ' target='_blank'>"
											."&nbsp;<img src='".plugin_dir_url(__FILE__)."images/new_window.gif' border='0' style='float:none'/>"
										."</a>";
									else
										echo "No email";
									echo "<br/>";
									
									// Outputs IP if exists
									if($ip)
										echo "<a href='edit-comments.php?s=$ip&mode=detail'>$ip </a>"
										."<a href='https://cleantalk.org/blacklists/$ip ' target='_blank'>"
											."&nbsp;<img src='".plugin_dir_url(__FILE__)."images/new_window.gif' border='0' style='float:none'/>"
										."</a>";
									else
										echo "No IP adress";
								echo "</td>";
							?>
								<td class="comment column-comment">
									<div class="submitted-on">
										<?php printf( __( 'Submitted on <a href="%1$s">%2$s at %3$s</a>' ), get_comment_link($id),
											/* translators: comment date format. See http://php.net/date */
											get_comment_date( __( 'Y/m/d' ),$id ),
											get_comment_date( get_option( 'time_format' ),$id )
											); 
										?>
											
									</div>
									<p>
									<?php print $c_spam[$i]->comment_content; ?>
									</p>
									<div style="height:16px; display: none;" id='cleantalk_button_set_<?php print $id; ?>'>
										<a href="#" class="cleantalk_delete_from_list_button"  	data-id="<?php print $id; ?>" style="color:#0a0;" onclick="return false;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';"><?php _e("Approve", "cleantalk"); ?></a>
										&nbsp;|&nbsp;
										<a href="#" class="cleantalk_delete_button"  			data-id="<?php print $id; ?>" style="color:#a00;" onclick="return false;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';"><?php _e("Delete", "cleantalk"); ?></a>
									</div>
								</td>
								<td class="response column-response">
									<div>
										<span>
											<a href="/wp-admin/post.php?post=<?php print $post_id; ?>&action=edit"><?php print get_the_title($post_id); ?></a>
											<br/>
											<a href="/wp-admin/edit-comments.php?p=<?php print $post_id; ?>" class="post-com-count">
												<span class="comment-count"><?php
													$p_cnt=wp_count_comments();
													print $p_cnt->total_comments;
												?></span>
											</a>
										</span>
										<a href="<?php print get_permalink($post_id); ?>"><?php print _e('View Post');?></a>
									</div>
								</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
				<?php
					// Pagination
					if($pages && $pages != 1){
						echo "<div class='pagination'>"
								."<b>Pages:</b>"
								."<ul class='pagination'>";
									for($i = 1; $i <= $pages; $i++){
										echo "<li class='pagination'>"
												."<a href='edit-comments.php?page=ct_check_spam&spam_page=$i&ct_worked=1'>"
													.($i == $page ? "<span class='current_page'>$i</span>" : $i)
												."</a>"
											."</li>";
									}
							echo "</ul>";
						echo "</div>";
					}
				?>
				<div id="ct_tools_buttons" style="margin-top: 10px;">
					<button class="button" id="ct_delete_all"><?php _e('Delete all comments from the list', 'cleantalk'); ?></button> 
					<button class="button" id="ct_delete_checked"><?php _e('Delete selected', 'cleantalk'); ?></button><br /><br />
				</div>
				<?php
			}
			echo $_SERVER['REMOTE_ADDR']=='127.0.0.1' ? '<br /><button class="button" id="ct_insert_comments">'. __('Insert comments', 'cleantalk') .'</button><br />' : '';
			?>
		<br />
		<table id="new_test_table">
			<tr valign="top">
				<td>
					<button class="button" id="ct_check_spam_button"><?php _e("Check for spam", 'cleantalk'); ?></button><br /><br />
				</td>
				<td style="padding-left: 2em;">
					<div id="ct_info_message">
						<?php _e("The plugin will check all comments against blacklists database and show you senders that have spam activity on other websites.", 'cleantalk'); ?>
					</div>
				</td>
			</tr>
		</table>
		<?php
			if($cnt_spam > 0)
				echo "<div id='ct_search_info'>"
						."<br />"
						.__('There is some differencies between blacklists database and our API mechanisms. Blacklists shows all history of spam activity, but our API (that used in spam checking) used another parameters, too: last day of activity, number of spam attacks during last days etc. This mechanisms help us to reduce number of false positivitie. So, there is nothing strange, if some emails/IPs will be not found by this checking.', 'cleantalk')
					."</div>";
		?>
	</div>
	<?php
}

function ct_ajax_check_comments(){
	
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	global $ct_options, $ct_ip_penalty_days;
	$ct_options = ct_get_options();
	
	$args_unchecked = array(
		'meta_query' => array(
			//'relation' => 'AND',
			Array(
				'key' => 'ct_checked',
				'value' => '1',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key' => 'ct_bad',
				'value' => '1',
				'compare' => 'NOT EXISTS'
			)
			/*array(
				'key' => 'ct_hash',
				'value' => '1',
				'compare' => 'NOT EXISTS'
			)*/
		),
		'number'=>100,
		'status' => 'all'
	);
	
	$c=get_comments($args_unchecked);
	
    $c=array_values($c);
	
	if(sizeof($c)>0){
		
		foreach($c as $comment_index => $comment){
			
			if(!isset($curr_date))
				$curr_date = (substr($comment->comment_date, 0, 10) ? substr($comment->comment_date, 0, 10) : '');

			if(substr($comment->comment_date, 0, 10) != $curr_date)
				unset($c[$comment_index]);

		}
		unset($comment_index, $comment);
		
		$data=Array();
		for($i=0;$i<sizeof($c);$i++){
			
			$curr_ip = $c[$i]->comment_author_IP;
			$curr_email = $c[$i]->comment_author_email;
			
			// Check for identity
			$curr_ip    = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $curr_ip) === 1 ? $curr_ip    : null;
			$curr_email = preg_match('/^\S+@\S+\.\S+$/', $curr_email) === 1                    ? $curr_email : null;
			
			if(empty($curr_ip) && empty($curr_email)){
				update_comment_meta($c[$i]->comment_ID,'ct_bad','1',true);
				unset($c[$i]);
				$c = array_values($c);
			}else{
				if(!empty($curr_ip))
					$data[] = $curr_ip;
				if(!empty($curr_email))
					$data[] = $curr_email;
				$c[$i]->comment_author_IP    = empty($curr_ip)    ? 'none' : $curr_ip;				
			    $c[$i]->comment_author_email = empty($curr_email) ? 'none' : $curr_email;
			}
		}
		
		// Drop if data empty
		if(count($data) == 0)
			die('1');
		
		$data=implode(',',$data);
		
		$request=Array();
        $request['method_name'] = 'spam_check_cms'; 
        $request['auth_key'] = $ct_options['apikey'];
        $request['data'] = $data;
		$request['date'] = $curr_date;
				
        $url='https://api.cleantalk.org';
		
        if(!function_exists('sendRawRequest'))
            require_once('cleantalk.class.php');
		
        $result=sendRawRequest($url, $request, false, 5);
        
		if(empty($result)){
			echo "Connection error";
		}else{
		
			$result=json_decode($result);
			if(isset($result->error_message)){
				
				// Data not set, marks comments as checked to avoid loops.
				if (isset($result->error_no) && $result->error_no == 8) { // Data not set.
					for($i=0;$i<sizeof($c);$i++) {
						add_comment_meta($c[$i]->comment_ID,'ct_checked',date("Y-m-d H:m:s"),true);
					}
					print 1;
				}else
					print "Server response: ".$result->error_message;
				
			}else{
				for($i=0;$i<sizeof($c);$i++){
					
					
					$mark_spam_ip = false;
					$mark_spam_email = false;

					add_comment_meta($c[$i]->comment_ID,'ct_checked',date("Y-m-d H:m:s"),true);
					$uip=$c[$i]->comment_author_IP;
					$uim=$c[$i]->comment_author_email;
					
					if(isset($result->data->$uip) && $result->data->$uip->appears == 1)
						$mark_spam_ip = true;
					
					if(isset($result->data->$uim) && $result->data->$uim->appears==1)
						$mark_spam_email = true;
					
					if ($mark_spam_ip || $mark_spam_email)
						add_comment_meta($c[$i]->comment_ID,'ct_marked_as_spam','1',true);
					
				}
				print 1;
			}
		}
	}else{
		print 0;
	}

	die;
}

function ct_ajax_info_comments($direct_call = true){
	
	if (!$direct_call)
        check_ajax_referer( 'ct_secret_nonce', 'security' );

	// Total comments
	$cnt=get_comments(Array('count'=>true));
	
	// Spam comments
	$args_spam = array(
		'meta_query' => array(
			Array(
				'key' => 'ct_marked_as_spam',
				'value' => '1',
				'compare' => 'NUMERIC'
			)
		),
		'count'=>true
	);
	$cnt_spam=get_comments($args_spam);
	
	// Already checked by Cleantalk
	$args_checked1=array(
		'meta_query' => array(
			Array(
				'key' => 'ct_hash',
				// 'value'=>'1',
				'compare' => 'EXISTS'
			)
		),
		'count'=>true
	);
	$cnt_checked1=get_comments($args_checked1);
	
	// Checked comments
	$args_checked2=array(
		'meta_query' => array(
			Array(
				'key' => 'ct_checked',
				//'value'=>'1',
				'compare' => 'EXISTS'
			)
		),
		'count'=>true
	);
	$cnt_checked2 =get_comments($args_checked2);
	
	// Total checked
	$cnt_checked =$cnt_checked1 + $cnt_checked2;
	
	// Bad comments (without IP and Email)
	$args_bad=array(
		'meta_query' => array(
			Array(
				'key' => 'ct_bad',
				'value'=>'1',
				'compare' => 'NUMERIC'
			)
		),
		'count'=>true
	);
	$cnt_bad =get_comments($args_bad);
	
	printf (__("Total comments %s. Checked %s. Found %s spam comments. %d bad comments (without IP or email).", 'cleantalk'), $cnt, $cnt_checked, $cnt_spam, $cnt_bad);
    $backup_notice = '&nbsp;';
	
    if ($cnt_spam > 0)
        $backup_notice = __("Please do backup of WordPress database before delete any comments!", 'cleantalk');
	
	print "<p>$backup_notice</p>";
    if (!$direct_call)
	    die();
    
    return null;
}


function ct_ajax_insert_comments(){
	
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	global $wpdb;
	
	$time = current_time('mysql');
	$to_insert = 20;
	
	$result = $wpdb->get_results("SELECT network FROM `".$wpdb->base_prefix."cleantalk_sfw` LIMIT $to_insert;", ARRAY_A);
	
	if($result){
		$ip = array();
		foreach($result as $value){
			$ips[] = long2ip($value['network']);
		}
		unset($value);
	
		$inserted = 0;
		for($i=0; $i<$to_insert; $i++){
			
			$rnd=mt_rand(1,100);
			
			$email="stop_email@example.com";
			
			$data = array(
				'comment_post_ID' => 1,
				'comment_author' => "author_$rnd",
				'comment_author_email' => $email,
				'comment_author_url' => 'http://',
				'comment_content' => "comment content ".mt_rand(1,10000)." ".mt_rand(1,10000)." ".mt_rand(1,10000),
				'comment_type' => '',
				'comment_parent' => 0,
				'user_id' => 1,
				'comment_author_IP' => $ips[$i],
				'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
				'comment_date' => $time,
				'comment_approved' => 1,
			);
			
			wp_insert_comment($data);
			$inserted++;
		}
	}else{
		$inserted = '0';
	}
	
	print $inserted;
	die();
}

function ct_ajax_delete_checked(){
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	foreach($_POST['ids'] as $key=>$value){
		wp_delete_comment($value, false);
	}
	die();
}

function ct_ajax_delete_all(){
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	$args_spam = array(
		'number'=>100,
		'meta_query' => array(
			Array(
				'key' => 'ct_marked_as_spam',
				'value' => '1',
				'compare' => 'NUMERIC'
			)
		)
	);	
	$c_spam=get_comments($args_spam);

	$cnt=sizeof($c_spam);
	
	$args_spam = array(
		'count'=>true,
		'meta_query' => array(
			Array(
				'key' => 'ct_marked_as_spam',
				'value' => '1',
				'compare' => 'NUMERIC'
			)
		)
	);
	$cnt_all=get_comments($args_spam);

	for($i=0;$i<sizeof($c_spam);$i++){
		wp_delete_comment($c_spam[$i]->comment_ID, false);
		usleep(10000);
	}
	print $cnt_all;
	die();
}

function ct_ajax_clear_comments(){
	check_ajax_referer( 'ct_secret_nonce', 'security' );
	global $wpdb;
	$wpdb->query("delete from $wpdb->commentmeta where meta_key='ct_checked' or meta_key='ct_marked_as_spam' or meta_key='ct_bad';");
	die();
}

/**
 * Admin action 'comment_unapproved_to_approved' - Approve comment, delete from the deleting list
 */
function ct_comment_check_approve_comment(){
	
	check_ajax_referer( 'ct_secret_nonce', 'security' );

	$id=$_POST['id'];
	$comment = get_comment($id, 'ARRAY_A');
	$comment['comment_content'] = ct_unmark_red($comment['comment_content']);
	$comment['comment_approved'] = 1;
	update_comment_meta($id, 'ct_marked_as_spam', 0);
	wp_update_comment($comment);

	die();
}
?>
