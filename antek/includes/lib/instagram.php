<?php


class Ideapark_Instagram {
	public static $instagram_error = '';

	public static function getUserName( $access_token ) {
		$ideapark_instagram_username = false;

		if ( preg_match( '~^([A-Z][a-z]{1,15})(-\d)?$~', $access_token ) ) {
			return $access_token;
		}

		if ( $access_token && preg_match( '~^(\d+)\.~', $access_token, $match ) && ( $userid = $match[1] ) ) {

			if ( !( $ideapark_instagram_username = get_option( 'ideapark_instagram_username' ) ) || get_option( 'ideapark_instagram_userid' ) != $userid) {
				$request_url = 'https://api.instagram.com/v1/users/' . rawurlencode( $userid ) . '/?access_token=' . rawurlencode( $access_token );

				if ( !extension_loaded( 'openssl' ) ) {
					self::$instagram_error = esc_html__( 'This class requires the php extension open_ssl to work as the instagram api works with https.', 'antek' );
				} else {
					global $wp_filesystem;
					if ( empty( $wp_filesystem ) ) {
						require_once ABSPATH . '/wp-admin/includes/file.php';
						WP_Filesystem();
					}
					$request       = $wp_filesystem->get_contents( $request_url );
					$usernamequery = json_decode( $request );
				}

				if ( !empty( $usernamequery ) && $usernamequery->meta->code == '200' && $usernamequery->data->username != '' ) {
					$ideapark_instagram_username = $usernamequery->data->username;

					if ( get_option( 'ideapark_instagram_username' ) ) {
						update_option( 'ideapark_instagram_username', $ideapark_instagram_username );
					} else {
						add_option( 'ideapark_instagram_username', $ideapark_instagram_username );
					}

					if ( get_option( 'ideapark_instagram_userid' ) ) {
						update_option( 'ideapark_instagram_userid', $userid );
					} else {
						add_option( 'ideapark_instagram_userid', $userid );
					}
				} else {
					self::$instagram_error = esc_html__( 'Wrong Instagram userid or Access Token.', 'antek' );
				}
			}
		} else {
			self::$instagram_error = esc_html__( 'Empty Instagram Access Token.', 'antek' );
		}

		return $ideapark_instagram_username;
	}
}