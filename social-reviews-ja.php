<?php 

if ( ! defined('ABSPATH') ) exit;

/*
*  Plugin Name: Social Reviews JA
 * Description:       Plugin to retreive google reviews and yelp.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:           Joshua Almasin
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       social-reviews-ja
 * Domain Path:       /languages
 */

$base_path = plugin_dir_path( __FILE__ );


class SocialReviewsJa 
{
    public function __construct()
    {
        
        if (function_exists('register_activation_hook')) {
            register_activation_hook( __FILE__ , array( $this, 'activationHook' ));
        }

        if (function_exists( 'register_deactivation_hook' )) {
            register_deactivation_hook( __FILE__ , array( $this, 'deactivationHook' ));
        }

        if (function_exists( 'register_uninstall_hook' )) {
            register_uninstall_hook( __FILE__ , 'uninstallHook ');
        }

        add_action( 'admin_menu', array( $this, 'admin_settings_menu' ));
        add_shortcode( 'social-shortcode', array( $this, 'handleFrontEnd') );
    }


    /**
     * Activation Hook function
     *
     * @return void
     * 
     * When plugin activated checks to see if it exisits, If not then store a null value
     */
    public function activationHook() 
    {
        
        if ( !get_option( 'social_reviews_api_key' )) {
            update_option( 'social_reviews_api_key', null);
            require_once( plugin_dir_path( __FILE__ ) . 'includes/Db.php');
            JaDB::is_table_exists();
        }
    }

    
    /**
     * Deactivate Hook function
     *
     * @return void
     * 
     * Delete stored api value from db when plugin is uninstalled.
     */
    public function deactivationHook() 
    {
        delete_option( 'social_reviews_api_key' );
        require_once( plugin_dir_path( __FILE__ ). 'includes/Db.php');
        JaDB::dropDbTable();
    }

    /**
     * Uninstall Hook function
     *
     * @return void
     * 
     * Delete stored api value from db when plugin is uninstalled.
     */
    public function uninstallHook()
    {
        delete_option( 'social_reviews_api_key' );
        require_once( plugin_dir_path( __FILE__ ). 'includes/Db.php');
        JaDB::dropDbTable();
    }

    public function admin_settings_menu()
    {
        add_options_page( 'Social Reviews JA', 'Social Reviews JA', 'manage_options', 'social_reviews_ja', array( $this, 'ja_admin_settings') );
    
    }

    public function ja_admin_settings() 
    {
        require_once( plugin_dir_path( __FILE__ ) . 'admin/settings.php');
    }

    public function handleFrontEnd()
    {
        require_once( plugin_dir_path( __FILE__ ) . 'frontend/shortcode-widget.php' );
        return $html;

    }
}

$socialReviewInstance = new SocialReviewsJa();