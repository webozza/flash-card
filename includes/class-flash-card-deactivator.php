<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://webozza.com
 * @since      1.0.0
 *
 * @package    Flash_Card
 * @subpackage Flash_Card/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Flash_Card
 * @subpackage Flash_Card/includes
 * @author     Mohammad <dev@webozza.com>
 */
class Flash_Card_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$allposts= get_posts( array('post_type'=>array('portfolio_sets', 'portfolio_flashcards'),'numberposts'=>-1) );
		foreach ($allposts as $eachpost) {
			// Delete all posts
			wp_delete_post( $eachpost->ID, true, $force_delete = true );
			// Delete options
			// delete_option('fc_rlink');
		}

	}

}
