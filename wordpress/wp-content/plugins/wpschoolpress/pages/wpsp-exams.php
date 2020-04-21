<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
    global $current_user, $wpdb;
		$current_user_role	=	$current_user->roles[0];
		$current_user_Id	=	intval($current_user->ID);
		$subject_table		=	$wpdb->prefix."wpsp_subject";
		if($current_user_role=='administrator' || $current_user_role=='teacher')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$filename	=	'';
			$header ='Exams';
			if( isset($_GET['tab'] ) && sanitize_text_field($_GET['tab']) == 'addexam' ) {
				$header	=	$label	=	__( 'Add New Exam', 'WPSchoolPress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-examForm.php';
			}elseif(( isset($_GET['id']) && intval($_GET['id'])))  {
				$header	=	$label	=	__( 'Update Exam', 'WPSchoolPress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-examForm.php';
			}
			$extable	=	$wpdb->prefix."wpsp_exam";
			$ctable		=	$wpdb->prefix."wpsp_class";
			$wpsp_exams =	$wpdb->get_results( "select * from $extable");
			$class_ID	=	0;
			if( $current_user_role=='teacher' ) {
				$cuserId	=	intval($current_user->ID);
				$class_ID	=	$wpdb->get_results("SELECT DISTINCT c.cid,c.c_name,s.id FROM wp_wpsp_class c
								INNER JOIN wp_wpsp_subject s ON s.class_id= c.cid
								WHERE s.sub_teach_id =$cuserId || c.teacher_id = $cuserId");
			$j=0;
			foreach( $class_ID as $class_IDa ) {
				$clsid[] = $class_IDa->cid;
				$subid[] = $class_IDa->id;
			}
			$msg		=	'Please Ask Principal To Assign Class';
			if( !empty( intval($class_ID )) ) {
				$wpsp_exams =	$wpdb->get_results( "select * from $extable where classid IN (".implode(',',$clsid).") and subject_id IN (".implode(',',$subid).")");
			}
		}
		if( !empty( $filename) ) {
			include_once ( $filename );
		} else {?>
		<div class="wpsp-card">
      <div class="wpsp-card-body">
				<div class="subject-head">
          <?php if( $current_user_role=='teacher' && empty( $class_ID ) ) {
            echo '<div class="alert alert-danger col-lg-2">'.$msg.'</div>';
          } ?>
				</div>
				<table id="exam_class_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
					<thead>
						<tr>
							<th class="nosort">#</th>
							<th>Exam Name</th>
							<th>Class Name</th>
							<th>Subject Name</th>
							<th>Start Date</th>
							<th>End Date</th>
						<?php	if($current_user_role=='administrator'){?>
							<th class="nosort">Action</th>
						<?php }?>
						</tr>
					</thead>
					<tbody>
						<?php
						$sno=1;
						foreach( $wpsp_exams as $wpsp_exam ) {
							$classname = $sublist	=	'';
							$classid	=	isset($wpsp_exam->classid)  ? intval($wpsp_exam->classid) : '';
							if( !empty( intval($classid )) ) {
								$classname = $wpdb->get_var( "SELECT c_name FROM $ctable WHERE cid = '$classid'" );
							}
							$sublist	=	'-';
							if( !empty($wpsp_exam->subject_id) ) {
								$subject_list	=	array();
								$slist	=	str_replace( 'All,', '',$wpsp_exam->subject_id);
								if( !empty( $slist ) ) {
									$subjectlist	=	$wpdb->get_results("SELECT sub_name FROM $subject_table WHERE id IN($slist) ", ARRAY_A );
									foreach( $subjectlist as $list ) {
										$subject_list[]	= $list['sub_name'];
									}
									$sublist	=	implode(", ",$subject_list);
								}
							}
						?>
							<tr id="<?php echo intval($wpsp_exam->eid);?>">
								<td><?php echo intval($sno); ?>
								<td><?php echo  $wpsp_exam->e_name;?></td>
								<td><?php echo $classname; ?></td>
								<td><?php echo  $sublist; ?></td>
								<td><?php echo  wpsp_ViewDate($wpsp_exam->e_s_date); ?></td>
								<td><?php echo  wpsp_ViewDate($wpsp_exam->e_e_date);?></td>
									<?php	if($current_user_role=='administrator'){?>
										<td align="center">
									<div class="wpsp-action-col">
										<a href="<?php echo wpsp_admin_url();?>sch-exams&id=<?php echo intval($wpsp_exam->eid).'&edit=true';?>"><i class="icon dashicons dashicons-edit wpsp-edit-icon"></i></a>
										<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo intval($wpsp_exam->eid);?>" >
                      <i class="icon dashicons dashicons-trash wpsp-delete-icon" data-id="<?php echo intval($wpsp_exam->eid);?>"></i>
                    </a>
									</div>
								</td>
							<?php } ?>
							</tr>
						<?php
							$sno++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th class="nosort">#</th>
							<th>Exam Name</th>
							<th>Class Name</th>
							<th>Subject Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<?php	if($current_user_role=='administrator'){?>
							<th class="nosort">Action</th>
							<?php } ?>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	<?php } ?>
		<?php if($current_user_role=='administrator'){?>
		<!--Info Modal-->
		<div class="modal fade" id="InfoModal" tabindex="-1" role="dialog" aria-labelledby="InfoModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="col-md-12">
						<div class="box box-success">
							<div class="box-header">
								<p class="box-title" id="InfoModalTitle"></p>
							</div><!-- /.box-header -->
							<div id="InfoModalBody" class="box-body PTZero">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.modal -->
		<?php
	}
  wpsp_body_end();
  wpsp_footer();
}
else if( $current_user_role=='parent' || $current_user_role='student'){
  wpsp_topbar();
  wpsp_sidebar();
  wpsp_body_start();
  ?>
  <div class="wpsp-card">
    <div class="wpsp-card-head">
      <h3 class="wpsp-card-title">Time Table </h3>
    </div>
    <div class="wpsp-card-body">
      <table id="exam_class_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
        <thead>
          <tr>
            <th class="nosort">#</th>
						<th>Exam Name</th>
						<th>Class Name</th>
						<th>Subject Name</th>
						<th>Start Date</th>
						<th>End Date</th>
					</tr>
				</thead>
        <tbody>
          <?php
          $class_id = base64_decode($_GET['cid']);
          $extable	=	$wpdb->prefix."wpsp_exam";
					$studtable	=	$wpdb->prefix."wpsp_student";
					$classtable	=	$wpdb->prefix."wpsp_class";
          $wpsp_exams = [];
					if( $current_user_role=='parent' ) {
			  $wpsp_exams =$wpdb->get_results( "SELECT DISTINCT e.*,c.c_name FROM $studtable st, $extable e, $classtable c where st.parent_wp_usr_id=$current_user_Id AND e.classid=$class_id AND c.cid= $class_id");
				} else {
              $wpsp_exams =$wpdb->get_results( "SELECT DISTINCT e.*,c.c_name FROM $studtable st, $extable e, $classtable c where st.wp_usr_id=$current_user_Id AND e.classid=$class_id AND c.cid=$class_id");
					}
          $sno=1;
          if(!empty($wpsp_exams)){
  					foreach ($wpsp_exams as $wpsp_exam){
              $sublist	=	'';
              if( !empty($wpsp_exam->subject_id) ) {
                $subject_list	=	array();
  							$subjectlist	=	$wpdb->get_results("SELECT sub_name FROM $subject_table WHERE id IN($wpsp_exam->subject_id)", ARRAY_A );
  							foreach( $subjectlist as $list ) {
  								$subject_list[]	= $list['sub_name'];
  							}
  							$sublist	=	implode(", ",$subject_list);
  						}
  						?>
  						<tr id="<?php echo $wpsp_exam->eid;?>" class="pointer">
  							<td><?php echo $sno;?></td>
  							<td><?php echo  $wpsp_exam->e_name;?></td>
  							<td><?php echo  $wpsp_exam->c_name;?> </td>
  							<td style="width: 580px;"><?php echo  $sublist; ?> </td>
  							<td><?php echo  wpsp_ViewDate($wpsp_exam->e_s_date); ?></td>
  							<td><?php echo  wpsp_ViewDate($wpsp_exam->e_e_date);?></td>
  						</tr>
  						<?php
  						$sno++;
  					}
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
  wpsp_body_end();
  wpsp_footer();
}
}
else{
	//Include Login Section
  include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
}
?>
