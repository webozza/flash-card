<?php

/**
 * Checks the preset ids rendered by another plugin that did not render it well with ids and structure...
 *
 *
 * @link       https://webozza.com
 * @since      1.0.0
 *
 * @package    Flash_Card
 * @subpackage Flash_Card/public/partials
 */

?>

<!-- Vars 
--------------------------------------------------------------->
<?php
    $post_id = get_the_ID();
    $custom_field_keys = get_post_custom_keys();

    // Get Options
    $getoptions = get_option('fc_rlink');
    $redirectlink = $getoptions['duplicate_redirect_link'];
?>

<script>
    let _dups = {
        title: "<?= the_title() ?>",
        meta: {
            selected_presets: [
                <?php 
                    foreach ($custom_field_keys as $key => $value) {
                        if(strpos($value, '_attached_posts') == true) {
                            $presetids = get_post_meta($post_id, $value);
                            ?>
                                {cat: "<?= 'select-' . str_replace('_attached_posts', '', $value) ?>", ids: [<?php foreach($presetids as $presetid) {  foreach($presetid as $id) { ?> "<?= $id ?>", <?php } } ?>]},
                            <?php
                        }
                    }
                ?>
            ],
        },
        status: "publish",
    };

    addEventListener("load", (event) => {
        jQuery(document).ready(function ($) {
        /* START
        ------------------------------------------------------------*/

        /* Conditions
        ------------------------------------------------------------*/
        let hasPresets = $("._items_list a").length > 0;

        /* Append the Duplicate Button
        ------------------------------------------------------------*/
        let addDuplicateBtn = () => {
            $("._items_list:last-of-type").after(`
                <form class="duplicate-preset-form" style="display:none" action="" method="post">
                    <input type="hidden" name="dup_preset_id" value="<?= get_the_ID() ?>">
                    <input type="hidden" name="duplicate_presets" value="1"> 
                    <button type="submit"></button>
                </form>
                <a id="duplicatePresets" href="javascript:void(0)" class="button primary" style="margin-top: 20px;">
                    Duplicate Presets
                </a>
            `);
        };


        /* Initiations
        ------------------------------------------------------------*/
        if (hasPresets) {
            addDuplicateBtn();
            $("#duplicatePresets").click(function() {
                $(this).prev().submit();
            });
        }

        /* END
        ------------------------------------------------------------*/
        $('body').attr('style', 'background:#fff');
        });
    });

</script>

