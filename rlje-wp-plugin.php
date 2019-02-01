<?php
/**
 * Plugin Name: RLJE Theme Plugin
 * Plugin URI: https://www.rljentertainment.com/
 * Description: This plugin add functionality to enhance RLJE WordPress sites
 * Version: 1.0.0
 * Author: RLJE
 * Author URI: https://www.rljentertainment.com/
 * License: GPL2
 */

require_once 'helpers/api.php';

require_once 'rlje-theme-settings/rlje-theme-settings.php';

$rlje_theme_plugins_settings = get_option( 'rlje_theme_plugins_settings' );

require_once 'rlje-front-page/rlje-front-page.php';

if ( intval( $rlje_theme_plugins_settings['landing_pages'] ) ) {
	require_once 'rlje-landing-page/rlje-landing-page.php';
}

if ( intval( $rlje_theme_plugins_settings['news_and_reviews'] ) ) {
	require_once 'rlje-news-and-reviews-page/rlje-news-and-reviews-page.php';
}

require_once 'rlje-browse-page/rlje-browse-page.php';
require_once 'rlje-signup-page/rlje-signup-page.php';
require_once 'rlje-signin-page/rlje-signin-page.php';
require_once 'rlje-account-page/rlje-account-page.php';
require_once 'rlje-store-page/rlje-store-page.php';
