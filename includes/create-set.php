<?php

function createSet() {
	if(isset($_POST['create_set']) == '1') {
        $jsarray = $_POST['create_set_presets'];
        
        $create_set = array(
			'post_type' => 'portfolio_sets',
			'post_title' => $_POST['create_set_title'],
			'post_content' => $_POST['create_set_content'],
			'post_status' => 'publish',
		);

        $decodeready = stripslashes($jsarray);
        $decoded = json_decode($decodeready, true);

        // assign the preset cards
		$newpostid = wp_insert_post( $create_set );
        update_post_meta($newpostid, 'selected_presets', $decoded);

        // create the custom cards
        $ccjsarray = stripslashes($_POST['create_set_cards']);
        $decodedcards = json_decode($ccjsarray, true);
        
        foreach($decodedcards as $decodedcard) {
            $create_card = array(
                'post_type' => 'portfolio_flashcards',
                'post_title' => $decodedcard['post_title'],
                'post_content' => $decodedcard['post_desc'],
                'post_status' => 'publish',
            );
            $newcardid = wp_insert_post( $create_card );
            set_post_thumbnail($newcardid, $decodedcard['thumb_id']);
            update_post_meta($newcardid, 'parent_sets', $newpostid);
        }
		wp_redirect( get_permalink( $newpostid ) );
	}
}
add_action('init', 'createSet');


?>