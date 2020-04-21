<?php if (!defined( 'ABSPATH' ) ) exit('No Such File');

$student_table = $wpdb->prefix."wpsp_student";
$class_table = $wpdb->prefix."wpsp_class";
$users_table = $wpdb->prefix."users";
$sid = intval($_GET['id']);$edit = true;$msg = '';
$student_index = '';
if( isset($_GET['edit']) && sanitize_text_field($_GET['edit'])=='true' && ($current_user_role=='administrator' || $current_user_role=='teacher' ) && (isset( $_POST['sedit_nonce'] ) && wp_verify_nonce( sanitize_text_field($_POST['sedit_nonce']), 'StudentEdit' ) ) )  {
    ob_start();
    wpsp_UpdateStudent();
}
$stinfo  =  $wpdb->get_row("select * from $student_table where wp_usr_id='$sid'");
// print_r($stinfo);
if( !empty( $stinfo ) ) {
  $student_index = $stinfo->sid;
    $loc_avatar=get_user_meta($sid,'simple_local_avatar',true);
    $img_url= $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/default_avtar.jpg';
     $parentid   =   intval($stinfo->parent_wp_usr_id);
    if( !empty( $parentid ) ) {
        $parentInfo =   get_user_by( 'id', $parentid );
        $parentEmail =  isset( $parentInfo->data->user_email ) ? $parentInfo->data->user_email : '';
        //Update Parent Profile Picture
        $parent_loc_avatar=get_user_meta($parentid,'simple_local_avatar',true);
        $parent_img_url= $parent_loc_avatar ? $parent_loc_avatar['full'] : WPSP_PLUGIN_URL.'img/default_avtar.jpg';
    }
?>

<div id="formresponse"></div>

<form name="StudentEditForm" id="StudentEditForm" method="post" enctype="multipart/form-data" novalidate="novalidate">
    <div class="wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_personal_detail', esc_html__( 'Personal Details', 'WPSchoolPress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
                    <?php wp_nonce_field( 'StudentRegister', 'sregister_nonce', '', true ) ?>

                    <div class="wpsp-row">
                        <?php
                          do_action('wpsp_before_student_personal_detail_fields');
                          /*Required field Hook*/
                          $is_required_item = apply_filters('wpsp_student_personal_is_required',array());
                        ?>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label displaypicture">  <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Profile Image","WPSchoolPress"));
                                      if(isset($item['section']) && $item['section'] == "personal" && isset($item['displaypicture'])){
                                        echo esc_html($item['displaypicture'],"WPSchoolPress");
                                  }else{
                                      echo esc_html("Profile Image","WPSchoolPress");
                                  }
                                  ?></label>
                                <div class="wpsp-profileUp">
                                    <img src="<?php echo $img_url;?>" id="img_preview" onchange="imagePreview(this);" height="150px" width="150px" class="wpsp-upAvatar" />
                                    <div class="wpsp-upload-button"><i class="fa fa-camera"></i>
                                        <input name="displaypicture" class="wpsp-file-upload upload" id="displaypicture" type="file" accept="image/jpg, image/jpeg" />
                                    </div>
                                </div>
                                <p class="wpsp-form-notes">* Only JPEG and JPG supported, * Max 3 MB Upload </p>
                                <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;">Please Upload Profile Image</label>
                                <p id="test" style="color:red" class="validation-error-displaypicture"></p>
                            </div>
                        </div>
                        <input type="hidden" id="studID" name="wp_usr_id" value="<?php echo $sid;?>">
                        <input type="hidden" name="parentid" value="<?php echo $stinfo->parent_wp_usr_id;?>">
                        <div class="wpsp-col-lg-3 wpsp-col-md-3 wpsp-col-sm-12 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="gender"><?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Gender","WPSchoolPress"));
                                    if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_gender'])){
                                      echo esc_html($item['s_gender'],"WPSchoolPress");
                                    }else{
                                      echo esc_html("Gender","WPSchoolPress");
                                    }
                                ?></label>
                                <div class="wpsp-radio-inline">
                                    <div class="wpsp-radio">
                                        <input type="radio" name="s_gender" <?php if(strtolower($stinfo->s_gender)=='male') echo "checked"?> value="Male" checked="checked">
                                        <label for="Male">Male</label>
                                    </div>
                                    <div class="wpsp-radio">
                                        <input type="radio" name="s_gender" <?php if(strtolower($stinfo->s_gender)=='female') echo "checked"; ?> value="Female">
                                        <label for="Female">Female</label>
                                    </div>
                                    <div class="wpsp-radio">
                                       <input type="radio" name="s_gender" <?php if(strtolower($stinfo->s_gender)=='other') echo "checked"; ?> value="other">
                                       <label for="other">Other</label>
                                    </div>
                                </div>
                           </div>
                        </div>
                        <div class="clearfix wpsp-ipad-show"></div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                          <div class="wpsp-form-group">
                            <?php
                            $pl = "";
                            $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("First Name","WPSchoolPress"));
                                if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_fname'])){
                                $pl = esc_html($item['s_fname'],"WPSchoolPress");
                            }else{
                                $pl = esc_html("First Name","WPSchoolPress");

                            }
                            /*Check Required Field*/
                            if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_fname'])){
                                $is_required =  $is_required_item['s_fname'];
                            }else{
                                $is_required = true;
                            }
                            ?>
                                <label class="wpsp-label" for="firstname"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="firstname" value="<?php echo !empty( $stinfo->s_fname ) ? $stinfo->s_fname : $stinfo->s_fname; ?>" name="s_fname" placeholder="<?php echo $pl; ?>">
                               <?php wp_nonce_field( 'StudentEdit', 'sedit_nonce', '', true ) ?>
                                    <input type="hidden" id="studID" name="wp_usr_id" value="<?php echo $sid;?>">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                              <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Middle Name","WPSchoolPress"));
                                  if(isset($item['section']) && $item['section'] == "personal" && isset($item['middlename'])){
                                   $pl = esc_html($item['middlename'],"WPSchoolPress");
                              }else{
                                    $pl = esc_html("Middle Name","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['middlename'])){
                                  $is_required =  $is_required_item['middlename'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                                <label class="wpsp-label" for="middlename"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" value="<?php echo !empty( $stinfo->s_mname ) ? $stinfo->s_mname : $stinfo->s_mname; ?>" id="middlename" name="s_mname" placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                              <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Last Name","WPSchoolPress"));
                              if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_lname'])){
                                      $pl =  esc_html($item['s_lname'],"WPSchoolPress");
                              }else{
                                     $pl =  esc_html("Last Name","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_lname'])){
                                  $is_required =  $is_required_item['s_lname'];
                              }else{
                                  $is_required = true;
                              }

                              ?>
                                <label class="wpsp-label" for="lastname"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="lastname" value="<?php echo !empty( $stinfo->s_lname ) ? $stinfo->s_lname : $stinfo->s_lname; ?>" name="s_lname" placeholder="<?php echo $pl; ?>" required="required">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                              <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Date of Birth","WPSchoolPress"));
                                  if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_dob'])){
                                      $pl =  esc_html($item['s_dob'],"WPSchoolPress");
                                  }else{
                                      $pl = esc_html("Date of Birth","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_dob'])){
                                      $is_required =  $is_required_item['s_dob'];
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                <label class="wpsp-label" for="dateofbirth"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control select_date" value="<?php echo !empty( $stinfo->s_dob ) ? wpsp_ViewDate($stinfo->s_dob) : ''; ?>" id="Dob" name="s_dob" placeholder="mm/dd/yyyy">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="bloodgroup">
                                  <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Blood Group","WPSchoolPress"));
                                  if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_bloodgrp'])){
                                        echo esc_html($item['s_bloodgrp'],"WPSchoolPress");
                                  }else{
                                      echo esc_html("Blood Group","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_bloodgrp'])){
                                      $is_required =  $is_required_item['s_bloodgrp'];
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                </label>
                                <select class="wpsp-form-control" data-is_required="<?php echo $is_required; ?>" id="Bloodgroup" name="s_bloodgrp">
                                    <option value="">Select Blood Group</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'O+') echo "selected"; ?> value="O+">O +</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'O-') echo "selected"; ?> value="O-">O -</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'A+') echo "selected"; ?> value="A+">A +</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'A-') echo "selected"; ?> value="A-">A -</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'B+') echo "selected"; ?> value="B+">B +</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'B-') echo "selected"; ?> value="B-">B -</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'AB+') echo "selected"; ?> value="AB+">AB +</option>
                                    <option <?php if ($stinfo->s_bloodgrp == 'AB-') echo "selected"; ?> value="AB-">AB -</option>
                                </select>
                            </div>
                               </div>
                            <div class="wpsp-col-lg-3 wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                                <div class="wpsp-form-group">
                                        <label class="wpsp-label" for="s_p_phone"><?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Phone Number","WPSchoolPress"));
                                        if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_p_phone'])){
                                              echo $pl= esc_html($item['s_p_phone'],"WPSchoolPress");
                                        }else{
                                            echo $pl= esc_html("Phone Number","WPSchoolPress");
                                        }
                                        /*Check Required Field*/
                                        if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_p_phone'])){
                                            $is_required =  $is_required_item['s_p_phone'];
                                        }else{
                                            $is_required = false;
                                        }

                                        ?>
                                        <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                        <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="s_p_phone" name="s_p_phone" value="<?php echo $stinfo->p_phone;?>" placeholder="<?php echo $pl; ?>" onkeypress='return event.keyCode == 8 || event.keyCode == 46
 || event.keyCode == 37 || event.keyCode == 39 || event.charCode >= 48 && event.charCode <= 57'>
                                        <small>(Please enter country code with mobile number)</small>
                                        <input type="hidden" name="parentid" id="parentid" value="<?php echo $stinfo->parent_wp_usr_id;?>"/>
                                    </div>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <hr />
                            <h4 class="card-title mt-5">Address</h4>
                        </div>
                      <div class="wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                              <?php
                              $pl="";
                               $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Current Address","WPSchoolPress"));
                              if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_address'])){
                                    $pl = esc_html($item['s_address'],"WPSchoolPress");
                              }else{
                                  $pl = esc_html("Current Address","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_address'])){
                                  $is_required =  $is_required_item['s_address'];
                              }else{
                                  $is_required = true;
                              }

                              ?>
                                <label class="wpsp-label" for="Address"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" name="s_address" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" rows="4" id="current_address" value="<?php echo $stinfo->s_address; ?>" placeholder="<?php echo $pl; ?>" />
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group ">
                                <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("City Name","WPSchoolPress"));
                                    if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_city'])){
                                          $pl = esc_html($item['s_city'],"WPSchoolPress");
                                    }else{
                                        $pl = esc_html("City Name","WPSchoolPress");
                                    }
                                    /*Check Required Field*/
                                    if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_city'])){
                                        $is_required =  $is_required_item['s_city'];
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                                <label class="wpsp-label" for="CityName"><?php echo $pl;?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="current_city" value="<?php echo $stinfo->s_city; ?>" name="s_city" placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Country">  <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Country","WPSchoolPress"));
                                  if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_country'])){
                                        echo esc_html($item['s_country'],"WPSchoolPress");
                                  }else{
                                      echo esc_html("Country","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_country'])){
                                      $is_required =  $is_required_item['s_country'];
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <?php $countrylist = wpsp_county_list(); ?>
                                    <select class="wpsp-form-control" data-is_required="<?php echo $is_required; ?>" id="current_country" name="s_country">
                                        <option value="">Select Country</option>
                                        <?php foreach ($countrylist as $key => $value) { ?>
                                           <option value="<?php echo $value; ?>" <?php echo selected($stinfo->s_country, $value); ?>>
                                                <?php echo $value; ?>
                                            </option>
                                            <?php
                                            }
                                            ?>
                                    </select>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                           <div class="wpsp-form-group">
                             <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Pin Code","WPSchoolPress"));
                            $pl = "";
                             if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_zipcode'])){
                                    $pl = esc_html($item['s_zipcode'],"WPSchoolPress");
                             }else{
                                    $pl =  esc_html("Pin Code","WPSchoolPress");
                             }
                             /*Check Required Field*/
                             if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_zipcode'])){
                                 $is_required =  $is_required_item['s_zipcode'];
                             }else{
                                 $is_required = false;
                             }
                             ?>
                                <label class="wpsp-label" for="Zipcode"><?php echo $pl; ?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="current_pincode" name="s_zipcode"  value="<?php echo $stinfo->s_zipcode; ?>" placeholder="<?php echo $pl; ?>" data-isrequired="<?php echo $data = apply_filters( 'wpsp_student_isrequired_pincode',true); ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                           <div class="wpsp-form-group">
                                <input type="checkbox" id="sameas" value="1" onclick="sameAsAbove()">
                                <label for="sameas"> Same as Above </label>
                            </div>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <div class="wpsp-form-group">
                                    <label for="PermanentAddress">  <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Permanent Address","WPSchoolPress"));
                                      $pl = "";
                                      if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_paddress'])){
                                            echo $pl = esc_html($item['s_paddress'],"WPSchoolPress");
                                      }else{
                                          echo $pl = esc_html("Permanent Address","WPSchoolPress");
                                      }
                                      /*Check Required Field*/
                                      if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_paddress'])){
                                          $is_required =  $is_required_item['s_paddress'];
                                      }else{
                                          $is_required = false;
                                      }

                                      ?>
                                      <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                    </label>
                                    <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" rows="5" placeholder="<?php echo $pl; ?>" id="permanent_address" value="<?php echo $stinfo->s_paddress;?>" name="s_paddress">
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label for="City">  <?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("City Name","WPSchoolPress"));
                                  $pl = "";
                                  if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_pcity'])){
                                        echo $pl = esc_html($item['s_pcity'],"WPSchoolPress");
                                  }else{
                                      echo $pl = esc_html("City Name","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_pcity'])){
                                      $is_required =  $is_required_item['s_pcity'];
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="permanent_city" value="<?php echo $stinfo ->s_pcity; ?>" name="s_pcity" placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label for="Country"><?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Country","WPSchoolPress"));
                                    if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_pcountry'])){
                                          echo esc_html($item['s_pcountry'],"WPSchoolPress");
                                    }else{
                                        echo esc_html("Country","WPSchoolPress");
                                    }
                                    /*Check Required Field*/
                                    if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_pcountry'])){
                                        $is_required =  $is_required_item['s_pcountry'];
                                    }else{
                                        $is_required = false;
                                    }

                                    ?>
                                    <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                  </label>
                                <?php $countrylist = wpsp_county_list(); ?>
                                    <select data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="permanent_country" name="s_pcountry">
                                        <option value="">Select Country</option>
                                        <?php foreach ($countrylist as $key => $value) { ?>
                                            <option value="<?php echo $value; ?>" <?php echo selected($stinfo->s_pcountry, $value); ?>>
                                                <?php echo $value; ?>
                                            </option>
                                            <?php
                                            }
                                            ?>
                                    </select>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                   <label class="wpsp-label" for="Zipcode"><?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Pin Code","WPSchoolPress"));
                                   $pl = "";
                                   if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_pzipcode'])){
                                         echo $pl = esc_html($item['s_pzipcode'],"WPSchoolPress");
                                   }else{
                                       echo $pl = esc_html("Pin Code","WPSchoolPress");
                                   }
                                   /*Check Required Field*/
                                   if(isset($is_required_item['section']) && $is_required_item['section'] == "personal" && isset($is_required_item['s_pzipcode'])){
                                       $is_required =  $is_required_item['s_pzipcode'];
                                   }else{
                                       $is_required = false;
                                   }

                                   ?>
                                   <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                 </label>
                                   <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="permanent_pincode" name="s_pzipcode" value="<?php echo $stinfo->s_pzipcode;?>" placeholder="<?php echo $pl; ?>"  data-isrequired="<?php echo $data = apply_filters( 'wpsp_student_isrequired_pincode',true); ?>">

                            </div>
                        </div>
                          <?php

                              do_action( 'wpsp_after_student_personal_detail_fields' );
                          ?>
                        <div class="wpsp-col-xs-12">
                            <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform">Next</button>&nbsp;&nbsp;
                        </div>
                    </div>
            </div>
        </div>
    </div>


    <div class="wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_parent_detail', esc_html__( 'Parent Detail', 'WPSchoolPress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
                <div class="wpsp-row">
                      <?php
                            do_action('wpsp_before_student_parent_detail_fields');
                            /*Required field Hook*/
                            $is_required_parent = apply_filters('wpsp_student_parent_is_required',array());

                        ?>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <input type="hidden" id="wpsp_locationginal" value="<?php echo admin_url();?>" />
                            <label class="customUpload btnUpload  wpsp-label"><?php $item =  apply_filters( 'wpsp_student_personal_title_item',esc_html("Pin Code","WPSchoolPress"));
                            $pl = "";
                            if(isset($item['section']) && $item['section'] == "personal" && isset($item['s_pzipcode'])){
                                  echo $pl = esc_html($item['s_pzipcode'],"WPSchoolPress");
                            }else{
                                echo $pl = esc_html("Pin Code","WPSchoolPress");
                            }
                            ?></label>
                            <div class="wpsp-profileUp">
                                <?php if($parent_img_url == ""){?>
                                     <img class="wpsp-upAvatar" id="img_preview1"  onchange="imagePreview(this);" src="<?php echo plugins_url();?>/wpschoolpress/img/default_avtar.jpg">
                               <!--  <img class="wpsp-upAvatar" id="img_preview1" onchange="imagePreview(this);" src="<?php echo $parent_img_url;?>"> -->
                            <?php } else {?>
                                <img class="wpsp-upAvatar" id="img_preview1" onchange="imagePreview(this);" src="<?php echo $parent_img_url;?>">
                            <?php }?>
                                <div class="wpsp-upload-button"><i class="fa fa-camera"></i>
                                    <input name="p_displaypicture" class="wpsp-file-upload" id="p_displaypicture" type="file" accept="image/jpg, image/jpeg" />
                                </div>
                            </div>
                            <p class="wpsp-form-notes">* Only JPEG and JPG supported, * Max 3 MB Upload </p>
                            <label id="pdisplaypicture-error" class="error" for="pdisplaypicture" style="display: none;">
                                Please Upload Profile Image</label>
                            <p id="test" style="color:red"></p>
                        </div>
                    </div>
                    <div class="wpsp-col-lg-9 wpsp-col-md-8 wpsp-col-sm-12 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="p_gender"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Gender","WPSchoolPress"));
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_gender'])){
                                    echo esc_html($item['p_gender'],"WPSchoolPress");
                              }else{
                                  echo esc_html("Gender","WPSchoolPress");
                              }
                              ?></label>
                            <div class="wpsp-radio-inline">
                                <div class="wpsp-radio">
                                    <input type="radio" name="p_gender" <?php if (strtolower($stinfo->p_gender) == 'male') echo "checked" ?> value="Male" checked="checked">
                                    <label for="Male">Male</label>
                                </div>
                                <div class="wpsp-radio">
                                    <input type="radio" name="p_gender" <?php if (strtolower($stinfo->p_gender) == 'female') echo "checked"; ?> value="Female">
                                    <label for="Female">Female</label>
                                </div>
                                <div class="wpsp-radio">
                                    <input type="radio" name="p_gender" <?php if (strtolower($stinfo->p_gender) == 'other') echo "checked"; ?> value="other">
                                    <label for="other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix wpsp-ipad-show"></div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="firstname"><?php $pl = "";
                               $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("First Name","WPSchoolPress"));
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_fname'])){
                                    echo $pl = esc_html($item['p_fname'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("First Name","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_fname'])){
                                  $is_required =  $is_required_parent['p_fname'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="firstname" value="<?php echo!empty($stinfo->p_fname) ? $stinfo->p_fname : ''; ?>" name="p_fname" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="middlename"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Middle Name","WPSchoolPress"));
                              $pl = "";
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_mname'])){
                                    echo $pl = esc_html($item['p_mname'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("Middle Name","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_mname'])){
                                  $is_required =  $is_required_parent['p_mname'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="middlename" value="<?php echo!empty($stinfo->p_mname) ? $stinfo->p_mname : ''; ?>" name="p_mname" placeholder="<?php echo $pl; ?>" >
                        </div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="lastname"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Last Name","WPSchoolPress"));
                              $pl = "";
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_lname'])){
                                    echo $pl = esc_html($item['p_lname'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("Last Name","WPSchoolPress");
                              }

                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_lname'])){
                                  $is_required =  $is_required_parent['p_lname'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="lastname" value="<?php echo!empty($stinfo->p_lname) ? $stinfo->p_lname : ''; ?>" name="p_lname" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <?php // If parent not created then add this fields?>
                    <?php if($stinfo->parent_wp_usr_id == 0){?>
                        <input type="hidden"  name="studentprofileparentnew" value="studentprofileparentnew">
                        <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="pEmail"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Email Address","WPSchoolPress"));
                                  $pl = "";
                                  if(isset($item['section']) && $item['section'] == "parent" && isset($item['pEmail'])){
                                        echo $pl = esc_html($item['pEmail'],"WPSchoolPress");

                                  }else{
                                      echo $pl = esc_html("Email Address","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['pEmail'])){
                                      $is_required =  $is_required_parent['pEmail'];
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control chk-email" id="pEmail" name="pEmail" placeholder="<?php echo $pl; ?>" type="email">
                            </div>
                        </div>

                        <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Username"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Username","WPSchoolPress"));
                                  $pl = "";
                                  if(isset($item['section']) && $item['section'] == "parent" && isset($item['pUsername'])){
                                        echo $pl = esc_html($item['pUsername'],"WPSchoolPress");

                                  }else{
                                      echo $pl = esc_html("Username","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['pUsername'])){
                                      $is_required =  $is_required_parent['pUsername'];
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control chk-username" id="p_username" name="pUsername" placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>

                        <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Password">  <?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Password","WPSchoolPress"));
                                    $pl = "";
                                    if(isset($item['section']) && $item['section'] == "parent" && isset($item['pPassword'])){
                                          echo $pl = esc_html($item['pPassword'],"WPSchoolPress");

                                    }else{
                                        echo $pl = esc_html("Password","WPSchoolPress");
                                    }
                                    /*Check Required Field*/
                                    if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['pPassword'])){
                                        $is_required =  $is_required_parent['pPassword'];
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                                    <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="password" data-is_required="<?php echo $is_required;?>" class="wpsp-form-control" id="p_password" name="pPassword" placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>

                        <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="ConfirmPassword">  <?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Confirm Password","WPSchoolPress"));
                                    $pl = "";
                                    if(isset($item['section']) && $item['section'] == "parent" && isset($item['pConfirmPassword'])){
                                          echo $pl = esc_html($item['pConfirmPassword'],"WPSchoolPress");

                                    }else{
                                        echo $pl = esc_html("Confirm Password","WPSchoolPress");
                                    }
                                    /*Check Required Field*/
                                    if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['pConfirmPassword'])){
                                        $is_required =  $is_required_parent['pConfirmPassword'];
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                                    <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                <input type="password" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="p_confirmpassword" name="pConfirmPassword"  placeholder="<?php echo $pl; ?>">
                            </div>
                        </div>
                    <?php } ?>
                   <!--  <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="pEmail">Email Address</label>
                            <input class="wpsp-form-control chk-email" id="pEmail" name="pEmail" placeholder="Parent Email" type="email" value="<?php echo $parentEmail; ?>">
                        </div>
                    </div> -->
                    <div class="wpsp-col-md-3 wpsp-col-sm-3 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="p_edu"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Education","WPSchoolPress"));
                              $pl = "";
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_edu'])){
                                    echo $pl = esc_html($item['p_edu'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("Education","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_edu'])){
                                  $is_required =  $is_required_parent['p_edu'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" value="<?php echo $stinfo->p_edu; ?>" name="p_edu" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-3 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="p_profession"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Profession","WPSchoolPress"));
                              $pl = "";
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_profession'])){
                                    echo $pl = esc_html($item['p_profession'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("Profession","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_profession'])){
                                  $is_required =  $is_required_parent['p_profession'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" name="p_profession" value="<?php echo $stinfo->p_profession; ?>" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-3 wpsp-col-sm-3 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label for="phone"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Phone","WPSchoolPress"));
                              $pl = "";
                              if(isset($item['section']) && $item['section'] == "parent" && isset($item['s_phone'])){
                                    echo $pl = esc_html($item['s_phone'],"WPSchoolPress");

                              }else{
                                  echo $pl = esc_html("Phone","WPSchoolPress");
                              }
                              /*Check Required Field*/
                              if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['s_phone'])){
                                  $is_required =  $is_required_parent['s_phone'];
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="phone" value="<?php echo $stinfo->s_phone; ?>" name="s_phone" placeholder="<?php echo $pl; ?>" >
                        </div>
                    </div>

                    <div class="wpsp-col-lg-3 wpsp-col-md-3 wpsp-col-sm-3 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label for="bloodgroup"><?php $item =  apply_filters( 'wpsp_student_parent_title_item',esc_html("Blood Group","WPSchoolPress"));
                                $pl = "";
                                if(isset($item['section']) && $item['section'] == "parent" && isset($item['p_bloodgroup'])){
                                      echo $pl = esc_html($item['p_bloodgroup'],"WPSchoolPress");

                                }else{
                                    echo $pl = esc_html("Blood Group","WPSchoolPress");
                                }
                                /*Check Required Field*/
                                if(isset($is_required_parent['section']) && $is_required_parent['section'] == "parent" && isset($is_required_parent['p_bloodgroup'])){
                                    $is_required =  $is_required_parent['p_bloodgroup'];
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                              </label>
                            <select data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="Bloodgroup" name="p_bloodgrp">
                                <option value="">Select Blood Group</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'O+') echo "selected"; ?> value="O+">O +</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'O-') echo "selected"; ?> value="O-">O -</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'A+') echo "selected"; ?> value="A+">A +</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'A-') echo "selected"; ?> value="A-">A -</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'B+') echo "selected"; ?> value="B+">B +</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'B-') echo "selected"; ?> value="B-">B -</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'AB+') echo "selected"; ?> value="AB+">AB +</option>
                                <option <?php if ($stinfo->p_bloodgrp == 'AB-') echo "selected"; ?> value="AB-">AB -</option>
                            </select>
                        </div>
                    </div>
                     <?php
                            do_action('wpsp_after_student_parent_detail_fields');
                        ?>
                    <div class="wpsp-col-xs-12">
                        <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform">Next</button>&nbsp;&nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_school_detail', esc_html__( 'School Details', 'WPSchoolPress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
                <div class="wpsp-row">
                     <?php
                          do_action('wpsp_before_student_school_detail_fields');
                          /*Required field Hook*/
                          $is_required_school = apply_filters('wpsp_student_school_is_required',array());
                      ?>
                    <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="dateofbirth"><?php $item =  apply_filters( 'wpsp_student_school_title_item',esc_html("Joining Date","WPSchoolPress"));
                                $pl = "";
                                if(isset($item['section']) && $item['section'] == "school" && isset($item['s_doj'])){
                                      echo $pl = esc_html($item['s_doj'],"WPSchoolPress");

                                }else{
                                    echo $pl = esc_html("Joining Date","WPSchoolPress");
                                }
                                /*Check Required Field*/
                                if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['s_doj'])){
                                    $is_required =  $is_required_school['s_doj'];
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                              </label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control select_date" id="Doj" value="<?php echo !empty( $stinfo->s_doj ) ? wpsp_ViewDate($stinfo->s_doj) : '' ; ?>" name="s_doj" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>
                    <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                      <?php
                      $classes = [];
                      $classIDArray = [];
                      $class_table = $wpdb->prefix . "wpsp_class";
                      $class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
                      $classes = $wpdb->get_results("select cid,c_name from $class_table");
                      ?>
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="Class"><?php $item =  apply_filters( 'wpsp_student_school_title_item',esc_html("Class","WPSchoolPress"));
                                $pl = "";
                                if(isset($item['section']) && $item['section'] == "school" && isset($item['Class'])){
                                      echo $pl = esc_html($item['Class'],"WPSchoolPress");

                                }else{
                                    echo $pl = esc_html("Class","WPSchoolPress");
                                }
                                /*Check Required Field*/
                                if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['Class'])){
                                    $is_required =  $is_required_school['Class'];
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                              </label>
                            <?php

                            if (is_numeric($stinfo->class_id)){
                              $classIDArray[] = $stinfo->class_id;
                            }else{
                              $classIDArray = unserialize($stinfo->class_id);
                            }
                            $prohistory    =    wpsp_check_pro_version('wpsp_mc_version');
                            $prodisablehistory    =    ! $prohistory['status'] ? 'notinstalled' : 'installed';

                            if( $prodisablehistory == 'installed'){
                              echo '<select data-is_required="'.$is_required.'" class="wpsp-form-control multiselect student-profile" multiple="multiple" name="Class[]">';
                            }else{
                              echo '<select data-is_required="'.$is_required.'" class="wpsp-form-control" name="Class[]">';
                              echo '<option value="" disabled selected>Select Class</option>';
                            }

                            foreach ($classes as $class) {
                            ?>
                              <option value="<?php echo $class->cid; ?>" <?php if(!empty($classIDArray)){ if (in_array( $class->cid , $classIDArray )) echo 'selected'; }?>>
                                <?php echo $class->c_name; ?>
                              </option>
                            <?php }?>
                          </select>

                          <input type="hidden" name="prev_select_class" value="<?php echo $stinfo->class_id;?>">
                        </div>
                        <div class="date-input-block">
                          <table class="table table-bordered" width="100%" cellspacing="0" cellpadding="5">
                          <?php

                          foreach ($classes as $class) {
                            $class_data = $wpdb->get_results("select * from $class_mapping_table where sid=$student_index AND cid = $class->cid");
                            if(!empty($classIDArray)){
                            if (in_array( $class->cid , $classIDArray )){
                            ?>
                            <tr>
                              <td style="border:1px solid;padding:5px;">
                                <strong><?php echo $class->c_name; ?></strong>
                              </td>
                              <td style="border:1px solid;padding:5px;">
                              <input type='text' class='someclass datepicker' name='Classdata[]' placeholder='yyyy-mm-dd' <?php echo (((isset($class_data[0]->date) && ($class_data[0]->date != ''))) ? 'value="'.$class_data[0]->date.'"' : ''); ?>>
                            </td>
                            </tr>
                          <?php } }
                           }?>
                            </table>
                        </div>
                    </div>
                    <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="dateofbirth">  <?php $item =  apply_filters( 'wpsp_student_school_title_item',esc_html("Roll Number","WPSchoolPress"));
                                  $pl = "";
                                  if(isset($item['section']) && $item['section'] == "school" && isset($item['s_rollno'])){
                                        echo $pl = esc_html($item['s_rollno'],"WPSchoolPress");

                                  }else{
                                      echo $pl = esc_html("Roll Number","WPSchoolPress");
                                  }
                                  /*Check Required Field*/
                                  if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['s_rollno'])){
                                      $is_required =  $is_required_school['s_rollno'];
                                  }else{
                                      $is_required = true;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span>
                                </label>
                            <input type="text" data-is_required="<?php echo $is_required; ?>" class="wpsp-form-control" id="Rollno" value="<?php echo $stinfo->s_rollno; ?>" name="s_rollno" placeholder="<?php echo $pl; ?>">
                        </div>
                    </div>
                     <?php
                      do_action('wpsp_after_student_school_detail_fields');
                  ?>
                </div>
                <div class="">
                    <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform">Update</button>&nbsp;&nbsp;
                    <a href="<?php echo wpsp_admin_url();?>sch-student" class="wpsp-btn wpsp-dark-btn">Back</a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php } else { echo "Sorry! No data retrieved";} ?>
