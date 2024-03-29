<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webozza.com
 * @since      1.0.0
 *
 * @package    Flash_Card
 * @subpackage Flash_Card/admin/partials
 */

?>

<style>
    .flashcard-settings-container a:focus {box-shadow:none !important;}
    .new-role-btn {
        color: #fff;
        outline: none;
        border: none;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 10px 0 !important;
    }
    .new-role-btn {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .new-role-btn span {
        font-size: 1.7em;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flashcard-settings-container .select2-container--default {
        max-width: 200px;
        height: 30px;
        background: #fff;
    }
    .fc-fields-container .inner {
        display: flex;
        gap: 10px;
    }
    .fc-fields-container .select2-container--default .select2-selection--single {
        border: none;
    }
    .fc-fields-container.with-selections {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
</style>

<?php
    $roles_obj = new WP_Roles();
    $roles_names_array = $roles_obj->get_names();
    $getoptions = get_option('fc_rlink');
    $redirectid = $getoptions['duplicate_redirect_id'];
    $redirectlink = $getoptions['duplicate_redirect_link'];
    $rolesselected = $getoptions['roles_selected'];
    $rolesselectedstripped = stripslashes($rolesselected);
    $rolesselectedarray = json_decode($rolesselectedstripped);
    array_filter($rolesselectedarray, fn($value) => !is_null($value) && $value !== '');
?>

<div class="wrap">
    <h2>Settings - Flash Cards Plugin</h2>
    <div class="flashcard-settings-container">
        <h3>Limit creation of sets by user roles</h3>

        <div class="inner">

            
            <?php if($getoptions['roles_selected'] == null || $getoptions['roles_selected'] == "[]" || $getoptions['roles_selected'] == "") { ?>
                <div class="fc-fields-container">
                    <div class="inner">
                        <select multiple class="fc-user-role">
                            <?php foreach($roles_names_array as $role_name) { ?>
                                <option value="<?= $role_name ?>"><?= $role_name ?></option>
                            <?php } ?>
                        </select>
                        <input type="number" placeholder="set limit" value="" />
                        <a class="fc-save-each" style="display:none;" href="javascript:void(0)">Save</a>
                        <input type="hidden" name="selections" value="">
                        <input type="hidden" name="set_limit" value="">
                    </div>
                </div>
            <?php } else { ?>
                <?php foreach($rolesselectedarray as $role) { ?>
                    <div class="fc-fields-container with-selections">
                        <div class="inner">
                            <select multiple class="fc-user-role has-selected">
                                <?php foreach($roles_names_array as $role_name) { ?>
                                    <option value="<?= $role_name ?>"><?= $role_name ?></option>
                                <?php } ?>
                            </select>
                            <input type="number" placeholder="set limit" value="<?= $role->set_limit ?>" />
                            <a class="fc-save-each" style="display:none;" href="javascript:void(0)">Save</a>
                            <input type="hidden" name="set_limit" value="">
                            <input type="hidden" name="selections" value="">
                            <input type="hidden" name="php_role_selections" value="<?= $role->roles ?>">
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
                

            <div class="new-role-btn-wrapper">
                <a class="new-role-btn button-primary" href="javascript:void(0)">
                    <span>+</span>
                </a>
            </div>

            <h3>Select redirection page for duplicate set button on single posts</h3>

            <?php $options = get_option( 'my_settings' ); ?>

            <select id="duplicate_redirect" name="page"> 
                <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option> 
                <?php 
                $pages = get_pages(); 
                foreach ( $pages as $page ) {
                    $option = '<option data-slug="'. $page->post_name .'" data-guid="'. $page->guid .'" value="' . $page->ID . '">'.$page->post_title.'</option>';
                    echo $option;
                }
                ?>
            </select>

            <div class="publish-save" style="margin-top:30px; width:fit-content">
                <a id="save-settings-btn" class="button button-primary button-large" href="javascript:void(0)">Save Settings</a>
                <div style="display:none; padding: 3px;" class="success-msg">Success...</div>
            </div>

        </div>

        <form class="backend-settings-form" style="display:none" action="" method="post">
            <input type="hidden" name="selection_limits" value="">
            <input type="hidden" name="redirect_slug" value="">
            <input type="hidden" name="redirect_id" value="">
            <input type="hidden" name="redirect_link" value="">
            <input type="hidden" name="backend_settings" value="1"/> 
            <button type="submit"></button>
        </form>

    </div>
</div>

<script>
    jQuery(document).ready(async function($) {
        
        $('.fc-user-role').select2();

        let selectInit = () => {
            $('.fc-user-role.new').select2();
            setTimeout(() => {
                $('.fc-user-role.new').slideDown();
            }, 200);
        }
        selectInit();

        let initialSelected = [];

        $('.new-role-btn').click(async function() {

            // Store the initial selections
            $('.with-selections').each(function() {
                let sInitial = $(this).find('select').select2('data');
                sInitial.map(entries => {
                    initialSelected.push(entries.text);
                });
            });

            // Append the new selection
            $(this).parent().prev().after(`
                <div class="fc-fields-container">
                    <div class="inner">
                        <select multiple class="fc-user-role new">
                            <?php foreach($roles_names_array as $role_name) { ?>
                                <option value="<?= $role_name ?>"><?= $role_name ?></option>
                            <?php } ?>
                        </select>
                        <input type="number" placeholder="set limit" value="" />
                        <a class="fc-save-each new" style="display:none;" href="javascript:void(0)">Save</a>
                    </div>
                    <input type="hidden" name="selections" value="">
                    <input type="hidden" name="set_limit" value="">
                </div>
            `);

            // Filter out the already selected
            $('.fc-user-role.new option').each(function() {
            let getOptions = $(this).val();
                if($.inArray(getOptions, initialSelected) > -1) {
                    $(this).remove();
                } else {
                    // do nothing for now...
                }
            })

            selectInit();
            getNewSelection();
            handleSelectionChange();
            handleSetLimitChange();
        });



        // Initial Select Box
        let getSelections = async () => {
            $('.fc-save-each').click(function() {
                let selections = $(this).parent().find('select.fc-user-role').select2('data');
                let setLimit = $(this).parent().find('input[type="number"]').val();
                selections.map(entries => {
                    let fetchUsers = async () => {
                        const url = `/wp-json/wp/v2/users?roles=${entries.id}`;
                        let res = await fetch(url, {
                            method: "GET",
                            headers: {
                                'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                                'Content-Type': 'application/json',
                            },
                        });
                        return await res.json();
                    }
                    let renderUsers = async () => {
                        let userData = await fetchUsers();
                        userData.map(users => {
                            let fetchSetLimit = async () => {
                                const url = `/wp-json/wp/v2/users/${users.id}`;
                                let res = await fetch(url, {
                                    method: "POST",
                                    headers: {
                                        'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        meta: {
                                            set_creation_limit: setLimit
                                        }
                                    })
                                });
                            }
                            let pushSetLimit = async () => {
                                let checkPush = await fetchSetLimit();
                            }
                            pushSetLimit();
                        });
                    }
                    renderUsers();
                });
            });
        }
        getSelections();

        // New Select box
        let getNewSelection = async () => {
            $('.flashcard-settings-container > .inner > div:not(.new-role-btn-wrapper):not(.publish-save)').eq(-1).find('.fc-save-each.new').click(function () {
                let selections = $(this).parent().find('select.fc-user-role').select2('data');
                let setLimit = $(this).parent().find('input[type="number"]').val();
                selections.map(entries => {
                    let fetchUsers = async () => {
                        const url = `/wp-json/wp/v2/users?roles=${entries.id}`;
                        let res = await fetch(url, {
                            method: "GET",
                            headers: {
                                'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                                'Content-Type': 'application/json',
                            },
                        });
                        return await res.json();
                    }
                    let renderUsers = async () => {
                        let userData = await fetchUsers();
                        userData.map(users => {
                            let fetchSetLimit = async () => {
                                const url = `/wp-json/wp/v2/users/${users.id}`;
                                let res = await fetch(url, {
                                    method: "POST",
                                    headers: {
                                        'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        meta: {
                                            set_creation_limit: setLimit
                                        }
                                    })
                                });
                            }
                            let pushSetLimit = async () => {
                                let checkPush = await fetchSetLimit();
                            }
                            pushSetLimit();
                        });
                    }
                    renderUsers();
                });
            });
        }

            // Check endpoint
            // let checkEndPoint = async () => {
            //     const url = `/wp-json/wp/v2/flashcard`;
            //     let res = await fetch(url, {
            //         method: "GET",
            //         headers: {
            //             'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
            //         }
            //     });
            //     return await res.json();
            // }

            // let renderEndPoint = async () => {
            //     let checkData = await checkEndPoint();
            // }
            // renderEndPoint();

            // Post Redirect Link
            var _fcsettings = {
                duplicate_redirect_link: "",
            }

            let fetchSettings = async () => {
                const url = `/wp-json/wp/v2/flashcard`;
                let res = await fetch(url, {
                    method: "POST",
                    headers: {
                        'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(_fcsettings)
                });
                return await res.json();
            }

            let postSettings = async () => {
                let flashcardSettings = await fetchSettings();
            }

            $('#duplicate_redirect').select2();
            $('#duplicate_redirect').val('<?= $redirectid ?>').trigger('change')

            $('#save-settings-btn').click(async function() {
                
                $('.success-msg').fadeIn('slow').delay('1500').fadeOut();
                $('.fc-save-each').trigger('click');
                _fcsettings.duplicate_redirect_link = $('#duplicate_redirect :selected').data('guid');
                _fcsettings.duplicate_redirect_id = $('#duplicate_redirect :selected').val();
                _fcsettings.duplicate_redirect_slug = $('#duplicate_redirect :selected').data('slug');
                await postSettings();
                
            });

            // Deleting selections
            $('.fc-fields-container.with-selections select').change(function() {
                let fCount = $('.fc-fields-container.with-selections select').length;
                let sCount = $(this).select2('data').length;
                if(sCount == 0 && fCount > 1) {
                    $(this).select2('destroy');
                    $(this).parent().parent().remove();
                }
            });

            $('.select2-selection__choice__remove').click(function() {
                var sRemoved = $(this).parent().text();
                var sRemoved = sRemoved.slice(1, sRemoved.length);
                console.log(sRemoved);

                let updateRole = async () => {
                    const url = `/wp-json/wp/v2/users?roles=${sRemoved}`;
                    let res = await fetch(url, {
                        method: "GET",
                        headers: {
                            'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                            'Content-Type': 'application/json',
                        },
                    });
                    return await res.json();
                }
                let renderRole = async () => {
                    let updateRoles = await updateRole();
                    updateRoles.map(entries => {
                        let updateEntries = async () => {
                            const url = `/wp-json/wp/v2/users/${entries.id}`;
                            let res = await fetch(url, {
                                method: "POST",
                                headers: {
                                    'X-WP-Nonce': '<?= wp_create_nonce("wp_rest") ?>',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    meta: {
                                        set_creation_limit: ""
                                    }
                                })
                            });
                            return await res.json();
                        }
                        let runUpdate = async () => {
                            let logUpdate = updateEntries();
                            console.log(logUpdate);
                        }
                        runUpdate();
                    });
                }
                renderRole();

            });

            // Set the final selection
            $('.has-selected').each(function() {
                var selected__roles = $(this).parent().find('[name="php_role_selections"]').val();
                var selected__roles = selected__roles.split(',');
                $(this).val(selected__roles).trigger('change');
            });

            // Push the variables once on load
            $('.fc-fields-container select').each(function() {
                let selections = $(this).find(':selected').map(function() {
                    return $(this).text();
                }).get().join(',');
                $(this).parent().parent().find('input[name="selections"]').val(selections);
            });
            $('.fc-fields-container input[type="number"]').each(function() {
                let setLimitVal = $(this).val();
                $(this).parent().find('input[name="set_limit"]').val(setLimitVal);
            });

            $('.fc-fields-container select').change(function() {
                let selections = $(this).find(':selected').map(function() {
                    return $(this).text();
                }).get().join(',');
                $(this).parent().parent().find('input[name="selections"]').val(selections);
            });

            $('.fc-fields-container input[type="number"]').change(function() {
                let setLimitVal = $(this).val();
                $(this).parent().parent().find('input[name="set_limit"]').val(setLimitVal);
            });

            // Handles change on selection of roles for newly added selection
            let handleSelectionChange = () => {
                $('.fc-fields-container').eq(-1).find('select').change(function() {
                    let selections = $(this).find(':selected').map(function() {
                        return $(this).text();
                    }).get().join(',');
                    $(this).parent().parent().find('input[name="selections"]').val(selections);
                });
            }
            
            // Handles change on set limit by roles for newly added selection
            let handleSetLimitChange = () => {
                $('.fc-fields-container').eq(-1).find('input[type="number"]').change(function() {
                    let setLimitVal = $(this).val();
                    $(this).parent().parent().find('input[name="set_limit"]').val(setLimitVal);
                });
            }

            // Remove if no roles selected on save
            let removeIfNoRoles = async () => {
                $('.fc-fields-container').each(function() {
                    let selectFields = $(this).find('select');
                    let checkRolesSelected = selectFields.find(':selected').val();
                    if(checkRolesSelected == undefined) {
                        selectFields.parent().parent().remove();
                    }
                });
            } 

            // Push the final selection values to form for retreiving it on server
            let pushFinalToDB = [];
            $('.flashcard-settings-container .publish-save').click(async function() {
                await removeIfNoRoles();
                let redirectLink = $('select#duplicate_redirect').find(':selected').data('guid');
                let redirectId = $('select#duplicate_redirect').find(':selected').val();
                let redirectSlug = $('select#duplicate_redirect').find(':selected').data('slug');
                pushFinalToDB = [];
                $('.fc-fields-container').map(async function() {
                    let eachSet = $(this);
                    let finalSelections = eachSet.find('input[name="selections"]').val();
                    let finalLimits = eachSet.find('input[name="set_limit"]').val();
                    pushFinalToDB.push({
                        roles: finalSelections,
                        set_limit: finalLimits
                    });
                    console.log('pushToFinalDB ->', pushFinalToDB);
                });
                $('.backend-settings-form input[name="selection_limits"]').val(JSON.stringify(pushFinalToDB));
                $('.backend-settings-form input[name="redirect_link"]').val(redirectLink);
                $('.backend-settings-form input[name="redirect_id"]').val(redirectId);
                $('.backend-settings-form input[name="redirect_slug"]').val(redirectSlug);
                $('.backend-settings-form').submit();
            });


            // Validates and removes empty containers
            $('.flashcard-settings-container .inner .fc-fields-container').each(function() {
                if($(this).html().length == 46) {
                    $(this).remove();
                }
            });



        });
</script>