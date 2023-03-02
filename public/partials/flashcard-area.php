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
$getallusers = wp_get_current_user();
if ( in_array( '', (array) $getallusers->roles ) ) {
    echo 'this use has the none role';
}
echo get_current_user_id();
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
$whatINeed = explode('/', $_SERVER['REQUEST_URI']);
$shortcodepath = $_SERVER['HTTP_HOST'] . '/' . $whatINeed[1] . '/';
$getoptions = get_option('fc_rlink');
$redirectslug = $getoptions['duplicate_redirect_slug'];
global $paged;
$curpage = $paged ? $paged : 1;

$usersets = array(
    'post_type' => 'portfolio_sets',
    'author' => get_current_user_id(),
    'posts_per_page' => 10,
    'order' => 'DESC',
    'paged' => $paged
);
$user_sets = new WP_Query( $usersets ); 

$featuredsets = array(
    'post_type' => 'portfolio_sets',
    'posts_per_page' => -1,
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' => 'featured_set',
            'value' => 'true'
        )
    )
);

$featured_sets = new WP_Query( $featuredsets );

$setownerid = get_current_user_id();
$usersetlimit = get_user_meta($setownerid, 'set_creation_limit')[0];
$usersetcount = count_user_posts($setownerid, 'portfolio_sets')[0];

global $post;
?>

<?php if ( $user_sets->have_posts() && !isset($_GET['edit-set']) && !isset($_GET['new-set']) ) : ?>
	<script>
		var initialSets = '<?php echo $user_sets->post_count ?>';
	</script>
    <div class="flash-card-area-wrapper">

        <!-- Custom sets -->
        <div id="custom-flashcard-sets">
			<input type="hidden" id="total-sets" value="">
            <h2>Custom Flashcard Sets</h2>
            <div class="inner">
                <?php while ( $user_sets->have_posts() ) : $user_sets->the_post(); ?>
                    <?php 
                        $setslug = sanitize_title(get_the_title());
                        $setid = get_the_ID();
                    ?>
                    <div id="custom-card-<?= get_the_ID() ?>" class="custom-card-crud">
                        <div data-attr="<?= $setslug ?>" class="card-title"><a href="<?= "/" . "$redirectslug/" . $setid . '/' . $setslug ?>"><h4><?php the_title(); ?></h4></a></div>
                        <div class="btn-crud">
                            <!-- Delete set form -->
                            <form class="delete-set-form" style="display:none" action="" method="post">
                                <input type="hidden" name="delete_set_id" value="<?= get_the_ID() ?>">
                                <input type="hidden" name="delete_post" value="1"/> 
                                <button type="submit"></button>
                            </form>
                            <a class="cc-delete" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/delete.png" /></a>
                            <a class="cc-edit" href="https://<?= $shortcodepath . '?edit-set=' . get_the_ID() ?>" class=""><img src="/wp-content/plugins/flash-card/public/img/edit.png" />
                                <form class="fc--hide">
                                    <input type="hidden" name="edit-set" value="<?= get_the_ID() ?>">
                                    <button type="submit" ></button>
                                </form>
                            </a>
                            <?php if($usersetcount < $usersetlimit && $usersetlimit != "" && $usersetlimit != 0) { ?>
                                <!-- Duplicate set form -->
                                <form class="duplicate-set-form" style="display:none" action="" method="post">
                                    <input type="hidden" name="dup_set_id" value="<?= get_the_ID() ?>">
                                    <input type="hidden" name="duplicate_post" value="1"/> 
                                    <button type="submit"></button>
                                </form>
                                <a class="cc-duplicate" href="javascript:void(0)"><img src="/wp-content/plugins/flash-card/public/img/duplicate.png" /></a>
                            <?php } ?>
                        </div>
                    </div>
					<div class="creating-set-loader" style="display:none;">
                        <span class="loader">
                            <img src="/wp-content/plugins/flash-card/public/img/loader.png">
                        </span>
                        <div class="success-message">
                            Duplicating set...
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if ( $user_sets->max_num_pages > 1) { ?> 
            <?php
                echo '
                <div id="wp_pagination">
                    <a class="first page" href="'.get_pagenum_link(1).'">&laquo;</a>
                    <a class="previous page" href="'.get_pagenum_link(($curpage-1 > 0 ? $curpage-1 : 1)).'">&lsaquo;</a>';
                    for($i=1;$i<=$user_sets->max_num_pages;$i++)
                    echo '<a class="'.($i == $curpage ? 'active ' : '').'page" href="'.get_pagenum_link($i).'">'.$i.'</a>';
                    echo '
                    <a class="next page" href="'.get_pagenum_link(($curpage+1 <= $user_sets->max_num_pages ? $curpage+1 : $user_sets->max_num_pages)).'">&rsaquo;</a>
                    <a class="last page" href="'.get_pagenum_link($user_sets->max_num_pages).'">&raquo;</a>
                </div>
                ';
            ?>
        <?php } ?>

        
        <!-- Create Set Btn -->

        <?php if($usersetcount >= $usersetlimit && $usersetlimit !== "") { ?>
            <span>You have exceeded your limit for creating sets....</span>
        <?php } ?>

        <?php if($usersetcount < $usersetlimit && $usersetlimit != "" && $usersetlimit != 0) { ?>
            <a id="createSet" href="<?= 'https://' . $shortcodepath . '?new-set'?>" class="button primary">
                <span>Create Set</span>
            </a>
        <?php } ?>

        <!-- Default Sets (aka featured sets) -->
        <div id="default-flashcard-sets">
            <h2>Default Flashcard Sets</h2>
            <div class="inner">
                <?php while ( $featured_sets->have_posts() ) : $featured_sets->the_post(); ?>
                    <div id="default-card-<?= get_the_ID() ?>" class="default-card-crud">
                        <div class="card-title"><a href="<?= "/" . "$redirectslug/" . get_the_ID() . '/' . $featured_sets->post_title ?>"><h4><?php the_title(); ?></h4></a></div>
                        <div class="btn-crud">
                        <?php if($usersetcount < $usersetlimit && $usersetlimit != "" && $usersetlimit != 0) { ?>
                            <a class="cc-duplicate" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/duplicate.png" /></a>
                            <!-- Duplicate set form -->
                            <form class="duplicate-set-form" style="display:none" action="" method="post">
                                <input type="hidden" name="dup_set_id" value="<?= get_the_ID() ?>">
                                <input type="hidden" name="duplicate_post" value="1"/> 
                                <button type="submit"></button>
                            </form>
                        <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>

<!-----------------------------------------------
    USERS WITH 0 SETS WILL SEE THIS 
----------------------------------------------->
<?php if ( !$user_sets->have_posts() && !isset($_GET['edit-set']) && !isset($_GET['new-set'])) : ?>
    <div class="flash-card-area-wrapper">

        <!-- Custom sets -->
        <div id="custom-flashcard-sets">
            <h2>Custom Flashcard Sets</h2>
            <div class="inner">
                <span>You have no sets</span>
                <?php while ( $user_sets->have_posts() ) : $user_sets->the_post(); ?>
                    <div id="custom-card-<?= get_the_ID() ?>" class="custom-card-crud">
                        <div class="card-title"><a href="<?= "/" . "$redirectslug/" . $setid . '/' . $setslug ?>"><h4><?php the_title(); ?></h4></a></div>
                        <div class="btn-crud">
                            <a class="cc-delete" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/delete.png" />
                            </a>
                            <a class="cc-edit" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/edit.png" />
                                <form class="fc--hide">
                                    <input type="hidden" name="edit-set" value="<?= get_the_ID() ?>">
                                    <button type="submit" ></button>
                                </form>
                            </a>
                            <?php if($usersetcount < $usersetlimit && $usersetlimit != "" && $usersetlimit != 0) { ?>
                                <a class="cc-duplicate" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/duplicate.png" /></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <!-- Create Set Btn -->
        <a id="createSet" class="button primary" href="<?= 'https://' . $shortcodepath . '?new-set'?>" class="button primary">
            <span>Create Set</span>
        </a>

        <!-- Default Sets (aka featured sets) -->
        <div id="default-flashcard-sets">
            <h2>Default Flashcard Sets</h2>
            <div class="inner">
                <?php while ( $featured_sets->have_posts() ) : $featured_sets->the_post(); ?>
                    <div id="default-card-<?= get_the_ID() ?>" class="default-card-crud">
                        <div class="card-title"><a href="<?= "/" . "$redirectslug/" . $setid . '/' . $setslug ?>"><h4><?php the_title() ?></h4></a></div>
                        <div class="btn-crud">
                        <?php if($usersetcount < $usersetlimit && $usersetlimit != "" && $usersetlimit != 0) { ?>
                            <a class="cc-duplicate" href="javascript:void(0)" class=""><img src="/wp-content/plugins/flash-card/public/img/duplicate.png" /></a>
                        <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php wp_reset_postdata(); ?>

    <script>
        jQuery(document).ready(function($) {
            if(window.location.href.indexOf('/page/') > -1 && $('.custom-card-crud').length == 0) {
                window.location.href = "https://<?= $shortcodepath ?>";
            }
        });
    </script>

<?php endif; ?>

<!-----------------------------------------------
    EDIT SET
----------------------------------------------->
<?php if( isset($_GET['edit-set']) && get_post_type( $_GET['edit-set'] ) == 'portfolio_sets' ) : ?>

    <script type="text/javascript">
        var setId = '<?= $_GET['edit-set'] ?>';
        jQuery(document).ready(function($) {
            let isNewSet = $('.set-title input').val() == "New Set Title";
            if(isNewSet == true) {
                $('#updateSet span').text('Create Set');
                $('.set-title input').attr('value', 'New Set');
            }
        });
    </script>

    <?php 
        // Query the selected post for editing
        $pid = $_GET['edit-set'];
        $selectedset = array(
            'p' => $pid,
            'post_type' => 'portfolio_sets',
            'posts_per_page' => -1,
            'order' => 'ASC',
        );
        $edit_set = new WP_Query( $selectedset ); 
        remove_filter('the_content', 'wpautop');

        // Query the custom cards
        $customcards = array(
            'post_type' => 'portfolio_flashcards',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'parent_sets',
                    'value' => $pid
                )
            )
        );
        $custom_cards = new WP_Query( $customcards );

        $checkpostid = $_GET['edit-set'];
        $checkauthor = get_post_field( 'post_author', $checkpostid );
        $currentuserid = get_current_user_id();

    ?>

<?php if( $currentuserid != $checkauthor ) { ?>
    <h3>You're not allowed to edit this set</h3>
<?php } ?>

<?php if( $currentuserid == $checkauthor ) { ?>

    <form style="display:none;" class="edit-set-form" action="" method="post">
        <input type="hidden" name="edit_existing_set" value="1">
        <input type="hidden" name="edit_set_id" value="<?= $_GET['edit-set'] ?>">
        <input type="hidden" name="edit_set_title" value="">
        <input type="hidden" name="edit_set_content" value="">
        <input type="hidden" name="edit_set_presets" value="">
        <input type="hidden" name="new_set_cards" value="">
        <input type="hidden" name="existing_set_cards" value="">
        <button type="submit">Submit</button>
    </form>

    <script>
        jQuery(document).ready(function($) {
            $('[name="edit_set_title"]').val($('#set_title').val());
            $('[name="edit_set_content"]').val($('#edit_set_desc').val());

            $('#set_title').change(function() {
                $('[name="edit_set_title"]').val($(this).val());
            });
            $('#edit_set_desc').change(function() {
                $('[name="edit_set_content"]').val($(this).val());
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
                $('[name="edit_set_presets"]').val(parseIt);
            })

            var customCards = [];
            var existingCustomCards = [];

            //new set cards
            $('.edit-set-form').submit(function(e) {
                customCards = [];
                existingCustomCards = [];
                e.preventDefault();
                $('.new-card').each(function() {
                    customCards.push({
                        post_title: $(this).find('input[name="new_cc_title"]').val(),
                        post_desc: $(this).find('textarea[name="new_cc_desc"]').val(),
                        thumb_id: $(this).find('input[name="rudr_img"]').val(),
                    })
                });
                
                $('[name="new_set_cards"]').val(JSON.stringify(customCards));

                //existing set cards
                $('.accordion-item:not(.new-card)').each(function() {
                    existingCustomCards.push({
                        post_title: $(this).find('input[name="edit_cc_title"]').val(),
                        post_desc: $(this).find('textarea[name="edit_cc_desc"]').val(),
                        thumb_id: $(this).find('input[name="rudr_img"]').val(),
                        post_id: $(this).find('input.existing-card-id').val(),
                    })
                });
                $('[name="existing_set_cards"]').val(JSON.stringify(existingCustomCards));
                $(this).unbind('submit').submit();
            });
            

            let createSet = () => {
                $('#updateSet').click(function() {
                    $('.edit-set-form').submit();
                });
            }
            createSet();

        });
    </script>

    <div class="flash-card-edit-wrapper">
        <?php while ( $edit_set->have_posts() ) : $edit_set->the_post(); ?>
        <div id="editing-set-<?= get_the_ID() ?>">
            <div class="set-title">
                <h2>Flashcard set title</h2>
                <input id="set_title" type="text" value="<?php the_title() ?>" >
            </div>
            <div class="set-description">
                <h2>Flashcard set description</h2>
                <textarea type="text" id="edit_set_desc" name="fname" value="<?php the_content() ?>"><?php the_content() ?></textarea>
            </div>
        <?php wp_reset_postdata(); ?>
        <?php endwhile; ?>
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
            'order' => 'ASC',
            'orderby' => 'date',
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
            echo "<h3 class='fc_cf_title'>". $category->name ."</h3>"; ?>
                <div class="fc_custom_field_">
                    <select multiple="multiple" id="<?= 'select-' . $category->slug ?>" class="preset-selection" style="visibility:hidden">
            <?php 
            while ( $cat_query->have_posts() ) {
                $cat_query->the_post();
                ?>
                    <option value="<?php the_ID() ?>" data-img_url="<?php the_post_thumbnail_url("thumbnail") ?>">
                        <?php echo get_post_field('post_title') ?>
                    </option>
                <?php
            }
            echo "</select><a class='save-preset fc--hide' href='javascript:void(0)'>save</a></div>";
        }
        wp_reset_postdata();
    }
    ?>
    
</div>



<!-- CUSTOM CARDS
-------------------------------------------------------------------------------->
<div class="custom-cards">
    <h2>Custom Cards</h2>
    <div class="accordion">
        <?php while ( $custom_cards->have_posts() ) : $custom_cards->the_post(); ?>
        <div id="custom-card-<?= get_the_ID() ?>" class="accordion-item">
            <a href="javascript:void(0)" class="accordion-title plain">
                <div>
                    <div class="card---title"><?= get_the_title() ?></div>
                    <button class="toggle" aria-label="Toggle">
                        <i class="icon-angle-down"></i>
                    </button>
                </div>
                <div>
                    <span class="delete--card">
                        <img draggable="false" role="img" class="emoji" alt="❌" src="https://s.w.org/images/core/emoji/14.0.0/svg/274c.svg">
                    </span>
                </div>
            </a>
            <div class="accordion-inner">
                <h3>Side A</h3>
                <p>Title of card</p>
                <input type="text" name="edit_cc_title" value="<?= get_the_title() ?>">
                <p>Card description</p>
                <textarea type="text" id="fname" name="edit_cc_desc" value="<?= get_the_content() ?>"><?= get_the_content() ?></textarea>
                <h3>Side B</h3>
                <div class="cc_field_image">
                    <a href="#" class="rudr-upload"><img featured_media_id="<?= get_post_thumbnail_id() ?>" src="<?php the_post_thumbnail_url("thumbnail") ?>"></a>
                    <a href="javascript:void(0)" class="rudr-remove" style="">Remove image</a>
                    <input type="hidden" name="rudr_img" value="<?= get_post_thumbnail_id() ?>">
                </div>
                <div class="update--card hidden">
                    <a href="javascript:void(0)">Update Card</a>
                </div>
            </div>
            <input type="hidden" class="existing-card-id" name="edit_cc_id" value="<?= get_the_ID() ?>">
        </div>
        <?php endwhile; ?>
    </div>
    <a id="addNewCard" class="button primary"><span>+</span></a>
    </div>
        </div>
    </div>
    <div class="fc-publish">
        <a id="updateSet" class="button primary">
            <span>Update set</span>
        </a>
        <div class="creating-set-loader" style="display:none;">
            <span class="loader">
                <img src="/wp-content/plugins/flash-card/public/img/loader.png">
            </span>
            <div class="success-message">
                Updating set...
            </div>
        </div>
    </div>
</div>

<?php } ?>
<?php wp_reset_postdata(); ?>
<?php endif; ?>


<!-- IF SOMEONE TRIES TO EDIT A NORMAL POST DIRECTLY -->
<?php if( isset($_GET['edit-set']) && get_post_type( $_GET['edit-set'] ) == 'post' ) : ?>
    <h3>You're not allowed to edit this post</h3>
<?php wp_reset_postdata(); ?>
<?php endif; ?>