<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
    wpsp_header();
?>
<style>
.mes-dedeactivate-block{
    position: relative;
}
.mes-dedeactivate-block #message-license-deactivate{
    position: absolute;
    top: 29px;
    right: 0;
}
.mes-dedeactivate-block #multi-license-deactivate{
    position: absolute;
    top: 29px;
    right: 0;
}
</style>
<?php
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
            $current_user_role=$current_user->roles[0];
        wpsp_topbar();
        wpsp_sidebar();
        wpsp_body_start();
        //$proversion   =   wpsp_check_pro_version();
        $proversion     =   wpsp_check_pro_version('wpsp_sms_version');
        $proclass       =   !$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
        $protitle       =   !$proversion['status'] && isset( $proversion['message'] )? $proversion['message']   : '';
        $prodisable     =   !$proversion['status'] ? 'disabled="disabled"'  : '';
        $promessage    =    wpsp_check_pro_version('wpsp_message_version');
     $prodisablemessage    =    !$promessage['status'] ? 'notinstalled'    : 'installed';
     $prohistory    =    wpsp_check_pro_version('wpsp_mc_version');
    $prodisablehistory    =    !$prohistory['status'] ? 'notinstalled'    : 'installed';

        if($current_user_role=='administrator') {
            $ex_field_tbl   =   $wpdb->prefix."wpsp_mark_fields";
            $subject_tbl    =   $wpdb->prefix."wpsp_subject";
            $class_tbl      =   $wpdb->prefix."wpsp_class";
        ?>
        <div class="wpsp-card">
                            <?php
                            if(isset($_GET['sc'])&& sanitize_text_field($_GET['sc'])=='subField') {
                                //Fields Edit Section
                                if( isset( $_GET['sid'] ) && intval($_GET['sid'])>0 ) {
                                    $subject_id =   intval($_GET['sid']);
                                    $fields     =   $wpdb->get_results("select f.*,s.sub_name,c.c_name from $ex_field_tbl f LEFT JOIN $subject_tbl s ON s.id=f.subject_id LEFT JOIN $class_tbl c ON c.cid=s.class_id where f.subject_id=$subject_id");
                                    ?>
                                    <div class="wpsp-card-body">
                                <div class="wpsp-row">
                                    <div class="wpsp-col-md-12 line_box wpsp-col-lg-12">
                                        <div class="wpsp-form-group">
                                        <div class="wpsp-row">
                                        <div class="wpsp-col-md-3">
                                            <label class="wpsp-labelMain"><?php _e( 'Class:', 'WPSchoolPress'); ?></label> <?php echo $fields[0]->c_name;?>
                                        </div>
                                        <div class="wpsp-col-md-3">
                                            <label class="wpsp-labelMain"><?php _e( 'Subject:', 'WPSchoolPress'); ?></label> <?php echo $fields[0]->sub_name;?>
                                        </div>
                                        </div>
                                        <input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
                                        </div>
                                        <div class="wpsp-row">
                                        <?php
                                            if(count($fields)>0){
                                                $sno=1;
                                                foreach($fields as $field){ ?>
                                                        <div class="wpsp-col-sm-6 wpsp-col-md-4">
                                                            <div class="wpsp-form-group smf-inline-form">
                                                                <input type="text" id="<?php echo intval($field->field_id);?>SF" value="<?php echo $field->field_text;?>" class="wpsp-form-control">
                                                                <button id="sf_update" class="wpsp-btn wpsp-btn-success  SFUpdate" data-id="<?php echo intval($field->field_id);?>"><span class="dashicons dashicons-yes"></span></button>
                                                                <button id="d_teacher" class="wpsp-btn wpsp-btn-danger  popclick" data-pop="DeleteModal" data-id="<?php echo intval($field->field_id);?>"><i class="icon wpsp-trash"></i></button>
                                                          </div>
                                                        </div>
                                                <?php $sno++; }
                                            }else{
                                                echo "<div class='wpsp-col-md-8 wpsp-col-md-offset-4'>".__( 'No data retrived!', 'WPSchoolPress')."</div>";
                                            }
                                        ?>
                                        </div>
                                        <a href="<?php echo wpsp_admin_url();?>sch-settings&sc=subField" class="wpsp-btn wpsp-dark-btn"><?php _e( 'Back', 'WPSchoolPress'); ?></a>
                                    </div>
                                    </div>
                                    </div>
                                    <style>
                                    #AddFieldsButton{display:none}
                                    </style>
                                <?php }else{
                                //Subject Mark Extract fields
                                $all_fields =   $wpdb->get_results("select mfields.subject_id, GROUP_CONCAT(mfields.field_text) AS fields,class.c_name,subject.sub_name from $ex_field_tbl mfields LEFT JOIN $subject_tbl subject ON subject.id=mfields.subject_id LEFT JOIN $class_tbl class ON class.cid=subject.class_id group by mfields.subject_id");
                            ?>

                            <div class="wpsp-card-body">
                                <div class="wpsp-row">
                                <div class="wpsp-col-md-12 wpsp-table-responsive">
                                <table id="wpsp_sub_division_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="nosort">#</th>
                                        <th><?php _e( 'Class', 'WPSchoolPress'); ?></th>
                                        <th><?php _e( 'Subject', 'WPSchoolPress'); ?></th>
                                        <th><?php _e( 'Fields', 'WPSchoolPress'); ?></th>
                                        <th><?php _e( 'Action', 'WPSchoolPress'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno=1;
                                    foreach($all_fields as $exfield){ ?>
                                        <tr>
                                            <td><?php echo $sno; ?></td><td><?php echo $exfield->c_name;?></td><td><?php echo $exfield->sub_name;?></td><td><?php echo $exfield->fields;?></td>
                                            <td>
                                                <div class="wpsp-action-col">
                                                <a href="<?php echo wpsp_admin_url();?>sch-settings&sc=subField&ac=edit&sid=<?php echo $exfield->subject_id;?>" title="Edit"><i class="icon wpsp-edit wpsp-edit-icon"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $sno++; } ?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th>#</th>
                                    <th><?php _e( 'Class', 'WPSchoolPress'); ?></th>
                                    <th><?php _e( 'Subject', 'WPSchoolPress'); ?></th>
                                    <th><?php _e( 'Fields', 'WPSchoolPress'); ?></th>
                                    <th><?php _e( 'Action', 'WPSchoolPress'); ?></th>
                                  </tr>
                                </tfoot>
                              </table></div>
                              </div>
<!--- Add Field Popup -->
                            <div class="wpsp-popupMain" id="addFieldModal" >
                                <div class="wpsp-overlayer"></div>
                                <div class="wpsp-popBody">
                                    <div class="wpsp-popInner">
                                        <a href="javascript:;" class="wpsp-closePopup"></a>
                                        <div class="wpsp-panel-heading">
                                            <h3 class="wpsp-panel-title"><?php echo apply_filters( 'wpsp_subject_mark_field_heading_item',esc_html("Add Subject Mark Fields","WPSchoolPress")); ?></h3>
                                            </div>
                                            <div class="wpsp-panel-body">
                                                        <div class="wpsp-row">
                                                <form action="#" method="POST" name="SubFieldsForm" id="SubFieldsForm">
                                                                <div class="wpsp-col-md-12 line_box">
                                                                    <div class="wpsp-row">
                                                                      <?php
                                                                        $item =  apply_filters( 'wpsp_subject_mark_field_title_item',esc_html("Class Name","WPSchoolPress"));
                                                                        $is_required_item = apply_filters('wpsp_subject_mark_field_is_required',array());
                                                                      ?>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <?php wp_nonce_field( 'SubjectFields', 'subfields_nonce', '', true ) ?>
                                                                                <label class="wpsp-label" for="Class"><?php
                                                                                  $pl = "";
                                                                                  if(isset($item['ClassID'])){
                                                                                        echo $pl = esc_html($item['ClassID'],"WPSchoolPress");
                                                                                  }else{
                                                                                      echo $pl = esc_html("Class","WPSchoolPress");
                                                                                  }
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['ClassID'])){
                                                                                      $is_required =  $is_required_item['ClassID'];
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                                                                <select name="ClassID" data-is_required="<?php echo $is_required; ?>" id="SubFieldsClass" class="wpsp-form-control">
                                                                                    <option value="">Select Class</option>
                                                                                    <?php $classes=$wpdb->get_results("select cid,c_name from $class_tbl");
                                                                                        foreach($classes as $class){
                                                                                    ?>
                                                                                        <option value="<?php echo intval($class->cid);?>"><?php echo $class->c_name;?></option>
                                                                                        <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <label class="wpsp-label" for="Subject"><?php
                                                                                  $pl = "";
                                                                                  if(isset($item['SubjectID'])){
                                                                                        echo $pl = esc_html($item['SubjectID'],"WPSchoolPress");
                                                                                  }else{
                                                                                      echo $pl = esc_html("Subject","WPSchoolPress");
                                                                                  }
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['SubjectID'])){
                                                                                      $is_required =  $is_required_item['SubjectID'];
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                                                                <select name="SubjectID" data-is_required="<?php echo $is_required; ?>" id="SubFieldSubject" class="wpsp-form-control">
                                                                                    <option value="">Select Subject</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <label class="wpsp-label" for="Field"><?php
                                                                                  $pl = "";
                                                                                  if(isset($item['FieldName'])){
                                                                                        echo $pl = esc_html($item['FieldName'],"WPSchoolPress");
                                                                                  }else{
                                                                                      echo $pl = esc_html("Field","WPSchoolPress");
                                                                                  }
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['FieldName'])){
                                                                                      $is_required =  $is_required_item['FieldName'];
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                                                                </label>
                                                                                <input type="text" data-is_required="<?php echo $is_required; ?>" name="FieldName" class="wpsp-form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-md-12">
                                                                            <button type="submit" class="wpsp-btn wpsp-btn-success"><?php echo apply_filters( 'wpsp_subject_mark_field_button_submit_text',esc_html("Submit","WPSchoolPress")); ?></button>
                                                                            <button type="button" class="wpsp-btn wpsp-dark-btn" data-dismiss="modal"><?php echo apply_filters( 'wpsp_subject_mark_field_button_cancel_text',esc_html("Cancel","WPSchoolPress")); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                    </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                            </div>
                                        <!-- End popup -->
                            </div>
                            <?php
                                }
                        } else if(isset($_GET['sc'])&& sanitize_text_field($_GET['sc'])=='WrkHours') {
                                //Class Hours
                                if(isset($_POST['AddHours'])){
                                    $workinghour_table  =   $wpdb->prefix."wpsp_workinghours";
                                    if( empty( $_POST['hname'] ) || empty( $_POST['hstart'] ) || empty( $_POST['hend'])  || sanitize_text_field($_POST['htype'])=='' ) {
                                        echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Please fill all values.', 'WPSchoolPress')."</div></div>";
                                    } elseif( strtotime( $_POST['hend'] ) <= strtotime( $_POST['hstart'] ) ) {
                                        echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Invalid Class Time.', 'WPSchoolPress')."</div></div>";
                                    } else {
                                        $workinghour_namelist = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) AS total_hour FROM $workinghour_table WHERE HOUR = %s", $_POST['hname'] ) );
                                        if( $workinghour_namelist > 0 ) {
                                            echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Class Hour Name Already exists.', 'WPSchoolPress')."</div></div>";
                                        } else {
                    $workinghour_table_data = array('hour'      =>  sanitize_text_field($_POST['hname']),
                                                    'begintime' =>  sanitize_text_field( $_POST['hstart'] ),
                                                    'endtime'   =>  sanitize_text_field( $_POST['hend'] ),
                                                    'type'      =>  sanitize_text_field( $_POST['htype'] )
                                                    );
                                            $ins=$wpdb->insert( $workinghour_table,$workinghour_table_data);
                                        }
                                    }
                                }
                                if( isset($_GET['ac']) && sanitize_text_field($_GET['ac'])=='DeleteHours' ) {
                                    $workinghour_table=$wpdb->prefix."wpsp_workinghours";
                                    $hid=intval($_GET['hid']);
                                    $del=$wpdb->delete($workinghour_table,array('id'=>$hid));
                                }
                                //Save hours
                            ?>
                            <div class="wpsp-card-body">
                            <form name="working_hour" method="post" action="">
                                <div class="wpsp-form-group">
                                            <h3 class="wpsp-card-title"><?php echo apply_filters('wpsp_workinghours_heading_item',esc_html__( 'Class hours', 'WPSchoolPress'));?></h3>
                                        </div>
                                            <div class="wpsp-row">
                                              <?php
                                                  do_action("wpsp_workinghours_before");
                                                  $item =  apply_filters( 'wpsp_setting_workinghours_title_item',array());
                                              ?>
                                                <div class="wpsp-col-md-4">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['hname'])){
                                                               $pl = esc_html($item['hname'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Class Hour Name","WPSchoolPress");
                                                          }
                                                      ?>
                                                        <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <input type="text" name="hname" class="wpsp-form-control" placeholder="<?php echo $pl;?>">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-2 wpsp-col-sm-6">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['hstart'])){
                                                               $pl = esc_html($item['hstarthstart'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("From","WPSchoolPress");

                                                          }
                                                      ?>
                                                        <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <input type="text" name="hstart" class="wpsp-form-control" placeholder="<?php echo $pl; ?>" id="timepicker1">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-2 wpsp-col-sm-6">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['hend'])){
                                                               $pl = esc_html($item['hend'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("To","WPSchoolPress");

                                                          }
                                                      ?>
                                                        <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <input type="text" name="hend" class="wpsp-form-control" placeholder="<?php echo $pl; ?>" id="wp-end-time" data-provide="timepicker">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-4">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['htype'])){
                                                               $pl = esc_html($item['htype'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Type","WPSchoolPress");

                                                          }
                                                      ?>
                                                        <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <select name="htype" class="wpsp-form-control">
                                                            <option value="1">Teaching</option>
                                                            <option value="0">Break</option>
                                                        </select>
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-12">
                                                    <div class="wpsp-form-group">
                                                        <button type="submit" class="wpsp-btn wpsp-btn-success" name="AddHours" value="AddHours"><i class="fa fa-plus"></i>&nbsp; Add Hour</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php do_action("wpsp_workinghours_after"); ?>
                                </form>
                                    <table class="wpsp-table" id="wpsp_class_hours" cellspacing="0" width="100%" style="width:100%">
                                        <thead><tr>
                                            <th> Class Hour </th>
                                            <th>Begin Time</th>
                                            <th>End Time</th>
                                            <th>Type</th>
                                            <th class="nosort">Action</th>
                                        </tr> </thead>
                                        <tbody>
                                            <?php
                                                $htypes=array('Break','Teaching');
                                                $workinghour_table=$wpdb->prefix."wpsp_workinghours";
                                                $workinghour_list =$wpdb->get_results("SELECT * FROM $workinghour_table") ;
                                                    foreach ($workinghour_list as $single_workinghour) {
                                                        $hourtype=$htypes[$single_workinghour->type]; ?>
                                                    <tr> <td><?php echo stripslashes( $single_workinghour->hour ) ?></td>
                                                            <td><?php echo $single_workinghour->begintime ?></td>
                                                            <td><?php echo $single_workinghour->endtime ?></td>
                                                            <td><?php echo $hourtype ?></td>
                                                            <td>
                                                                <div class="wpsp-action-col">
                                                                    <a href="<?php echo wpsp_admin_url();?>sch-settings&sc=WrkHours&ac=DeleteHours&hid=<?php echo intval($single_workinghour->id); ?>" class="delete"><i class="icon wpsp-trash wpsp-delete-icon"></i></a>
                                                                </div>
                                                            </td>
                                                            </tr>
                                                <?php   }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th> Class Hour </th>
                                                <th>Begin Time</th>
                                                <th>End Time</th>
                                                <th>Type</th>
                                                <th class="nosort">Action</th>
                                            </tr>
                                        </tfoot>
                                </table>
                                </div>
                                <?php
                            }else{
                                //General Settings
                                $wpsp_settings_table    =   $wpdb->prefix."wpsp_settings";
                                $wpsp_settings_edit     =   $wpdb->get_results("SELECT * FROM $wpsp_settings_table" );
                                foreach( $wpsp_settings_edit as $sdat ) {
                                    $settings_data[$sdat->option_name]  =   $sdat->option_value;
                                }
                            ?>
                            <div class="wpsp-card-body">
                            <div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
                            <div class="tabList">
                                <ul class="wpsp-resp-tabs-list">
                                    <li class="wpsp-tabing" title="Info"><?php echo apply_filters( 'wpsp_settings_tab_info_heading', esc_html__( 'Info', 'WPSchoolPress' )); ?></li>
                                    <li class="wpsp-tabing <?php echo $proclass; ?>" title="<?php echo $protitle;?>" <?php echo $prodisable; ?> title="An overdose in each drop"><?php echo apply_filters( 'wpsp_settings_tab_sms_heading', esc_html__( 'SMS', 'WPSchoolPress' )); ?></li>
                                    <li class="wpsp-tabing"  title="Licensing"><?php echo apply_filters( 'wpsp_settings_tab_info_heading', esc_html__( 'Licensing', 'WPSchoolPress' )); ?></li>
                                </ul>
                            </div>
                                <div class="wpsp-tabBody wpsp-resp-tabs-container">
                                    <div class="wpsp-tabMain">
                                        <form name="schinfo_form" id="SettingsInfoForm" class="wpsp-form-horizontal" method="post">
                                          <?php do_action('wpsp_before_setting_info');
                                            $item =  apply_filters( 'wpsp_setting_info_title_item',array());
                                          ?>
                                            <div  class="wpsp-row">
                                            <div  class="wpsp-form-group">
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['displaypicture'])){
                                                               $pl = esc_html($item['displaypicture'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("School Logo","WPSchoolPress");

                                                          }
                                                      ?>
                                                      <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <div class="wpsp-profileUp">
                                                          <?php
                                                          $url = site_url()."/wp-content/plugins/wpschoolpress/img/wpschoolpresslogo.jpg";
                                                          ?>
                                                              <img src="<?php  echo isset( $settings_data['sch_logo'] ) ? $settings_data['sch_logo'] : $url;?>" id="img_preview" onchange="imagePreview(this);" height="150px" width="150px" class="wpsp-upAvatar" />
                                                            <div class="wpsp-upload-button"><i class="fa fa-camera"></i>
                                                            <input type="hidden" name="old_img" id="old_image" value="<?php  echo isset( $settings_data['sch_logo'] ) ? $settings_data['sch_logo'] : $url;?>">
                                                                                                <input name="displaypicture" class="wpsp-file-upload" id="displaypicture" type="file" accept="image/jpg, image/jpeg, image/png" >
                                                            </div>
                                                        </div>
                                                        <p class="wpsp-form-notes">* Only JPEG, JPG and PNG supported, * Max 3 MB Upload </p>
                                                        <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;">Please Upload Profile Image</label>
                                                        <p id="test" style="color:red"></p>
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-8 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_name'])){
                                                               $pl = esc_html($item['sch_name'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("School Name","WPSchoolPress");

                                                          }
                                                      ?>
                                                        <label class="wpsp-label"><?php echo $pl; ?></label>
                                                        <input type="text" name="sch_name" placeholder="<?php echo $pl; ?>" class="wpsp-form-control" value="<?php echo stripslashes(isset( $settings_data['sch_name'] ) ? $settings_data['sch_name'] : '');?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['Phone'])){
                                                               $pl = esc_html($item['Phone'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Phone","WPSchoolPress");
                                                          }
                                                      ?>
                                                        <label class="wpsp-label" for="phone"><?php echo $pl; ?></label>
                                                        <input type="text" class="wpsp-form-control" id="phone" name="Phone" placeholder="(XXX)-(XXX)-(XXXX)"  value="<?php echo isset( $settings_data['sch_pno'] ) ? $settings_data['sch_pno'] : '';?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['email'])){
                                                               $pl = esc_html($item['email'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Email Address","WPSchoolPress");
                                                          }
                                                      ?>
                                                        <label class="wpsp-label" for="phone"><?php echo $pl; ?></label>
                                                        <input type="text" class="wpsp-form-control" id="email" name="email" placeholder="Email" value="<?php echo isset( $settings_data['sch_email'] ) ? $settings_data['sch_email'] : '';?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-9 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_addr'])){
                                                               $pl = esc_html($item['sch_addr'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Address","WPSchoolPress");
                                                          }
                                                      ?>
                                                        <label class="wpsp-label" for="Address"><?php echo $pl; ?><!-- <span class="wpsp-required">*</span> --></label>
                                                        <textarea rows="2" cols="45" name="sch_addr" class="wpsp-form-control"><?php echo isset( $settings_data['sch_addr'] ) ? $settings_data['sch_addr'] : '';?></textarea>
                                                    </div>
                                                </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_city'])){
                                                               $pl = esc_html($item['sch_city'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("City","WPSchoolPress");

                                                          }
                                                      ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="sch_city" placeholder="<?php echo $pl;?>" class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_city'] ) ? $settings_data['sch_city'] : '';?>">
                                                </div>
                                            </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_state'])){
                                                               $pl = esc_html($item['sch_state'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("State","WPSchoolPress");

                                                          }
                                                      ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="sch_state" placeholder="<?php echo $pl;?>" class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_state'] ) ? $settings_data['sch_state'] : '';?>">
                                                </div>
                                            </div>
                                              <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['country'])){
                                                               $pl = esc_html($item['country'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Country","WPSchoolPress");

                                                          }
                                                      ?>
                                                <label class="wpsp-label" for="Country"><?php echo $pl; ?></label>
                                                 <select class="wpsp-form-control" id="Country" name="country">
                                                    <option value="">Select Country</option>
                                                <?php $country = wpsp_county_list();
                                                //print_r($country);
                                                foreach ($country as $key => $value) {?>
                                                        <option value=<?php echo $key;?><?php  if($key == $settings_data['sch_country']){ echo ' selected';}?>><?php echo $value; ?></option>
                                                <?php }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_fax'])){
                                                               $pl = esc_html($item['sch_fax'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Fax","WPSchoolPress");

                                                          }
                                                      ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="sch_fax" placeholder="<?php echo $pl;?>"  class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_fax'] ) ? $settings_data['sch_fax'] :'';?>">
                                                </div>
                                            </div>
                                              <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['sch_website'])){
                                                               $pl = esc_html($item['sch_website'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Website","WPSchoolPress");

                                                          }
                                                      ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="sch_website" placeholder="<?php echo $pl;?>"  class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_website'] ) ? $settings_data['sch_website'] : '';?>">
                                                    <input type="hidden" name="type"  value="info">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                      <?php
                                                          if(isset($item['date_format'])){
                                                               $pl = esc_html($item['date_format'],"WPSchoolPress");
                                                          }else{
                                                              $pl = esc_html("Date Format","WPSchoolPress");

                                                          }
                                                      ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <select name="date_format"  class="wpsp-form-control">
                                                        <option value="m/d/Y" <?php echo  isset( $settings_data['date_format'] ) && ( $settings_data['date_format']=='m/d/Y')?'selected':''?>>mm/dd/yyyy</option>
                                                        <option value="Y-m-d" <?php echo  isset( $settings_data['date_format'] ) && ($settings_data['date_format']=='Y-m-d')?'selected':''?> >yyyy-mm-dd</option>
                                                        <option value="d-m-Y" <?php echo  isset( $settings_data['date_format'] ) && ($settings_data['date_format']=='d-m-Y')?'selected':''?>>dd-mm-yyyy</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                   <div class="wpsp-form-group">
                                                     <?php
                                                         if(isset($item['markstype'])){
                                                              $pl = esc_html($item['markstype'],"WPSchoolPress");
                                                         }else{
                                                             $pl = esc_html("Marks Type","WPSchoolPress");

                                                         }
                                                     ?>
                                                    <label class="wpsp-label"><?php echo $pl;?></label>
                                                    <select name="markstype" class="wpsp-form-control">
                                                        <option value="Number" <?php echo  isset( $settings_data['markstype'] ) && ( $settings_data['markstype']=='Number')?'selected':''?>>Number </option>
                                                        <option value="Grade" <?php echo  isset( $settings_data['markstype'] ) && ($settings_data['markstype']=='Grade')?'selected':''?>>Grade </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                            <div class="wpsp-form-group <?php echo $proclass; ?>" title="<?php echo $protitle;?>" <?php echo $prodisable; ?>>
                                                    <label class="wpsp-label"><?php _e( 'SMS Setting' ,'WPSchoolPress'); ?></label>
                                                    <input id="absent_sms_alert" type="checkbox" class="wpsp-checkbox ccheckbox <?php echo $proclass; ?> " title="<?php echo $protitle;?>" <?php echo $prodisable; ?> <?php if(isset($settings_data['absent_sms_alert']) && $settings_data['absent_sms_alert']==1) echo "checked"; ?> name="absent_sms_alert" value="1" >
                                                    <label for="absent_sms_alert" class="wpsp-checkbox-label"> <?php _e( 'Send SMS to parent when student absent','WPSchoolPress');?></label>
                                                    <input id="notification_sms_alert" type="checkbox" class="wpsp-checkbox ccheckbox <?php echo $proclass; ?>" title="<?php echo $protitle;?>" <?php echo $prodisable; ?> <?php if(isset($settings_data['notification_sms_alert']) && $settings_data['notification_sms_alert']==1) echo "checked"; ?> name="notification_sms_alert" value="1" >
                                                    <label for="notification_sms_alert" class="wpsp-checkbox-label"> <?php _e( 'Enable SMS Notification','WPSchoolPress');?></label>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <button type="submit" class="wpsp-btn wpsp-btn-success" id="setting_submit" name="submit" style="margin-top: 20px;!important" > Save  </button>
                                                </div>
                                            </div>
                                    </div>
                                    </div>
                                      <?php
                                        do_action('wpsp_after_setting_info');
                                      ?>
                                        </form>
                                    </div>

                                    <div class="wpsp-tabMain">
                                            <?php
                                        if( isset( $proversion['status'] ) && $proversion['status'] ) {
                                            do_action( 'wpsp_sms_setting_html', $settings_data );
                                        } else {
                                            _e( 'Please Purchase This <a href="https://wpschoolpress.com/downloads/sms-add-on-wpschoolpress/" target="_blank">Add-on</a>', 'WPSchoolPress' );
                                        }
                                        ?>
                                    </div>
                                    <div class="wpsp-tabMain">
                                            <form name="Settingslicensing" id="Settingslicensing" class="wpsp-form-horizontal" method="post">
                                            <div class="wpsp-row">
                                                <?php  do_action('wpsp_before_license');
                                                $item =  apply_filters( 'wpsp_setting_licensing_title_item',array());
                                                if(isset($item['importexport'])){
                                                     $pl = esc_html($item['importexport'],"WPSchoolPress");
                                                }else{
                                                    $pl = esc_html("Import Export","WPSchoolPress");

                                                }
                                                ?>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="importexport"  class="wpsp-form-control" value="<?php echo isset( $settings_data['importexport'] ) ? $settings_data['importexport'] : '';?>">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                  <?php
                                                      if(isset($item['smsaddons'])){
                                                           $pl = esc_html($item['smsaddons'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("SMS Addons: ","WPSchoolPress");

                                                      }
                                                 ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="smsaddons"  class="wpsp-form-control" value="<?php echo isset( $settings_data['smsaddons'] ) ? $settings_data['smsaddons'] : '';?>">
                                                </div>
                                            </div>
                                                <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                  <?php
                                                      if(isset($item['feraddons'])){
                                                           $pl = esc_html($item['feraddons'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("Front End Registration Addons: ","WPSchoolPress");

                                                      }
                                                 ?>
                                                    <label class="wpsp-label"><?php echo $pl;?></label>
                                                    <input type="text" name="feraddons"  class="wpsp-form-control" value="<?php echo isset( $settings_data['feraddons'] ) ? $settings_data['feraddons'] : '';?>">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">
                                                  <?php
                                                      if(isset($item['ddma'])){
                                                           $pl = esc_html($item['ddma'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("Dashboard to Dashboard Message Addons : ","WPSchoolPress");

                                                      }
                                                 ?>
                                                    <label class="wpsp-label"><?php echo $pl;?></label>
                                                    <input type="text" name="ddma"  class="wpsp-form-control" value="<?php echo isset( $settings_data['ddma'] ) ? $settings_data['ddma'] : '';?>">
                                                    <?php if($prodisablemessage == 'installed'){ ?>
                                                    <button type="button" id="message-license-deactivate" class="wpsp-btn wpsp-btn-denger"><?php echo esc_html("Deactivate","WPSchoolPress");?></button>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">
                                                  <?php
                                                      if(isset($item['mcaon'])){
                                                           $pl = esc_html($item['mcaon'],"WPSchoolPress");
                                                      }else{
                                                          $pl = esc_html("Multi-class Add-on :","WPSchoolPress");
                                                      }
                                                 ?>
                                                    <label class="wpsp-label"><?php echo $pl; ?></label>
                                                    <input type="text" name="mcaon"  class="wpsp-form-control" value="<?php echo isset( $settings_data['mcaon'] ) ? $settings_data['mcaon'] : '';?>">
                                                    <?php if($prodisablehistory == 'installed'){ ?>
                                                    <button type="button" id="multi-license-deactivate" class="wpsp-btn wpsp-btn-denger"><?php echo esc_html("Deactivate","WPSchoolPress");?></button>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <?php  do_action('wpsp_after_license'); ?>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12">
                                                <div class="wpsp-form-group">
                                                    <button type="submit" id="s_save" class="wpsp-btn wpsp-btn-success" name="submit"><?php echo apply_filters( 'wpsp_setting_licensig_button_submit_text',esc_html__('Save','WPSchoolPress'));?></button>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--    </div> -->
                            <?php } ?>
                        </div>
            <?php } else if($current_user_role=='parent' || $current_user_role=='student') {
                }
        wpsp_body_end();
        wpsp_footer(); ?>
    <?php
    }else {
        include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
    }
?>
