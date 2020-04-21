<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Initilize SchoolPress
 * 
 * Handle to initilize Settings of SchoolPress
 * 
 * @package WPSchoolPress
 * @since 2.0.0
 */
function wpsp_get_setting() {
	global $wpsp_settings_data, $wpdb;
	$wpsp_settings_table	=	$wpdb->prefix."wpsp_settings";
	$wpsp_settings_edit		=	$wpdb->get_results("SELECT * FROM $wpsp_settings_table" );
	foreach($wpsp_settings_edit as $sdat) {
		$wpsp_settings_data[$sdat->option_name]	=	$sdat->option_value;
	}
}
/*
* Send mail when new user register
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_send_user_register_mail( $userInfo = array(), $user_id ) {
	if( !empty( $user_id ) && $user_id > 0 ) {
		wp_new_user_notification( $user_id, '', 'user');
	}
}
/*
* Check current user is authorized or not
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_Authenticate() {
	global $current_user;
	if($current_user->roles[0]!='administrator' && $current_user->roles[0]!='teacher' ) {
		echo "Unauthorized Access!";
		exit;
	}
}
/*
* Check current user has update access or not
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_UpdateAccess($role,$id){
	global $current_user;
	$current_user_role=$current_user->roles[0];
	if( $current_user_role=='administrator' || ( $current_user_role==$role && $current_user->ID==$id ) || $current_user_role=='teacher'  ) {
		return true;	
	} else {
		return false;
	}
}
/*
* Get role of current user
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_CurrentUserRole(){
	global $current_user;
	return isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';
}
/*
* Get add as per given setting
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_ViewDate($date){
	
	global $wpdb, $wpsp_settings_data;	
	$date_format	=	isset( $wpsp_settings_data['date_format'] ) ? $wpsp_settings_data['date_format'] : '';	
	$dformat		=	empty( $date_format ) ? 'm/d/Y' : $date_format;		
	return ( !empty( $date ) && $date!='0000-00-00' ) ? date( $dformat,strtotime($date) ) : $date;
}
/*
* Store date as per given setting
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_StoreDate($date) {
	return ( !empty ( $date ) && $date!='0000-00-00' ) ? date('Y-m-d',strtotime($date)) : $date;
}
/*
* Check for username exists or not
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_CheckUsername($username='',$return=false){
	$username	=	empty( $username ) ? esc_sql($_POST['username'] ) : $username ;
	if ( username_exists( $username ) ) {
        if ($return)
            return true;
        else{
            echo "true";
            wp_die();
        }
    } else {
        if ($return)
            return false;
        else {
            echo "false";
            wp_die();
        }
    }
}
/*
* Check for emailID exists or not
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_CheckEmail(){
	$email=esc_sql(sanitize_email($_POST['email']));
	echo email_exists( $email ) ? "true" : "false";
	wp_die();
}
/*
* Create dynamic email id if not specified
* @package WPSchoolPress
* @since 2.0.0
*/
function wpsp_EmailGen($username){
	return $username."@wpschoolpress.com";
}
/* This function is used for send mail */
function wpsp_send_mail( $to, $subject, $body, $attachment='' ) {
	global $wpsp_settings_data;	
	$email			=	$wpsp_settings_data['sch_email'];
	$from			=	$wpsp_settings_data['sch_name'];
	$admin_email	=	get_option( 'admin_email' );
		
	$email		=	!empty( $email ) ? $email : $admin_email;
	$from		=	!empty( $from ) ? $from : get_option( 'blogname'  );
	$headers	=	 array();
	
	if( !empty( $email ) && !empty( $from ) ) {
		$headers[]	=	"From: $from <$email>";
		$headers[] 	=	'Content-Type: text/html; charset=UTF-8';
	}	
	if( wp_mail( $to, $subject, $body, $headers, $attachment )) return true;	
	else return false;
}
/* This function is return country list */
function wpsp_county_list() {
	return array(
	'AF' => __( 'Afghanistan', 'WPSchoolPress' ),
	'AX' => __( '&#197;land Islands', 'WPSchoolPress' ),
	'AL' => __( 'Albania', 'WPSchoolPress' ),
	'DZ' => __( 'Algeria', 'WPSchoolPress' ),
	'AD' => __( 'Andorra', 'WPSchoolPress' ),
	'AO' => __( 'Angola', 'WPSchoolPress' ),
	'AI' => __( 'Anguilla', 'WPSchoolPress' ),
	'AQ' => __( 'Antarctica', 'WPSchoolPress' ),
	'AG' => __( 'Antigua and Barbuda', 'WPSchoolPress' ),
	'AR' => __( 'Argentina', 'WPSchoolPress' ),
	'AM' => __( 'Armenia', 'WPSchoolPress' ),
	'AW' => __( 'Aruba', 'WPSchoolPress' ),
	'AU' => __( 'Australia', 'WPSchoolPress' ),
	'AT' => __( 'Austria', 'WPSchoolPress' ),
	'AZ' => __( 'Azerbaijan', 'WPSchoolPress' ),
	'BS' => __( 'Bahamas', 'WPSchoolPress' ),
	'BH' => __( 'Bahrain', 'WPSchoolPress' ),
	'BD' => __( 'Bangladesh', 'WPSchoolPress' ),
	'BB' => __( 'Barbados', 'WPSchoolPress' ),
	'BY' => __( 'Belarus', 'WPSchoolPress' ),
	'BE' => __( 'Belgium', 'WPSchoolPress' ),
	'PW' => __( 'Belau', 'WPSchoolPress' ),
	'BZ' => __( 'Belize', 'WPSchoolPress' ),
	'BJ' => __( 'Benin', 'WPSchoolPress' ),
	'BM' => __( 'Bermuda', 'WPSchoolPress' ),
	'BT' => __( 'Bhutan', 'WPSchoolPress' ),
	'BO' => __( 'Bolivia', 'WPSchoolPress' ),
	'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'WPSchoolPress' ),
	'BA' => __( 'Bosnia and Herzegovina', 'WPSchoolPress' ),
	'BW' => __( 'Botswana', 'WPSchoolPress' ),
	'BV' => __( 'Bouvet Island', 'WPSchoolPress' ),
	'BR' => __( 'Brazil', 'WPSchoolPress' ),
	'IO' => __( 'British Indian Ocean Territory', 'WPSchoolPress' ),
	'VG' => __( 'British Virgin Islands', 'WPSchoolPress' ),
	'BN' => __( 'Brunei', 'WPSchoolPress' ),
	'BG' => __( 'Bulgaria', 'WPSchoolPress' ),
	'BF' => __( 'Burkina Faso', 'WPSchoolPress' ),
	'BI' => __( 'Burundi', 'WPSchoolPress' ),
	'KH' => __( 'Cambodia', 'WPSchoolPress' ),
	'CM' => __( 'Cameroon', 'WPSchoolPress' ),
	'CA' => __( 'Canada', 'WPSchoolPress' ),
	'CV' => __( 'Cape Verde', 'WPSchoolPress' ),
	'KY' => __( 'Cayman Islands', 'WPSchoolPress' ),
	'CF' => __( 'Central African Republic', 'WPSchoolPress' ),
	'TD' => __( 'Chad', 'WPSchoolPress' ),
	'CL' => __( 'Chile', 'WPSchoolPress' ),
	'CN' => __( 'China', 'WPSchoolPress' ),
	'CX' => __( 'Christmas Island', 'WPSchoolPress' ),
	'CC' => __( 'Cocos (Keeling) Islands', 'WPSchoolPress' ),
	'CO' => __( 'Colombia', 'WPSchoolPress' ),
	'KM' => __( 'Comoros', 'WPSchoolPress' ),
	'CG' => __( 'Congo (Brazzaville)', 'WPSchoolPress' ),
	'CD' => __( 'Congo (Kinshasa)', 'WPSchoolPress' ),
	'CK' => __( 'Cook Islands', 'WPSchoolPress' ),
	'CR' => __( 'Costa Rica', 'WPSchoolPress' ),
	'HR' => __( 'Croatia', 'WPSchoolPress' ),
	'CU' => __( 'Cuba', 'WPSchoolPress' ),
	'CW' => __( 'Cura&Ccedil;ao', 'WPSchoolPress' ),
	'CY' => __( 'Cyprus', 'WPSchoolPress' ),
	'CZ' => __( 'Czech Republic', 'WPSchoolPress' ),
	'DK' => __( 'Denmark', 'WPSchoolPress' ),
	'DJ' => __( 'Djibouti', 'WPSchoolPress' ),
	'DM' => __( 'Dominica', 'WPSchoolPress' ),
	'DO' => __( 'Dominican Republic', 'WPSchoolPress' ),
	'EC' => __( 'Ecuador', 'WPSchoolPress' ),
	'EG' => __( 'Egypt', 'WPSchoolPress' ),
	'SV' => __( 'El Salvador', 'WPSchoolPress' ),
	'GQ' => __( 'Equatorial Guinea', 'WPSchoolPress' ),
	'ER' => __( 'Eritrea', 'WPSchoolPress' ),
	'EE' => __( 'Estonia', 'WPSchoolPress' ),
	'ET' => __( 'Ethiopia', 'WPSchoolPress' ),
	'FK' => __( 'Falkland Islands', 'WPSchoolPress' ),
	'FO' => __( 'Faroe Islands', 'WPSchoolPress' ),
	'FJ' => __( 'Fiji', 'WPSchoolPress' ),
	'FI' => __( 'Finland', 'WPSchoolPress' ),
	'FR' => __( 'France', 'WPSchoolPress' ),
	'GF' => __( 'French Guiana', 'WPSchoolPress' ),
	'PF' => __( 'French Polynesia', 'WPSchoolPress' ),
	'TF' => __( 'French Southern Territories', 'WPSchoolPress' ),
	'GA' => __( 'Gabon', 'WPSchoolPress' ),
	'GM' => __( 'Gambia', 'WPSchoolPress' ),
	'GE' => __( 'Georgia', 'WPSchoolPress' ),
	'DE' => __( 'Germany', 'WPSchoolPress' ),
	'GH' => __( 'Ghana', 'WPSchoolPress' ),
	'GI' => __( 'Gibraltar', 'WPSchoolPress' ),
	'GR' => __( 'Greece', 'WPSchoolPress' ),
	'GL' => __( 'Greenland', 'WPSchoolPress' ),
	'GD' => __( 'Grenada', 'WPSchoolPress' ),
	'GP' => __( 'Guadeloupe', 'WPSchoolPress' ),
	'GT' => __( 'Guatemala', 'WPSchoolPress' ),
	'GG' => __( 'Guernsey', 'WPSchoolPress' ),
	'GN' => __( 'Guinea', 'WPSchoolPress' ),
	'GW' => __( 'Guinea-Bissau', 'WPSchoolPress' ),
	'GY' => __( 'Guyana', 'WPSchoolPress' ),
	'HT' => __( 'Haiti', 'WPSchoolPress' ),
	'HM' => __( 'Heard Island and McDonald Islands', 'WPSchoolPress' ),
	'HN' => __( 'Honduras', 'WPSchoolPress' ),
	'HK' => __( 'Hong Kong', 'WPSchoolPress' ),
	'HU' => __( 'Hungary', 'WPSchoolPress' ),
	'IS' => __( 'Iceland', 'WPSchoolPress' ),
	'IN' => __( 'India', 'WPSchoolPress' ),
	'ID' => __( 'Indonesia', 'WPSchoolPress' ),
	'IR' => __( 'Iran', 'WPSchoolPress' ),
	'IQ' => __( 'Iraq', 'WPSchoolPress' ),
	'IE' => __( 'Republic of Ireland', 'WPSchoolPress' ),
	'IM' => __( 'Isle of Man', 'WPSchoolPress' ),
	'IL' => __( 'Israel', 'WPSchoolPress' ),
	'IT' => __( 'Italy', 'WPSchoolPress' ),
	'CI' => __( 'Ivory Coast', 'WPSchoolPress' ),
	'JM' => __( 'Jamaica', 'WPSchoolPress' ),
	'JP' => __( 'Japan', 'WPSchoolPress' ),
	'JE' => __( 'Jersey', 'WPSchoolPress' ),
	'JO' => __( 'Jordan', 'WPSchoolPress' ),
	'KZ' => __( 'Kazakhstan', 'WPSchoolPress' ),
	'KE' => __( 'Kenya', 'WPSchoolPress' ),
	'KI' => __( 'Kiribati', 'WPSchoolPress' ),
	'KW' => __( 'Kuwait', 'WPSchoolPress' ),
	'KG' => __( 'Kyrgyzstan', 'WPSchoolPress' ),
	'LA' => __( 'Laos', 'WPSchoolPress' ),
	'LV' => __( 'Latvia', 'WPSchoolPress' ),
	'LB' => __( 'Lebanon', 'WPSchoolPress' ),
	'LS' => __( 'Lesotho', 'WPSchoolPress' ),
	'LR' => __( 'Liberia', 'WPSchoolPress' ),
	'LY' => __( 'Libya', 'WPSchoolPress' ),
	'LI' => __( 'Liechtenstein', 'WPSchoolPress' ),
	'LT' => __( 'Lithuania', 'WPSchoolPress' ),
	'LU' => __( 'Luxembourg', 'WPSchoolPress' ),
	'MO' => __( 'Macao S.A.R., China', 'WPSchoolPress' ),
	'MK' => __( 'Macedonia', 'WPSchoolPress' ),
	'MG' => __( 'Madagascar', 'WPSchoolPress' ),
	'MW' => __( 'Malawi', 'WPSchoolPress' ),
	'MY' => __( 'Malaysia', 'WPSchoolPress' ),
	'MV' => __( 'Maldives', 'WPSchoolPress' ),
	'ML' => __( 'Mali', 'WPSchoolPress' ),
	'MT' => __( 'Malta', 'WPSchoolPress' ),
	'MH' => __( 'Marshall Islands', 'WPSchoolPress' ),
	'MQ' => __( 'Martinique', 'WPSchoolPress' ),
	'MR' => __( 'Mauritania', 'WPSchoolPress' ),
	'MU' => __( 'Mauritius', 'WPSchoolPress' ),
	'YT' => __( 'Mayotte', 'WPSchoolPress' ),
	'MX' => __( 'Mexico', 'WPSchoolPress' ),
	'FM' => __( 'Micronesia', 'WPSchoolPress' ),
	'MD' => __( 'Moldova', 'WPSchoolPress' ),
	'MC' => __( 'Monaco', 'WPSchoolPress' ),
	'MN' => __( 'Mongolia', 'WPSchoolPress' ),
	'ME' => __( 'Montenegro', 'WPSchoolPress' ),
	'MS' => __( 'Montserrat', 'WPSchoolPress' ),
	'MA' => __( 'Morocco', 'WPSchoolPress' ),
	'MZ' => __( 'Mozambique', 'WPSchoolPress' ),
	'MM' => __( 'Myanmar', 'WPSchoolPress' ),
	'NA' => __( 'Namibia', 'WPSchoolPress' ),
	'NR' => __( 'Nauru', 'WPSchoolPress' ),
	'NP' => __( 'Nepal', 'WPSchoolPress' ),
	'NL' => __( 'Netherlands', 'WPSchoolPress' ),
	'AN' => __( 'Netherlands Antilles', 'WPSchoolPress' ),
	'NC' => __( 'New Caledonia', 'WPSchoolPress' ),
	'NZ' => __( 'New Zealand', 'WPSchoolPress' ),
	'NI' => __( 'Nicaragua', 'WPSchoolPress' ),
	'NE' => __( 'Niger', 'WPSchoolPress' ),
	'NG' => __( 'Nigeria', 'WPSchoolPress' ),
	'NU' => __( 'Niue', 'WPSchoolPress' ),
	'NF' => __( 'Norfolk Island', 'WPSchoolPress' ),
	'KP' => __( 'North Korea', 'WPSchoolPress' ),
	'NO' => __( 'Norway', 'WPSchoolPress' ),
	'OM' => __( 'Oman', 'WPSchoolPress' ),
	'PK' => __( 'Pakistan', 'WPSchoolPress' ),
	'PS' => __( 'Palestinian Territory', 'WPSchoolPress' ),
	'PA' => __( 'Panama', 'WPSchoolPress' ),
	'PG' => __( 'Papua New Guinea', 'WPSchoolPress' ),
	'PY' => __( 'Paraguay', 'WPSchoolPress' ),
	'PE' => __( 'Peru', 'WPSchoolPress' ),
	'PH' => __( 'Philippines', 'WPSchoolPress' ),
	'PN' => __( 'Pitcairn', 'WPSchoolPress' ),
	'PL' => __( 'Poland', 'WPSchoolPress' ),
	'PT' => __( 'Portugal', 'WPSchoolPress' ),
	'QA' => __( 'Qatar', 'WPSchoolPress' ),
	'RE' => __( 'Reunion', 'WPSchoolPress' ),
	'RO' => __( 'Romania', 'WPSchoolPress' ),
	'RU' => __( 'Russia', 'WPSchoolPress' ),
	'RW' => __( 'Rwanda', 'WPSchoolPress' ),
	'BL' => __( 'Saint Barth&eacute;lemy', 'WPSchoolPress' ),
	'SH' => __( 'Saint Helena', 'WPSchoolPress' ),
	'KN' => __( 'Saint Kitts and Nevis', 'WPSchoolPress' ),
	'LC' => __( 'Saint Lucia', 'WPSchoolPress' ),
	'MF' => __( 'Saint Martin (French part)', 'WPSchoolPress' ),
	'SX' => __( 'Saint Martin (Dutch part)', 'WPSchoolPress' ),
	'PM' => __( 'Saint Pierre and Miquelon', 'WPSchoolPress' ),
	'VC' => __( 'Saint Vincent and the Grenadines', 'WPSchoolPress' ),
	'SM' => __( 'San Marino', 'WPSchoolPress' ),
	'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'WPSchoolPress' ),
	'SA' => __( 'Saudi Arabia', 'WPSchoolPress' ),
	'SN' => __( 'Senegal', 'WPSchoolPress' ),
	'RS' => __( 'Serbia', 'WPSchoolPress' ),
	'SC' => __( 'Seychelles', 'WPSchoolPress' ),
	'SL' => __( 'Sierra Leone', 'WPSchoolPress' ),
	'SG' => __( 'Singapore', 'WPSchoolPress' ),
	'SK' => __( 'Slovakia', 'WPSchoolPress' ),
	'SI' => __( 'Slovenia', 'WPSchoolPress' ),
	'SB' => __( 'Solomon Islands', 'WPSchoolPress' ),
	'SO' => __( 'Somalia', 'WPSchoolPress' ),
	'ZA' => __( 'South Africa', 'WPSchoolPress' ),
	'GS' => __( 'South Georgia/Sandwich Islands', 'WPSchoolPress' ),
	'KR' => __( 'South Korea', 'WPSchoolPress' ),
	'SS' => __( 'South Sudan', 'WPSchoolPress' ),
	'ES' => __( 'Spain', 'WPSchoolPress' ),
	'LK' => __( 'Sri Lanka', 'WPSchoolPress' ),
	'SD' => __( 'Sudan', 'WPSchoolPress' ),
	'SR' => __( 'Suriname', 'WPSchoolPress' ),
	'SJ' => __( 'Svalbard and Jan Mayen', 'WPSchoolPress' ),
	'SZ' => __( 'Swaziland', 'WPSchoolPress' ),
	'SE' => __( 'Sweden', 'WPSchoolPress' ),
	'CH' => __( 'Switzerland', 'WPSchoolPress' ),
	'SY' => __( 'Syria', 'WPSchoolPress' ),
	'TW' => __( 'Taiwan', 'WPSchoolPress' ),
	'TJ' => __( 'Tajikistan', 'WPSchoolPress' ),
	'TZ' => __( 'Tanzania', 'WPSchoolPress' ),
	'TH' => __( 'Thailand', 'WPSchoolPress' ),
	'TL' => __( 'Timor-Leste', 'WPSchoolPress' ),
	'TG' => __( 'Togo', 'WPSchoolPress' ),
	'TK' => __( 'Tokelau', 'WPSchoolPress' ),
	'TO' => __( 'Tonga', 'WPSchoolPress' ),
	'TT' => __( 'Trinidad and Tobago', 'WPSchoolPress' ),
	'TN' => __( 'Tunisia', 'WPSchoolPress' ),
	'TR' => __( 'Turkey', 'WPSchoolPress' ),
	'TM' => __( 'Turkmenistan', 'WPSchoolPress' ),
	'TC' => __( 'Turks and Caicos Islands', 'WPSchoolPress' ),
	'TV' => __( 'Tuvalu', 'WPSchoolPress' ),
	'UG' => __( 'Uganda', 'WPSchoolPress' ),
	'UA' => __( 'Ukraine', 'WPSchoolPress' ),
	'AE' => __( 'United Arab Emirates', 'WPSchoolPress' ),
	'GB' => __( 'United Kingdom (UK)', 'WPSchoolPress' ),
	'US' => __( 'United States (US)', 'WPSchoolPress' ),
	'UY' => __( 'Uruguay', 'WPSchoolPress' ),
	'UZ' => __( 'Uzbekistan', 'WPSchoolPress' ),
	'VU' => __( 'Vanuatu', 'WPSchoolPress' ),
	'VA' => __( 'Vatican', 'WPSchoolPress' ),
	'VE' => __( 'Venezuela', 'WPSchoolPress' ),
	'VN' => __( 'Vietnam', 'WPSchoolPress' ),
	'WF' => __( 'Wallis and Futuna', 'WPSchoolPress' ),
	'EH' => __( 'Western Sahara', 'WPSchoolPress' ),
	'WS' => __( 'Western Samoa', 'WPSchoolPress' ),
	'YE' => __( 'Yemen', 'WPSchoolPress' ),
	'ZM' => __( 'Zambia', 'WPSchoolPress' ),
	'ZW' => __( 'Zimbabwe', 'WPSchoolPress' )
	);
}
/* This Function is Check Pro Version */
function wpsp_check_pro_version( $class='wpsp_pro_version' ) {
	
	$response = array();
	$response['status']	 =true;	
	if( !empty( $class ) && !class_exists( $class ) ) {
		$response['status']		=	false;
		$response['class']		=	'upgrade-to-wpsp-version';
		$response['message']	=	'Please Purchase This Add-on';	
		return $response;
	}
	return $response;
}
?>