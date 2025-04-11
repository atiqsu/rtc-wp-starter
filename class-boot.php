<?php
/**
 * File for plugin bootstrapping.
 *
 * @created 2025-04-09
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp
 */

namespace rtCamp;

use rtCamp\Contributor;
use rtCamp\Metabox;
use rtCamp\Author_Archive;

/**
 * Class Boot.
 */
class Boot {

	/**
	 * The path to the plugin.
	 *
	 * @var string
	 */
	protected string $path;

	/**
	 * Constructor.
	 *
	 * @param string $path The path to the plugin.
	 */
	public function __construct( string $path ) {

		$this->path = $path;

		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );

		( new Metabox() )->init();
		( new Contributor() )->init();
		( new Author_Archive() )->init();
	}

	/**
	 * Load the plugin text domain.
	 */
	public function i18n(): void {
		load_plugin_textdomain(
			'rtc-wp-assignment',
			false,
			plugin_dir_path( plugin_basename( $this->path ) ) . '/languages/'
		);
	}

	/**
	 * Enqueue the frontend assets.
	 */
	public function frontend_assets() {
		wp_enqueue_style( 'rtc-front-style', plugins_url( 'assets/style.css', $this->path ), array(), '1.0.0' );
	}
}
