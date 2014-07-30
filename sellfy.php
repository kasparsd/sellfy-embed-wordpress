<?php
/*
	Plugin Name: Sellfy for WordPress
	Plugin URI: http://kaspars.net
	Description: Converts Sellfy product and store links into appropriate embed codes.
	Author: Kaspars Dambis
	Version: 1.2.1
	Author URI: http://kaspars.net
	Text Domain: sellfy-embed
	Domain Path: /lang
*/

// Disable direct calls
if ( ! defined( 'ABSPATH' ) )
	die;


// Go!
SellfyEmbed::instance();


class SellfyEmbed {

	public static $instance;


	private function __construct() {

		add_action( 'plugins_loaded', array( $this, 'init' ) );

	}


	public static function instance() {

		if ( ! self::$instance )
			self::$instance = new self();

		return self::$instance;

	}


	function init() {

		// Parse URLs such as https://sellfy.com/p/00xx/
		wp_embed_register_handler(
			'sellfy-buynow',
			'#https?://(?:www\.)?sellfy.com/p/([a-zA-Z0-9]+)/?#i',
			array( $this, 'sellfy_buy_now_handler' )
		);

		wp_embed_register_handler(
			'sellfy-store',
			'#https?://(?:www\.)?sellfy.com/([a-zA-Z0-9]+)/?#i',
			array( $this, 'sellfy_store_handler' )
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_sellfy_scripts' ) );

	}


	function enqueue_sellfy_scripts() {
		
		// Register Sellfy API JS
		wp_register_script( 'sellfy-button-js', 'https://sellfy.com/js/api_buttons.js', null, null, true );

		// Register Sellfy Store JS
		wp_register_script( 'sellfy-store-js', 'https://s3.amazonaws.com/sellfy_cdn/media/js/embed.js', null, null, true );

	}


	function sellfy_buy_now_handler( $matched, $attr, $url, $raw_attr ) {

		// Make sure we found a matching product ID
		if ( ! isset( $matched[1] ) )
			return;

		// Enqueue JS for the button in the footer
		wp_enqueue_script( 'sellfy-button-js' );

		// Return the embed code
		return sprintf(
				'<a href="https://sellfy.com/p/%1$s/" id="%1$s" class="sellfy-buy-button">%s</a>',
				esc_attr( $matched[1] ),
				__( 'Buy', 'sellfy-embed' )
			);

	}


	function sellfy_store_handler( $matched, $attr, $url, $raw_attr ) {

		// Make sure we found a matching Sellfy username
		if ( ! isset( $matched[1] ) )
			return;

		// Enqueue JS for the store in the footer
		wp_enqueue_script( 'sellfy-store-js' );

		// Return the embed code
		return sprintf(
				'<iframe src="https://sellfy.com/embed/profile/%s" scrolling="no" width="100%%" style="border:none;"></iframe>',
				esc_attr( trim( $matched[1] ) )
			);

	}


}

