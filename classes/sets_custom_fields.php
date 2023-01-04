<?php

/* Custom Cards Meta Box
 * ---------------------------------------------------------------------*/
add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add() {
    add_meta_box( 
        'manage-custom-cards', 
        'Custom Cards', 
        'cd_meta_box_cb', 
        'portfolio_sets', 
        'normal', 
        'high' 
    );
}

function cd_meta_box_cb() {
    ?>
        <div id="mcc-rest" class="cc--new">
            <div id="accordion_new_card" class="accordion-container">      
            </div>
            <button class="button-primary" id="add_new_card">
                <span>+</span>
            </button>
        </div>
    <?php
}

/* Set Owner Meta Box
 * ---------------------------------------------------------------------*/
add_action( 'add_meta_boxes', 'set_owner_metabox' );
function set_owner_metabox() {
    add_meta_box( 
        'set_owner_metabox', 
        'Set Owner', 
        'set_owner_metabox_cb', 
        'portfolio_sets', 
        'normal', 
        'high' 
    );
}

function set_owner_metabox_cb() { ?>
    <?php
        $currentpostid = get_the_ID();
        $author_id = get_post_field ('post_author', $currentpostid);
        $owners = get_users();
    ?>

    <select id="set_owner"></select>
    
    <script>
        jQuery(document).ready(async function($) {
            $("#set_owner").append(`<option value="<?= $author_id ?>"><?= get_the_author_meta('display_name', $author_id) ?></option>`);
            $("#set_owner").val(<?= $author_id ?>).trigger("change");
            $("#set_owner").select2({
                placeholder: "a short description of what this content explains",
                minimumInputLength: 2,
                tags: false,
            });
            let keyupoccured = false;
            $('#set_owner').next().click(function() {
                setTimeout(() => {
                    $(document).on('keypress', 'input[aria-controls="select2-set_owner-results"]',function () {
                        if(keyupoccured == false) {
                            $('#set_owner').append(`
                                <?php foreach($owners as $owner) {
                                    if($owner->ID != $author_id) {
                                        ?>
                                            <option value="<?= $owner->ID ?>"><?= $owner->display_name ?></option>
                                        <?php
                                    }
                                } ?>
                            `);
                        }
                        keyupoccured = true;
                    });
                }, 100);
                $("#set_owner").on("select2:select", function (e) {
                    let selectedOwnerID = $(this).val();
                    $("#post_author").val(selectedOwnerID);
                }); 
            });
        });
    </script>
<?php }

/* Additional Text Box for Portfolio Items
 * ---------------------------------------------------------------------*/

 add_action( 'edit_form_after_editor', 'no_metabox_wspe_114084' );
 add_action( 'save_post', 'save_wpse_114084', 10, 2 );
 
 function no_metabox_wspe_114084()
 {
     global $post;
     if( 'portfolio' != $post->post_type )
         return;
 
     $editor1 = get_post_meta( $post->ID, '_custom_editor_1', true);

     wp_nonce_field( plugin_basename( __FILE__ ), 'wspe_114084' );
     echo '<h2>Additional Description</h2>';
     echo wp_editor( $editor1, 'custom_editor_1', 
     array( 
        'textarea_name' => 'custom_editor_1' ,
        'show_in_rest' => true,
        ) 
    );
 }
 
 function save_wpse_114084( $post_id, $post_object )
 {
     if( !isset( $post_object->post_type ) || 'portfolio' != $post_object->post_type )
         return;
 
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
         return;
 
     if ( !isset( $_POST['wspe_114084'] ) || !wp_verify_nonce( $_POST['wspe_114084'], plugin_basename( __FILE__ ) ) )
         return;
 
     if ( isset( $_POST['custom_editor_1'] )  )
         update_post_meta( $post_id, '_custom_editor_1', $_POST['custom_editor_1'] );
 
 }


/* Preset Cards Meta Box
 * ---------------------------------------------------------------------*/
add_action( 'add_meta_boxes', 'set_presets' );
function set_presets() {
    add_meta_box( 
        'manage-preset-cards', 
        'Preset Cards', 
        'set_presets_cb', 
        'portfolio_sets', 
        'normal', 
        'high' 
    );
}

function set_presets_cb() {
    ?>
        <div class="preset-cards">
            <?php
                $cat_args = array (
                    'taxonomy' => 'portfolio_entries',
            );
            $categories = get_categories ( $cat_args );
            $getpresetvalues = get_post_meta(get_the_ID(), 'selected_presets');
            
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
                        <div class="fc_custom_field">
                            <select
                                multiple="multiple" 
                                selected="selected"
                                id="<?= 'select-' . $category->slug ?>" 
                                class="preset-selection"
                                style="visibility:hidden">
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
                    echo "</select><a class='save-preset hidden' href='javascript:void(0)'>save</a></div>";
                }
                wp_reset_postdata();
            }
            ?>
            <script>
                let selectedPresetRecords = <?= wp_json_encode($getpresetvalues[0]) ?>;
                console.log('wp-encoded', selectedPresetRecords);
                selectedPresetRecords.map((entries) => {
                    jQuery(`#${entries.cat}`).val(entries.ids).trigger("change");
                });
            </script>
            <input type="hidden" name="save_preset_cards" value="">
        </div>
    <?php
}