<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header(); ?>
<?php
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		if($current_user_role=='administrator' || $current_user_role=='teacher')
		{
		?>
		<div class="wpsp-card">
		<div class="wpsp-card-head">
            <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_event_heading_item',esc_html("Event calendar","WPSchoolPress")); ?></h3>
        </div>
         <div class="wpsp-card-body">
			<div id="calendar"></div>
		<div class="wpsp-popupMain" id="eventPop">
		  <div class="wpsp-overlayer"></div>
		  <div class="wpsp-popBody">
		    <div class="wpsp-popInner">
		    	<a href="javascript:;" class="wpsp-closePopup"></a>
		    	<div class="wpsp-popup-body">
		    		<div class="wpsp-panel-heading">
						<h3 class="wpsp-panel-title"><?php echo apply_filters( 'wpsp_add_event_popup_heading_item',esc_html("Add Event","WPSchoolPress")); ?></h3>
					</div>
		    		<div class="wpsp-popup-cont">
		    		<div id="response"></div>
							<?php
								$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
								if(isset($item['sdate'])){
										$pl = esc_html($item['sdate'],"WPSchoolPress");
								}else{
											$pl = esc_html("Start Date","WPSchoolPress");
								}
							?>
						<form name="calevent_entry" method="post" class="form-horizontal" id="calevent_entry">
								<?php  do_action('wpsp_before_event'); ?>
							<div class="wpsp-col-sm-6 wpsp-col-xs-12">
								<div class="wpsp-form-group">
									<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
									<input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
									<input type="text" name="sdate" class="wpsp-form-control sdate" id="sdate">
								</div>
							</div>
							<div class="wpsp-col-sm-6">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['stime'])){
												$pl = esc_html($item['stime'],"WPSchoolPress");
										}else{
													$pl = esc_html("Start Time","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?> <span class="wpsp-required">*</span></label>
									<input type="text" name="stime" class="wpsp-form-control stime" id="stime">
								</div>
							</div>
							<div class="wpsp-col-sm-6">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['stime'])){
													$pl = esc_html($item['stime'],"WPSchoolPress");
										}else{
													$pl = esc_html("Start Time","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
									<input type="text" name="edate" class="wpsp-form-control edate" id="edate">
								</div>
							</div>

							<div class="wpsp-col-sm-6">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['etime'])){
													$pl = esc_html($item['etime'],"WPSchoolPress");
										}else{
													$pl = esc_html("End Time","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
									<input type="text" name="etime" class="wpsp-form-control etime" id="etime">
								</div>
							</div>
							<div class="wpsp-col-sm-12">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['evtitle'])){
													$pl = esc_html($item['evtitle'],"WPSchoolPress");
										}else{
													$pl = esc_html("Title","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?> *</label>
									<input type="text" name="evtitle" class="wpsp-form-control" id="evtitle">
								</div>
							</div>
							<div class="wpsp-col-sm-12">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['evdesc'])){
													$pl = esc_html($item['evdesc'],"WPSchoolPress");
										}else{
													$pl = esc_html("Description","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?></label>
									<textarea name="evdesc" class="wpsp-form-control" id="evdesc"></textarea>
								</div>
							</div>
							<div class="wpsp-col-sm-6">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['evtype'])){
													$pl = esc_html($item['evtype'],"WPSchoolPress");
										}else{
													$pl = esc_html("Type","WPSchoolPress");
										}
									?>
								<label class="wpsp-label"><?php echo $pl; ?></label>
									<select class="wpsp-form-control" id="evtype" name="evtype">
										<option value="0">External(Show to all)</option>
										<option value="1">Internal(Show to teachers only)</option>
									</select>
									<input type="hidden" name="evid" class="wpsp-form-control" id="evid">
								</div>
							</div>
							<div class="wpsp-col-sm-6">
								<div class="wpsp-form-group">
									<?php
										$item =  apply_filters('wpsp_add_event_popup_title_item',esc_html("Add Event","WPSchoolPress"));
										if(isset($item['evcolor'])){
													$pl = esc_html($item['evcolor'],"WPSchoolPress");
										}else{
													$pl = esc_html("Color","WPSchoolPress");
										}
									?>
									<label class="wpsp-label"><?php echo $pl; ?></label>
									<select name="evcolor" class="wpsp-form-control" id="evcolor">
										<option class="bg-blue" value="">Default</option>

									</select>
								</div>
							</div>
							<?php  do_action('wpsp_after_event'); ?>
						</form>
						<div class="wpsp-col-sm-12">
							<?php
								$item =  apply_filters('wpsp_add_event_popup_button_submit_text',esc_html("Save","WPSchoolPress"));
								if(isset($item['calevent_save'])){
											$pl = esc_html($item['calevent_save'],"WPSchoolPress");
								}else{
											$pl = esc_html("Save","WPSchoolPress");
								}
							?>
							<button type="button" id="calevent_save" class="wpsp-btn wpsp-btn-success"><?php echo $pl; ?></button>
						</div>
						</div>
					</div>
					</div>
				</div>
		    </div>
		     <!-- popup -->
		    <div class="wpsp-popupMain" id="editeventPop">
			  <div class="wpsp-overlayer"></div>
			  <div class="wpsp-popBody">
			    <div class="wpsp-popInner">
			    		<a href="javascript:;" class="wpsp-closePopup"></a>
						<div class="wpsp-popup-body">
			    		<div class="wpsp-panel-heading">
							<h3 class="wpsp-panel-title" id="viewEventTitle"></h3>
						</div>
			    		<div class="wpsp-popup-cont">
			    		<div class="wpsp-col-md-6">
			    			<div class="wpsp-form-group">
									<?php
										$pl =  apply_filters('wpsp_add_event_popup_start_label',esc_html("Start :","WPSchoolPress"));
									?>
								<label class="wpsp-labelMain"><?php echo $pl; ?></label> <span id="eventStart"> </span>
							</div>
						</div>
						<div class="wpsp-col-md-6">
							<div class="wpsp-form-group">
								<?php
									$pl =  apply_filters('wpsp_add_event_popup_end_label',esc_html("End :","WPSchoolPress"));
								?>
								<label class="wpsp-labelMain"><?php echo $pl; ?></label> <span id="eventEnd"> </span>
							</div>
						</div>
						<div class="wpsp-col-md-12">
							<div class="wpsp-form-group">
							<label><?php echo apply_filters('wpsp_add_event_popup_description_label',esc_html("Description :","WPSchoolPress")); ?> </label> <span id="eventDesc"> </span>
							</div>
						</div>
						<?php if($current_user_role=='administrator'){?>
						<div class="wpsp-col-md-12">
							<button class="wpsp-btn wpsp-btn-success" id="editEvent"><?php echo apply_filters('wpsp_add_event_popup_button_edit_text',esc_html("Edit Event","WPSchoolPress")); ?></button>
							<button class="wpsp-btn wpsp-btn-danger" id="deleteEvent"><?php echo apply_filters('wpsp_add_event_popup_button_delete_text',esc_html("Delete","WPSchoolPress")); ?></button>
						</div>
					<?php }?>
			    	</div>
			    </div>
			</div>
			</div>
		</div>
		     <!-- popup-end -->
		  </div>
		</div>

		<?php  }else if($current_user_role=='parent' || $current_user_role='student'){ ?>
		<div class="wpsp-card">
			<div class="wpsp-card-head">
        		<h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_event_heading_item',esc_html("Event calendar","WPSchoolPress")); ?></h3>
    		</div>
			<div class="wpsp-card-body">
				<div id="calendar"></div>
		<div class="wpsp-popupMain" id="editeventPop">
		  <div class="wpsp-overlayer"></div>
		  <div class="wpsp-popBody">
		    <div class="wpsp-popInner">
		    		<a href="javascript:;" class="wpsp-closePopup"></a>
		    		<div class="wpsp-popBody">
		    		<div class="wpsp-panel-heading">
						<h3 class="wpsp-panel-title" id="viewEventTitle"></h3>
					</div>
		    			<div class="wpsp-popup-cont">
		    			<div class="col-md-6">
		    				<div class="wpsp-form-group">
									<?php
										$pl =  apply_filters('wpsp_add_event_popup_start_label',esc_html("Start :","WPSchoolPress"));
									?>
								<label class="wpsp-labelMain"><?php echo $pl; ?></label> <span id="eventStart"> </span>
							</div>
						</div>
							<div class="col-md-6">
								<div class="wpsp-form-group">
									<?php
										$pl =  apply_filters('wpsp_add_event_popup_end_label',esc_html("End :","WPSchoolPress"));
									?>
									<label class="wpsp-labelMain"><?php echo $pl; ?></label> <span id="eventEnd"> </span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wpsp-form-group">
									<label>	<label><?php echo apply_filters('wpsp_add_event_popup_description_label',esc_html("Description :","WPSchoolPress")); ?> </label> <span id="eventDesc"> </span></label> <span id="eventDesc"> </span>
								</div>
							</div>
		    	</div>
		    </div>
		</div>
		</div>
		</div>
		</div>
		</div>
	<?php }
		wpsp_body_end();
		wpsp_footer();
	}
	else{
		//Include Login Section
		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
?>
