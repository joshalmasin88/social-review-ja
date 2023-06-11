<?php

if ( ! defined( 'ABSPATH' )) exit;

class JaDB
{

    private static $wpdb;

    public static function init()
    {
        global $wpdb;
        self::$wpdb = $wpdb;
    }

    public static function createDbTables()
    {

        $charset_collate = self::$wpdb->get_charset_collate();
        $table_name = self::$wpdb->prefix . 'reviews';

        print('here');
        $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        review_stars int(255) NULL,
        reviewer_img varchar(500)  NULL,
        review_content text  NULL,
        reviewer_name varchar(255) NULL,
        review_date varchar(255) NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        return true;
    }


    public static function fetchReviews() 
    {
         /** Make call to DB to get reviews from DB */
        $reviews = self::$wpdb->get_results("SELECT * FROM " . self::$wpdb->prefix . 'reviews' );
        return $reviews;
    }

    public static function dropDbTable()
    {
        self::$wpdb->query("DROP TABLE IF EXISTS " . self::$wpdb->prefix . "reviews");
    }

    public static function insertReview($name, $body, $rating, $img, $date)
    {
        self::$wpdb->insert(
            self::$wpdb->prefix . "reviews",
            array(
                'review_stars' => $rating,
                'reviewer_img' => $img,
                'review_content' => $body,
                'reviewer_name' => $name,
                'review_date' => $date
            )
        );
    }

    public static function is_table_exists()
    {
        $result = self::$wpdb->get_results("SHOW TABLES LIKE '" . self::$wpdb->prefix . "reviews'");
        $table = count($result) > 0;
        if ($table == 0) {
            self::createDbTables();
        }
    }
}

JaDB::init();
