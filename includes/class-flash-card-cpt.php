<?php

/**
 * Fired during plugin activation.
 *
 * This function declares the portfolio_flashcards post type (cpt)
 *
 * @since      1.0.0
 * @package    Flash_Card
 * @subpackage Flash_Card/includes
 * @author     Mohammad <dev@webozza.com>
 */


// Custom Cards from Sets
function cpt_flashcards() {
    register_post_type( 'portfolio_flashcards',
        array(
            'labels' => array(
                'name' => __( 'Flashcards' ),
                'singular_name' => __( 'Flashcard' ),
                'all_items' => __( 'Custom Cards' ),
                'add_new_item' => __( 'Add New Card' ),
                'edit_item' => __( 'Edit Card' ),
            ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'has_archive' => false,
        'rewrite'   => array( 'slug' => 'flashcard' ),
        'menu_position' => 4,
        'menu_icon' => 'dashicons-list-view',
        )
    );
}
add_action( 'init', 'cpt_flashcards' );

// Portfolio Sets
function cpt_sets() {
    $getoptions = get_option('fc_rlink');
    $redirectslug = $getoptions['duplicate_redirect_slug'];
    register_post_type( 'portfolio_sets',
        array(
        'labels' => array(
            'name' => __( 'Sets' ),
            'singular_name' => __( 'Set' ),
            'all_items' => __( 'Sets' ),
            'add_new_item' => __( 'Add New Set' ),
            'edit_item' => __( 'Edit Set' ),
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_menu' => false,
        'rewrite' => array(
            'slug' => $redirectslug,
            'with_front' => false,
        ),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-list-view',
        )
    );
    global $wp_rewrite;
    $wp_rewrite->extra_permastructs['portfolio_sets']['struct'] =
        ''.$redirectslug.'/%post_id%/%portfolio_sets%';
}
add_action( 'init', 'cpt_sets' );


add_filter(
    'post_type_link',
    function ($post_link, $post) {
        if ($post && 'portfolio_sets' === $post->post_type) {
            return str_replace('%post_id%', $post->ID, $post_link);
        }
        return $post_link;
    },
    10,
    2
);

/* Register User Meta
-------------------------------------------------------*/
register_meta('user', 'set_creation_limit', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

function get_user_roles($object, $field_name, $request) {
    return get_userdata($object['id'])->roles;
}

add_action('rest_api_init', function() {
    register_rest_field('user', 'roles', array(
        'get_callback' => 'get_user_roles',
        'update_callback' => null,
        'schema' => array(
        'type' => 'array'
        )
    ));
});

/* Register Parent Sets Meta
 * ---------------------------------------------------------------------*/
register_post_meta(
    'portfolio_flashcards',
    'parent_sets',
    array(
        'show_in_rest' => true,
    )
);

/* Register Featured Post Meta
 * ---------------------------------------------------------------------*/
register_post_meta(
    'portfolio_sets',
    'featured_set',
    array(
        'default' => 'false',
        'show_in_rest' => true,
    )
);

/* Register Custom Editor for Portfolio CPT
 * ---------------------------------------------------------------------*/
register_post_meta(
    'portfolio',
    '_custom_editor_1',
    array(
        'single' => true,
        'type' => 'string',
        'show_in_rest' => true,
    ),
);

/* Register Selected Presets as Post Meta
 * ---------------------------------------------------------------------*/
register_post_meta(
    'portfolio_sets',
    'selected_presets',
    array(
        'single'       => true,
        'type'         => 'array',
        'show_in_rest' => array(
            'schema' => array(
                'items' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'cat'    => array(
                            'type' => 'string',
                        ),
                        'ids' => array(
                            'type'   => 'array',
                        ),
                    ),
                ),
            ),
        ),
    )
);

