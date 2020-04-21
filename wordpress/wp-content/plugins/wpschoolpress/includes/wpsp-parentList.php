<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
	  $proversion	=	wpsp_check_pro_version();
	  $proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
	  $protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';
	  $prodisable	=	!$proversion['status'] ? 'disabled="disabled"'	: '';
	  $parentFieldList =  array(	'p_fname'		=>	__('First Name', 'WPSchoolPress'),	
									'p_mname'		=>	__('Middle Name', 'WPSchoolPress'),	
									'p_lname'		=>	__('Last Name', 'WPSchoolPress'),
									's_fname'		=>	__('Student Name', 'WPSchoolPress'),
									'user_email'	=>	__('Parent Email ID', 'WPSchoolPress'),
									'p_edu'			=>	__('Education', 'WPSchoolPress'),									
									'p_gender'		=>	__('Gender', 'WPSchoolPress'),
									'p_profession'	=>	__('Profession', 'WPSchoolPress'),
									'p_bloodgrp'	=>	__('Blood Group', 'WPSchoolPress'),
							);
		$sel_classid	=	isset( $_POST['ClassID'] ) ? sanitize_text_field($_POST['ClassID']) : '';										
		$class_table	=	$wpdb->prefix."wpsp_class";
		$classQuery		=	"select cid,c_name from $class_table Order By cid ASC";
		// if( $current_user_role=='teacher' ) {
		// 	$cuserId	=	intval($current_user->ID);
		// 	$classQuery	=	"select cid,c_name from $class_table where teacher_id=$cuserId";
		// 	$msg		=	'Please Ask Principal To Assign Class';
		// }
		$sel_class		=	$wpdb->get_results( $classQuery );
		global $current_user;
	$role		=	 $current_user->roles;
	$cuserId	=	 $current_user->ID;
?>
<!-- This file form is used for ParentList -->
<div class="wpsp-card">
	<div class="wpsp-card-head">   
		<div class="subject-inner wpsp-left wpsp-class-filter">
			<form name="ClassForm" id="ClassForm" method="post" action="">
				<label class="wpsp-labelMain"><?php _e( 'Select Class Name', 'WPSchoolPress' );?></label>
				<select name="ClassID" id="ClassID" class="wpsp-form-control">
					
					<option value="all" <?php if($sel_classid=='all') echo "selected"; ?>>All</option>
				
					<?php										
					foreach( $sel_class as $classes ) { ?>
						
						<option value="<?php echo $classes->cid;?>" <?php if($sel_classid==$classes->cid) echo "selected"; ?>>
							<?php echo $classes->c_name;?>
						</option>
					<?php } 
					//if($current_user_role=='administrator' ) { ?>											
					<!-- 	<option value="all" <?php if($sel_classid=='all') echo "selected"; ?>>All</option> -->
					<?php //} ?>	
				</select>
			</form>
		</div>
		<div class="wpsp-right wpsp-import-export"> 
					<div class="wpsp-btn-lists"  title="<?php echo $protitle;?>" <?php echo $prodisable;?>>
						<?php if ( in_array( 'teacher', $role ) ) {?>
<div class="wpsp-btn-list"  <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>
						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left"> 
							<button type="button" class="wpsp-btn wpsp-btn-success  print" id="PrintParent" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">
							<i class="fa fa-print"></i> Print </button>
							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo $prodisable;?> title="<?php //echo $protitle;?>"><!-- 
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
							</button>
							<div class="wpsp-dropdown wpsp-dropdown-md">
								<ul>
								<form id="ParentColumnForm" name="ParentColumnForm">
									<li class="wpsp-drop-title wpsp-checkList"> Select Columns to Print </li>
										<?php foreach( $parentFieldList as $key=>$value ) { ?>
										<li class="wpsp-checkList">
											<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
											<label class="wpsp-label" for="<?php echo $key; ?>"><?php echo $value; ?></label>
										</li>
										<?php } ?>												
<?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?>
<input type="hidden" name="ClassID" value="<?php  echo $currentSelectClass; ?>">
								</form>
								</ul>
							</div>
						</div>
					</div>
						<div class="wpsp-btn-list"  <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>
						<div class="psp-dropdownmain wpsp-button-group">
							<button type="button" class="wpsp-btn print" id="ExportParents" <?php echo $prodisable;?> title="<?php echo $protitle;?>"><i class="fa fa-download"></i> Export </button>
							<button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo $prodisable;?> title="<?php echo $protitle;?>">
								<!-- <span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
							</button>
							<div id="exportcontainer" style="display:none;"></div>
							<div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">
								<ul>
									<form id="ExportColumnForm" name="ExportParentColumn" method="POST">
										<li class="wpsp-drop-title wpsp-checkList"> Select Columns to Export </li>
										<?php foreach( $parentFieldList as $key=>$value ) { ?>
										<li class="wpsp-checkList" >
											<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
											<label for="<?php echo $key; ?>"><?php echo $value; ?></label>
										</li>
										<?php } ?>
									<?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?>
<input type="hidden" name="ClassID" value="<?php  echo $currentSelectClass; ?>">
										<input type="hidden" name="exportparent" value="exportparent">
									</form>
								</ul>
						</div>
					</div>
				</div>
				<?php } ?>
<?php if ( in_array( 'administrator', $role ) ) {?>
						<div class="wpsp-btn-list"  <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>
						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left"> 
							<button type="button" class="wpsp-btn wpsp-btn-success  print" id="PrintParent" <?php echo $prodisable;?> title="<?php //echo $protitle;?>">
							<i class="fa fa-print"></i> Print </button>
							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo $prodisable;?> title="<?php //echo $protitle;?>"><!-- 
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
							</button>
							<div class="wpsp-dropdown wpsp-dropdown-md">
								<ul>
								<form id="ParentColumnForm" name="ParentColumnForm">
									<li class="wpsp-drop-title wpsp-checkList"> Select Columns to Print </li>
										<?php foreach( $parentFieldList as $key=>$value ) { ?>
										<li class="wpsp-checkList">
											<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
											<label class="wpsp-label" for="<?php echo $key; ?>"><?php echo $value; ?></label>
										</li>
										<?php } ?>												
<?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?>
							<input type="hidden" name="ClassID" value="<?php  echo $currentSelectClass; ?>">
										<!-- <input type="hidden" name="ClassID" value="<?php if(isset($_POST['ClassID'])) echo sanitize_text_field($_POST['ClassID']); else echo $sel_class[0]->cid; ?>"> -->
								</form>
								</ul>
							</div>
						</div>
					</div>
					<div class="wpsp-btn-list"  <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo $protitle;?>" <?php } ?>>
						<div class="psp-dropdownmain wpsp-button-group">
							<button type="button" class="wpsp-btn print" id="ExportParents" <?php echo $prodisable;?> title="<?php echo $protitle;?>"><i class="fa fa-download"></i> Export </button>
							<button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo $prodisable;?> title="<?php echo $protitle;?>">
								<!-- <span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
							</button>
							<div id="exportcontainer" style="display:none;"></div>
							<div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">
								<ul>
									<form id="ExportColumnForm" name="ExportParentColumn" method="POST">
										<li class="wpsp-drop-title wpsp-checkList"> Select Columns to Export </li>
										<?php foreach( $parentFieldList as $key=>$value ) { ?>
										<li class="wpsp-checkList" >
											<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
											<label for="<?php echo $key; ?>"><?php echo $value; ?></label>
										</li>
										<?php } ?>
										<input type="hidden" name="ClassID" value="<?php if(isset($_POST['ClassID'])) echo sanitize_text_field($_POST['ClassID']); else echo 0; ?>">
										<input type="hidden" name="exportparent" value="exportparent">
									</form>
								</ul>
						</div>
					</div>
				</div>
					<?php }?> 				
			</div>
		</div>
	</div>
	<div class="wpsp-card-body">				
			<?php if( empty( $sel_class ) && $current_user_role=='teacher' ) {
				echo '<div class="alert alert-danger wpsp-col-lg-2">'.$msg.'</div>';
			} else { ?>
			<div class="wpsp-row">
			<div class="wpsp-col-md-12 table-responsive">
			<table id="parent_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
			<thead>
				<tr>								
					<th><?php echo apply_filters( 'wpsp_parent_name_list_detail', esc_html__( 'Parent Name', 'WPSchoolPress' )); ?></th>
					<th>Student Name</th>							
					<th><?php echo apply_filters( 'wpsp_parent_email_list_detail', esc_html__( 'Parent Email ID', 'WPSchoolPress' )); ?></th>
					<th  align="center" class="nosort">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$student_table	=	$wpdb->prefix."wpsp_student";							
				$users_table	=	$wpdb->prefix."users";
				if(isset($_POST['ClassID'])){
					$class_id=intval($_POST['ClassID']);
				} else if(!empty($sel_class)) {
					$class_id = 'all';
				} else {
					$class_id='';
				}
				$classquery	=	" AND class_id='$class_id' ";
				if($class_id=='all'){
					$classquery	=	"";
				}
				$parent_ids=$wpdb->get_results("select DISTINCT u.user_email, CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name, p.s_fname,p.s_lname, p.wp_usr_id, p.parent_wp_usr_id from $student_table p, $users_table u where u.ID=p.parent_wp_usr_id AND user_login != 'parent' $classquery");
				
				foreach($parent_ids as $pinfo)
				{	
				$parent_ids							
				?>
					<tr>									
						<td><?php echo $pinfo->full_name;?></td>
						<td><?php echo $pinfo->s_fname." ".$pinfo->s_lname; ?> </td>
						<td><?php echo $pinfo->user_email;?></td>									
						<td align="center">
							<div class="wpsp-action-col">
								<a href="javascript:void(0)" title="View" data-pop="ViewModal" data-id="<?php echo intval($pinfo->parent_wp_usr_id);?>" class="ViewParent wpsp-popclick">
									<i class="icon dashicons dashicons-visibility wpsp-view-icon"></i></a>
								<a href="<?php echo wpsp_admin_url();?>sch-student&id=<?php echo intval($pinfo->wp_usr_id).'&edit=true#parent-field-lists';?>" title="Edit"><i class="icon dashicons dashicons-edit wpsp-edit-icon"></i></a>
							</div>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
			<tfoot>
			  <tr>														
					<th><?php echo apply_filters( 'wpsp_parent_name_list_detail', esc_html__( 'Parent Name', 'WPSchoolPress' )); ?></th>
				<th>Student Name</th>							
				<th><?php echo apply_filters( 'wpsp_parent_email_list_detail', esc_html__( 'Parent Email ID', 'WPSchoolPress' )); ?></th>
				<th  align="center">Action</th>
			  </tr>
			</tfoot>
		  </table>
		</div>
		</div>
		 <?php } ?> 
	</div>
</div>