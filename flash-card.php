<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://webozza.com
 * @since             1.0.0
 * @package           Flash_Card
 *
 * @wordpress-plugin
 * Plugin Name:       Flash Card
 * Plugin URI:        https://github.com/webozza/wp-plugins/flashcard
 * Description:       Custom flash card plugin developed by @webozza
 * Version:           29.1.5
 * Author:            Webozza
 * Author URI:        https://webozza.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       flash-card
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FLASH_CARD_VERSION', '29.1.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-flash-card-activator.php
 */
function activate_flash_card() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flash-card-activator.php';
	Flash_Card_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-flash-card-deactivator.php
 */
function deactivate_flash_card() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flash-card-deactivator.php';
	Flash_Card_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_flash_card' );
register_deactivation_hook( __FILE__, 'deactivate_flash_card' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-flash-card.php';

/**
 * The core portfolio_flashcards cpt is being declared
 * @webozza
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-flash-card-cpt.php';

/**
 * The duplicate set function resides here
 * @webozza
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/duplicate-set.php';

/**
 * The delete set function resides here
 * @webozza
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/delete-set.php';

/**
 * The create set function resides here
 * @webozza
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/create-set.php';

/**
 * The edit set function resides here
 * @webozza
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/edit-set.php';

/**
 * Add the settings page
 * @webozza
 */
function flashcard_register_settings() {
    //Add Settings Sub Menu 
    add_submenu_page('edit.php?post_type=portfolio_flashcards', 'flashcard settings', 'Settings', "manage_options", 'flashcard_settings', 'flashcardsettings');

	$fcsettings = array(
		'fc_redirect_link' => '',
		'fc_redirect_id' => '',
		'fc_redirect_slug' => '',
	);
	add_option('fc_rlink', $fcsettings);
}
add_action('admin_menu', 'flashcard_register_settings');

function flashcardsettings(){
    require plugin_dir_path( __FILE__ ) . 'admin/partials/settings.php';
}

/**
 * Creates the frontend shortcode
 * @webozza
 */
function flashcard_area() {
	if ( is_user_logged_in() ) {
		ob_start();
		require plugin_dir_path( __FILE__ ) . 'public/partials/flashcard-area.php';
		require plugin_dir_path( __FILE__ ) . 'public/partials/flashcard-newset.php';
		return ob_get_clean();   
	} else {
		ob_start();
		require plugin_dir_path( __FILE__ ) . 'public/partials/flashcard-loggedout.php';
		require plugin_dir_path( __FILE__ ) . 'public/partials/single-portfolio_sets.php';
		return ob_get_clean();
	}
 } 
 add_shortcode( 'flashcard-area', 'flashcard_area' );

 /**
 * Creates the frontend view shortcode
 * @webozza
 */
function flashcard($atts) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'public/partials/set-param.php';
	return ob_get_clean();
 } 
 add_shortcode( 'flashcard', 'flashcard' );

 /* flashcard with parameter for id shortcode */
function flashcard_shortcode($atts = array(), $content = null, $tag = 'flashcard') {
    extract(shortcode_atts(array(
        'sets' => 'sets'
    ), $atts));

  /* Call to template */
    ob_start();
    include( plugin_dir_path( __FILE__ ) . 'public/partials/set-param.php' );
    return ob_get_clean();
}
add_shortcode('flashcard', 'flashcard_shortcode');

/**
 * Enqueue scripts and styles for cpt portfolio_sets
 * @webozza
 */
function run_plugin_scripts() {
	if( is_singular('portfolio_sets') || shortcode_exists( 'flashcard' ) ) {
		$public_dir = '/wp-content/plugins/flash-card/public/';
		$admin_dir = '/wp-content/plugins/flash-card/admin/';

		/* Stylesheets */
		wp_enqueue_style('select2', $public_dir . 'css/select2.min.css' );
		wp_enqueue_style('swiper', $public_dir . 'css/swiper.min.css' );
		wp_enqueue_style('flashcard-slider', $public_dir . 'css/fc-slider.css' );
		wp_enqueue_style('cpfp',  $public_dir . 'css/cpfp.css' );

		/* Scripts */
		wp_enqueue_script('swiper', $public_dir . 'js/swiper.min.js', array('jquery') );
		wp_enqueue_script('select2', $public_dir . 'js/select2.min.js', array('jquery') );
		if ( is_singular('portfolio_sets') ) {
			wp_enqueue_script('single-sets', $public_dir . 'js/single-sets.js', array('jquery') );
		};
	}
}
add_action('wp_enqueue_scripts', 'run_plugin_scripts');

/**
 * Enqueue duplicate set function for single posts
 * @webozza
 */
function single_scripts() {
	// Check Limit

	if (is_user_logged_in()) {
		$setownerid = get_current_user_id();
		$usersetlimit = get_user_meta($setownerid, 'set_creation_limit')[0];
		$usersetcount = count_user_posts($setownerid, 'portfolio_sets')[0];

		if ( is_singular('post') && ($usersetcount < $usersetlimit || $usersetlimit == "") ) {
			$public_dir = '/wp-content/plugins/flash-card/public/';
			require_once( 'public/partials/duplicate-post-presets.php');
		}
	}
}
add_action('wp_enqueue_scripts', 'single_scripts');

/* Handles the appended stuff in the url for duplicate titles
 * ---------------------------------------------------------------------*/
function myplugin_update_slug( $data, $postarr ) {
    if ( ! in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) && $data['post_type'] == "portfolio_sets" ) {
        $data['post_name'] = sanitize_title( $data['post_title'] );
    }
    return $data;
}
add_filter( 'wp_insert_post_data', 'myplugin_update_slug', 92, 2 );

/* Assign template for portfolio_sets
 * ---------------------------------------------------------------------*/
add_filter('single_template', 'override_single_template');
function override_single_template($single_template)
{
    global $post;

    if ($post->post_type == 'portfolio_sets') {
        $file = plugin_dir_path( __FILE__ ) . '/public/partials/single-' . $post->post_type . '.php';

        if (file_exists($file)) {
            $single_template = $file;
        }
    }

    return $single_template;
}

/* Register Custom Endpoint for Flashcard Settings
 * ---------------------------------------------------------------------*/
// function fc_response($request){
//     $args = array(
// 		'duplicate_redirect_link' => $request['duplicate_redirect_link'],
// 		'duplicate_redirect_id' => $request['duplicate_redirect_id'],
// 		'duplicate_redirect_slug' => $request['duplicate_redirect_slug'],
// 	);

// 	if($request['duplicate_redirect_link'] !== null) {
// 		update_option('fc_rlink', array(
// 			'duplicate_redirect_link' => $request['duplicate_redirect_link'],
// 			'duplicate_redirect_id' => $request['duplicate_redirect_id'],
// 			'duplicate_redirect_slug' => $request['duplicate_redirect_slug']
// 		));
// 	}

// 	$response = new WP_REST_Response($args);
//     $response->set_headers([ 'Cache-Control' => 'no-cache' ]);

//     return $response;
// }

add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', '/flashcard', array(
			'methods'  => array('GET', 'POST', WP_REST_Server::EDITABLE),
			'callback' => 'fc_response',
			'args' => [
				'plugin_settings' => array(
					'required' => false,
				)
			],
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		));
	}
);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_flash_card() {

	$plugin = new Flash_Card();
	$plugin->run();

	if (is_admin()) {
        require_once ('classes/sets_custom_fields.php');
		require('includes/backend.php');
		// throw in the nonce value to all sets edit page
		add_filter( 'views_edit-portfolio_sets', function($views){
			$flashcardNonce = wp_create_nonce( 'wp_rest' );
			echo "<script>var flashcardSettings = {nonce: `". $flashcardNonce ."`}</script>";
			return $views;
		});

    } else {
		require_once ('classes/frontend_media.php');
	}

}
run_flash_card();





