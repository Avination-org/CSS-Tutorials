<?php

if ( ! function_exists( 'ideapark_about_after_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'ideapark_about_after_switch_theme', 1000 );
	function ideapark_about_after_switch_theme() {
		$theme = wp_get_theme();
		if ( $theme->parent() ) {
			$theme = $theme->parent();
		}
		update_option( str_replace( '-child', '', $theme->get_stylesheet() ) . '_about_page', 1 );
	}
}

if ( ! function_exists( 'ideapark_about_after_setup_theme' ) ) {
	add_action( 'init', 'ideapark_about_after_setup_theme', 1000 );
	function ideapark_about_after_setup_theme() {
		if ( IDEAPARK_IS_AJAX ) {
			return;
		}
		if ( get_transient( '_wc_activation_redirect' ) ) {
			delete_transient( '_wc_activation_redirect' );
		}
		$theme = wp_get_theme();
		if ( $theme->parent() ) {
			$theme = $theme->parent();
		}
		$option_name = str_replace( '-child', '', $theme->get_stylesheet() ) . '_about_page';

		if ( ! defined( 'WP_CLI' ) && ( get_option( $option_name ) == 1 ) ) {
			wp_cache_delete( $option_name, 'options' );
			wp_cache_delete( 'alloptions', 'options' );
			wp_cache_delete( 'notoptions', 'options' );
			delete_option( $option_name );
			if ( strpos( filter_input( INPUT_SERVER, 'REQUEST_URI' ), 'page=ideapark_about' ) === false ) {
				wp_redirect( admin_url() . 'themes.php?page=ideapark_about' );
				exit();
			}
		}
	}
}

if ( ! function_exists( 'ideapark_about_add_menu_items' ) ) {
	add_action( 'admin_menu', 'ideapark_about_add_menu_items' );
	function ideapark_about_add_menu_items() {
		$theme = wp_get_theme();
		if ( $theme->parent() ) {
			$theme = $theme->parent();
		}
		$theme_name = $theme->name;
		add_theme_page(
			sprintf( esc_html__( 'About %s', 'antek' ), $theme_name ),
			sprintf( esc_html__( 'About %s', 'antek' ), $theme_name ),
			'manage_options',
			'ideapark_about',
			'ideapark_about_page'
		);
	}
}

if ( ! function_exists( 'ideapark_about_enqueue_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'ideapark_about_enqueue_scripts' );
	function ideapark_about_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && false !== strpos( $screen->id, '_page_ideapark_about' ) ) {
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			wp_enqueue_script( 'ideapark-plugins-installer', IDEAPARK_URI . '/includes/theme-about/plugins-installer.js', [ 'jquery' ], null, true );
			wp_localize_script( 'ideapark-plugins-installer', 'ideapark_pi_vars', [
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'errorText' => esc_html__( 'Something went wrong...', 'antek' )
			] );
			wp_enqueue_style( 'ideapark-about', IDEAPARK_URI . '/includes/theme-about/theme-about.css', [], null );
		}
	}
}

if ( ! function_exists( 'ideapark_about_ajax' ) ) {
	add_action( 'wp_ajax_ideapark_about_ajax', 'ideapark_about_ajax' );
	function ideapark_about_ajax() {

		extract( ideapark_about_plugins() );
		/* @var $next_action array
		 * @var $other_plugin_action array
		 * @var $other_plugin_list   array
		 * @var $other_plugin_unchecked   array
		 */

		if ( ! empty( $_POST['is_additional'] ) ) {

			if ( ! empty( $other_plugin_action ) ) {
				echo json_encode( $other_plugin_action );
			} else {
				ob_start();
				ideapark_additional_plugin_list( $other_plugin_list, $other_plugin_unchecked );
				$list = ob_get_clean();
				echo json_encode(
					[
						'success' => true,
						'list'    => trim( $list )
					] );
			}

		} else {

			if ( ! empty( $next_action ) ) {
				echo json_encode( $next_action );
			} else {
				if ( ideapark_is_elementor() ) {
					$elementor_instance = Elementor\Plugin::instance();
					$elementor_instance->files_manager->clear_cache();
				}
				echo json_encode( [ 'success' => true ] );
			}
		}

		die();
	}
}

if ( ! function_exists( 'ideapark_is_installed_all_required_plugins' ) ) {
	function ideapark_is_installed_all_required_plugins() {
		extract( ideapark_about_plugins() );

		/* @var $next_action array */
		return empty( $next_action );
	}
}

if ( ! function_exists( 'ideapark_about_plugins' ) ) {
	function ideapark_about_plugins() {
		static $cache;

		if ( $cache ) {
			return $cache;
		}

		$plugins             = ideapark_get_required_plugins();
		$next_action         = [];
		$main_plugin_name    = '';
		$plugin_names        = [];
		$other_plugin_list   = [];
		$other_plugin_action = [];
		$other_plugin_unchecked = [];
		$filter              = [];

		if ( ! empty( $_POST['plugins'] ) ) {
			$filter = explode( ',', $_POST['plugins'] );
		}

		foreach ( $plugins as $plugin ) {
			$is_required     = ! empty( $plugin['required'] );
			$plugin['state'] = ideapark_plugins_installer_check_plugin_state( $plugin['slug'] );
			if ( ! empty( $plugin['notice_disable'] ) ) {
				$other_plugin_unchecked[] = $plugin['slug'];
			}
			if ( in_array( $plugin['state'], [ 'install', 'activate', 'update' ] ) ) {
				if ( $is_required ) {
					if ( ! $next_action ) {
						$next_action = [
							'name'   => sprintf( $plugin['state'] == 'install' ? esc_html__( 'Install %s', 'antek' ) : ( $plugin['state'] == 'update' ? esc_html__( 'Update %s', 'antek' ) : esc_html__( 'Activate %s', 'antek' ) ), $plugin['name'] ),
							'slug'   => $plugin['slug'],
							'state'  => $plugin['state'],
							'action' => ideapark_plugins_installer_get_action( $plugin )
						];
					}
					if ( ( $plugin['slug'] == 'ideapark-antek' ) ) {
						$main_plugin_name = $plugin['name'];
						$plugin['name']   .= "*";
					}
					$plugin_names[] = $plugin['name'];
				} else {
					if ( ! $other_plugin_action && in_array( $plugin['slug'], $filter ) ) {
						$other_plugin_action = [
							'name'   => ( $plugin['state'] == 'install' ? esc_html__( 'Install', 'antek' ) : ( $plugin['state'] == 'update' ? esc_html__( 'Update', 'antek' ) : esc_html__( 'Activate', 'antek' ) ) ) . ' ' . $plugin['name'],
							'slug'   => $plugin['slug'],
							'state'  => $plugin['state'],
							'action' => ideapark_plugins_installer_get_action( $plugin )
						];
					}
					$other_plugin_list[ $plugin['slug'] ] = esc_html($plugin['name']) . ideapark_wrap( $plugin['state'] == 'install' ? esc_html__( 'Install and activate', 'antek' ) : ( $plugin['state'] == 'update' ? esc_html__( 'Update', 'antek' ) : esc_html__( 'Activate', 'antek' ) ), '<span class="action_name">', '</span>' );
				}
			}
		}

		return $cache = [
			'plugin_names'        => $plugin_names,
			'main_plugin_name'    => $main_plugin_name,
			'next_action'         => $next_action,
			'other_plugin_action' => $other_plugin_action,
			'other_plugin_list'   => $other_plugin_list,
			'other_plugin_unchecked' => $other_plugin_unchecked,
		];
	}
}

if ( ! function_exists( 'ideapark_about_page' ) ) {
	function ideapark_about_page() {
		$theme = wp_get_theme();
		if ( $theme->parent() ) {
			$theme = $theme->parent();
		}

		extract( ideapark_about_plugins() );
		/* @var $plugin_names array */
		/* @var $main_plugin_name string */
		/* @var $next_action array */
		/* @var $other_plugin_unchecked array */

		if ( isset( $_REQUEST['clear_cache'] ) ) {
			global $wpdb;
			ideapark_clear_customize_cache();
			delete_option( 'ideapark_scripts_hash' );
			if ( ideapark_is_elementor() ) {
				$elementor_instance = Elementor\Plugin::instance();
				$elementor_instance->files_manager->clear_cache();
			}
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'" );
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_ideapark_inline_svg'" );
			if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
				if ( ! wc_update_product_lookup_tables_is_running() ) {
					wc_update_product_lookup_tables();
					WC()->queue()->schedule_single(
						time() + 15,
						'ideapark_delete_transient'
					);
				}
			}
			wp_cache_flush();
			do_action('ideapark_clear_cache');
			wp_redirect( admin_url( 'themes.php?page=ideapark_about' ) );
			exit();
		}
		
		?>
		<div class="ideapark_about">

			<h1 class="ideapark_about_title">
				<?php
				echo esc_html(
					sprintf(
						__( 'Welcome to %1$s v.%2$s', 'antek' ),
						$theme->name,
						$theme->version
					)
				);
				?>
			</h1>

			<?php if ( $plugin_names ) { ?>
				<div class="ideapark_about_required_plugins">
					<div class="ideapark_about_description">
						<p>
							<?php
							echo wp_kses(
								sprintf(
									__( 'In order to continue, please install and activate or update required plugins:<br><b>%1$s</b>', 'antek' ),
									implode( ', ', $plugin_names )
								), [
									'b'  => [],
									'br' => [],
								]
							);
							?>
						</p>
					</div>

					<div class="ideapark_about_buttons">
						<a class="ideapark_plugins_installer_link button button-primary install-now"
						   href="#"><?php esc_html_e( 'Continue', 'antek' ) ?></a>
						<div class="ideapark_plugins_installer_error"></div>
						<div class="ideapark_plugins_installer_success hidden">
							<p>
								<span
									class="dashicons dashicons-yes"></span> <?php esc_html_e( 'All required plugins have been successfully installed.', 'antek' ); ?>
							</p>
						</div>
					</div>

					<?php if ( $main_plugin_name ) { ?>
						<div class="ideapark_about_notes">
							<p>
								<sup>*</sup>
								<?php
								echo esc_html(
									sprintf(
										__( '%1$s plugin will allow you to import demo content, and improve the theme\'s functionality', 'antek' ),
										$main_plugin_name
									)
								);
								?>
							</p>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="ideapark_about_next_step <?php if ( $plugin_names ) { ?>hidden<?php } ?>">
				<?php if ( current_user_can( 'install_plugins' ) ) { ?>
					<div class="step">
						<b><?php esc_html_e( 'Additional plugins', 'antek' ); ?></b>
						<p><?php esc_html_e( 'You can install additional plugins to extend the functionality of the theme', 'antek' ); ?></p>
						<?php if ( ! empty( $other_plugin_list ) && ideapark_plugins_installer_tgmpa_menu() ) { ?>
							<?php ideapark_additional_plugin_list( $other_plugin_list, $other_plugin_unchecked ); ?>
							<a href="<?php echo admin_url( 'themes.php?page=tgmpa-install-plugins' ); ?>"
							   class="ideapark_plugins_installer_link additional button button-primary"><?php esc_html_e( 'Continue', 'antek' ); ?></a>
						<?php } ?>
						<p class="additional-plugins-installed <?php if ( ! empty( $other_plugin_list ) && ideapark_plugins_installer_tgmpa_menu() ) { ?> hidden<?php } ?>">
							<b><?php esc_html_e( 'All additional plugins are installed.', 'antek' ); ?></b>
						</p>
					</div>
				<?php } ?>
				<div class="step">
					<b><?php esc_html_e( 'Import Demo', 'antek' ); ?></b>
					<p><?php esc_html_e( 'Use our Demo Content Importer to make your website similar to our demos', 'antek' ); ?>
						<br><br><b><?php esc_html_e( 'Please install necessary additional plugins before importing', 'antek' ); ?></b>
					</p>
					<a href="<?php echo admin_url( 'themes.php?page=ideapark_themes_importer_page' ); ?>"
					   class="button button-primary"><?php esc_html_e( 'Import demo', 'antek' ); ?></a>
				</div>
				<div class="step">
					<b><?php esc_html_e( 'Theme customization', 'antek' ); ?></b>
					<p><?php echo wp_kses( sprintf( __( 'Explore the <a href="%s" target="_blank">documentation</a> and then start customizing the theme', 'antek' ), IDEAPARK_MANUAL ), [
							'a' => [
								'href'   => true,
								'target' => true,
							]
						] ); ?></p>
					<a href="<?php echo admin_url( 'customize.php' ); ?>"
					   class="button button-primary"><?php esc_html_e( 'Customize', 'antek' ); ?></a>
					<p>
						<a style="color: #fff"
						   href="<?php echo admin_url( 'themes.php?page=ideapark_about&clear_cache&noheader' ); ?>"><?php esc_html_e( 'Clear theme cache', 'antek' ); ?></a>
					</p>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'ideapark_additional_plugin_list' ) ) {
	function ideapark_additional_plugin_list( $other_plugin_list, $other_plugin_unchecked = [] ) {
		$filter = false;
		if ( isset( $_POST['plugins'] ) ) {
			$filter = explode( ',', $_POST['plugins'] );
		}
		?>
		<?php if ( $other_plugin_list ) { ?>
			<ul class="plugins_list">
				<?php foreach ( $other_plugin_list as $plugin_code => $plugin_name ) { ?>
					<li class="plugins_list__item">
						<label><input type="checkbox" class="ideapark_additional_plugin" name="plugin[]"
									  value="<?php echo esc_attr( $plugin_code ); ?>"
						              <?php if ( ( $filter === false || in_array( $plugin_code, $filter ) ) && $filter === false && ! in_array( $plugin_code, $other_plugin_unchecked ) ) { ?>checked<?php } ?>> <?php echo ideapark_wrap( $plugin_name ); ?>
						</label>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		<?php
	}
}

if ( ! function_exists( 'ideapark_about_page_disable_tgmpa_notice' ) ) {
	add_filter( 'tgmpa_show_admin_notice_capability', 'ideapark_about_page_disable_tgmpa_notice' );
	function ideapark_about_page_disable_tgmpa_notice( $capability ) {
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'ideapark_about' ) {
			$capability = 'unfiltered_upload';
		}

		return $capability;
	}
}

if ( ! function_exists( 'ideapark_plugins_installer_get_action' ) ) {
	function ideapark_plugins_installer_get_action( $plugin ) {
		$output = '';
		if ( ! empty( $plugin['slug'] ) ) {
			$slug = $plugin['slug'];
			switch ( $plugin['state'] ) {
				case 'install':
					if ( class_exists( 'TGM_Plugin_Activation' ) ) {
						$instance = call_user_func( [ get_class( $GLOBALS['tgmpa'] ), 'get_instance' ] );
						$nonce    = wp_nonce_url(
							add_query_arg(
								[
									'plugin'        => urlencode( $slug ),
									'tgmpa-install' => 'install-plugin',
								],
								$instance->get_tgmpa_url()
							),
							'tgmpa-install',
							'tgmpa-nonce'
						);
					} else {
						$nonce = wp_nonce_url(
							add_query_arg(
								[
									'action' => 'install-plugin',
									'from'   => 'import',
									'plugin' => urlencode( $slug ),
								],
								network_admin_url( 'update.php' )
							),
							'install-plugin_' . trim( $slug )
						);
					}
					$output = $nonce;
					break;

				case 'activate':
					if ( class_exists( 'TGM_Plugin_Activation' ) ) {
						$instance = call_user_func( [ get_class( $GLOBALS['tgmpa'] ), 'get_instance' ] );
						$nonce    = wp_nonce_url(
							add_query_arg(
								[
									'plugin'         => urlencode( $slug ),
									'tgmpa-activate' => 'activate-plugin',
								],
								$instance->get_tgmpa_url()
							),
							'tgmpa-activate',
							'tgmpa-nonce'
						);
					} else {
						$plugin_link = $slug . '/' . $slug . '.php';
						$nonce       = add_query_arg(
							[
								'action'        => 'activate',
								'plugin'        => rawurlencode( $plugin_link ),
								'plugin_status' => 'all',
								'paged'         => '1',
								'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $plugin_link ),
							],
							network_admin_url( 'plugins.php' )
						);
					}
					$output = $nonce;
					break;

				case 'update':
					if ( class_exists( 'TGM_Plugin_Activation' ) ) {
						$instance = call_user_func( [ get_class( $GLOBALS['tgmpa'] ), 'get_instance' ] );
						$nonce    = wp_nonce_url(
							add_query_arg(
								[
									'plugin'       => urlencode( $slug ),
									'tgmpa-update' => 'update-plugin',
								],
								$instance->get_tgmpa_url()
							),
							'tgmpa-update',
							'tgmpa-nonce'
						);
					} else {
						$plugin_link = $slug . '/' . $slug . '.php';
						$nonce       = add_query_arg(
							[
								'action'        => 'update',
								'plugin'        => rawurlencode( $plugin_link ),
								'plugin_status' => 'all',
								'paged'         => '1',
								'_wpnonce'      => wp_create_nonce( 'update-plugin_' . $plugin_link ),
							],
							network_admin_url( 'plugins.php' )
						);
					}
					$output = $nonce;
					break;
			}
		}

		return str_replace( '&amp;', '&', $output );
	}
}

if ( ! function_exists( 'ideapark_plugins_installer_check_plugin_state' ) ) {
	function ideapark_plugins_installer_check_plugin_state( $slug ) {

		static $installed_plugins;

		$state = 'install';

		if ( empty( $installed_plugins ) ) {
			ideapark_register_required_plugins();
			$installed_plugins = true;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		foreach ( $plugins AS $path => $plugin ) {
			if ( strpos( $path, $slug . '/' ) === 0 ) {
				$state = is_plugin_inactive( $path ) ? 'activate' : 'deactivate';
			}
		}

		if ( $state != 'install' && ! empty( $GLOBALS['tgmpa'] ) && $GLOBALS['tgmpa']->does_plugin_have_update( $slug ) ) {
			$state = 'update';
		} elseif ( $state != 'install' && ! empty( $GLOBALS['tgmpa'] ) && $GLOBALS['tgmpa']->does_plugin_require_update( $slug ) ) {
			$state = 'update';
		}

		return $state;
	}
}

if ( ! function_exists( 'ideapark_plugins_installer_tgmpa_menu' ) ) {
	function ideapark_plugins_installer_tgmpa_menu() {

		static $installed_plugins;

		$state = true;

		if ( empty( $installed_plugins ) ) {
			ideapark_register_required_plugins();
			$installed_plugins = true;
		}

		if ( empty( $GLOBALS['tgmpa'] ) || true == $GLOBALS['tgmpa']->is_tgmpa_complete() ) {
			$state = false;
		}

		return $state;
	}
}