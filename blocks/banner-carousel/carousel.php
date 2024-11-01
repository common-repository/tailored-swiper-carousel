<?php
/**
 * Tailored Swiper Carousel: Gutenberg Block
 * Carousel Block
 *
 * @package TailoredSwiperCarousel
 * @subpackage Block
 */

new Tailored_ACF_Swiper_Banner_Carousel();

/**
 * Gutenberg Block for the Swiper Carousel
 */
class Tailored_ACF_Swiper_Banner_Carousel {
	/**
	 * Image Size
	 *
	 * @var string
	 */
	public $image_size = 'carousel_banner';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'init', array( $this, 'register_block' ) );

		add_action(
			'plugins_loaded',
			function() {
				$width  = apply_filters( 'tailored_carousel_banner_width', 2000 );
				$height = apply_filters( 'tailored_carousel_banner_height', 1200 );
				add_image_size( $this->image_size, $width, $height, false );
			}
		);
	}

	/**
	 * Register scripts & styles for later use
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		wp_register_script( 'tailored-banner-carousel', plugins_url( 'carousel.js', __FILE__ ), array( 'jquery', 'swiper' ), '1.1', true );
		wp_register_style( 'tailored-banner-carousel', plugins_url( 'carousel.css', __FILE__ ), array( 'swiper' ), '1.0' );
	}

	/**
	 * Enqueue scripts & styles now
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		// Not used, as we enqueue via registration & callback.
	}


	/**
	 * Render the block
	 *
	 * @param array $block Gutenberg block args.
	 * @return bool
	 */
	public function block_render_callback( $block = array() ) {
		// Check ACF installed.
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		// Add CSS & JS.
		wp_enqueue_style( 'tailored-banner-carousel' );
		if ( ! is_admin() ) {
			$options = get_field( 'options' );
			if ( ! is_array( $options ) ) {
				$options = array(
					'images_per_view' => 1,
					'space_between'   => 0,
				);
			}
			wp_localize_script(
				'tailored-banner-carousel',
				'tailored_banner_carousel',
				array(
					'per_view'      => $options['images_per_view'],
					'space_between' => $options['space_between'],

				)
			);
			wp_enqueue_script( 'tailored-banner-carousel' );
		}

		if ( ! isset( $block['className'] ) ) {
			$block['className'] = '';
		}
		if ( $block['align'] ) {
			$block['className'] .= ' align' . $block['align'];
		}

		// Load Slides.
		$banners = get_field( 'banners' );
		if ( ! $banners || empty( $banners ) ) {
			return false;
		}

		echo '<div class="wp-block wp-block-banner-carousel ' . esc_attr( $block['className'] ) . '">' . PHP_EOL;
		echo '<div class="swiper-container">' . PHP_EOL;
		echo '<div class="swiper-wrapper">' . PHP_EOL;
		foreach ( $banners as $banner ) {
			$image = $banner['image'];
			if ( ! $image ) {
				continue;
			}

			// Load.
			$image = wp_get_attachment_image( $image['ID'], $this->image_size, false, array() );

			$caption = apply_filters( 'the_content', trim( $banner['caption'] ) );

			// If a link, make the image a link.
			if ( $banner['link'] && $banner['link']['url'] ) {
				$image = sprintf(
					'<a href="%2$s" target="%3$s">%1$s</a>',
					$image,
					esc_attr( $banner['link']['url'] ),
					esc_attr( $banner['link']['target'] ),
				);
			}

			// Output.
			echo '<div class="swiper-slide">';
			echo '<div class="image">' . $image . '</div>';
			if ( $caption ) {
				echo '<div class="caption">' . $caption . '</div>' . PHP_EOL;
			}
			echo '</div><!-- swiper-slide -->' . PHP_EOL;
		}
		echo '</div><!-- swiper-wrapper -->' . PHP_EOL;

		// If we need pagination.
		echo '<div class="swiper-pagination"></div>' . PHP_EOL;

		// If we need navigation buttons.
		echo '<div class="swiper-button-prev"></div>' . PHP_EOL;
		echo '<div class="swiper-button-next"></div>' . PHP_EOL;

		echo '</div><!-- swiper-container -->' . PHP_EOL;
		echo '</div><!-- wp-block-carousel-offers -->' . PHP_EOL;
		return true;
	}

	/**
	 *  Gutenberg & ACF Pro
	 */
	public function register_block() {
		// Check ACF installed.
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return false;
		}

		// Register our block.
		acf_register_block_type(
			array(
				'name'            => 'tailored-banner-carousel',
				'title'           => __( 'Banner Carousel' ),
				'description'     => __( 'Banner carousel using Swiper JS.' ),
				'render_callback' => array( $this, 'block_render_callback' ),
				'category'        => 'embed',
				'icon'            => 'format-gallery',
				'keywords'        => array( 'tailored', 'image', 'banner', 'carousel', 'swiper', 'slideshow' ),
				'mode'            => 'auto',
			)
		);
	}
}
