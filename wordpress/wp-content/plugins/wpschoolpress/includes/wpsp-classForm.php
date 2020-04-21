<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');

$c_fee_type = '';
$ctable=$wpdb->prefix."wpsp_class";
$teacher_table=	$wpdb->prefix."wpsp_teacher";
$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name from $teacher_table order by tid");
$classname	= $classnumber	= $classcapacity = $classlocation = $classstartingdate = $classendingdate= $teacherid = '';
if( isset( $_GET['id']) ) {
	$classid =	intval($_GET['id']);
	$wpsp_classes =$wpdb->get_results("select * from $ctable where cid='$classid'");

	foreach ($wpsp_classes as $wpsp_editclass) {
		$classname=$wpsp_editclass->c_name;
		$classnumber=$wpsp_editclass->c_numb;
		$classcapacity=$wpsp_editclass->c_capacity;
		$classlocation=$wpsp_editclass->c_loc;
		$classstartingdate1=$wpsp_editclass->c_sdate;

		$classstartingdate = date("m/d/Y", strtotime($classstartingdate1));
		$classendingdate1=$wpsp_editclass->c_edate;
		$classendingdate = date("m/d/Y", strtotime($classendingdate1));
		$teacherid=$wpsp_editclass->teacher_id;
		if($wpsp_editclass->c_fee_type != ''){
			$c_fee_type =$wpsp_editclass->c_fee_type  ;
		}
	}
}
$label			=	isset( $_GET['id'] ) ? apply_filters( 'wpsp_class_main_heading_update', esc_html__( 'Update Class Information', 'WPSchoolPress' )) : apply_filters( 'wpsp_class_main_heading_add', esc_html__( 'Add Class Information', 'WPSchoolPress' ));
$formname		=	isset( $_GET['id'] ) ? 'ClassEditForm' : 'ClassAddForm';
$buttonname	=	isset( $_GET['id'] ) ? 'Update' : 'Submit';
$propayment = wpsp_check_pro_version('pay_WooCommerce');
$propayment = !$propayment['status'] ? 'notinstalled'    : 'installed';
?>
<!-- This form is used for Add/Update Class -->
<div id="formresponse"></div>
<form name="<?php echo $formname;?>" id="<?php echo $formname; ?>" method="post">
	<?php if( isset( $_GET['id']) ) { ?>
		<input type="hidden" name="cid" value="<?php echo $classid;?>">
	<?php } ?>
	<div class="wpsp-row">
	<div class="wpsp-col-xs-12">
		<div class="wpsp-card">
			<div class="wpsp-card-head">
				<h3 class="wpsp-card-title"><?php echo $label; ?></h3>
			</div>
			<div class="wpsp-card-body">
				 <?php wp_nonce_field( 'ClassAction', 'caction_nonce', '', true ) ?>
				<div class="wpsp-row">
					<?php  do_action('wpsp_before_class_detail_fields');
					  $is_required_item = apply_filters('wpsp_class_is_required',array());
						$item =  apply_filters( 'wpsp_class_title_item',esc_html("Class Name","WPSchoolPress"));
					?>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group ">
							<label class="wpsp-label" for="Name"><?php
								$pl = "";
								if(isset($item['Name'])){
											echo $pl = esc_html($item['Name'],"WPSchoolPress");
								}else{
										echo $pl = esc_html("Class Name","WPSchoolPress");
								}
								/*Check Required Field*/
								if(isset($is_required_item['Name'])){
										$is_required =  $is_required_item['Name'];
								}else{
										$is_required = true;
								}
								?>
							<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
							</label>
							<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control"  name="Name" placeholder="<?php echo $pl; ?>" value="<?php echo $classname; ?>">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
					   <div class="wpsp-form-group">
							<label class="wpsp-label" for="Number"><?php
								$pl = "";
								if(isset($item['Number'])){
											echo $pl = esc_html($item['Number'],"WPSchoolPress");
								}else{
										echo $pl = esc_html("Class Number","WPSchoolPress");
								}
								/*Check Required Field*/
								if(isset($is_required_item['Number'])){
										$is_required =  $is_required_item['Number'];
								}else{
										$is_required = true;
								}
								?>
							<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<input data-is_required="<?php echo $is_required; ?>" type="text" class="wpsp-form-control"  name="Number" placeholder="<?php echo $pl; ?>" value="<?php echo $classnumber; ?>">
							<input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Capacity"><?php
								$pl = "";
								if(isset($item['capacity'])){
											echo $pl = esc_html($item['capacity'],"WPSchoolPress");
								}else{
										echo $pl = esc_html("Class Capacity","WPSchoolPress");
								}
								/*Check Required Field*/
								if(isset($is_required_item['capacity'])){
										$is_required =  $is_required_item['capacity'];
								}else{
										$is_required = true;
								}
								?>		<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo $is_required; ?>" pattern="[0-9]*" class="wpsp-form-control numbers"  name="capacity" placeholder="<?php echo $pl; ?>" id="c_capacity" value="<?php echo $classcapacity; ?>" min="0">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
						   <label class="wpsp-label" for="Selectteacher"><?php
 								$pl = "";
 								if(isset($item['ClassTeacherID'])){
 											echo $pl = esc_html($item['ClassTeacherID'],"WPSchoolPress");
 								}else{
 										echo $pl = esc_html("Class Teacher","WPSchoolPress")."<span> (Incharge)</span>";
 								}
 								/*Check Required Field*/
 								if(isset($is_required_item['ClassTeacherID'])){
 										$is_required =  $is_required_item['ClassTeacherID'];
 								}else{
 										$is_required = false;
 								}
 								?>
								<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<select data-is_required="<?php echo $is_required; ?>" name="ClassTeacherID" class="wpsp-form-control">
								<option value="">Select Teacher </option>
								<?php
								if(!empty($teacher_data)){
								foreach ($teacher_data as $teacher_list) {
									$teacherlistid= $teacher_list->wp_usr_id;
									?>
								<option value="<?php echo $teacherlistid;?>" <?php if($teacherlistid == $teacherid) echo "selected"; ?> ><?php echo $teacher_list->full_name;?></option>
								<?php }
								}?>
							</select>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Starting"><?php
							 $pl = "";
							 if(isset($item['Sdate'])){
										 echo $pl = esc_html($item['Sdate'],"WPSchoolPress");
							 }else{
									 echo $pl = esc_html("Class Starting on","WPSchoolPress");
							 }
							 /*Check Required Field*/
							 if(isset($is_required_item['Sdate'])){
									 $is_required =  $is_required_item['Sdate'];
							 }else{
									 $is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control select_date wpsp-start-date" name="Sdate" placeholder="<?php echo $pl; ?>" value="<?php echo $classstartingdate; ?>" readonly>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Ending"><?php
							 $pl = "";
							 if(isset($item['Edate'])){
										 echo $pl = esc_html($item['Edate'],"WPSchoolPress");
							 }else{
									 echo $pl = esc_html("Class Ending on","WPSchoolPress");
							 }
							 /*Check Required Field*/
							 if(isset($is_required_item['Edate'])){
									 $is_required =  $is_required_item['Edate'];
							 }else{
									 $is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control select_date wpsp-end-date" name="Edate" placeholder="<?php echo $pl; ?>" value="<?php echo $classendingdate; ?>" readonly>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
								<label class="wpsp-label" for="Location"><?php
								 $pl = "";
								 if(isset($item['Location'])){
											 echo $pl = esc_html($item['Location'],"WPSchoolPress");
								 }else{
										 echo $pl = esc_html("Class Location","WPSchoolPress");
								 }
								 /*Check Required Field*/
								 if(isset($is_required_item['Location'])){
										 $is_required =  $is_required_item['Location'];
								 }else{
										 $is_required = false;
								 }
								 ?>
								 <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
								<input type="text" class="wpsp-form-control" data-is_required="<?php echo $is_required; ?>" name="Location" placeholder="<?php echo $pl; ?>" value="<?php echo $classlocation; ?>">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
					<div class="wpsp-form-group">
							<label class="wpsp-label" for="Location"><?php
							 $pl = "";
							 if(isset($item['classfeetype'])){
										 echo $pl = esc_html($item['classfeetype'],"WPSchoolPress");
							 }else{
									 echo $pl = esc_html("Class Fee Type","WPSchoolPress");
							 }
							 /*Check Required Field*/
							 if(isset($is_required_item['classfeetype'])){
									 $is_required =  $is_required_item['classfeetype'];
							 }else{
									 $is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
							<select data-is_required="<?php echo $is_required; ?>" name="classfeetype" class="wpsp-form-control">
								<option value="" selected disabled>Select Class Fee Type </option>
								 <?php if($propayment == "installed"){
								 	echo $c_fee_type;?>
								<option value="paid" <?php if($c_fee_type == "paid") echo "selected"; ?>>Paid</option>
							<?php } ?>
								<option value="free" <?php if($c_fee_type == "free") echo "selected"; ?>>Free</option>
              </select>
							<!-- <input type="text" class="wpsp-form-control" name="Location" placeholder="Class Location" value="<?php echo $classlocation; ?>"> -->
						</div>
					</div>
					<?php  do_action('wpsp_after_class_detail_fields'); ?>
					<div class="wpsp-col-xs-12 wpsp-btnsubmit-section">
						<button type="submit" class="wpsp-btn wpsp-btn-success" id="c_submit"><?php echo $buttonname; ?></button>
						<a href="<?php echo wpsp_admin_url();?>sch-class" class="wpsp-btn wpsp-dark-btn" >Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<!-- End of Add/Update New Class Form -->
