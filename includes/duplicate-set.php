<?php

// duplicate button in flashcard-area
function duplicateSets() {
	if(isset($_POST['duplicate_post']) == '1') {
        $getoptions = get_option('fc_rlink');
        $redirectlink = $getoptions['duplicate_redirect_link'];
		$dupsetid = $_POST['dup_set_id'];
		$duppresets = get_post_meta( $dupsetid, 'selected_presets', true );
		$duplicate_post = array(
			'post_type' => 'portfolio_sets',
			'post_title' => get_the_title($dupsetid) . ' copy',
			'post_content' => get_post_field('post_content', $dupsetid),
			'post_status' => 'publish',
		);
		$newpostid = wp_insert_post( $duplicate_post );
		update_post_meta($newpostid,'selected_presets', $duppresets);
		// Create duplicate cards for duplicated set
        $fc_cards = array(
            'post_type' => 'portfolio_flashcards',
            'posts_per_page' => -1,
            'order' => 'ASC',
            // 'author' => $carduserid,
            'meta_query' => array(
                array(
                    'key' => 'parent_sets',
                    'value' => $dupsetid
                )
            )
        );
        $fc_cards_query = new WP_Query( $fc_cards );
		$fccards = $fc_cards_query->posts;
		foreach($fccards as $fccard) {
			$duplicate_card = array(
				'post_type' => 'portfolio_flashcards',
				'post_title' => get_the_title($fccard->ID),
				'post_content' => get_post_field('post_content', $fccard->ID),
				'post_status' => 'publish',
			);
			$new_duplicate_card = wp_insert_post($duplicate_card);
			update_post_meta($new_duplicate_card, 'parent_sets', $newpostid);
			set_post_thumbnail( $new_duplicate_card, get_post_thumbnail_id($fccard->ID) );
		};
		wp_redirect( $redirectlink );
	}
}
add_action('init', 'duplicateSets');

// Duplicate button in normal wp posts
function duplicate_presets() {
    if(isset($_POST['duplicate_presets']) == '1') {
        $getoptions = get_option('fc_rlink');
        $redirectlink = $getoptions['duplicate_redirect_link'];
        $duppresetid = $_POST['dup_preset_id'];
        $duplicate_preset = array(
            'post_type' => 'portfolio_sets',
            'post_title' => get_the_title($duppresetid),
            // 'post_content' => get_post_field('post_content', $duppresetid),
            'post_content' => '',
            'post_status' => 'publish',
        );
        $newpresetid = wp_insert_post( $duplicate_preset );

        // create the array
        $post_id = $duppresetid;
        $custom_field_keys = get_post_custom_keys($duppresetid);
        $presetarray = array();
        foreach ($custom_field_keys as $key => $value) {
            if(strpos($value, '_attached_posts') == true) {
                $presetids = get_post_meta($post_id, $value);
                $presetidsarray = array();
                foreach($presetids as $presetid) {
                    foreach($presetid as $id) {
                        array_push($presetidsarray, $id);
                    }
                }
                $formatvalue = 'select-' . str_replace('_attached_posts', '', $value);
                array_push($presetarray, array(
                    "cat" => $formatvalue, 
                    "ids" => $presetidsarray,
                ));
            }
        }
        update_post_meta($newpresetid, 'selected_presets', $presetarray);
        
        // redirect to flash-card area
        wp_redirect( $redirectlink );
    }
}
add_action('init', 'duplicate_presets');

?>