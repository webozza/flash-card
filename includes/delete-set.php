<?php

function deleteSets() {
	if(isset($_POST['delete_post']) == '1') {
		$deletesetid = $_POST['delete_set_id'];
		wp_delete_post($deletesetid, false);

        // get cards of deleted set
        $fc_cards = array(
            'post_type' => 'portfolio_flashcards',
            'posts_per_page' => -1,
            'order' => 'ASC',
            // 'author' => $carduserid,
            'meta_query' => array(
                array(
                    'key' => 'parent_sets',
                    'value' => $deletesetid
                )
            )
        );
        $fc_cards_query = new WP_Query( $fc_cards );
		$fccards = $fc_cards_query->posts;
		foreach($fccards as $fccard) {
            wp_delete_post($fccard->ID, false);
        }
		wp_redirect( site_url() . $_SERVER['REQUEST_URI']);
	}
}
add_action('init', 'deleteSets');

?>