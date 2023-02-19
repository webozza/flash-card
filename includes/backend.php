<?php

    // Adds Featured Set Checkbox
    function is_featured_set( $post ) {
    if ( 'portfolio_sets' === $post->post_type ) { ?>

    <?php
        $getfeaturedsetvalue = get_post_meta(get_the_ID(), 'featured_set');
    ?>

    <div class="misc-pub-section" id="featured_set_checkbox">
        <input type="checkbox" name="featured_set" value="<?= $getfeaturedsetvalue[0] ?>">
        <label> Is this a featured set?</label>
        <a style="display:none" class="" href="javascript:void(0)">Save</a>
    </div>
    <input type="hidden" name="featured_set_val" value="1">
    
    <?php
        if(isset($getfeaturedsetvalue[0])) { 
            if($getfeaturedsetvalue[0] == "true") {
                ?>
                    <script>jQuery('[name="featured_set"]').prop("checked", true);</script>
                <?php
            } else {
                ?>
                    <script>jQuery('[name="featured_set"]').prop("checked", false);</script>
                <?php
            }
        }  
    ?>

    <?php }}

    add_action( 'post_submitbox_misc_actions', 'is_featured_set' );

    function update_set_featured() {
        if(isset($_POST['publish_portfolio_sets']) == '1') {
            $getpresetvalues = get_post_meta($_POST['post_ID'], 'selected_presets');
            // save the featured set status

            if(isset($_POST['featured_set']) == true) {
                update_post_meta($_POST['post_ID'], 'featured_set', 'true');
            } else {
                update_post_meta($_POST['post_ID'], 'featured_set', 'false');
            }
            
            // if(isset($_POST['post_ID'])) {
            //     if($_POST['featured_set'] == null || $_POST['featured_set'] == "") {
            //         $isfeaturedset = "false";
            //     } else {
            //         $isfeaturedset = "true";
            //     }
                
            // }
        
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

    /* All Sets Backend Edit Page
     * ---------------------------------------------------------------------*/
    ?>
        <script>
            jQuery(document).ready(function($) {
                let fetchAllSets = async () => {
                const url = `/wp-json/wp/v2/portfolio_sets`;
                let res = await fetch(url);
                return await res.json();
            };
        
            let checkAllSets = async () => {
                let allSets = await fetchAllSets();
                if ($(".no-items").length !== 1) {
                $(".wp-list-table thead tr").append("<th>Featured</th>");
                $("#the-list tr").append(
                    "<td><span class='featured-set-checkbox'><input class='star' type='checkbox'></span></td>"
                );
                }
        
                allSets.map((entries) => {
                if (entries.meta.featured_set[0] == "true") {
                    var findFeaturedSets = entries.id;
                    $(
                    `#the-list tr[id='post-${findFeaturedSets}'] td:last-child input`
                    ).attr("checked", "");
                }
                });
        
                updateSetMetas();
            };
        
            var _updateSetMetas = {
                meta: {
                featured_set: "",
                },
            };
        
            var updateSetId;
            let getSet = async () => {
                const url = `/wp-json/wp/v2/portfolio_sets/${updateSetId}`;
                let res = await fetch(url, {
                method: "POST",
                headers: {
                    "X-WP-Nonce": flashcardSettings.nonce,
                    "Content-type": "application/json; charset=UTF-8",
                },
                body: JSON.stringify(_updateSetMetas),
                });
                return await res.json();
            };
        
            let updateSet = async () => {
                let updateSet = await getSet();
                console.log(updateSet);
            };
        
            let updateSetMetas = async () => {
                $(".featured-set-checkbox").on("click", function () {
                var checkbox = $(this).find("input");
                if (checkbox[0].checked == true) {
                    checkbox.attr("checked", "");
                    _updateSetMetas.meta.featured_set = "true";
                } else {
                    checkbox.removeAttr("checked", "");
                    _updateSetMetas.meta.featured_set = "false";
                }
                updateSetId = checkbox.parent().parent().parent().attr("id").slice(5);
                updateSet();
                });
            };
            });
        </script>
    <?php

?>