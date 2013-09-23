<?php
/*
+----------------------------------------------------------------+
|                                                                |
|	WordPress Plugin: WP-Polls                                   |
|	Copyright (c) 2012 Lester "GaMerZ" Chan                      |
|                                                                |
|	File Written By:                                             |
|	- Lester "GaMerZ" Chan                                       |
|	- http://lesterchan.net                                      |
|                                                                |
|	File Information:                                            |
|	- Configure Poll Options                                     |
|	- wp-content/plugins/wp-polls/polls-options.php              |
|                                                                |
+----------------------------------------------------------------+
*/


### Function: Display Polls Options Page
function display_polls_options_page() {

	### Polls Table Name
	global $wpdb;
	$wpdb->pollsq = $wpdb->prefix.'pollsq';
	$wpdb->pollsa = $wpdb->prefix.'pollsa';
	$wpdb->pollsip = $wpdb->prefix.'pollsip';

	### Check Whether User Can Manage Polls
	if(!current_user_can('manage_polls')) {
		die('Access Denied');
	}

	### Variables Variables Variables
	$page_name = 'admin.php?page=wp-polls_options';
	$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);

	### If Form Is Submitted
	if( isset($_POST['Submit']) && $_POST['Submit'] ) {
		check_admin_referer('wp-polls_options');
		// Check if form was submitted via buttons to add/delete bar images, instead of regular options changes
		if ($_FILES['poll_bar_image']['size'] > 0) {
			if (poll_add_bar_image())
				$text = '<font color="green">'.__('Added new bar image', 'wp-polls').': '.$_FILES['poll_bar_image']['name'].'</font><br />';
			else
				$text = '<font color="red">'.__('Unsuitable file provided as bar image', 'wp-polls').'</font><br />';
		}
		else if ($_POST['poll_delete_bar_images'] == 1) {
			if (poll_delete_bar_images())
				$text = '<font color="green">'.__('Deleted bar images', 'wp-polls').'</font><br />';
			else 
				$text = '<font color="red">'.__('Failed to delete bar images. Check server permissions.', 'wp-polls').'</font><br />';
			if (poll_update_bar_style_after_delete())
				$text.= '<font color="red">'.__('Bar style reset to default', 'wp-polls').'</font><br />';
		}
		else {
			$poll_bar_style = strip_tags(trim($_POST['poll_bar_style']));
			$poll_bar_background = strip_tags(trim($_POST['poll_bar_bg']));
			$poll_bar_border = strip_tags(trim($_POST['poll_bar_border']));
			$poll_bar_height = intval($_POST['poll_bar_height']);
			$poll_bar = array('style' => $poll_bar_style, 'background' => $poll_bar_background, 'border' => $poll_bar_border, 'height' => $poll_bar_height);
			$poll_ajax_style = array('loading' => intval($_POST['poll_ajax_style_loading']), 'fading' => intval($_POST['poll_ajax_style_fading']));
			$poll_ans_sortby = strip_tags(trim($_POST['poll_ans_sortby']));
			$poll_ans_sortorder = strip_tags(trim($_POST['poll_ans_sortorder']));
			$poll_ans_result_sortby = strip_tags(trim($_POST['poll_ans_result_sortby']));
			$poll_ans_result_sortorder = strip_tags(trim($_POST['poll_ans_result_sortorder']));
			$poll_archive_perpage = intval($_POST['poll_archive_perpage']);
			$poll_archive_displaypoll = intval($_POST['poll_archive_displaypoll']);
			$poll_archive_url = strip_tags(trim($_POST['poll_archive_url']));
			$poll_archive_show = intval($_POST['poll_archive_show']);
			$poll_currentpoll = intval($_POST['poll_currentpoll']);
			$poll_close = intval($_POST['poll_close']);
			$poll_logging_method = intval($_POST['poll_logging_method']);
			$poll_cookielog_expiry = intval($_POST['poll_cookielog_expiry']);
			$poll_allowtovote = intval($_POST['poll_allowtovote']);
			$update_poll_queries = array();
			$update_poll_text = array();
			$update_poll_queries[] = update_option('poll_bar', $poll_bar);
			$update_poll_queries[] = update_option('poll_ajax_style', $poll_ajax_style);
			$update_poll_queries[] = update_option('poll_ans_sortby', $poll_ans_sortby);
			$update_poll_queries[] = update_option('poll_ans_sortorder', $poll_ans_sortorder);
			$update_poll_queries[] = update_option('poll_ans_result_sortby', $poll_ans_result_sortby);
			$update_poll_queries[] = update_option('poll_ans_result_sortorder', $poll_ans_result_sortorder);
			$update_poll_queries[] = update_option('poll_archive_perpage', $poll_archive_perpage);
			$update_poll_queries[] = update_option('poll_archive_displaypoll', $poll_archive_displaypoll);
			$update_poll_queries[] = update_option('poll_archive_url', $poll_archive_url);
			$update_poll_queries[] = update_option('poll_archive_show', $poll_archive_show);
			$update_poll_queries[] = update_option('poll_currentpoll', $poll_currentpoll);
			$update_poll_queries[] = update_option('poll_close', $poll_close);
			$update_poll_queries[] = update_option('poll_logging_method', $poll_logging_method);
			$update_poll_queries[] = update_option('poll_cookielog_expiry', $poll_cookielog_expiry);
			$update_poll_queries[] = update_option('poll_allowtovote', $poll_allowtovote);
			$update_poll_text[] = __('Poll Bar Style', 'wp-polls');
			$update_poll_text[] = __('Poll AJAX Style', 'wp-polls');
			$update_poll_text[] = __('Sort Poll Answers By Option', 'wp-polls');
			$update_poll_text[] = __('Sort Order Of Poll Answers Option', 'wp-polls');
			$update_poll_text[] = __('Sort Poll Results By Option', 'wp-polls');
			$update_poll_text[] = __('Sort Order Of Poll Results Option', 'wp-polls');
			$update_poll_text[] = __('Number Of Polls Per Page To Display In Poll Archive Option', 'wp-polls');
			$update_poll_text[] = __('Type Of Polls To Display In Poll Archive Option', 'wp-polls');
			$update_poll_text[] = __('Poll Archive URL Option', 'wp-polls');
			$update_poll_text[] = __('Show Poll Achive Link Option', 'wp-polls');
			$update_poll_text[] = __('Current Active Poll Option', 'wp-polls');
			$update_poll_text[] = __('Poll Close Option', 'wp-polls');
			$update_poll_text[] = __('Logging Method', 'wp-polls');
			$update_poll_text[] = __('Cookie And Log Expiry Option', 'wp-polls');
			$update_poll_text[] = __('Allow To Vote Option', 'wp-polls');
			$i=0;
			$text = '';
			foreach($update_poll_queries as $update_poll_query) {
				if($update_poll_query) {
					$text .= '<font color="green">'.$update_poll_text[$i].' '.__('Updated', 'wp-polls').'</font><br />';
				}
				$i++;
			}
			if(empty($text)) {
				$text = '<font color="red">'.__('No Poll Option Updated', 'wp-polls').'</font>';
			}
			cron_polls_place();
		}
	}
	?>
	<script type="text/javascript">
	/* <![CDATA[*/
		function set_pollbar_height(height) {
				jQuery("#poll_bar_height").val(height);
		}
		function update_pollbar(where) {
			pollbar_background = "#" + jQuery("#poll_bar_bg").val();
			pollbar_border = "#" + jQuery("#poll_bar_border").val();
			pollbar_height = jQuery("#poll_bar_height").val() + "px";
			if(where  == "background") {
				jQuery("#wp-polls-pollbar-bg").css("background-color", pollbar_background);
			} else if(where == "border") {
				jQuery("#wp-polls-pollbar-border").css("background-color", pollbar_border);
			} else if(where == "style") {
				pollbar_style = jQuery("input[name='poll_bar_style']:checked").val();
				if(pollbar_style == "use_css")
					jQuery("#wp-polls-pollbar").css("background-image", "none");
				else 
					jQuery("#wp-polls-pollbar").css("background-image", "url('<?php echo plugins_url(); ?>/" + pollbar_style + "')");
			}
			jQuery("#wp-polls-pollbar").css({"background-color":pollbar_background, "border":"1px solid " + pollbar_border, "height":pollbar_height});
		}
		function delete_bar_images(count) {
			var result = true;
			if (count == 1) result = confirm("This will permanently remove " + count + " image. Proceed anyway?");
			if (count > 1) result = confirm("This will permanently remove " + count + " images. Proceed anyway?");
			if (result==true) {
				jQuery("#poll_delete_bar_images").val(1);
				jQuery("input[type=submit]").click();
			}
		}
	/* ]]> */
	</script>
	<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
	<form id="poll_options_form" method="post" action="<?php echo admin_url($page_name); ?>" enctype="multipart/form-data">
	<?php wp_nonce_field('wp-polls_options'); ?>
	<div class="wrap">
		<div id="icon-wp-polls" class="icon32"><br /></div>
		<h2><?php _e('Poll Options', 'wp-polls'); ?></h2>
		<!-- Poll Bar Style -->
		<h3><?php _e('Appearance', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Bar Style', 'wp-polls'); ?></th>
				<td>
					<?php
						$pollbar = get_option('poll_bar');
						display_polls_bar_options_for_dir('wp-polls/images/bars', $pollbar['style']);
						$user_bar_image_count = display_polls_bar_options_for_dir('wp-polls-data/bars', $pollbar['style']);						
					?>
					<input type="radio" id="poll_bar_style-use_css" name="poll_bar_style" value="use_css"<?php checked('use_css', $pollbar['style']); ?> onclick="update_pollbar('style');" /><label for="poll_bar_style-use_css">&nbsp;&nbsp;&nbsp;<?php _e('CSS Style:', 'wp-polls'); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"></th>
				<td>
				<div style="width:400px;">
					<table id="wp-polls-css-table">
						<tr>
							<th scope="row" valign="top"><?php _e('Background', 'wp-polls'); ?></th>
							<td width="40%" dir="ltr"><input type="text" id="poll_bar_bg" name="poll_bar_bg" value="<?php echo $pollbar['background']; ?>" size="6" maxlength="6" onblur="update_pollbar('background');" />
							<td><div id="wp-polls-pollbar-bg" style="background-color: #<?php echo $pollbar['background']; ?>;"></div></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><?php _e('Border', 'wp-polls'); ?></th>
							<td width="40%" dir="ltr"><input type="text" id="poll_bar_border" name="poll_bar_border" value="<?php echo $pollbar['border']; ?>" size="6" maxlength="6" onblur="update_pollbar('border');" />
							<td><div id="wp-polls-pollbar-border" style="background-color: #<?php echo $pollbar['border']; ?>;"></div></td>
						</tr>
						<tr>
							<th scope="row" valign="top"><?php _e('Height', 'wp-polls'); ?></th>
							<td dir="ltr"><input type="file" id="poll_bar_image_file" name="poll_bar_image" style="position:absolute; left:-1000px;" onchange="jQuery('input[type=submit]').click();"/><input type="text" id="poll_bar_height" name="poll_bar_height" value="<?php echo $pollbar['height']; ?>" size="2" maxlength="2" onblur="update_pollbar('height');">px</td><td></td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"></th>
				<td><input type="button" class="button" value="Add Bar Image" onclick="jQuery('#poll_bar_image_file').click();" />
				<input type="hidden" id="poll_delete_bar_images" name="poll_delete_bar_images" value="0"/>
				<input type="button" class="button" value="Delete Added Images" onclick="delete_bar_images(<?php echo $user_bar_image_count;?>);" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Bar Preview', 'wp-polls'); ?></th>
				<td colspan="2">
					<?php
						// TODO On plugin upgrade will need to change current style setting to become a full path if it is not 'use_css'.
						// The changes we have made here will break existing installs if this is not done.
						if($pollbar['style'] == 'use_css') {
							echo '<div id="wp-polls-pollbar" style="width: 255px; height: '.$pollbar['height'].'px; background-color: #'.$pollbar['background'].'; border: 1px solid #'.$pollbar['border'].'"></div>'."\n";
						} else {
							echo '<div id="wp-polls-pollbar" style="width: 255px; height: '.$pollbar['height'].'px; background-color: #'.$pollbar['background'].'; border: 1px solid #'.$pollbar['border'].'; background-image: url(\''.plugins_url($pollbar['style']).'\');"></div>'."\n";
						}
					?>
				</td>
			</tr>
		</table>

		<!-- Polls AJAX Style -->
		<?php $poll_ajax_style = get_option('poll_ajax_style'); ?>
		<h3><?php _e('Polls AJAX Style', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Show Loading Image With Text', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ajax_style_loading" size="1">
						<option value="0"<?php selected('0', $poll_ajax_style['loading']); ?>><?php _e('No', 'wp-polls'); ?></option>
						<option value="1"<?php selected('1', $poll_ajax_style['loading']); ?>><?php _e('Yes', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Show Fading In And Fading Out Of Poll', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ajax_style_fading" size="1">
						<option value="0"<?php selected('0', $poll_ajax_style['fading']); ?>><?php _e('No', 'wp-polls'); ?></option>
						<option value="1"<?php selected('1', $poll_ajax_style['fading']); ?>><?php _e('Yes', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<!-- Sorting Of Poll Answers -->
		<h3><?php _e('Sorting Of Poll Answers', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Sort Poll Answers By:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ans_sortby" size="1">
						<option value="polla_aid"<?php selected('polla_aid', get_option('poll_ans_sortby')); ?>><?php _e('Exact Order', 'wp-polls'); ?></option>
						<option value="polla_answers"<?php selected('polla_answers', get_option('poll_ans_sortby')); ?>><?php _e('Alphabetical Order', 'wp-polls'); ?></option>
						<option value="RAND()"<?php selected('RAND()', get_option('poll_ans_sortby')); ?>><?php _e('Random Order', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Sort Order Of Poll Answers:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ans_sortorder" size="1">
						<option value="asc"<?php selected('asc', get_option('poll_ans_sortorder')); ?>><?php _e('Ascending', 'wp-polls'); ?></option>
						<option value="desc"<?php selected('desc', get_option('poll_ans_sortorder')); ?>><?php _e('Descending', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<!-- Sorting Of Poll Results -->
		<h3><?php _e('Sorting Of Poll Results', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Sort Poll Results By:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ans_result_sortby" size="1">
						<option value="polla_votes"<?php selected('polla_votes', get_option('poll_ans_result_sortby')); ?>><?php _e('Votes Cast', 'wp-polls'); ?></option>
						<option value="polla_aid"<?php selected('polla_aid', get_option('poll_ans_result_sortby')); ?>><?php _e('Exact Order', 'wp-polls'); ?></option>
						<option value="polla_answers"<?php selected('polla_answers', get_option('poll_ans_result_sortby')); ?>><?php _e('Alphabetical Order', 'wp-polls'); ?></option>
						<option value="RAND()"<?php selected('RAND()', get_option('poll_ans_result_sortby')); ?>><?php _e('Random Order', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Sort Order Of Poll Results:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_ans_result_sortorder" size="1">
						<option value="asc"<?php selected('asc', get_option('poll_ans_result_sortorder')); ?>><?php _e('Ascending', 'wp-polls'); ?></option>
						<option value="desc"<?php selected('desc', get_option('poll_ans_result_sortorder')); ?>><?php _e('Descending', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<!-- Allow To Vote -->
		<h3><?php _e('Allow To Vote', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Who Is Allowed To Vote?', 'wp-polls'); ?></th>
				<td>
					<select name="poll_allowtovote" size="1">
						<option value="0"<?php selected('0', get_option('poll_allowtovote')); ?>><?php _e('Guests Only', 'wp-polls'); ?></option>
						<option value="1"<?php selected('1', get_option('poll_allowtovote')); ?>><?php _e('Registered Users Only', 'wp-polls'); ?></option>
						<option value="2"<?php selected('2', get_option('poll_allowtovote')); ?>><?php _e('Registered Users And Guests', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<!-- Logging Method -->
		<h3><?php _e('Logging Method', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr valign="top">
				<th scope="row" valign="top"><?php _e('Poll Logging Method:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_logging_method" size="1">
						<option value="0"<?php selected('0', get_option('poll_logging_method')); ?>><?php _e('Do Not Log', 'wp-polls'); ?></option>
						<option value="1"<?php selected('1', get_option('poll_logging_method')); ?>><?php _e('Logged By Cookie', 'wp-polls'); ?></option>
						<option value="2"<?php selected('2', get_option('poll_logging_method')); ?>><?php _e('Logged By IP', 'wp-polls'); ?></option>
						<option value="3"<?php selected('3', get_option('poll_logging_method')); ?>><?php _e('Logged By Cookie And IP', 'wp-polls'); ?></option>
						<option value="4"<?php selected('4', get_option('poll_logging_method')); ?>><?php _e('Logged By Username', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Expiry Time For Cookie And Log:', 'wp-polls'); ?></th>
				<td><input type="text" name="poll_cookielog_expiry" value="<?php echo intval(get_option('poll_cookielog_expiry')); ?>" size="10" /> <?php _e('seconds (0 to disable)', 'wp-polls'); ?></td>
			</tr>
		</table>

		<!-- Poll Archive -->
		<h3><?php _e('Poll Archive', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Number Of Polls Per Page:', 'wp-polls'); ?></th>
				<td><input type="text" name="poll_archive_perpage" value="<?php echo intval(get_option('poll_archive_perpage')); ?>" size="2" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Type Of Polls To Display In Poll Archive:', 'wp-polls'); ?></th>
				<td>
					<select name="poll_archive_displaypoll" size="1">
						<option value="1"<?php selected('1', get_option('poll_archive_displaypoll')); ?>><?php _e('Closed Polls Only', 'wp-polls'); ?></option>
						<option value="2"<?php selected('2', get_option('poll_archive_displaypoll')); ?>><?php _e('Opened Polls Only', 'wp-polls'); ?></option>
						<option value="3"<?php selected('3', get_option('poll_archive_displaypoll')); ?>><?php _e('Closed And Opened Polls', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Poll Archive URL:', 'wp-polls'); ?></th>
				<td><input type="text" name="poll_archive_url" value="<?php echo get_option('poll_archive_url'); ?>" size="50" dir="ltr" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Display Poll Archive Link Below Poll?', 'wp-polls'); ?></th>
				<td>
					<select name="poll_archive_show" size="1">
						<option value="0"<?php selected('0', get_option('poll_archive_show')); ?>><?php _e('No', 'wp-polls'); ?></option>
						<option value="1"<?php selected('1', get_option('poll_archive_show')); ?>><?php _e('Yes', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php _e('Note', 'wp-polls'); ?></th>
				<td><em><?php _e('Only polls\' results will be shown in the Poll Archive regardless of whether the poll is closed or opened.', 'wp-polls'); ?></em></td>
			</tr>
		</table>

		<!-- Current Active Poll -->
		<h3><?php _e('Current Active Poll', 'wp-polls'); ?></h3>
		<table class="form-table">
			 <tr>
				<th scope="row" valign="top"><?php _e('Current Active Poll', 'wp-polls'); ?>:</th>
				<td>
					<select name="poll_currentpoll" size="1">
						<option value="-1"<?php selected(-1, get_option('poll_currentpoll')); ?>><?php _e('Do NOT Display Poll (Disable)', 'wp-polls'); ?></option>
						<option value="-2"<?php selected(-2, get_option('poll_currentpoll')); ?>><?php _e('Display Random Poll', 'wp-polls'); ?></option>
						<option value="0"<?php selected(0, get_option('poll_currentpoll')); ?>><?php _e('Display Latest Poll', 'wp-polls'); ?></option>
						<optgroup>&nbsp;</optgroup>
						<?php
							$polls = $wpdb->get_results("SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC");
							if($polls) {
								foreach($polls as $poll) {
									$poll_question = stripslashes($poll->pollq_question);
									$poll_id = intval($poll->pollq_id);
									if($poll_id == intval(get_option('poll_currentpoll'))) {
										echo "<option value=\"$poll_id\" selected=\"selected\">$poll_question</option>\n";
									} else {
										echo "<option value=\"$poll_id\">$poll_question</option>\n";
									}
								}
							}
						?>
					</select>
				</td>
			</tr>
			 <tr>
				<th scope="row" valign="top"><?php _e('When Poll Is Closed', 'wp-polls'); ?>:</th>
				<td>
					<select name="poll_close" size="1">
						<option value="1"<?php selected(1, get_option('poll_close')); ?>><?php _e('Display Poll\'s Results', 'wp-polls'); ?></option>
						<option value="3"<?php selected(3, get_option('poll_close')); ?>><?php _e('Display Disabled Poll\'s Voting Form', 'wp-polls'); ?></option>
						<option value="2"<?php selected(2, get_option('poll_close')); ?>><?php _e('Do Not Display Poll In Post/Sidebar', 'wp-polls'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<!-- Submit Button -->
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'wp-polls'); ?>" />
		</p>
	</div>
	</form>
<?php
}

### Function: Display a selectable option for all available bars in specified location
function display_polls_bar_options_for_dir($raw_path, $selected_bar) {
	$bar_url = plugins_url($raw_path);
	$bar_path = WP_PLUGIN_DIR.'/'.$raw_path;
	$count = 0;
	if($handle = @opendir($bar_path)) {
		while (false !== ($filename = readdir($handle))) {
			if (substr($filename, 0, 1) != '.' && substr($filename, 0, 2) != '..') {
				if(!is_dir($bar_path.'/'.$filename)) {
					$pollbar_info = getimagesize($bar_path.'/'.$filename);
					if ($pollbar_info != FALSE) {
						echo '<p>'."\n";
						$short_filename = pathinfo($filename, PATHINFO_FILENAME);
						if($selected_bar == $raw_path.'/'.$filename) {
							echo '<input type="radio" id="poll_bar_style-'.$short_filename.'" name="poll_bar_style" value="'.$raw_path.'/'.$filename.'" checked="checked" onclick="set_pollbar_height('.$pollbar_info[1].'); update_pollbar(\'style\');" />';
						} else {
							echo '<input type="radio" id="poll_bar_style-'.$short_filename.'" name="poll_bar_style" value="'.$raw_path.'/'.$filename.'" onclick="set_pollbar_height('.$pollbar_info[1].'); update_pollbar(\'style\');" />';
						}
						echo '<label for="poll_bar_style-'.$short_filename.'">&nbsp;&nbsp;&nbsp;';
						echo '<span style="display:inline-block; width: 100px; height: '.$pollbar_info[1].'px; background-image: url(\''.$bar_url.'/'.$filename.'\');"></span>';
						echo '&nbsp;&nbsp;&nbsp;('.$short_filename.')</label>';
						echo '</p>'."\n";
					}
					$count ++;
				}
			}
		}
		closedir($handle);
	}
	return $count;
}

### Function: 
function poll_check_user_bars_destination() {
	$old_mask = umask(0); 
	if (!is_dir(WP_PLUGIN_DIR.'/wp-polls-data') && !mkdir(WP_PLUGIN_DIR.'/wp-polls-data',0775)) return false;
	if (!is_dir(WP_PLUGIN_DIR.'/wp-polls-data/bars') && !mkdir(WP_PLUGIN_DIR.'/wp-polls-data/bars',0775)) return false;
	umask($old_mask); 
	return true;
}


### Function: Attempt to add an uploaded image as a bar image, returning true if successful
function poll_add_bar_image() {
	// Determine suitability of file: it must be an image of no more than 26px height
	$size = getimagesize($_FILES['poll_bar_image']['tmp_name']);
	if ($size == false || $size[1] > 26) return false;
	// Check if destination folder exists and create it as necessary
	if (!poll_check_user_bars_destination()) return false;
	// Determine destination file name and move the uploaded file there
	$dest_file = WP_PLUGIN_DIR.'/wp-polls-data/bars/'.$_FILES['poll_bar_image']['name'];
	$idx = 2;
	while (file_exists($dest_file)) {
		$dest_file = WP_PLUGIN_DIR.'/wp-polls-data/bars/'.pathinfo($_FILES['poll_bar_image']['name'], PATHINFO_FILENAME)
			.'-'.$idx.'.'.pathinfo($_FILES['poll_bar_image']['name'], PATHINFO_EXTENSION);
		++ $idx;
	}
	$success = move_uploaded_file($_FILES['poll_bar_image']['tmp_name'], $dest_file);
	chmod($dest_file, 0664);
	return $success;
}

### Function: Attempt to delete all uploaded bar images, returning true if successful
function poll_delete_bar_images() {
	$bar_path = WP_PLUGIN_DIR."/wp-polls-data/bars/";
	$success = true;
	$handle = @opendir($bar_path);
	if ($handle == false) return false;
	while (false !== ($filename = readdir($handle))) {
//		if (substr($filename, 0, 1) != '.' && substr($filename, 0, 2) != '..') {
			if(!is_dir($bar_path.'/'.$filename) && !unlink($bar_path.'/'.$filename)) $success = false;
//		}
	}
	closedir($handle);
	return $success;
}

### Function: Check if current bar style was set to one of uploaded bars after deleting them.
### If so, reset style to default and return true.
function poll_update_bar_style_after_delete() {
	$poll_bar = get_option('poll_bar');
	$user_bar_image_path = "wp-polls-data/bars/";
	if (strncmp($poll_bar['style'], $user_bar_image_path, strlen($user_bar_image_path)) == 0) {
		$poll_bar['style'] = "wp-polls/images/bars/default.gif";
		$poll_bar['background'] = 'd8e1eb';
		$poll_bar['border'] = 'c8c8c8';
		$poll_bar['height'] = 8;
		update_option('poll_bar', $poll_bar);
		return true;
	}
	return false;
}
				
