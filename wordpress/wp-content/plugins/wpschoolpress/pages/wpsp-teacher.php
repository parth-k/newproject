<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wp_roles, $wpdb;
    $current_user_role=$current_user->roles[0];
		if($current_user_role=='administrator' || $current_user_role=='teacher')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			?>
			<?php
				if(isset( $_GET['tab'] ) && sanitize_text_field($_GET['tab'])=='addteacher')
				{
					include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-teacherForm.php' );
				}
				else if(isset($_GET['id']) && is_numeric($_GET['id']))
				{
					include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-teacherProfile.php' );
				}
				else {
					include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-teacherList.php' );
				?>
				<?php do_action( 'wpsp_teacher_import_html' ); ?>
			<?php
			}
			wpsp_body_end();
			wpsp_footer();
		}
		if($current_user_role=='parent' || $current_user_role=='student')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$ID	=	intval($current_user->ID);
			$teacher_table	=	$wpdb->prefix."wpsp_teacher";
			$class_table	=	$wpdb->prefix."wpsp_class";
			$subjects_table = 	$wpdb->prefix."wpsp_subject";
			$student_table  = 	$wpdb->prefix."wpsp_student";
			$queryFiels		=	$current_user_role=='student' ? 'wp_usr_id' : 'parent_wp_usr_id';
			$classlist		=	array();
			$classquery		=	'';

			$classID = base64_decode($_GET['cid']);

			?>
	     <div class="wpsp-card">
			<div class="wpsp-card-head">
        <h3 class="wpsp-card-title">Teacher's Details </h3>
      </div>
			<div class="wpsp-card-body">
					<table id="teacher_table" class="wpsp-table">
						<thead>
						<tr>
							<th class="nosort">#</th>
							<th><?php _e( 'Full Name', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Incharge of Class', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Subjects Handling', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Phone', 'WPSchoolPress' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						if( !empty( $classID ) ) {

							$classquery	=	'AND c.cid='.$classID;

            	$sub_han		=	$wpdb->get_results("select sub_name, sub_teach_id, c.c_name from $subjects_table s, $class_table c where sub_teach_id > 0 AND c.cid = s.class_id $classquery order by c.cid");

              if(!empty($sub_han)){
                foreach($sub_han as $subhan) {
  								$sub_handling[$subhan->sub_teach_id][]=$subhan->sub_name.' ('.$subhan->c_name.')';
  								$teacher[]	=	$subhan->sub_teach_id;
							  }
              }else{
                $teacher[]	=	'0';
              }

							$incharges=$wpdb->get_results("select c.c_name,c.teacher_id from $class_table c LEFT JOIN $teacher_table t ON t.wp_usr_id=c.teacher_id where c.teacher_id>0 $classquery");
							foreach($incharges as $incharge){
								$cincharge[$incharge->teacher_id][]=$incharge->c_name;
							}
							if( !empty( $teacher ) && !empty($classID) ) {
								$teacherQuery	=	' WHERE wp_usr_id IN ('.implode( ", " , $teacher ).') ';
							}
							$teachers = $wpdb->get_results("select * from $teacher_table $teacherQuery order by tid DESC");
							$sno		=	0;

							foreach($teachers as $tinfo)
							{
								$loc_avatar	=	get_user_meta($tinfo->wp_usr_id,'simple_local_avatar',true);
								$img_url	=	$loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
								$sno	=	$sno+1;
							?>
							<tr>
								<td><?php echo $sno;?></td>
								<td><?php echo $tinfo->first_name." ". $tinfo->middle_name." ".$tinfo->last_name;?></td>
								<td><?php if(isset($cincharge[$tinfo->wp_usr_id])) { echo implode( ", ", $cincharge[$tinfo->wp_usr_id] ); } else { echo '-'; } ?></td>
								<td><?php if(isset($sub_handling[$tinfo->wp_usr_id])) { echo implode( ", ", $sub_handling[$tinfo->wp_usr_id] ); } else { echo '-'; } ?></td>
								<td><?php echo $tinfo->phone;?></td>
							</tr>
							<?php }	?>
						<?php } ?>
						</tbody>
						<tfoot>
						<tr>
							<th>#</th>
							<th><?php _e( 'Full Name', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Incharge of Class', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Subjects Handling', 'WPSchoolPress' ); ?></th>
							<th><?php _e( 'Phone', 'WPSchoolPress' ); ?></th>
						</tr>
						</tfoot>
					</table>
			</div>
		</div>
		<?php
			wpsp_body_end();
			wpsp_footer();
		}?>
		<div class="wpsp-popupMain" id="ViewModal">
		  <div class="wpsp-overlayer"></div>
		  <div class="wpsp-popBody">
		    <div class="wpsp-popInner">
		    	<a href="javascript:;" class="wpsp-closePopup"></a>
				<div id="ViewModalContent">
				</div>
		    </div>
		  </div>
		</div>
		<?php
	}
	else {
		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
	?>
