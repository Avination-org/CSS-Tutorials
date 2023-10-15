<?php

function ideapark_theme_colors() {
	return [
		'text_color'             => $text_color = esc_attr( ideapark_mod_hex_color_norm( 'text_color', '#000000' ) ),
		'text_color_light'       => ideapark_hex_to_rgb_overlay( '#FFFFFF', $text_color, 0.62 ),
		'background_color'       => esc_attr( ideapark_mod_hex_color_norm( 'background_color', '#ffffff' ) ),
		'light_background_color' => esc_attr( ideapark_mod_hex_color_norm( 'light_background_color', '#f6f6f6' ) ),
		'accent_color'           => esc_attr( ideapark_mod_hex_color_norm( 'accent_color', 'inherit' ) ),
	];
}

function ideapark_customize_css( $is_return_value = false ) {

	$custom_css = '';

	/**
	 * @var $text_color                  string
	 * @var $text_color_light            string
	 * @var $background_color            string
	 * @var $light_background_color      string
	 * @var $accent_color                string
	 */
	extract( ideapark_theme_colors() );

	$custom_css .= '
	<style> 
		:root {
			--text-color: ' . $text_color . ';
			--text-color-mid: ' . ideapark_hex_to_rgb_overlay( '#FFFFFF', $text_color, 0.767 ) . ';
			--text-color-light: ' . ideapark_hex_to_rgb_overlay( '#FFFFFF', $text_color, 0.54 ) . ';
			--text-color-extra-light: ' . ideapark_hex_to_rgb_overlay( '#FFFFFF', $text_color, 0.4 ) . ';
			--text-color-tr: ' . ideapark_hex_to_rgba( $text_color, 0.15 ) . ';
			--accent-border-color: ' . ideapark_hex_to_rgb_overlay( $accent_color, '#FFFFFF', 0.5 ) . ';
			--accent-dark-color: ' . ideapark_hex_to_rgb_overlay( $accent_color, $text_color, 0.5 ) . ';
			--background-color: ' . $background_color . ';
			--background-color-light: ' . $light_background_color . ';
			--background-color-10: ' . ideapark_hex_to_rgb_overlay( $background_color, $text_color, 0.1 ) . ';
			--accent-color: ' . $accent_color . ';
			--font-text: "' . esc_attr( ideapark_mod( 'theme_font' ) ) . '", sans-serif;
			--logo-size: ' . esc_attr( round( ideapark_mod( 'logo_size' ) ) ) . 'px;
			--logo-size-mobile: ' . esc_attr( round( ideapark_mod( 'logo_size_mobile' ) ) ) . 'px;
			--top-menu-submenu-color: ' . ideapark_mod_hex_color_norm( 'top_menu_submenu_color', $text_color ) . ';
			--top-menu-submenu-bg-color: ' . ideapark_mod_hex_color_norm( 'top_menu_submenu_bg_color', '#FFFFFF' ) . ';
			--shadow-color-desktop: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'shadow_color_desktop', $text_color ), 0.95 ) . ';
			--shadow-color-mobile: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'shadow_color_mobile', $text_color ), 0.95 ) . ';
			--select-bg: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="' . $text_color . '" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"/></svg>' ) . '");
			--stretch-bar-white: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="8" height="1" viewBox="0 0 10 1" xmlns="http://www.w3.org/2000/svg"><g fill="#FFFFFF" fill-rule="evenodd"><path d="M0 0h1v1H0zM5 0h1v1H5z"/></g></svg>' ) . '");
			--stretch-bar: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="8" height="1" viewBox="0 0 10 1" xmlns="http://www.w3.org/2000/svg"><g fill="' . $text_color . '" fill-rule="evenodd"><path d="M0 0h1v1H0zM5 0h1v1H5z"/></g></svg>' ) . '");
			--stretch-bar-gray: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="8" height="1" viewBox="0 0 10 1" xmlns="http://www.w3.org/2000/svg"><g fill="#cccccc" fill-rule="evenodd"><path d="M0 0h1v1H0zM5 0h1v1H5z"/></g></svg>' ) . '");
			--stretch-bar-accent: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="11" height="1" viewBox="0 0 11 1" xmlns="http://www.w3.org/2000/svg"><path fill="' . $accent_color . '" d="M0 0h1.5v1H0z" fill-rule="evenodd"/></svg>' ) . '");
			--steps-line: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="20" height="2" viewBox="0 0 20 2" xmlns="http://www.w3.org/2000/svg"><path fill="' . $text_color . '" d="M0 0h14v2H0z" fill-rule="evenodd"/></svg>' ) . '");
			--slider-base: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="275" height="14" viewBox="0 0 275 14" xmlns="http://www.w3.org/2000/svg"><path d="M0 10L275 0v14H0z" fill="#ffffff" fill-rule="evenodd"/></svg>' ) . '");
			--slider-base-selected: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="275" height="14" viewBox="0 0 275 14" xmlns="http://www.w3.org/2000/svg"><path d="M0 10L275 0v14H0z" fill="' . ideapark_hex_to_rgb_overlay( '#000000', $accent_color, 0.54 ) . '" fill-rule="evenodd"/></svg>' ) . '");
			
			--custom-transform-transition: visibility 0.5s cubic-bezier(0.86, 0, 0.07, 1), opacity 0.5s cubic-bezier(0.86, 0, 0.07, 1), transform 0.5s cubic-bezier(0.86, 0, 0.07, 1);
			--opacity-transition: opacity 0.3s linear, visibility 0.3s linear;
			--opacity-transform-transition: opacity 0.3s linear, visibility 0.3s linear, transform 0.3s ease-out;
			--hover-transition: opacity 0.3s linear, visibility 0.3s linear, color 0.3s linear, border-color 0.3s linear, background-color 0.3s linear, box-shadow 0.3s linear, transform 0.3s linear;
			--star-rating-image: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="14" height="10" fill="' . ideapark_mod_hex_color_norm( 'star_rating_color', $text_color ) . '" xmlns="http://www.w3.org/2000/svg"><path d="M8.956 9.782c.05.153-.132.28-.27.186L5.5 7.798l-3.19 2.168c-.137.093-.32-.033-.269-.187l1.178-3.563L.07 3.99c-.135-.095-.065-.3.103-.302l3.916-.032L5.335.114c.053-.152.28-.152.333 0L6.91 3.658l3.916.035c.168.001.238.206.103.302L7.78 6.217l1.175 3.565z"/></svg>' ) . '");
			--star-rating-image-sidebar:  url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="14" height="10" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M8.956 9.782c.05.153-.132.28-.27.186L5.5 7.798l-3.19 2.168c-.137.093-.32-.033-.269-.187l1.178-3.563L.07 3.99c-.135-.095-.065-.3.103-.302l3.916-.032L5.335.114c.053-.152.28-.152.333 0L6.91 3.658l3.916.035c.168.001.238.206.103.302L7.78 6.217l1.175 3.565z"/></svg>' ) . '");
			--select-image: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M.47 1.53 1.53.47 5 3.94 8.47.47l1.06 1.06L5 6.06.47 1.53z" fill="#dddddd"/></svg>' ) . '");
			
			--icon-decor: "\f111" /* ip-decor */;
			--icon-decor-left: "\f10e" /* ip-decor-left */;
			--icon-decor-right: "\f110" /* ip-decor-right */;
			--icon-li: "\f13f" /* ip-tick */;
			--icon-calendar: "\f104" /* ip-cal */;
			--icon-submenu: "\f128" /* ip-menu-right */;
			--icon-dropdown: "\f137" /* ip-select */;
			--icon-quote: "\f131" /* ip-quote */;
			--icon-close: "\f10b" /* ip-close */;
			--icon-check: "\f109" /* ip-check */;
			--icon-range: "\f132" /* ip-range */;
			--icon-user: "\f102" /* ip-author */;
			--icon-select: "\f115" /* ip-down_arrow */;
			--icon-depth: "\f112" /* ip-depth */;
			--icon-plus: "\f12f" /* ip-plus */;
		}
		
		.c-header {
			--top-color: ' . esc_attr( ideapark_mod_hex_color_norm( 'header_top_color', '#FFFFFF' ) ) . ';
			--top-background-color: ' . esc_attr( ideapark_mod_hex_color_norm( 'header_top_background_color', '#000000' ) ) . ';
			
			--header-color-mobile: ' . ideapark_mod_hex_color_norm( 'mobile_header_color', $text_color ) . ';
			--header-color-bg-mobile: ' . ideapark_mod_hex_color_norm( 'mobile_header_background_color', '#FFFFFF' ) . ';
		}
		
		.c-page-header {
			--header-min-height: ' . esc_attr( round( ideapark_mod( 'header_min_height' ) ) ) . 'px;
			--header-font-size-responsive: calc(30px + ' . esc_attr( round( ideapark_mod( 'header_font_size' ) ) - 30 ) . ' * (100vw - 320px) / 1079); /* 1399 - 320 */
			--bg-opacity: ' . esc_attr( round( ideapark_mod( 'header_image_opacity' ), 2 ) ) . ';
			--page-header-color: ' . esc_attr( ideapark_mod_hex_color_norm( 'header_color', '#FFFFFF' ) ) . ';
			--page-header-bg-color: ' . esc_attr( ideapark_mod_hex_color_norm( 'header_background_color', $text_color ) ) . ';
			--page-header-bg-color-tr: ' . esc_attr( ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'header_background_color', $text_color ), 0 ) ) . ';
			--stretch-bar: url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="8" height="1" viewBox="0 0 10 1" xmlns="http://www.w3.org/2000/svg"><g fill="' . ideapark_mod_hex_color_norm( 'header_color', '#FFFFFF' ) . '" fill-rule="evenodd"><path d="M0 0h1v1H0zM5 0h1v1H5z"/></g></svg>' ) . '");
		}
		
		.woocommerce-store-notice {
			--store-notice-color: ' . ideapark_mod_hex_color_norm( 'store_notice_color' ) . ';
			--store-notice-background-color: ' . ideapark_mod_hex_color_norm( 'store_notice_background_color' ) . ';
		}
		
		.c-to-top-button {
			--to-top-button-color: ' . ideapark_mod_hex_color_norm( 'to_top_button_color' ) . ';
		}
		
		.c-vehicle__booking-wrap {
			--price-block-width: ' . esc_attr( round( ideapark_mod( 'price_block_width' ) ) ) . 'px;
		}
	</style>';

	$custom_css = preg_replace( '~[\r\n]~', '', preg_replace( '~[\t\s]+~', ' ', str_replace( [
		'<style>',
		'</style>'
	], [ '', '' ], $custom_css ) ) );

	if ( $custom_css ) {
		if ( $is_return_value ) {
			return $custom_css;
		} else {
			wp_add_inline_style( 'ideapark-core', $custom_css );
		}
	}
}

function ideapark_uniord( $u ) {
	$k  = mb_convert_encoding( $u, 'UCS-2LE', 'UTF-8' );
	$k1 = ord( substr( $k, 0, 1 ) );
	$k2 = ord( substr( $k, 1, 1 ) );

	return $k2 * 256 + $k1;
}

function ideapark_b64enc( $input ) {

	$keyStr = "ABCDEFGHIJKLMNOP" .
	          "QRSTUVWXYZabcdef" .
	          "ghijklmnopqrstuv" .
	          "wxyz0123456789+/" .
	          "=";

	$output = "";
	$i      = 0;

	do {
		$chr1 = ord( substr( $input, $i ++, 1 ) );
		$chr2 = $i < strlen( $input ) ? ord( substr( $input, $i ++, 1 ) ) : null;
		$chr3 = $i < strlen( $input ) ? ord( substr( $input, $i ++, 1 ) ) : null;

		$enc1 = $chr1 >> 2;
		$enc2 = ( ( $chr1 & 3 ) << 4 ) | ( $chr2 >> 4 );
		$enc3 = ( ( $chr2 & 15 ) << 2 ) | ( $chr3 >> 6 );
		$enc4 = $chr3 & 63;

		if ( $chr2 === null ) {
			$enc3 = $enc4 = 64;
		} else if ( $chr3 === null ) {
			$enc4 = 64;
		}

		$output = $output .
		          substr( $keyStr, $enc1, 1 ) .
		          substr( $keyStr, $enc2, 1 ) .
		          substr( $keyStr, $enc3, 1 ) .
		          substr( $keyStr, $enc4, 1 );
		$chr1   = $chr2 = $chr3 = "";
		$enc1   = $enc2 = $enc3 = $enc4 = "";
	} while ( $i < strlen( $input ) );

	return $output;
}