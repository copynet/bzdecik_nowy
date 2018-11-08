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
define('DB_NAME', 'bzdecik2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'X,#]@rRocgx|R{5yI.I~g*bMe2r|xvsP!n!6}0%6Li*1y^?r7OvoNj`wDk(J(Jng');
define('SECURE_AUTH_KEY',  ',YBmZ|+9LNN7^54[ gzP5L]#[ *yjmkJ:Qg.f9-e+gzMG_u(30[gh&~oF{$sHYr$');
define('LOGGED_IN_KEY',    '&$>}_cX;?e@^-*spb9j@gll|3nXOX7i28TR@K^AP{gnx@XzQ?P.(ngg+|OyHf,(o');
define('NONCE_KEY',        'P1m}/.e~km]0W1x<N%]^GbrIbbbf;IP>0-,@g^f<q6WtAQK3! wl>%@QgCn]0b]h');
define('AUTH_SALT',        'F)c/@=~xw?(#dir4SLxAs~aVC4/Y>%rqLB,{h#<TMj=p=@Pm!nCCKxw;YA=&TLYY');
define('SECURE_AUTH_SALT', 'F_^~%x(Sm09Zq7[Wn@z5_.,#1~8r(U1zZ_ 9#9^>)GUDC.`ZNk;PM#FR0LqU!^9M');
define('LOGGED_IN_SALT',   '!Typ5QH):/u)?2Bimg0ju9vPJ[Nt*eQvk`i kr&em9t5ikfbP5rHE0g 4 [OaK}n');
define('NONCE_SALT',       'FEIEJnR<sNn%,{!TF@w`@8sV9V_3^^Njf!m3;W]I`y8hKZ,VPDE4q{bTzB3g[8{>');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
