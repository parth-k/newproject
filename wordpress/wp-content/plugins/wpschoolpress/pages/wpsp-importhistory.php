<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
        //get_currentuserinfo();
            $current_user_role=$current_user->roles[0];
        wpsp_topbar();
        wpsp_sidebar();
        wpsp_body_start();
        if($current_user_role=='administrator' || $current_user_role=='teacher')
        { ?>
            <div class="wpsp-card">
                 <div class="wpsp-card-body">
                                <?php
                                    $importtable=$wpdb->prefix."wpsp_import_history";
                                    $result = $wpdb->get_results("SELECT * FROM $importtable");
                                    $imtype=array('-','Student','Teacher','Parent','Mark');
                                ?>
                                  <table class="wpsp-table" id="import" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th class="nosort">#</th>
                                        <th>Imported Date</th>
                                        <th>Type</th>
                                        <th>Number Of Rows</th>
                                        <?php if($current_user_role=='administrator'){?>
                                        <th class="nosort">Undo</th>
<?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     <?php
                                     $count = 0;
                                     foreach($result as $value){
                                         $count = $count+1;
                                     ?>
                                        <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo wpsp_ViewDate($value->time); ?></td>
                                        <td><?php echo $imtype[$value->type];?></td>
                                        <td><?php echo $value->count; ?></td>
<?php if($current_user_role=='administrator'){?>
                                        <td align="center">
                                            <div class="wpsp-action-col">
                                                <a href="javascript:;" class="undoimport" value="<?php echo intval($value->id);?>">Click to undo</a>
                                            </div>
                                        </td>
                                    <?php } ?>
                                        </tr>
                                     <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="nosort">#</th>
                                            <th>Imported Date</th>
                                            <th>Type</th>
                                            <th>Number Of Rows</th>
<?php if($current_user_role=='administrator'){?>
                                            <th class="nosort">Undo</th>
<?php } ?>
                                        </tr>
                                    </tfoot>
                                  </table>
                            </div>
                        </div>
     <?php }
        wpsp_body_end();
        wpsp_footer();
    }
    else {
        include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
    ?>
