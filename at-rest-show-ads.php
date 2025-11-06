<?php
/**
 * Plugin Name: AtRest Show Ads
 * Description: A plugin to show ads on the website.
 * Author: Na-Gora
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AT_REST_SHOW_ADS_DIR', plugin_dir_path(__FILE__));
define('AT_REST_SHOW_ADS_URL', plugin_dir_url( __FILE__));

require_once AT_REST_SHOW_ADS_DIR . 'src/Shortcode/ShowAds.php';
require_once AT_REST_SHOW_ADS_DIR . 'src/Ajax/AdsContent.php';
require_once AT_REST_SHOW_ADS_DIR . 'src/Data/AdsPosts.php';
require_once AT_REST_SHOW_ADS_DIR . 'src/Data/AdsPostDataHelper.php';

// Init shortcode
new Supernova\AtRest\Shortcode\ShowAds();

// Init ajax refreshing ads content
$adsPosts = new Supernova\AtRest\Data\AdsPosts('advertisement');
$adsHelper = new Supernova\AtRest\Data\AdsPostDataHelper();
new Supernova\AtRest\Ajax\AdsContent($adsPosts, $adsHelper);