<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb,$wpsp_settings_data;
		$notify_table	=	$wpdb->prefix . "wpsp_notification";
		$status = $ins = 0;
		$current_user_role=$current_user->roles[0];
	if($current_user_role=='teacher') {
		$receiverTypeList = array( 'all'  => __( 'All Users', 'WPSchoolPress' ),
			 						'allt' => __( 'All Teachers', 'WPSchoolPress' ),
								'alls' => __( 'All Students', 'WPSchoolPress'),
							    'allp' => __( 'All Parents', 'WPSchoolPress')
							   );
} else {
	$receiverTypeList = array( 'all'  => __( 'All Users', 'WPSchoolPress' ),
								'alls' => __( 'All Students', 'WPSchoolPress'),
							    'allp' => __( 'All Parents', 'WPSchoolPress'),
							    'allt' => __( 'All Teachers', 'WPSchoolPress' ) );
}
		$notifyTypeList	=	array( 0 	=>	__( 'All', 'WPSchoolPress') ,
							   1 	=>	__( 'Email', 'WPSchoolPress'),
							   2	=>	__( 'SMS', 'WPSchoolPress'),
							   3	=> 	__( 'Web Notification', 'WPSchoolPress'),
							   4	=>	__( 'Push Notification (Android & IOS)', 'WPSchoolPress') );
		//to send notifications
			$student_table	=	$wpdb->prefix.'wpsp_student';
					$teacher_table	=	$wpdb->prefix.'wpsp_teacher';
					$users_table	=	$wpdb->prefix.'users ';
					$whereQuery1	= 'where ut.ID = st.parent_wp_usr_id AND ut.user_email!=""';
$whereQuery	= 'where ut.ID = st.wp_usr_id AND ut.user_email!=""';
$student_ids1	=	$wpdb->get_results( "select * from $student_table st, $users_table ut $whereQuery",ARRAY_A );
$teacher_ids1	=	$wpdb->get_results( "select * from $teacher_table st, $users_table ut $whereQuery", ARRAY_A );
$parent_ids1	=	$wpdb->get_results( "select * from $student_table st, $users_table ut $whereQuery1", ARRAY_A );
$usersList1	=	array_merge( $student_ids1,$teacher_ids1);
		if( isset( $_POST['notifySubmit']) && sanitize_text_field($_POST['notifySubmit']) == 'Notify' ) {
			if(isset( $_POST['type'] )  &&
				isset( $_POST['subject'])  && !empty( sanitize_text_field($_POST['subject']) ) && isset( $_POST['description'] ) && !empty( sanitize_text_field($_POST['description']) ) ) {
					$student_table	=	$wpdb->prefix.'wpsp_student';
					$parents_table	=	$wpdb->prefix.'wpsp_parent';
					$teacher_table	=	$wpdb->prefix.'wpsp_teacher';
					$users_table	=	$wpdb->prefix.'users ';
					 $receiverType	=	$_POST['receiver'];
					$notifyType		=	intval($_POST['type']);
					$subject 		=	sanitize_text_field($_POST['subject']);
					$description 	=	sanitize_text_field($_POST['description']);
					$usersList		=	$student_ids	=	$parent_ids	=	$teacher_ids	=	array();
					$whereQuery	= 'where ut.ID = st.wp_usr_id';
					 $whereQuery1    = 'where ut.ID = st.wp_usr_id';
					if ( $notifyType ==1 || $notifyType ==0 ) {
						$whereQuery	.=	' AND ut.user_email!=""';
					}
					if ( $notifyType ==2 || $notifyType ==0 ) {
					$whereQuery	.=	' AND st.s_phone!=""';
					}
					if ( $notifyType ==2 || $notifyType ==0 ) {
					$whereQuery1	.=	' AND st.phone!=""';
					}
					if ( $notifyType ==1 || $notifyType ==0 ) {
						$whereQuery1	.=	' AND ut.user_email!=""';
					}
					foreach($receiverType as $receivers)
					{
					if( $receivers  == 'alls' || $receivers == 'all')	{
						$student_ids	=	$wpdb->get_results( "select * from $student_table st, $users_table ut $whereQuery",ARRAY_A );
					}
					else if( $receivers == 'allp' || $receivers == 'all' ) {
						$parent_ids		=	$wpdb->get_results( "select * from $student_table st ,$users_table ut where ut.ID=st.parent_wp_usr_id AND ut.user_email!=''", ARRAY_A );
					}
					else if( $receivers == 'allt' || $receivers == 'all' ) {
						$teacher_ids	=	$wpdb->get_results( "select * from $teacher_table st, $users_table ut $whereQuery", ARRAY_A );
					}
					else {
						 	$sqlvar =  'select * from '.$users_table.' where ID = '.$receivers.' AND user_email!=""';
						$student_ids	=	$wpdb->get_results( $sqlvar,ARRAY_A );
					}
					}
					$usersList	=	array_merge( $student_ids,$parent_ids,$teacher_ids );
					if ( $notifyType ==1 || $notifyType ==0 ) { //If notification is mail/All
						$wpsp_settings_table=$wpdb->prefix."wpsp_settings";
						$wpsp_settings_edit=$wpdb->get_results( "SELECT * FROM $wpsp_settings_table" );
						foreach($wpsp_settings_edit as $sdat) {
							$settings_data[$sdat->option_name]=$sdat->option_value;
						}
						add_filter( 'wp_mail_from', 'wpsp_new_mail_from' );
						add_filter( 'wp_mail_from_name', 'wpsp_new_mail_from_name' );
						function wpsp_new_mail_from($old) {
						   global $settings_data;
						  return isset( $settings_data['sch_email'] ) && !empty($settings_data['sch_email']) ? $settings_data['sch_email'] : $old;
						}
						function wpsp_new_mail_from_name($old) {
							global $settings_data;
							return isset( $settings_data['sch_name'] ) && !empty( $settings_data['sch_name'] ) ? $settings_data['sch_name'] : $old;
						}
						$body = nl2br( $description );
						$headers = array('Content-Type: text/html; charset=UTF-8');
						foreach( $usersList as $key =>$value ) {
							$to = $value['user_email'];
							if( !empty( $to ) ) {
								if( wpsp_send_mail( $to, $subject, $body ) ) $status = 1;
							}
						}
					}
					if( isset( $wpsp_settings_data['notification_sms_alert'] ) && $wpsp_settings_data['notification_sms_alert'] == 1 ) { //if notification enable from setting page
						if ( $notifyType ==2 || $notifyType ==0 ) { //If notification is sms/All
							foreach( $usersList as $key =>$value ) {
								$to = $value['s_phone'];
								if( !empty( $to ) ) {
								if($wpsp_settings_data['sch_sms_slaneuser']!= ""){
                                    $notify_msg_response = apply_filters('wpsp_send_notification_msg', false, $to, $description );
                                    }
                                    else {
                                        $notify_msg_response = apply_filters('wpsp_send_notification_msg_twilio', false, $to, $description );
                                    }
									if( $notify_msg_response ) $status = 1;
								}
							}
						}
					}
					$currentDate	=	wpsp_StoreDate( esc_attr( date('Y-m-d h:i:s') ) );
					$description	=	strlen( $description ) > 255 ? substr( $description, 0, 254 ) : $description;
					//insert into db
					$notify_table_data = array(
											'name' => $subject,
											'description' => $description,
											'receiver' => $receiverType,
											'type' => $notifyType,
											'status' => $status,
											'date'	=> $currentDate
										);
									$ins = $wpdb->insert( $notify_table,$notify_table_data);
				}
		}
		$current_user_role=$current_user->roles[0];
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		$addUrl = add_query_arg( 'ac', 'add', get_permalink());
		if($current_user_role=='administrator' || $current_user_role=='teacher') { 	?>
		<?php
		if($ins) {  ?>
		<div class="wpsp-popupMain wpsp-popVisible" id="SuccessModal" style="display:block;">
			<div class="wpsp-overlayer"></div>
			<div class="wpsp-popBody wpsp-alert-body">
				<div class="wpsp-popInner">
					<a href="javascript:;" class="wpsp-closePopup"></a>
					<div class="wpsp-popup-cont wpsp-alertbox wpsp-alert-success">
						<div class="wpsp-alert-icon-box">
							<i class="icon dashicons dashicons-yes"></i>
						</div>
						<div class="wpsp-alert-data">
							<input type="hidden" name="teacherid" id="teacherid">
							<h4>Success</h4>
							<p>Notification Successfully Send!</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php  } ?>
		<?php if( isset($_GET['ac']) && sanitize_text_field($_GET['ac'])=='add' ) { ?>
		<div class="wpsp-card">
			<div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters('wpsp_add_notify_heading_item',esc_html("Notification :","WPSchoolPress")); ?> </h3>
            </div>
                 <div class="wpsp-card-body">
							<form  method="post" class="form-horizontal" id="NotifyEntryForm">
								<input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
								<div class="wpsp-row">
										<?php  do_action('wpsp_before_notification');
										$item =  apply_filters('wpsp_add_event_popup_title_item',array());
										?>
									<div class="wpsp-col-md-4">
										<div class="wpsp-form-group">
											<?php
												if(isset($item['subject'])){
															$pl = esc_html($item['subject'],"WPSchoolPress");
												}else{
															$pl = esc_html("Name","WPSchoolPress");
												}
											?>
											<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required"> *</span></label>
											<input type="text" name="subject" class="wpsp-form-control">
										</div>
									</div>
									<div class="wpsp-col-md-4">
										<div class="wpsp-form-group">
											<?php
												if(isset($item['receiver'])){
															$pl = esc_html($item['receiver'],"WPSchoolPress");
												}else{
															$pl = esc_html("Receiver","WPSchoolPress");
												}
											?>
											<label class="wpsp-label"><?php echo $pl; ?></label>
											<select id="example-enableClickableOptGroups-disabled" name="receiver[]" multiple="multiple">
										 <option value="all">All Users</option>
										 <option value="alls">All Students</option>
										 <option value="<?php echo 'allp'?>">All Parents</option>
										 <option value="<?php echo 'allt'?>">All Teachers</option>
										 <?php foreach($usersList1 as $usersList1details)
											{?>
												 <option value="<?php echo $usersList1details['wp_usr_id'];?>"><?php echo $usersList1details['s_fname']." ".$usersList1details['s_lname'];
												echo $usersList1details['first_name']." ".$usersList1details['last_name'];?></option>
											<?php } if(!empty($parent_ids1))
											{
												foreach($parent_ids1 as $parent_idsdata)
											{
												?>
												 <option value="<?php echo $parent_idsdata['parent_wp_usr_id'];?>"><?php echo $parent_idsdata['p_fname']." ".$parent_idsdata['p_lname'];
												?></option>
											<?php } }
											?>

                            		</select>
											<!-- <select name="receiver" class="wpsp-form-control">
												<option value=""><?php _e( 'Whom to notify?', 'WPSchoolPress'); ?></option>
												<?php
													foreach( $receiverTypeList as $key => $value ) {
														echo '<option value="'.$key.'">'.$value.'</option>';
													}
												?>
											</select> -->
										</div>
									</div>
									<div class="wpsp-col-md-4">
										<div class="wpsp-form-group">
											<?php
												if(isset($item['type'])){
															$pl = esc_html($item['type'],"WPSchoolPress");
												}else{
															$pl = esc_html("Notify Type","WPSchoolPress");
												}
											?>
											<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required"> *</span></label>
												<?php $proversion = wpsp_check_pro_version('wpsp_sms_version');
													$proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
													$protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';
													$prodisable		=	!$proversion['status'] ? 'disabled="disabled"'	: '';
												?>
												<select name="type" class="wpsp-form-control">
													<option value=""><?php _e( 'How to notify?', 'WPSchoolPress'); ?></option>
													<option value="1"><?php _e( 'Email', 'WPSchoolPress'); ?></option>
													<option value="2" title="<?php echo $protitle; ?>" class="<?php echo $proclass; ?>"
														<?php if( !empty( $prodisable ) ) { ?> disabled <?php  } ?>>
														<?php _e( 'SMS', 'WPSchoolPress'); ?>
													</option>
													<option value="0"><?php _e( 'All', 'WPSchoolPress'); ?></option>
												</select>
												<?php
												if( !isset( $wpsp_settings_data['notification_sms_alert'] ) || ( isset( $wpsp_settings_data['notification_sms_alert'] ) && $wpsp_settings_data['notification_sms_alert'] != 1 ) ) {
													echo '<p style="margin-top:6px;">
													<img src="../wp-content/plugins/wpschoolpress/img/svg/info-icon.svg" width="12" height="12" /> Enable SMS Notification Option from setting page to send SMS</p>';
												}
												?>
											</div>
										</div>
								</div>
								<div class="wpsp-row">
									<div class="wpsp-col-md-12">
										<div class="wpsp-form-group">
											<?php
												if(isset($item['description'])){
															$pl = esc_html($item['description'],"WPSchoolPress");
												}else{
															$pl = esc_html("Description","WPSchoolPress");
												}
											?>
											<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required"> *</span></label>
											<textarea class="wpsp-form-control" name="description" required minlength="15"></textarea>
										</div>
									</div>
								</div>
									<?php  do_action('wpsp_after_notification'); ?>
									<div class="wpsp-form-group">
										<div class="wpsp-row">
											<div class="wpsp-col-md-12">
												<input type="submit" class="wpsp-btn wpsp-btn-success" name="notifySubmit" value="Notify" id="notifySubmit">
												<a href="<?php echo wpsp_admin_url();?>sch-notify" class="wpsp-btn wpsp-dark-btn">Back</a>
											</div>
										</div>
									</div>
							</form>
						</div>
					</div>
			<?php } else { ?>
			<div class="wpsp-card">
                 <div class="wpsp-card-body">
							<table id="notify_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
								<thead>
									<tr>
										<th class="nosort">#</th>
										<th><?php _e( 'Name', 'WPSchoolPress' ); ?></th>
										<th><?php _e( 'Description', 'WPSchoolPress' );?></th>
										<!-- <th><?php _e( 'Receiver', 'WPSchoolPress' ); ?></th> -->
										<th><?php _e( 'Type', 'WPSchoolPress' ); ?></th>
										<th><?php _e( 'Date', 'WPSchoolPress');  ?></th>
										<th class="nosort" align="center"><?php _e( 'Action', 'WPSchoolPress'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										//Last added will me shown first
										$notifyInfo = $wpdb->get_results("Select * from $notify_table order by nid desc");
										foreach( $notifyInfo as $key=>$value ) {
											$receiver	=	isset( $receiverTypeList[$value->receiver] ) ? $receiverTypeList[$value->receiver] : $value->receiver;
											$type		=	isset( $notifyTypeList[$value->type] ) ? $notifyTypeList[$value->type] : $value->type;
												echo '<tr>
													<td>'.($key+1).'</td>
													<td>'.$value->name.'</td>
													<td>'.substr( $value->description, 0, 20).'</td>
													<td>'.$type.'</td>
													<td>'.wpsp_ViewDate( $value->date ).'</td>
													<td align="center">
														<div class="wpsp-action-col">
														<a href="javascript:;" class="wpsp-popclick notify-view"  data-id="'.intval($value->nid).'"  data-pop="ViewModal"><i class="icon wpsp-view wpsp-view-icon"></i></a>
															<a href="javascript:;" class="wpsp-popclick notify-Delete"  data-id="'.intval($value->nid).'" >
															<i class="icon wpsp-trash wpsp-delete-icon notify-Delete" ></i>
															</a>
														</div>
													</td>
												</tr>';
										}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th class="nosort">#</th>
										<th><?php _e( 'Name', 'WPSchoolPress' ); ?></th>
										<th><?php _e( 'Description', 'WPSchoolPress' );?></th>
										<!-- <th><?php _e( 'Receiver', 'WPSchoolPress' ); ?></th> -->
										<th><?php _e( 'Type', 'WPSchoolPress' ); ?></th>
										<th><?php _e( 'Date', 'WPSchoolPress');  ?></th>
										<th class="nosort"><?php _e( 'Action', 'WPSchoolPress'); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
		<div class="wpsp-popupMain" id="ViewModal">
			<div class="wpsp-overlayer"></div>
			<div class="wpsp-popBody">
				<div class="wpsp-popInner">
					<a href="javascript:;" class="wpsp-closePopup"></a>
					<div id="ViewModalContent" class="wpsp-text-left"></div>
				</div>
			</div>
		</div>
		<?php }
		}
		else if($current_user_role=='parent' || $current_user_role='student')
		{
		}
		wpsp_body_end();
		wpsp_footer();
	}
	else{
		include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
	}
?>
