<?php

global $wpdb;

$wpdb->br_c = $wpdb->prefix.'br_categories';

if( $_POST['br_add_post'] == "1")
{
	if( $_POST['br_add_category'] == "" )
	{
		echo '<div class="updated"><p><strong>Error: You must enter a category name</strong></p></div>';
	}
	else
	{
            $wpdb->query
            (
                    $wpdb->prepare
                    ( 
                            "INSERT INTO $wpdb->br_c
                            ( name )
                            VALUES ( %s )", 
                            $_POST['br_add_category']
                    )
            );

            echo '<div class="updated"><p><strong>You have added a new category: ' . $_POST['br_add_category'] . '</strong></p></div>';
	}
}

if( $_POST['br_limiter_post'] == "1" )
{
    if( !($_POST['limit_selector'] == "10" || $_POST['limit_selector'] == "20" || $_POST['limit_selector'] == "50" || $_POST['limit_selector'] == "100") )
    {
        echo '<div class="updated"><p><strong>Error: Limit must be 10, 20, 50, or 100</strong></p></div>';
    }
    else
    {
        update_option( "br_limit", $_POST['limit_selector'] );
        echo '<div class="updated"><p><strong>Results per page has been set to ' . $_POST['limit_selector'] . '</strong></p></div>';
    }
}

if( $_POST['br_remove_post'] == "1" )
{
	$cat_was_removed = false;

	foreach( $_POST as $var => $value )
	{
		if( substr($var, 0, 7) == "br_cat_" )
		{
			$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->br_c WHERE name = '%s'", $value));
			$categories_str .= $value . ", ";
			$cat_was_removed = true;
		} 
	}

	if( $cat_was_removed == false )
	{
		echo '<div class="updated"><p><strong>Error: You must select at least one category for removal</strong></p></div>';
	}
	else
	{
		echo '<div class="updated"><p><strong>You have removed the following categories: ' . substr($categories_str, 0, -2) . '</strong></p></div>';
	}
}

if( $_POST['br_rename_post'] == "1" )
{
    	if( $_POST['br_rename_category'] == "" )
	{
		echo '<div class="updated"><p><strong>Error: You must enter a new category name</strong></p></div>';
	}
	else
	{
            $cat_was_renamed = $wpdb->update( $wpdb->br_c, array('name' => $_POST['br_rename_category']), array('id' => $_POST['rename_selector']) );

            if( $cat_was_renamed == false )
            {
                    echo '<div class="updated"><p><strong>Error: You must select a category to rename</strong></p></div>';
            }
            else
            {
                    echo '<div class="updated"><p><strong>Category renamed successfully</strong></p></div>';
            }
        }
}
?>
<div class="wrap">
	<h2>Benday Reviews Options</h2>

	<form name="br_add_cat_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="br_add_post" value="1">
		<h4>Add New Category</h4>
		<p><label for="br_add_category">Category: </label><input type="text" name="br_add_category" id="br_add_category"><input type="submit" value="Add New Category"></p>
	</form>
        
        <form name="br_limiter_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="br_limiter_post" value="1">
		<h4>Limit Results</h4>
                <p>Display how many results per page:</p>
		<p>
                    <input type="radio" name="limit_selector" value="10"<?php if( get_option('br_limit') == "10" ) { echo " checked"; } ?>> 10<br />
                    <input type="radio" name="limit_selector" value="20"<?php if( get_option('br_limit') == "20" ) { echo " checked"; } ?>> 20<br />
                    <input type="radio" name="limit_selector" value="50"<?php if( get_option('br_limit') == "50" ) { echo " checked"; } ?>> 50<br />
                    <input type="radio" name="limit_selector" value="100"<?php if( get_option('br_limit') == "100" ) { echo " checked"; } ?>> 100<br />
		</p>
		<p><input type="submit" value="Save"></p>
	</form>
        
        <form name="br_rename_cat_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="br_rename_post" value="1">
		<h4>Rename Existing Categories</h4>
                <p><label for="br_rename_category">Rename to: </label><input type="text" name="br_rename_category" id="br_rename_category">
		<p>
		<?php
		$categories_array = $wpdb->get_results("SELECT * FROM $wpdb->br_c", ARRAY_A);

		for( $i = 0; $i < count($categories_array); $i++ )
		{
		?>
			<input type="radio" name="rename_selector" value="<?php echo $categories_array[$i]['id']; ?>"> <?php echo $categories_array[$i]['name']; ?><br />
		<?php
		}
		?>
		</p>
		<p><input type="submit" value="Rename Categories"></p>
	</form>

	<form name="br_remove_cat_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="br_remove_post" value="1">
		<h4>Remove Existing Categories</h4>
		<p>
		<?php
		$categories_array = $wpdb->get_results("SELECT * FROM $wpdb->br_c", ARRAY_A);

		for( $i = 0; $i < count($categories_array); $i++ )
		{
		?>
			<input type="checkbox" name="br_cat_<?php echo $categories_array[$i]['id']; ?>" value="<?php echo $categories_array[$i]['name']; ?>"> <?php echo $categories_array[$i]['name']; ?><br />
		<?php
		}
		?>
		</p>
		<p><input type="submit" value="Remove Categories"></p>
	</form>
</div>