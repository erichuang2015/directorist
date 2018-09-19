<?php 
function directorist_exim_get_categories_checkboxes( $taxonomy = 'category', $selected_cats = null ) {
	$args = array (
		'taxonomy' => $taxonomy
	);
	$all_categories = get_categories($args);
	
	$o = '<div class="checkbox_box checkbox_with_all"><ul><li class="e2t_all"><label><input class="e2t_all_input" type="checkbox" name="taxonomy['.$taxonomy.'][]" value="e2t_all" checked="checked" /> All</label></li>';
	foreach($all_categories as $key => $cat) {
		if($cat->parent == "0") $o .= directorist_exim_show_category($cat, $taxonomy, $selected_cats);
	}
	return $o . '</ul></div>';
}
function directorist_exim_show_category($cat_object, $taxonomy = 'category', $selected_cats = null) {
	$checked = "";
	if(!is_null($selected_cats) && is_array($selected_cats)) {
		$checked = (in_array($cat_object->cat_ID, $selected_cats)) ? 'checked="checked"' : "";
	}
	$ou = '<li><label><input class="e2t_input" ' . $checked .' type="checkbox" name="taxonomy['.$taxonomy.'][]" value="'. $cat_object->cat_ID .'" /> ' . $cat_object->cat_name . '</label>';
	$childs = get_categories('parent=' . $cat_object->cat_ID);
	foreach($childs as $key => $cat) {
		$ou .= '<ul class="children">' . directorist_exim_show_category($cat, $taxonomy, $selected_cats) . '</ul>';
	}
	$ou .= '</li>';
	return $ou;
}
// get taxonomies terms links
function directorist_exim_custom_taxonomies_terms_links() {
	global $post, $post_id;
	// get post by post id
	/*$post = &get_post($post->ID);
	// get post type by post
	$post_type = $post->post_type;*/
	// get post type taxonomies
	$taxonomies = get_object_taxonomies(ATBDP_POST_TYPE);
	ob_start();
	var_dump($taxonomies);
	/*
	 * array (size=3)
  0 => string 'at_biz_dir-location' (length=19)
  1 => string 'at_biz_dir-category' (length=19)
  2 => string 'at_biz_dir-tags'*/
	$return = '';
	$taxonomies_count = count($taxonomies);
	foreach ($taxonomies as $taxonomy) {
		if( $taxonomy !=  'category' && $taxonomy != 'post_tag' ) {
            $taxonomies_count--;
			// get the terms related to post
			$terms = get_the_terms( $post->ID, $taxonomy );
			//var_dump($terms);
			if ( !empty( $terms ) ) {
				$return .= $taxonomy.':';
				$count = count($terms);
				foreach ( $terms as $term ){

                    if($count > 1) {
                        $return .= $term->slug. ','; // include the comma if there is more than 1 items
                    } else {
                        $return .= $term->slug; // just add the number if there is only one item.
                    }
                    $count--; // reduce the counter
                }

				$return .= '&';
			}
		}
	}

    return ob_get_clean(); //  @todo; remove it later.

    //return $return;
}

// Code used to get start and end dates with posts
function directorist_exim_the_post_dates() {
	global $wpdb, $wp_locale;
	
	$dateoptions = '';
	$types = "'" . implode("', '", get_post_types( array( 'public' => true, 'can_export' => true ), 'names' )) . "'";
	if ( function_exists( 'get_post_stati' ) ) {
		$stati = "'" . implode("', '", get_post_stati( array( 'internal' => false ), 'names' )) . "'";
	}
	else {
		$stati = "'" . implode("', '", get_post_statuses()) . "'";
	}

    $monthyears = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, YEAR(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `eyear`, MONTH(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `emonth` FROM $wpdb->posts WHERE post_type IN ($types) AND post_status IN ($stati) ORDER BY post_date ASC ");

	if ( $monthyears ) {
		foreach ( $monthyears as $k => $monthyear ) $monthyears[$k]->lmonth = $wp_locale->get_month( $monthyear->month );

		for( $s = 0, $e = count( $monthyears ) - 1; $e >= 0; $s++, $e-- ) {
			$dateoptions .= "\t<option value=\"" . $monthyears[$e]->eyear . '-' . zeroise( $monthyears[$e]->emonth, 2 ) . '">' . $monthyears[$e]->lmonth . ' ' . $monthyears[$e]->year . "</option>\n";
		}
	}
	
	return $dateoptions;
}

function directorist_exim_implode_wrapped($before, $after, $array, $glue = '') {
    return $before . implode($after . $glue . $before, $array) . $after;
}