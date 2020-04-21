<?php



if (!defined( 'ABSPATH' ) )exit('No Such File');



 $proversion	=	wpsp_check_pro_version();



	  $proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';



	  $protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';



	  $prodisable	=	!$proversion['status'] ? 'disabled="disabled"'	: '';



	  $teacherFieldList =  array(	'empcode'			=>	__('Emp. Code', 'WPSchoolPress'),



									'first_name'		=>	__('First Name', 'WPSchoolPress'),	



									'middle_name'		=>	__('Middle Name', 'WPSchoolPress'),	



									'last_name'			=>	__('Last Name', 'WPSchoolPress'),



									'user_email'		=>	__('Teacher Email', 'WPSchoolPress'),



									'zipcode'			=>	__('Zip Code', 'WPSchoolPress'),	



									'country'			=>	__('Country', 'WPSchoolPress'),



									'gender'			=>	__('Gender', 'WPSchoolPress'),



									'address'			=>	__('Address', 'WPSchoolPress'),										



									'dob'				=>	__('Date Of Birth', 'WPSchoolPress'),



									'doj'				=>	__('Date Of Join', 'WPSchoolPress'),	



									'dol'				=>	__('Date Of Releaving', 'WPSchoolPress'),



									'phone'				=>	__('Phone Number', 'WPSchoolPress'),



									'qualification'	    =>	__('Qualification', 'WPSchoolPress'),



									'gender'			=>	__('Gender', 'WPSchoolPress'),



									'bloodgrp'			=>	__('Blood Group', 'WPSchoolPress'),



									'position'			=>	__('Position', 'WPSchoolPress'),



									'whours'			=>	__('Working Hours', 'WPSchoolPress'),



							);



$teacher_table	=	$wpdb->prefix."wpsp_teacher";



$class_table	=	$wpdb->prefix."wpsp_class";



$subjects_table =	$wpdb->prefix."wpsp_subject";



$role			=	sanitize_text_field( $current_user->roles);



$sel_classid	=	isset( $_POST['ClassID'] ) ? intval($_POST['ClassID']) : '';



$sub_handling	=	$cincharge	=	$teacher	=	array();



$classquery		=	$teacherQuery	=	'';



if( !empty( $sel_classid ) && $sel_classid!='all' ){



	$classquery	=	" AND c.cid=$sel_classid ";



}



$sub_han		=	$wpdb->get_results("select sub_name,sub_teach_id,c.c_name from $subjects_table s, $class_table c where sub_teach_id>0 AND c.cid=s.class_id $classquery order by c.cid");



foreach($sub_han as $subhan) {



	$sub_handling[$subhan->sub_teach_id][]=$subhan->sub_name.' ('.$subhan->c_name.')';



	$teacher[]	=	$subhan->sub_teach_id;



}



$incharges=$wpdb->get_results("select c.c_name,c.teacher_id from $class_table c LEFT JOIN $teacher_table t ON t.wp_usr_id=c.teacher_id where c.teacher_id>0 $classquery");



foreach($incharges as $incharge){



	$cincharge[$incharge->teacher_id][]=$incharge->c_name;	



}



if( !empty( $teacher ) && !empty( $sel_classid ) && $sel_classid!='all' ) {



	$teacherQuery	=	' WHERE wp_usr_id IN ('.implode( ", " , $teacher ).") ";



}



$teachers=$wpdb->get_results("select * from $teacher_table WHERE $teacherQuery  first_name != 'teacher' order by tid DESC");



$plugins_url=plugins_url();



?>	



<div class="wpsp-card">



		<div class="wpsp-card-head">                                        



            <div class="subject-inner wpsp-left wpsp-class-filter">



				<form name="TeacherClass" id="TeacherClass" method="post" action="">



					<label class="wpsp-labelMain"><?php _e( 'Select Class Name', 'WPSchoolPress' ); ?></label>



					<select name="ClassID" id="ClassID" class="wpsp-form-control">										



						<option value="all" <?php if($sel_classid=='all') echo "selected"; ?>><?php _e( 'All', 'WPSchoolPress' ); ?></option>



						 <?php



						$class_table	=	$wpdb->prefix."wpsp_class";



						$sel_class		=	$wpdb->get_results("select cid,c_name from $class_table Order By cid ASC");



						foreach( $sel_class as $classes ) {



						?> 



							<option value="<?php echo intval($classes->cid);?>" <?php if($sel_classid==$classes->cid) echo "selected"; ?>><?php echo $classes->c_name;?></option>



						<?php } ?>										 



					</select>



				</form>								



			</div>



			<div class="wpsp-right wpsp-import-export"> 



				<div class="wpsp-btn-lists" <?php echo $prodisable;?> title="<?php echo $protitle;?>">

					<?php if ( $current_user_role=='teacher') {?>



					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>



						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left"> 



							<button type="button" class="wpsp-btn wpsp-btn-success print" id="PrintTeacher" data-toggle="dropdown" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">



								<i class="fa fa-print"></i> <?php _e( 'Print', 'WPSchoolPress'); ?>



							</button>



							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo $prodisable;?>  title="<?php //echo $protitle;?>">



								<!-- <span class="sr-only"><?php _e( 'Toggle Dropdown', 'WPSchoolPress' );?></span> -->



							</button>



							<div class="wpsp-dropdown wpsp-dropdown-md">



							<ul>



								<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Print', 'WPSchoolPress' );?> </li>



								<form id="TeacherColumnForm" name="TeacherColumnForm" method="POST">



									<?php foreach( $teacherFieldList as $key=>$value ) { ?>



										<li class="wpsp-checkList" >



											



											<label class="wpsp-label" for="<?php echo "print".$key; ?>"><input type="checkbox" name="TeacherColumn[]" value="<?php echo $key; ?>" id="<?php echo "print".$key; ?>" checked="checked"><?php echo $value; ?></label>



										</li>



									<?php } ?>



									



									<input type="hidden" name="classid" id="classid" value="<?php echo $sel_classid;?>">



								</form>



							</ul>



						</div>											



						</div>



					</div>



					<?php }?> 	



					<?php if ( $current_user_role=='administrator') {?>



					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>



						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left"> 



							<button type="button" class="wpsp-btn wpsp-btn-success print" id="PrintTeacher" data-toggle="dropdown" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">



								<i class="fa fa-print"></i> <?php _e( 'Print', 'WPSchoolPress'); ?>



							</button>



							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo $prodisable;?>  title="<?php //echo $protitle;?>">



								<!-- <span class="sr-only"><?php _e( 'Toggle Dropdown', 'WPSchoolPress' );?></span> -->



							</button>



							<div class="wpsp-dropdown wpsp-dropdown-md">



							<ul>



								<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Print', 'WPSchoolPress' );?> </li>



								<form id="TeacherColumnForm" name="TeacherColumnForm" method="POST">



									<?php foreach( $teacherFieldList as $key=>$value ) { ?>



										<li class="wpsp-checkList" >



											



											<label class="wpsp-label" for="<?php echo "print".$key; ?>"><input type="checkbox" name="TeacherColumn[]" value="<?php echo $key; ?>" id="<?php echo "print".$key; ?>" checked="checked"><?php echo $value; ?></label>



										</li>



									<?php } ?>



									



									<input type="hidden" name="classid" id="classid" value="<?php echo $sel_classid;?>">



								</form>



							</ul>



						</div>											



						</div>



					</div>



					<?php }?> 		



					<?php if($current_user_role=='administrator') { ?>



						<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>



							<button id="ImportTeacher" class="wpsp-btn wpsp-dark-btn impt wpsp-popclick"  <?php echo $prodisable;?>  title="<?php //echo $protitle;?>"  data-pop="ImportModal">



								<i class="fa fa-upload"></i> Import 



							</button>							



						</div>



						<?php } ?>



					<?php if ( $current_user_role=='administrator') {?>	



					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>



						<div class="wpsp-dropdownmain wpsp-button-group">



							<button type="button" class="wpsp-btn  print" id="ExportTeachers" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">



								<i class="fa fa-download"></i> <?php _e( 'Export', 'WPSchoolPress'); ?>



							</button>



							<button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">



								<!-- <span class="caret"></span>



								<span class="sr-only"><?php _e( 'Toggle Dropdown', 'WPSchoolPress' );?></span> -->



							</button>	



							 <div id="exportcontainer" style="display:none;"></div>									



							<div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">



								<ul >



									<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Export', 'WPSchoolPress' );?> </li>



									<form id="ExportColumnForm" name="ExportTeacherColumn" method="POST">



										<?php foreach( $teacherFieldList as $key=>$value ) { ?>



										<li class="wpsp-checkList">



											



											<label class="wpsp-label" for="<?php echo $key; ?>"> <input type="checkbox" name="TeacherColumn[]" value="<?php echo $key; ?>" id="<?php echo "export".$key; ?>" checked="checked"> <?php echo $value; ?></label>



										</li>



										<?php } ?>	



										<?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?>



										<input type="hidden" name="exportteacher" value="exportteacher">



										<input type="hidden" name="classid" id="export-classid" value="<?php echo $currentSelectClass;?>">



									</form>



								</ul>



							</div>



						</div>



					</div>



<?php } ?>



				</div>



			



			</div>



        </div>



	    <div class="wpsp-card-body">



	    	<?php if($current_user_role=='administrator') { ?>



	    		<div class="wpsp-bulkaction">



					<select name="bulkaction" class="wpsp-form-control" id="bulkaction">



					<option value="">Select Action</option>



					<option value="bulkUsersDelete" id="d_teacher">Delete</option>



					</select>



				</div>				



			<?php } ?>	



			<table id="teacher_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">



				<thead>



					<tr>								



						<th class="nosort">



							<?php if($current_user_role=='administrator') { ?>



							<input type="checkbox" id="selectall" name="selectall" class="ccheckbox">



							<?php  } else if(  $current_user_role=='teacher' ) { ?>



								Sr. No.



							<?php } ?>



						</th>		



						<th> <?php _e( 'Employee Code', 'WPSchoolPress' );?></th>						



						<th> <?php _e( 'Name', 'WPSchoolPress' );?> </th>



						<th> <?php _e( 'Incharge Class', 'WPSchoolPress' );?></th>



						<th> <?php _e( 'Subjects Handling', 'WPSchoolPress' );?></th>								



						<th> <?php _e( 'Phone', 'WPSchoolPress' );?></th>



						<th align="center" class="nosort"><?php _e( 'Action', 'WPSchoolPress' );?></th>



					</tr>



				</thead>



				<tbody>



					<?php							



						foreach($teachers as $key=>$tinfo) { ?>



							<tr>									



								<td>



									<?php if($current_user_role=='administrator') { ?>



									<input type="checkbox" class="ccheckbox tcrowselect" name="UID[]" value="<?php echo $tinfo->wp_usr_id;?>">



									<?php } else if(  $current_user_role=='teacher' ) { echo $key+1; } ?>	



								</td>



								<td><?php echo $tinfo->empcode; ?></td>



								<td><?php echo $tinfo->first_name." ".$tinfo->last_name;?></td>



								<td><?php if( isset( $cincharge[$tinfo->wp_usr_id] ) ) { echo implode( ", ", $cincharge[$tinfo->wp_usr_id] ); } else { echo '-';} ?></td>



								<td><?php if( isset( $sub_handling[$tinfo->wp_usr_id] ) ) { echo implode( "<br> ", $sub_handling[$tinfo->wp_usr_id] ); } else { echo '-';} ?></td>									



								<td><?php echo $tinfo->phone; ?></td>



								<td align="center">



									<div class="wpsp-action-col">



									<a href="<?php echo "?id=".$tinfo->wp_usr_id;?>javascript:;" class="wpsp-popclick ViewTeacher"  data-id="<?php echo $tinfo->wp_usr_id;?>" data-pop="ViewModal"><i class="icon dashicons dashicons-visibility wpsp-view-icon"></i></a> 



									<?php if($current_user_role=='administrator') { ?>



										<a href="<?php echo wpsp_admin_url();?>sch-teacher&id=<?php echo $tinfo->wp_usr_id."&edit=true";?>" title="Edit">



											<i class="icon dashicons dashicons-edit wpsp-edit-icon"></i>



											</a>



										<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo $tinfo->tid;?>" >



											<i class="icon dashicons dashicons-trash wpsp-delete-icon" data-id="<?php echo $tinfo->tid;?>"></i>	                                </a>



									<?php } ?>	



								</div>



								</td>



							</tr>



					<?php } ?>



				</tbody>



				<tfoot>



					<tr>



						<th><?php if(  $current_user_role=='teacher' ) { ?>



								Sr. No.



						<?php } ?>



						</th>



						<th> <?php _e( 'Employee Code', 'WPSchoolPress' );?></th>							



						<th><?php _e( 'Name', 'WPSchoolPress' );?> </th>



						<th> <?php _e( 'Incharge Class', 'WPSchoolPress' );?></th>



						<th> <?php _e( 'Subjects Handling', 'WPSchoolPress' );?></th>								



						<th> <?php _e( 'Phone', 'WPSchoolPress' );?></th>



						<th  align="center">Action</th>



					</tr>



				</tfoot>



		 	</table>



		</div>



	</div><!-- /.box-body -->				 