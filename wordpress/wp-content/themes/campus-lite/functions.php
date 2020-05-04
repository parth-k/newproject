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
	session_start();
	$creds['user_login'] = $request["username"];
	$creds['user_password'] =  $request["password"];
	// $creds['remember'] = true;
	$_SESSION['se_login'] = $creds['user_login'];
	$_SESSION['se_password'] = $creds['user_password'];
  // $creds['remember'] = true;

  // echo $_SESSION['se_login'];
  // echo $_SESSION['se_password'];

	$user=wp_signon($creds,false);
	if ( is_wp_error($user) )
		echo $user->get_error_message();

	return $user;
}

// add_action( 'after_setup_theme', 'custom_login' );s

function add_cors_http_header(){
	header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

add_filter('allowed_http_origins', 'add_allowed_origins');

function add_allowed_origins($origins) {
	$origins[] = 'https://www.yourdomain.com/';
	return $origins;
}






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
	$nm=$_POST['post_title'];//username
	$mt=$_POST['post_mime_type'];//user mobile number
	$em=$_POST['post_content'];//user email address
	$dt=$_POST['post_date'];//event date
	$name=$_POST['post_name'];// event name
	$pinged = $_POST['pinged'];
  // echo $userID;


	if($nm == "" || !preg_match("/^[a-z]+$/", $nm) || $mt == "" || !preg_match("/^[0-9]{10}$/", $mt) || $em == "" || 
		!preg_match("/^[a-zA-Z0-9.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $em) || $dt == "")
	{
		$required = "BAD Request";
		http_response_code(400);
		echo $required;
		exit;
  // echo "All Fields Are Reqiuired";
	}
	else{

		$con = mysqli_connect('localhost','root','','schoolsite');


		$qy= "INSERT INTO wp_posts (pinged, post_title , post_content, post_date, post_name, post_mime_type) 
		VALUES ('$pinged','$nm','$em','$dt','$name','$mt')";		
		if ($con->query($qy) === TRUE) {
		$abc =mysqli_insert_id($con);

			echo "ID:".$abc."Name:".$nm."Mobile_No.".$mt."Email".$em."Date-Time".$dt."Event_name".$name ;
		} 
		else {
			echo "Error: " . $qy . "<br>" . $con->error;
		}
	}
}
// add_action( 'after_setup_theme', 'custom_booking' );
add_action( 'rest_api_init', 'logoutfn' );

function logoutfn() {
	register_rest_route(
		'custom-plugin', '/logout/',
		array(
			'methods'  => 'GET',
			'callback' => 'logout')
	);
}
function logout(){
	// echo "logout user";
	session_destroy();
}


add_action( 'rest_api_init', 'Book_event' );

function Book_event() {
	register_rest_route(
		'custom-plugin', '/bevent/',
		array(
			'methods'  => 'GET',
			'callback' => 'bevent')
	);
}
	// $pinged = $_GET['pinged'];

 //   $conn = mysqli_connect('localhost','root','','schoolsite');

 //    $sql = "SELECT  ID, post_title, post_mime_type , post_content , post_date , post_name, pinged FROM wp_posts 
 //    WHERE pinged = '1'";
 //    $result = $conn->query($sql);
 //    if ($result->num_rows > 0) {
 //        while($row = $result->fetch_assoc()) {
 //            print json_encode($row);
 //            exit();
 //        }
 //    } else {
 //        echo "0 results";
 //    }

function bevent($request){
	$pinged = $_GET['pinged'];
    // echo $userID;
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "schoolsite";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$sql = "SELECT ID , post_title , post_content , post_name , post_date, post_mime_type FROM wp_posts WHERE pinged = $pinged";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$sql = "SELECT ID , post_title , post_content , post_name , post_date , post_mime_type FROM wp_posts WHERE pinged = $pinged";
			// echo "id: "   . $row["ID"]. "\n".
			// "name: "   . $row["post_title"]. "\n".
			// "email:"   . $row["post_content"]. "\n".
			// "phone: "  . $row["post_mime_type"].  "\n".
			// "date: "   . $row["post_date"]. "\n".
			// "event_name: " . $row["post_name"]. "\n";
            echo json_encode($row);
			
		} 
	}else 
		{
            // echo "This User is not valid to see his bookings"; 
		} 
	}