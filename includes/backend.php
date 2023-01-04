<?php

    // Adds Featured Set Checkbox
    function is_featured_set( $post ) {
    if ( 'portfolio_sets' === $post->post_type ) { ?>

    <?php
        $getfeaturedsetvalue = get_post_meta(get_the_ID(), 'featured_set');
        if(isset($getpresetvalues)) {
            $getpresetvalues = get_post_meta(get_the_ID(), 'selected_presets');
        }
    ?>

    <div class="misc-pub-section" id="featured_set_checkbox">
        <input type="checkbox" name="featured_set" value="<?= $getfeaturedsetvalue[0] ?>">
        <label> Is this a featured set?</label>
        <a style="display:none" class="" href="javascript:void(0)">Save</a>
    </div>
    
    <?php
        if(isset($getfeaturedsetvalue[0])) { 
            if($getfeaturedsetvalue[0] == true) {
                ?>
                    <script>jQuery('[name="featured_set"]').prop("checked", true);</script>
                <?php
            }
            if($getfeaturedsetvalue[0] == false) {
                ?>
                    <script>jQuery('[name="featured_set"]').prop("checked", false);</script>
                <?php
            }
        }  
    ?>

    <?php }}

    add_action( 'post_submitbox_misc_actions', 'is_featured_set' );

    function update_set_featured() {
        if(isset($_POST['post_type']) == 'portfolio_sets') {
            // save the featured set status
            update_post_meta(get_the_ID(), 'featured_set', $_POST['featured_set']);
            // save the preset selection
            $savepresetselection = $_POST['save_preset_cards'];
            $decodepresets = stripslashes($savepresetselection);
            $decodedpresets = json_decode($decodepresets, true);
            if($getpresetvalues != $savepresetselection) {
                update_post_meta(get_the_ID(), 'selected_presets', $decodedpresets);
            }
        }
    }
    add_action('save_post', 'update_set_featured');

    // save existing cards
    function update_set_custom_cards() {
        if(isset($_POST['publish_portfolio_sets']) == '1') {
            $saveexistingcards = stripslashes($_POST['save_existing_cards']);
            $decodedexistingcards = json_decode($saveexistingcards, true);
            
            foreach($decodedexistingcards as $decodedexistingcard) {
                $existing_save_card = array(
                    'ID' => $decodedexistingcard['post_id'],
                    'post_type' => 'portfolio_flashcards',
                    'post_title' => $decodedexistingcard['post_title'],
                    'post_content' => $decodedexistingcard['post_desc'],
                    'post_status' => 'publish',
                );
                wp_insert_post( $existing_save_card );
                set_post_thumbnail($decodedexistingcard['post_id'], $decodedexistingcard['thumb_id']);
                update_post_meta($decodedexistingcard['post_id'], 'parent_sets', $_POST['post_ID']);
            }
        }
    }
    add_action('init', 'update_set_custom_cards');

    // save new cards
    function save_new_custom_cards() {
        if(isset($_POST['publish_portfolio_sets']) == '1') {
            $savenewcards = stripslashes($_POST['save_new_cards']);
            $decodednewcards = json_decode($savenewcards, true);
            
            foreach($decodednewcards as $decodednewcard) {
                $new_save_card = array(
                    'post_type' => 'portfolio_flashcards',
                    'post_title' => $decodednewcard['post_title'],
                    'post_content' => $decodednewcard['post_desc'],
                    'post_status' => 'publish',
                );
                $newccid = wp_insert_post( $new_save_card );
                set_post_thumbnail($newccid, $decodednewcard['thumb_id']);
                update_post_meta($newccid, 'parent_sets', $_POST['post_ID']);
            }
        }
    }
    add_action('init', 'save_new_custom_cards');
?>