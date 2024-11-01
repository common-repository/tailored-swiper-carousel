<?php
/**
 * Plugin Name: Tailored Swiper Carousel
 * Description: Uses ACF Pro and Swiper to create a Gutenberg image carousel block.  Great for creating big hero banners.  You'll need your own copy of ACF Pro, not included in this plugin.
 * Version:     1.2.1
 * Author:      Tailored Media
 * Author URI:  https://www.tailoredmedia.com.au
 *
 * @package TailoredSwiperCarousel
 */

/**
 * Tailored Swiper Carousel
 */
class Tailored_Swiper_Carousel {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_register_scripts' ), 1 );
		add_action( 'admin_notices', array( $this, 'admin_notice_dependencies' ) );
		// Local JSON for Advanced Custom Fields.
		add_filter( 'acf/settings/load_json', array( $this, 'acf_json_load_point' ) );
		// Blocks.
		include 'blocks/banner-carousel/carousel.php';
	}

	/**
	 * Register scripts for later enqueue
	 *
	 * @return void
	 */
	public function wp_register_scripts() {
		wp_register_script( 'swiper', plugins_url( 'swiper/swiper-bundle.min.js', __FILE__ ), array(), '6.3.5', true );
		wp_register_style( 'swiper', plugins_url( 'swiper/swiper-bundle.min.css', __FILE__ ), array(), '6.3.5' );
	}

	/**
	 * Blocks require ACF Pro.  Show an admin alert if we don't have it.
	 *
	 * @return void
	 */
	public function admin_notice_dependencies() {
		if ( ! class_exists( 'acf_pro' ) ) {
			echo '<div class="notice notice-warning"><p>The Swiper Carousel plugin requires that ACF Pro plugin be active.  It won\'t work without it.</p></div>';
		}
	}

	/**
	 * Register ACF JSON location to import, for easier management of fields
	 *
	 * @param array $paths Array of paths ACF will look for JSON files.
	 * @return array
	 */
	public function acf_json_load_point( $paths ) {
		$paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';
		return $paths;
	}

}

/**
 * Load plugin class
 */
new Tailored_Swiper_Carousel();

