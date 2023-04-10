<?php

/**
 * Post Author Column Class
 */
class Post_Author_Column
{

    /**
     * Load actions for creating custom column.
     *
     * @return void
     */
    function init()
    {
        // Set column heading.
        add_filter('manage_portfolio_sets_posts_columns', [$this, 'set_author_title'], 10);

        // Set column heading content.
        add_action('manage_portfolio_sets_posts_custom_column', [$this, 'set_author_title_content'], 10, 2);
    }

    /**
     * Display column heading.
     *
     * @param array $defaults
     * @return void
     */
    function set_author_title($defaults)
    {
        $defaults['author_name'] = 'Author';
        return $defaults;
    }

    /**
     * Display author name.
     *
     * @param string $column_name
     * @param int $post_ID
     * @return void
     */
    function set_author_title_content($column_name, $post_ID)
    {
        if ($column_name == 'author_name') {
            $author_id = get_post_field('post_author', $post_ID);
            echo get_the_author_meta('display_name', $author_id);
        }
    }
}

/**
 * Initialize the class.
 */
(new Post_Author_Column())->init();
