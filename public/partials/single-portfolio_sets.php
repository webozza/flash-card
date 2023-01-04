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
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if(is_singular('portfolio_sets')) { ?>

<?php
    $pluginimg = '/wp-content/plugins/flash-card/public/img/';
    // Check Limit

    if(is_user_logged_in()) {
        $setownerid = get_current_user_id();
        $usersetlimit = get_user_meta($setownerid, 'set_creation_limit')[0];
        $usersetcount = count_user_posts($setownerid, 'portfolio_sets')[0];
    }

    // Get Options
    $getoptions = get_option('fc_rlink');
    $redirectid = $getoptions['duplicate_redirect_id'];
    $redirectlink = $getoptions['duplicate_redirect_link'];

    // Other stuff
    $pluginurl = '/wp-content/plugins/flash-card/';
    $fcimg = '/wp-content/plugins/flash-card/public/img/';
    $cardsetid = get_the_ID();
    $carduserid = get_current_user_id();
    $currentUserId = get_current_user_id();
    $duplicateSetId = $cardsetid;

?>

<script>
    let flashcardSettings = {
        nonce: "<?= wp_create_nonce('wp_rest') ?>",
        pluginimg: "<?= $pluginimg ?>",
        cardsetid: "<?= $cardsetid ?>",
        redirectlink: "<?= $redirectlink ?>",
    }
</script>

<?php get_header() ?>
<?php
        
        // Query the custom cards
        $customcards = array(
            'post_type' => 'portfolio_flashcards',
            'posts_per_page' => -1,
            'order' => 'ASC',
            // 'author' => $carduserid,
            'meta_query' => array(
                array(
                    'key' => 'parent_sets',
                    'value' => $cardsetid
                )
            )
        );
        $custom_cards = new WP_Query( $customcards );

        $totalcustomcards = $custom_cards->post_count;
        
        $getpresetcards = get_post_meta($cardsetid, 'selected_presets')[0];
        $uniquepresetcardids = array();

        foreach($getpresetcards as $dog) {
            foreach($dog['ids'] as $cat) {
                array_push($uniquepresetcardids, $cat);
            }
        }

        $totalpresetcards = count(array_unique($uniquepresetcardids));
        $totalcardsofset = $totalcustomcards + $totalpresetcards;

        // Query the preset cards
        $preset_cards = array (
            'post__in' => $uniquepresetcardids,
            'orderby' => 'post__in',
            'post_status' => 'publish',
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
        );
        
        $presetcards = new WP_Query($preset_cards);
        
    ?>
    <div class="fc-main-container">
        <div class="fc-heading text-center">
            <h1 class="entry-title"><?= get_the_title($cardsetid) ?></h1>
            <p><?= get_the_content($cardsetid) ?></p>
        </div>
        <div class="fc-body flashcard_set" style="width:650px;height: 600px;">
            <div id="set-id-<?= $cardsetid ?>" class="text-center flashcard_set-container">
                <!-- Slider main container -->
                <div class="swiper">
                <!-- Additional required wrapper -->
                <div id="fc--swiper" class="swiper-wrapper">
                    <!-- Slides | Start the Loop -->
                    <?php $row = 1; while ( $custom_cards->have_posts() ) : $custom_cards->the_post(); ?>
                    <div class="fc-item card-fc-item swiper-slide">
                        <div class="item">
                            <div id="card" class="card-item fc-custom--card" custom-card-id="<?= get_the_id() ?>">
                            <div class="front">
                                <div class="fav-container">
                                <div class="loader-container">
                                    <div class="loader"></div>
                                </div>
                                <a href="javascript:void(0)" class="fav removed-from-fav">
                                    <svg
                                    aria-hidden="true"
                                    focusable="false"
                                    data-prefix="fas"
                                    data-icon="star"
                                    role="img"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                    style="backface-visibility: hidden"
                                    >
                                    <path
                                        no-flip=""
                                        fill="currentColor"
                                        d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                        class=""
                                        style="backface-visibility: hidden"
                                    ></path>
                                    </svg>
                                </a>
                                <div
                                    class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                    data-remove="Star card for study later"
                                    data-add="Remove star"
                                    style=""
                                >
                                    <span style="backface-visibility: hidden"
                                    >Star card for study later</span
                                    >
                                </div>
                                </div>
                                <div class="card-text">
                                    <!-- <span class="">Side A</span> -->
                                    <img class="fc--card-thumb swiper-lazy" data-src="<?= get_the_post_thumbnail_url() ?>"></img>
                                </div>
                            </div>
                            <div class="back">
                                <div class="fav-container">
                                <div class="loader-container">
                                    <div class="loader"></div>
                                </div>
                                <a href="javascript:void(0)" class="fav removed-from-fav">
                                    <svg
                                    aria-hidden="true"
                                    focusable="false"
                                    data-prefix="fas"
                                    data-icon="star"
                                    role="img"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                    style="backface-visibility: hidden"
                                    >
                                    <path
                                        fill="currentColor"
                                        d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                        class=""
                                        style="backface-visibility: hidden"
                                    ></path>
                                    </svg>
                                </a>
                                <div
                                    class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                    data-remove="Star card for study later"
                                    data-add="Remove star"
                                    style=""
                                >
                                    <span style="backface-visibility: hidden"
                                    >Star card for study later</span
                                    >
                                </div>
                                </div>
                                <div class="card-text">
                                    <h3 class="card-title"><?= get_the_title() ?></h3>
                                    <p class="card-description"><?= get_the_content() ?></p>
                                    <p class="card-cat"></p>
                                </div>
                            </div>
                            <div class="loader-container full-screen" style="display: none">
                                <div class="loader"></div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php ++$row; endwhile; ?>

                    <!-- Preset Cards Loop -->
                    <?php while ( $presetcards->have_posts() ) : $presetcards->the_post(); ?>
                    <div class="fc-item card-fc-item swiper-slide">
                            <div class="item">
                                <div id="card" class="card-item fc-preset--card" preset-card-id="<?= get_the_ID() ?>">
                                <div class="front">
                                    <div class="fav-container">
                                    <div class="loader-container">
                                        <div class="loader"></div>
                                    </div>
                                    <a href="javascript:void(0)" class="fav removed-from-fav">
                                        <svg
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="star"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                        style="backface-visibility: hidden"
                                        >
                                        <path
                                            fill="currentColor"
                                            d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                            class=""
                                            style="backface-visibility: hidden"
                                        ></path>
                                        </svg>
                                    </a>
                                    <div
                                        class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                        data-remove="Star card for study later"
                                        data-add="Remove star"
                                        style=""
                                    >
                                        <span style="backface-visibility: hidden"
                                        >Star card for study later</span
                                        >
                                    </div>
                                    </div>
                                    <div class="card-text">
                                        <!-- <span class="">Side A</span> -->
                                        <img class="fc--card-thumb swiper-lazy" data-src="<?= get_the_post_thumbnail_url() ?>"></img>
                                    </div>
                                </div>
                                <div class="back">
                                    <div class="fav-container">
                                    <div class="loader-container">
                                        <div class="loader"></div>
                                    </div>
                                    <a href="javascript:void(0)" class="fav removed-from-fav">
                                        <svg
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="star"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        class="svg-inline--fa fa-star fa-w-18 fa-2x"
                                        style="backface-visibility: hidden"
                                        >
                                        <path
                                            fill="currentColor"
                                            d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                            class=""
                                            style="backface-visibility: hidden"
                                        ></path>
                                        </svg>
                                    </a>
                                    <div
                                        class="UITooltip UITooltip--micro UITooltip--includeArrowInBounds fav-tooltip"
                                        data-remove="Star card for study later"
                                        data-add="Remove star"
                                        style=""
                                    >
                                        <span style="backface-visibility: hidden"
                                        >Star card for study later</span
                                        >
                                    </div>
                                    </div>
                                    <div class="card-text">
                                        <div class="card-title-container">
                                            <h3 class="card-title"><?= get_the_title() ?></h3>
                                                <span class="fc-item-header">Other Item Names:</span>
                                                <p><?= get_the_content() ?></p>
                                        </div>
                                        <div class="card-description-container">
                                            <span class="fc-item-header">Item Description:</span>
                                            <p><?= get_post_meta($post->ID, '_custom_editor_1', true) ?></p>
                                        </div>
                                        <div class="card-cat-container">
                                            <p class="card-cat" data-post-id="<?= get_the_ID() ?>">
                                                <?php 
                                                    $postcats = wp_get_object_terms( $post->ID, 'portfolio_entries', array( 'fields' => 'names' ) );
                                                    foreach($postcats as $postcat) {
                                                        echo '<span class="cat-names">'.$postcat.'</span>';
                                                    };
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="loader-container full-screen" style="display: none">
                                    <div class="loader"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    <?php ++$row; endwhile; ?>
                </div>
                <!-- Flashcard Slide Controls -->
                <div class="flashcard-slide-controls">
                    <div class="fc-slide-controls-left"></div>
                    <div class="fc-slide-controls-middle">
                        <div class="swiper-button-prev">&#8592;</div>
                        <div class="fc-slide-count">
                            <span class="fc-current-card">1</span>
                            <span class="fc-slide-divider">/</span>
                            <span class="fc-total-cards"><?= $totalcardsofset ?></span>
                        </div>
                        <div class="swiper-button-next">&#8594;</div>
                    </div>
                    <div class="fc-slide-controls-right">
                        <a href="javascript:void(0)" class="shuffle-cards" style="display:none">
                            <img src="<?= $fcimg . 'shuffle-icon.png' ?>">
                        </a>
                        <a href="javascript:void(0)" class="shuffle-cards-2">
                            <img src="<?= $fcimg . 'shuffle-icon.png' ?>">
                        </a>
                        <a href="javascript:void(0)" class="switch-cards">
                            <img src="<?= $fcimg . 'switch-icon.png' ?>">
                        </a>
                    </div>
                </div>
                <!-- Flashcard Slide Controls -->
                <?php if(is_user_logged_in()) { ?>
                    <?php if($totalcardsofset > 0 && ($usersetcount < $usersetlimit || $usersetlimit == "")) { ?>
                        <form class="duplicate-set-form" style="display:none;" action="" method="post">
                            <input type="hidden" name="dup_set_id" value="<?= $cardsetid ?>">
                            <input type="hidden" name="duplicate_post" value="1"> 
                            <button type="submit">duplicate set</button>
                        </form>
                        <a id="duplicateSet" href="javascript:void(0)" class="button primary" style="margin-top:20px;">
                            <span>Duplicate Set</span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

<?php get_footer() ?>
<?php } ?>


