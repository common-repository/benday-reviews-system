<?php
/*
Plugin Name: Benday Reviews System
Plugin URI: http://wordpress.org/extend/plugins/benday-reviews-system/
Description: Stores review data for display on an archive page.
Version: 1.2
Author: Alan Richey
Author URI: http://vectorvondoom.com
*/

function br_install()
{
	global $wpdb;

	$wpdb->br_c = $wpdb->prefix.'br_categories';

	$tables = $wpdb->get_col("SHOW TABLES");

	if(!in_array($wpdb->br_c, $tables))
	{
		$charset_collate = '';
		if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') )
		{
			if (!empty($wpdb->charset)) {
				$charset_collate .= " DEFAULT CHARACTER SET $wpdb->charset";
			}
			if (!empty($wpdb->collate)) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}
		$result = $wpdb->query("
			CREATE TABLE `$wpdb->br_c` (
			`id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`name` VARCHAR( 255 ) NOT NULL ,
			INDEX ( `id` )
			) $charset_collate
		");
	}
        
        update_option( "br_limit", "20" );
}
register_activation_hook( __FILE__, 'br_install' );

function br_post()
{
	add_meta_box('br-meta-box','Benday Reviews System - Review Data','br_meta_box','post','normal','high');
	add_action('save_post','br_save_post');
}
add_action('admin_init','br_post');

function br_meta_box($post,$box)
{
	global $wpdb;

	$wpdb->br_c = $wpdb->prefix.'br_categories';

	$review_score = get_post_meta($post->ID,'_br_score',true);
	$review_cat = get_post_meta($post->ID,'_br_cat',true);
	$categories_array = $wpdb->get_results("SELECT id, name FROM $wpdb->br_c", ARRAY_A);
	$review_title = get_post_meta($post->ID,'_br_title',true);

        echo '<p>All fields must be populated for the review to show up in the table.</p>';
	echo '<label for="br-review-title">Title: </label><input type="text" name="br-review-title" value="' . $review_title . '"> ';
	echo '<label for="br-review-score">Score: </label><input type="text" name="br-review-score" value="' . $review_score . '"><br /><br />';

	echo '<label>Category:</label><br /><br />';

	for( $i = 0; $i < count($categories_array); $i++ )
	{
		echo '<label for="review_category"><input type="radio" name="br-review-category" value="' . $categories_array[$i]['id'] . '"';

		if( $categories_array[$i]['id'] == $review_cat ) { echo ' checked'; }

		echo '> ' . $categories_array[$i]['name'] . '</label><br />';
	}
}

function br_save_post($post_id)
{
	if( isset($_POST['br-review-title']) && !($_POST['br-review-title'] == "") )
	{
		update_post_meta($post_id,'_br_title',$_POST['br-review-title']);
	}
        
	if( isset($_POST['br-review-score']) && !($_POST['br-review-score'] == "") && is_numeric($_POST['br-review-score']) )
	{
                number_format($number, 1, '.', '');
		update_post_meta($post_id,'_br_score',$_POST['br-review-score']);
	}
        
        if( isset($_POST['br-review-category']) && !($_POST['br-review-category'] == "") )
	{
		update_post_meta($post_id,'_br_cat',$_POST['br-review-category']);
	}
}

function br_admin_menu()
{
	add_options_page('Benday Reviews System Options', 'Benday Reviews', 8, 'br-admin-options', 'br_admin_options');
}
function br_admin_options()
{
	include('br-admin.php');
}
add_action('admin_menu', 'br_admin_menu');

function br_display($text)
{
	$trigger = "[br-review-table]";

	if( strpos($text, $trigger) !== false )
	{
		$text = str_replace('[br-review-table]', '', $text);
		echo $text;
		include('br-display.php');
		return;
	}
	else
	{
		return $text;
	}
}
add_filter('the_content', 'br_display');

function br_head_link()
{
	if ( !defined( 'WP_PLUGIN_URL' ) )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

	echo '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/style.css" type="text/css" media="screen" />';
}
add_action('wp_head', 'br_head_link');
?>