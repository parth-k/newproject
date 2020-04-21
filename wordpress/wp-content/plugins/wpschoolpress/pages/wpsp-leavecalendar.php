<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
    wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
            $current_user_role=$current_user->roles[0];
            if ($current_user_role == 'administrator' || $current_user_role == 'teacher') {
                wpsp_topbar();
                wpsp_sidebar();
                wpsp_body_start();
                $class_table=$wpdb->prefix."wpsp_class";
                $leave_table=$wpdb->prefix."wpsp_leavedays";
                $current_user_id = get_current_user_id();
                if ($current_user_role == 'administrator') {
                $class_r=$wpdb->get_results("select cid,c_name,c_sdate,c_edate from $class_table");
                }
                if ($current_user_role == 'teacher') {

                    $class_r=$wpdb->get_results("select cid,c_name,c_sdate,c_edate from $class_table where teacher_id =  '$current_user_id '");
                }
                $classes=array();

                foreach($class_r as $classinfo){
                    $classes[$classinfo->cid]=array('c_name'=>$classinfo->c_name,'c_sdate'=>$classinfo->c_sdate,'c_edate'=>$classinfo->c_edate);
                }

                ?>
                <div class="wpsp-card">
                    <div class="wpsp-card-head">
                        <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_leaveclendar_heading_item',esc_html__( 'Generate Leave days for a class', 'WPSchoolPress')); ?></h3>
                    </div>
                    <?php if ($current_user_role == 'administrator') { ?>
                    <div class="wpsp-card-body" id="addLeaveDaysBody">
                        <div class="wpsp-row">
                        <?php
                        if(isset($_POST['submit']) && sanitize_text_field($_POST['submit'])=='Save Dates'){
                            $class_id   =   intval($_POST['ClassID']);
                           if( $class_id!='' )
                                $check      =   $wpdb->get_row("select * from $leave_table where class_id=$class_id");
                            if(!is_numeric($class_id)){
                               echo "<div class='wpsp-col-md-12 wpsp-text-red off-dates wpsp-form-group'>Select class to generate weekly off dates</div>";
                            }else if(!empty($check)){
                                echo "<div class='wpsp-col-md-12 wpsp-text-red off-dates wpsp-form-group'>Dates were already generated for <span class='wpsp-text-blue'>".$classes[$class_id]['c_name']."</span> please delete or use add option to add leave dates</div>";
                            }else if(sanitize_text_field($_POST['from'])=='' || sanitize_text_field($_POST['to'])=='') {
                                echo "<div class='wpsp-col-md-12 wpsp-text-red off-dates wpsp-form-group'>Enter valid  <span class='wpsp-text-blue'> school year</span> dates before trying!</div>";
                            }else{
                                $strDateFrom=date('Y-m-d',strtotime($_POST['from']));
                                $strDateTo=date('Y-m-d',strtotime($_POST['to']));
                                $check_class=$wpdb->get_row("select c_sdate,c_edate from $class_table where cid=$class_id");
                                if($check_class->c_sdate =='' || $check_class->c_sdate ==NULL){
                                    $wpdb->update($class_table,array('c_sdate'=>$strDateFrom),array('cid'=>$class_id));
                                }
                                if($check_class->c_edate =='' || $check_class->c_edate ==NULL){
                                    $wpdb->update($class_table,array('c_edate'=>$strDateTo),array('cid'=>$class_id));
                                }
                                if(sanitize_text_field($_POST['weeklyoff'])=='0'){
                                    $weeklyoff=array('Saturday','Sunday');
                                }else{
                                    $weeklyoff=array(sanitize_text_field($_POST['weeklyoff']));
                                }
                                $leave_dates=wpsp_leaveDates($strDateFrom,$strDateTo,$weeklyoff);
                                foreach($leave_dates as $ldate){
                                    $leave_table_data = array('class_id'=>intval($_POST['ClassID']),'leave_date'=>$ldate,'description'=>'Weekend');
                                    $dt_ins=$wpdb->insert($leave_table,$leave_table_data);
                                }
                            }
                        }
                        ?>
                        </div>
                        <div class="wpsp-row">
                             <?php  if ($current_user_role == 'administrator'){?>
                        <div class="wpsp-col-md-6">
                            <form action="" name="leaveDaysForm" id="leaveDaysForm" method="post">
                              <?php do_action("wpsp_before_leavecalendar");
                                $item =  apply_filters( 'wpsp_leaveclendar_title_item',array());
                                ?>
                                <div class="wpsp-form-group">
                                  <?php
                                      if(isset($item['ClassID'])){
                                           $pl = esc_html($item['ClassID'],"WPSchoolPress");
                                      }else{
                                          $pl = esc_html("Class","WPSchoolPress");

                                      }
                                  ?>
                                    <label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
                                    <select name="ClassID" id="ClassID" class="wpsp-form-control">
                                        <option value="">Select Class</option>
                                        <?php foreach($classes as $cid=>$cname){ ?>
                                           <option value="<?php echo $cid;?>"><?php echo $cname['c_name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="wpsp-display-none">
                                    <div class="wpsp-row">
                                            <div class="wpsp-col-md-6">
                                                <div class="wpsp-form-group">
                                                  <?php
                                                      if(isset($item['from'])){
                                                           $pl = esc_html($item['from'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("Start Date","WPSchoolPress");

                                                      }
                                                  ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
                                                    <input type="text" name="from" id="CSDate" class="wpsp-form-control select_date">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-md-6">
                                                <div class="wpsp-form-group">
                                                  <?php
                                                      if(isset($item['to'])){
                                                           $pl = esc_html($item['to'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("End Date","WPSchoolPress");

                                                      }
                                                  ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
                                                    <input type="text" name="to" id="CEDate" class="wpsp-form-control select_date">
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="wpsp-form-group">
                                  <?php
                                      if(isset($item['weeklyoff'])){
                                           $pl = esc_html($item['weeklyoff'],"WPSchoolPress");
                                      }else{
                                          $pl = esc_html("Weekly off ","WPSchoolPress");

                                      }
                                  ?>
                                    <label class="wpsp-label"><?php echo $pl; ?><span class="wpsp-required">*</span></label>
                                    <select name="weeklyoff" class="wpsp-form-control">
                                        <option value="">Select Day</option>
                                        <option value="0">All Saturday and Sunday</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                                <div class="wpsp-form-group MTTen">
                                    <input type="submit" name="submit" class="wpsp-btn wpsp-btn-primary" value="Save Dates">
                                </div>
                            </form>
                        </div>
                        <div class="wpsp-col-md-6">
                            <ul class="wpsp-ulli-list">
                                <li>Use this form only once for a class to generate all weekly off dates</li>
                                <li>All these dates are excluded for attendance calculation</li>
                                <li>You can delete any date if you have a school on that date</li>
                                <li>This is for attendance calculation purpose only</li>
                            </ul>
                        </div>
                         <?php  }?>
                    </div>
                </div>
                <?php
            }
                $leaves=$wpdb->get_results("select class_id,description,count(*) as numleaves from $leave_table group by class_id");
                ?>
                            <div class="wpsp-card">
                                <div class="wpsp-card-body">
                                    <table class="wpsp-table" id="wpsp_leave_days" cellspacing="0" width="100%" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th>School Year</th>
                                                <th>Number of leave days</th>
                                                 <?php  if ($current_user_role == 'administrator'){?>
                                                <th align="center" class="nosort">Action</th>
                                                <?php }?>
                                            </tr>
                                        </thead>
                                    <tbody>
                                    <?php foreach($leaves as $leave){
                                        if($classes[$leave->class_id]['c_name'] == ""){ }else{ ?>
                                        <tr>
                                            <td><?php echo $classes[$leave->class_id]['c_name'];?></td>
                                            <td>
                                                <?php
                                                echo wpsp_ViewDate($classes[$leave->class_id]['c_sdate']).' to '.wpsp_ViewDate($classes[$leave->class_id]['c_edate']);
                                                ?></td>
                                            <td><?php echo $leave->numleaves;?></td>
                                               <?php  if ($current_user_role == 'administrator'){?>
                                            <td align="center">
                                                <div class="wpsp-action-col">
                                                <a href="javascript:;" class="leaveView wpsp-popclick" data-pop="ViewModal" data-id="<?php echo intval($leave->class_id);?>">
                                                <i class="icon wpsp-view wpsp-view-icon"></i></a>
                                                <a href="javascript:;" class="leaveAdd wpsp-popclick" data-pop="ViewModal" data-id="<?php echo intval($leave->class_id);?>">
                                                <i class="icon fa fa-plus-circle btn btn-primary gap-bottom-small"></i></a>
                                               <!--  <a href="javascript:;" class="leaveDelete" data-id="<?php echo intval($leave->class_id);?>">
                                                <i class="icon wpsp-trash wpsp-delete-icon"></i></a> -->
                                                <a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo intval($leave->class_id);?>" >
                                                    <i class="icon wpsp-trash wpsp-delete-icon" data-id="<?php echo intval($leave->class_id);?>"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <?php }?>
                                        </tr>
                                    <?php } } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Class</th>
                                            <th>School Year</th>
                                            <th>Number of leave days</th>
                                              <?php  if ($current_user_role == 'administrator'){?><th class="nosort">Action</th><?php }?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                                </div>
                            </div>
                    <div class="wpsp-popupMain" id="ViewModal">
                      <div class="wpsp-overlayer"></div>
                      <div class="wpsp-popBody">
                        <div class="wpsp-popInner">
                            <a href="javascript:;" class="wpsp-closePopup"></a>
                            <div id="ViewModalContent" class="wpsp-text-left"></div>
                        </div>
                      </div>
                    </div>
                <?php
                wpsp_body_end();
                wpsp_footer();
            }else if($current_user_role == 'parent' || $current_user_role == 'student'){
                wpsp_topbar();
                wpsp_sidebar();
                wpsp_body_start();
                $current_user_id = get_current_user_id();
                                //echo 'Your User ID is: ' .$current_user_id;
                                $Student_table=$wpdb->prefix."wpsp_student";
                                if($current_user_role == 'student'){
                                $examinfo=$wpdb->get_row("select class_id from $Student_table where wp_usr_id = '$current_user_id'");
                                     //$examinfo->class_id;
                                    $class_id = base64_decode($_GET['cid']);
                             } else {
                                $examinfo=$wpdb->get_row("select class_id from $Student_table where parent_wp_usr_id = '$current_user_id'");
                                 //$examinfo->class_id;
                                    $class_id = base64_decode($_GET['cid']);
                             }
                $class_table=$wpdb->prefix."wpsp_class";
                $leave_table=$wpdb->prefix."wpsp_leavedays";
               // $class_r=$wpdb->get_results("select cid,c_name,c_sdate,c_edate from $class_table where cid = $examinfo->class_id");
                 $class_r=$wpdb->get_results("select cid,c_name,c_sdate,c_edate from $class_table where cid =  $class_id");
                $classes=array();
                foreach($class_r as $classinfo){
                    $classes[ $classinfo->cid]=array('c_name'=>$classinfo->c_name,'c_sdate'=>$classinfo->c_sdate,'c_edate'=>$classinfo->c_edate);
                }
                ?>
                            <?php
                            //$leaves=$wpdb->get_results("select class_id,description,count(*) as numleaves from $leave_table WHERE class_id = $examinfo->class_id  group by class_id");
                             $leaves=$wpdb->get_results("select class_id,description,count(*) as numleaves from $leave_table WHERE class_id = $class_id group by class_id");

                            ?>
                        <div class="wpsp-card">
                            <div class="wpsp-card-head" >
                                <h3 class="wpsp-card-title">Leave Calender  </h3>
                            </div>
                          <div class="wpsp-card-body">
                                <table class="wpsp-table" id="wpsp_leave_days">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>School Year</th>
                                            <th>Number of leave days</th>
                                            <th align="center" class="nosort">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($leaves as $leave){ ?>
                                        <tr>
                                            <td><?php echo $classes[$leave->class_id]['c_name'];?></td>
                                            <td>
                                                <?php
                                                echo wpsp_ViewDate($classes[$leave->class_id]['c_sdate']).' to '.wpsp_ViewDate($classes[$leave->class_id]['c_edate']);
                                                ?></td>
                                            <td><?php echo $leave->numleaves-1;?></td>
                                            <td align="center">
                                                <div class="wpsp-action-col minWidthAuto">
                                                <span><a href="javascript:;" class="leaveView wpsp-popclick" data-pop="ViewModal" data-id="<?php echo intval($leave->class_id);?>"><i class="icon wpsp-view wpsp-view-icon"></i></a></span>
                                                </div>
                                            </td></tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
<!-- Modal popup of leave view -->
        <div class="wpsp-popupMain" id="ViewModal">
          <div class="wpsp-overlayer"></div>
          <div class="wpsp-popBody">
            <div class="wpsp-popInner">
                <a href="javascript:;" class="wpsp-closePopup"></a>
                <div id="ViewModalContent">
                    <div class="wpsp-panel-body">
                        <h3 id="leaveModalHeader" class="wpsp-card-title">
                    </div>
                    <div id="leaveModalBody" class="modal-body">
                    </div>
                </div>
               <!-- <div class="wpsp-col-md-12">
                    <button type="button" class="wpsp-btn wpsp-dark-btn wpsp-popup-cancel" data-dismiss="modal">Cancel1</button>
                </div>-->
            </div>
            </div>
          </div>
        </div>
<!-- END Modal popup of leave view -->

                <div class="wpsp-wrapper">
                <div class="wpsp-container">
            <?php
                wpsp_body_end();
                wpsp_footer();?>
            </div></div>
            <?php }
    }else{
       include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
?>
