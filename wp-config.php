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
define( 'DB_NAME', 'wp-blog' );

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
define( 'AUTH_KEY',         '^i4/5Yhw1->{vI]`JT`R7#`ha@yp,23b<Me!h7r%+xG06Cfn9i{5,YUVgwKF{>vr' );
define( 'SECURE_AUTH_KEY',  'E9,d_?mZR-wNF.)N7KG{+07}EZ>o%N[OI 0L*u1$;OQjM<ZTqJjsge_xL{Mnn9+7' );
define( 'LOGGED_IN_KEY',    '^Cq}^u@eboe+-u;x}*<%]N(t!7!v[PBj}z:/PbF<r%oEnzLY#X}JNTzDE&h<HclK' );
define( 'NONCE_KEY',        '9LpYOB8BRfig,:XJ[kJ@W[o!=s#)X(Q}V6%f WPx?5`Fcg6,-?Z@vjo`feX/ q8H' );
define( 'AUTH_SALT',        'g:|U?qb}w:;9/Mo<,EnV`|7Y#>H+w~OzVULP*j*Os*$dhdB@NV8Ty{H,(};E+kz$' );
define( 'SECURE_AUTH_SALT', 'dMWZ(THgZRuv@ _niC+K%x^wHx~ea]E}GqERz$Yq_JePWR%VsZN@5aEq3r5}rRkL' );
define( 'LOGGED_IN_SALT',   '~!<PyZ!o(YBvU~:R|F$FqmDosZs|A}aQXDE_|jymo3`sbztSymAFh<C17#3sAz0P' );
define( 'NONCE_SALT',       '.a~6>R?QmEnv>ls<?WS,p3H2n(_{p6 v:5,Jp+)Zx7j#r>>C3@Ji;Ceg.KE!B95.' );

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
