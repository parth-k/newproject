<?php
if (!defined('ABSPATH')) exit('No Such File');

function wpsp_DeleteUser($uids = array() , $type)
{
	wpsp_Authenticate();
	global $wpdb;
	$student_tbl = $wpdb->prefix . "wpsp_student";
	$teacher_tbl = $wpdb->prefix . "wpsp_teacher";
	$mark_tbl = $wpdb->prefix . "wpsp_mark";
	$user_tbl = $wpdb->prefix . "users";
	$delid = array();
if (count($uids) > 0)
	{
		foreach($uids as $uid)
		{
			$del = wp_delete_user($uid); //delete from user table
			if ($del)
			{
				array_push($delid, $uid);
			}
		}
	}

	if (!empty($delid))
	{
		foreach($delid as $uid)
		{
			if ($type == 'student')
			{
				do_action('wpsp_student_delete', intval($uid));
				$parentresult = $wpdb->get_row("SELECT parent_wp_usr_id FROM $student_tbl WHERE wp_usr_id='$uid'");

				$wpdb->delete($student_tbl, array(
					'wp_usr_id' => $uid
				)); //delete from student table
				$wpdb->delete($mark_tbl, array(
					'student_id' => $uid
				)); //delete from mark table
				$wpdb->delete( $user_tbl, array( 'ID' => $parentresult->parent_wp_usr_id ) );

				$wpdb->delete($user_tbl, array(
					'ID' => $uid
				));//delete from User table
			}
			else
			if ($type == 'teacher')
			{
				do_action('wpsp_teacher_delete', intval($uid));
				$wpdb->delete($teacher_tbl, array(
					'wp_usr_id' => $uid
				));
				$wpdb->delete($user_tbl, array(
					'ID' => $uid
				));//delete from User table
			}
		}
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}
function sanitize_price_array( $array ) {
   foreach ( (array) $array as $k => $v ) {
      if ( is_array( $v ) ) {
          $array[$k] =  sanitize_price_array( $v );
      } else {
          $array[$k] = sanitize_text_field( $v );
      }
   }
  return $array;
}
function wpsp_IsNameExist($table, $namecolumn, $name, $idcolumn = null, $id = null, $return = false)
{
	global $wpdb;
	if ($idcolumn == null && $id == null)
	{
		$sql = $wpdb->get_row("select * from $table where UPPER($namecolumn)=UPPER('$name')");
	}
	else
	{
		$sql = $wpdb->get_row("select $idcolumn from $table where UPPER($namecolumn)=UPPER('$name') and $idcolumn!='$id'");
	}
	if (!empty($sql))
	{
		if ($return) return true;
		else echo "true";
	}

	else
	{
		if ($return) return false;
		else echo "false";
	}
	wp_die();
}



function isa_test_cron_job_send_mail()
								{

									global $wpdb;
  $classidlist  = array();
  $classidc_sdate = array();
  $classidc_edate = array();
  $class_id='';
  $stl =  array();
  $stl0 =   array();
  $student_table  = $wpdb->prefix."wpsp_student";
  $history_table  = $wpdb->prefix."wpsp_history";
  $class_mapping_table  = $wpdb->prefix."wpsp_class_mapping";
  $users_table  = $wpdb->prefix."users";
  $class_tbl = $wpdb->prefix . "wpsp_class";
  $mresult = $wpdb->get_results("select * from $class_tbl");
  $current_date =  strtotime(date("Y/m/d"));
                  foreach( $mresult  as $value )
                  {
                      $classidlist[$value->cid] = $value->cid;
                      $classidc_sdate[$value->cid] = $value->c_sdate;
                      $classidc_edate[$value->cid] = $value->c_edate;
                      $end_date =  strtotime($value->c_edate);
                      $studentlists_mapping = $wpdb->get_results("select * from $student_table s, $class_mapping_table m where m.sid = s.sid and m.cid = '".$value->cid."'");

                        foreach ($studentlists_mapping as $stu)
                        {
                           $end_date_time =  strtotime($stu->date);
                          if($stu->date == "0000-00-00")
                          {
                            if($current_date > $end_date)
                              {

                              $mresult1 = $wpdb->get_row("select * from $class_tbl where cid = ".$stu->cid);
                              $mresult1->cid;
                                 $wpdb->insert( $history_table, array("c_id" => $mresult1->cid, "t_id" => $mresult1->teacher_id,"s_id" => $stu->wp_usr_id, "start_date" => $mresult1->c_sdate,"end_date" => $mresult1->c_edate, "enrollment_date" => $stu->s_doj ));

                                $class_id_array = unserialize( $stu->class_id );
                                foreach($class_id_array as $key => $value)
                                {
                                  if($value == $mresult1->cid)
                                  {
                                    unset($class_id_array[$key]);
                                  }
                                }
                                $clswithremove = serialize($class_id_array);
                                 $wpdb->query("UPDATE $student_table SET class_id='$clswithremove' WHERE wp_usr_id=$stu->wp_usr_id");
                                 $wpdb->query("DELETE FROM $class_mapping_table WHERE cid = '".$stu->cid."'");
                                }


                          }
                          else
                          {
                               if($stu->date != "0000-00-00")
                          {
                              if($current_date > $end_date_time)
                              {

                                $mresult1 = $wpdb->get_row("select * from $class_tbl where cid = ".$stu->cid);
                                 $mresult1->cid;
                                    $wpdb->insert( $history_table, array("c_id" => $mresult1->cid, "t_id" => $mresult1->teacher_id,"s_id" => $stu->wp_usr_id, "start_date" => $mresult1->c_sdate,"end_date" => $mresult1->c_edate, "enrollment_date" => $stu->s_doj ));

                                  $class_id_array = unserialize( $stu->class_id );
                                 // print_r($class_id_array);
                                 // echo $mresult1->cid;
                                  foreach($class_id_array as $key => $value)
                                  {
                                    if($value == $mresult1->cid)
                                    {
                                      unset($class_id_array[$key]);
                                    }
                                  }
                                  $clswithremove = serialize($class_id_array);

                                    $wpdb->query("UPDATE $student_table SET class_id='$clswithremove' WHERE wp_usr_id=$stu->wp_usr_id");
                                   // echo "DELETE FROM $class_mapping_table WHERE cid = '".$stu->cid."'";
                                    $wpdb->query("DELETE FROM $class_mapping_table WHERE cid = '".$stu->cid."'");
                              }
                          }
                          }
                        }


                  }
								}
function wpsp_IsMarkEntered($classid, $subjectid, $examid)
{
	global $wpdb;
	$mark_tbl = $wpdb->prefix . "wpsp_mark";
	$mresult = $wpdb->get_results("select * from $mark_tbl where subject_id='$subjectid' and class_id='$classid' and exam_id='$examid'");
	return count($mresult) > 0 ? true : false;
}
function wpsp_GetMarks($classid, $subjectid, $examid)
{
	global $wpdb;
	$mtable = $wpdb->prefix . "wpsp_mark";
	$marks = $wpdb->get_results("select * from $mtable WHERE subject_id=$subjectid and class_id=$classid and exam_id=$examid order by mid ASC");
	return $marks;
}
function wpsp_GetExMarks($subjectid, $examid)
{
	global $wpdb;
	$exmark_tbl = $wpdb->prefix . "wpsp_mark_extract";
	$ext_marks = $wpdb->get_results("select * from $exmark_tbl WHERE subject_id=$subjectid and exam_id=$examid");
	return $ext_marks;
}
function wpsp_GetRow($table, $select = '*', $id, $column = 'id')
{
	global $wpdb;
	$row_info = $wpdb->get_row("select $select from $table where $column='$id'");
	return $row_info;
}
function wpsp_validation($input = array())
{
	$error = array();
	foreach($input as $value => $rule)
	{
		$rules = explode("|", $rule);
		foreach($rules as $rl)
		{
			switch ($rl)
			{
			case 'required':
				if (trim($value) == '')
				{
					array_push($error, 'Required field should not be empty');
				}
				break;
			case 'email':
				if (!filter_var($value, FILTER_VALIDATE_EMAIL))
				{
					array_push($error, 'Please enter valid email address');
				}
				break;
			case 'unique':
				if (wpsp_CheckUsername($value, true))
				{
					array_push($error, 'Please enter valid email address');
				}
				break;
			}
		}
	}
	if (empty($error))
	{
		return true;
	}
	else
	{
		return $error;
	}
}
function wpsp_UploadPhoto()
{
	$sid = sanitize_text_field($_POST['sid']);
	$gallery_path = WPSP_PLUGIN_PATH . 'uploads/gallery/' . $sid;
	if (!file_exists($gallery_path))
	{
		mkdir($gallery_path);
	}
	if (!empty($_FILES['studentPhotos']))
	{
		$allowed = array(
			'png',
			'jpg',
			'jpeg',
			'jpe'
		);
		$filename = $_FILES['studentPhotos']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if (in_array($ext, $allowed))
		{
			if (move_uploaded_file($_FILES['studentPhotos']['tmp_name'], $gallery_path . '/' . $filename)) echo "Photo Uploaded successfully!";
			else echo "Something went wrong!";
		}
		else
		{
			echo "Unallowed extension!";
		}
	}
	wp_die();
}
function wpsp_DeletePhoto()
{
	wpsp_Authenticate();
	$iname = sanitize_text_field($_POST['iname']);
	$sid = sanitize_text_field($_POST['sid']);
	$gallery_file = WPSP_PLUGIN_PATH . 'uploads/gallery/' . $sid . '/' . $iname;
	if (unlink($gallery_file))
	{
		echo "Photo deleted successfully";
	}
	else
	{
		echo "Spmething went wrong!";
	}
	wp_die();
}
function wpsp_BulkDelete()
{
		$uids = explode(',', sanitize_text_field($_POST['UID']));
	$type = sanitize_text_field($_POST['type']);
	if (wpsp_DeleteUser($uids, $type))
	{
		echo "success";
	}
	else
	{
		echo "failed";
	}
	wp_die();
}
/* Delete Student */
function wpsp_DeleteStudent()
{
	wpsp_Authenticate();
	global $wpdb;
	$student_tbl = $wpdb->prefix . "wpsp_student";
	$sid = esc_sql($_POST['sid']);
	$usertable = $wpdb->prefix . "users";
	$result = $wpdb->get_row("SELECT wp_usr_id,parent_wp_usr_id FROM $student_tbl WHERE sid='$sid'", ARRAY_A);

	do_action('wpsp_student_delete', intval($sid));

	$delstu = $wpdb->delete($student_tbl, array(
		'sid' => $sid
	));

	$wpdb->delete( $usertable, array( 'ID' => $result['wp_usr_id'] ) );
	$wpdb->delete( $usertable, array( 'ID' => $result['parent_wp_usr_id'] ) );

	if ($delstu) echo "success";
	else echo "Something went wrong!";
	wp_die();
}
/* Delete Teacher */
function wpsp_DeleteTeacher()
{
	wpsp_Authenticate();
	global $wpdb;
	$teacher_tbl = $wpdb->prefix . "wpsp_teacher";
	$usertable = $wpdb->prefix . "users";
	$tid = esc_sql($_POST['tid']);
	$result = $wpdb->get_row("SELECT wp_usr_id FROM $teacher_tbl WHERE tid='$tid'", ARRAY_A);
	do_action('wpsp_teacher_delete', intval($tid));
	$wpdb->delete( $usertable, array( 'ID' => $result['wp_usr_id'] ) );
	$deltec = $wpdb->delete($teacher_tbl, array(
		'tid' => $tid
	));
	if ($deltec) echo "success";
	else echo "Something went wrong!";
	wp_die();
}
/* Delete imported User*/
function wpsp_UndoImport()
{
	wpsp_Authenticate();
	global $wpdb;
	$id = intval($_POST['id']);
	$importtable = $wpdb->prefix . "wpsp_import_history";
	$usertable = $wpdb->prefix . "users";
	$result = $wpdb->get_row("SELECT * ,count as totalcount FROM $importtable WHERE id='$id'", ARRAY_A);
	$imported_array = json_decode($result['imported_id']);

	$count = $result['totalcount'];
	 $type = $result['type'];
	if ($type == '1')
	{


		$studenttable = $wpdb->prefix . "wpsp_student";
		foreach($imported_array as $value)
		{

		$user_del = $wpdb->delete($usertable, array('ID' => $value));
			$result1 = $wpdb->get_row("SELECT parent_wp_usr_id FROM $studenttable WHERE wp_usr_id='$value'");

			$user_del1 = $wpdb->delete($usertable, array('ID' => $result1->parent_wp_usr_id));

			$wpsp_del = $wpdb->delete($studenttable, array('wp_usr_id' => $value));
	}
	}
	else if ($type == '2')
	{
		$teachertable = $wpdb->prefix . "wpsp_teacher";
		foreach($imported_array as $value)
		{
			$user_del = $wpdb->delete($usertable, array(
				'ID' => $value
			));
			$wpsp_del = $wpdb->delete($teachertable, array(
				'wp_usr_id' => $value
			));
		}
	}
	else
	if ($type == '4')
	{
		$marktable = $wpdb->prefix . "wpsp_mark";
		foreach($imported_array as $value)
		{

			$wpsp_del = $wpdb->delete($marktable, array(
				'mid' => $value
			));
		}
	}

	$import_del = $wpdb->delete($importtable, array(
		'id' => $id
	));
	if (($user_del) && ($wpsp_del))
	{
		echo "Imported " . $count . " rows are removed successfully!!";
	}
	else
	{
		echo "Success.. But something wrong because some rows may be deleted previously..  ";
	}
	wp_die();
}
function wpsp_ParentPublicProfile($pid = '', $button = 0)
{
	global $wpdb;
	$student_table = $wpdb->prefix . "wpsp_student";
	$users_table = $wpdb->prefix . "users";
	if ($pid == '') $pid = intval($_POST['id']);
	if (!empty($pid)) $where = "where p.parent_wp_usr_id='$pid'";
	$button = isset($_POST['button']) ? sanitize_text_field($_POST['button']) : $button;
	$pinfo = $wpdb->get_row("select p.*, CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name ,u.user_email from $student_table p LEFT JOIN $users_table u ON u.ID=p.parent_wp_usr_id $where");
	$loc_avatar = get_user_meta($pid, 'simple_local_avatar', true);
	$img_url = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/avatar.png';


	if (!empty($pinfo))
	{
		$profile = "
				<div class='wpsp-panel-body'>
					<div class='wpsp-userpic'>
						<img src='$img_url' height='150px' width='150px' class='img img-circle'/>
					</div>
					<div class='wpsp-userDetails'>
						<table class='wpsp-table'>
							<tbody>
								<tr>
									<td colspan='2'><strong>Full Name: </strong>$pinfo->p_fname $pinfo->p_mname $pinfo->p_lname </td>
								</tr>
								<tr>
									<td><strong>Email: </strong>$pinfo->user_email</td>
									<td><strong>Gender: </strong>$pinfo->p_gender</td>
								</tr>
								<tr>
									<td><strong>Education: </strong>$pinfo->p_edu</td>
									<td><strong>Profession: </strong>$pinfo->p_profession</td>
								</tr>
								<tr>
									<td  colspan='2'><strong>Blood Group: </strong> $pinfo->p_bloodgrp</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>";
	}
	else
	{
		$profile = "No data retrived!..";
	}
	echo apply_filters('wpsp_parent_profile', $profile, intval($pid));
	wp_die();
}
/****************************** Class Functions ******************************/
function wpsp_AddClass()
{
	if (!isset($_POST['caction_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['caction_nonce']) , 'ClassAction'))
	{
		echo "Unauthorized Submission";
		exit;
	}
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_class_table = $wpdb->prefix . "wpsp_class";
	$class_name = sanitize_text_field($_POST['Name']);
	$class_number = intval($_POST['Number']);
	$class_teacher = intval($_POST['ClassTeacherID']);
	$class_location = sanitize_text_field($_POST['Location']);
	$start_date = wpsp_StoreDate(sanitize_text_field($_POST['Sdate']));
	$end_date = wpsp_StoreDate(sanitize_text_field($_POST['Edate']));
	$classfeetype = sanitize_text_field($_POST['classfeetype']);
	if (wpsp_IsNameExist($wpsp_class_table, 'c_name', $class_name, '', '', true) == true)
	{
		if (isset($_POST['add_from']))
		{
			$response['statuscode'] = 0;
			$response['html'] = '';
			$response['msg'] = 'Class Name Already Exists!';
			echo json_encode($response);
		}
		else
		{
			echo "Class name exists!";
		}
		exit;
	}
	else
	{
		$class_data = array(
			'c_numb' => $class_number,
			'c_name' => $class_name,
			'teacher_id' => $class_teacher,
			'c_loc' => $class_location,
			'c_sdate' => $start_date,
			'c_edate' => $end_date,
			'c_fee_type' => $classfeetype,
			'c_capacity' => sanitize_text_field($_POST['capacity'])
		);
		$wpsp_class_ins = $wpdb->insert($wpsp_class_table, $class_data);
		if ($wpsp_class_ins)
		{
			do_action('wpsp_class_created', $wpdb->insert_id, $class_data);
		}
		if (isset($_POST['add_from']) && sanitize_text_field($_POST['add_from']) == 'student')
		{
			$response['statuscode'] = 0;
			$response['html'] = '';
			$response['msg'] = 'Error in adding class, Try again later..';
			if ($wpsp_class_ins && !empty($wpdb->insert_id))
			{
				$response['statuscode'] = 1;
				$response['html'] = '<option value="' . $wpdb->insert_id . '">' . $class_name . '</option>';
				$response['msg'] = 'Class Added Successfully';
			}
			echo json_encode($response);
		}
		else
		{
			echo $status = $wpsp_class_ins ? "inserted" : "error";
		}
	}
	wp_die();
}
function wpsp_UpdateClass()
{
	if (!isset($_POST['caction_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['caction_nonce']) , 'ClassAction'))
	{
		echo "Unauthorized Submission";
		exit;
	}
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_class_table = $wpdb->prefix . "wpsp_class";
	$class_name = sanitize_text_field($_POST['Name']);
	$class_number = intval($_POST['Number']);
	$class_teacher = intval($_POST['ClassTeacherID']);
	$class_location = sanitize_text_field($_POST['Location']);
	$start_date = wpsp_StoreDate(sanitize_text_field($_POST['Sdate']));
	$end_date = wpsp_StoreDate(sanitize_text_field($_POST['Edate']));
	$classfeetype = sanitize_text_field($_POST['classfeetype']);
	$cid = intval($_POST['cid']);
	if (wpsp_IsNameExist($wpsp_class_table, 'c_name', $class_name, 'cid', $cid, true) == true)
	{
		echo "Class name exists!";
		exit;
	}
	else
	{
		$class_data = array(
			'c_numb' => $class_number,
			'c_name' => $class_name,
			'teacher_id' => $class_teacher,
			'c_loc' => $class_location,
			'c_sdate' => $start_date,
			'c_edate' => $end_date,
			'c_fee_type' => $classfeetype,
			'c_capacity' => sanitize_text_field($_POST['capacity'])
		);
		$wpsp_class_upd = $wpdb->update($wpsp_class_table, $class_data, array(
			'cid' => $cid
		));

		if ($wpsp_class_upd)
		{
			do_action('wpsp_class_updated', intval($cid) , $class_data);
		}
		echo "updated";
	}
	wp_die();
}
function wpsp_GetClass()
{
	global $wpdb;
	$ctable = $wpdb->prefix . "wpsp_class";
	$cid = esc_sql($_POST['cid']);
	$clinfo = $wpdb->get_row("select * from $ctable where cid='$cid'", ARRAY_A);
	$clinfo['c_sdate'] = wpsp_ViewDate($clinfo['c_sdate']);
	$clinfo['c_edate'] = wpsp_ViewDate($clinfo['c_edate']);
	if (!empty($clinfo)) echo json_encode($clinfo);
	else echo "false";
	wp_die();
}
function wpsp_Updateregisterdeactive()
{
	wpsp_Authenticate();
	global $wpdb;
	 $temp_tbl = $wpdb->prefix . "wpsp_temp";
	$cid = esc_sql($_POST['cid']);
	$temp_data = array(
		't_active' => '0'
	);

	$delcl = $wpdb->update($temp_tbl, $temp_data, array('t_id' => $cid));

	if ($delcl) {
	echo "success";
	$userdetails = $wpdb->get_row("select * from $temp_tbl where t_id='$cid'", ARRAY_A);
	$msg = 'Hi ' . $userdetails['t_name'];
	$msg.= '<br /><br />Unfortunately your registration is not confirmed by the school. You can directly consult school for this.<br /><br />';
	$msg.= 'Regards,<br />' . get_bloginfo('name');
	wpsp_send_mail($userdetails['t_email'], 'Registration Denied', $msg);
	}
	else
	{
	echo "Something went wrong!";
	}
	wp_die();
}
function  wpsp_Updateregisteractive()
{

	wpsp_Authenticate();
	global $wpdb;
	 $temp_tbl = $wpdb->prefix . "wpsp_temp";
	 $wpsp_student_user = $wpdb->prefix . "users";
	 $wpsp_student_table = $wpdb->prefix . "wpsp_student";
	 $wpsp_teacher_table = $wpdb->prefix . "wpsp_teacher";
	$cid = esc_sql($_POST['cid']);

		$userdetails = $wpdb->get_row("select * from $temp_tbl where t_id='$cid'", ARRAY_A);

		if($userdetails['t_type'] == "student"){

			$userInfo = array(
			'user_login' => $userdetails['t_username'],
			'user_pass' => sanitize_text_field($userdetails['t_password']),
			'user_nicename' => sanitize_text_field($userdetails['t_name']),
			'first_name' => $userdetails['t_name'],
			'user_email' => $userdetails['t_email'],
			'role' => 'student'
			);

			$user_id = wp_insert_user($userInfo);

			 	if (!is_wp_error($user_id))
				{
					$studenttable = array(
						'wp_usr_id' => $user_id,
						'parent_wp_usr_id' => '',
						'class_id' => '',
						's_rollno' => '',
						's_fname' => $userdetails['t_name'],
						's_mname' => '',
						's_lname' => '',
						's_zipcode' => '',
						's_country' => '',
						's_gender' => '',
						's_address' => '',
						's_bloodgrp' => '',
						's_dob' => '',
						's_doj' => '',
						's_phone' => '',
						'p_fname' => '',
						'p_mname' => '',
						'p_lname' => '',
						'p_gender' => '',
						'p_edu' => '',
						'p_profession' => '',
						's_paddress' => '',
						'p_bloodgrp' => '',
						's_city' => '',
						's_pcountry' => '',
						's_pcity' => '',
						's_pzipcode' => '',
						'p_phone'  => ''
					);
					$msg = 'Hi ' . $userdetails['t_name'];
					$msg.= '<br /><br />Congratulations, your registration is confirmed. You can fill the remaining details after logging in to your dashboard.<br /><br />';
					$msg.= 'Your Login details are below.<br />';
					$msg.= 'Your User Name is : ' . $userdetails['t_email'] . '<br />';
					$msg.= 'Your Password is : ' . sanitize_text_field($userdetails['t_password']) . '<br /><br />';
					$msg.= 'Please <a href="' . site_url() . '/sch-dashboard">click here </a>to loggin.<br /><br />';
					$msg.= 'You will be notified when a class is assigned to you.<br /><br />';
					$msg.= 'Regards,<br />' . get_bloginfo('name');
					wpsp_send_mail($userdetails['t_email'], 'Student Registration Confirmation', $msg);
					$sp_stu_ins = $wpdb->insert($wpsp_student_table, $studenttable);
					if ($sp_stu_ins)
					{
						do_action('wpsp_student_created', $user_id, $studenttable);
					}
					// send registration mail
					//wpsp_send_user_register_mail($userInfo, $user_id);
					$msg = $sp_stu_ins ? "success" : "Oops! Something went wrong try again.";


					$delrequest = $wpdb->delete($temp_tbl, array(
						't_id' => $cid
					));
					if ($delrequest) echo "success";
					else echo "Something went wrong!";
				}
				else
				if (is_wp_error($user_id))
				{
					$msg = $user_id->get_error_message();
				}
				echo $msg;

		}
		elseif($userdetails['t_type'] == "teacher"){

			$userInfo = array(
			'user_login' => $userdetails['t_username'],
			'user_pass' => sanitize_text_field($userdetails['t_password']),
			'user_nicename' => sanitize_text_field($userdetails['t_name']),
			'first_name' => $userdetails['t_name'],
			'user_email' => $userdetails['t_email'],
			'role' => 'teacher'
			);

			$user_id = wp_insert_user($userInfo);

			 	if (!is_wp_error($user_id))
				{
					$teachertable = array(
						'wp_usr_id' => $user_id,
						'first_name' => $userdetails['t_name'],
						'middle_name' => '',
						'last_name' => '',
						'address' => '',
						'city' => '',
						'country' => '',
						'zipcode' => '',
						'empcode' => '',
						'dob' => '',
						'doj' => '',
						'dol' =>  '',
						'whours' => '',
						'phone' => '',
						'qualification' => '',
						'gender' => '',
						'bloodgrp' => '',
						'position' => ''
					);
					$msg = 'Hi ' . $userdetails['t_name'];
					$msg.= '<br /><br />Congratulations, your registration is confirmed. You can fill the remaining details after logging in to your dashboard.<br /><br />';
					$msg.= 'Your Login details are below.<br />';
					$msg.= 'Your User Name is : ' . $userdetails['t_email'] . '<br />';
					$msg.= 'Your Password is : ' . sanitize_text_field($userdetails['t_password']) . '<br /><br />';
					$msg.= 'Please<a href="' . site_url() . '/sch-dashboard">click here </a>to loggin.<br /><br />';
					$msg.= 'Regards,<br />' . get_bloginfo('name');
					wpsp_send_mail($userdetails['t_email'], 'Teacher Registration Confirmation', $msg);
					$sp_stu_ins = $wpdb->insert($wpsp_teacher_table, $teachertable);
					if ($sp_stu_ins)
					{
						do_action('wpsp_teacher_created', $user_id, $teachertable);
					}
					// send registration mail
					//wpsp_send_user_register_mail($userInfo, $user_id);
					$msg = $sp_stu_ins ? "success" : "Oops! Something went wrong try again.";


					$delrequest = $wpdb->delete($temp_tbl, array(
						't_id' => $cid
					));
					if ($delrequest) echo "success";
					else echo "Something went wrong!";
				}
				else
				if (is_wp_error($user_id))
				{
					$msg = $user_id->get_error_message();
				}
				echo $msg;

		}

}
function wpsp_bulkdisaproverequest()
{
	wpsp_Authenticate();
	global $wpdb;
	 $temp_tbl = $wpdb->prefix . "wpsp_temp";
	$uids = explode(',', sanitize_text_field($_POST['UID']));
	$temp_data = array(
			't_active' => '0'
		);


	foreach ($uids as $cid) {

		$delcl = $wpdb->update($temp_tbl, $temp_data, array('t_id' => $cid));

		if ($delcl) {

		$userdetails = $wpdb->get_row("select * from $temp_tbl where t_id='$cid'", ARRAY_A);
		$msg = 'Hi ' . $userdetails['t_name'];
		$msg.= '<br /><br />Unfortunately your registration is not confirmed by the school. You can directly consult school for this.<br /><br />';
		$msg.= 'Regards,<br />' . get_bloginfo('name');
		wpsp_send_mail($userdetails['t_email'], 'Registration Denied', $msg);
		}
		else
		{
		echo "Something went wrong!";
		}
	}
	if ($delcl) {echo "success";}

	wp_die();
}

function wpsp_bulkaproverequest()
{
	wpsp_Authenticate();
	global $wpdb;
	 $temp_tbl = $wpdb->prefix . "wpsp_temp";
	 $wpsp_student_user = $wpdb->prefix . "users";
	 $wpsp_student_table = $wpdb->prefix . "wpsp_student";
	 $wpsp_teacher_table = $wpdb->prefix . "wpsp_teacher";
	 $uids = explode(',', sanitize_text_field($_POST['UID']));

	 foreach ($uids as $cid) {

		$userdetails = $wpdb->get_row("select * from $temp_tbl where t_id='$cid'", ARRAY_A);

		if($userdetails['t_type'] == "student"){

			$userInfo = array(
			'user_login' => $userdetails['t_username'],
			'user_pass' => sanitize_text_field($userdetails['t_password']),
			'user_nicename' => sanitize_text_field($userdetails['t_name']),
			'first_name' => $userdetails['t_name'],
			'user_email' => $userdetails['t_email'],
			'role' => 'student'
			);

			$user_id = wp_insert_user($userInfo);

			 	if (!is_wp_error($user_id))
				{
					$studenttable = array(
						'wp_usr_id' => $user_id,
						'parent_wp_usr_id' => '',
						'class_id' => '',
						's_rollno' => '',
						's_fname' => $userdetails['t_name'],
						's_mname' => '',
						's_lname' => '',
						's_zipcode' => '',
						's_country' => '',
						's_gender' => '',
						's_address' => '',
						's_bloodgrp' => '',
						's_dob' => '',
						's_doj' => '',
						's_phone' => '',
						'p_fname' => '',
						'p_mname' => '',
						'p_lname' => '',
						'p_gender' => '',
						'p_edu' => '',
						'p_profession' => '',
						's_paddress' => '',
						'p_bloodgrp' => '',
						's_city' => '',
						's_pcountry' => '',
						's_pcity' => '',
						's_pzipcode' => '',
						'p_phone'  => ''
					);
					$msg = 'Hi ' . $userdetails['t_name'];
					$msg.= '<br /><br />Congratulations, your registration is confirmed. You can fill the remaining details after logging in to your dashboard.<br /><br />';
					$msg.= 'Your Login details are below.<br />';
					$msg.= 'Your User Name is : ' . $userdetails['t_email'] . '<br />';
					$msg.= 'Your Password is : ' . sanitize_text_field($userdetails['t_password']) . '<br /><br />';
					$msg.= 'Please <a href="' . site_url() . '/sch-dashboard">click here </a>to loggin.<br /><br />';
					$msg.= 'You will be notified when a class is assigned to you.<br /><br />';
					$msg.= 'Regards,<br />' . get_bloginfo('name');
					wpsp_send_mail($userdetails['t_email'], 'Student Registration Confirmation', $msg);
					$sp_stu_ins = $wpdb->insert($wpsp_student_table, $studenttable);
					if ($sp_stu_ins)
					{
						do_action('wpsp_student_created', $user_id, $studenttable);
					}
					// send registration mail
					//wpsp_send_user_register_mail($userInfo, $user_id);
					$msg = $sp_stu_ins ? "success" : "Oops! Something went wrong try again.";


					$delrequest = $wpdb->delete($temp_tbl, array(
						't_id' => $cid
					));
					if ($delrequest) echo "success";
					else echo "Something went wrong!";
				}
				else
				if (is_wp_error($user_id))
				{
					$msg = $user_id->get_error_message();
				}
				echo $msg;

		}
		elseif($userdetails['t_type'] == "teacher"){

			$userInfo = array(
			'user_login' => $userdetails['t_username'],
			'user_pass' => sanitize_text_field($userdetails['t_password']),
			'user_nicename' => sanitize_text_field($userdetails['t_name']),
			'first_name' => $userdetails['t_name'],
			'user_email' => $userdetails['t_email'],
			'role' => 'teacher'
			);

			$user_id = wp_insert_user($userInfo);

			 	if (!is_wp_error($user_id))
				{
					$teachertable = array(
						'wp_usr_id' => $user_id,
						'first_name' => $userdetails['t_name'],
						'middle_name' => '',
						'last_name' => '',
						'address' => '',
						'city' => '',
						'country' => '',
						'zipcode' => '',
						'empcode' => '',
						'dob' => '',
						'doj' => '',
						'dol' =>  '',
						'whours' => '',
						'phone' => '',
						'qualification' => '',
						'gender' => '',
						'bloodgrp' => '',
						'position' => ''
					);
					$msg = 'Hi ' . $userdetails['t_name'];
					$msg.= '<br /><br />Congratulations, your registration is confirmed. You can fill the remaining details after logging in to your dashboard.<br /><br />';
					$msg.= 'Your Login details are below.<br />';
					$msg.= 'Your User Name is : ' . $userdetails['t_email'] . '<br />';
					$msg.= 'Your Password is : ' . sanitize_text_field($userdetails['t_password']) . '<br /><br />';
					$msg.= 'Please <a href="' . site_url() . '/sch-dashboard">click here </a>to loggin.<br /><br />';
					$msg.= 'Regards,<br />' . get_bloginfo('name');
					wpsp_send_mail($userdetails['t_email'], 'Teacher Registration Confirmation', $msg);
					$sp_stu_ins = $wpdb->insert($wpsp_teacher_table, $teachertable);
					if ($sp_stu_ins)
					{
						do_action('wpsp_teacher_created', $user_id, $teachertable);
					}
					// send registration mail
					//wpsp_send_user_register_mail($userInfo, $user_id);
					$msg = $sp_stu_ins ? "success" : "Oops! Something went wrong try again.";


					$delrequest = $wpdb->delete($temp_tbl, array(
						't_id' => $cid
					));
					if ($delrequest) echo "success";
					else echo "Something went wrong!";
				}
				else
				if (is_wp_error($user_id))
				{
					$msg = $user_id->get_error_message();
				}
				echo $msg;

		}
	 }
	wp_die();
}

function wpsp_DeleteClass()
{
	wpsp_Authenticate();
	global $wpdb;
	$class_tbl = $wpdb->prefix . "wpsp_class";
	$cid = esc_sql($_POST['cid']);
	do_action('wpsp_class_delete', intval($cid));
	$delcl = $wpdb->delete($class_tbl, array(
		'cid' => $cid
	));
	if ($delcl) echo "success";
	else echo "Something went wrong!";
	wp_die();
}
function wpsp_ClassList()
{
	global $wpdb;
	$class_tbl = $wpdb->prefix . "wpsp_class";
	$classes = $wpdb->get_results("select cid,c_name,c_edate,c_capacity from $class_tbl", ARRAY_A);
	return $classes;
}
function wpsp_GetClassName($class_id)
{
	global $wpdb;
	$class_tbl = $wpdb->prefix . "wpsp_class";
	$classes = $wpdb->get_row("select c_name from $class_tbl where cid='$class_id'", ARRAY_A);
	echo $classes['c_name'];
}
function wpsp_GetClassYear()
{
	global $wpdb;
	$class_id = intval($_POST['cid']);
	$class_tbl = $wpdb->prefix . "wpsp_class";
	$cl_year = $wpdb->get_row("select c_sdate,c_edate from $class_tbl where cid='$class_id'", ARRAY_A);
	$cl_year['c_sdate'] = wpsp_ViewDate($cl_year['c_sdate']);
	$cl_year['c_edate'] = wpsp_ViewDate($cl_year['c_edate']);
	echo json_encode($cl_year);
	wp_die();
}
/* Exam Functions */
function wpsp_AddExam()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_exam_table = $wpdb->prefix . "wpsp_exam";
	$exam_name = sanitize_text_field(trim($_POST['ExName']));
	$exam_start = sanitize_text_field($_POST['ExStart']);
	$exam_end = sanitize_text_field($_POST['ExEnd']);
	$subjectlist = implode(",", $_POST['subjectid']);
	if (wpsp_IsNameExist($wpsp_exam_table, 'e_name', $exam_name, "", "", true) == true)
	{
		echo "Name Exists!";
	}
	else
	{
		$exam_data = array(
			'e_name' => $exam_name,
			'subject_id' => $subjectlist,
			'classid' => sanitize_text_field($_POST['class_name']) ,
			'e_s_date' => wpsp_StoreDate($exam_start) ,
			'e_e_date' => wpsp_StoreDate($exam_end)
		);
		$wpsp_exam_ins = $wpdb->insert($wpsp_exam_table, $exam_data);
		if ($wpsp_exam_ins)
		{
			do_action('wpsp_exam_created', $wpdb->insert_id, $exam_data);
		}
		if ($wpsp_exam_ins) echo "success";
		else echo "error";
	}
	wp_die();
}
function wpsp_UpdateExam()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_exam_table = $wpdb->prefix . "wpsp_exam";
	$exam_name = sanitize_text_field($_POST['ExName']);
	$exam_start = sanitize_text_field($_POST['ExStart']);
	$exam_end = sanitize_text_field($_POST['ExEnd']);
	$eid = intval($_POST['ExamID']);
	$subjectlist = implode(",", sanitize_price_array($_POST['subjectid']));
	$exam_data = array(
		'e_name' => $exam_name,
		'classid' => sanitize_text_field($_POST['class_name']) ,
		'subject_id' => $subjectlist,
		'e_s_date' => date('Y-m-d', strtotime($exam_start)) ,
		'e_e_date' => date('Y-m-d', strtotime($exam_end))
	);
	$wpsp_exam_ins = $wpdb->update($wpsp_exam_table, $exam_data, array(
		'eid' => $eid
	));
	if ($wpsp_exam_ins)
	{
		do_action('wpsp_exam_updated', intval($eid) , $exam_data);
	}
	if ($wpsp_exam_ins)
	{
		echo "updated";
	}
	else
	{
		echo "error";
	}
	wp_die();
}
function wpsp_DeleteExam()
{
	wpsp_Authenticate();
	global $wpdb;
	$eid = intval($_POST['eid']);
	$exam_tbl = $wpdb->prefix . "wpsp_exam";
	do_action('wpsp_exam_delete', intval($eid));
	$exam_del = $wpdb->delete($exam_tbl, array(
		'eid' => $eid
	));
	if ($exam_del) echo "deleted";
	else echo "Not deleted! Pls try again.";
	wp_die();
}
function wpsp_ExamInfo()
{
	global $wpdb;
	$etable = $wpdb->prefix . "wpsp_exam";
	$eid = esc_sql($_POST['eid']);
	$exinfo = $wpdb->get_row("select * from $etable where eid='$eid'");
	$stable = $wpdb->prefix . "wpsp_subject";
	$html = '';
	if (!empty($exinfo))
	{
		$exinfo->e_s_date = wpsp_ViewDate($exinfo->e_s_date);
		$exinfo->e_e_date = wpsp_ViewDate($exinfo->e_e_date);
		$subject_list = explode(",", $exinfo->subject_id);
		$wpsp_subjects = $wpdb->get_results("select * from $stable where class_id=$exinfo->classid");
		foreach($wpsp_subjects as $subjectlist)
		{
			$checked = in_array($subjectlist->id, $subject_list) ? 'checked=checked' : '';
			$html.= '<input ' . $checked . ' type="checkbox" name="subjectid[]" value="' . $subjectlist->id . '" class="exam-subjects wpsp-checkbox" id="subject-' . $subjectlist->id . '"><label for="subject-' . $subjectlist->id . '">' . $subjectlist->sub_name . '</label>';

		}
		$exinfo->sub_list = $html;
		echo json_encode($exinfo);
	}
	else echo "false";
	wp_die();
}
function wpsp_StudentCount()
{
	global $wpdb;
	$classid = intval($_POST['cid']);
	$student_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$stl = $wpdb->get_results("select COUNT(*) as cscount from $student_table where class_id='$classid'");
	$count = $stl[0]->cscount;
	if ($count == '0')
	{
		$cl_del = $wpdb->delete($class_table, array(
			'cid' => $classid
		));
		if ($cl_del) echo "deleted";
		else echo "Something went wrong! Try again.";
	}
	else
	{
		echo "There are " . $count . " students associated with this class.";
	}
	wp_die();
}
/**************************** Attendance Functions ***********************/
function wpsp_getStudentsList()
{
	global $wpdb;
	$classid = intval($_POST['classid']);
	$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['date'])));
	$show_date = wpsp_ViewDate($entry_date);
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$student_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$check_date = $wpdb->get_row("SELECT * FROM $class_table WHERE cid=$classid");

	$startdate = isset($check_date->c_sdate) && !empty($check_date->c_sdate) ? strtotime($check_date->c_sdate) : '';
	$enddate = isset($check_date->c_edate) && !empty($check_date->c_edate) ? strtotime($check_date->c_edate) : '';
	$classname = isset($check_date->c_name) ? $check_date->c_name : '';
	$selected = strtotime($_POST['date']);
	if (!empty($startdate) && !empty($enddate))
	{
		if ($startdate <= $selected && $enddate >= $selected)
		{
		}
		else
		{
			$msg = __(sprintf('You have selected wrong date, your class startdate is %s and enddate %s', $check_date->c_sdate, $check_date->c_edate) , 'WPSchoolPress');
			$response['status'] = 0;
			$response['msg'] = $msg;
			echo json_encode($response);
			exit();
		}
	}

	$check_attend = $wpdb->get_row("SELECT ab.absents,ab.aid,cls.c_name FROM $att_table ab LEFT JOIN $class_table cls ON cls.cid=ab.class_id  WHERE ab.class_id=$classid and ab.date = '$entry_date'");


	$ex_absents = array();
	$title = __('New Attendance Entry', 'WPSchoolPress');
	$warning = $nil = '';
	if ($check_attend)
	{
		$title = __('Update Attendance Entry', 'WPSchoolPress');
		$warning = __('Already attendance were entered!', 'WPSchoolPress');
		if ($check_attend->absents != 'Nil')
		{
			$abs = json_decode($check_attend->absents);
			foreach($abs as $ab)
			{
				$ex_absents[$ab->sid] = $ab->reason;
			}
		}
		else
		if ($check_attend->absents == 'Nil')

		{
			$nil = 'checked';
		}
	}

	$stl = [];

	// $stl = $wpdb->get_results("select wp_usr_id,s_rollno ,CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name from $student_table where class_id='$classid' order by s_rollno ASC");
	 $st_array = $wpdb->get_results("select sid, class_id from $student_table");

	 foreach ($st_array as $st) {
		 if(is_numeric($st->class_id) ){
			 if($st->class_id == $classid){
			 	$stl[] = $st->sid;
		 	}
		 }else{
			  $class_id_array = unserialize($st->class_id);
				if(in_array($classid, $class_id_array)){
					$stl[] = $st->sid;
				}
		 }
	 }

	$content = '		<h3 class="wpsp-card-title">' . $title . '</h3>
					<div class="wpsp-row">
					<div class="wpsp-col-md-12">
                    <form name="AttendanceEntryForm" id="AttendanceEntryForm" method="post" class="form-horizontal">
                        <div class="box-body">
                        <p><span class="wpsp-text-red">' . $warning . '</span></p>
                        <div>
                            <input type="hidden" value="' . $entry_date . '" name="AttendanceDate">
                            <input type="hidden" value="' . $classid . '" name="AttendanceClass">
                            <input type="checkbox" class="checkAll ccheckbox wpsp-checkbox" ' . $nil . ' name="Nil" value="Nil"> <span class="wpsp-text-green MRTen" style="
								vertical-align: middle;
							">All Present</span>
							';
							$content.= wp_nonce_field('StudentAttendance', 'sattendance_nonce', '', false) . '
                            </div>
                            <div class="wpsp-form-group">
                            <table class="wpsp-table wpsp-table-bordered" id="addAttendanceTable" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr><td colspan="5">
									<span class="pull-left">Class: <span class="wpsp-f500">' . $classname . '</span></span><span class="pull-right">Date: <span class="wpsp-f500">' . $show_date . '</span></span></td>
									</tr>
                                    <tr>
										<th>#</th>
										<th>Roll No.</th>
										<th>Name </th>
										<th>Absent </th>
										<th>Reason </th>
									</tr>
                                </thead>
                                <tbody>';
								$sno = 1;
								foreach($stl as $sid)
								{
									$st = $wpdb->get_row("select wp_usr_id,s_rollno ,CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name from $student_table where sid='$sid' order by s_rollno ASC");

									$checked = array_key_exists($st->wp_usr_id, $ex_absents) ? 'checked' : '';
									$content.= '<tr>
												<td>' . $sno . '</td>
												<td>' . $st->s_rollno . '</td>
												<td>' . $st->full_name . '</td>
												<td><input type="checkbox" ' . $checked . ' class="ccheckbox wpsp-checkbox" name="absent[]" value="' . $st->wp_usr_id . '"> Absent </td>
												<td><input type="text"  name="reason[' . $st->wp_usr_id . ']" value="' . stripslashes($ex_absents[$st->wp_usr_id]) . '" class="wpsp-form-control"></td>
											</tr>';
									$sno++;
								}
							$content.=
								'</tbody>
                            </table>
                            </div>
                            <div id="formresponse"></div>
                    </div>
                    <div class="box-footer">
                        <div class="wpsp-form-group">
                            <button id="AttendanceSubmit" class="wpsp-btn wpsp-btn-success">Submit</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
            </div>
';
	$response['status'] = 1;
	$response['msg'] = $content;
	echo json_encode($response);
	wp_die();
}

function wpsp_AttendanceEntry()
{
	if (!isset($_POST['sattendance_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['sattendance_nonce']) , 'StudentAttendance'))
	{
		echo "Unauthorized Submission";
		exit;
	}
	wpsp_Authenticate();
	global $wpdb, $wpsp_settings_data;
	$class = sanitize_text_field($_POST['AttendanceClass']);
	$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['AttendanceDate'])));
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$stud_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$check_attend = $wpdb->get_row("SELECT * FROM $att_table WHERE class_id= $class and date = '$entry_date'");


	$classname = $wpdb->get_var("SELECT c_name FROM $class_table where cid=$class");
	$previousList = $previoudids = array();
	if (!empty($check_attend) && isset($check_attend->aid))
	{
		$previousList = json_decode($check_attend->absents, true);
		$del = $wpdb->delete($att_table, array(
			'aid' => $check_attend->aid
		));
		$previoudids = array_column($previousList, 'sid', 'sid');
	}
	if (isset($_POST['Nil']) && $_POST['Nil'] == 'Nil')
	{
		$att_data = array(
			'class_id' => $class,
			'absents' => Nil,
			'date' => $entry_date
		);
		$ins_attend = $wpdb->insert($att_table, $att_data);
	}
	else
	{
		$abs = sanitize_price_array($_POST['absent']);
		$reason = sanitize_price_array($_POST['reason']);
		$attend = array();
		foreach($abs as $stid)
		{
			$attend[] = array(
				'sid' => $stid,
				'reason' => $reason[$stid]
			);
			if (isset($wpsp_settings_data['absent_sms_alert']) && $wpsp_settings_data['absent_sms_alert'] == 1)
			{
			//parent absent notification enable
				$studInfo = $wpdb->get_row("SELECT s_phone, s_fname  FROM  $stud_table ws WHERE ws.wp_usr_id=$stid");
				if (isset($studInfo->s_phone) && !empty($studInfo->s_phone))
				{
					$absentreason = 'Your Child ' . $studInfo->s_fname . ' of class ' . $classname . ' is absent on ' . $entry_date . ' for reason ' . $reason[$stid];
					if($wpsp_settings_data['sch_sms_slaneuser']!= ""){
					$status = apply_filters('wpsp_send_notification_msg', false, $studInfo->s_phone, $absentreason);
					} else {
					$status = apply_filters('wpsp_send_notification_msg_twilio', false, $studInfo->s_phone, $absentreason);
				}
				}
			}
		}
		$attendance = json_encode($attend);
		$att1_data = array(
			'class_id' => $class,
			'absents' => $attendance,
			'date' => $entry_date
		);
		$ins_attend = $wpdb->insert($att_table, $att1_data);
	}
	if ($ins_attend)
	{
		$msg = "success";
	}
	else
	{
		$msg = "error";
	}
	echo $msg;
	wp_die();
}
function wpsp_DeleteAttendance()
{
	wpsp_Authenticate();
	global $wpdb;
	$aid = intval($_POST['aid']);
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$del = $wpdb->delete($att_table, array(
		'aid' => $aid
	));
	wp_die();
}
function wpsp_WorkingDays($class)
{
	global $wpdb;
	$class_table = $wpdb->prefix . "wpsp_class";
	$c_dates = $wpdb->get_row("select c_sdate,c_edate from $class_table where cid='$class'");
	$today = date('Y-m-d');
	if ($c_dates->c_edate > $today || $c_dates->c_edate == '' || $c_dates->c_edate == '0000-00-00') $edate = $today;
	else $edate = $c_dates->c_edate;
	if ($c_dates->c_sdate > $today || $c_dates->c_sdate == '' || $c_dates->c_sdate == '0000-00-00') $sdate = $today;
	else $sdate = $c_dates->c_sdate;
	$diff_days = strtotime($edate) - strtotime($sdate);
	$days = floor($diff_days / (60 * 60 * 24));
	return $days;
}
function wpsp_WorkingDates($start_date, $end_date, $class)
{
	global $wpdb;
	$ignore = array();
	$leave_table = $wpdb->prefix . "wpsp_leavedays";
	$ldates = $wpdb->get_results("select leave_date from `$leave_table` where class_id='$class' and leave_date BETWEEN '$start_date' and '$end_date'", ARRAY_A);
	foreach($ldates as $hol)
	{
		array_push($ignore, $hol['leave_date']);
	}
	$iDateFrom = strtotime($start_date);
	$iDateTo = strtotime($end_date);
	$wdays = array();
	$leaves = array();
	while ($iDateFrom < $iDateTo)
	{
		$day_date = date('Y-m-d', $iDateFrom);
		if ((!in_array($day_date, $ignore))) array_push($wdays, $day_date);
		else
		if (in_array($day_date, $ignore)) array_push($leaves, $day_date);
		$iDateFrom+= 86400;
	}
	return array(
		'wdays' => $wdays,
		'leaves' => $leaves
	);
}
function wpsp_AttStatus($sdate, $edate, $class)
{
	global $wpdb;
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$n_attrows = $wpdb->get_row("select count(*) as count from $att_table where date BETWEEN '$sdate' and '$edate'");
	$days_info = wpsp_WorkingDates($sdate, $edate, $class);
	$n_wdays = count($days_info['wdays']);
	$not_entered = $n_wdays - $n_attrows->count;
	return array(
		'wdays' => $n_wdays,
		'not_entered' => $not_entered
	);
}
function wpsp_leaveDates($strDateFrom, $strDateTo, $weeklyOff = array(
	'Sunday'
))
{
	$aryRange = array();
	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2) , substr($strDateFrom, 8, 2) , substr($strDateFrom, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2) , substr($strDateTo, 8, 2) , substr($strDateTo, 0, 4));
	if ($iDateTo >= $iDateFrom)
	{
		while ($iDateFrom < $iDateTo)
		{
			$date = date('Y-m-d', $iDateFrom);
			$day = date('l', strtotime($date));
			if (in_array($day, $weeklyOff))
			{
				array_push($aryRange, $date);
			}
			$iDateFrom+= 86400;
		}
	}
	return $aryRange;
}
function wpsp_GetAttReport()
{
	wpsp_Authenticate();
	echo wpsp_AttReport(intval($_POST['student_id']) , 0);
}
function wpsp_AttReport($st_id, $close = 1)
{
	global $wpdb;
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$st_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$ser = '%' . $st_id . '%';

	$stinfo = $wpdb->get_row("select st.class_id, st.s_rollno , CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name, c.c_name, c.c_sdate, c.c_edate from $st_table st LEFT JOIN $class_table c ON c.cid=st.class_id where st.wp_usr_id='$st_id'");



	if (is_numeric($stinfo->class_id)){
		$classIDArray[] = $stinfo->class_id;
	}else{
		$classIDArray = unserialize($stinfo->class_id);
	}

	$classname_array = [];
	foreach ($classIDArray as $id ) {
		$clasname = $wpdb->get_var("SELECT c_name FROM $class_table where cid=$id");
		$classname_array[] = $clasname;
	}

// print_r($classname_array);die();

	$att_info = $wpdb->get_row("select count(*) as count from $att_table WHERE absents LIKE '$ser'");
	$stinfo->c_edate = wpsp_ViewDate($stinfo->c_edate);
	$stinfo->c_sdate = wpsp_ViewDate($stinfo->c_sdate);
	$loc_avatar = get_user_meta($st_id, 'simple_local_avatar', true);
	$img_url = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/avatar.png';
	$attendance_days = $wpdb->get_results("select *from $att_table where class_id=$stinfo->class_id");
	$present_days = 0;
	foreach($attendance_days as $days => $attendance)
	{
		if ($attendance->absents == 'Nil')
		{
			$present_days++;
		}
		else
		{
			$absents = json_decode($attendance->absents, true);
			if (array_search($st_id, array_column($absents, 'sid')) !== False)
			{
			}
			else
			{
				$present_days++;
			}
		}
	}


	$working_days = $present_days + $att_info->count;
	$content = "<div class='wpsp-panel-body'>
					<div class='wpsp-userpic'>
						 <img src='$img_url' height='150px' width='150px' class='img img-circle'/>
					</div>
					<div class='wpsp-userDetails'>
						<table class='wpsp-table'>
							<tbody>
								<tr>
									<td colspan='2'><strong>Name: </strong>$stinfo->full_name</td>
								</tr>
								<tr>
									<td width='50%'><strong>Class: </strong>".implode(", ",$classname_array)."</td>
									<td width='50%'><strong>Roll No. : </strong>$stinfo->s_rollno</td>
								</tr>
								<tr>
									<td width='50%'><strong>Class Start: </strong> $stinfo->c_sdate </td>
									<td width='50%'><strong>Class End : </strong>$stinfo->c_edate</td>
								</tr>
								<tr>
									<td width='50%'><strong>Number of Absent days: </strong>$att_info->count</td>
									<td width='50%'><strong>Number of Present days: </strong>$present_days</td>
								</tr>
								<tr>
									<td colspan='2'><strong>Number of Attendance days: </strong>$working_days</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>";
	echo $content;
	wp_die();
}
function wpsp_GetAbsentees()
{
	global $wpdb;
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$st_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$cid = intval($_POST['classid']);
	if (isset($_POST['date'])) $date = sanitize_text_field($_POST['date']);
	else $date = date('Y-m-d');
	$show_date = wpsp_ViewDate($date);
	$att_info = $wpdb->get_row("select at.*,cl.c_name from $att_table at LEFT JOIN $class_table cl ON cl.cid=at.class_id where at.class_id='$cid' and at.date='$date'");
	$absents = $att_info->absents;
	$content = "<div class='box box-info'><div class='box-header'>Absentees List </div><div class='box-body'><span><label>Class : </label>" . $att_info->c_name . "</span><span class='pull-right'><label> Date : </label>" . $show_date . "</span><table class='table table-bordered'><thead><th>Student</th><th>Reason</th></thead><tbody>";
	if ($absents != 'Nil')
	{
		$ab_decode = json_decode($absents);
		foreach($ab_decode as $abs)
		{
			$st_id = $abs->sid;
			$st_info = $wpdb->get_row("select CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name from $st_table where wp_usr_id='$st_id'");
			$content.= "<tr><td>" . $st_info->full_name . "</td><td>" . $abs->reason . "</td></tr>";
		}
	}
	$content.= "</tbody></table></div>";
	echo $content;
	wp_die();
}
function wpsp_GetAbsentDates()
{
	global $wpdb;
	$att_table = $wpdb->prefix . "wpsp_attendance";
	$st_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$st_id = intval($_POST['sid']);
	$st_info = $wpdb->get_row("select CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name,cl.c_name from $st_table st LEFT JOIN $class_table cl ON cl.cid=st.class_id where st.wp_usr_id='$st_id'");
	$ser = '%' . $st_id . '%';
	$att_info = $wpdb->get_results("select date, absents from $att_table WHERE absents LIKE '$ser'");
	$absents = array();
	foreach($att_info as $ainfo)
	{
		$ab_decode = json_decode($ainfo->absents);
		foreach($ab_decode as $abs)
		{
			if ($abs->sid == $st_id)
			{
				$absents[] = array(
					'date' => $ainfo->date,
					'reason' => $abs->reason
				);
			}
		}
	}
	$content = "<div class='box box-info'><div class='box-header'>Absent Dates </div><div class='box-body'><span><label>Class : </label>" . $st_info->c_name . "</span><span class='pull-right'><label> Student : </label>" . $st_info->full_name . "</span><table class='table table-bordered'><thead><th>Absent Date</th><th>Reason</th></thead><tbody>";
	foreach($absents as $abd)
	{
		$show_date = wpsp_ViewDate($abd['date']);
		$content.= "<tr><td>" . $show_date . "</td><td>" . $abd['reason'] . "</td></tr>";
	}
	$content.= "</tbody></table></div>";
	echo $content;
	wp_die();
}
/************************** Subject Functions ***************************/
function wpsp_AddSubject()
{
	wpsp_Authenticate();
	global $wpdb;
	$subject_tbl = $wpdb->prefix . "wpsp_subject";
	$code = sanitize_price_array($_POST['SCodes']);
	$subjects = sanitize_price_array($_POST['SNames']);
	$class_id = sanitize_price_array($_POST['SCID']);
	$subject_teacher_id = sanitize_price_array($_POST['STeacherID']);
	$book_name = sanitize_price_array($_POST['BNames']);
	$n = count($subjects);
	$response_msg = '';
	// Get all subject names for this class and check with array instead of query each time
	$c_subjects = $wpdb->get_results("select sub_name from $subject_tbl where class_id=$class_id", ARRAY_A);
	$class_subjects = array();
	if (count($c_subjects) > 0)
	{
		foreach($c_subjects as $sub)
		{
			$class_subjects[] = strtoupper($sub['sub_name']);
		}
	}
	$subj_array = array();
	if ($n > 0)
	{
		for ($i = 0; $i < $n; $i++)
		{
			if ($subjects[$i] != '')
			{
				$sub_name = strtoupper(trim(sanitize_text_field($subjects[$i])));
				if (array_search($sub_name, $class_subjects) === false)
				{
					$c_subjects = array();
					if (!empty($code[$i])) $c_subjects = $wpdb->get_row("select *from $subject_tbl where sub_code=$code[$i] AND class_id=$class_id");

					if (empty($c_subjects)) $subj_array[] = array(
						'sub_code' => $code[$i],
						'class_id' => $class_id,
						'sub_name' => trim(sanitize_text_field($subjects[$i])) ,
						'sub_teach_id' => $subject_teacher_id[$i],
						'book_name' => $book_name[$i]
					);
					else $response_msg = 'Subject Code Already Assigned to another subject	';
				}
				else
				{
					$response_msg = 'Subject Name Already Exists.';
					break;
				}
			}
		}
	}
	else
	{
		echo "Subjects Empty!";
		wp_die();
	}
	if (count($subj_array) > 0 && empty($response_msg))
	{
		foreach($subj_array as $sub_ent)
		{
			$insub = $wpdb->insert($subject_tbl, $sub_ent);
			do_action('wpsp_subject_created', $wpdb->insert_id, $sub_ent);
		}
		$response_msg = $insub ? "success" : "false";
	}
	echo $response_msg;
	wp_die();
}
function wpsp_UpdateSubject()
{
	wpsp_Authenticate();

	global $wpdb;
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$class_id = intval($_POST['ClassID']);
	$srid = sanitize_text_field($_POST['SRowID']);
	$subject_code = sanitize_text_field($_POST['EditSCode']);
	$subject_name = trim(sanitize_text_field($_POST['EditSName']));
	$subject_teacher_id = sanitize_text_field($_POST['EditSTeacherID']);
	$book_name = sanitize_text_field($_POST['EditBName']);
	$check_sub = $wpdb->get_results("select sub_name from $sub_table where UPPER(sub_name)=UPPER('$subject_name') and class_id='$class_id' and id!='$srid'");
	if (count($check_sub) > 0)
	{
		echo "Subject name exists!";
	}
	else
	{
		$subject_data = array(
			'sub_code' => $subject_code,
			'sub_name' => $subject_name,
			'sub_teach_id' => $subject_teacher_id,
			'book_name' => $book_name
		);
		$sub_upd = $wpdb->update($sub_table, $subject_data, array(
			'id' => $srid
		));
		if ($sub_upd)
		{
			do_action('wpsp_subject_updated', intval($srid) , $subject_data);
		}
		if ($sub_upd) echo "updated";
		else echo "No change found";
	}
	wp_die();
}
function wpsp_DeleteSubject()
{
	wpsp_Authenticate();
	global $wpdb;
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$subid = intval($_POST['sid']);
	do_action('wpsp_subject_delete', intval($subid));
	$sub_del = $wpdb->delete($sub_table, array(
		'id' => $subid
	));
	if ($sub_del)
	{
		echo "deleted";
	}
	else
	{
		echo "failed";
	}
	wp_die();
}
function wpsp_SubjectList()
{
	global $wpdb;
	$result = array();
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$examtable = $wpdb->prefix . "wpsp_exam";
	$cid = intval($_POST['ClassID']);
	$result['exam'] = $wpdb->get_results("select eid,e_name,subject_id from $examtable where classid=$cid");
	if (isset($_POST['get_exam_list']) && intval($_POST['get_exam_list']) == 1)
	{
		if (count($result['exam']) == 1)
		{
			$subjectID = $result['exam'][0]->subject_id;
			if (!empty($subjectID))
			{
				$result['subject'] = $wpdb->get_results("SELECT id,sub_name FROM $sub_table WHERE id IN($subjectID)");
			}
		}
	}
	else
	{
		$result['subject'] = $wpdb->get_results("select sub_name,id from $sub_table where class_id=$cid");
	}
	echo json_encode($result);
	wp_die();
}
function wpsp_getMarksubject()
{
	global $wpdb;
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$examtable = $wpdb->prefix . "wpsp_exam";
	$eid = intval($_POST['ExamID']);
	$subjectID = $wpdb->get_var("select subject_id from $examtable where eid=$eid");
	if (!empty($subjectID))
	{
		$result['subject'] = $wpdb->get_results("SELECT id,sub_name FROM $sub_table WHERE id IN($subjectID)");
	}
	echo json_encode($result);
	wp_die();
}
function wpsp_SubjectInfo()
{
	global $wpdb;
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$sid = intval($_POST['sid']);
	$cdat = $wpdb->get_row("select * from $sub_table where id='$sid'");
	echo json_encode($cdat);
	wp_die();
}
function wpsp_GeneralSubjectEntry()
{
	global $wpdb;
	$subjects = sanitize_text_field($_POST['SName']);
	$subject_teacher_id = sanitize_text_field($_POST['STeacherID']);
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$attendance = json_encode($attend);
	$sub_table_data = array(
		'class_id' => 0,
		'sub_name' => $subjects,
		'sub_teach_id' => $subject_teacher_id
	);
	$insub = $wpdb->insert($sub_table, $sub_table_data);
	if ($insub)
	{
		echo "success";
	}
	else
	{
		echo "false";
	}
	wp_die();
}
function wpsp_GensubjectEdit()
{
	global $wpdb;
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$srid = intval($_POST['SRowID']);
	$subject_name = sanitize_text_field($_POST['EditSName']);
	$subject_teacher_id = sanitize_text_field($_POST['EditSTeacherID']);
	$sub_table_update_data = array(
		'sub_name' => $subject_name,
		'sub_teach_id' => $subject_teacher_id
	);
	$sub_upd = $wpdb->update($sub_table, $sub_table_update_data, array(
		'id' => $srid
	));
	if ($sub_upd)
	{
		echo "update";
	}
	else
	{
		echo "fail";
	}
	wp_die();
}
/******************* Time Table ************************/
function wpsp_SaveTimetable()
{
	wpsp_Authenticate();
	$cid = intval($_POST['cid']);
	$tid = intval($_POST['tid']);
	$sid = intval($_POST['sid']);
	$sessionid = intval($_POST['sessionid']);
	$day = sanitize_text_field($_POST['day']);
	global $wpdb;
	// check teacher period exist
	$sub_table = $wpdb->prefix . "wpsp_subject";
	$time_table = $wpdb->prefix . "wpsp_timetable";
	$class_table = $wpdb->prefix . "wpsp_class";
	$subcheck_entry = $wpdb->get_row("SELECT sub_teach_id from $sub_table where id=$sid");
	$techerid = isset($subcheck_entry->sub_teach_id) ? $subcheck_entry->sub_teach_id : '';
	$exityesid = 0;
	$exityescname = '';
	if ($techerid > 0 && !empty($techerid))
	{
		$getsubject = $wpdb->get_results("SELECT id from $sub_table where sub_teach_id=$techerid");
		foreach($getsubject as $subjid)
		{
			$subjeid = $subjid->id;
			$techbook = $wpdb->get_results("SELECT id,(select c_name from $class_table where cid=class_id) as cname from $time_table where day=$day and subject_id=$subjeid and time_id=$tid");
			foreach($techbook as $techcheck)
			{
				$exityesid = $techcheck->id;
				$exityescname = $techcheck->cname;
			}
		}
	}
	if ($exityesid)
	{
		//echo $exityescname . ",";
	}
		$check_entry = $wpdb->get_row("SELECT * from $time_table where class_id=$cid and day=$day and time_id=$tid and session_id=$sessionid ");
	if (count($check_entry) > 0)
	{
		$time_table_update_data = array(
			'subject_id' => $sid,
			'is_active' => 0,
		);
		$upd = $wpdb->update($time_table, $time_table_update_data, array(
			'id' => $check_entry->id
		));
		if ($upd) echo 'updated';
		else echo "fail";
	}
	else
	{
		$time_table_data = array(
			'class_id' => $cid,
			'time_id' => $tid,
			'subject_id' => $sid,
			'session_id' => $sessionid,
			'day' => $day
		);
		$ins = $wpdb->insert($time_table, $time_table_data);
		if ($ins) echo 'true';
		else echo "false";
	}
	wp_die();
}
function wpsp_DeleteTimetable()
{
	wpsp_Authenticate();
	global $wpdb;
	$ttable = $wpdb->prefix . "wpsp_timetable";
	$cid = intval($_POST['cid']);
	$del = $wpdb->delete($ttable, array(
		'class_id' => $cid
	));
	if ($del)
	{
		echo "deleted";
	}
	else
	{
		echo "error";
	}
	wp_die();
}
/*********** Remove class ************/
function wpsp_DeleteTimetablesloat()
{
	wpsp_Authenticate();
	global $wpdb;
	$ttable = $wpdb->prefix . "wpsp_timetable";
	$cid = intval($_POST['cid']);
	$rid = intval($_POST['rid']);
	//die()
$del	=	$wpdb->update($ttable,array('is_active'=>1),array(
		'session_id' => $cid,
		'day' => $rid
	));
	// $del = $wpdb->delete($ttable, array(
	// 	'session_id' => $cid,
	// 	'day' => $rid
	// ));
	if ($del)
	{
		echo "deleted";
	}
	else
	{
		echo "error";
	}
	wp_die();
}

/***** Mark Functions *****/
function wpsp_AddMark(){
	wpsp_Authenticate();
	global $wpdb, $wpsp_settings_data;
	$stclass		=	sanitize_price_array($_POST['ClassID']);
	$stsubject		=	sanitize_price_array($_POST['SubjectID']);
	$stexam			=	sanitize_price_array($_POST['ExamID']);
	$marks			=	sanitize_price_array($_POST['marks']);
	$remarks		=	sanitize_price_array($_POST['remarks']);
	$mark_table		=	$wpdb->prefix."wpsp_mark";
	$exmark_table	=	$wpdb->prefix."wpsp_mark_extract";
	$marklimit		=	isset( $wpsp_settings_data['max_marks'] ) ? $wpsp_settings_data['max_marks'] : 0;
	$msg = '';
	if( wpsp_IsMarkEntered($stclass,$stsubject,$stexam) ) {
		if( isset($_POST['update']) && $_POST['update']=='true' ) {
			foreach($marks as $key=>$mark) {
				//foreach($remarks as $key=>$remarks) {
				if( $marklimit> 0 && $mark[0]>$marklimit)
					$msg = 'Some marks couldn\'t be enterted, Marks limit exceeds';
				else
					$m_upd=$wpdb->update($mark_table,array('mark'=>$mark[0],'remarks'=>$remarks[$key][0]),array('mid'=>$key));
				//}
			}
			foreach(sanitize_price_array($_POST['exmarks']) as $stid=>$field) {
				foreach($field as $flid=>$flmark) {
					$exmrk_chk	=	$wpdb->get_row("select id from $exmark_table where student_id=$stid and exam_id=$stexam and subject_id=$stsubject and field_id=$flid");

					if( $marklimit> 0 && $flmark>$marklimit)
						$msg = 'Some marks couldn\'t be enterted, Marks limit exceeds';
					else {
						if(!empty($exmrk_chk)) {
							if($flmark == ''){
								$flmark = 0;
							}
							$m_upd	=	$wpdb->update($exmark_table,array('mark'=>$flmark),array('id'=>$exmrk_chk->id));

						} else {
							if($flmark == ''){
								$flmark = 0;
							}
							$exmark_data	=	array( 'student_id'=>$stid,'field_id'=>$flid,'exam_id'=>$stexam,'subject_id'=>$stsubject,'mark'=>$flmark );
							$exm_ins		=	$wpdb->insert($exmark_table,$exmark_data);
						}
					}
				}
			}
			if( empty( $msg ) )
				echo "update";
			else
				echo $msg;
		} else {
			echo "false";
		}
	} else {
		foreach($marks as $key=>$mark) {
		//	foreach($remarks as $key=>$remarks) {
			if( $marklimit> 0 && $mark[0]>$marklimit )
				$msg = 'Some marks couldn\'t be enterted, Marks limit exceeds';
			else {
				$mark_data=array('subject_id'=>$stsubject,'class_id'=>$stclass,'student_id'=>$key,'exam_id'=>$stexam,'mark'=>$mark[0],'remarks'=>$remarks[$key][0]);
				$m_ins=$wpdb->insert($mark_table,$mark_data);
			}
		}
		if( !empty( $_POST['exmarks'] ) ) {
			foreach( sanitize_price_array($_POST['exmarks']) as $stid=>$field ) {
				foreach($field as $flid=>$flmark) {
					if( $marklimit> 0 && $mark[0]>$flmark )
						$msg = 'Some marks couldn\'t be enterted, Marks limit exceeds';
					else {
						$exmark_data	=	array( 'student_id'=>$stid,'field_id'=>$flid,'exam_id'=>$stexam,'subject_id'=>$stsubject,'mark'=>$flmark );
						$exm_ins		=	$wpdb->insert($exmark_table,$exmark_data);
					}
				}
			}
		}
		if( !empty( $msg ) ) {
			echo $msg;
		} else if($m_ins) {
			echo "success";
		} else {
			echo "false";
		}
	}
	wp_die();
}

function wpsp_MarkReport($st_id, $c_id){
	global $wpdb;

	$mark_table = $wpdb->prefix . "wpsp_mark";
	$exam_table = $wpdb->prefix . "wpsp_exam";
	$subject_table = $wpdb->prefix . "wpsp_subject";
	$extra_marks_table = $wpdb->prefix . "wpsp_mark_extract";
	$extra_fields = $wpdb->prefix . "wpsp_mark_fields";
	$marks = array();
	$prev_id = $content = '';

	// $all_mark = $wpdb->get_results("select m.subject_id, m.student_id, m.exam_id, m.mark,m.remarks, e.e_name, s.sub_name from $mark_table m LEFT JOIN $exam_table e ON e.eid=m.exam_id LEFT JOIN $subject_table s ON s.id=m.subject_id where m.student_id='$st_id' order by m.exam_id");

	$all_mark = $wpdb->get_results("select m.subject_id, m.student_id, m.exam_id, m.mark,m.remarks, e.e_name, s.sub_name from $mark_table m LEFT JOIN $exam_table e ON e.eid=m.exam_id LEFT JOIN $subject_table s ON s.id=m.subject_id where m.student_id='$st_id' and m.class_id='$c_id' order by m.exam_id");
	foreach($all_mark as $mk)
	{
		$subject_id = $mk->subject_id;
		$exam_id = $mk->exam_id;
		$exam_name = $mk->e_name;
		$extra_marks = $wpdb->get_results("select ex.mark,ef.field_text from $extra_marks_table ex LEFT JOIN $extra_fields ef ON ef.field_id=ex.field_id where ex.subject_id='$subject_id' and ex.exam_id='$exam_id' and ex.student_id=$st_id");
		$extract = array();
		if (!empty($extra_marks))
		{
			foreach($extra_marks as $exm)
			{
				$extract[$exm->field_text] = $exm->mark;
			}
		}
		$m_data = array(
			'subject_name' => (($mk->sub_name == '')? 'N/A' : $mk->sub_name),
			'mark' => (($mk->mark == '')? '0' : $mk->mark),
			'status' => '',
			'extrafield' => $extract,
			'remarks' => (($mk->remarks == '')? '0' : $mk->remarks)
		);
		if ($exam_id != $prev_id)
		{
			$marks[$exam_name] = array();
		}
		array_push($marks[$exam_name], $m_data);
		$prev_id = $exam_id;
	}
	if (count($marks) > 0)
	{

		foreach($marks as $exam_name => $mark)
		{
			$i = 1;
			$content.= '<div class="wpsp-table-responsive"><table class="wpsp-table table-striped table-bordered">
							<thead><span class="label label-info pull-left">' . $exam_name . '</span>
								<tr>
										<th>#</th>
										<th>Subject</th>
										<th>Mark</th>
										<th>Other</th>
										<th>Remarks</th>
								</tr>
							</thead>
						<tbody>';
			foreach($mark as $mrk)
			{
				$extrafield = '';
				foreach($mrk['extrafield'] as $key => $value)
				{
					$extrafield.= '<b>' . $key . "</b> - " . $value . '<br />';
				}
				$content.= '<tr><td>' . $i . '</td><td>' . $mrk['subject_name'] . '</td><td>' . $mrk['mark'] . '</td><td>' . $extrafield . '</td><td>' . $mrk['remarks'] . '</td></tr>';
				$i++;
			}
			$content.= '</tbody></table></div>';
		}
	}
	else
	{
		$content = "<label class='wpsp-text-red'>No Marks available to show!</label>";
	}
	echo $content;
}
/************** Settings *******************/


function wpsp_GenSetting()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_settings_table = $wpdb->prefix . "wpsp_settings";
//$logo = isset($_POST['sch_logo']) ? sanitize_text_field($_POST['sch_logo']) : '';
		if (!empty($_FILES['displaypicture']['name']))
		{
			$mimes = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
           'tif|tiff' => 'image/tiff'
			);
			if (!function_exists('wp_handle_upload')) require_once (ABSPATH . 'wp-admin/includes/file.php');
			$avatar = wp_handle_upload($_FILES['displaypicture'], array(
				'mimes' => $mimes,
				'test_form' => false,
			));
			$logo = isset($avatar['url']) ? $avatar['url'] : $logo;
		}
		else
		{
			$logo = sanitize_text_field($_POST['old_img']);
		}
		$option_value['sch_name'] = sanitize_text_field($_POST['sch_name']);
		$option_value['sch_logo'] = $logo;
		//$option_value['sch_wrkinghrs'] = sanitize_text_field($_POST['sch_wrkinghrs']);
		//$option_value['sch_wrkingyear'] = sanitize_text_field($_POST['sch_wrkingyear']);
		//$option_value['sch_holiday'] = sanitize_text_field($_POST['sch_holiday']);
		$option_value['sch_addr'] = sanitize_text_field($_POST['sch_addr']);
		$option_value['sch_city'] = sanitize_text_field($_POST['sch_city']);
		$option_value['sch_state'] = sanitize_text_field($_POST['sch_state']);
		$option_value['sch_country'] = sanitize_text_field($_POST['country']);
		$option_value['sch_pno'] = sanitize_text_field($_POST['Phone']);
		$option_value['sch_fax'] = intval($_POST['sch_fax']);
		$option_value['sch_email'] = sanitize_email($_POST['email']);
		$option_value['sch_website'] = sanitize_text_field($_POST['sch_website']);
		$option_value['date_format'] = sanitize_text_field($_POST['date_format']);
		$option_value['markstype'] = sanitize_text_field($_POST['markstype']);
		$option_value['absent_sms_alert'] = isset($_POST['absent_sms_alert']) ? 1 : 0;
		$option_value['notification_sms_alert'] = isset($_POST['notification_sms_alert']) ? 1 : 0;
		//$option_value['allow_parent_profile'] = isset($_POST['allow_parent_profile']) ? 1 : 0;

		//print_r($option_value);

	foreach($option_value as $key => $val)
	{
		$check_sett = $wpdb->get_row("Select * from $wpsp_settings_table where option_name='$key'");
		if (empty($check_sett))
		{
			$settings_table_data = array(
				'option_name' => $key,
				'option_value' => $val
			);
			$wpsp_settings_ins = $wpdb->insert($wpsp_settings_table, $settings_table_data);
		}
		else
		{
			$settings_table_update_data = array(
				'option_value' => $val
			);
			$wpsp_settings_upd = $wpdb->update($wpsp_settings_table, $settings_table_update_data, array(
				'option_name' => $key
			));
		}
	}
$optionvalues = array_filter($option_value);
	if (empty($optionvalues))
	{
		echo 'All Fields are blank, Please insert values...';
	}
	else
	{
		echo "success";
	}
	wp_die();
}
function wpsp_GenSettingsocial()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_settings_table = $wpdb->prefix . "wpsp_settings";
	$_POST['type']= 'social';
	if (isset($_POST['type']) && sanitize_text_field($_POST['type']) == 'social')
	{
		$option_value['sfb'] = sanitize_text_field($_POST['sfb']);
		$option_value['stwitter'] = sanitize_text_field($_POST['stwitter']);
		$option_value['sgoogle'] = sanitize_text_field($_POST['sgoogle']);
		$option_value['spinterest'] = sanitize_text_field($_POST['spinterest']);
	}
	foreach($option_value as $key => $val)
	{
		$check_sett = $wpdb->get_row("Select * from $wpsp_settings_table where option_name='$key'");
		if (empty($check_sett))
		{
			$settings_table_data = array(
				'option_name' => $key,
				'option_value' => $val
			);
		$wpsp_settings_ins = $wpdb->insert($wpsp_settings_table, $settings_table_data);
		}
		else
		{
			$settings_table_update_data = array(
				'option_value' => $val
			);
			$wpsp_settings_upd = $wpdb->update($wpsp_settings_table, $settings_table_update_data, array(
				'option_name' => $key
			));
		}
	}
	$optionvalues = array_filter($option_value);
	//print_r($optionvalues);
	//die();
	if (empty($optionvalues))
	{
		echo 'All Fields are blank, Please insert values...';
	}
	else
	{
		echo "success";
	}
wp_die();
}
function wpsp_GenSettinglicensing()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_settings_table = $wpdb->prefix . "wpsp_settings";

		$option_value['importexport'] = sanitize_text_field($_POST['importexport']);
		$option_value['smsaddons'] = sanitize_text_field($_POST['smsaddons']);
		$option_value['feraddons'] = sanitize_text_field($_POST['feraddons']);
		$option_value['ddma'] = sanitize_text_field($_POST['ddma']);
		$option_value['mcaon'] = sanitize_text_field($_POST['mcaon']);


		// $option_value['sgoogle'] = sanitize_text_field($_POST['sgoogle']);
		// $option_value['spinterest'] = sanitize_text_field($_POST['spinterest']);

	foreach($option_value as $key => $val)
	{
		$check_sett = $wpdb->get_row("Select * from $wpsp_settings_table where option_name='$key'");
		if (empty($check_sett))
		{
			$settings_table_data = array(
				'option_name' => $key,
				'option_value' => $val
			);
		$wpsp_settings_ins = $wpdb->insert($wpsp_settings_table, $settings_table_data);
		}
		else
		{
			$settings_table_update_data = array(
				'option_value' => $val
			);
			$wpsp_settings_upd = $wpdb->update($wpsp_settings_table, $settings_table_update_data, array(
				'option_name' => $key
			));
		}
	}
	$optionvalues = array_filter($option_value);
	if (empty($optionvalues))
	{
		echo 'All Fields are blank, Please insert values...';
	}
	else
	{
	echo "success";
	}
wp_die();
}
function wpsp_GenSettingsms()
{
	wpsp_Authenticate();
	global $wpdb;
	$wpsp_settings_table = $wpdb->prefix . "wpsp_settings";
	 $_POST['type']= 'sms';
	//echo $_POST['sch_sms_slaneuser']."adsad";
	//die();
	 if (isset($_POST['type']) && sanitize_text_field($_POST['type']) == 'sms')
	{
		$option_value['sch_sms_slaneapikey'] = $_POST['sch_sms_slaneapikey'];
		$option_value['sch_sms_provider'] = sanitize_text_field($_POST['sch_sms_provider']);
		$option_value['sch_sms_user'] = sanitize_user($_POST['sch_sms_user']);
		$option_value['sch_sms_password'] = sanitize_text_field($_POST['sch_sms_password']);
		$option_value['sch_sms_from_number'] = sanitize_text_field($_POST['sch_sms_from_number']);
		$option_value['sch_sms_slaneuser'] = sanitize_user($_POST['sch_sms_slaneuser']);
		$option_value['sch_sms_slanepassword'] = sanitize_text_field($_POST['sch_sms_slanepassword']);
		$option_value['sch_sms_slanesid'] = sanitize_text_field($_POST['sch_sms_slanesid']);
		$option_value['sch_sms_apikey'] = sanitize_text_field($_POST['sch_sms_apikey']);
		$option_value['twilio_api_sid'] = sanitize_text_field($_POST['twilio_api_sid']);
		$option_value['twilio_api_auth_token'] = sanitize_text_field($_POST['twilio_api_auth_token']);
		$option_value['twilio_api_phone_number'] = sanitize_text_field($_POST['twilio_api_phone_number']);
	}
	/*print_r($option_value);
	die();*/
	foreach($option_value as $key => $val)
	{
		$check_sett = $wpdb->get_row("Select * from $wpsp_settings_table where option_name='$key'");
		if (empty($check_sett))
		{
			$settings_table_data = array(
				'option_name' => $key,
				'option_value' => $val
			);
			$wpsp_settings_ins = $wpdb->insert($wpsp_settings_table, $settings_table_data);
		}
		else
		{
			$settings_table_update_data = array(
				'option_value' => $val
			);
			$wpsp_settings_upd = $wpdb->update($wpsp_settings_table, $settings_table_update_data, array(
				'option_name' => $key
			));
		}
	}
	$optionvalues = array_filter($option_value);
	//print_r($optionvalues);
	//die();
	if (empty($optionvalues))
	{
		echo 'All Fields are blank, Please insert values...';
	}
	else
	{
		echo "success";
	}
	wp_die();
}
function wpsp_ManageGrade()
{
	wpsp_Authenticate();
	global $wpdb;
	$grade_table = $wpdb->prefix . "wpsp_grade";
	if (isset($_POST['actype']) && sanitize_text_field($_POST['actype']) == 'add')
	{
		$grade_table_data = array(
			'g_name' => sanitize_text_field($_POST['grade_name']) ,
			'g_point' => sanitize_text_field($_POST['grade_point']) ,
			'mark_from' => sanitize_text_field($_POST['mark_from']) ,
			'mark_upto' => sanitize_text_field($_POST['mark_upto']) ,
			'comment' => sanitize_text_field($_POST['grade_comment'])
		);
		$grade_status = $wpdb->insert($grade_table, $grade_table_data);
		if ($grade_status)
		{
			echo "success";
		}
	}
	else
	if (isset($_POST['actype']) && sanitize_text_field($_POST['actype']) == 'delete')
	{
		$gid = intval($_POST['grade_id']);
		$grade_status = $wpdb->delete($grade_table, array(
			'gid' => $gid
		));
		echo "success";
	}
	wp_die();
}
function wpsp_AddSubField()
{
	if (!isset($_POST['subfields_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['subfields_nonce']) , 'SubjectFields'))
	{
		echo "Unauthorized Submission";
		exit;
	}
	wpsp_Authenticate();
	global $wpdb;
	$fields_tbl = $wpdb->prefix . "wpsp_mark_fields";
	$subject_id = intval($_POST['SubjectID']);
	$field = sanitize_text_field($_POST['FieldName']);
	if ($subject_id == '' || $field == '')
	{
		echo "Check all fields are entered!";
		exit;
	}
	$check_field = $wpdb->get_results("select * from $fields_tbl where field_text='$field' and subject_id=$subject_id");
	if (empty($check_field))
	{
		$fields_table_data = array(
			'subject_id' => $subject_id,
			'field_text' => $field
		);
		$ins = $wpdb->insert($fields_tbl, $fields_table_data);
	}
	else
	{
		echo "Field already exists! ";
	}
	if ($ins) echo "success";
	else echo "Fields not saved! Pls try again";
	wp_die();
}
function wpsp_DeleteSubField()
{
	wpsp_Authenticate();
	global $wpdb;
	$fields_tbl = $wpdb->prefix . "wpsp_mark_fields";
	$sfid = intval($_POST['sfid']);
	$sfdel = $wpdb->delete($fields_tbl, array(
		'field_id' => $sfid
	));
	if ($sfdel) echo "success";
	else echo "Something went wrong!";
	wp_die();
}
function wpsp_UpdateSubField()
{
	wpsp_Authenticate();
	global $wpdb;
	$fields_tbl = $wpdb->prefix . "wpsp_mark_fields";
	$sfid = intval($_POST['sfid']);
	$field = sanitize_text_field($_POST['field']);
	if ($field == "" || $sfid == 0)
	{
		echo "Check field name and field id!";
	}
	else
	{
		$upd = $wpdb->update($fields_tbl, array(
			'field_text' => $field
		) , array(
			'field_id' => $sfid
		));
		if ($upd)
		{
			echo "success";
		}
		else
		{
			echo "Something went wrong!";
		}
	}
	wp_die();
}
/********* Notify Function ***********/
function wpsp_Notify()
{
	include_once ('wpsp-notify.php');
}
/************ Event Functions **********/
function wpsp_ListEvent()
{
	global $wpdb, $current_user;
	$start = sanitize_text_field($_POST['start']);
	$end = sanitize_text_field($_POST['end']);
	$event_table = $wpdb->prefix . "wpsp_events";
	if ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'teacher')
	{
		$event_list = $wpdb->get_results("select * from $event_table where start >= '$start' and end <='$end'");
	}
	else
	{
		$event_list = $wpdb->get_results("select * from $event_table where type='0' and (start >= '$start' and end <='$end')");
	}
	echo json_encode($event_list);
	wp_die();
}
function wpsp_AddEvent()
{
	wpsp_Authenticate();
	global $wpdb;
	$event_table = $wpdb->prefix . "wpsp_events";
	$stime = sanitize_text_field($_POST['stime']);
	$stime = date("H:i:s", strtotime($stime));
	$etime = date("H:i:s", strtotime(sanitize_text_field($_POST['etime'])));
	$sdate = wpsp_StoreDate(sanitize_text_field($_POST['sdate']));
	$edate = wpsp_StoreDate(sanitize_text_field($_POST['edate']));
	$start = $sdate . ' ' . $stime;
	$end = $edate . ' ' . $etime;
	$event_data = array(
		'start' => $start,
		'end' => $end,
		'type' => sanitize_text_field($_POST['evtype']) ,
		'title' => sanitize_text_field($_POST['evtitle']) ,
		'description' => sanitize_text_field($_POST['evdesc']) ,
		'color' => sanitize_text_field($_POST['evcolor'])
	);
	$event_status = $wpdb->insert($event_table, $event_data);
	if ($event_status)
	{
		do_action('wpsp_event_created', $wpdb->insert_id, $event_data);
	}
	echo "success";
	wp_die();
}
function wpsp_UpdateEvent()
{
	wpsp_Authenticate();
	global $wpdb;
	$event_table = $wpdb->prefix . "wpsp_events";
	if (isset($_POST['evid']) && sanitize_text_field($_POST['evid']) != '')
	{
		$evid = intval($_POST['evid']);
		$stime = sanitize_text_field($_POST['stime']);
		$stime = date("H:i:s", strtotime($stime));
		$etime = date("H:i:s", strtotime(sanitize_text_field($_POST['etime'])));
		$sdate = wpsp_StoreDate(sanitize_text_field($_POST['sdate']));
		$edate = wpsp_StoreDate(sanitize_text_field($_POST['edate']));
		$start = $sdate . ' ' . $stime;
		$end = $edate . ' ' . $etime;
		$event_data = array(
			'start' => $start,
			'end' => $end,
			'type' => sanitize_text_field($_POST['evtype']) ,
			'title' => sanitize_text_field($_POST['evtitle']) ,
			'description' => sanitize_text_field($_POST['evdesc']) ,
			'color' => sanitize_text_field($_POST['evcolor'])
		);
		$event_status = $wpdb->update($event_table, $event_data, array(
			'id' => $evid
		));
		if ($event_status)
		{
			do_action('wpsp_event_updated', intval($evid) , $event_data);
		}
	}
	echo "success";
	wp_die();
}
function wpsp_DeleteEvent()
{
	wpsp_Authenticate();
	global $wpdb;
	$event_table = $wpdb->prefix . "wpsp_events";
	$evid = intval($_POST['evid']);
	do_action('wpsp_event_delete', intval($evid));
	$event_status = $wpdb->delete($event_table, array(
		'id' => $evid
	));
	echo "success";
	wp_die();
}
/**************** Leave Calendar ********************/
function wpsp_AddLeaveDay()
{
	wpsp_Authenticate();
	global $wpdb;
	$leave_table = $wpdb->prefix . "wpsp_leavedays";
	$sdate = wpsp_StoreDate(sanitize_text_field($_POST['spls']));
	$edate = wpsp_StoreDate(sanitize_text_field($_POST['sple']));
	$reason = sanitize_text_field($_POST['splr']);
	$class_id = intval($_POST['ClassID']);
	$avl_dates = $wpdb->get_results("select leave_date from $leave_table where class_id=$class_id");
	$ex_dates = array();
	foreach($avl_dates as $exd)
	{
		if ($exd->leave_date != '') array_push($ex_dates, $exd->leave_date);
	}
	$dates = array();
	if ($edate == '')
	{
		$edate = $sdate;
	}
	if ($sdate == '')
	{
		echo "date missing";
		wp_die();
	}
	else
	if (!is_numeric($class_id))
	{
		echo "Invalid class id";
		wp_die();
	}
	$iDateFrom = mktime(1, 0, 0, substr($sdate, 5, 2) , substr($sdate, 8, 2) , substr($sdate, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($edate, 5, 2) , substr($edate, 8, 2) , substr($edate, 0, 4));
	if ($iDateTo >= $iDateFrom)
	{
		while ($iDateFrom <= $iDateTo)
		{
			array_push($dates, date('Y-m-d', $iDateFrom));
			$iDateFrom+= 86400;
		}
	}
	foreach($dates as $date)
	{
		if (!in_array($date, $ex_dates))
		{
			$leave_table_data = array(
				'class_id' => $class_id,
				'leave_date' => $date,
				'description' => $reason
			);
			$insd = $wpdb->insert($leave_table, $leave_table_data);
		}
	}
	if ($insd) echo "success";
	else echo "Not inserted! Date may exist, pls check";
	wp_die();
}
function wpsp_GetLeaveDays()
{
	global $wpdb, $current_user;
	$cid = intval($_POST['cid']);
	$leave_table = $wpdb->prefix . "wpsp_leavedays";
	$ldays = $wpdb->get_results("select * from $leave_table where class_id='$cid' and leave_date IS NOT NULL");
	$sno = 1;
	echo "<div class='wpsp-row'>
			<div class='wpsp-col-xs-12 wpsp-col-sm-12 wpsp-col-md-12 wpsp-col-lg-12' >
			  <div class='wpsp-panel wpsp-panel-info'>
				<div class='wpsp-panel-heading'>
				  <h3 class='wpsp-panel-title textleft'>Leave Dates</h3>
				</div>
				<div class='wpsp-panel-body'>
				<div class='wpsp-userDetails'>
					<table class='wpsp-table table-user-information'>
					<thead>
					<tr>
						<th>#</th>
						<th>Date</th>
						<th>Description</th>";
						if ($current_user->roles[0] == 'administrator' && $current_user->roles[0] == 'teacher')
						{
							echo "<th>Action</th>";
						}
						echo "</tr>
						</thead>
						<tbody>";
						foreach($ldays as $lday)
						{
							$date = wpsp_ViewDate($lday->leave_date);
							echo "<tr>
									<td>$sno</td>
									<td>$date</td>
									<td>$lday->description</td>";
									if ($current_user->roles[0] == 'administrator' && $current_user->roles[0] == 'teacher')
									{
										echo "<td><span class='text-blue pointer dateDelete' data-id=$lday->id>Delete</td>";
									}
							echo "</tr>";
							$sno++;
						}
				echo "</tbody>
					</table>
				</div>
				</div>
			</div>
		</div>
	</div>";
	wp_die();
}
function wpsp_DeleteLeave()
{
	wpsp_Authenticate();
	global $wpdb;
	$leave_table = $wpdb->prefix . "wpsp_leavedays";
	if (isset($_POST['cid']) && is_numeric($_POST['cid']))
	{
		$ldel = $wpdb->delete($leave_table, array(
			'class_id' => intval($_POST['cid'])
		));
	}
	else
	if (isset($_POST['lid']) && is_numeric($_POST['lid']))
	{
		$ldel = $wpdb->delete($leave_table, array(
			'id' => intval($_POST['lid'])
		));
	}
	if ($ldel) echo "success";
	else echo "fail";
	wp_die();
}
/**************** Transport Functions ***************/
function wpsp_FormValidation($pValues, $rFields)
{
	$error = TRUE;
	foreach($rFields as $field)
	{
		if (!isset($pValues[$field]) || trim($pValues[$field]) == '')
		{
			$error = $field . " is missing";
			break;
		}
	}
	return $error;
}
function wpsp_AddTransport()
{
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		wpsp_Authenticate();
		global $wpdb;
		$trans_table = $wpdb->prefix . "wpsp_transport";
		$validation = wpsp_FormValidation($_POST, ['VhName', 'VhNumb']);
		if ($validation === TRUE)
		{
			$trans_table_data = array(
				'bus_no' => sanitize_text_field($_POST['VhNumb']) ,
				'bus_name' => sanitize_text_field($_POST['VhName']) ,
				'driver_name' => sanitize_text_field($_POST['DrName']) ,
				'bus_route' => sanitize_text_field($_POST['VhRoute']) ,
				'phone_no' => sanitize_text_field($_POST['DrPhone']) ,
				'route_fees' => sanitize_text_field($_POST['route_fees'])
			);
			$ins = $wpdb->insert($trans_table, $trans_table_data);
			if ($ins) echo "success";
			else echo "Data not saved. Something went wrong";
		}
		else
		{
			echo $validation;
		}
	}
	else
	{

		$pl = "";
		$item =  apply_filters( 'wpsp_subject_title_item',array());
		if(isset($item['VhName'])){
					$vname = esc_html($item['VhName'],"WPSchoolPress");
		}else{
			 	$vname = esc_html("Vehicle Name","WPSchoolPress");

		}

		if(isset($item['VhNumb'])){
						$VhNumb = esc_html($item['VhNumb'],"WPSchoolPress");
		}else{
					$VhNumb = esc_html("Vehicle Number","WPSchoolPress");

		}
		if(isset($item['DrName'])){
						$DrName = esc_html($item['DrName'],"WPSchoolPress");
		}else{
					$DrName = esc_html("Driver Name","WPSchoolPress");

		}
		if(isset($item['DrPhone'])){
						$DrPhone = esc_html($item['DrPhone'],"WPSchoolPress");
		}else{
					$DrPhone = esc_html("Driver Phone","WPSchoolPress");

		}
		if(isset($item['route_fees'])){
						$route_fees = esc_html($item['route_fees'],"WPSchoolPress");
		}else{
					$route_fees = esc_html("Route Fees","WPSchoolPress");

		}
		if(isset($item['VhRoute'])){
						$VhRoute = esc_html($item['VhRoute'],"WPSchoolPress");
		}else{
					$VhRoute = esc_html("Vehicle Route","WPSchoolPress");

		}





		$form = '<form name="TransEntryForm" action="#" id="TransEntryForm" method="post">
		<div class="wpsp-row">
					<div class="wpsp-col-xs-12">
						<div class="wpsp-panel-heading">
							<h3 class="wpsp-panel-title">'.apply_filters('wpsp_add_transportation_heading_item',esc_html__('Add Transportation', 'WPSchoolPress' )).'</h3>
						</div>
							<div class="wpsp-card-body">
							<div class="wpsp-row">
										<div class="wpsp-col-md-12">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="Name">'.$vname.'<span class="wpsp-required">*</span></label>
												<input type="hidden"  id="wpsp_locationginal" value='.admin_url().'"/>
												<input type="text" class="wpsp-form-control" ID="VhName" name="VhName" placeholder="'.$vname.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="VhNumber">'.$VhNumb.'<span class="wpsp-required"> *</span></label>
												<input type="text" class="wpsp-form-control select_date" ID="VhNumb" name="VhNumb" placeholder="'.$VhNumb.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="Name">'.$DrName.'</label>
												<input type="text" class="wpsp-form-control" ID="DrName" name="DrName" placeholder="Driver Name">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="Name">'.$DrPhone.'</label>
												<input type="text" class="wpsp-form-control" ID="DrPhone" name="DrPhone" placeholder="'.$DrPhone.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="Name">'.$route_fees.'</label>
												<input type="text" class="wpsp-form-control" ID="route_fees" name="route_fees" placeholder="'.$route_fees.'">
											</div>
										</div>
										<div class="wpsp-col-md-12">
											<div class="wpsp-form-group">
												<label class="wpsp-label" for="Name">'.$VhRoute.'</label>
												<textarea name="VhRoute" class="wpsp-form-control"></textarea>
											</div>
										</div>
										<div class="wpsp-col-md-12">
												<button type="submit" id="TransSubmit" class="wpsp-btn wpsp-btn-success ">Submit</button>

										</div>
									</div>
								</div>
							</div>
							</div>
							</div>
					</form>';
		echo $form;
	}
	wp_die();
}
function wpsp_UpdateTransport()
{
	wpsp_Authenticate();
	global $wpdb;
	$trans_table = $wpdb->prefix . "wpsp_transport";
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		wpsp_Authenticate();
		$validation = wpsp_FormValidation($_POST, ['VhName', 'VhNumb']);
		if ($validation === TRUE){
			$wpdb->update($trans_table, array(
				'bus_no' => sanitize_text_field($_POST['VhNumb']) ,
				'bus_name' => sanitize_text_field($_POST['VhName']) ,
				'driver_name' => sanitize_text_field($_POST['DrName']) ,
				'bus_route' => sanitize_text_field($_POST['VhRoute']) ,
				'phone_no' => sanitize_text_field($_POST['DrPhone']) ,
				'route_fees' => sanitize_text_field($_POST['route_fees'])
			) , array(
				'id' => intval($_POST['transid'])
			));
			echo "success";
		}
		else
		{
			echo $validation;
		}
	}
	else
	{
		$transid = intval($_GET['id']);
		$get_trans = $wpdb->get_row("select * from $trans_table where id='$transid'");
		if (!empty($get_trans))
		{

					$pl = "";
					$item =  apply_filters( 'wpsp_subject_title_item',array());
					if(isset($item['VhName'])){
								$vname = esc_html($item['VhName'],"WPSchoolPress");
					}else{
							$vname = esc_html("Vehicle Name","WPSchoolPress");

					}

					if(isset($item['VhNumb'])){
								$VhNumb = esc_html($item['VhNumb'],"WPSchoolPress");
					}else{
							$VhNumb = esc_html("Vehicle Number","WPSchoolPress");

					}
					if(isset($item['DrName'])){
								$DrName = esc_html($item['DrName'],"WPSchoolPress");
					}else{
							$DrName = esc_html("Driver Name","WPSchoolPress");

					}
					if(isset($item['DrPhone'])){
								$DrPhone = esc_html($item['DrPhone'],"WPSchoolPress");
					}else{
							$DrPhone = esc_html("Driver Phone","WPSchoolPress");

					}
					if(isset($item['route_fees'])){
								$route_fees = esc_html($item['route_fees'],"WPSchoolPress");
					}else{
							$route_fees = esc_html("Route Fees","WPSchoolPress");

					}
					if(isset($item['VhRoute'])){
								$VhRoute = esc_html($item['VhRoute'],"WPSchoolPress");
					}else{
							$VhRoute = esc_html("Vehicle Route","WPSchoolPress");

					}
			$form = '<form name="TransEditForm" action="#" id="TransEditForm" method="post">
					<div class="wpsp-row">
						<div class="wpsp-col-xs-12">
						<div class="wpsp-card">
							<div class="wpsp-panel-heading">
								<h3 class="wpsp-panel-title">Update Transport Information</h3>
							</div>
							<div class="wpsp-card-body">
							<div class="wpsp-row">
										<div class="wpsp-col-md-12">
											<div class="wpsp-form-group">
												<label for="Name">'.$vname.'</label>
												<input type="text" class="wpsp-form-control" value="' . $get_trans->bus_name . '" name="VhName" placeholder="Vehicle Name">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label for="VhNumber">'.$VhNumb.'</label><span class="wpsp-text-red">*</span>
												<input type="text" class="wpsp-form-control select_date" value="' . $get_trans->bus_no . '" name="VhNumb" placeholder="'.$VhNumb.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label for="Name">'.$DrName.'</label><span class="wpsp-text-red">*</span>
												<input type="text" class="wpsp-form-control" value="' . $get_trans->driver_name . '" name="DrName" placeholder="'.$DrName.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label for="Name">'.$DrPhone.'</label><span class="wpsp-text-red">*</span>
												<input type="text" class="wpsp-form-control" value="' . $get_trans->phone_no . '" name="DrPhone" placeholder="'.$DrPhone.'">
											</div>
										</div>
										<div class="wpsp-col-md-6">
											<div class="wpsp-form-group">
												<label for="Name">'.$route_fees.'</label>
												<input type="text" class="wpsp-form-control" ID="route_fees" name="route_fees" placeholder="'.$route_fees.'" value="' . $get_trans->route_fees . '">
											</div>
										</div>
										<div class="wpsp-col-md-12">
											<div class="wpsp-form-group">
												<label for="Name">'.$VhRoute.'</label><span class="wpsp-text-red">*</span>
												<textarea name="VhRoute" class="wpsp-form-control">' . $get_trans->bus_route . '</textarea>
											</div>
										</div>
									<div class="wpsp-col-md-12">
									        <input type="hidden" name="transid" value="' . $get_trans->id . '">
											<button type="submit" class="wpsp-btn wpsp-btn-success" id="TransUpdate">Update</button>

									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					</form>';
			echo $form;
		}
		else
		{
			echo "Can't retrive data from DB!";
		}
	}
	wp_die();
}
function wpsp_ViewTransport()
{
	global $wpdb;
	$trans_table = $wpdb->prefix . "wpsp_transport";
	$transid = intval($_GET['id']);
	$get_trans = $wpdb->get_row("select * from $trans_table where id='$transid'");
	if (!empty($get_trans))
	{
		$content = '<div class="wpsp-panel-heading">
						<h3 class="wpsp-panel-title">Transport Information</h3>
					</div>
		<div class="wpsp-panel-body">
			<div class="wpsp-userDetails">
				<table class="wpsp-table">
					<tbody>
					<tr>
						<td><strong>Vehicle Name: </strong>'. $get_trans->bus_name .' </td>
						<td><strong>Vehicle Number: </strong> '. $get_trans->bus_no .'</td>
					</tr>
					<tr>
						<td><strong>Driver Name: </strong> ' . $get_trans->driver_name . '</td>
						<td><strong>Driver Phone: </strong>  ' . $get_trans->phone_no . '</td>
					</tr>
					<tr>
						<td><strong>Route Fees: </strong> ' . $get_trans->route_fees . '</td>
						<td><strong>Vehicle Route: </strong> ' . $get_trans->bus_route . '</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>';
		echo $content;
	}
	else
	{
		echo "Can't retrive data from DB!";
	}
	wp_die();
}
function wpsp_DeleteTransport()
{
	wpsp_Authenticate();
	global $wpdb;
	$trans_table = $wpdb->prefix . "wpsp_transport";
	$id = intval($_POST['id']);
	$del = $wpdb->delete($trans_table, array(
		'id' => $id
	));
	if ($del) echo 'success';
	wp_die();
}


function wpsp_sendSubMessage(){

	global $wpdb, $current_user;
	$messages_table = $wpdb->prefix . 'wpsp_messages';

	$sender = $current_user->ID;
	$send = '';
	$send_error = '';


	if (isset($_POST['main_m_id']) && !empty($_POST['main_m_id'])){

		$main_m_id = $_POST['main_m_id'];
		$replay_m_id = $_POST['replay_m_id'];
		$reciver_id = $_POST['reciver_id'];
		$re_message = $_POST['message'];

		$message_block = $get_mb = $wpdb->get_row("SELECT * from $messages_table where mid=$main_m_id");

		$receiverid = isset($message_block->r_id) ? $message_block->r_id : '';
		$r_id = isset($message_block->s_id) ? $message_block->s_id : '';
		$subject = isset($message_block->subject) ? $message_block->subject : '';

		$message_data = array(
			's_id' => $sender,
			'r_id' => $reciver_id,
			'subject' => $subject,
			'msg' => $re_message,
			'del_stat' => 0,
			'replay_id'  => $replay_m_id,
			'main_m_id'  => $main_m_id,
		);

		// do_action('wpsp_message_updated', intval($mid) , $message_data);
		$send = $wpdb->insert($messages_table, $message_data);

		if (!empty($subject) && !empty($r_id))
		{
			$receiverInfo = get_user_by('id', $receiverid);
			$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
			if (!empty($receiverEmail))
			{
				wpsp_send_mail($receiverEmail, $subject, sanitize_text_field($_POST['message']));
			}
		}

		if ($send)
		{
			echo "Message sent successfully";
		}
		// echo wpsp_ViewMessage($mid, true);
	}
}



function wpsp_SendMessage(){
	global $wpdb, $current_user;
	$messages_table = $wpdb->prefix . 'wpsp_messages';
	$messages_delete_table = $wpdb->prefix . 'wpsp_messages_delete';
	$sender = $current_user->ID;
	$send = '';
	$send_error = '';


	if (isset($_POST['mid']) && !empty($_POST['mid'])){

		$mid = intval($_POST['mid']);
		$re_message = stripslashes($_POST['message']);

		$message_block = $get_mb = $wpdb->get_row("SELECT * from $messages_table where mid='$mid'");
	//	$delete_msg_count = $wpdb->get_var("select count(*) from $messages_delete_table where m_id = $mid  AND delete_status = 1");

		$receiver_id = isset($message_block->r_id) ? $message_block->r_id : '';
		$sender_id = isset($message_block->s_id) ? $message_block->s_id : '';
		$subject = isset($message_block->subject) ? $message_block->subject : '';
		$messages = json_decode($message_block->msg);

		if($sender == $sender_id){
			$s_id = $sender_id;
			$r_id = $receiver_id;
		}else{
			$r_id = $sender_id;
			$s_id = $receiver_id;
		}

		$wpdb->delete($messages_delete_table, array(
			'm_id' => $mid
		));

		$message_data = array(
			's_id' => $s_id,
			'r_id' => $r_id,
			'subject' => $subject,
			'msg' => $re_message,
			'del_stat' => 0,
			'replay_id'  => $mid,
			'main_m_id'  => $mid,
		);

		// do_action('wpsp_message_updated', intval($mid) , $message_data);
		$wpdb->insert($messages_table, $message_data);

		$lastid = $wpdb->insert_id;

		// $msg_status = $wpdb->update($messages_table, array(
		// 	'replay_id' => $lastid
		// ) , array(
		// 	'mid' => $lastid
		// ));

		if (!empty($subject) && !empty($receiverid))
		{
			$receiverInfo = get_user_by('id', $receiverid);
			$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
			if (!empty($receiverEmail))
			{
				wpsp_send_mail($receiverEmail, $subject, sanitize_text_field(stripslashes($_POST['message'])));
			}
		}
		// echo wpsp_ViewMessage($mid, true);
	}
	else
	{
		if (isset($_POST['group']) && sanitize_text_field($_POST['group']) != '')
		{
			$grp = sanitize_text_field($_POST['group']);
			$subj = sanitize_text_field($_POST['subject']);
			$msg = sanitize_text_field(stripslashes($_POST['message']));


			if ($msg == '')
			{
				$send_error = "Message text Missing!";
			}
			else
			{
				if($grp == 'admins'){
					$role = 'administrator';
				}elseif ($grp == 'parents') {
					$role = 'parent';
				}elseif ($grp == 'students') {
					$role = 'student';
				}elseif ($grp == 'teachers') {
					$role = 'teacher';
				}else{
					$role = '';
				}

				if ($role != '')
				{
					$role_filter = array(
						'role'    => $role,
						'orderby' => 'ID',
						'order'   => 'ASC'
					);

					$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
				  $messages = isset($_POST['message']) ? $_POST['message'] : '';

					$receiver_list = get_users($role_filter);

					foreach($receiver_list as $receiver)
					{
						if($receiver->ID != $sender){
							$message_data = array(
								's_id'      => $sender,
								'r_id'      => $receiver->ID,
								'subject'   => $subject,
								'msg'       => stripslashes($messages) ,
								'del_stat'  => 0,
								'replay_id' => 0,
								'main_m_id' => 0,
							);
							$send = $wpdb->insert($messages_table, $message_data);

							$lastid = $wpdb->insert_id;

							$msg_status = $wpdb->update($messages_table, array(
								'main_m_id' => $lastid
							) , array(
								'mid' => $lastid
							));

							do_action('wpsp_message_created', $wpdb->insert_id, $message_data);
							$receiverEmail = isset($receiver->data->user_email) ? $receiver->data->user_email : '';
							if (!empty($receiverEmail))
							{
								wpsp_send_mail($receiverEmail, $subj, $msg);
							}
						}
					}
				}
				else
				{
					$explode = explode(".", $grp);
					if ($explode[0] == 's')
					{
						/* All Students in a Class */
						$class_id = $explode[1];
						$messages = isset($_POST['message']) ? $_POST['message'] : '';
						$student_table = $wpdb->prefix . "wpsp_student";
						$class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
						$students_list = [];
						$studentData = [];
						$studentData_2 = [];

						$students_list_1 = $wpdb->get_results("select wp_usr_id,class_id from $student_table");
						// $students_list_2 = $wpdb->get_results("SELECT DISTINCT wp_usr_id from $student_table WHERE sid in ( select DISTINCT st.sid from wp_wpsp_student st JOIN $class_mapping_table cm on st.sid=cm.sid WHERE cid=$class_id)");

						foreach ($students_list_1 as $studentdata) {
							if(is_numeric($studentdata->class_id) ){
							if($studentdata->class_id == $class_id){
							 $studentData[] = $studentdata->wp_usr_id;
						 }
						}else{
							 $class_id_array = unserialize($studentdata->class_id);
							 if(in_array($class_id, $class_id_array)){
								$studentData[] = $studentdata->wp_usr_id;
							 }
						}
					}

					// foreach($students_list_2 as $student){
					// 	$studentData_2[] = $student->wp_usr_id;
					// }

					$students_list = array_unique($studentData);
					if(!empty($students_list)){
						foreach($students_list as $student)
						{
							if ($student > 0)
							{
								$message_data = array(
									's_id' => $sender,
									'r_id' => $student,
									'subject' => $subj,
									'msg'     => stripslashes($messages) ,
									'del_stat' => 0,
									'replay_id'  => 0,
									'main_m_id'  => 0,
								);
								$send = $wpdb->insert($messages_table, $message_data);

								$lastid = $wpdb->insert_id;

								$msg_status = $wpdb->update($messages_table, array(
									'main_m_id' => $lastid
								) , array(
									'mid' => $lastid
								));

								do_action('wpsp_message_created', $wpdb->insert_id, $message_data);

								$receiverInfo = get_user_by('id', $student->wp_usr_id);
								$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
								if (!empty($receiverEmail))
								{
									wpsp_send_mail($receiverEmail, $subj, $msg);
								}
							}
						}
					}else{
						echo "Students not available. Please try again!";
					}

					}
					else
					if ($explode[0] == 'p')
					{
						/* All Parents of a Class */
						$class_id = $explode[1];
						$student_table = $wpdb->prefix . "wpsp_student";
						$class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
						$class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
						$messages = isset($_POST['message']) ? $_POST['message'] : '';

						// $parent_list = $wpdb->get_results("select parent_wp_usr_id from $student_table where class_id='$class_id'");
						$parentData_1 = [];
						$parentData_2 = [];
						$parent_list = [];
						$parent_list_1 = $wpdb->get_results("select parent_wp_usr_id,class_id from $student_table where parent_wp_usr_id != 0");
						// $parent_list_2 = $wpdb->get_results("SELECT DISTINCT parent_wp_usr_id from $student_table WHERE sid in ( select DISTINCT st.sid from wp_wpsp_student st JOIN $class_mapping_table cm on st.sid=cm.sid WHERE cid=$class_id)");



						foreach ($parent_list_1 as $parentdata) {
							if(is_numeric($parentdata->class_id) ){
								if($parentdata->class_id == $class_id){
							 	$parentData_1[] = $parentdata->parent_wp_usr_id;
						 	}
						}else{
							 $class_id_array = unserialize($parentdata->class_id);
							 if(in_array($class_id, $class_id_array)){
								$parentData_1[] = $parentdata->parent_wp_usr_id;
							 }
						}
					}

					$parent_list = array_unique($parentData_1);

					if (!empty($parent_list)){
						foreach($parent_list as $parent)
						{
							if ($parent > 0)
							{
									$message_data = array(
										's_id' => $sender,
										'r_id' => $parent,
										'subject' => $subj,
										'msg'     => stripslashes($messages) ,
										'del_stat' => 0,
										'replay_id'  => 0,
										'main_m_id'  => 0,
									);
									$send = $wpdb->insert($messages_table, $message_data);

									$lastid = $wpdb->insert_id;

									$msg_status = $wpdb->update($messages_table, array(
										'main_m_id' => $lastid
									) , array(
										'mid' => $lastid
									));


									do_action('wpsp_message_created', $wpdb->insert_id, $message_data);
									$receiverInfo = get_user_by('id', $parent->parent_wp_usr_id);
									$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
									if (!empty($receiverEmail))
									{
										wpsp_send_mail($receiverEmail, $subj, $msg);
									}
								}
							}
						}	else{
							echo "Parent not available. Please try again!";
						}
					}
					else
					if ($explode[0] == 't')
					{
						/* All teacher of a Class */
						$class_id = $explode[1];
						$class_table = $wpdb->prefix . "wpsp_class";
						$teacher_table = $wpdb->prefix . "wpsp_teacher";
						$messages = isset($_POST['message']) ? $_POST['message'] : '';

						$teacher_list = $wpdb->get_results("select DISTINCT t.wp_usr_id from $teacher_table t JOIN $class_table c on t.wp_usr_id = c.teacher_id where c.cid=$class_id");
						// $teacher_list = $wpdb->get_results("select DISTINCT teacher_id from $class_table where cid=$class_id");
						if(!empty($teacher_list)){
							foreach($teacher_list as $teacher)
							{
								if ($teacher > 0)
								{
									$message_data = array(
										's_id' => $sender,
										'r_id' => $teacher->wp_usr_id,
										'subject' => $subj,
										'msg'     => stripslashes($messages) ,
										'del_stat' => 0,
										'replay_id'  => 0,
										'main_m_id'  => 0,
									);
									$send = $wpdb->insert($messages_table, $message_data);

									$lastid = $wpdb->insert_id;

									$msg_status = $wpdb->update($messages_table, array(
										'main_m_id' => $lastid
									) , array(
										'mid' => $lastid
									));


									do_action('wpsp_message_created', $wpdb->insert_id, $message_data);
									$receiverInfo = get_user_by('id', $parent->parent_wp_usr_id);
									$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
									if (!empty($receiverEmail))
									{
										wpsp_send_mail($receiverEmail, $subj, $msg);
									}
								}
							}
						}else{
								echo "Teacher not available. Please try again!";
						}
					}
					else
					{
						$send_error = "Cannot determine group";
					}
				}
			}
		}
		else
		{
			if (!isset($_POST['r_id']))
			{
				echo 'Please Select Receiver';
			}
			else
			{
				foreach(sanitize_price_array($_POST['r_id']) as $receiver)
				{

					$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
				  $messages = isset($_POST['message']) ? $_POST['message'] : '';

					$new_mblock = array();

					$message_data = array(
						's_id'       => $sender,
						'r_id'       => sanitize_text_field($receiver) ,
						'subject'    => $subject ,
						'msg'        => $messages,
						'del_stat'   => 0,
						'replay_id'  => 0,
						'main_m_id'  => 0,
					);

					$send = $wpdb->insert($messages_table, $message_data);

					$lastid = $wpdb->insert_id;

					$msg_status = $wpdb->update($messages_table, array(
						'main_m_id' => $lastid
					) , array(
						'mid' => $lastid
					));

					do_action('wpsp_message_created', $wpdb->insert_id, $message_data);
					$receiverInfo = get_user_by('id', $receiver);
					$receiverEmail = isset($receiverInfo->data->user_email) ? $receiverInfo->data->user_email : '';
					if (!empty($receiverEmail))
					{
						wpsp_send_mail($receiverEmail, sanitize_text_field($_POST['subject']) , $_POST['message']);
					}
				}
			}
		}
	}
	if ($send)
	{
		echo "Message sent successfully";
	}
	else
	{
		echo $send_error;
	}
	wp_die();
}

function wpsp_ViewMessage($mid = '', $return = false)
{
	global $wpdb, $current_user;
	if ($mid == '')
	{
		$mid = intval($_POST['mid']);
	}

	$msgs_table = $wpdb->prefix . "wpsp_messages";
	$cuid = intval($current_user->ID);
	$get_mrow = $wpdb->get_row("select * from $msgs_table where mid=$mid and (s_id=$cuid || r_id=$cuid)");
	// echo "<pre>";
	// print_r($get_mrow);
	// // die();
	// echo "</pre>";
	$change_stat = wpsp_MarkRead($mid, $cuid);
	$s_info = get_userdata($get_mrow->s_id);
	$snickname = isset($s_info->user_nicename) ? $s_info->user_nicename : '';
	$nickname = isset($s_info->user_nicename) ? $r_info->user_nicename : '';

	$content = '<div class="wpsp-row message_header">
            		<div class="wpsp-col-md-12"><h3 class="card-title mt-5 mb-5"><strong>Subject : '. $get_mrow->subject .'</strong></h3></div>
							</div>
						<div class="wpsp-col-md-12" id="message_display"><ul>';

	$view_mb = json_decode($get_mrow->msg);

	$content.= '<li><div class="wpsp-row">
            <div class="wpsp-col-md-1" style="float:left">'. get_avatar($get_mrow->s_id, 50) .'</div>
            <div class="wpsp-col-md-11">
						<h3 style="margin-top:0;margin-bottom:5px;font-weight: 500;">'.$snickname.'<span class="m-date"> '.wpsp_ViewDate($get_mrow->m_date).'</span></h3>

						<p>'.$get_mrow->msg.'</p>
						</div>
        </div>';

				$content .= view_sub_message($get_mrow->mid);

				$content .='</li></ul>';
	//$content = apply_filters('wpsp_message_content', $content, intval($mid));
	if ($return)
	{
		return $content;
	}
	else
	{
		echo $content;
	}
	wp_die();
}

function view_sub_message($mid){

	global $wpdb, $current_user;
	$cuid = intval($current_user->ID);
	$msgs_table = $wpdb->prefix . "wpsp_messages";

	$get_mrow = $wpdb->get_results("select * from $msgs_table where replay_id = $mid");
	if(!empty($get_mrow)){

	foreach ($get_mrow as $msg) {
		$r_info = get_userdata($msg->s_id);
		$nickname = isset($r_info->user_nicename) ? $r_info->user_nicename : '';
		$content .= '<ul><li><div class="wpsp-row">
							<div class="wpsp-col-md-1" style="float:left">
									'. get_avatar($msg->r_id, 50).'
							</div>
							<div class="wpsp-col-md-11">
							<h3 style="margin-top:0;margin-bottom:5px;font-weight: 500;">'.$nickname.'<span class="m-date"> '.wpsp_ViewDate($msg->m_date).'</span></h3>

							<p>'.$msg->msg.'</p>';
							if ($msg->s_id != $cuid ){
								$content .= '<a href="javascript:;" class="wpsp-replay-message-btn" data-main_m_id="'.$msg->main_m_id.'" data-replay_m_id="'.$msg->mid.'" data-senderid="'.$msg->r_id.'" data-reciver_id="'.$msg->s_id.'">Reply</a>';
							}

					$content .= '</div></div></li>';

					$content .= view_sub_message($msg->mid);

					$content .= '</ul>';
				}
			}

			return $content;
}

function wpsp_MarkRead($mid, $rid)
{
	global $wpdb;
	global $wpdb;
	$message_block = array();
	$table_name = $wpdb->prefix . 'wpsp_messages';
	$mrow = $wpdb->get_row("SELECT * from $table_name where mid='$mid'");
	$mblock = json_decode($mrow->msg);
	$um_count = count($mblock);
	if (!empty($mblock))
	{
		foreach($mblock as $mess)
		{
			if ($mess->stat == 0 && $mess->s_id != $rid)
			{
				$message_block[] = array(
					's_id' => $mess->s_id,
					'msg' => $mess->msg,
					'time' => $mess->time,
					'stat' => $rid
				);
			}
			else
			{
				$message_block[] = array(
					's_id' => $mess->s_id,
					'msg' => $mess->msg,
					'time' => $mess->time,
					'stat' => $mess->stat
				);
			}
		}
	}
	$rm_count = count($message_block);
	$message_block = json_encode($message_block);
	if ($um_count == $rm_count)
	{
		// $msg_status = $wpdb->update($table_name, array(
		// 	'msg' => $message_block
		// ) , array(
		// 	'mid' => $mid
		// ));
	}
}

function wpsp_deleteMessage()
{
	global $wpdb, $current_user;
	$mid = $_POST['mid'];
	$trashid = intval($_POST['trashid']);
	$cuid = intval($current_user->ID);
	$current_user_status = '';
	$msg_table = $wpdb->prefix . "wpsp_messages";
	$msg_delete_table = $wpdb->prefix . "wpsp_messages_delete";
	if (isset($_POST['multipledelete']) && intval($_POST['multipledelete']) == 1)
	{
		$mids  = (explode(",",$mid));

		foreach ($mids as $id) {

			if ($trashid == 0 ){
				$message_data = array(
					'm_id'          => $id,
					'user_id'       => $cuid,
					'delete_status' => 0
				);
				$wpdb->insert($msg_delete_table, $message_data);
			} else{
				$msg = $wpdb->update($msg_delete_table, array(
					'delete_status' => 1
					), array(
						'm_id'    => $id,
						'user_id' => $cuid,
					));
				}
		}
		echo 'true';
	}
	elseif (!empty($mid))
	{
		if ($trashid == 0 ){
			$message_data = array(
				'm_id'          => $mid,
				'user_id'       => $cuid,
				'delete_status' => 0
			);
			$wpdb->insert($msg_delete_table, $message_data);
		} else{
			$msg = $wpdb->update($msg_delete_table, array(
				'delete_status' => 1
				), array(
					'm_id'    => $mid,
					'user_id' => $cuid,
				));
			}

			echo 'true';

	}else{
		echo 'false';
	}
	wp_die();
}

function wpsp_UnreadCount(){

	global $wpdb, $current_user;
	ob_start();
	$uid = intval($current_user->ID);
	$messages_table = $wpdb->prefix . 'wpsp_messages';
	$messages_delete_table = $wpdb->prefix . 'wpsp_messages_delete';
	$fetch_mess = 0;
	$mid = $wpdb->get_results("select distinct main_m_id from $messages_table where ( r_id=$uid ) and del_stat !=$uid  order by mid DESC");

	foreach($mid as $id){
		$sender_id = $wpdb->get_results("select s_id from $messages_table where mid = $id->main_m_id");

		if($sender_id[0]->s_id == $uid){
			$unread_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= $id->main_m_id AND s_read = 0 AND r_id = $uid AND main_m_id not in (select  distinct main_m_id from $messages_delete_table where m_id = $id->main_m_id and user_id = $uid)");
			$fetch_mess = $fetch_mess + $unread_msg_count;
		}else{
			$unread_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= $id->main_m_id AND r_read = 0 AND r_id = $uid AND main_m_id not in (select  distinct main_m_id from $messages_delete_table where m_id = $id->main_m_id and user_id = $uid)");
			$fetch_mess = $fetch_mess + $unread_msg_count;
		}
	}

	if($fetch_mess == 0){
		return '';
	}else{
		return $fetch_mess;
	}
ob_flush();
}

/* Display teacher list in popup */
function wpsp_getTeachersList(){

	if (isset($_POST['date']) && !empty($_POST['date'])){

		global $wpdb;

		$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['date'])));
		$show_date = wpsp_ViewDate($entry_date);
		$teacher_table = $wpdb->prefix . "wpsp_teacher";
		$teacher_attendance_table = $wpdb->prefix . "wpsp_teacher_attendance";
		$teachers = $wpdb->get_results("select * from $teacher_table");
		$check_attend = $wpdb->get_results("SELECT *FROM $teacher_attendance_table WHERE leave_date = '$entry_date'");
		$reasonList = $attendanceID = $teacherID = array();
		$title = _('New Attendance Entry', 'WPSchoolPress');
		$allPresent = 0;
		if ($check_attend)
		{
			$title = __('Update Attendance Entry', 'WPSchoolPress');
			$warning = __('Attendance already were entered!', 'WPSchoolPress');
			foreach($check_attend as $key => $value)
			{
				$attendanceID[] = $value->id;
				if ($value->status == 'Nil') $allPresent = 1;
				else
				{
					$reasonList[$value->teacher_id] = $value->reason;
				}
			}
		}
		ob_start();
		echo '<div class="wpsp-row"><div class="wpsp-col-md-12">
					<!-- <div class="box box-info">
						 <div class="box-header">
							<h3 class="box-title">' . $title . '</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>/.box-header -->
						  <form name="AttendanceEntryForm" id="AttendanceEntryForm" method="post" class="form-horizontal">
							<div class="box-body">
							<div class="wpsp-form-group">
								<p><span class="wpsp-text-red">' . $warning . '</span></p>
							</div>
							<div class="wpsp-row">
								<div class="wpsp-col-md-12">
								<input type="checkbox" class="ccheckbox checkAll wpsp-checkbox" ' . checked($allPresent, 1, false) . ' name="Nil" value="Nil">
									<span class="wpsp-text-green MRTen" style="vertical-align: middle;">All Present</span>
									</div>
									</div>';
									echo wp_nonce_field('StudentAttendance', 'sattendance_nonce', '', true) . '
								<div class="wpsp-form-group">
								<table class="wpsp-table wpsp-table-bordered attendance-entry">
									<thead>
										<tr><td colspan="5"><span class="pull-right">Date: <span class="wpsp-f500">' . $show_date . '</span></span></td>
										</tr>
										<tr><th class="nosort">#</th><th>Name </th><th class="nosort">Absent </th><th class="nosort">Reason </th></tr>
									</thead>
									<tbody>';
								$sno = 1;
								foreach($teachers as $st)
								{
									$full_name = $st->first_name . " " . $st->middle_name . " " . $st->last_name;
									$reason = isset($reasonList[$st->wp_usr_id]) ? $reasonList[$st->wp_usr_id] : '';
									$rchecked = isset($reasonList[$st->wp_usr_id]) ? 1 : 0;
									echo '<tr><td>' . $sno . '</td>
												<td>' . $full_name . '</td>
												<td><input type="checkbox" ' . checked($rchecked, 1, false) . ' class="ccheckbox wpsp-checkbox" name="absent[]" value="' . $st->wp_usr_id . '"> Absent </td>
												<td><input type="text"  name="reason[' . $st->wp_usr_id . ']" value="' . $reason . '" class="wpsp-form-control"></td>
											  </tr>';
									$sno++;
								}
							echo '</tbody>
								</table>
								</div>
								<div id="formresponse"></div>
							</div>
							<div class="box-footer">
								<div class="wpsp-row">
								<div class="wpsp-col-md-12">
								<div class="wpsp-form-group">
								<button id="AttendanceSubmit" class="wpsp-btn wpsp-btn-success">Submit</button>
									<input type="hidden" value="' . $entry_date . '" name="AttendanceDate">

								</div>
								</div>
								</div>
							</div>
						</form>
					</div>
				</div>';
		$content = ob_get_clean();
		echo $content;
	}
	wp_die();
}
/* add/update teacher attendance */
function wpsp_TeacherAttendanceEntry()
{
	if (!isset($_POST['sattendance_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['sattendance_nonce']) , 'StudentAttendance'))
	{
		echo "Unauthorized Submission";
		exit;
	}
	wpsp_Authenticate();
	global $wpdb;
	$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['AttendanceDate'])));
	$att_table = $wpdb->prefix . "wpsp_teacher_attendance";
	$check_attend = $wpdb->get_row("SELECT * FROM $att_table WHERE leave_date = '$entry_date'");
	if ($check_attend){
		$del = $wpdb->delete($att_table, array(
			'leave_date' => $entry_date
		)); // Remove existing record
	}
	if (isset($_POST['Nil']) && sanitize_text_field($_POST['Nil']) == 'Nil')
	{
		$att_table_data = array(
			'status' => 'Nil',
			'leave_date' => $entry_date
		);
		$ins_attend = $wpdb->insert($att_table, $att_table_data); // mark all teachers as presents
	}
	else
	if (isset($_POST['absent']) && (count($_POST['absent']) > 0))
	{
		foreach($_POST['absent'] as $teacherId => $teacherValue)
		{
			$reason = isset($_POST['reason'][$teacherValue]) ? sanitize_text_field($_POST['reason'][$teacherValue]) : '';
			$att_table_teacher_data = array(
				'teacher_id' => $teacherValue,
				'status' => 'leave',
				'reason' => $reason,
				'leave_date' => $entry_date
			);
			$ins_attend = $wpdb->insert($att_table, $att_table_teacher_data);
		}
	}
	if ($ins_attend)
	{
		$msg = "success1";
	}
	else{
		$msg = "error";
	}
	echo $msg;
	wp_die();
}
function wpsp_TeacherAttendanceView(){

	if (isset($_POST['selectedate']) && !empty($_POST['selectedate']))
	{
		global $wpdb;
		$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['selectedate'])));
		$teacher_attendance_table = $wpdb->prefix . "wpsp_teacher_attendance";
		$teacher_table = $wpdb->prefix . "wpsp_teacher";
		$check_attend = $wpdb->get_results("SELECT *FROM $teacher_attendance_table WHERE leave_date = '$entry_date'", ARRAY_A);
		$allPresent = 0;
		$reasonList = array();
		if (empty($check_attend))
		{
			$result = 'No Attendance entered yet...';
		}
		else
		{
			foreach($check_attend as $key => $value)
			{
				if ($value['status'] == 'Nil')
				{
					$allPresent = 1;
					break;
				}
				else
				{
					$reasonList[$value['teacher_id']] = $value['reason'];
				}
			}
			$teacherlist = $wpdb->get_results("SELECT *FROM $teacher_table", ARRAY_A);
			ob_start();
?>
			<table class="wpsp-table" id="teacherAttendanceTable" cellspacing="0" width="100%" style="width:100%">
				<thead>
				<tr>
					<th>Teacher Code</th>
					<th>Teacher Name</th>
					<th>Attendance</th>
					<th>Commment</th>
				</tr>
				</thead>
				<tbody>
				<?php
			foreach($teacherlist as $teacherInfo){ ?>
				<tr>
					<td><?php
				echo $teacherInfo['empcode']; ?></td>
					<td><?php
				echo $teacherInfo['first_name'] . ' ' . $teacherInfo['middle_name'] . ' ' . $teacherInfo['last_name']; ?></td>
					<td><?php
				if (isset($reasonList[$teacherInfo['wp_usr_id']])) echo 'Absent';
				else echo 'Present'; ?></td>
					<td><?php
				if ($allPresent == 1) echo '-';
				else
				if (isset($reasonList[$teacherInfo['wp_usr_id']])) echo $reasonList[$teacherInfo['wp_usr_id']]; ?></td>
				</tr>
			<?php
			} ?>
				</tbody>
			</table>
		<?php
			$result = ob_get_clean();
		}
	}
	else
	{
		$result = 'Please Select date';
	}
	echo $result;
	wp_die();
}
/* remove attendance delete */
function wpsp_TeacherAttendanceDelete(){
	wpsp_Authenticate();
	global $wpdb;
	$entry_date = date('Y-m-d', strtotime(sanitize_text_field($_POST['aid'])));
	$att_table = $wpdb->prefix . "wpsp_teacher_attendance";
	$del = $wpdb->delete($att_table, array(
		'leave_date' => $entry_date
	)); // Remove existing record
	wp_die();
}
/*remove notification*/
function wpsp_deleteNotify(){
	// wpsp_Authenticate();
	global $wpdb;
	$notify_table = $wpdb->prefix . "wpsp_notification";
	$del = $wpdb->delete($notify_table, array(
		'nid' => intval($_POST['notifyid'])
	)); // Remove existing record
	wp_die();
}
/*show notification information*/
function wpsp_getNotifyInfo()
{
	global $wpdb;
	if (isset($_POST['notifyid']) && !empty($_POST['notifyid']))
	{
		$notify_table = $wpdb->prefix . "wpsp_notification";
		$notifyID = intval($_POST['notifyid']);
		$notifyInfo = $wpdb->get_row("Select *from $notify_table where nid= $notifyID");
		$receiverTypeList = array(
			'all' => __('All Users', 'WPSchoolPress') ,
			'alls' => __('All Students', 'WPSchoolPress') ,
			'allp' => __('All Parents', 'WPSchoolPress') ,
			'allt' => __('All Teachers', 'WPSchoolPress')
		);
		$notifyTypeList = array(
			0 => __('All', 'WPSchoolPress') ,
			1 => __('Email', 'WPSchoolPress') ,
			2 => __('SMS', 'WPSchoolPress') ,
			3 => __('Web Notification', 'WPSchoolPress') ,
			4 => __('Push Notification (Android & IOS)', 'WPSchoolPress')
		);
		if (!empty($notifyInfo))
		{
			$receiver = isset($receiverTypeList[$notifyInfo->receiver]) ? $receiverTypeList[$notifyInfo->receiver] : $notifyInfo->receiver;
			$type = isset($notifyTypeList[$notifyInfo->type]) ? $notifyTypeList[$notifyInfo->type] : $notifyInfo->type;
			$info = "<section class='wpsp-content'>
						<div class='wpsp-row'>
							<div class='wpsp-col-xs-12 wpsp-col-sm-12 wpsp-col-md-12 wpsp-col-lg-12'>
								<div class='wpsp-panel panel-info'>
									<div class='wpsp-panel-heading'>
										<h3 class='wpsp-panel-title'>" . $notifyInfo->name . "</h3>
									</div>
								<div class='wpsp-panel-body'>
									<div class='wpsp-row'>
										<div class='wpsp-col-md-12 wpsp-col-lg-12 '>
											<table class='wpsp-table wpsp-table-user-information'>
												<tbody>
													<tr>
														<td>" . __('Notification ID', 'WPSchoolPress') . "</td>
														<td> $notifyInfo->nid </td>
													</tr>
													<tr>
														<td>" . __('Name', 'WPSchoolPress') . "</td>
														<td> $notifyInfo->name </td>
													</tr>
													<tr>
														<td>" . __('Description', 'WPSchoolPress') . "</td>
														<td> $notifyInfo->description </td>
													</tr>
													<tr>
														<td>" . __('Receiver', 'WPSchoolPress') . "</td>
														<td> $receiver </td>
													</tr>
													<tr>
														<td>" . __('Notify Type', 'WPSchoolPress') . "</td>
														<td> $type </td>
													</tr>
													<tr>
														<td>" . __('Notify Date', 'WPSchoolPress') . "</td>
														<td> " . wpsp_ViewDate($notifyInfo->date) . "</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>";
		}
		else
		{
			$info = "No date retrived";
		}
	}
	echo $info;
	wp_die();
}
function wpsp_changepassword(){
	global $current_user;
	$loginusername = $current_user->data->user_login;
	$password = sanitize_text_field($_POST['oldpw']);
	$status = 0;
	$msg = '';
	$user_id = intval($current_user->ID);
	if (wp_check_password($password, $current_user->data->user_pass, $current_user->ID)){
		if (sanitize_text_field($_POST['newpw']) == sanitize_text_field($_POST['newpw'])){
			wp_set_password(wp_slash(sanitize_text_field($_POST['newpw'])) , $current_user->ID);
			/*$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
			$rp_path   = site_url();
			echo 'path'.$rp_path;
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true ); */
			wp_set_current_user($user_id, $loginusername);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $loginusername);
			wp_password_change_notification($current_user);
			$msg = __('Password Updated Successfully', 'WPSchoolPress');
			$status = 1;
		}
		else
		{
			$msg = __('New Password and re-enter New Password Should be same', 'WPSchoolPress');
		}
	}
	else
	{
		$msg = __('Please enter correct old password', 'WPSchoolPress');
	}
	$response = array(
		'status' => $status,
		'msg' => $msg
	);
	echo json_encode($response);
	exit();
}

function wpsp_getStudentsAttendanceList()
{
	$classID = isset($_POST['classid']) ? intval($_POST['classid']) : '';
	$date = isset($_POST['date']) ? date('Y-m-d', strtotime(sanitize_text_field($_POST['date']))) : date('Y-m-d');
	if (!empty($classID))
	{
		global $wpdb;
		$att_table = $wpdb->prefix . "wpsp_attendance";
		$student_table = $wpdb->prefix . "wpsp_student";
		$leave_table = $wpdb->prefix . "wpsp_leavedays";
		$class_table = $wpdb->prefix . "wpsp_class";
		$check_date = $wpdb->get_row("SELECT * FROM $class_table WHERE cid=$classID");
		$startdate = isset($check_date->c_sdate) && !empty($check_date->c_sdate) ? strtotime($check_date->c_sdate) : '';
		$enddate = isset($check_date->c_edate) && !empty($check_date->c_edate) ? strtotime($check_date->c_edate) : '';
		$selected = strtotime(sanitize_text_field($_POST['date']));
		if (!empty($startdate) && !empty($enddate))
		{
			if ($startdate <= $selected && $enddate >= $selected)
			{
			}
			else
			{
				$msg = __(sprintf('You have selected wrong date, your class startdate is %s and enddate %s', $check_date->c_sdate, $check_date->c_edate) , 'WPSchoolPress');
				$response['status'] = 0;
				$response['msg'] = $msg;
				echo json_encode($response);
				exit();
			}
		}
		$leaveday = $wpdb->get_row("SELECT * from $leave_table where leave_date='$date' and class_id='$classID'", ARRAY_A);
		$attendanceList = '';
		$absentList = array();
		if (count($leaveday) > 0)
		{
			$msg = __('<span class="wpsp-text-red">N/A</span> Not Applicable(Date is marked as leave)', 'WPSchoolPress');
		}
		else
		{
			$attendance = $wpdb->get_row("SELECT * from $att_table where date='$date' and class_id='$classID'", ARRAY_A);

			if (empty($attendance))
			{
				$msg = __('<span class="wpsp-text-red">N/E</span> No Attendance Entered Yet', 'WPSchoolPress');
			}
			elseif (isset($attendance['absents']) && $attendance['absents'] != 'Nil')
			{
				$attendanceList = json_decode($attendance['absents']);
				foreach($attendanceList as $key => $value)
				{
					$absentList[$value->sid] = $value->reason;
				}
			}

			$studentData = [];
			$studentArray = $wpdb->get_results("SELECT class_id, sid from $student_table");
					foreach ($studentArray as $studentdata) {

					if(is_numeric($studentdata->class_id) ){

						if($studentdata->class_id == $classID){
						 $studentData[] = $studentdata->sid;
					 }
					}else{
						 $class_id_array = unserialize($studentdata->class_id);
						 if(in_array($classID, $class_id_array)){
							$studentData[] = $studentdata->sid;
						 }
					}
				}

			if (count($studentData) > 0 && empty($msg))
			{
				ob_start();
				echo '<table class="wpsp-table" id="attendanceOverview" cellspacing="0" width="100%" style="width:100%"><tr><th>' . __('Roll Number', 'WPSchoolPress') . '</th>
							<th>' . __('Student Name', 'WPSchoolPress') . '</th>
							<th>' . __('Attendance', 'WPSchoolPress') . '</th>
							<th>' . __('Commment', 'WPSchoolPress') . '</th>
							</tr>';

							foreach($studentData as $student){

								$studentList = $wpdb->get_results("SELECT CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name, wp_usr_id,s_rollno from $student_table where sid='$student'", ARRAY_A);


								foreach($studentList as $key => $value)
					{
						$userID = $value['wp_usr_id'];
						$s_rollno = $value['s_rollno'];
						$userName = $value['full_name'];
						$sattendance = count($absentList) > 0 && array_key_exists($userID, $absentList) ? __('Absent', 'WPSchoolPress') : __('Present', 'WPSchoolPress');
						$commnet = isset($absentList[$userID]) ? stripslashes($absentList[$userID]) : '';
						echo '<tr><td>' . $s_rollno . '</td>
									<td>' . $userName . '</td>
									<td>' . $sattendance . '</td>
									<td>' . $commnet . '</td>';
						echo '</tr>';
					}
					}

				echo '</table>';

				$msg = ob_get_clean();
			}
			elseif (empty($msg))
			{
				$msg = __('<span class="wpsp-text-red">No Students Available in this class</span>', 'WPSchoolPress');
			}
		}
		$title = '<h3 class="wpsp-card-title">' . __('Attendance Overview', 'WPSchoolPress') . '</h3>';
		$response['status'] = 1;
		$response['msg'] = $title . $msg;
		echo json_encode($response);
	}
	exit();
}
function wpsp_listdashboardschedule(){
	global $wpdb, $current_user;
	$start = sanitize_text_field($_POST['start']);
	$end = sanitize_text_field($_POST['end']);
	$event_table = $wpdb->prefix . "wpsp_events";
	$student_table = $wpdb->prefix . "wpsp_student";
	$event_list = array();
	// Event List
	if ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'teacher'){
		$event_list = $wpdb->get_results("select start,end,title  from $event_table where start >= '$start' and end <='$end'", ARRAY_A);
	}
	else
	{
		$event_list = $wpdb->get_results("select start,end,title from $event_table where type='0' and (start >= '$start' and end <='$end')");
	}
	// Exam List
	$exam_table=$wpdb->prefix."wpsp_exam";
	$class_table=$wpdb->prefix."wpsp_class";
	$examinfo = $wpdb->get_results("SELECT u.*, c.*
	FROM $exam_table u
	INNER JOIN $class_table c ON u.classid= c.cid
	order by u. e_s_date DESC", ARRAY_A);
	foreach($examinfo as $key => $value)
	{
		$event_list[] = array(
			'start' => $value['e_s_date'],
			'end' => $value['e_e_date'],
			'title' => $value['e_name']." For Class ".$value['c_name'],
			'color' => '#dd4b39',
		);
	}
	// holiday
	$leave_table = $wpdb->prefix . "wpsp_leavedays";
	$class_table = $wpdb->prefix . "wpsp_class";
	if ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'teacher')
	{
		$leaves = $wpdb->get_results("select c_name, description,leave_date from $leave_table l,$class_table c WHERE l.class_id=c.cid", ARRAY_A);
	} else{

		if ($current_user->roles[0] == 'parent'){
			$parent_id = intval($current_user->ID);
			$students = $wpdb->get_results("select class_id from $student_table where parent_wp_usr_id='$parent_id'");

		}

		if ($current_user->roles[0] == 'student'){
			$student_id = intval($current_user->ID);
			$students = $wpdb->get_results("select class_id from $student_table where wp_usr_id='$student_id'");
		}
		$child_class = array();
		foreach($students as $child){
			if(is_numeric($child->class_id)){
				$child_class[] = $child->class_id;
			}else{
				$classArray = unserialize($child->class_id);
				foreach($classArray as $id){
					$child_class[] = $id;
				}
			}

		}
		$child_cids = implode(',', $child_class);
		if (!empty($child_cids)){
			$leaves = $wpdb->get_results("select c_name, description,leave_date from $leave_table l,$class_table c WHERE l.class_id=c.cid AND c.cid IN($child_cids)", ARRAY_A);
		}
	}

	foreach($leaves as $key => $value){
		$event_list[] = array(
			'start' => $value['leave_date'],
			'end' => $value['leave_date'],
			'title' => $value['description'] . ' leave for class ' . $value['c_name'],
			'color' => '#00a65a',
		);
	}
	// Exam dates
	echo json_encode($event_list);
	wp_die();
}

function wpsp_Import_Dummy_contents(){
	global $wpdb;
	$wpsp_teacher_table = $wpdb->prefix . "wpsp_teacher";
	$wpsp_class_table = $wpdb->prefix . "wpsp_class";
	$wpsp_student_table = $wpdb->prefix . "wpsp_student";
	$wpsp_subject_table = $wpdb->prefix . "wpsp_subject";
	$teacherarray = $ins_teacher = $tch_ins_arr = $wpsp_class_ins = $studentarray = $sp_stu_ins = array();

	$teacherarray[0] = array(
		'wp_usr_id' => '',
		'first_name' => 'Wolfie',
		'middle_name' => 'Lorenzo',
		'last_name' => 'Gallahue',
		'address' => '9716 Northland Parkway',
		'city' => 'Saint-Etienne',
		'country' => 'France',
		'zipcode' => '42963',
		'empcode' => 'Emp-01',
		'dob' => '1988-10-10',
		'doj' => date('Y-m-d') ,
		'whours' => 2,
		'phone' => '5884176019',
		'qualification' => 'Engineering',
		'gender' => 'Male',
		'bloodgrp' => 'A+',
		'position' => 'General Manager'
	);
	$teacherarray[1] = array(
		'wp_usr_id' => '',
		'first_name' => 'Judye',
		'middle_name' => 'Laurella',
		'last_name' => 'Duhig',
		'address' => '731 Beilfuss Circle',
		'city' => 'Ahmedabad',
		'country' => 'India',
		'zipcode' => '360005',
		'empcode' => 'Emp-02',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '5884176021',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'Geological Engineer'
	);
	$teacherarray[2] = array(
		'wp_usr_id' => '',
		'first_name' => 'Hayden',
		'middle_name' => 'Clark',
		'last_name' => 'Lowerson',
		'address' => '67 Spring Creek Road',
		'city' => 'Drouin South',
		'country' => 'Australia',
		'zipcode' => '360015',
		'empcode' => 'Emp-03',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '4563258790',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'Physics Engineer'
	);
	$teacherarray[3] = array(
		'wp_usr_id' => '',
		'first_name' => 'Luca',
		'middle_name' => 'Elan',
		'last_name' => 'Rymill',
		'address' => '78 Eungella Road',
		'city' => 'Airdmillan',
		'country' => 'Australia',
		'zipcode' => '360020',
		'empcode' => 'Emp-04',
		'dob' => '1967-05-05',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '7895136428',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A+',
		'position' => 'Data Engineer'
	);
	$teacherarray[4] = array(
		'wp_usr_id' => '',
		'first_name' => 'Timothy',
		'middle_name' => 'Shawn',
		'last_name' => 'Corlis',
		'address' => '50 Goebels Road',
		'city' => 'Fordsdale',
		'country' => 'Australia',
		'zipcode' => '360025',
		'empcode' => 'Emp-05',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '1254638790',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'B+',
		'position' => 'Aerospace Engineer'
	);
	$teacherarray[5] = array(
		'wp_usr_id' => '',
		'first_name' => 'Christopher',
		'middle_name' => 'Matte',
		'last_name' => 'Kenniff',
		'address' => '10 Chapman Avenue',
		'city' => 'Oberon',
		'country' => 'Australia',
		'zipcode' => '360030',
		'empcode' => 'Emp-06',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '5884176021',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'Computer Engineer'
	);
	$teacherarray[6] = array(
		'wp_usr_id' => '',
		'first_name' => 'Zoe',
		'middle_name' => 'Aron',
		'last_name' => 'Ransford',
		'address' => '9 McLeans Road',
		'city' => 'Yarrol',
		'country' => 'Australia',
		'zipcode' => '360035',
		'empcode' => 'Emp-07',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '7896541254',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'Data Engineer'
	);
	$teacherarray[7] = array(
		'wp_usr_id' => '',
		'first_name' => 'Holly',
		'middle_name' => 'Canon',
		'last_name' => 'Woolacott',
		'address' => '4 Saggers Road',
		'city' => 'Dumbleyung',
		'country' => 'Australia',
		'zipcode' => '360040',
		'empcode' => 'Emp-08',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '5884176021',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'EC Engineer'
	);

	$teacherarray[8] = array(
		'wp_usr_id' => '',
		'first_name' => 'Mia',
		'middle_name' => 'Wattle',
		'last_name' => 'Macintyre',
		'address' => '62 Clifton Street',
		'city' => 'Victoria',
		'country' => 'Australia',
		'zipcode' => '360040',
		'empcode' => 'Emp-09',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '7458963210',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'IT Engineer'
	);
	$teacherarray[9] = array(
		'wp_usr_id' => '',
		'first_name' => 'Adam',
		'middle_name' => 'Kanon',
		'last_name' => 'Hodgson',
		'address' => '15 Acheron Road',
		'city' => 'Victoria',
		'country' => 'Australia',
		'zipcode' => '360045',
		'empcode' => 'Emp-010',
		'dob' => '1990-06-04',
		'doj' => date('Y-m-d') ,
		'whours' => 4,
		'phone' => '9630258741',
		'qualification' => 'Research and Development',
		'gender' => 'Male',
		'bloodgrp' => 'A-',
		'position' => 'Software Engineer'
	);
	$classarray[0] = array(
		'cid' => 1,
		'c_numb' => 1,
		'c_name' => 'wpsp standard-1',
		'teacher_id' => '',
		'c_capacity' => '10',
		'c_loc' => 'France',
		'c_sdate' => date('Y-m-d') ,
		'c_edate' => date('Y-m-d', strtotime('+6 month', time()))
	);
	$classarray[1] = array(
		'cid' => 2,
		'c_numb' => 2,
		'c_name' => 'wpsp standard-2',
		'teacher_id' => '',
		'c_capacity' => '15',
		'c_loc' => 'India',
		'c_sdate' => date('Y-m-d') ,
		'c_edate' => date('Y-m-d', strtotime('+3 month', time()))
	);
	$classarray[2] = array(
		'cid' => 3,
		'c_numb' => 3,
		'c_name' => 'wpsp standard-3',
		'teacher_id' => '',
		'c_capacity' => '20',
		'c_loc' => 'Africa',
		'c_sdate' => date('Y-m-d') ,
		'c_edate' => date('Y-m-d', strtotime('+3 month', time()))
	);
	foreach($teacherarray as $key => $value){
		$userInfo = array(
			'user_login' => $value['first_name'],
			'user_pass' => $value['first_name'],
			'first_name' => $value['first_name'],
			'user_email' => $value['first_name'] . 'wpsp@yourdomain.com',
			'role' => 'teacher'
		);
		$user_id = wp_insert_user($userInfo);
		if (!is_wp_error($user_id)){
			$value['wp_usr_id'] = $user_id;
			$tch_ins = $wpdb->insert($wpsp_teacher_table, $value);
			$tch_ins_arr[] = $user_id;
		}
	}
	if (count($tch_ins_arr) > 0){
		$i=1;
		foreach($tch_ins_arr as $key => $value){
			$classarray[$key]['teacher_id'] = $value;
			if($i <= 3){
			$wpdb->insert($wpsp_class_table, $classarray[$key]);
			$wpsp_class_ins[] = $wpdb->insert_id;
			}
			else {
			}
			$i++;
		}
	}

	$subjectarray[0] = array(
		'sub_code' => '01',
		'class_id' => '1',
		'sub_name' => 'Physics',
		'sub_teach_id' => '',
		'book_name' => 'Physics Book',
		'sub_desc' => 'Physics demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);

	$subjectarray[1] = array(
		'sub_code' => '02',
		'class_id' => '2',
		'sub_name' => 'Chemistry',
		'sub_teach_id' => '',
		'book_name' => 'Chemistry Book',
		'sub_desc' => 'Chemistry demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[2] = array(
		'sub_code' => '03',
		'class_id' => '3',
		'sub_name' => 'Maths',
		'sub_teach_id' => '',
		'book_name' => 'Maths Book',
		'sub_desc' => 'Maths demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[3] = array(
		'sub_code' => '05',
		'class_id' => '2',
		'sub_name' => 'Science',
		'sub_teach_id' => '',
		'book_name' => 'Science Book',
		'sub_desc' => 'Science demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[4] = array(
		'sub_code' => '06',
		'class_id' => '3',
		'sub_name' => 'Social',
		'sub_teach_id' => '',
		'book_name' => 'Social Book',
		'sub_desc' => 'Social demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[5] = array(
		'sub_code' => '04',
		'class_id' => '1',
		'sub_name' => 'Hindi',
		'sub_teach_id' => '',
		'book_name' => 'Hindi Book',
		'sub_desc' => 'Hindi demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);

	$subjectarray[6] = array(
		'sub_code' => '07',
		'class_id' => '1',
		'sub_name' => 'Biology',
		'sub_teach_id' => '',
		'book_name' => 'Biology Book',
		'sub_desc' => 'Biology demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[7] = array(
		'sub_code' => '08',
		'class_id' => '2',
		'sub_name' => 'English',
		'sub_teach_id' => '',
		'book_name' => 'English Book',
		'sub_desc' => 'English demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);
	$subjectarray[8] = array(
		'sub_code' => '09',
		'class_id' => '3',
		'sub_name' => 'Gujarati',
		'sub_teach_id' => '',
		'book_name' => 'Gujarati Book',
		'sub_desc' => 'Gujarati demo',
		'max_mark' => '100',
		'pass_mark' => '35',
	);

	if (count($tch_ins_arr) > 0){
		$i=1;
		foreach($tch_ins_arr as $key => $value){
			$subjectarray[$key]['sub_teach_id'] = $value;
			if($i <= 9){
			$wpdb->insert($wpsp_subject_table,$subjectarray[$key]);
			$wpsp_subject_ins[] = $wpdb->insert_id;
			}
			else {
			}
			$i++;
		}
	}
	$studentarray[0] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '1',
		's_rollno' => 1,
		's_fname' => 'Erna',
		's_mname' => 'Tresa',
		's_lname' => 'Keeffe',
		's_zipcode' => '3600587',
		's_country' => 'Portugal',
		's_gender' => 'Male',
		's_address' => '84646 Fallview Center',
		's_bloodgrp' => 'B-',
		's_dob' => '1991-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '2026301795',
		'p_fname' => 'Joli',
		'p_mname' => 'Trisha',
		'p_lname' => 'Keeffe',
		'p_gender' => 'Male',
		'p_edu' => 'Human Resources',
		'p_profession' => 'Research Nurse',
		's_paddress' => '7 Northridge Drive',
		'p_bloodgrp' => 'O+',
		's_city' => 'Panghadzngan',
		's_pcountry' => 'Brazil',
		's_pcity' => 'Panghadangan',
		's_pzipcode' => '65415000'
	);
	$studentarray[1] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '2',
		's_rollno' => 2,
		's_fname' => 'Karilynn',
		's_mname' => 'Fern',
		's_lname' => 'Davydzenko',
		's_zipcode' => '260020',
		's_country' => 'Albania',
		's_gender' => 'Female',
		's_address' => '2 Pawling Parkway',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '7229532243',
		'p_fname' => 'Aurelia',
		'p_mname' => 'Effie',
		'p_lname' => 'Allbon',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '2 Pawling Parkway',
		'p_bloodgrp' => 'A+',
		's_city' => 'Nambalan',
		's_pcountry' => 'Albania',
		's_pcity' => 'Nambalan',
		's_pzipcode' => '260020'
	);
	$studentarray[2] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '3',
		's_rollno' => 3,
		's_fname' => 'Paul',
		's_mname' => 'Smith',
		's_lname' => 'Hawks',
		's_zipcode' => '5024',
		's_country' => 'Australia',
		's_gender' => 'Male',
		's_address' => '200 Broadway Av',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '8465288785',
		'p_fname' => 'Smith',
		'p_mname' => 'Rock',
		'p_lname' => 'Hawks',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '200 Broadway Av',
		'p_bloodgrp' => 'A+',
		's_city' => 'South Australia',
		's_pcountry' => 'Australia',
		's_pcity' => 'South Australia',
		's_pzipcode' => '5024'
	);
	$studentarray[3] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '1',
		's_rollno' => 4,
		's_fname' => 'Jams',
		's_mname' => 'Mike',
		's_lname' => 'Patel',
		's_zipcode' => '5050',
		's_country' => 'Australia',
		's_gender' => 'Female',
		's_address' => '200 Alley Av',
		's_bloodgrp' => 'B+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '1234567890',
		'p_fname' => 'Mike',
		'p_mname' => 'Rock',
		'p_lname' => 'Patel',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '200 Alley Av',
		'p_bloodgrp' => 'B+',
		's_city' => 'New Canberra',
		's_pcountry' => 'Australia',
		's_pcity' => 'New Canberra',
		's_pzipcode' => '5050'
	);
	$studentarray[4] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '2',
		's_rollno' => 5,
		's_fname' => 'Shown',
		's_mname' => 'James',
		's_lname' => 'Clark',
		's_zipcode' => '5070',
		's_country' => 'Australia',
		's_gender' => 'Male',
		's_address' => '200 Crescent Av',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '9632587410',
		'p_fname' => 'James',
		'p_mname' => 'Rock',
		'p_lname' => 'Clark',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '200 Crescent Av',
		'p_bloodgrp' => 'A+',
		's_city' => 'Wandloo',
		's_pcountry' => 'Australia',
		's_pcity' => 'Wandloo',
		's_pzipcode' => '5070'
	);
	$studentarray[5] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '1',
		's_rollno' => 7,
		's_fname' => 'Percy',
		's_mname' => 'Pineapple',
		's_lname' => 'Appleton',
		's_zipcode' => '6020',
		's_country' => 'Australia',
		's_gender' => 'Male',
		's_address' => '11 Hinkley Road',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '9856321470',
		'p_fname' => 'Pineapple',
		'p_mname' => 'Rock',
		'p_lname' => 'Appleton',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '11 Hinkley Road',
		'p_bloodgrp' => 'A+',
		's_city' => 'Bromborough',
		's_pcountry' => 'Australia',
		's_pcity' => 'Bromborough',
		's_pzipcode' => '6020'
	);
	$studentarray[6] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '2',
		's_rollno' => 8,
		's_fname' => 'Olivia',
		's_mname' => 'Fred ',
		's_lname' => 'Orange',
		's_zipcode' => '6030',
		's_country' => 'Australia',
		's_gender' => 'Female',
		's_address' => '11 Hinkley Road',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '1230456987',
		'p_fname' => 'Fred',
		'p_mname' => 'Rock',
		'p_lname' => 'Orange',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '11 Hinkley Road',
		'p_bloodgrp' => 'A+',
		's_city' => 'South Australia',
		's_pcountry' => 'Australia',
		's_pcity' => 'South Australia',
		's_pzipcode' => '6030'
	);
	$studentarray[7] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '3',
		's_rollno' => 10,
		's_fname' => 'Pauls',
		's_mname' => 'Smiths',
		's_lname' => 'Hawkss',
		's_zipcode' => '5024',
		's_country' => 'Australia',
		's_gender' => 'Male',
		's_address' => '200 Broadway Av',
		's_bloodgrp' => 'A+',
		's_dob' => '1990-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '8465288785',
		'p_fname' => 'Smiths',
		'p_mname' => 'Rocks',
		'p_lname' => 'Hawkss',
		'p_gender' => 'Male',
		'p_edu' => 'Research and Development',
		'p_profession' => 'Editor',
		's_paddress' => '200 Broadway Av',
		'p_bloodgrp' => 'A+',
		's_city' => 'South Australia',
		's_pcountry' => 'Australia',
		's_pcity' => 'South Australia',
		's_pzipcode' => '5024'
	);
	$studentarray[8] = array(
		'wp_usr_id' => '',
		'parent_wp_usr_id' => '',
		'class_id' => '1',
		's_rollno' => 11,
		's_fname' => 'Harry',
		's_mname' => 'Smith',
		's_lname' => 'Patel',
		's_zipcode' => '3600587',
		's_country' => 'Portugal',
		's_gender' => 'Male',
		's_address' => '84646 Fallview Center',
		's_bloodgrp' => 'B-',
		's_dob' => '1991-07-17',
		's_doj' => date('Y-m-d') ,
		's_phone' => '2026301795',
		'p_fname' => 'Smith',
		'p_mname' => 'Trisha',
		'p_lname' => 'Patel',
		'p_gender' => 'Male',
		'p_edu' => 'Human Resources',
		'p_profession' => 'Research Nurse',
		's_paddress' => '7 Northridge Drive',
		'p_bloodgrp' => 'O+',
		's_city' => 'Panghadangan',
		's_pcountry' => 'Brazil',
		's_pcity' => 'Panghadangan',
		's_pzipcode' => '65415000'
	);
	$wpsp_class_ins = array(
		3,
		2,
		1,
		3,
		2,
		1,
		3,
		2
	);
	if (count($wpsp_class_ins) > 0){
		foreach($studentarray as $key => $value){
			$userInfo = array(
				'user_login' => $value['s_fname'],
				'user_pass' => $value['s_fname'],
				'first_name' => $value['s_fname'],
				'user_email' => $value['s_fname'] . 'wpsp@yourdomain.com',
				'role' => 'student'
			);
			$student_id = wp_insert_user($userInfo);
			$userInfo = array(
				'user_login' => $value['p_fname'],
				'user_pass' => $value['p_fname'],
				'first_name' => $value['p_fname'],
				'user_email' => $value['p_fname'] . 'wpsp@yourdomain.com',
				'role' => 'parent'
			);

			$parent_id = wp_insert_user($userInfo);
			if (!is_wp_error($student_id) && !is_wp_error($parent_id)){
				$value['wp_usr_id'] = $student_id;
				$value['parent_wp_usr_id'] = $parent_id;
				$value['class_id'] = $wpsp_class_ins[$key];
				$wpdb->insert($wpsp_student_table, $value);
				$sp_stu_ins[] = $wpdb->insert_id;
			} else {
				wp_delete_user($student_id);
				wp_delete_user($parent_id);
			}
		}
	}
	$msg = 'Demo data already exists';
	if (count($sp_stu_ins) > 0) $msg = 'Demo data imported successfully';
	echo $msg;
	wp_die();
}
?>
