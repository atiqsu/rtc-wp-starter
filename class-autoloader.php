<?php
/**
 * File for autoloading the plugin classes.
 *
 * @created 2025-04-09
 *
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp
 */

namespace rtCamp;

defined( 'ABSPATH' ) || exit;

/**
 * PHP class autoloader
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Autoloading all php class according to our rule/convention.
	 *
	 * PSR4 implementation will be added later
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function run() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload.
	 *
	 * @param string $cls - actual class name.
	 * @since 1.0.0
	 * @access private
	 */
	private static function autoload( $cls ) {

		if ( 0 !== strpos( $cls, __NAMESPACE__ ) ) {
			return;
		}

		$nms     = self::get_plg_nms();
		$pattern = '/\b' . preg_quote( $nms, '/' ) . '\\\/';
		$fl_nm   = preg_replace( $pattern, '', $cls );
		$fl_nm   = preg_replace( '/\\\/', DIRECTORY_SEPARATOR, $fl_nm );

		/**
		 * Preparig dash convention.
		 */
		$fl_nm = preg_replace( '/([a-z])([A-Z])/', '$1-$2', $fl_nm ); // when like AuthorContent -> Author-Content.
		$fl_nm = str_replace( '_', '-', $fl_nm ); // when like Author_Content -> Author-Content.

		$fl_nm = plugin_dir_path( __FILE__ ) . 'class-' . $fl_nm . '.php';
		$fl_nm = strtolower( $fl_nm );

		if ( file_exists( $fl_nm ) ) {
			require_once $fl_nm;
		}
	}

	/**
	 * Get the plugin namespace.
	 *
	 * @return string
	 */
	public static function get_plg_nms(): string {
		return __NAMESPACE__;
	}
}

Autoloader::run();
