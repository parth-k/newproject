<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'schoolsite' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'g~3^)3tUV{_b$+7gY7lT}gi+ bjiy2jhahXG?H>Bm+U!,M#Ka^>F?4RkUzN=z&F~' );
define( 'SECURE_AUTH_KEY',  'GX?_[RbR/Jq$D/i^)p9$y7;op,}!9:oc`5A%;4AF>X6*vo{X#;~:)O$!?>Kv.%2p' );
define( 'LOGGED_IN_KEY',    '3P?(myJ6)d{wH]1o@xTXU! AXxojkQU-GR5Ti>_i}0kv15^0)JB|ZdVU|ZU`TM*<' );
define( 'NONCE_KEY',        'F}IxT[JSI^p+2Ya_+rl`#a!=c]%N@$4NCPtd2KS,g7RK1<gHBv%XE Q|8=:7]lUK' );
define( 'AUTH_SALT',        'AuWUgN o$UMHCRHjp);$b8$M$l`]RA?{+!]1=vA]i<y1+hbJwZwxVsHJ+r5GToXW' );
define( 'SECURE_AUTH_SALT', '70Xg@8`f$:_.g.q;sck[tlzFEMZJNw{1T$6UVw7wc4!ccFh--AFg~zXt8u0=h5PI' );
define( 'LOGGED_IN_SALT',   ']`LSF(^j;%>N,9Jg3YE*>F}-X^dX,D[;@tXM6*[BCKg7w0UDUMQQ<~LD#XOZ6l^`' );
define( 'NONCE_SALT',       'lY)4:N6mIn@;aQ- 9%H<}y!mNH}GxWi,&5Ql zxI1>ou9hXh?~e$FY*S>7-lU|VJ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', true );
@ini_set ( 'display_errors',0 );
define( 'SCRIPT_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
