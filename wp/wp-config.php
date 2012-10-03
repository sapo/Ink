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
define('AUTH_KEY',         'OM]TZ}J<!3sGe|Y}y8Wsx.8x_P0LfNE-5l]l452CM 3m]b|%Dad|QTO)7LjWn+E/');
define('SECURE_AUTH_KEY',  'INnMuC}vR-#<?NM#u^D;%bzd`Z~-#_dEzog+=}d0vaB  ;?X%*Li5~;#2D 7Tw^v');
define('LOGGED_IN_KEY',    'y+c-1MFK0q+-71RMapEGj4rxu|q(t7vB5=b_07 1aX~&&-(=b#,5?zi{[BqxaYcf');
define('NONCE_KEY',        'H#Wn=~?sHA(+*QL0.Q.x#5(>y/Nla3AEUZ#F_TKJ+Y(+>jbmtd!o|;JJ|5*|!`Jg');
define('AUTH_SALT',        'X|xFLJ+U:q%b;v$bKF+V|vF}74>;^-g;Sj**3YzGQ6TV b$Vzu(TkH9L+m#.oSv|');
define('SECURE_AUTH_SALT', '#UY~S}6-su*RI8#mJnO&,31@Llw=T/|abij`)q:53nSOk;LknCmu5uD9H/=1L%9I');
define('LOGGED_IN_SALT',   '[]jY09UX<xD>+b#Zr`ghDS3`3j,6C=>q(UMoX_zw-U+[Oz$+m|d&=1_PkGHP|- T');
define('NONCE_SALT',       '26->d[~+6^&bIrVuj!bwq1o!.9/6lZ)$Bh2l_YSX&JMW?0C*n-[}X-)OpSYG}a2<');

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
