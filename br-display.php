<?php

global $wpdb;

$wpdb->br_c = $wpdb->prefix.'br_categories';

if( isset($_POST['br-pagination-select']) && !($_POST['br-pagination-select'] == "") )
    $offset = (int)$_POST['br-pagination-select'];
elseif( isset($_GET['rvoffset']) && !($_GET['rvoffset'] == "") )
    $offset = (int)$_GET['rvoffset'];
else
    $offset = 1;

$categories_array = $wpdb->get_results("SELECT * FROM $wpdb->br_c", ARRAY_A);
$post_limit = get_option('br_limit');

$posts_query = "
    SELECT COUNT(1) AS post_count
    FROM $wpdb->posts, $wpdb->postmeta AS m1, $wpdb->postmeta AS m2, $wpdb->postmeta AS m3
    WHERE $wpdb->posts.ID = m1.post_id 
    AND m2.post_id = m1.post_id
    AND m3.post_id = m2.post_id
    AND $wpdb->posts.post_status = 'publish' 
    AND m1.meta_key = '_br_cat'
    AND m2.meta_key = '_br_score'
    AND m3.meta_key = '_br_title'
    AND m1.meta_value <> ''
    AND m2.meta_value <> ''
    AND m3.meta_value <> ''
";

if( isset($_GET['rvcat']) && !( $_GET['rvcat'] == "" ) )
{
    $posts_query .= " AND m1.meta_value = '" . $_GET['rvcat'] . "'";
}

$total_posts_array = $wpdb->get_results($posts_query, ARRAY_A);
$total_posts = $total_posts_array[0]['post_count'];
if( !($total_posts % $post_limit == 0) )
    $total_pages = ((int)($total_posts / $post_limit) + 1);
else
    $total_pages = $total_posts / $post_limit;

$posts_query = "
    SELECT $wpdb->posts.ID, $wpdb->posts.guid, $wpdb->posts.post_date_gmt, m1.meta_value as br_cat, CAST(m2.meta_value AS DECIMAL(4,1)) AS br_score, m3.meta_value AS br_title
    FROM $wpdb->posts, $wpdb->postmeta AS m1, $wpdb->postmeta AS m2, $wpdb->postmeta AS m3
    WHERE $wpdb->posts.ID = m1.post_id 
    AND m2.post_id = m1.post_id
    AND m3.post_id = m2.post_id
    AND $wpdb->posts.post_status = 'publish' 
    AND m1.meta_key = '_br_cat'
    AND m2.meta_key = '_br_score'
    AND m3.meta_key = '_br_title'
    AND m1.meta_value <> ''
    AND m2.meta_value <> ''
    AND m3.meta_value <> ''
";

if( isset($_GET['rvcat']) && !( $_GET['rvcat'] == "" ) )
{
    $posts_query .= " AND m1.meta_value = '" . $_GET['rvcat'] . "'";
}

$title_link = "rvsort=title_asc";
$rating_link = "rvsort=rating_desc";
$date_link = "rvsort=date_desc";

if( $_GET['rvsort'] == "title_asc" )
{
	$posts_query .= " ORDER BY br_title ASC";
	$title_link = "rvsort=title_desc";
}
elseif( $_GET['rvsort'] == "title_desc" )
{
	$posts_query .= " ORDER BY br_title DESC";
}
elseif( $_GET['rvsort'] == "rating_asc" )
{
	$posts_query .= " ORDER BY br_score ASC";
}
elseif( $_GET['rvsort'] == "rating_desc" )
{
	$posts_query .= " ORDER BY br_score DESC";
	$rating_link = "rvsort=rating_asc";
}
elseif( $_GET['rvsort'] == "date_asc" )
{
	$posts_query .= " ORDER BY $wpdb->posts.post_date_gmt ASC";
}
elseif( $_GET['rvsort'] == "date_desc" )
{
	$posts_query .= " ORDER BY $wpdb->posts.post_date_gmt DESC";
	$date_link = "rvsort=date_asc";
}
else
{
	$posts_query .= " ORDER BY $wpdb->posts.post_date_gmt DESC";
	$date_link = "rvsort=date_asc";
}

$posts_query .= " LIMIT " . (($offset - 1) * $post_limit) . ", " . $post_limit;

$temp_url_query = "?";

foreach( $_GET as $k => $v )
{
    if( !($k == 'rvsort' || $k == 'rvcat' || $k == 'rvoffset') )
    {
        $temp_url_query .= $k . "=" . $v . "&";
    }
}

if( isset($_GET['rvcat']) && !( $_GET['rvcat'] == "" ) )
{
    $title_link .= "&rvcat=" . $_GET['rvcat'];
    $rating_link .= "&rvcat=" . $_GET['rvcat'];
    $date_link .= "&rvcat=" . $_GET['rvcat'];
}

$reviews_array = $wpdb->get_results($posts_query, ARRAY_A);

echo '<div class="br-review-table">' . "\n";
echo "\t" . '<div class="br-category-nav">Category: <a href="' . $temp_url_query . 'rvsort=date_desc&rvoffset=' . $offset . '">All</a>';

for( $i = 0; $i < count($categories_array); $i++ )
{
    echo ' | <a href="' . $temp_url_query . 'rvcat=' . $categories_array[$i]['id'] . '">' . $categories_array[$i]['name'] . '</a>';
}

echo '</div>' . "\n";
echo "\t" . '<div class="br-sort-nav">' . "\n";
echo "\t\t" . '<div class="br-title"><a href="' . $temp_url_query . $title_link . '&rvoffset=' . $offset . '">Title</a></div>' . "\n";
echo "\t\t" . '<div class="br-category">Category</div>' . "\n";
echo "\t\t" . '<div class="br-rating"><a href="' . $temp_url_query . $rating_link . '&rvoffset=' . $offset . '">Rating</a></div>' . "\n";
echo "\t\t" . '<div class="br-date"><a href="' . $temp_url_query . $date_link . '&rvoffset=' . $offset . '">Posted</a></div>' . "\n";
echo "\t" . '</div>' . "\n";

$count = 0;
for( $j = 0; $j < count($reviews_array); $j++ )
{
	$title = get_post_meta($reviews_array[$j]['ID'],'_br_title',true);
	if( $title == "" )
	{
		$title = $reviews_array[$j]['post_title'];
	}

        $temp_cat = get_post_meta($reviews_array[$j]['ID'],'_br_cat',true);
        
        for( $y = 0; $y < count($categories_array); $y++ )
        {
            if( $categories_array[$y]['id'] == $temp_cat ) { $category = $categories_array[$y]['name']; }
        }

        if( $count % 2 == 0 )
                echo "\t" . '<div class="br-review-row">' . "\n";
        else
                echo "\t" . '<div class="br-review-row br-review-row-alt">' . "\n";
        echo "\t\t" . '<div class="br-title"><a href="' . $reviews_array[$j]['guid'] . '">' . $title . '</a></div>' . "\n";
        echo "\t\t" . '<div class="br-category">' . $category . '</div>' . "\n";
        echo "\t\t" . '<div class="br-rating">' . $reviews_array[$j]['br_score'] . '</div>' . "\n";
        echo "\t\t" . '<div class="br-date">' . substr($reviews_array[$j]['post_date_gmt'], 0, -9) . '</div>' . "\n";
        echo "\t" . '</div>' . "\n";

        $count++;
}

echo "\t" . '<div class="br-pagination-nav">' . "\n";

if( isset($_GET['rvcat']) && !( $_GET['rvcat'] == "" ) )
{
    if( $offset <= 1 )
        echo "\t\t" . '<div class="br-pagination-left"></div>' . "\n";
    elseif( isset($_GET['rvsort']) && !($_GET['rvsort'] == "") )
        echo "\t\t" . '<div class="br-pagination-left"><a href="' . $temp_url_query . 'rvsort=' . $_GET['rvsort'] . '&rvcat=' . $_GET['rvcat'] . '&rvoffset=' . ($offset - 1) . '"><< Prev</a></div>' . "\n";
    else
        echo "\t\t" . '<div class="br-pagination-left"><a href="' . $temp_url_query . 'rvcat=' . $_GET['rvcat'] . '&rvoffset=' . ($offset - 1) . '"><< Prev</a></div>' . "\n";
}
else
{
    if( $offset <= 1 )
        echo "\t\t" . '<div class="br-pagination-left"></div>' . "\n";
    elseif( isset($_GET['rvsort']) && !($_GET['rvsort'] == "") )
        echo "\t\t" . '<div class="br-pagination-left"><a href="' . $temp_url_query . 'rvsort=' . $_GET['rvsort'] . '&rvoffset=' . ($offset - 1) . '"><< Prev</a></div>' . "\n";
    else
        echo "\t\t" . '<div class="br-pagination-left"><a href="' . $temp_url_query . 'rvoffset=' . ($offset - 1) . '"><< Prev</a></div>' . "\n";
}

echo "\t\t" . '<div class="br-pagination-middle">' . "\n";
echo "\t\t\t". '<div>' . "\n";
echo "\t\t\t\t" . '<span>Go to page&nbsp;</span>' . "\n";
echo "\t\t\t\t" . '<form name="br-pagination-form" method="post" action="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '">';
echo "\t\t\t\t\t" . '<select name="br-pagination-select" onchange="this.form.submit();">' . "\n";

for( $x = 1; $x <= $total_pages; $x++ )
{
    echo "\t\t\t\t\t\t" . '<option value="' . $x . '"';
    
    if( $x == $offset ) { echo ' selected="selected"'; }
    
    echo '>' . $x . '</option>' . "\n";
}

echo "\t\t\t\t\t" . '</select>' . "\n";
echo "\t\t\t\t" . '</form>' . "\n";
echo "\t\t\t" . '</div>' . "\n";
echo "\t\t" . '</div>' . "\n";

if( isset($_GET['rvcat']) && !( $_GET['rvcat'] == "" ) )
{
    if( count($reviews_array) < $post_limit )
        echo "\t\t" . '<div class="br-pagination-right"></div>' . "\n";
    elseif( isset($_GET['rvsort']) && !($_GET['rvsort'] == "") )
        echo "\t\t" . '<div class="br-pagination-right"><a href="' . $temp_url_query . 'rvsort=' . $_GET['rvsort'] . '&rvcat=' . $_GET['rvcat'] . '&rvoffset=' . ($offset + 1) . '">Next >></a></div>' . "\n";
    else
        echo "\t\t" . '<div class="br-pagination-right"><a href="' . $temp_url_query . 'rvcat=' . $_GET['rvcat'] . '&rvoffset=' . ($offset + 1) . '">Next >></a></div>' . "\n";
}
else
{
    if( count($reviews_array) < $post_limit )
        echo "\t\t" . '<div class="br-pagination-right"></div>' . "\n";
    elseif( isset($_GET['rvsort']) && !($_GET['rvsort'] == "") )
        echo "\t\t" . '<div class="br-pagination-right"><a href="' . $temp_url_query . 'rvsort=' . $_GET['rvsort'] . '&rvoffset=' . ($offset + 1) . '">Next >></a></div>' . "\n";
    else
        echo "\t\t" . '<div class="br-pagination-right"><a href="' . $temp_url_query . 'rvoffset=' . ($offset + 1) . '">Next >></a></div>' . "\n";
}

echo "\t" . '</div>' . "\n";

echo '</div>';
?>