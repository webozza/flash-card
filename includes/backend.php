<?php

    // Adds Featured Set Checkbox
    function is_featured_set( $post ) {
    if ( 'portfolio_sets' === $post->post_type ) { ?>

    <?php
        $getfeaturedsetvalue = get_post_meta(get_the_ID(), 'featured_set');
        $getpresetvalues = get_post_meta(get_the_ID(), 'selected_presets');
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
?>