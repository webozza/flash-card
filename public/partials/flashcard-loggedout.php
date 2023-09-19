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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<script type="text/javascript">
    var currentUserId = '<?= get_current_user_id() ?>';  
    var flashcardSettings = {
        nonce: '<?= wp_create_nonce('wp_rest') ?>',
    };
    jQuery(document).ready(function($) {
        $('body').addClass('shortcode-flashcard')
    });
</script>

<?php
global $paged;
$curpage = $paged ? $paged : 1;

$usersets = array(
    'post_type' => 'portfolio_sets',
    'author' => get_current_user_id(),
    'posts_per_page' => 10,
    'order' => 'ASC',
    'paged' => $paged
);
$user_sets = new WP_Query( $usersets ); 

$featuredsets = array(
    'post_type' => 'portfolio_sets',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'featured_set',
            'value' => 'true'
        )
    )
);

$featured_sets = new WP_Query( $featuredsets ); 

?>
    <div class="flash-card-area-wrapper">

        <!-- Custom sets -->
        <div id="custom-flashcard-sets">
            <h2>Custom Flashcard Sets</h2>
            <div>Please login to create sets</div>
        </div>

        <!-- Default Sets (aka featured sets) -->
        <div id="default-flashcard-sets">
            <h2>Default Flashcard Sets</h2>
            <div class="inner">
                <?php while ( $featured_sets->have_posts() ) : $featured_sets->the_post(); ?>
                    <div id="default-card-<?= get_the_ID() ?>" class="default-card-crud">
                        <div class="card-title"><a href="<?= get_permalink() ?>"><h4><?php the_title(); ?></h4></a></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php wp_reset_postdata(); ?>

