<?php
// $con = mysqli_connect('localhost','root','','schoolsite');
// if(!$con){
//     die('could not connect:'.mysqli_error());
// }else{
//     echo " successfull connection"; 
// }
/**
 * Campus functions and definitions
 *
 * @package Campus Lite
 */

if ( ! function_exists( 'campus_lite_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function campus_lite_setup() {

	if ( ! isset( $content_width ) )
		$content_width = 640; /* pixels */

	load_theme_textdomain( 'campus-lite', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo', array(
		'height'      => 240,
		'width'       => 240,
		'flex-height' => true,
	) );
	add_image_size('campus-lite-homepage-thumb',240,145,true);
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'campus-lite' ),		
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'f1f1f1'
	) );
	add_editor_style( array( 'editor-style.css', campus_lite_font_url() ) );
}
endif; // campus_lite_setup
add_action( 'after_setup_theme', 'campus_lite_setup' );


function campus_lite_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'campus-lite' ),
		'description'   => __( 'Appears on blog page sidebar', 'campus-lite' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Header left Top', 'campus-lite' ),
		'description'   => __( 'Appears left side of top bar.', 'campus-lite' ),
		'id'            => 'headtopleft',
		'before_widget' => '',
		'before_title'  => '<h3 class="header-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Header Right Top', 'campus-lite' ),
		'description'   => __( 'Appears right side of top bar.', 'campus-lite' ),
		'id'            => 'headtopright',
		'before_widget' => '',
		'before_title'  => '<h3 class="header-title">',
		'after_title'   => '</h3>',
	) );

}
add_action( 'widgets_init', 'campus_lite_widgets_init' );

function campus_lite_font_url(){
	$font_url = '';

		/* Translators: If there are any character that are
		* not supported by PT Sans, translate this to off, do not
		* translate into your own language.
		*/
		$ptsans = _x('on', 'PT Sans font:on or off','campus-lite');
		
		/* Translators: If there are any character that are
		* not supported by Roboto, translate this to off, do not
		* translate into your own language.
		*/
		$roboto = _x('on', 'Roboto font:on or off','campus-lite');
		
		/* Translators: If there are any character that are
		* not supported by Karla, translate this to off, do not
		* translate into your own language.
		*/
		$karla = _x('on', 'Karla font:on or off','campus-lite');
		
		/* Translators: If there are any character that are
		* not supported by Raleway, translate this to off, do not
		* translate into your own language.
		*/
		$raleway = _x('on', 'Raleway font:on or off','campus-lite');
		
		if('off' !== $ptsans || 'off' !==  $roboto || 'off' !== $karla || 'off' !== $raleway){
			$font_family = array();
			
			if('off' !== $ptsans){
				$font_family[] = 'PT Sans:300,400,600,700,800,900';
			}
			
			if('off' !== $roboto){
				$font_family[] = 'Roboto:400,700';
			}
			
			if('off' !== $karla){
				$font_family[] = 'karla:400,700,900';
			}
			
			if('off' !== $raleway){
				$font_family[] = 'Raleway:400,700';
			}
			
			$query_args = array(
				'family'	=> urlencode(implode('|',$font_family)),
			);
			
			$font_url = add_query_arg($query_args,'https://fonts.googleapis.com/css');
		}
		
		return $font_url;
	}

	function campus_lite_scripts() {
		wp_enqueue_style( 'campus-lite-font', campus_lite_font_url(), array() );
		wp_enqueue_style( 'campus-lite-basic-style', get_stylesheet_uri() );
		wp_enqueue_style( 'campus-lite-editor-style', get_template_directory_uri().'/editor-style.css' );
		wp_enqueue_style( 'campus-lite-responsive-style', get_template_directory_uri().'/css/theme-responsive.css' );
		wp_enqueue_style( 'nivo-style', get_template_directory_uri().'/css/nivo-slider.css');
		wp_enqueue_script( 'nivo-slider-js', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array('jquery') );
		wp_enqueue_script( 'campus-lite-customscripts', get_template_directory_uri() . '/js/custom.js', array('jquery') );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'campus_lite_scripts' );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function campus_lite_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'campus_lite_front_page_template' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/*
 * Load customize pro
 */
require_once( trailingslashit( get_template_directory() ) . 'customize-pro/class-customize.php' );


// URL DEFINES
define('campus_lite_pro_theme_url','https://flythemes.net/wordpress-themes/campus-education-wordpress-theme/');
define('campus_lite_site_url','https://flythemes.net/');

add_action( 'rest_api_init', 'register_api_hooks' );

function register_api_hooks() {
	register_rest_route(
		'custom-plugin', '/login/',
		array(
			'methods'  => 'GET',
			'callback' => 'login',
		)
	);
}

function login($request){
	$creds = array();
	$creds['user_login'] = $request["username"];
	$creds['user_password'] =  $request["password"];
	$creds['remember'] = true;
	$user = wp_signon( $creds, false );

	if ( is_wp_error($user) )
		echo $user->get_error_message();

	return $user;
}

add_action( 'after_setup_theme', 'custom_login' );

function add_cors_http_header(){
	header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

add_filter('allowed_http_origins', 'add_allowed_origins');

function add_allowed_origins($origins) {
	$origins[] = 'https://www.yourdomain.com/';
	return $origins;
}


// add_action( 'rest_api_init', 'register_api_hooks_Events');


// function register_api_hooks_Events() {
// 	register_rest_route(
// 		'custom-plugin', '/booking/',
// 		array(
// 			'methods'  => 'POST',
// 			'callback' => 'booking',
// 		)
// 	);
// }

// function Events($request){
// 	$creds = array();
// 	$creds['user_Event_name'] = $request["Event_name"];
// 	$creds['user_phone_no'] =  $request["phone_no"];
// 	$creds['user_email'] = $request["username"];
// 	$creds['user_date'] = $request["date"];
// 	$creds['user_time'] = $request["time"];
// 	$creds['user_events'] = $request["events"];
// 	$creds['user_address'] = $request["address"];
// 	$creds['remember'] = true;
// 	$user =wp_posts( $creds, false );

// 	if ( is_wp_error($user) )
// 		echo $user->get_error_message();

// 	return $user;
// }

// add_action( 'after_setup_theme', 'custom_Events_booking' );
// function add_cors_http_header(){
// 	header("Access-Control-Allow-Origin: *");
// }
// add_action('init','add_cors_http_header');

// add_filter('allowed_http_origins', 'add_allowed_origins');

// function add_allowed_origins($origins) {
// 	$origins[] = 'https://www.yourdomain.com/';
// 	return $origins;
// }



add_action( 'rest_api_init', 'register_api_hooks_booking' );
function register_api_hooks_booking() {
	register_rest_route(
		'custom-plugin', '/event/',
		array(
			'methods'  => 'POST',
			'callback' => 'Event_booking',
		)
	);
}
function Event_booking($request){
	// $field = array();

	// $con = mysqli_connect('localhost','root','','schoolsite');

	// $qy= "INSERT INTO wp_posts (post_content , post_title , post_date , post_name)
	//  VALUES ('raj sir','8140430000','05-May-2020','science class')";

	// if ($con->query($qy) === TRUE) {
	// 	echo "New record created successfully";
	// } 
	// else {
	// 	echo "Error: " . $qy . "<br>" . $con->error;
	// }
 //    // $user = wp_posts( $creds, false );
	// if ( is_wp_error($user) )
	// 	echo $user->get_error_message();
	// return $user;
	$nm=$_POST['post_title'];
	// $no=$_POST['mo_no'];
	// $em=$_POST['email'];
	$dt=$_POST['post_date'];
	// $tm=$_POST['post_date_gmt'];
	$co=$_POST['post_content'];

	$con = mysqli_connect('localhost','root','','schoolsite');

	$qy= "INSERT INTO wp_posts (post_title , post_content,post_date) 
	VALUES ('$nm' , '$co','$dt')";

	if ($con->query($qy) === TRUE) {
		echo "New record created successfully";
	} 
	else {
		echo "Error: " . $qy . "<br>" . $con->error;
	}
	$field = file_get_contents('php://input');
	echo $field;
	$object = json_decode($field);
	echo ($object);

}
add_action( 'after_setup_theme', 'custom_booking' );

