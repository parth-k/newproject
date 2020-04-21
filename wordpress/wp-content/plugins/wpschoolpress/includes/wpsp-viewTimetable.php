<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
function wpsp_ViewTimetable($class_id){
        global $wpdb;
        $tt_table = $wpdb->prefix . "wpsp_timetable";
        $subject_table = $wpdb->prefix . "wpsp_subject";
        $wpsp_hours_table = $wpdb->prefix . "wpsp_workinghours";
        $get_heading = $wpdb->get_row("SELECT * from $tt_table where class_id=$class_id and heading != ' ' ");
        $response   =   array();
        ob_start();
        if ($get_heading == null) {
            echo '<section class="content">             
                    <div class="wpsp-text-red">Timetable Has Not Created For This Class.</div>             
                </section> </div></div>';            
            $response['status'] =   1;
        } else {
            $response['status'] =   2;
            $session = unserialize($get_heading->heading);
            $tt_days=$wpdb->get_results("select * from $tt_table where class_id='$class_id'",ARRAY_A);
            foreach($tt_days as $ttd){
                $timetable[$ttd['day']][$ttd['time_id']]=$ttd['subject_id'];
            }
            $ses_info = $wpdb->get_results("Select * from $wpsp_hours_table");
            foreach($ses_info as $time){
                $period_times[$time->id]=array('start'=>$time->begintime,'end'=>$time->endtime,'type'=>$time->type);
            }           
            ?> 
             <section> 
          
                <div class="wpsp-row">
                    <div class="wpsp-col-md-12">
                    <table class="wpsp-table wpsp-table-bordered"  id="timetable_table" cellspacing="0" width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <?php foreach ($session as $sess) { ?>
                                    <th><?php echo isset( $period_times[$sess]['start'] ) ? $period_times[$sess]['start'] :'';
                                              echo " - " ;
                                            echo isset( $period_times[$sess]['end'] ) ? $period_times[$sess]['end'] :''; ?></th>
                                    <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $week_days=array('1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday','7'=>'Sunday');
                            for ($j = 1; $j <= 7; $j++) {
                                if(empty($timetable[$j])){
                                    continue;
                                }
                            ?>
                                <tr id="<?php echo $j; ?>">
                                    <td><span class="daynam"><?php echo $week_days[$j];?></span></td>
                                    <?php
                                    $subida = array(); 
                                    $subida1 = array(); 
                                    $subtimesloat = array();
                                   
                                    $get_heading = $wpdb->get_results("SELECT * from $tt_table where class_id=$class_id and day = $j ORDER BY session_id ASC");
   
  
                                    foreach ($get_heading as $subid)
                                    {
                                         $subida[] = $subid->subject_id;
                                          $subtimesloat[] = $subid->time_id;
                                        $subida1[] = $subid->is_active;
                                         reset($subida);
                                         reset($subida1);
                                         reset($subtimesloat);
                                    }
                                        $pa = 0;
                                     
                                        foreach($session as $ses){
                                            if($ses != $subtimesloat[$pa]){
                                            ?>
                                                <td class = "<?php echo $ses; ?> " tid="<?php echo $subida[$pa]; ?>" data-sessionid="<?php echo $key; ?>"> - </td>
                                            <?php
                                                continue;
                                            }
                                            $ses_type = isset( $period_times[$ses]['type'] ) ? $period_times[$ses]['type'] :'';
                                            if(isset($timetable[$j][$ses]))
                                                $sub_id=$timetable[$j][$ses];
                                            else
                                                $sub_id='';
                                            if ($sub_id == '') {
                                                if($ses_type==0)
                                                    $sub_name = '<i>Break</i>';
                                                else
                                                    $sub_name='-';
                                            } else {
                                                $sub_name_f = $wpdb->get_results("SELECT sub_name from $subject_table where id=$subida[$pa]");
                                                if($subida1[$pa] == 1){
                                                $sub_name = '-';
                                                    } else {
                                                        $sub_name = isset( $sub_name_f[0]->sub_name ) ? $sub_name_f[0]->sub_name : 'N/A';
                                                    }
                                            }
                                            ?>
                                            <td class = "<?php echo $subtimesloat[$pa]; ?> " tid="<?php echo $subida[$pa]; ?>" data-sessionid="<?php echo $key; ?>"> <?php echo $sub_name; ?> </td>
                                            <?php
                                            $pa++;
                                             reset($subida);
                                             reset($subida1);
                                             reset($subtimesloat);
                                        } ?>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
					</div>
                </div>
            </section>
        </div>
    </div>
        <?php
       }
       $response['msg'] =   ob_get_clean();
       return $response;
}?>