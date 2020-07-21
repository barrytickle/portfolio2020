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
define('DB_NAME', 'barrytickle_blog');

/** MySQL database username */
define('DB_USER', 'barrytickle_admi');

/** MySQL database password */
define('DB_PASSWORD', 'passport77key');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         's7J.r_,+dUAQ)$wcBGw^&BO3&J9`dC!<-9W]PJW.l/j,TRu*Ja+c_u#@k__v+5IF');
define('SECURE_AUTH_KEY',  'hz6*-1u`s}7hwU1T28!9Zt8,-r8T^]Z_~tN/8D<TDYUK tC~&$O~-7~}Qa+VrDvP');
define('LOGGED_IN_KEY',    '/%xjBVrjGob2CIO<EdN|.&6U%q#s1dThi8wr_Rdf1a{L-#iD, k,] v.n1Ee(y3_');
define('NONCE_KEY',        'b&RYFHBpHOLs2x1JPI rZ+KZbcAYC@BtRH;<sD)( ^aZ_>#S{4!pF%e}1/4Rzp=j');
define('AUTH_SALT',        ',qET=M9xc(: q_z3Ey3}j9b`Yf.@Wm@<Uhy=EpfmGnsWRk5KV+UqP(96MKI;i&gH');
define('SECURE_AUTH_SALT', 'J;Dlsyh9&.)TZfbDZ5P1#B,t4okV<A{PicfSQe&83Em}s=P n9z&@<2,FG)g`iOT');
define('LOGGED_IN_SALT',   '.#`#=,+uQ!>!G0SLxzSp88LX}ol.9&)(U9Ju%9[fd ^n=;bJH 8AP?%AJCF?PG#@');
define('NONCE_SALT',       'nh]51 c+GVuR(#@~2:XAy}^b^qE6A}8_S}r.BMTzl_zB*ZH6qmbn[?B#/?H8AXv;');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
