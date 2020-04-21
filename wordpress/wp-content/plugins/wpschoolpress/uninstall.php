<?php 
/**
 * WPSchoolPress Uninstall
 *
 * Uninstalling WPSchoolPress deletes user, roles, pages, tables.
 *
 */
if ( !defined( 'ABSPATH' ) ) {	exit; }
else {	
	require_once(ABSPATH.'wp-admin/includes/user.php' );	
	$remove_data_status	=	get_option( 'wpsp_remove_data');	
	if( $remove_data_status== 1 ) {		
		global $wpdb;		
		$teacher_table				=	$wpdb->prefix.'wpsp_teacher';		
		$student_table				=	$wpdb->prefix.'wpsp_student';			
		$class_table				=	$wpdb->prefix.'wpsp_class' ;		
		$parents_table				=	$wpdb->prefix.'wpsp_parent';		
		$exams_table				=	$wpdb->prefix.'wpsp_exam';		
		$mark_table					=	$wpdb->prefix.'wpsp_mark';		
		$mark_extract_table			=	$wpdb->prefix.'wpsp_mark_extract';		
		$mark_fields_table			=	$wpdb->prefix.'wpsp_mark_fields';		
		$messages_table				=	$wpdb->prefix.'wpsp_messages';		
		$time_table					=	$wpdb->prefix.'wpsp_timetable';		
		$notification_table 		=	$wpdb->prefix.'wpsp_notification';		
		$hostel_table				=	$wpdb->prefix.'wpsp_hostel';		
		$subject_table				=	$wpdb->prefix.'wpsp_subject';		
		$workinghours_table			=	$wpdb->prefix.'wpsp_workinghours';		
		$transport_table			=	$wpdb->prefix.'wpsp_transport';		
		$settings_table 			=	$wpdb->prefix.'wpsp_settings';		
		$attendance_table 			=	$wpdb->prefix.'wpsp_attendance';		
		$events_table 				=	$wpdb->prefix.'wpsp_events';		
		$teacher_attendance_table	=	$wpdb->prefix.'wpsp_teacher_attendance';		
		$grade_settings_table		=	$wpdb->prefix.'wpsp_grade';		
		$import_history_table		=	$wpdb->prefix.'wpsp_import_history';		
		$wpsp_fees					=	$wpdb->prefix.'wpsp_fees';		
		$wpsp_fees_payment			=	$wpdb->prefix.'wpsp_fees_payment';		
		$wpsp_fee_payment_history	=	$wpdb->prefix.'wpsp_fee_payment_history';
		$wpsp_leavedays	=	$wpdb->prefix.'wpsp_leavedays';			
		$page_titles=array( 'Teacher','Student','Class','Dashboard','Messages','Exams','Attendance','Timetable','Events','Subject','Transport','Settings','Marks','Notify','SMS','ImportHistory','LeaveCalendar','TeacherAttendance', 'Parent', 'Change Password', 'Payment' );		
		foreach($page_titles as $page_title) {			
			$page = get_page_by_title($page_title);			
			wp_delete_post($page->ID,true);	//Trash or delete a post or page.	
		}		
		$student_ids	=	$wpdb->get_col( "select wp_usr_id from $student_table" );		
		$parent_ids		=	$wpdb->get_col( "select parent_wp_usr_id from $student_table" );		
		$teacher_ids	=	$wpdb->get_col( "select wp_usr_id from $teacher_table" );		
		$user_ids[]     =   array();		
		$user_ids       =   array_merge( $student_ids,$parent_ids,$teacher_ids );				
		foreach($user_ids as $user_id) {			
			wp_delete_user($user_id); //Delete user		
		}
		$tables        =    array($teacher_table,$student_table,$class_table,$parents_table,$exams_table,$mark_table,$messages_table,$time_table,$notification_table,$hostel_table,$subject_table,$workinghours_table,$transport_table,$settings_table,$attendance_table,$events_table,$teacher_attendance_table,$grade_settings_table,$settings_table,$mark_extract_table,$mark_fields_table,$import_history_table,$leave_table,$wpsp_fees, $wpsp_fees_payment, $wpsp_fee_payment_history, $wpsp_leavedays);		
		foreach($tables as $table) {			
			$sql = "DROP TABLE IF EXISTS $table";			
			$wpdb->query($sql); //Delete tables		
		}	
	}
} ?><?php 
/**
 * WPSchoolPress Uninstall
 *
 * Uninstalling WPSchoolPress deletes user, roles, pages, tables.
 *
 */
if ( !defined( 'ABSPATH' ) ) {	exit; }
else {	
	require_once(ABSPATH.'wp-admin/includes/user.php' );	
	$remove_data_status	=	get_option( 'wpsp_remove_data');	
	if( $remove_data_status== 1 ) {		
		global $wpdb;		
		$teacher_table				=	$wpdb->prefix.'wpsp_teacher';		
		$student_table				=	$wpdb->prefix.'wpsp_student';			
		$class_table				=	$wpdb->prefix.'wpsp_class' ;		
		$parents_table				=	$wpdb->prefix.'wpsp_parent';		
		$exams_table				=	$wpdb->prefix.'wpsp_exam';		
		$mark_table					=	$wpdb->prefix.'wpsp_mark';		
		$mark_extract_table			=	$wpdb->prefix.'wpsp_mark_extract';		
		$mark_fields_table			=	$wpdb->prefix.'wpsp_mark_fields';		
		$messages_table				=	$wpdb->prefix.'wpsp_messages';		
		$time_table					=	$wpdb->prefix.'wpsp_timetable';		
		$notification_table 		=	$wpdb->prefix.'wpsp_notification';		
		$hostel_table				=	$wpdb->prefix.'wpsp_hostel';		
		$subject_table				=	$wpdb->prefix.'wpsp_subject';		
		$workinghours_table			=	$wpdb->prefix.'wpsp_workinghours';		
		$transport_table			=	$wpdb->prefix.'wpsp_transport';		
		$settings_table 			=	$wpdb->prefix.'wpsp_settings';		
		$attendance_table 			=	$wpdb->prefix.'wpsp_attendance';		
		$events_table 				=	$wpdb->prefix.'wpsp_events';		
		$teacher_attendance_table	=	$wpdb->prefix.'wpsp_teacher_attendance';		
		$grade_settings_table		=	$wpdb->prefix.'wpsp_grade';		
		$import_history_table		=	$wpdb->prefix.'wpsp_import_history';		
		$wpsp_fees					=	$wpdb->prefix.'wpsp_fees';		
		$wpsp_fees_payment			=	$wpdb->prefix.'wpsp_fees_payment';		
		$wpsp_fee_payment_history	=	$wpdb->prefix.'wpsp_fee_payment_history';		
		$page_titles=array( 'Teacher','Student','Class','Dashboard','Messages','Exams','Attendance','Timetable','Events','Subject','Transport','Settings','Marks','Notify','SMS','ImportHistory','LeaveCalendar','TeacherAttendance', 'Parent' );		
		foreach($page_titles as $page_title) {			
			$page = get_page_by_title($page_title);			
			wp_delete_post($page->ID,true);	//Trash or delete a post or page.	
		}		
		$student_ids	=	$wpdb->get_col( "select wp_usr_id from $student_table" );		
		$parent_ids		=	$wpdb->get_col( "select parent_wp_usr_id from $parents_table" );		
		$teacher_ids	=	$wpdb->get_col( "select wp_usr_id from $teacher_table" );		
		$user_ids[]     =   array();		
		$user_ids       =   array_merge( $student_ids,$parent_ids,$teacher_ids );				
		foreach($user_ids as $user_id) {			
			wp_delete_user($user_id); //Delete user		
		}
		$tables        =    array($teacher_table,$student_table,$class_table,$parents_table,$exams_table,$mark_table,$messages_table,$time_table,$notification_table,$hostel_table,$subject_table,$workinghours_table,$transport_table,$settings_table,$attendance_table,$events_table,$teacher_attendance_table,$grade_settings_table,$settings_table,$mark_extract_table,$mark_fields_table,$import_history_table,$leave_table,$wpsp_fees, $wpsp_fees_payment, $wpsp_fee_payment_history);		
		foreach($tables as $table) {			
			$sql = "DROP TABLE IF EXISTS $table";			
			$wpdb->query($sql); //Delete tables		
		}	
	}
} ?>