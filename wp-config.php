<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'iapp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'commonrbs');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'BAd {l*pKDvS[krFUb=Vi5`MJH5R Qx={R0@UokZYR{!5Gz/n4=G1$3(V7bY6[=o');
define('SECURE_AUTH_KEY',  '_25ze7uk|%N)UziZ)ZJn=IxEy|?OT(i6_h.A0?:L.CFZ@~t/?wCv|:-U%]T~Y%Rx');
define('LOGGED_IN_KEY',    'eO<u<M/}f@8w]TPh+u]HJt@LR)t!+nI 6Nh47LuBM1478L@21UrJ+LFdX2>r}dLO');
define('NONCE_KEY',        ';Z1=h`y=8iNe+=PkY+Ax6(GU/0AP_u$huT38Le?+k;7_>^X4^:v;s{b|P|jZ[){;');
define('AUTH_SALT',        '<tSK&+/i$^vtiQ+bi<fsu$?82Cf)S_*3 :^Tf#poej4-~JIy3ESzb-|,BI_0G dP');
define('SECURE_AUTH_SALT', 'ix7W~JShr:c3La6~<RNyCYf9C&6-UT03+nmJ^a[5gP&,t8TBjIM9_8+6U2T8?oXU');
define('LOGGED_IN_SALT',   'eWaG,|,{5?U,zd~X.BmTWv9ILQpDKBgVqd)o=;!ML>tBr%PQaM<&`QG|lBTAMl-/');
define('NONCE_SALT',       '-LMn4*+@4CMT|sZXS/7h:+Fd|%tCn2-]!z/c<L3_$oi/TXhX],24//5R D<U(<YE');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define('FS_METHOD', 'direct');

define( 'WP_MAX_MEMORY_LIMIT', '128M' );
ini_set('memory_limit', '128M');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
