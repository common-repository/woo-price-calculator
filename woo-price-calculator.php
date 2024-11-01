<?php
/*
Plugin Name: AWS Price Calculator
Plugin URI:  https://altoswebsolutions.com/cms-plugins/woopricecalculator
Description: Price Calculator for WooCommerce
Version:     2.4.1.2
Author:      Altos Web Solutions Italia
Author URI:  https://www.altoswebsolutions.com
License:     
License URI: 
Domain Path: /lang
Text Domain: PoEdit
WC requires at least: 2.6.0
WC tested up to: 3.7.1
*/

/*
 * ATTENZIONE, Se si aggiorna Version, aggiornare anche la variabile $plugin_db_version
 * qui sotto per il database
 */


require 'awspricecalculator.php';

/*
 * Controllo che WooCommerce sia attivato
 */
if (in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    $GLOBALS['woopricecalculator'] = new AWSPriceCalculator("2.4.1.2");
}
