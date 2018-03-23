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
define('DB_NAME', 'luduspro');

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
define('AUTH_KEY',         '_{Q(zbev{fbF]^xC}TMUgiI_M]bJ<,wr[-m.kRfl!#Y`wy`wLOeoz`r&*0?`jdV?');
define('SECURE_AUTH_KEY',  'OS4G[(hX^_.2.sQ!5JJL.](lY<O?z,^0%r<=^iY?GMd:1EQl$z`?f3HUh?)Rj110');
define('LOGGED_IN_KEY',    '~U~98Xz^7.x|D mIx0eh>K,se<fwLgQF0`_h+r|:mKH7O(6%q[^:|y~n%7N6&~#z');
define('NONCE_KEY',        'kjGlAJ#)st!W4lLV>gK[b|U.qrTm<OQHuVTFK-a0J2zEtBxBAMU+*2w$Oy!fX]ke');
define('AUTH_SALT',        'm?[a.{<iBsD13^49[@>WX#C{%:<m6&^HSxG#Ti`1QV[4_kyfG8R)j,G.NC%R(6]T');
define('SECURE_AUTH_SALT', 'WfQC1mnCpv+:dISE+Z~*/gs7@<qa:Xc~t7H<R[#vfM]5[8h{}-NITVXlO-FtU.tZ');
define('LOGGED_IN_SALT',   '}9*1<m[Ll}.uAJmSW pNzTiz]5%U8T1&^O7]6*Gydb%Oq8*i]OKE(bk|bmU0P^Yq');
define('NONCE_SALT',       'n(S{`i4n[Q~IZ6kaaq3JrUs@:OZV_ac#Z[)Goh5C^nqJI&(rt3,))e6V I;=HuAq');

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
