<?php

define('WFR_BASE_PATH', plugin_dir_path(__FILE__));
define('WFR_URL', plugin_dir_url(__FILE__));

if (!defined('ABSPATH')) exit;

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



class SocialReviewsJa
{
    public function __construct()
    {

        if (function_exists('register_activation_hook')) {
            register_activation_hook(__FILE__, array($this, 'activationHook'));
        }

        if (function_exists('register_deactivation_hook')) {
            register_deactivation_hook(__FILE__, array($this, 'deactivationHook'));
        }

        if (function_exists('register_uninstall_hook')) {
            register_uninstall_hook(__FILE__, 'uninstallHook ');
        }

        add_action('wp_enqueue_scripts', array($this, 'loadScripts'));
        add_action('admin_menu', array($this, 'admin_settings_menu'));
        add_shortcode('social-shortcode', array($this, 'handleFrontEnd'));
        add_action('wp', array($this, 'update_reviews_weekly'));
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

        if (!get_option('social_reviews_api_key')) {
            update_option('social_reviews_api_key', null);
            require_once(plugin_dir_path(__FILE__) . 'includes/Db.php');
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
        delete_option('social_reviews_api_key');
        delete_option('social_reviews_dataID');

        require_once(plugin_dir_path(__FILE__) . 'includes/Db.php');
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
        delete_option('social_reviews_api_key');
        delete_option('social_reviews_dataID');

        require_once(plugin_dir_path(__FILE__) . 'includes/Db.php');
        JaDB::dropDbTable();
    }

    public function loadScripts()
    {
        wp_enqueue_style('bs4', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');

        wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.2.1.slim.min.js', false);
        wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array(), '', true);

        wp_enqueue_style('social-reviews-css', plugin_dir_url(__FILE__) . '/style.css');
    }

    public function admin_settings_menu()
    {
        add_options_page('Social Reviews JA', 'Social Reviews JA', 'manage_options', 'social_reviews_ja', array($this, 'ja_admin_settings'));
    }

    public function ja_admin_settings()
    {
        require_once(plugin_dir_path(__FILE__) . 'admin/settings.php');
    }

    public function handleFrontEnd()
    {
        require_once(WFR_BASE_PATH . '/includes/Db.php');

        $reviews = JaDB::fetchReviews();


        ob_start();
?>
<div class="testimonial-slider">
    <?php if ( $reviews ): ?>
        <?php foreach ( $reviews as $review ) : ?>
  <div class="testimonial-slide">
    <img src="<?php echo $review->reviewer_img; ?>" alt="Testimonial 1">
    <h3><?php echo $review->reviewer_name ?></h3>
    <p><?php echo $review->review_content; ?></p>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script>
      $(document).ready(function(){
        $('.testimonial-slider').slick({
          dots: false,
          infinite: true,
          speed: 500,
          slidesToShow: 3,
          slidesToScroll: 1,
          prevArrow: '<button class="slick-prev">&#8249;</button>',
          nextArrow: '<button class="slick-next">&#8250;</button>',
          responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                prevArrow: '<button class="slick-prev">&#8249;</button>',
                nextArrow: '<button class="slick-next">&#8250;</button>',
              }
            }
          ]
        });
      });
</script>

<?php
        return ob_get_clean();
    }

    public function update_reviews_weekly()
    {
        if (!wp_next_scheduled('grab_new_reviews')) {
            wp_schedule_event(time(), 'weekly', 'grab_new_reviews');
        }
    }

    public function grab_new_reviews()
    {
        require_once(plugin_dir_path(__FILE__) . 'includes/Api.php');
        $api = new Api();
        $api->fetchReviews();
    }
}

$socialReviewInstance = new SocialReviewsJa();
