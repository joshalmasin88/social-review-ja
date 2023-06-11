<?php

/**
 * Below is the shortcode design 
 */

 if ( ! defined( 'ABSPATH' )) exit;

 require_once( ABSPATH . '/wp-content/plugins/social-reviews-ja/includes/Db.php');
 
$reviews = JaDB::fetchReviews();


// if ($reviews) {
//     foreach ($reviews as $review) {
//         // process each review here
//         echo '<img src='. $review->reviewer_img .' />';
//         echo 'Name: ' . $review->reviewer_name . '';
//         echo '<br>';
//         echo 'Stars:' . $review->review_stars;
//         echo '<br>';

//         echo 'Content: ' . $review->review_content . '';
//         echo '<br><br>';
//     }
// } else {
//     echo 'No reviews found.';
// }

?>


<!-- CODE UP REVIEW STYLES BELOW -->

<div>TEST</div>