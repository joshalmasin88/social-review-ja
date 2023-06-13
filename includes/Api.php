<?php


if (!defined('ABSPATH')) exit;

class Api
{

    public function fetchReviews()
    {

        $apiKey = get_option('social_reviews_api_key');
        $featureID = get_option('social_reviews_dataID');

        $url = "https://api.scaleserp.com/search?api_key={$apiKey}&search_type=place_reviews&data_id={$featureID}&max_page=5&include_html=false&output=json";
        $response = file_get_contents($url);

        if ($response !== false && strpos($http_response_header[0], '200') !== false) {
            $data = json_decode($response, true);

            $reviews = $data['place_reviews_results'];

            require_once(plugin_dir_path(__FILE__) . 'Db.php');
            if ( isset($reviews)) {
                for ($i = 0; $i < count($reviews); $i++) {

                    $name =  $reviews[$i]['source'];
                    $body =  $reviews[$i]['body_html'];
                    $rating =  $reviews[$i]['rating'];
                    $reviewer_img =  $reviews[$i]['source_image'];
                    $date =  $reviews[$i]['date'];

                    JaDB::insertReview($name, $body, $rating, $reviewer_img, $date);
                }
            } else {
                add_settings_error(
                    'social_reviews_notice',
                    esc_attr('settings_error'),
                    __('Make sure you entered a correct Feature ID', 'social-reviews-ja'),
                    'error'
                );
            }
        }
        if (strpos($http_response_header[0], '401') !== false) {
            // Show 401 Unauthorized
            add_settings_error(
                'social_reviews_notice',
                esc_attr('settings_error'),
                __('Make sure your API Key is correct on your account at: https://app.scaleserp.com/login', 'social-reviews-ja'),
                'error'
            );

            return false;
        }

        if (strpos($http_response_header[0], '402') !== false) {
            // Show 401 Unauthorized
            add_settings_error(
                'social_reviews_notice',
                esc_attr('settings_error'),
                __('Looks like you ran out of credits, Go purchase more at: https://app.scaleserp.com/login', 'social-reviews-ja'),
                'error'
            );

            return false;
        }
    }
}
