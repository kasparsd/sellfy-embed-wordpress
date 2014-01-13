<?php
/*
	Plugin Name: Sellfy for WordPress
	Plugin URI: http://kaspars.net
	Description: Convert all Sellfy product links into "Buy Now" buttons
	Author: Kaspars Dambis
	Version: 1.1
	Author URI: http://kaspars.net
	Text Domain: sellfy-embed
	Domain Path: /lang
*/

// Disable direct calls
if ( ! defined( 'ABSPATH' ) )
	die;


add_action( 'plugins_loaded', array( 'SellfyEmbed', 'init' ) );


class SellfyEmbed {


	function init() {

		// Parse URLs such as https://sellfy.com/p/00xx/
		wp_embed_register_handler(
			'sellfy-buynow',
			'#https?:\/\/(?:www\.)?sellfy.com\/p\/([a-zA-Z0-9]+)\/?#i',
			array( __CLASS__, 'sellfy_buy_now_handler' )
		);

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_sellfy_scripts' ) );

	}


	function enqueue_sellfy_scripts() {
		
		// Make the Sellfy API JS available to WordPress
		wp_register_script( 'sellfy-button-js', 'https://sellfy.com/js/api_buttons.js', null, null, true );

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


}

