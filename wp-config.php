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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cobaaja' );

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
define( 'AUTH_KEY',         '.MFZxU#?X&$|9hD D/ee6I$e:!W(.Mq2D4#f]jISIf:bhNQle.##*>:ijv/NfdV-' );
define( 'SECURE_AUTH_KEY',  'Q)MWUpfB_u=x?PIr3.Q$b_ch1LON`@.g0-keokAbYxNz*HQ$$DT?uIGGo.:65@Q1' );
define( 'LOGGED_IN_KEY',    'MrwgA1{Jt7=)wSn*B5(r3mp~K:,?<$hvSuZvIz!!XaHLkHY>63POh,r gI2pK3--' );
define( 'NONCE_KEY',        '{jOW4.cv8no*8@hA|Pxhg2u>-K_m=y2D,Dbj9>]uuC=WQ)IE>+ci%5NJ0HYgXo#t' );
define( 'AUTH_SALT',        '0gAz)`1VL(p.O-R6=MlPcaM,USH3;(kgdF%:0$0hu&#EjH+WnBeE_(nzZNJ:`Q6W' );
define( 'SECURE_AUTH_SALT', 'WF>8+!dUMLd-mz}_gv/6[0fk.PKb_QoJw0/pYy4R3_g/I[K`:XO5wh4c,/3jG4_}' );
define( 'LOGGED_IN_SALT',   'x$H@?-&5NH&w&2Jc#([9v919we{@h>%iJex$~_r|{P?$,*?(5}}I&B}K B4g^Q`w' );
define( 'NONCE_SALT',       'M]z2U.U:%6yNzT]-!nB.L aA&o;]b)Ruumh[~mxQh8If )y% rfu3kq7kJ8lPoGl' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
