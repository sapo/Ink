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
define('DB_NAME', 'ink');

/** MySQL database username */
define('DB_USER', 'ink_db_user');

/** MySQL database password */
define('DB_PASSWORD', 'ink_db_user');

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
define('AUTH_KEY',         't=#MDfI?U5Kv%T5vm)L7=[La|t*VljbGd>y%|Z;wC-y{C/B8gRC$l!6Zfb0CgiZ]');
define('SECURE_AUTH_KEY',  'z/h5uB-1:Z;~dJ -a7GJ(@ln;<=5-p|`7j>J9& >8AoC^]t{+eM},K@|ZL.VKJ0|');
define('LOGGED_IN_KEY',    'XL{rQ+f&tasCJ(z)hS<gAu@Q/8U_UUnRSx!+G%-Tx&C^X@+6NFu)*(YY*P1=)7i&');
define('NONCE_KEY',        '-k{nthm><h|*0+D<pRSW;svOA o[*mL@k%+KLv<pZb5Tt]eO8c.^;yYC3t6B-I2I');
define('AUTH_SALT',        'HCht)R!D+da+Muf6rrP|C,w0zD9%Avupv  dBh<amCh5u;~~X/X}+UCdIQj,ELkH');
define('SECURE_AUTH_SALT', '20ci!@7rx@/ T5tBQnAO6-qbf)6@#80x4uLz3oc}[6,NR !{kI -K4wY[V^+~t %');
define('LOGGED_IN_SALT',   'M)gLmP,I!6`|DqX-b|aQ5p5a[]*vpDa),luNEO<n@{.Z16+2) }.of|Ecjb^8yZ6');
define('NONCE_SALT',       ' I;=dro#PxiWhY7#vbChMrx+4B cuW_(y5kPe7`<`5?a[(iC${E=b+Bbmsmi*[}.');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ink_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');