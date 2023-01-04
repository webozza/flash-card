<?php

function editSet() {
	if(isset($_POST['edit_existing_set']) == '1') {
        
        
        $edit_set = array(
            'ID' => $_POST['edit_set_id'],
			'post_type' => 'portfolio_sets',
			'post_title' => $_POST['edit_set_title'],
			'post_content' => $_POST['edit_set_content'],
			'post_status' => 'publish',
		);

        wp_insert_post( $edit_set, false );

        $editsetjsarray = $_POST['edit_set_presets'];
        $decodeready = stripslashes($editsetjsarray);
        $decoded = json_decode($decodeready, true);

        // assign the preset cards
		
        update_post_meta($_POST['edit_set_id'], 'selected_presets', $decoded);

        // update existing custom cards
        $ccjsarray = stripslashes($_POST['existing_set_cards']);
        $decodedcards = json_decode($ccjsarray, true);
        
        foreach($decodedcards as $decodedcard) {
            $create_card = array(
                'ID' => $decodedcard['post_id'],
                'post_type' => 'portfolio_flashcards',
                'post_title' => $decodedcard['post_title'],
                'post_content' => $decodedcard['post_desc'],
                'post_status' => 'publish',
            );
            wp_insert_post( $create_card );
            set_post_thumbnail($decodedcard['post_id'], $decodedcard['thumb_id']);
            update_post_meta($decodedcard['post_id'], 'parent_sets', $_POST['edit_set_id']);
        }

        // create new custom cards
        $newccjsarray = stripslashes($_POST['new_set_cards']);
        $newdecodedcards = json_decode($newccjsarray, true);
        
        foreach($newdecodedcards as $newdecodedcard) {
            $new_create_card = array(
                'post_type' => 'portfolio_flashcards',
                'post_title' => $newdecodedcard['post_title'],
                'post_content' => $newdecodedcard['post_desc'],
                'post_status' => 'publish',
            );
            $newcardid = wp_insert_post( $new_create_card );
            set_post_thumbnail($newcardid, $newdecodedcard['thumb_id']);
            update_post_meta($newcardid, 'parent_sets', $_POST['edit_set_id']);
        }


		wp_redirect( get_permalink( $_POST['edit_set_id'] ) );
	}
}
add_action('init', 'editSet');


?>