<?php
/**
 * Autoload PHP classes.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\ForceAddToCart
 */

namespace WPGeeks\Plugin\ForceAddToCart;

spl_autoload_register(
	function ( $class_name ) {
		$path = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'classes';
		$file = strtolower( str_replace( 'WPGeeks\\Plugin\\ForceAddToCart\\', '', $class_name ) );

		// Class paths and name.
		$file  = str_replace( '_', '-', $file );
		$parts = explode( '\\', $file );

		foreach ( $parts as $index => $part ) {
			if ( count( $parts ) - 1 === $index ) {
				$type = 'class';

				if ( preg_match( '/traits/i', $class_name ) ) {
					$type = 'trait';
				}

				$part = sprintf( '%s-%s.php', $type, $part );
			}

			$path .= sprintf( '%s%s', DIRECTORY_SEPARATOR, $part );
		}

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);
