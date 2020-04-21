<?php if(!defined('ABSPATH')) exit;
$extable = $wpdb->prefix."wpsp_exam";
$examname = $examsdate = $examedate = $classid = $examid = '';
$subjectid = array();
if(isset($_GET['id'])){
	$examid = intval($_GET['id']);
	$wpsp_exams = $wpdb->get_results( "select * from $extable where eid='$examid'");
	foreach($wpsp_exams as $examdata){
	 $classid = $examdata->classid;
	$examname = $examdata->e_name;
	$examsdate = $examdata->e_s_date;
	$examedate = $examdata->e_e_date;
	$subjectid = explode( ",",$examdata->subject_id);
	}
}
$label = isset($_GET['id']) ? apply_filters( 'wpsp_exam_update_heading_item', esc_html__( 'Update Exam Information' , 'WPSchoolPress' )): apply_filters( 'wpsp_exam_add_heading_item', esc_html__('Add Exam Information' , 'WPSchoolPress' ));
$formname = isset($_GET['id']) ? 'ExamEditForm' : 'ExamEntryForm';
$buttonname = isset($_GET['id']) ? apply_filters( 'wpsp_exam_update_button_text', esc_html__( 'Update' , 'WPSchoolPress' )) : apply_filters( 'wpsp_exam_submit_button_text', esc_html__('Submit' , 'WPSchoolPress' ));
?>
<!-- This form is used for Add/Update New Exam Information -->
<div id="formresponse"></div>
<form name="
	<?php echo $formname;?>" action="#"
	id="<?php echo $formname;?>" method="post">
	<div class="wpsp-row">
	<div class="wpsp-col-xs-12">
		<div class="wpsp-card">
			<div class="wpsp-card-head">
				<h3 class="wpsp-card-title">
					<?php echo $label; ?>
				</h3>
			</div>
			<div class="wpsp-card-body">
				<div class="wpsp-row">
					<?php  do_action('wpsp_before_exam_fields');
            $is_required_item = apply_filters('wpsp_exam_fields_is_required',array());
           ?>
					<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
              <?php
               $pl = "";
               $item =  apply_filters( 'wpsp_exam_title_item',esc_html("Class Name","WPSchoolPress"));
                   if(isset($item['class_name'])){
                    $pl = esc_html($item['class_name'],"WPSchoolPress");
               }else{
                   $pl = esc_html("Class Name","WPSchoolPress");

               }

               /*Check Required Field*/
               if(isset($is_required_item['class_name'])){
                   $is_required =  $is_required_item['class_name'];
               }else{
                   $is_required = true;
               }
               ?>
								<label class="wpsp-label" for="Name"><?php echo $pl; ?>
									<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
									<?php if($current_user_role=='teacher') {} else {?>
								</label>
							<?php }?>
								<?php
								$classQuery	=	"select cid,c_name from $ctable";
								if($current_user_role=='teacher') {
								$cuserId		=	intval($current_user->ID);
								$classQuery		=	"select cid,c_name from $ctable where teacher_id=$cuserId";
								}
								$wpsp_classes 	=	$wpdb->get_results( $classQuery );

								if($current_user_role=='teacher') {
								echo ' : '.$wpsp_classes[0]->c_name;
								echo '<input type="hidden" name="class_name" id="class_name" value="'.$wpsp_classes[0]->cid.'">';
								echo '</label>';
										}
								else {	?>
									<select name="class_name" data-is_required="<?php echo $is_required; ?>" id="class_name" class="wpsp-form-control">
										<option value="">Select Class</option>
										<?php	foreach($wpsp_classes as $value) {
											$classlistid = intval($value->cid);?>
										<option value="<?php echo intval($value->cid);?>"
											<?php if($classlistid == $classid) echo "selected"; ?>>
											<?php echo $value->c_name;?>
										</option>
										<?php }	?>
									</select>
									<?php } ?>
								</div>
							</div>
							<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
								<div class="wpsp-form-group">
                  <?php
                   $pl = "";
                   $item =  apply_filters( 'wpsp_exam_title_item',esc_html("Exam Name","WPSchoolPress"));
                       if(isset($item['ExName'])){
                        $pl = esc_html($item['ExName'],"WPSchoolPress");
                   }else{
                       $pl = esc_html("Exam Name","WPSchoolPress");

                   }

                   /*Check Required Field*/
                   if(isset($is_required_item['ExName'])){
                       $is_required =  $is_required_item['ExName'];
                   }else{
                       $is_required = true;
                   }
                   ?>
									<label class="wpsp-label" for="Name"><?php echo $pl; ?>
										 <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
									</label>
									<input type="text" class="wpsp-form-control" data-is_required="<?php echo $is_required; ?>" ID="ExName" name="ExName" placeholder="<?php echo $pl; ?>" value="<?php echo $examname; ?>">
									</div>
								</div>
								<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
                    <?php
                     $pl = "";
                     $item =  apply_filters( 'wpsp_exam_title_item',esc_html("Exam Start Date","WPSchoolPress"));
                         if(isset($item['ExStart'])){
                          $pl = esc_html($item['ExStart'],"WPSchoolPress");
                     }else{
                         $pl = esc_html("Exam Start Date","WPSchoolPress");

                     }

                     /*Check Required Field*/
                     if(isset($is_required_item['ExStart'])){
                         $is_required =  $is_required_item['ExStart'];
                     }else{
                         $is_required = true;
                     }
                     ?>
										<label class="wpsp-label" for="Name"><?php echo $pl; ?>
											<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
										</label>
										<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control hasDatepicker" ID="ExStart" name="ExStart" placeholder="<?php echo $pl; ?>" value="<?php echo $examsdate; ?>">
										</div>
									</div>
									<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
										<div class="wpsp-form-group">
                      <?php
                       $pl = "";
                       $item =  apply_filters( 'wpsp_exam_title_item',esc_html("Exam End date","WPSchoolPress"));
                           if(isset($item['ExEnd'])){
                            $pl = esc_html($item['ExEnd'],"WPSchoolPress");
                       }else{
                           $pl = esc_html("Exam End date","WPSchoolPress");

                       }

                       /*Check Required Field*/
                       if(isset($is_required_item['ExEnd'])){
                           $is_required =  $is_required_item['ExEnd'];
                       }else{
                           $is_required = true;
                       }
                       ?>
											<label class="wpsp-label" for="Name"><?php echo $pl; ?>
												<span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
											</label>
											<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control ExEnd hasDatepicker" ID="ExEnd" name="ExEnd" placeholder="<?php echo $pl; ?>" value="<?php echo $examedate; ?>">
											</div>
										</div>
										<div class="wpsp-col-lg-8 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
											<div class="wpsp-form-group exam-subject-list">
                        <?php
                         $pl = "";
                         $item =  apply_filters( 'wpsp_exam_title_item',esc_html("Subject Name","WPSchoolPress"));
                             if(isset($item['subjectall'])){
                              $pl = esc_html($item['subjectall'],"WPSchoolPress");
                         }else{
                             $pl = esc_html("Subject Name","WPSchoolPress");

                         }

                         /*Check Required Field*/
                         if(isset($is_required_item['subjectall'])){
                             $is_required =  $is_required_item['subjectall'];
                         }else{
                             $is_required = false;
                         }
                         ?>
												<label class="wpsp-label" for="Name"><?php echo $pl; ?>
                        <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></label>
												<input type="checkbox" data-is_required="<?php echo $is_required; ?>" name="subjectall" value="All" class="exam-all-subjects wpsp-checkbox" id="all">
													<label for="all" class="wpsp-checkbox-label">All</label>
													<div class="exam-class-list">
														<?php $sub_table = $wpdb->prefix."wpsp_subject";
														if($current_user_role=='teacher') {
															$classid = $wpsp_classes[0]->cid;
														}
														if(!empty($classid)){
															$subjectlist	=	$wpdb->get_results("select sub_name,id from $sub_table where class_id=$classid");
															foreach($subjectlist as $svalue){ ?>
														<input type="checkbox" name="subjectid[]" value="<?php echo $svalue->id; ?>" class="exam-subjects wpsp-checkbox" id="subject-<?php echo $svalue->id;?>"
															<?php if(in_array($svalue->id, $subjectid)){ ?> checked
															<?php } ?> >
															<label for="subject-<?php echo $svalue->id;?>" class="wpsp-checkbox-label">
																<?php echo $svalue->sub_name;?>
															</label>
															<?php											} } ?>
														</div>
													</div>
										</div>
										<?php  do_action('wpsp_after_exam_fields'); ?>
									</div>
											<?php if(!empty($examid)){ ?>
											<input type="hidden" ID="ExamID" name="ExamID" value="<?php echo $examid; ?>">
											<?php } ?>
												<div class="wpsp-row">
													<div class="wpsp-col-xs-12">
														<button type="submit" class="wpsp-btn wpsp-btn-success" id="e_submit">
															<?php echo $buttonname; ?>
														</button>
														<a href="<?php echo wpsp_admin_url();?>sch-exams" class="wpsp-btn wpsp-dark-btn" ><?php echo apply_filters( 'wpsp_exam_back_button_text', esc_html__( 'Back' , 'WPSchoolPress' )); ?>
														</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								</form>
								<!-- End of Add/Update Exam Form -->
