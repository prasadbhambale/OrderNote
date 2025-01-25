<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ordernote' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'A9[k/wSoS)u1UJQlG.IU?)[Ei#Fxxt6q5}x$o-]fGqC/m^fYZB5cy.m]|QHq)3QS' );
define( 'SECURE_AUTH_KEY',  's_BN2mH]i-p+ks8Iq#ot1W`oF5k+Nwd*M71oFMpii?$?34ll&T]A3~+#n8;@n<c!' );
define( 'LOGGED_IN_KEY',    'EZ,.BQUyZ)gly#*/K[3V67fM=LM3!zGj Q2Ppq5-SwNoR/GwVAm|Rabn77_[%j|i' );
define( 'NONCE_KEY',        '$r}qu:>2Z1r[xY/z&utF= ~t8gKKM*8=i_*IC]Q .zW97YnC%Ya3H!!aiY0G>PnI' );
define( 'AUTH_SALT',        'E`s!pVxWGE,fh`6OZyX0`[5Ey$ifKe4_gG ./4ZG>&-#6Ke(oV_x#=DGx!/*BA]p' );
define( 'SECURE_AUTH_SALT', '/&9Ll?:Jlnt{x/h,8Y{]j#f)x/Auk<QO!yGhu5z5{7RN)+uk}7De,+4TVkAPXMR%' );
define( 'LOGGED_IN_SALT',   ';SCAz&5YxZob^m_/TrF1OnPcr#,%=t[:J 9W9Em=hN[[A[|O1#J2569I|07-~Gs(' );
define( 'NONCE_SALT',       '1+|Fl9Hv8B-0qAfYH&m_ ^Z6<|~.LewH-TRuuF.]CHu5rvG|PsP 4?%S9jpE9{rI' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
define('WP_CRON_DISABLED', true);


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
