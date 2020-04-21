<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
$subjectclassid =	intval($_GET['classid']);
$teacher_table=	$wpdb->prefix."wpsp_teacher";
$teacher_data = $wpdb->get_results("select * from $teacher_table");
$class_table	=	$wpdb->prefix."wpsp_class";
$classQuery		=	$wpdb->get_results("select * from $class_table where cid='$subjectclassid'");
foreach($classQuery as $classdata){
	$cid= intval($classdata->cid);
}
?>
<!-- This form is used for Add New Subject Form -->
<div class="formresponse"></div>
<form name="SubjectEntryForm" action="#" id="SubjectEntryForm" method="post">
		<div class="wpsp-card">
				<div class="wpsp-card-head">
					<div class="wpsp-row">
						<div class="wpsp-col-xs-12">
						 <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_subject_heading_item', esc_html__( 'New Subject Entry', 'WPSchoolPress' )); ?></h3>
						</div>
					</div>
				</div>

				<input type="hidden"  id="wpsp_locationginal1" value="<?php echo admin_url();?>"/>
				<div class="wpsp-card-body">
					<div class="wpsp-row">
					<div class="wpsp-col-md-12 line_box">
						<?php wp_nonce_field( 'SubjectRegister', 'subregister_nonce', '', true ); ?>
						<div class="wpsp-row">
							<?php
                  do_action('wpsp_before_subject_detail_fields');
                  /*Required field Hook*/
                  $is_required_item = apply_filters('wpsp_subject_fields_is_required',array());
              ?>
						<div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
							<div class="wpsp-form-group">
								<label class="wpsp-label" for="Name"><?php
                  $pl = "";
                  $item =  apply_filters( 'wpsp_subject_title_item',esc_html("Class","WPSchoolPress"));
                      if(isset($item['SCID'])){
                        echo $pl = esc_html($item['SCID'],"WPSchoolPress");
                  }else{
                      echo $pl = esc_html("Class","WPSchoolPress");

                  }

                  /*Check Required Field*/
                  if(isset($is_required_item['SCID'])){
                      $is_required =  $is_required_item['SCID'];
                  }else{
                      $is_required = true;
                  }
                  ?>
                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
								</label>
								<select name="SCID" data-is_required="<?php echo $is_required; ?>" id="SCID" class="wpsp-form-control" required>
								<option value="" ><?php echo "Please Select Class"?></option>
								<?php
								foreach($sel_class as $classes) { ?>
									<option value="<?php echo intval($classes->cid);?>" <?php if($sel_classid==$classes->cid) echo "selected"; ?>><?php echo $classes->c_name;?></option>
								<?php } ?>

							</select>
							<!-- <?php foreach($classQuery as $classdata){
								$cid= $classdata->cid; ?>
								<label class="wpsp-labelMain" for="Name">Class Name : <?php if($cid == $subjectclassid) echo $classdata->c_name;?></label>
									<input type="hidden" class="wpsp-form-control" id="SCID" name="SCID" value="<?php if($cid == $subjectclassid) echo $classdata->cid;?>">
								<?php } ?> -->
							</div>
						</div>
						</div>
						<?php for($i=1;$i<=5;$i++){?>
						<div class="wpsp-row">
								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
										<?php
	                    $pl = "";
	                    $item =  apply_filters( 'wpsp_subject_title_item',esc_html("Subject Name","WPSchoolPress"));
	                        if(isset($item['SNames'])){
	                         $pl = esc_html($item['SNames'],"WPSchoolPress");
	                    }else{
	                        $pl = esc_html("Subject Name","WPSchoolPress");

	                    }

	                    /*Check Required Field*/
	                    if(isset($is_required_item['SNames'])){
	                        $is_required =  $is_required_item['SNames'];
	                    }else{
	                        $is_required = true;
	                    }
	                    ?>
									<label class="wpsp-label" for="Name"><?php echo $pl." ".$i;?><?php if($i=='1') { ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
									<?php } ?></label>
									<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" name="SNames[]" placeholder="<?php echo $pl; ?>">
									</div>
								</div>

								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
										<label class="wpsp-label" for="Name"><?php
                      $pl = "";
                      $item =  apply_filters( 'wpsp_subject_title_item',esc_html("Subject Code","WPSchoolPress"));
                          if(isset($item['SCodes'])){
                            echo $pl = esc_html($item['SCodes'],"WPSchoolPress");
                      }else{
                          echo $pl = esc_html("Subject Code","WPSchoolPress");

                      }

                      /*Check Required Field*/
                      if(isset($is_required_item['SCodes'])){
                          $is_required =  $is_required_item['SCodes'];
                      }else{
                          $is_required = false;
                      }
                      ?>
                      <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
                    </label>
										<input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" name="SCodes[]" placeholder="<?php echo $pl; ?>">
									</div>
								</div>
								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
									<label class="wpsp-label" for="Name">
                    <?php
                      $pl = "";
                      $item =  apply_filters( 'wpsp_subject_title_item',esc_html("Subject Teacher","WPSchoolPress"));
                          if(isset($item['STeacherID'])){
                            echo $pl = esc_html($item['STeacherID'],"WPSchoolPress");
                      }else{
                          echo $pl = esc_html("Subject Teacher","WPSchoolPress")."<span> (Incharge)</span>";

                      }

                      /*Check Required Field*/
                      if(isset($is_required_item['STeacherID'])){
                          $is_required =  $is_required_item['STeacherID'];
                      }else{
                          $is_required = false;
                      }
                      ?>
                      <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?>
                    </label>
									<select name="STeacherID[]"  data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control">
										<option value="">Select Teacher </option>
											<?php
											foreach ($teacher_data as $teacher_list) {
												$teacherlistid= $teacher_list->wp_usr_id;?>
												<option value="<?php echo $teacherlistid;?>" ><?php echo $teacher_list->first_name ." ". $teacher_list->last_name;?></option>
												<?php
											}
											?>
									</select>
									</div>
								</div>
								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
										<label class="wpsp-label" for="BName">  <?php
                        $pl = "";
                        $item =  apply_filters( 'wpsp_subject_title_item',esc_html("Book Name","WPSchoolPress"));
                            if(isset($item['BNames'])){
                              echo $pl = esc_html($item['BNames'],"WPSchoolPress");
                        }else{
                            echo $pl = esc_html("Book Name","WPSchoolPress");

                        }

                        /*Check Required Field*/
                        if(isset($is_required_item['BNames'])){
                            $is_required =  $is_required_item['BNames'];
                        }else{
                            $is_required = false;
                        }
                        ?>
                        <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></label>
										<input type="text" class="wpsp-form-control" name="BNames[]" placeholder="Book Name">
									</div>
								</div>
								<?php if($i!='5') { ?>
								<hr style="border-top:1px solid #5C779E"/>
								<?php }?>

						</div>
						<?php } ?>
						<?php  do_action('wpsp_after_subject_detail_fields'); ?>
					</div>
					<div class="wpsp-col-md-12">
						<button type="submit" class="wpsp-btn wpsp-btn-success" id="s_submit"><?php echo apply_filters( 'wpsp_subject_button_submit_label',esc_html("Submit","WPSchoolPress"));?></button>
						 <a href="<?php echo wpsp_admin_url();?>sch-subject" class="wpsp-btn wpsp-dark-btn" ><?php echo apply_filters( 'wpsp_subject_button_back_label',esc_html("Back","WPSchoolPress"));?></a>
					</div>
				</div>
			</div>
		</div>
</form>
<!-- End of Add Subject Form -->
