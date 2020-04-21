<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wpdb;
        $current_user_role=$current_user->roles[0];
            if( $current_user_role=='administrator' || $current_user_role=='teacher')
            {
            wpsp_topbar();
            wpsp_sidebar();
            wpsp_body_start();

             $user_id = base64_decode($_GET['id']);
            ?>
        <div class="wpsp-row">
                    <div class="wpsp-col-md-12">
                        <div class="wpsp-card">
                        <div class="wpsp-card-head ui-sortable-handle">
                                    <h3 class="wpsp-card-title">Payment Details </h3>
                              
                                </div>
                            <div class="wpsp-card-body">

                                <table id="class_table" class="wpsp-table wpsp-table-bordered wpsp-table-striped" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                            <th>Student Name</th>
                                            <th>Roll No</th>
                                            <th>Class Name</th>
                                            <th>Class Teacher Name</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Class Fee Status</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                <?php
$classid = array();
$productid = array();
$stable=$wpdb->prefix."wpsp_student";
$ctlass=$wpdb->prefix."wpsp_class";
$cteacher =$wpdb->prefix."wpsp_teacher";
$wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.wp_usr_id = '".$user_id."'");
$classid_da = maybe_unserialize($wpsp_stud_data[0]->class_id);
$courses1 = get_user_meta($wpsp_stud_data[0]->parent_wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses2 = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter');
$courses = array_merge($courses1,$courses2);

if ( ! empty( $courses ) ) {
      $courses = maybe_unserialize( $courses );
    } else {
      $courses = array();
    }

$courses_check=array();
$courses_value=array();
$courses_value1=array();
foreach($courses as $key => $value){
    foreach($value as $key1 => $value1){
                    $courses_check[] = $key1;
                        $courses_value[] = $value1;
                }

    $pr++;
}
foreach($courses_value as   $valueorder){
    $courses_value1[] = $valueorder[0];
}
$wpsp_clas_data =$wpdb->get_results("SELECT * FROM  $ctlass
 where cid IN('".implode("','",$classid_da)."') and c_fee_type = 'paid'");
$proid = 0;
foreach($wpsp_clas_data as $clsloop){
    $wpsp_teacher_data =$wpdb->get_results("SELECT * FROM  $cteacher
 where wp_usr_id = '".$clsloop->teacher_id."'");
        if(in_array($clsloop->cid, $courses_check)){
        $paid = 'Paid';
    }else{
        $paid = 'Not Paid';
    }
        if(in_array($clsloop->cid, $courses_check)){

                         $order = new WC_Order($courses_value1[$proid]);
                          $order_data = $order->get_data();
                          $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
                          $price = get_post_meta( $courses_value1[$proid], '_order_total', true );
                                $proid++;
                }

?>
                                    <tr>
                                                <td><?php echo  $wpsp_stud_data[0]->s_fname.' '.$wpsp_stud_data[0]->s_lname;?></td>
                                                <td><?php echo  $wpsp_stud_data[0]->s_rollno;?></td>
                                                <td> <?php echo  $clsloop->c_name;?></td>
                                                <td> <?php echo  $wpsp_teacher_data[0]->first_name.' '. $wpsp_teacher_data[0]->last_name ;?></td>
                                                <td><?php if($price != ''){ echo get_woocommerce_currency_symbol() .$price; }else { echo '-'; }?></td>
                                                <td><?php if($order_date_created != ''){ echo $order_date_created; }else { echo '-'; }?></td>
                                                <td><?php echo $paid;?></td>

                                    </tr>
                                    <?php
                                $pr++;


                                 }?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

        <?php

            wpsp_body_end();
            wpsp_footer();
        }
        elseif($current_user_role=='parent' || $current_user_role='student')
        {
            wpsp_topbar();
            wpsp_sidebar();
            wpsp_body_start();
            ?>

                <div class="wpsp-row">
                    <div class="wpsp-col-md-12">
                        <div class="wpsp-card">
                        <div class="wpsp-card-head ui-sortable-handle">
                                    <h3 class="wpsp-card-title">Payment Details </h3>

                                </div>
                            <div class="wpsp-card-body">

                                <table id="class_table" class="wpsp-table wpsp-table-bordered wpsp-table-striped" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                            <th>Student Name</th>
                                            <th>Roll No</th>
                                            <th>Class Name</th>
                                            <th>Class Teacher Name</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Class Fee Status</th>

                                    </tr>
                                    </thead>
                                    <tbody>


<?php
$proid = 0;
 $proid1 = 0;
$classid = array();
$productid = array();
$stable=$wpdb->prefix."wpsp_student";
$ctlass=$wpdb->prefix."wpsp_class";
$cteacher =$wpdb->prefix."wpsp_teacher";
if($current_user_role == 'parent')
{

         $user_id = $wpdb->get_var("SELECT wp_usr_id FROM $stable where parent_wp_usr_id = $current_user->ID");
}
else {
 $user_id = $current_user->ID;
}
if($current_user_role == 'parent')
{
    $wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.parent_wp_usr_id = '".$current_user->ID."' and c.c_fee_type = 'paid'");
 }else {
$wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.wp_usr_id = '".$user_id."' and c.c_fee_type = 'paid'");
}
//$classid_da = maybe_unserialize($wpsp_stud_data[0]->class_id);
    $cid = [];
    if(is_numeric($wpsp_stud_data[0]->class_id) ){
        $cid[] = $wpsp_stud_data[0]->class_id;
    }else{
        $class_id_array = unserialize( $wpsp_stud_data[0]->class_id );
        $cid[] = $class_id_array;
    }
    if($current_user_role == 'parent')
{
     $courses1 = get_user_meta( $current_user->ID, '_pay_woocommerce_enrolled_class_access_counter');
 $courses2 = get_user_meta($wpsp_stud_data[0]->wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
 $courses = array_merge($courses1,$courses2);
}
else {
$courses1 = get_user_meta($wpsp_stud_data[0]->parent_wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses2 = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses = array_merge($courses1,$courses2);
}

if ( ! empty( $courses ) ) {
      $courses = maybe_unserialize( $courses );
    } else {
      $courses = array();
    }

 $courses_check=array();
 $courses_value=array();
 $courses_value1=array();

 $courses_check=array();
                foreach($courses as $key => $value)
                {
                    foreach($value as $key1 => $value1)
                    {
                        if(!empty($value1))
                        {
                            $courses_check[] = $key1;
                                $courses_value[] = $value1;
                        }
                    }
                }

foreach($courses_value as   $valueorder){
    $courses_value1[] = $valueorder[0];
}
  $courses_valu_final = array();
foreach($courses_value as $key => $value)
{
    foreach($value as $key1 => $value1)
    {
        $courses_valu_final[] = $value1;
    }
}

$product_id=array();
foreach($courses_valu_final as $key => $value ){

$order = wc_get_order($value);
$items = $order->get_items();

foreach ( $items as $item ) {
    $product_id[] = $item['product_id'];
}
}


    $wpsp_teacher_data =$wpdb->get_results("SELECT * FROM  $cteacher
 where wp_usr_id = '".$clsloop->teacher_id."'");
        if(in_array($clsloop->cid, $courses_check)){
        $paid = 'Paid';
    }else{
        $paid = 'Not Paid';
    }

    $args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => -1
                                );
                    $obituary_query = new WP_Query($args);

                    while ($obituary_query->have_posts()) : $obituary_query->the_post();
    $courses1 = get_post_meta( get_the_ID(), '_related_class', true );
                    if ( ! empty( $courses1 ) ) {
                        $courses1 = maybe_unserialize( $courses1 );
                    } else {
                        $courses1 = array();
                    }



                    foreach($courses1 as $key => $value){

        if(in_array($value, $cid[0])){
            $wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s INNER JOIN $ctlass c where s.wp_usr_id = '".$user_id."' and c.c_fee_type = 'paid' and c.cid = ".$value);
                                $wpsp_tech_data =$wpdb->get_results("SELECT * FROM  $cteacher t INNER JOIN $ctlass c ON t.wp_usr_id=c.teacher_id where c.cid = '".$value."' and c.c_fee_type = 'paid'");
    ?>
                                <tr>
                                                <td><?php echo  $wpsp_stud_data[0]->s_fname.' '.$wpsp_stud_data[0]->s_lname;?></td>
                                                <td><?php echo  $wpsp_stud_data[0]->s_rollno;?></td>
                                                    <td> <?php echo  $wpsp_stud_data[0]->c_name;?></td>
                                                <td> <?php echo  $wpsp_tech_data[0]->first_name.' '.$wpsp_tech_data[0]->last_name;?></td>
                                                <td><?php if(in_array(get_the_ID(), $product_id)){

 $order = new WC_Order($courses_valu_final[$proid]);
                          $order_data = $order->get_data();

                          $price = get_post_meta( $courses_valu_final[$proid], '_order_total', true );
                                                     if($price != ''){ echo get_woocommerce_currency_symbol() .$price; }$proid++;}else { echo '-'; }?></td>
                                                <td><?php
                                                if(in_array(get_the_ID(), $product_id)){
 $order = new WC_Order($courses_valu_final[$proid1]);
                          $order_data = $order->get_data();
                          $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');

                                                if($order_date_created != ''){ echo $order_date_created; }$proid1++;}else { echo '-'; }?></td>
                                                <td><?php if(in_array(get_the_ID(), $product_id)){ echo "Paid For ".get_the_title(); } else { echo "Not Paid";}?></td>
                                                <!-- <td><a class="wpsp-btn" href="<?php echo get_permalink();?>" target="_blank">Pay Now</a></td> -->
                                    </tr>
                                        <?php

                                     } }
                                    endwhile;
                wp_reset_query();
                ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
            wpsp_body_end();
            wpsp_footer();
        }
    }
    else{
        //Include Login Section
        include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
?>
