<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://webozza.com
 * @since      1.0.0
 *
 * @package    Flash_Card
 * @subpackage Flash_Card/public/partials
 * Template Name: Flashcard Area
 */
?>

<?php
    $setownerid = get_current_user_id();
    $usersetlimit = get_user_meta($setownerid, 'set_creation_limit');
    $usersetcount = count_user_posts($setownerid, 'portfolio_sets')[0];

    // echo 'user creation limit = ' . $usersetlimit;
    // echo '<br>';
    // echo 'user has sets = ' . $usersetcount;
?>

<?php if( isset( $_GET['new-set'] ) && !isset( $_GET['edit-set'] ) && ($usersetcount >= $usersetlimit && $usersetlimit !== "")) { ?>
    <span>You have exceeded your limit for creating sets....</span>
<?php } ?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if ( isset( $_GET['new-set'] ) && !isset( $_GET['edit-set'] ) && ($usersetcount < $usersetlimit || $usersetlimit == "" || $usersetlimit != 0) ) { ?>

    <form style="display:none;" class="create-new-set-form" action="" method="post">
        <input type="hidden" name="create_set" value="1">
        <input type="hidden" name="create_set_title" value="">
        <input type="hidden" name="create_set_content" value="">
        <input type="hidden" name="create_set_presets" value="">
        <input type="hidden" name="create_set_cards" value="">
        <button type="submit"></button>
    </form>

    <div class="flash-card-edit-wrapper">
  <div id="" class="editing-set-container">
    <div class="set-title">
      <h2>Flashcard set title</h2>
      <input id="set_title" type="text" value="" />
    </div>
    <div class="set-description">
      <h2>Flashcard set description</h2>
      <textarea type="text" value=""></textarea>
    </div>
    <div class="set-cards">

<!-- PRESET CARDS 
-------------------------------------------------------------------------------->
<div class="preset-cards">
    <h2>Preset Cards</h2>
    <?php
        $cat_args = array (
            'taxonomy' => 'portfolio_entries',
    );
    $categories = get_categories ( $cat_args );
    
    foreach ( $categories as $category ) {
        $cat_query = null;
        $args = array (
            'post_type' => 'portfolio',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'tax_query' => array(
                    array(
                        'taxonomy'  => 'portfolio_entries',
                        'terms'     => array( $category->slug ),
                        'field'     => 'slug',
                    )
                )
        );
    
        $cat_query = new WP_Query( $args );
    
        if ( $cat_query->have_posts() ) {
            echo "<h3 class='cpfp_cf_title'>". $category->name ."</h3>"; ?>
                <div class="cpfp_custom_field">
                <select multiple="multiple" selected="selected" id="<?= 'select-' . $category->slug ?>" class="preset-selection" style="visibility:hidden">
            <?php 
            while ( $cat_query->have_posts() ) {
                $cat_query->the_post();
                ?>
                    <option 
                        value="<?php the_ID() ?>" 
                        data-img_url="<?php the_post_thumbnail_url() ?>"
                    >
                        <?php the_title() ?>
                    </option>
                <?php
            }
            echo "</select><a class='save-new-preset fc--hide' href='javascript:void(0)'>save</a></div>";
        }
        wp_reset_postdata();
    }
    ?>
    
</div>

      <!-- CUSTOM CARDS
-------------------------------------------------------------------------------->
      <div class="custom-cards">
        <h2>Custom Cards</h2>
        <div class="accordion"></div>
        <a id="addNewCard" class="button primary"><span>+</span></a>
      </div>
    </div>
  </div>
  <div class="fc-publish">
    <a href="javascript:void(0)" id="createNewSet" class="button primary">
      <span>Create set</span>
    </a>
    <div class="creating-set-loader" style="display:none;">
        <span class="loader">
            <img src="/wp-content/plugins/flash-card/public/img/loader.png">
        </span>
        <div class="success-message">
            Creating set...
        </div>
    </div>
  </div>
</div>

<script>
    jQuery(document).ready(function($) {

        // NEW STUFF HERE...
        $('#set_title').change(function() {
            $('[name="create_set_title"]').val($(this).val());
        });
        $('.set-description textarea').change(function() {
            $('[name="create_set_content"]').val($(this).val());
        });

        var presetArray = [];

        let runOnChange = () => {
            $('.preset-selection').each(function() {
            let eachSelection = $(this);
            let initialSelectionIds = [];
            let getSelectionIds = eachSelection.find(':selected');
                getSelectionIds.each(function() {
                    initialSelectionIds.push($(this).val());
                });
                let selectionCats = eachSelection.attr('id');
                presetArray.push({
                    cat: selectionCats,
                    ids: initialSelectionIds,
                });
            });
        }

        $('.preset-selection').change(function() {
            presetArray = [];
            runOnChange();
            var parseIt = JSON.stringify(presetArray);
            $('[name="create_set_presets"]').val(parseIt);
        })
        
        var customCards = [];
        $('.create-new-set-form').submit(function(e) {
            customCards = [];
            e.preventDefault();
            $('.new-card').each(function() {
                customCards.push({
                    post_title: $(this).find('input[name="new_cc_title"]').val(),
                    post_desc: $(this).find('textarea[name="new_cc_desc"]').val(),
                    thumb_id: $(this).find('input[name="rudr_img"]').val(),
                })
            });
            $('[name="create_set_cards"]').val(JSON.stringify(customCards));
            $(this).unbind('submit').submit();
        });

        let createSet = () => {
            $('#createNewSet').click(function() {
                $('.create-new-set-form').submit();
            });
        }
        createSet();

    });
</script>

<?php } ?>

