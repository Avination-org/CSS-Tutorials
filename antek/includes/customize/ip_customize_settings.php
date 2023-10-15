<?php

$ideapark_customize_custom_css = [];
$ideapark_customize            = [];
$ideapark_customize_mods       = [];
$ideapark_customize_panels     = [];
$ideapark_customize_mods_def   = [];

if ( ! function_exists( 'ideapark_init_theme_customize' ) ) {
	function ideapark_init_theme_customize() {
		global $ideapark_customize, $ideapark_customize_panels;

		$ideapark_customize_panels = [
			'theme_settings' => [
				'priority'    => 1,
				'title'       => __( 'Antek Theme Settings', 'antek' ),
				'description' => '',
			]
		];

		$version = md5( ideapark_mtime( __FILE__ ) . '-' . IDEAPARK_VERSION );

		if ( ( $data = get_option( 'ideapark_customize' ) ) && ! empty( $data['version'] ) && ! empty( $data['settings'] ) ) {
			if ( $data['version'] == $version ) {
				$ideapark_customize = $data['settings'];

				return;
			} else {
				delete_option( 'ideapark_customize' );
			}
		}

		$ideapark_customize = [
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'General catalog settings', 'antek' ),
				'controls' => [
					'disable_booking'                => [
						'label'             => __( 'Disable booking', 'antek' ),
						'description'       => __( 'If you disable booking, you can configure the feedback form to send a request.', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'request_shortcode'              => [
						'label'             => __( 'Request form shortcode', 'antek' ),
						'description'       => __( 'Contact Form 7 shortcode', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'refresh'           => false,
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking' => [ 'not_empty' ],
						],
					],
					'request_shortcode_field_name'   => [
						'label'             => __( 'Hidden field name for order details', 'antek' ),
						'description'       => __( 'Please add hidden field [hidden field-name] to price request form. This field will contain item name.', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'refresh'           => false,
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking' => [ 'not_empty' ],
						],
					],
					'price_type'                     => [
						'label'             => __( 'Price type', 'antek' ),
						'description'       => __( 'Global setting of the catalog pricing mode', 'antek' ),
						'default'           => 'default',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'is_option'         => true,
						'choices'           => [
							'default' => __( 'Days / Week / Month', 'antek' ),
							'cond'    => __( 'Conditional', 'antek' ),
						],
					],
					'unlimited_booking'              => [
						'label'             => __( 'Stock management', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'is_option'         => true,
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'show_available'                 => [
						'label'             => __( 'Display the number of available products for the user', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'is_option'         => true,
						'dependency'        => [
							'unlimited_booking' => [ 'not_empty' ],
							'disable_booking'   => [ 'is_empty' ],
						],
					],
					'multiple_booking'               => [
						'label'             => __( 'Multiple bookings in one order', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'disable_redirect_after_booking' => [
						'label'             => __( 'Disable redirection to the shopping cart or checkout after booking', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'show_booked_days'               => [
						'label'             => __( 'Show booked days in the calendar on the product page (frontend)', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'show_price_before_discount'     => [
						'label'             => __( 'Show price before discount', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'disable_payment'     => [
						'label'             => __( 'Disable payment', 'antek' ),
						'description'       => __( 'Disable all payment gateways on the checkout page.', 'antek' ),
						'default'           => false,
						'refresh'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'disable_self_pickup' => [
						'label'             => __( 'Disable self-pickup', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'wc_integration' => [
						'label'             => __( 'Woocommerce integration type', 'antek' ),
						'description'       => __( 'Use the second option if you are going to use coupons or Woocommerce shipping related to specific rental items.', 'antek' ),
						'default'           => 'one',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'is_option'         => true,
						'refresh'           => false,
						'choices'           => [
							'one'  => __( 'Using one Woocommerce product for all rental items', 'antek' ),
							'each' => __( 'Creating a separate Woocommerce product for each rental item', 'antek' ),
						],
					],
					'booking_type'   => [
						'label'             => __( 'Booking type', 'antek' ),
						'default'           => 'night',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'night' => __( 'Nights booking', 'antek' ),
							'day'   => __( 'Days booking', 'antek' ),
						],
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'date_format'    => [
						'label'             => __( 'Date format for filter and booking', 'antek' ),
						'default'           => 'MM/DD/YYYY',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'MM/DD/YYYY' => 'MM/DD/YYYY',
							'DD-MM-YYYY' => 'DD-MM-YYYY',
							'DD.MM.YYYY' => 'DD.MM.YYYY',
							'YYYY-MM-DD' => 'YYYY-MM-DD',
						],
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],

					'cleaning_days'        => [
						'label'             => __( 'Cleaning after booking (additional days)', 'antek' ),
						'default'           => '0',
						'min'               => '0',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => false,
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'hours_before_booking' => [
						'label'             => __( 'Hours before booking starts', 'antek' ),
						'description'       => __( 'For example, enter 24 hours to exclude booking today for today', 'antek' ),
						'default'           => '',
						'min'               => '',
						'pattern'           => '^[0-9]*$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => false,
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],

					'pickup_dropoff_days' => [
						'label'             => __( 'Days of the week available for pick-up and drop-off', 'antek' ),
						'default'           => '7,1,2,3,4,5,6',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Checkbox_Set_Control',
						'refresh'           => false,
						'choices'           => [
							'7' => __( 'Su', 'antek' ),
							'1' => __( 'Mo', 'antek' ),
							'2' => __( 'Tu', 'antek' ),
							'3' => __( 'We', 'antek' ),
							'4' => __( 'Th', 'antek' ),
							'5' => __( 'Fr', 'antek' ),
							'6' => __( 'Sa', 'antek' ),
						],
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'pickup_dropoff_time' => [
						'label'             => __( 'Pick-up and drop-off time selection', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'pickup_time_range'   => [
						'label'             => __( 'Pick-up time range', 'antek' ),
						'default'           => '14,18',
						'show_value'        => false,
						'type'              => 'range',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 24,
						'step'              => 1,
						'refresh'           => false,
						'dependency'        => [
							'disable_booking'     => [ 'is_empty' ],
							'pickup_dropoff_time' => [ 'not_empty' ],
						],
					],
					'dropoff_time_range'  => [
						'label'             => __( 'Drop-off time range', 'antek' ),
						'default'           => '8,12',
						'show_value'        => false,
						'type'              => 'range',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 24,
						'step'              => 1,
						'refresh'           => false,
						'dependency'        => [
							'disable_booking'     => [ 'is_empty' ],
							'pickup_dropoff_time' => [ 'not_empty' ],
						],
					],
					'time_format'         => [
						'label'             => __( 'Time format', 'antek' ),
						'type'              => 'radio',
						'default'           => '24h',
						'refresh'           => false,
						'choices'           => [
							'12h' => __( '12H', 'antek' ),
							'24h' => __( '24H', 'antek' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'     => [ 'is_empty' ],
							'pickup_dropoff_time' => [ 'not_empty' ],
						],
					],
					'time_increment'      => [
						'label'             => __( 'Increment of the minutes selection list for times (i.e. 30 to allow only selection of times ending in 0 or 30)', 'antek' ),
						'type'              => 'select',
						'default'           => '15',
						'refresh'           => false,
						'choices'           => [
							'5'  => __( '5', 'antek' ),
							'10' => __( '10', 'antek' ),
							'15' => __( '15', 'antek' ),
							'30' => __( '30', 'antek' ),
							'60' => __( '60', 'antek' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'     => [ 'is_empty' ],
							'pickup_dropoff_time' => [ 'not_empty' ],
						],
					],
					'catalog_permalinks'  => [
						'label'             => __( 'Permalink Settings', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'catalog_base'        => [
						'label'             => __( 'Catalog base', 'antek' ),
						'type'              => 'text',
						'is_option'         => true,
						'default'           => 'catalog',
						'refresh'           => false,
						'sanitize_callback' => 'ideapark_sanitize_base',
					],
					'type_base'           => [
						'label'             => __( 'Category base', 'antek' ),
						'type'              => 'text',
						'is_option'         => true,
						'default'           => 'vehicle_type',
						'refresh'           => false,
						'sanitize_callback' => 'ideapark_sanitize_base',
					],
				],
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Catalog items list', 'antek' ),
				'controls' => [
					'catalog_list_header'       => [
						'label'             => __( 'Page header', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Rental Listings', 'antek' ),
						'sanitize_callback' => 'sanitize_textarea_field',
					],
					'favorites_header'          => [
						'label'             => __( 'Favorites list header', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Favorites', 'antek' ),
						'sanitize_callback' => 'sanitize_textarea_field',
					],
					'price_block_title'         => [
						'label'             => __( 'Price block title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Total Rental Price', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'price_block_tax'           => [
						'label'             => __( 'Price block tax info', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Incl. taxes', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'reserve_button_title_list' => [
						'label'             => __( 'Reserve button title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Reserve', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'delivery_title_list'       => [
						'label'             => __( 'Delivery title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Two way delivery', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'sidebar_catalog'           => [
						'label'             => __( 'Sidebar in catalog', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'catalog_order_list'        => [
						'label'             => __( 'Sorting Options in Catalog', 'antek' ),
						'type'              => 'checklist',
						'default'           => 'newest=1|low_price=1|high_price=1|menu_order=1',
						'choices'           => [
							'newest'     => __( 'Newest first', 'antek' ),
							'low_price'  => __( 'Low price first', 'antek' ),
							'high_price' => __( 'High price first', 'antek' ),
							'menu_order' => __( 'Manual', 'antek' ),
						],
						'sortable'          => true,
						'class'             => 'WP_Customize_Checklist_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'catalog_order'             => [
						'label'             => __( 'Default sorting in Catalog', 'antek' ),
						'type'              => 'radio',
						'default'           => 'newest',
						'choices'           => [
							'newest'     => __( 'Newest first', 'antek' ),
							'low_price'  => __( 'Low price first', 'antek' ),
							'high_price' => __( 'High price first', 'antek' ),
							'menu_order' => __( 'Manual', 'antek' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
					],

					'price_block_width'                => [
						'label'             => __( 'Price block width (desktop, px)', 'antek' ),
						'default'           => 150,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 150,
						'max'               => 300,
						'step'              => 10,
						'refresh_css'       => '.c-vehicle__booking-wrap',
						'refresh'           => false,
					],
					'archive_block'                    => [
						'label'             => __( 'HTML block (description or SEO-text)', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Block_Control',
						'sanitize_callback' => 'absint',
					],
					'archive_block_place'              => [
						'label'             => __( 'Placement of the HTML block', 'antek' ),
						'default'           => 'bottom',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'top'    => __( 'Top', 'antek' ),
							'bottom' => __( 'Bottom', 'antek' ),
						],
						'dependency'        => [
							'archive_block' => [ 'not_empty' ],
						],
					],
					'archive_block_on_first_page_only' => [
						'label'             => __( 'Show Html block on first page only', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'archive_block' => [ 'not_empty' ],
						],
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Catalog item page', 'antek' ),
				'controls' => [
					'catalog_item_header'       => [
						'label'             => __( 'Page header', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Details', 'antek' ),
						'sanitize_callback' => 'sanitize_textarea_field',
					],
					'reserve_button_title_item' => [
						'label'             => __( 'Book button title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Book now', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'delivery_title_item'       => [
						'label'             => __( 'Delivery title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Two way delivery', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'product_sections'          => [
						'label'             => __( 'Product page sections', 'antek' ),
						'description'       => __( 'Enable or disable section, and then drag and drop sections below to set up their order', 'antek' ),
						'type'              => 'checklist',
						'default'           => 'gallery=1|header=1|locations=1|download=1|calendar=1|description=1|details=1',
						'choices'           => [
							'gallery'     => __( 'Image gallery', 'antek' ),
							'header'      => __( 'Header', 'antek' ),
							'locations'   => __( 'Locations', 'antek' ),
							'download'    => __( 'Attached file / Favorite', 'antek' ),
							'calendar'    => __( 'Booking calendar', 'antek' ),
							'description' => __( 'Description', 'antek' ),
							'details'     => __( 'Details', 'antek' ),
						],
						'sortable'          => true,
						'class'             => 'WP_Customize_Checklist_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'hide_booking_sidebar'      => [
						'label'             => __( 'Hide sidebar', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'show_price_block'          => [
						'label'             => __( 'Show price block', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'hide_booking_sidebar' => [ 'is_empty' ],
						],
					],
					'show_cnt_day'              => [
						'label'             => __( 'Show the number of selected days (if more than 1)', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'hide_booking_sidebar' => [ 'is_empty' ],
						],
					],
					'product_modal'             => [
						'label'             => __( 'Images modal gallery', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_bottom_page' => [
						'label'             => __( 'HTML block to display at the bottom of the product page', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Block_Control',
						'sanitize_callback' => 'absint',
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Filter', 'antek' ),
				'controls' => [
					'disable_filter'             => [
						'label'             => __( 'Hide horizontal filter in catalog', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'filter_location'            => [
						'label'             => __( 'Show "Location" in filter', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'filter_type'                => [
						'label'             => __( 'Show "Category" in filter', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'filter_type_only_top_level' => [
						'label'             => __( 'Top-level categories only', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'filter_type' => [ 'not_empty' ],
						],
					],
					'filter_delivery'            => [
						'label'             => __( 'Show "Self-Pickup/Delivery" in filter', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'filter_location_icon' => [
						'label'             => __( 'Custom icon for "Location"', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'filter_type_icon'     => [
						'label'             => __( 'Custom icon for "Category"', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'filter_range_icon'    => [
						'label'             => __( 'Custom icon for "Date range"', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'filter_button_text'   => [
						'label'             => __( 'Button text', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Find my rentals', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field'
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Minimum and maximum days for booking', 'antek' ),
				'controls' => [
					'auto_set_minimum_days'           => [
						'label'             => __( 'Auto set the minimum or maximum days on the product page if the dates are out of the allowed range', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'maximum_days'                    => [
						'label'             => __( 'Maximum days for booking', 'antek' ),
						'default'           => '',
						'min'               => '',
						'pattern'           => '^[0-9]*$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'minimum_days'                    => [
						'label'             => __( 'Minimum days for booking (default)', 'antek' ),
						'default'           => '1',
						'min'               => '1',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'conditional_minimum_days_info_1' => [
						'label'             => __( 'Conditional #1', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'condition_minimum_days'          => [
						'label'             => __( 'Condition', 'antek' ),
						'description'       => __( 'Higher priority than the default option above', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Condition',
						'sanitize_callback' => 'absint',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'conditional_minimum_days'        => [
						'label'             => __( 'Minimum days for booking', 'antek' ),
						'default'           => '1',
						'min'               => '1',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'        => [ 'is_empty' ],
							'condition_minimum_days' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_included' => [
						'label'             => __( 'All selected days must be in the condition range', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking'          => [ 'is_empty' ],
							'condition_minimum_days'   => [ 'not_empty' ],
							'conditional_minimum_days' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_error'    => [
						'label'             => __( 'Custom error text', 'antek' ),
						'description'       => __( 'Enter the error text that the user will see if the number of selected days is less than the allowed one.', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_textarea_field',
						'dependency'        => [
							'disable_booking'                 => [ 'is_empty' ],
							'condition_minimum_days'          => [ 'not_empty' ],
							'conditional_minimum_days'        => [ 'not_empty' ],
							'condition_minimum_days_included' => [ 'not_empty' ],
						],
					],

					'conditional_minimum_days_info_2'   => [
						'label'             => __( 'Conditional #2', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'condition_minimum_days_2'          => [
						'label'             => __( 'Condition', 'antek' ),
						'description'       => __( 'Higher priority than the condition above', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Condition',
						'sanitize_callback' => 'absint',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'conditional_minimum_days_2'        => [
						'label'             => __( 'Minimum days for booking', 'antek' ),
						'default'           => '1',
						'min'               => '1',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'          => [ 'is_empty' ],
							'condition_minimum_days_2' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_included_2' => [
						'label'             => __( 'All selected days must be in the condition range', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking'            => [ 'is_empty' ],
							'condition_minimum_days_2'   => [ 'not_empty' ],
							'conditional_minimum_days_2' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_error_2'    => [
						'label'             => __( 'Custom error text', 'antek' ),
						'description'       => __( 'Enter the error text that the user will see if the number of selected days is less than the allowed one.', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_textarea_field',
						'dependency'        => [
							'disable_booking'                   => [ 'is_empty' ],
							'condition_minimum_days_2'          => [ 'not_empty' ],
							'conditional_minimum_days_2'        => [ 'not_empty' ],
							'condition_minimum_days_included_2' => [ 'not_empty' ],
						],
					],

					'conditional_minimum_days_info_3'   => [
						'label'             => __( 'Conditional #3', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'condition_minimum_days_3'          => [
						'label'             => __( 'Condition', 'antek' ),
						'description'       => __( 'Higher priority than the condition above', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Condition',
						'sanitize_callback' => 'absint',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'conditional_minimum_days_3'        => [
						'label'             => __( 'Minimum days for booking', 'antek' ),
						'default'           => '1',
						'min'               => '1',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'          => [ 'is_empty' ],
							'condition_minimum_days_3' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_included_3' => [
						'label'             => __( 'All selected days must be in the condition range', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking'            => [ 'is_empty' ],
							'condition_minimum_days_3'   => [ 'not_empty' ],
							'conditional_minimum_days_3' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_error_3'    => [
						'label'             => __( 'Custom error text', 'antek' ),
						'description'       => __( 'Enter the error text that the user will see if the number of selected days is less than the allowed one.', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_textarea_field',
						'dependency'        => [
							'disable_booking'                   => [ 'is_empty' ],
							'condition_minimum_days_3'          => [ 'not_empty' ],
							'conditional_minimum_days_3'        => [ 'not_empty' ],
							'condition_minimum_days_included_3' => [ 'not_empty' ],
						],
					],

					'conditional_minimum_days_info_4'   => [
						'label'             => __( 'Conditional #4', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'condition_minimum_days_4'          => [
						'label'             => __( 'Condition', 'antek' ),
						'description'       => __( 'Higher priority than the condition above', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Condition',
						'sanitize_callback' => 'absint',
						'dependency'        => [
							'disable_booking' => [ 'is_empty' ],
						],
					],
					'conditional_minimum_days_4'        => [
						'label'             => __( 'Minimum days for booking', 'antek' ),
						'default'           => '1',
						'min'               => '1',
						'pattern'           => '^[0-9]+$',
						'class'             => 'WP_Customize_Number_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'disable_booking'          => [ 'is_empty' ],
							'condition_minimum_days_4' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_included_4' => [
						'label'             => __( 'All selected days must be in the condition range', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'disable_booking'            => [ 'is_empty' ],
							'condition_minimum_days_4'   => [ 'not_empty' ],
							'conditional_minimum_days_4' => [ 'not_empty' ],
						],
					],
					'condition_minimum_days_error_4'    => [
						'label'             => __( 'Custom error text', 'antek' ),
						'description'       => __( 'Enter the error text that the user will see if the number of selected days is less than the allowed one.', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_textarea_field',
						'dependency'        => [
							'disable_booking'                   => [ 'is_empty' ],
							'condition_minimum_days_4'          => [ 'not_empty' ],
							'conditional_minimum_days_4'        => [ 'not_empty' ],
							'condition_minimum_days_included_4' => [ 'not_empty' ],
						],
					],
				],
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Price on request', 'antek' ),
				'controls' => [

					'price_on_request_label'      => [
						'label'             => __( 'Price request label (instead of the price)', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Request', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'price_on_request_button'     => [
						'label'             => __( 'Price request button text', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Request a price', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'price_on_request_shortcode'  => [
						'label'             => __( 'Price request form shortcode', 'antek' ),
						'description'       => __( 'Contact Form 7 shortcode', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field'
					],
					'price_on_request_field_name' => [
						'label'             => __( 'Hidden field name for order details', 'antek' ),
						'description'       => __( 'Please add hidden field [hidden field-name] to price request form. This field will contain order details such as selected dates, vehicle name and other.', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field'
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Header', 'antek' ),
				'controls' => [
					'header_desktop_settings_info' => [
						'label'             => __( 'Header Settings', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'header_type' => [
						'label'             => '',
						'description'       => '',
						'type'              => 'hidden',
						'default'           => 'header-type-1',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Hidden_Control',
					],

					'header_search'        => [
						'label'             => __( 'Show search button', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__main-row-item--search-btn',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-search-button',
					],
					'header_cart'          => [
						'label'             => __( 'Show cart', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__main-row-item--cart',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-cart',
					],
					'header_support'       => [
						'label'             => __( 'Show support block', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__support',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-support',
					],
					'header_support_title' => [
						'label'             => __( 'Support title', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__support',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-support',
						'dependency'        => [
							'header_support' => [ 'not_empty' ],
						],
					],
					'header_support_phone' => [
						'label'             => __( 'Support phone number', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__support',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-support',
						'dependency'        => [
							'header_support' => [ 'not_empty' ],
						],
					],

					'header_top_bar_settings_info' => [
						'label'             => __( 'Top Bar Settings', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'header_blocks' => [
						'label'             => __( 'Text blocks', 'antek' ),
						'description'       => __( 'Enable or disable blocks, and then drag and drop blocks below to set up their order', 'antek' ),
						'type'              => 'checklist',
						'default'           => 'social=1|phone=1|email=1|address=1|hours=1|callback=1|auth=1|favorites=1|other=1',
						'choices'           => [
							'social'    => __( 'Social Media', 'antek' ),
							'phone'     => __( 'Phone', 'antek' ),
							'email'     => __( 'Email', 'antek' ),
							'address'   => __( 'Address', 'antek' ),
							'hours'     => __( 'Working Hours', 'antek' ),
							'callback'  => __( 'Call me back', 'antek' ),
							'auth'      => __( 'Auth', 'antek' ),
							'favorites' => __( 'Favorites', 'antek' ),
							'other'     => __( 'Other', 'antek' ),
						],
						'sortable'          => true,
						'class'             => 'WP_Customize_Checklist_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-top-row',
					],

					'header_blocks_layout' => [
						'label'             => __( 'Text blocks layout', 'antek' ),
						'default'           => 'blocks-first',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'choices'           => [
							'blocks-center' => IDEAPARK_URI . '/assets/img/blocks-center.png',
							'blocks-first'  => IDEAPARK_URI . '/assets/img/blocks-first.png',
							'blocks-last'   => IDEAPARK_URI . '/assets/img/blocks-last.png',
						],
						'refresh'           => '.c-header__top-row',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-top-row',
					],

					'header_phone'   => [
						'label'             => __( 'Phone', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--phone',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-phone',
						'dependency'        => [
							'header_blocks' => [ 'search=phone=1' ],
						],
					],
					'header_email'   => [
						'label'             => __( 'Email', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--email',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-email',
						'dependency'        => [
							'header_blocks' => [ 'search=email=1' ],
						],
					],
					'header_address' => [
						'label'             => __( 'Address', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--address',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-address',
						'dependency'        => [
							'header_blocks' => [ 'search=address=1' ],
						],
					],
					'header_hours'   => [
						'label'             => __( 'Working hours', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--hours',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-hours',
						'dependency'        => [
							'header_blocks' => [ 'search=hours=1' ],
						],
					],

					'header_callback' => [
						'label'             => __( 'Call me back', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--callback',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-callback',
						'dependency'        => [
							'header_blocks' => [ 'search=callback=1' ],
						],
					],

					'header_callback_title'     => [
						'label'             => __( '"Call me back" popup window header', 'antek' ),
						'description'       => __( 'Disabled if empty', 'antek' ),
						'type'              => 'text',
						'default'           => 'Call me back',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--callback',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-callback',
						'dependency'        => [
							'header_blocks'   => [ 'search=callback=1' ],
							'header_callback' => [ 'not_empty' ]
						],
					],
					'header_callback_shortcode' => [
						'label'             => __( '"Call me back" form shortcode', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--callback',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-callback',
						'dependency'        => [
							'header_blocks'   => [ 'search=callback=1' ],
							'header_callback' => [ 'not_empty' ]
						],
					],

					'header_other' => [
						'label'             => __( 'Other', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__top-row-item--other',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-other',
						'dependency'        => [
							'header_blocks' => [ 'search=other=1' ],
						],
					],

					'header_top_background_color' => [
						'label'             => __( 'Top bar background color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#222222',
						'refresh'           => false,
						'refresh_css'       => '.c-header',
					],

					'header_top_color' => [
						'label'             => __( 'Top bar text color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header',
					],

					'header_top_accent_color' => [
						'label'             => __( 'Top bar accent color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#edb509',
						'refresh'           => false,
						'refresh_css'       => '.c-header',
					],

					'header_mobile_settings_info' => [
						'label'             => __( 'Mobile Header Settings', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'auth_disabled_mobile'           => [
						'label'             => __( 'Hide Auth button in header', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'header_type_mobile' => [ 'header-type-mobile-2' ],
						],
					],
					'search_disabled_mobile'         => [
						'label'             => __( 'Hide Search button in header', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'header_type_mobile' => [ 'header-type-mobile-2' ],
						],
					],
					'cart_disabled_mobile'           => [
						'label'             => __( 'Hide Cart button in header', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'header_type_mobile' => [ 'header-type-mobile-2' ],
						],
					],
					'mobile_header_background_color' => [
						'label'             => __( 'Header background color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header',
					],

					'mobile_header_color' => [
						'label'             => __( 'Header text color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#000000',
						'refresh'           => false,
						'refresh_css'       => '.c-header',
					],
				],
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Page Header', 'antek' ),
				'controls' => [
					'header_color'            => [
						'label'             => __( 'Header text color ', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-page-header'
					],
					'header_background_color' => [
						'label'             => __( 'Header background color (inner pages)', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-page-header'
					],
					'header_icon'             => [
						'label'             => __( 'Headed default icon (inner pages)', 'antek' ),
						'description'       => __( 'Displayed if not specified on the page', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => true,
					],
					'header_image'            => [
						'label'             => __( 'Default background image (inner pages)', 'antek' ),
						'description'       => __( 'Displayed if not specified on the page', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => true,
					],
					'header_image_catalog'    => [
						'label'             => __( 'Catalog background image', 'antek' ),
						'description'       => __( 'Displayed if not specified on the page', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => true,
					],
					'header_image_shop'       => [
						'label'             => __( 'Shop background image', 'antek' ),
						'description'       => __( 'Displayed if not specified on the page', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => true,
					],
					'header_image_opacity'    => [
						'label'             => __( 'Headed image opacity (inner pages)', 'antek' ),
						'default'           => 1,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 1,
						'step'              => 0.01,
						'refresh_css'       => '.c-page-header',
						'refresh'           => false,
					],
					'header_min_height'       => [
						'label'             => __( 'Header min height (px)', 'antek' ),
						'default'           => 465,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 200,
						'max'               => 500,
						'step'              => 10,
						'refresh_css'       => '.c-page-header',
						'refresh'           => false,
					],
					'header_font_size'        => [
						'label'             => __( 'Page title font size (px)', 'antek' ),
						'default'           => 64,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 30,
						'max'               => 70,
						'step'              => 1,
						'refresh_css'       => '.c-page-header',
						'refresh'           => false,
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Menu', 'antek' ),
				'controls' => [

					'menu_desktop_settings_info' => [
						'label'             => __( 'Desktop Menu', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'top_menu_third'            => [
						'label'             => __( 'Show third level in the top menu', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-top-menu',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-top-menu',
					],
					'top_menu_submenu_color'    => [
						'label'             => __( 'Top menu popup text color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh_css'       => '.c-top-menu',
						'refresh'           => false,
					],
					'top_menu_submenu_bg_color' => [
						'label'             => __( 'Top menu popup background color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh_css'       => '.c-top-menu',
						'refresh'           => false,
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Footer', 'antek' ),
				'controls' => [
					'footer_page'      => [
						'label'             => __( 'HTML block to display in the footer', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Block_Control',
						'sanitize_callback' => 'absint',
						'refresh'           => '.c-footer',
						'refresh_id'        => 'footer',
						'refresh_wrapper'   => true,
					],
					'footer_copyright' => [
						'label'             => __( 'Copyright', 'antek' ),
						'type'              => 'text',
						'default'           => '&copy; Copyright 2021, Antek WordPress Theme',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-footer__copyright',
						'refresh_id'        => 'footer-copyright',
						'refresh_wrapper'   => true,
						'dependency'        => [
							'footer_page' => [ 0, '' ],
						],
					],
				],
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Fonts', 'antek' ),
				'controls' => [

					'theme_font'         => [
						'label'             => __( 'Content Font (Google Font)', 'antek' ),
						'default'           => 'Barlow',
						'description'       => __( 'Default font: Barlow', 'antek' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_font_choices',
					],
					'theme_font_subsets' => [
						'label'             => __( 'Fonts subset (if available)', 'antek' ),
						'default'           => 'latin-ext',
						'description'       => __( 'Default: Latin Extended', 'antek' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_google_font_subsets',
					],
				],
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Post / Page', 'antek' ),
				'controls' => [

					'post_title' => [
						'label'             => __( 'Post title placement', 'antek' ),
						'default'           => 'header',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'header'  => __( 'In Header Area', 'antek' ),
							'content' => __( 'In Content Area', 'antek' ),
						],
					],

					'sidebar_settings' => [
						'label'             => __( 'Sidebar', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'sidebar_blog'     => [
						'label'             => __( 'Sidebar in Blog and Archive', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'sidebar_post'     => [
						'label'             => __( 'Sidebar in Post / Page', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'post_page_settings' => [
						'label'             => __( 'Post settings', 'antek' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'post_layout' => [
						'label'             => __( 'Blog Layout', 'antek' ),
						'type'              => 'radio',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => 'list',
						'choices'           => [
							'grid' => __( 'Grid', 'antek' ),
							'list' => __( 'List', 'antek' ),
						],
					],

					'post_hide_category' => [
						'label'             => __( 'Hide Category', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_date'     => [
						'label'             => __( 'Hide Date', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_comment'  => [
						'label'             => __( 'Hide Comment counter', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_share'    => [
						'label'             => __( 'Hide Share Buttons', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_tags'     => [
						'label'             => __( 'Hide Tags', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_author'   => [
						'label'             => __( 'Hide Author Info', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_postnav'  => [
						'label'             => __( 'Hide Post Navigation (Prev / Next)', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_related'  => [
						'label'             => __( 'Hide Related Posts', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			! ideapark_woocommerce_on() ? null : [
				'panel'       => 'theme_settings',
				'title'       => __( 'Woocommerce Settings', 'antek' ),
				'description' => __( 'This is a settings section to change Antek WooCommerce properties.', 'antek' ),
				'controls'    => [

					'product_header' => [
						'label'             => __( 'Woocommerce product page header', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Shop', 'antek' ),
						'sanitize_callback' => 'sanitize_textarea_field',
					],

					'product_title_in_header' => [
						'label'             => __( 'Show product title in page header', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_price_block_title' => [
						'label'             => __( 'Price block title', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Total Price', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'product_price_block_tax'   => [
						'label'             => __( 'Price block tax info', 'antek' ),
						'type'              => 'text',
						'default'           => __( 'Incl. taxes', 'antek' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'shop_sidebar'              => [
						'label'             => __( 'Show sidebar on product list', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'product_sidebar'           => [
						'label'             => __( 'Show sidebar on product page', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_short_description' => [
						'label'             => __( 'Show product short description in the product list', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'show_subtotal' => [
						'label'             => __( 'Show Subtotal on the checkout page', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'star_rating_color' => [
						'label'             => __( 'Star rating color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#FFD141',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'grid_image_fit'    => [
						'label'             => __( 'Image fit in grid', 'antek' ),
						'default'           => 'cover',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'is_option'         => true,
						'choices'           => [
							'cover'   => IDEAPARK_URI . '/assets/img/thumb-cover.png',
							'contain' => IDEAPARK_URI . '/assets/img/thumb-contain.png',
						],
					],
					'subcat_image_fit'  => [
						'label'             => __( 'Image fit in grid for subcategories', 'antek' ),
						'default'           => 'cover',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'is_option'         => true,
						'choices'           => [
							'cover'   => IDEAPARK_URI . '/assets/img/thumb-cover.png',
							'contain' => IDEAPARK_URI . '/assets/img/thumb-contain.png',
						],
					],
					'product_image_fit' => [
						'label'             => __( 'Image fit on product page', 'antek' ),
						'default'           => 'cover',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'is_option'         => true,
						'choices'           => [
							'cover'   => IDEAPARK_URI . '/assets/img/thumb-cover.png',
							'contain' => IDEAPARK_URI . '/assets/img/thumb-contain.png',
						],
					],

					'is_woocommerce_on' => [
						'label'             => '',
						'description'       => '',
						'type'              => 'hidden',
						'default'           => ideapark_woocommerce_on() ? 1 : 0,
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'class'             => 'WP_Customize_Hidden_Control',
					],
				],
			],
			[
				'panel'           => 'theme_settings',
				'title'           => __( 'Social Media Links', 'antek' ),
				'description'     => __( 'Add the full url of your social media page e.g http://twitter.com/yoursite', 'antek' ),
				'refresh'         => '.c-soc',
				'refresh_wrapper' => true,
				'refresh_id'      => 'soc',
				'controls'        => [
					'facebook'  => [
						'label'             => __( 'Facebook url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'instagram' => [
						'label'             => __( 'Instagram url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'vk'        => [
						'label'             => __( 'VK url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'ok'        => [
						'label'             => __( 'OK url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'telegram'  => [
						'label'             => __( 'Telegram url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'whatsapp'  => [
						'label'             => __( 'Whatsapp url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'twitter'   => [
						'label'             => __( 'Twitter url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'youtube'   => [
						'label'             => __( 'YouTube url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'vimeo'     => [
						'label'             => __( 'Vimeo url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'snapchat'  => [
						'label'             => __( 'Snapchat url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'tiktok'    => [
						'label'             => __( 'TikTok url', 'antek' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'linkedin'  => [
						'label'             => __( 'LinkedIn url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'flickr'    => [
						'label'             => __( 'Flickr url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'pinterest' => [
						'label'             => __( 'Pinterest url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'tumblr'    => [
						'label'             => __( 'Tumblr url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'github'    => [
						'label'             => __( 'Github url', 'antek' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
				]
			],
			[
				'panel'    => 'theme_settings',
				'title'    => __( 'Other Settings', 'antek' ),
				'controls' => [
					'disable_block_editor' => [
						'label'             => __( 'Disable widget block editor', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'sticky_sidebar'       => [
						'label'             => __( 'Sticky sidebar and checkout summary', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'404_page'             => [
						'label'             => __( 'HTML block to display on 404 page', 'antek' ),
						'default'           => 0,
						'class'             => 'WP_Customize_HTML_Block_Control',
						'sanitize_callback' => 'absint',
					],
					'404_image'            => [
						'label'             => __( 'Custom image for 404 page', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'404_page' => [ 'is_empty' ],
						],
					],
					'cart_empty_button'    => [
						'label'             => __( 'Empty cart button', 'antek' ),
						'type'              => 'radio',
						'default'           => 'booking',
						'choices'           => [
							'booking' => __( 'Leads to the Booking page', 'antek' ),
							'shop'    => __( 'Leads to the Shop page', 'antek' ),
						],
						'sanitize_callback' => 'sanitize_text_field',

					],
					'cart_empty_image'     => [
						'label'             => __( 'Custom image for empty cart', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'cart_empty_favorites' => [
						'label'             => __( 'Custom image for empty favorites list', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'ajax_search'          => [
						'label'             => __( 'Ajax search', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'to_top_button'        => [
						'label'             => __( 'To Top button enable', 'antek' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'to_top_button_color'  => [
						'label'             => __( 'To Top button color', 'antek' ),
						'description'       => __( 'Default color if empty', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#edb509',
						'dependency'        => [
							'to_top_button' => [ '1' ],
						],
					],
				]
			],
			[
				'panel'       => 'theme_settings',
				'title'       => __( 'Performance', 'antek' ),
				'description' => __( 'Use these options to put your theme to a high speed as well as save your server resources!', 'antek' ),
				'controls'    => [
					'use_minified_css'          => [
						'label'             => __( 'Use minified CSS', 'antek' ),
						'description'       => __( 'Load all theme css files combined and minified into a single file', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'use_minified_js'           => [
						'label'             => __( 'Use minified JS', 'antek' ),
						'description'       => __( 'Load all theme js files combined and minified into a single file', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'load_jquery_in_footer'     => [
						'label'             => __( 'Load jQuery in footer', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'lazyload'                  => [
						'label'             => __( 'Lazy load images', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'google_fonts_display_swap' => [
						'label'             => __( 'Use parameter display=swap for Google Fonts', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'js_delay'                  => [
						'label'             => __( 'Delay JavaScript execution', 'antek' ),
						'description'       => __( 'Improves performance by delaying the execution of JavaScript until user interaction (e.g. scroll, click). ', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],

			[
				'panel'    => 'woocommerce',
				'section'  => 'woocommerce_store_notice',
				'controls' => [
					'store_notice'                  => [
						'label'             => __( 'Store notice placement', 'antek' ),
						'default'           => 'top',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'priority'          => 50,
						'choices'           => [
							'top'    => __( 'At the top of the page', 'antek' ),
							'bottom' => __( 'At the bottom of the screen (fixed)', 'antek' ),
						],
					],
					'store_notice_color'            => [
						'label'             => __( 'Store notice text color ', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'priority'          => 51,
					],
					'store_notice_background_color' => [
						'label'             => __( 'Store notice background color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#CF3540',
						'priority'          => 53,
					],
				]
			],
			[
				'section'  => 'title_tagline',
				'controls' => [
					'logo'             => [
						'label'             => __( 'Logo', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'priority'          => 50,
						'refresh'           => '.c-header__logo',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-logo',
						'refresh_callback'  => 'ideapark_set_header_bg_height_force',
					],
					'logo_mobile'      => [
						'label'             => __( 'Mobile Logo', 'antek' ),
						'description'       => __( 'Leave empty for using main Logo', 'antek' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'priority'          => 51,
						'refresh'           => '.c-header__logo',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-logo',
						'dependency'        => [
							'logo' => [ 'not_empty' ],
						],
					],
					'logo_size'        => [
						'label'             => __( 'Logo size (Desktop)', 'antek' ),
						'default'           => 170,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 70,
						'max'               => 250,
						'step'              => 1,
						'priority'          => 51,
						'refresh_css'       => '.c-header__logo',
						'refresh'           => false,
						'refresh_callback'  => 'ideapark_set_header_bg_height_force',
					],
					'logo_size_mobile' => [
						'label'             => __( 'Logo max width (Mobile)', 'antek' ),
						'default'           => 110,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 70,
						'max'               => 300,
						'step'              => 1,
						'priority'          => 103,
						'refresh_css'       => '.c-header__logo',
						'refresh'           => false,
						'refresh_callback'  => 'ideapark_set_header_bg_height_force',
					],
				],
			],
			[
				'section'  => 'background_image',
				'controls' => [
					'hide_inner_background' => [
						'label'             => __( 'Hide background on inner pages', 'antek' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

				],
			],
			[
				'section'  => 'colors',
				'controls' => [

					'headers_color' => [
						'label'             => __( 'Headers and main elements color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#222222',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'text_color' => [
						'label'             => __( 'Base text color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#222222',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'accent_color' => [
						'label'             => __( 'Accent color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#edb509',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'light_background_color' => [
						'label'             => __( 'Light background color', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#f6f6f6',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'shadow_color_desktop' => [
						'label'             => __( 'Modal window overlay color (Desktop)', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#25201E',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'shadow_color_mobile' => [
						'label'             => __( 'Modal window overlay color (Mobile)', 'antek' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#25201E',
						'sanitize_callback' => 'sanitize_text_field',
					],
				]
			],
		];

		ideapark_parse_added_blocks();

		ideapark_add_last_control();

		add_option( 'ideapark_customize', [
			'version'  => $version,
			'settings' => $ideapark_customize
		], '', 'yes' );
	}
}

if ( ! function_exists( 'ideapark_reset_theme_mods' ) ) {
	function ideapark_reset_theme_mods() {
		global $ideapark_customize;

		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['default'] ) ) {
							set_theme_mod( $control_name, $control['default'] );
							ideapark_mod_set_temp( $control_name, $control['default'] );
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'ideapark_fix_theme_mods' ) ) {
	function ideapark_fix_theme_mods() {
		if ( is_admin() && ! IDEAPARK_IS_AJAX && $GLOBALS['pagenow'] != 'wp-login.php' ) {
			if ( get_option( 'ideapark_fix_theme_mods_ver' ) != IDEAPARK_VERSION ) {
				update_option( 'ideapark_fix_theme_mods_ver', IDEAPARK_VERSION );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_init_theme_mods' ) ) {
	function ideapark_init_theme_mods() {
		global $ideapark_customize, $ideapark_customize_mods, $ideapark_customize_mods_def;

		$all_mods_default     = [];
		$all_mods_names       = [];
		$all_image_mods_names = [];
		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['default'] ) ) {
							$ideapark_customize_mods_def[ $control_name ] = $all_mods_default[ $control_name ] = $control['default'];
						}
						$all_mods_names[] = $control_name;
						if ( isset( $control['class'] ) && $control['class'] == 'WP_Customize_Image_Control' ) {
							$all_image_mods_names[] = $control_name;
						}
					}
				}
			}
		}

		$ideapark_customize_mods = get_theme_mods();

		foreach ( $all_mods_names as $name ) {
			if ( ! is_array( $ideapark_customize_mods ) || ! array_key_exists( $name, $ideapark_customize_mods ) ) {
				$ideapark_customize_mods[ $name ] = apply_filters( "theme_mod_{$name}", array_key_exists( $name, $all_mods_default ) ? $all_mods_default[ $name ] : null );
			} else {
				$ideapark_customize_mods[ $name ] = apply_filters( "theme_mod_{$name}", $ideapark_customize_mods[ $name ] );
			}
		}

		if ( is_customize_preview() && $all_image_mods_names ) {
			foreach ( $all_image_mods_names as $control_name ) {
				if ( ( $url = get_theme_mod( $control_name ) ) && ( $attachment_id = attachment_url_to_postid( $url ) ) ) {
					$params = wp_get_attachment_image_src( $attachment_id, 'full' );

					$ideapark_customize_mods[ $control_name . '__url' ]           = $params[0];
					$ideapark_customize_mods[ $control_name . '__attachment_id' ] = $attachment_id;
					$ideapark_customize_mods[ $control_name . '__width' ]         = $params[1];
					$ideapark_customize_mods[ $control_name . '__height' ]        = $params[2];
				} else {
					$ideapark_customize_mods[ $control_name . '__url' ]           = null;
					$ideapark_customize_mods[ $control_name . '__attachment_id' ] = null;
					$ideapark_customize_mods[ $control_name . '__width' ]         = null;
					$ideapark_customize_mods[ $control_name . '__height' ]        = null;
				}
			}
		}

		ideapark_fix_theme_mods();
		do_action( 'ideapark_init_theme_mods' );
	}

	if ( $GLOBALS['pagenow'] != 'wp-login.php' ) {
		add_action( 'wp_loaded', 'ideapark_init_theme_mods' );
	}
}

if ( ! function_exists( 'ideapark_mod' ) ) {
	function ideapark_mod( $mod_name ) {
		global $ideapark_customize_mods;

		if ( array_key_exists( $mod_name, $ideapark_customize_mods ) ) {
			return $ideapark_customize_mods[ $mod_name ];
		} else {
			return get_option( 'antek_mod_' . $mod_name, null );
		}
	}
}

if ( ! function_exists( 'ideapark_mod_default' ) ) {
	function ideapark_mod_default( $mod_name ) {
		global $ideapark_customize_mods_def;

		if ( array_key_exists( $mod_name, $ideapark_customize_mods_def ) ) {
			return $ideapark_customize_mods_def[ $mod_name ];
		} else {
			return null;
		}
	}
}

if ( ! function_exists( 'ideapark_mod_set_temp' ) ) {
	function ideapark_mod_set_temp( $mod_name, $value ) {
		global $ideapark_customize_mods;
		if ( $value === null && isset( $ideapark_customize_mods[ $mod_name ] ) ) {
			unset( $ideapark_customize_mods[ $mod_name ] );
		} else {
			$ideapark_customize_mods[ $mod_name ] = $value;
		}
	}
}

if ( ! function_exists( 'ideapark_register_theme_customize' ) ) {
	function ideapark_register_theme_customize( $wp_customize ) {
		global $ideapark_customize_custom_css, $ideapark_customize, $ideapark_customize_panels;

		/**
		 * @var  WP_Customize_Manager $wp_customize
		 **/

		if ( class_exists( 'WP_Customize_Control' ) ) {

			class WP_Customize_Google_API_Key_Control extends WP_Customize_Control {
				public $type = 'instagram_access_token';

				public function render_content() {
					?>
					<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?>
						<a href="<?php echo esc_url( 'https://developers.google.com/maps/documentation/javascript/get-api-key' ); ?>"
						   target="_blank"><?php echo esc_html__( 'Find it here', 'antek' ); ?></a></span>
						<textarea
							style="width:100%; height:47px;" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
					</label>
					<?php
				}
			}

			class WP_Customize_Checkbox_Set_Control extends WP_Customize_Control {
				public $type = 'checkbox-set';

				public function render_content() {
					$input_id         = '_customize-input-' . $this->id;
					$description_id   = '_customize-description-' . $this->id;
					$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

					if ( empty( $this->choices ) ) {
						return;
					}

					$name    = '_customize-radio-' . $this->id;
					$value   = $this->value();
					$checked = is_array( $value ) ? $value : explode( ',', $value );
					$value   = implode( ',', $checked );
					?>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span id="<?php echo esc_attr( $description_id ); ?>"
							  class="description customize-control-description"><?php echo ideapark_wrap( $this->description ); ?></span>
					<?php endif; ?>

					<div class="ideapark-checkbox-set-list">
						<input type="hidden"
							   class="js-ideapark-checkbox-set"
							   name="<?php echo esc_attr( $name ); ?>"
							   value="<?php echo esc_attr( $value ); ?>"
							   id="<?php echo esc_attr( $input_id . '-chk-set' ); ?>"
							<?php $this->link(); ?>
						/>
						<?php foreach ( $this->choices as $value => $label ) { ?>
							<label class="ideapark-checkbox-set-item">
								<span
									class="ideapark-checkbox-set-title" <?php echo ideapark_wrap( $describedby_attr ); ?>><?php echo esc_html( $label ); ?></span>
								<span class="ideapark-checkbox-set-value">
								<input
									class="js-ideapark-checkbox-set-item"
									type="checkbox"
									value="<?php echo esc_attr( $value ); ?>"
									<?php if ( in_array( $value, $checked ) ) { ?>checked<?php } ?>
									/>
							</span>
							</label>
						<?php } ?>
					</div>
					<?php
				}
			}

			class WP_Customize_Image_Radio_Control extends WP_Customize_Control {
				public $type = 'image-radio';

				public function render_content() {
					$input_id         = '_customize-input-' . $this->id;
					$description_id   = '_customize-description-' . $this->id;
					$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

					if ( empty( $this->choices ) ) {
						return;
					}

					$name = '_customize-radio-' . $this->id;
					?>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span id="<?php echo esc_attr( $description_id ); ?>"
							  class="description customize-control-description"><?php echo ideapark_wrap( $this->description ); ?></span>
					<?php endif; ?>

					<?php foreach ( $this->choices as $value => $label ) { ?>
						<span class="customize-inside-control-row">
						<label>
						<input
							id="<?php echo esc_attr( $input_id . '-radio-' . $value ); ?>"
							type="radio"
							<?php echo ideapark_wrap( $describedby_attr ); ?>
							value="<?php echo esc_attr( $value ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							<?php $this->link(); ?>
							<?php checked( $this->value(), $value ); ?>
							/>
						<?php echo( substr( $label, 0, 4 ) == 'http' ? '<img class="ideapark-radio-img" src="' . esc_url( $label ) . '">' : esc_html( $label ) ); ?></label>
						</span><?php
					}
				}
			}

			class WP_Customize_Number_Control extends WP_Customize_Control {
				public $type = 'number';

				public function render_content() {
					?>
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
						<input type="number" name="quantity" <?php $this->link(); ?>
						       <?php if ( ! empty( $this->input_attrs['pattern'] ) ) { ?>pattern="<?php echo esc_attr( $this->input_attrs['pattern'] ); ?>"<?php } ?>
						       <?php if ( isset( $this->input_attrs['min'] ) ) { ?>min="<?php echo esc_attr( $this->input_attrs['min'] ); ?>"<?php } ?>
						       <?php if ( isset( $this->input_attrs['max'] ) ) { ?>max="<?php echo esc_attr( $this->input_attrs['max'] ); ?>"<?php } ?>
							   value="<?php echo esc_textarea( $this->value() ); ?>" style="width:70px;">
					</label>
					<?php
				}
			}

			class WP_Customize_Category_Control extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_categories(
						[
							'name'              => '_customize-dropdown-categories-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'antek' ) . ' &mdash;',
							'option_none_value' => '0',
							'selected'          => $this->value(),
						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_Page_Control extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_pages(
						[
							'name'              => '_customize-dropdown-pages-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'antek' ) . ' &mdash;',
							'option_none_value' => '0',
							'selected'          => $this->value(),
							'post_status'       => [ 'publish', 'draft' ],

						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_HTML_Block_Control extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_pages(
						[
							'name'              => '_customize-dropdown-pages-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'antek' ) . ' &mdash;',
							'option_none_value' => '0',
							'selected'          => $this->value(),
							'post_type'         => 'html_block',
							'post_status'       => [ 'publish' ],
						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>
						<div class="ideapark-manage-blocks"><a href="' . esc_url( admin_url( 'edit.php?post_type=html_block' ) ) . '">' . esc_html__( 'Manage html blocks', 'antek' ) . '</a></div>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_HTML_Condition extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_categories(
						[
							'name'              => '_customize-dropdown-pages-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'antek' ) . ' &mdash;',
							'option_none_value' => '0',
							'hide_empty'        => 0,
							'selected'          => $this->value(),
							'taxonomy'          => 'condition',
						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>
						<div class="ideapark-manage-blocks"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=condition&post_type=catalog' ) ) . '">' . esc_html__( 'Manage conditions', 'antek' ) . '</a></div>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_Info_Control extends WP_Customize_Control {
				public $type = 'info';

				public function render_content() {
					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'</div>'
					);
				}
			}

			class WP_Customize_Warning_Control extends WP_Customize_Control {
				public $type = 'warning';

				public function render_content() {
					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="notification-message">', '</span>' ),
						'<div class="notice notice-warning ideapark-notice">',
						'</div>'
					);
				}
			}

			class WP_Customize_Text_Editor_Control extends WP_Customize_Control {
				public $type = 'text_editor';

				public function render_content() {

					if ( function_exists( 'wp_enqueue_editor' ) ) {
						wp_enqueue_editor();
					}
					ob_start();
					wp_editor(
						$this->value(), '_customize-text-editor-' . esc_attr( $this->id ), [
							'default_editor' => 'tmce',
							'wpautop'        => isset( $this->input_attrs['wpautop'] ) ? $this->input_attrs['wpautop'] : false,
							'teeny'          => isset( $this->input_attrs['teeny'] ) ? $this->input_attrs['teeny'] : false,
							'textarea_rows'  => isset( $this->input_attrs['rows'] ) && $this->input_attrs['rows'] > 1 ? $this->input_attrs['rows'] : 10,
							'editor_height'  => 16 * ( isset( $this->input_attrs['rows'] ) && $this->input_attrs['rows'] > 1 ? (int) $this->input_attrs['rows'] : 10 ),
							'tinymce'        => [
								'resize'             => false,
								'wp_autoresize_on'   => false,
								'add_unload_trigger' => false,
							],
						]
					);
					$editor_html = ob_get_contents();
					ob_end_clean();

					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="hidden"' . $this->get_link() .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) .
						' value="' . esc_textarea( $this->value() ) . '" />' .

						ideapark_wrap( $editor_html, '<div class="ideapark_text_editor">', '</div>' ) . ' 
					</span></div>'
					);

					ideapark_mod_set_temp( 'need_footer_scripts', true );
				}
			}

			class WP_Customize_Select_Control extends WP_Customize_Control {
				public $type = 'select';

				public function render_content() {
					$input_id         = '_customize-input-' . $this->id;
					$description_id   = '_customize-description-' . $this->id;
					$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
					if ( empty( $this->choices ) ) {
						return;
					}

					?>
					<?php if ( ! empty( $this->label ) ) : ?>
						<label for="<?php echo esc_attr( $input_id ); ?>"
							   class="customize-control-title"><?php echo esc_html( $this->label ); ?></label>
					<?php endif; ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span id="<?php echo esc_attr( $description_id ); ?>"
							  class="description customize-control-description"><?php echo ideapark_wrap( $this->description ); ?></span>
					<?php endif; ?>

					<select
						id="<?php echo esc_attr( $input_id ); ?>" <?php echo ideapark_wrap( $describedby_attr ); ?> <?php $this->link(); ?>>
						<?php
						$is_option_group = false;
						foreach ( $this->choices as $value => $label ) {
							if ( strpos( $value, '*' ) === 0 ) {
								if ( $is_option_group ) {
									echo ideapark_wrap( '</optgroup>' );
								}
								echo ideapark_wrap( '<optgroup label="' . $label . '">' );
								$is_option_group = true;
							} else {
								echo ideapark_wrap( '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>' );
							}

						}
						if ( $is_option_group ) {
							echo ideapark_wrap( '</optgroup>' );
						}
						?>
					</select>
					<?php
				}
			}

			class WP_Customize_Hidden_Control extends WP_Customize_Control {
				public $type = 'hidden';

				public function render_content() {
					?>
					<input type="hidden" name="_customize-hidden-<?php echo esc_attr( $this->id ); ?>" value=""
						<?php
						$this->link();
						if ( ! empty( $this->input_attrs['var_name'] ) ) {
							echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
						}
						?>
					>
					<?php
					if ( 'last_option' == $this->id && ideapark_mod( 'need_footer_scripts' ) ) {
						ideapark_mod_set_temp( 'need_footer_scripts', false );
						do_action( 'admin_print_footer_scripts' );
					}
				}
			}

			class WP_Customize_Range_Control extends WP_Customize_Control {
				public $type = 'range';

				public function render_content() {
					$show_value = ! isset( $this->input_attrs['show_value'] ) || $this->input_attrs['show_value'];
					$output     = '';

					wp_enqueue_script( 'jquery-ui-slider', false, [ 'jquery', 'jquery-ui-core' ], null, true );
					$is_range   = 'range' == $this->input_attrs['type'];
					$field_min  = ! empty( $this->input_attrs['min'] ) ? $this->input_attrs['min'] : 0;
					$field_max  = ! empty( $this->input_attrs['max'] ) ? $this->input_attrs['max'] : 100;
					$field_step = ! empty( $this->input_attrs['step'] ) ? $this->input_attrs['step'] : 1;
					$field_val  = ! empty( $value )
						? ( $value . ( $is_range && strpos( $value, ',' ) === false ? ',' . $field_max : '' ) )
						: ( $is_range ? $field_min . ',' . $field_max : $field_min );
					$output     .= '<div id="' . esc_attr( '_customize-range-' . esc_attr( $this->id ) ) . '"'
					               . ' class="ideapark_range_slider"'
					               . ' data-range="' . esc_attr( $is_range ? 'true' : 'min' ) . '"'
					               . ' data-min="' . esc_attr( $field_min ) . '"'
					               . ' data-max="' . esc_attr( $field_max ) . '"'
					               . ' data-step="' . esc_attr( $field_step ) . '"'
					               . '>'
					               . '<span class="ideapark_range_slider_label ideapark_range_slider_label_min">'
					               . esc_html( $field_min )
					               . '</span>'
					               . '<span class="ideapark_range_slider_label ideapark_range_slider_label_max">'
					               . esc_html( $field_max )
					               . '</span>';
					$values     = explode( ',', $field_val );
					for ( $i = 0; $i < count( $values ); $i ++ ) {
						$output .= '<span class="ideapark_range_slider_label ideapark_range_slider_label_cur">'
						           . esc_html( $values[ $i ] )
						           . '</span>';
					}
					$output .= '</div>';

					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="' . ( ! $show_value ? 'hidden' : 'text' ) . '"' . $this->get_link() .
						( $show_value ? ' class="ideapark_range_slider_value"' : '' ) .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) . '" />' .
						$output . ' 
					</span></div>'
					);

				}
			}

			class WP_Customize_Checklist_Control extends WP_Customize_Control {
				public $type = 'checklist';

				public function render_content() {
					$output = '';
					$value  = $this->value();

					if ( ! empty( $this->input_attrs['sortable'] ) ) {
						wp_enqueue_script( 'jquery-ui-sortable', false, [
							'jquery',
							'jquery-ui-core'
						], null, true );
					}
					$output .= '<div class="ideapark_checklist ' . ( ! empty( $this->input_attrs['max-height'] ) ? 'ideapark_checklist_scroll' : '' ) . ' ideapark_checklist_' . esc_attr( ! empty( $this->input_attrs['dir'] ) ? $this->input_attrs['dir'] : 'vertical' )
					           . ( ! empty( $this->input_attrs['sortable'] ) ? ' ideapark_sortable' : '' )
					           . '"' . ( ! empty( $this->input_attrs['max-height'] ) ? ' style="max-height: ' . trim( esc_attr( $this->input_attrs['max-height'] ) ) . 'px"' : '' )
					           . ( ! empty( $this->input_attrs['add_ajax_action'] ) ? ' data-add-ajax-action="' . esc_attr( $this->input_attrs['add_ajax_action'] ) . '"' : '' )
					           . ( ! empty( $this->input_attrs['delete_ajax_action'] ) ? ' data-delete-ajax-action="' . esc_attr( $this->input_attrs['delete_ajax_action'] ) . '"' : '' )
					           . '>';
					if ( ! is_array( $value ) ) {
						if ( ! empty( $value ) ) {
							parse_str( str_replace( '|', '&', $value ), $value );
						} else {
							$value = [];
						}
					}

					if ( ! empty( $this->input_attrs['choices_add'] ) ) {
						$choices = array_filter( $this->input_attrs['choices_add'], function ( $key ) use ( $value ) {
							return isset( $value[ $key ] );
						}, ARRAY_FILTER_USE_KEY );

						$choices = ideapark_array_merge( $value, $choices );
					} else {
						if ( ! empty( $this->input_attrs['sortable'] ) && is_array( $value ) ) {
							$value = array_filter( $value, function ( $key ) {
								return array_key_exists( $key, $this->input_attrs['choices'] );
							}, ARRAY_FILTER_USE_KEY );

							$this->input_attrs['choices'] = ideapark_array_merge( $value, $this->input_attrs['choices'] );
						}
						$choices = $this->input_attrs['choices'];
					}

					foreach ( $choices as $k => $v ) {
						$output .= '<div class="ideapark_checklist_item_label'
						           . ( ! empty( $this->input_attrs['sortable'] ) ? ' ideapark_sortable_item' : '' )
						           . '"><label>'
						           . '<input type="checkbox" value="1" data-name="' . $k . '"'
						           . ( isset( $value[ $k ] ) && 1 == (int) $value[ $k ] ? ' checked="checked"' : '' )
						           . ' />'
						           . ( substr( $v, 0, 4 ) == 'http' ? '<img src="' . esc_url( $v ) . '">' : esc_html( preg_replace( '~^[ \-]+~u', '', $v ) ) )
						           . '</label>'
						           . ( ! empty( $this->input_attrs['choices_edit'][ $k ] ) ? '<button type="button" class="ideapark_checklist_item_edit" data-control="' . esc_attr( $this->input_attrs['choices_edit'][ $k ] ) . '"><span class="dashicons dashicons-admin-generic"></span></button>' : '' )
						           . ( ! empty( $this->input_attrs['choices_delete'] ) && in_array( $k, $this->input_attrs['choices_delete'] ) || ! empty( $this->input_attrs['choices_add'] ) ? '<button type="button" class="ideapark_checklist_item_delete" data-section="' . esc_attr( $k ) . '"><span class="dashicons dashicons-no-alt"></span></button>' : '' )
						           . '</div>';
					}
					$output .= '</div>';

					$output_add = '';

					if ( ! empty( $this->input_attrs['can_add_block'] ) ) {
						$output_add .= ideapark_wrap(
							ideapark_wrap( esc_html__( 'Please reload the page to see the settings of the new blocks', 'antek' ), '<span class="notification-message">', '<br><button type="button" data-id="' . esc_attr( $this->id ) . '" class="button-primary button ideapark-customizer-reload">' . esc_html__( 'Reload', 'antek' ) . '</button></span>' ),
							'<div class="notice notice-warning ideapark-notice ideapark_checklist_add_notice">',
							'</div>'
						);
						$output_add .= '<div class="ideapark_checklist_add_wrap">';
						$output_add .= esc_html__( 'Add new block', 'antek' );
						$output_add .= '<div class="ideapark_checklist_add_inline"><select class="ideapark_checklist_add_select">';
						$output_add .= '<option value="">' . esc_html__( '- select block -', 'antek' ) . '</option>';
						foreach ( $this->input_attrs['can_add_block'] as $section_id ) {
							$output_add .= '<option value="' . esc_attr( $section_id ) . '">' . $this->input_attrs['choices'][ $section_id ] . '</option>';
						}
						$output_add .= '</select><button class="button ideapark_checklist_add_button" type="button">' . esc_html__( 'Add', 'antek' ) . '</button></div>';
						$output_add .= '</div>';
					} elseif ( ! empty( $this->input_attrs['choices_add'] ) ) {
						$output_add      .= '<div class="ideapark_checklist_add_wrap">';
						$output_add      .= esc_html__( 'Add new', 'antek' );
						$output_add      .= '<div class="ideapark_checklist_add_inline"><select class="ideapark_checklist_add_select">';
						$output_add      .= '<option value="">' . esc_html__( '- select -', 'antek' ) . '</option>';
						$is_option_group = false;
						foreach ( $this->input_attrs['choices_add'] as $section_id => $section_name ) {
							if ( strpos( $section_id, '*' ) === 0 ) {
								if ( $is_option_group ) {
									$output_add .= '</optgroup>';
								}
								$output_add      .= '<optgroup label="' . $section_name . '">';
								$is_option_group = true;
							} else {
								$output_add .= '<option value="' . esc_attr( $section_id ) . '">' . $section_name . '</option>';
							}
						}
						if ( $is_option_group ) {
							$output_add .= '</optgroup>';
						}
						$output_add .= '</select><button class="button ideapark_checklist_add_button" type="button">' . esc_html__( 'Add', 'antek' ) . '</button></div>';
						$output_add .= '</div>';
					}


					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="hidden" ' . $this->get_link() .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) . ' />' .
						$output . '</span>' . $output_add . '</div>'
					);
				}
			}
		}

		$panel_priority = 1;

		foreach ( $ideapark_customize_panels as $panel_name => $panel ) {
			$wp_customize->add_panel( $panel_name, [
				'capability'  => 'edit_theme_options',
				'title'       => ! empty( $panel['title'] ) ? $panel['title'] : '',
				'description' => ! empty( $panel['description'] ) ? $panel['description'] : '',
				'priority'    => isset( $panel['priority'] ) ? $panel['priority'] : $panel_priority ++,
			] );
		}

		foreach ( $ideapark_customize as $i_section => $section ) {
			if ( ! empty( $section['controls'] ) ) {

				$panel_name = ! empty( $section['panel'] ) ? $section['panel'] : '';

				if ( ! array_key_exists( 'section', $section ) ) {
					$wp_customize->add_section( $section_name = 'ideapark_section_' . ( ! empty( $section['section_id'] ) ? $section['section_id'] : $i_section ), [
						'panel'       => $panel_name,
						'title'       => ! empty( $section['title'] ) ? $section['title'] : '',
						'description' => ! empty( $section['description'] ) ? $section['description'] : '',
						'priority'    => isset( $section['priority'] ) ? $section['priority'] : 160 + $i_section,
					] );
				} else {
					$section_name = $section['section'];
				}

				$control_priority = 1;
				$control_ids      = [];
				$first_control    = '';
				foreach ( $section['controls'] as $control_name => $control ) {

					if ( ! empty( $control['type'] ) || ! empty( $control['class'] ) ) {

						if ( ! $first_control ) {
							$first_control = $control_name;
						}

						$a = [
							'transport' => isset( $control['transport'] ) ? $control['transport'] : ( ( isset( $section['refresh'] ) && ! isset( $control['refresh'] ) && true !== $section['refresh'] ) || ( isset( $control['refresh'] ) && true !== $control['refresh'] ) ? 'postMessage' : 'refresh' )
						];
						if ( isset( $control['default'] ) ) {
							$a['default'] = $control['default'];
						}
						if ( isset( $control['sanitize_callback'] ) ) {
							$a['sanitize_callback'] = $control['sanitize_callback'];
						} else {
							die( 'No sanitize_callback found!' . print_r( $control, true ) );
						}

						call_user_func( [ $wp_customize, 'add_setting' ], $control_name, $a );

						if ( ! IDEAPARK_IS_AJAX_HEARTBEAT ) {

							if ( ! empty( $control['choices'] ) && is_string( $control['choices'] ) ) {
								if ( function_exists( $control['choices'] ) ) {
									$control['choices'] = call_user_func( $control['choices'] );
								} else {
									$control['choices'] = [];
								}
							}

							if ( ! empty( $control['choices_add'] ) && is_string( $control['choices_add'] ) ) {
								if ( function_exists( $control['choices_add'] ) ) {
									$control['choices_add'] = call_user_func( $control['choices_add'] );
								} else {
									$control['choices_add'] = [];
								}
							}
						}

						if ( empty( $control['class'] ) ) {
							$wp_customize->add_control(
								new WP_Customize_Control(
									$wp_customize,
									$control_name,
									[
										'label'    => $control['label'],
										'section'  => $section_name,
										'settings' => ! empty( $control['settings'] ) ? $control['settings'] : $control_name,
										'type'     => $control['type'],
										'priority' => ! empty( $control['priority'] ) ? $control['priority'] : $control_priority + 1,
										'choices'  => ! empty( $control['choices'] ) ? $control['choices'] : null,
									]
								)
							);
						} else {
							if ( class_exists( $control['class'] ) ) {
								$wp_customize->add_control(
									new $control['class'](
										$wp_customize,
										$control_name,
										[
											'label'           => $control['label'],
											'section'         => $section_name,
											'settings'        => ! empty( $control['settings'] ) ? $control['settings'] : $control_name,
											'type'            => ! empty( $control['type'] ) ? $control['type'] : null,
											'priority'        => ! empty( $control['priority'] ) ? $control['priority'] : $control_priority + 1,
											'choices'         => ! empty( $control['choices'] ) ? $control['choices'] : null,
											'active_callback' => ! empty( $control['active_callback'] ) ? $control['active_callback'] : '',
											'input_attrs'     => array_merge(
												$control, [
													'value'    => ideapark_mod( $control_name ),
													'var_name' => ! empty( $control['customizer'] ) ? $control['customizer'] : '',
												]
											),
										]
									)
								);
							}
						}

						if ( ! empty( $control['description'] ) ) {
							$ideapark_customize_custom_css[ '#customize-control-' . $control_name . ( ! empty( $control['type'] ) && in_array( $control['type'], [
								'radio',
								'checkbox'
							] ) ? '' : ' .customize-control-title' ) ] = $control['description'];
						}

						$f = false;
						if ( isset( $control['refresh'] ) && is_string( $control['refresh'] )
						     &&
						     (
							     ( $is_auto_load = isset( $control['refresh_id'] ) && ideapark_customizer_check_template_part( $control['refresh_id'] ) )
							     ||
							     function_exists( $f = "ideapark_customizer_partial_refresh_" . ( isset( $control['refresh_id'] ) ? $control['refresh_id'] : $control_name ) )
						     )
						     && isset( $wp_customize->selective_refresh ) ) {
							$wp_customize->selective_refresh->add_partial(
								$control_name, [
									'selector'            => $control['refresh'],
									'settings'            => $control_name,
									'render_callback'     => $is_auto_load ? 'ideapark_customizer_load_template_part' : $f,
									'container_inclusive' => ! empty( $control['refresh_wrapper'] ),
								]
							);
						} elseif ( ! isset( $control['refresh'] ) ) {
							$control_ids[] = $control_name;
						}
					}
				}

				if ( isset( $section['refresh_id'] ) && isset( $section['refresh'] ) && is_string( $section['refresh'] )
				     &&
				     (
					     ( $is_auto_load = ideapark_customizer_check_template_part( $section['refresh_id'] ) )
					     ||
					     function_exists( "ideapark_customizer_partial_refresh_{$section['refresh_id']}" )
				     )
				     && isset( $wp_customize->selective_refresh ) ) {
					$wp_customize->selective_refresh->add_partial(
						$first_control /* first control from this section*/, [
							'selector'            => $section['refresh'],
							'settings'            => $control_ids,
							'render_callback'     => $is_auto_load ? 'ideapark_customizer_load_template_part' : "ideapark_customizer_partial_refresh_{$section['refresh_id']}",
							'container_inclusive' => ! empty( $section['refresh_wrapper'] ),
						]
					);
				}
			}
		}

		$sec = $wp_customize->get_section( 'static_front_page' );
		if ( is_object( $sec ) ) {
			$sec->priority = 87;
		}

		$sec = $wp_customize->get_panel( 'woocommerce' );
		if ( is_object( $sec ) ) {
			$sec->priority = 110;
		}

		$sec = $wp_customize->get_panel( 'nav_menus' );
		if ( is_object( $sec ) ) {
			$sec->priority = 120;
		}

		$sec = $wp_customize->get_panel( 'widgets' );
		if ( is_object( $sec ) ) {
			$sec->priority = 125;
		}
	}
}

if ( ! function_exists( 'ideapark_get_theme_dependencies' ) ) {
	function ideapark_get_theme_dependencies() {
		global $ideapark_customize;
		$result              = [
			'refresh_css'          => [],
			'dependency'           => [],
			'refresh_callback'     => [],
			'refresh_pre_callback' => []
		];
		$partial_refresh     = [];
		$css_refresh         = [];
		$css_refresh_control = [];
		foreach ( $ideapark_customize as $i_section => $section ) {
			$first_control_name = '';
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					if ( ! $first_control_name ) {
						$first_control_name = $control_name;
					}
					if ( ! empty( $control['refresh_css'] ) ) {
						$result['refresh_css'][] = $control_name;
					}
					if ( ! empty( $control['refresh'] ) && is_string( $control['refresh'] ) ) {
						$result['refresh'][ $control_name ] = $control['refresh'];
						$partial_refresh[]                  = trim( $control['refresh'] );
					} elseif ( ! empty( $control['refresh_css'] ) && is_string( $control['refresh_css'] ) ) {
						$result['refresh'][ $control_name ] = $control['refresh_css'];
					}

					if ( ! empty( $control['refresh_css'] ) && is_string( $control['refresh_css'] ) ) {
						$css_refresh[] = $selector = trim( $control['refresh_css'] );
						if ( ! array_key_exists( $selector, $css_refresh_control ) ) {
							$css_refresh_control[ $selector ] = $control_name;
						}
					}

					if ( ! empty( $control['refresh_callback'] ) && is_string( $control['refresh_callback'] ) ) {
						$result['refresh_callback'][ $control_name ] = $control['refresh_callback'];
					}

					if ( ! empty( $control['refresh_pre_callback'] ) && is_string( $control['refresh_pre_callback'] ) ) {
						$result['refresh_pre_callback'][ $control_name ] = $control['refresh_pre_callback'];
					}

					if ( ! empty( $control['dependency'] ) && is_array( $control['dependency'] ) ) {
						$result['dependency'][ $control_name ] = $control['dependency'];
					}
				}
			}

			if ( ! empty( $section['refresh'] ) && is_string( $section['refresh'] ) && $first_control_name ) {
				$result['refresh'][ $first_control_name ] = $section['refresh'];
				$partial_refresh[]                        = trim( $section['refresh'] );
			}

			if ( ! empty( $section['refresh_css'] ) && is_string( $section['refresh_css'] ) && $first_control_name ) {
				$css_refresh[] = $selector = trim( $section['refresh_css'] );
				if ( ! array_key_exists( $selector, $css_refresh_control ) ) {
					$css_refresh_control[ $selector ] = $first_control_name;
				}
			}

			if ( ! empty( $section['refresh_callback'] ) && is_string( $section['refresh_callback'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$result['refresh_callback'][ $control_name ] = $section['refresh_callback'];
				}
			}

			if ( ! empty( $section['refresh_pre_callback'] ) && is_string( $section['refresh_pre_callback'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$result['refresh_pre_callback'][ $control_name ] = $section['refresh_pre_callback'];
				}
			}
		}

		$refresh_only_css = array_diff( array_unique( $css_refresh ), array_unique( $partial_refresh ) );

		$result['refresh_only_css'] = [];
		foreach ( $refresh_only_css as $selector ) {
			$result['refresh_only_css'][ $selector ] = $css_refresh_control[ $selector ];
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_customizer_check_template_part' ) ) {
	function ideapark_customizer_check_template_part( $template ) {
		return ideapark_is_file( IDEAPARK_DIR . '/templates/' . $template . '.php' ) || ideapark_is_file( IDEAPARK_DIR . '/' . $template . '.php' );
	}
}

if ( ! function_exists( 'ideapark_customizer_load_template_part' ) ) {
	function ideapark_customizer_load_template_part( $_control ) {
		global $ideapark_customize;
		$is_found = false;
		foreach ( $ideapark_customize as $i_section => $section ) {
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$is_found = $control_name == $_control->id;
					if ( $is_found && ! empty( $control['refresh_id'] ) ) {
						ob_start();
						if ( ideapark_is_file( IDEAPARK_DIR . '/templates/' . $control['refresh_id'] . '.php' ) ) {
							ideapark_get_template_part( 'templates/' . $control['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
						}
						if ( ideapark_is_file( IDEAPARK_DIR . '/' . $control['refresh_id'] . '.php' ) ) {
							ideapark_get_template_part( $control['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
						}
						$output = ob_get_contents();
						ob_end_clean();

						return $output;
					}
					if ( $is_found ) {
						break;
					}
				}
			}
			if ( $is_found && ! empty( $section['refresh_id'] ) ) {
				ob_start();
				if ( ideapark_is_file( IDEAPARK_DIR . '/templates/' . $section['refresh_id'] . '.php' ) ) {
					ideapark_get_template_part( 'templates/' . $section['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
				}
				if ( ideapark_is_file( IDEAPARK_DIR . '/' . $section['refresh_id'] . '.php' ) ) {
					ideapark_get_template_part( $section['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
				}
				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_customizer_get_template_part' ) ) {
	function ideapark_customizer_get_template_part( $template ) {
		ob_start();
		get_template_part( $template );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

if ( ! function_exists( 'ideapark_customizer_partial_refresh_top_menu' ) ) {
	function ideapark_customizer_partial_refresh_top_menu() {
		return ideapark_customizer_get_template_part( 'templates/home-top-menu' );
	}
}

if ( ! function_exists( 'ideapark_expanded_alowed_tags' ) ) {
	function ideapark_expanded_alowed_tags() {
		$my_allowed = wp_kses_allowed_html( 'post' );

		$my_allowed['iframe'] = [
			'src'             => [],
			'height'          => [],
			'width'           => [],
			'frameborder'     => [],
			'allowfullscreen' => [],
			'style'           => [],
		];

		return $my_allowed;
	}
}

if ( ! function_exists( 'ideapark_sanitize_embed_field' ) ) {
	function ideapark_sanitize_embed_field( $input ) {
		return wp_kses( $input, ideapark_expanded_alowed_tags() );
	}
}

if ( ! function_exists( 'ideapark_parse_checklist' ) ) {
	function ideapark_parse_checklist( $str ) {
		$values = [];
		if ( ! empty( $str ) ) {
			parse_str( str_replace( '|', '&', $str ), $values );
		}

		return $values;
	}
}

if ( ! function_exists( 'ideapark_sanitize_base' ) ) {
	function ideapark_sanitize_base( $input ) {
		$s = (string) sanitize_title( $input );

		return $s ? $s : new WP_Error( 'nan', __( 'The field cannot be empty.', 'antek' ) );
	}
}


if ( ! function_exists( 'ideapark_sanitize_checkbox' ) ) {
	function ideapark_sanitize_checkbox( $input ) {
		if ( $input ):
			$output = true;
		else:
			$output = false;
		endif;

		return $output;
	}
}

if ( ! function_exists( 'ideapark_customize_admin_style' ) ) {
	function ideapark_customize_admin_style() {
		global $ideapark_customize_custom_css;
		if ( ! empty( $ideapark_customize_custom_css ) && is_array( $ideapark_customize_custom_css ) ) {
			?>
			<style type="text/css">
				<?php foreach ( $ideapark_customize_custom_css as $style_name => $text ) { ?>
				<?php echo esc_attr( $style_name ); ?>:after {
					content: "<?php echo esc_attr($text) ?>";
				}

				<?php } ?>
			</style>
			<?php
		}
	}
}

if ( ! function_exists( 'ideapark_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'ideapark_customizer_preview_js' );
	function ideapark_customizer_preview_js() {
		wp_enqueue_script(
			'ideapark-customizer-preview',
			IDEAPARK_URI . '/assets/js/admin-customizer-preview.js',
			[ 'customize-preview' ], null, true
		);
	}
}

if ( ! function_exists( 'ideapark_get_all_atributes' ) ) {
	function ideapark_get_all_atributes() {
		$attribute_array      = [ '' => '' ];
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( $taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$attribute_array[ $taxonomy ] = $tax->attribute_name;
				}
			}
		}

		return $attribute_array;
	}
}

if ( ! function_exists( 'ideapark_get_all_fonts' ) ) {
	function ideapark_get_all_fonts() {
		$google_fonts = ideapark_get_google_fonts();

		/**
		 * Allow for developers to modify the full list of fonts.
		 *
		 * @param array $fonts The list of all fonts.
		 *
		 * @since 1.3.0.
		 *
		 */
		return apply_filters( 'ideapark_all_fonts', $google_fonts );
	}
}

if ( ! function_exists( 'ideapark_get_font_choices' ) ) {
	function ideapark_get_font_choices() {
		$fonts   = ideapark_get_all_fonts();
		$choices = [];

		// Repackage the fonts into value/label pairs
		foreach ( $fonts as $key => $font ) {
			$choices[ $key ] = $font['label'];
		}

		return $choices;
	}
}

if ( ! function_exists( 'ideapark_get_google_font_uri' ) ) {
	function ideapark_get_google_font_uri( $fonts ) {

		$fonts = array_unique( $fonts );
		$hash  = md5( implode( ',', $fonts ) . '--' . IDEAPARK_VERSION );

		if ( ( $data = get_option( 'ideapark_google_font_uri' ) ) && ! empty( $data['version'] ) && ! empty( $data['uri'] ) ) {
			if ( $data['version'] == $hash ) {
				return $data['uri'];
			} else {
				delete_option( 'ideapark_google_font_uri' );
			}
		}

		$allowed_fonts = ideapark_get_google_fonts();
		$family        = [];

		foreach ( $fonts as $font ) {
			$font = trim( $font );

			if ( array_key_exists( $font, $allowed_fonts ) ) {
				$filter   = [ '200', '300', 'regular', '500', '600', '700', '900' ];
				$family[] = urlencode( $font . ':' . join( ',', ideapark_choose_google_font_variants( $font, $allowed_fonts[ $font ]['variants'], $filter ) ) );
			}
		}

		if ( empty( $family ) ) {
			return '';
		} else {
			$request = '//fonts.googleapis.com/css?family=' . implode( rawurlencode( '|' ), $family );
		}

		$subset = ideapark_mod( 'theme_font_subsets' );

		if ( 'all' === $subset ) {
			$subsets_available = ideapark_get_google_font_subsets();

			unset( $subsets_available['all'] );

			$subsets = array_keys( $subsets_available );
		} else {
			$subsets = [
				'latin',
				$subset,
			];
		}

		if ( ! empty( $subsets ) ) {
			$request .= urlencode( '&subset=' . join( ',', $subsets ) );
		}

		if ( ideapark_mod( 'google_fonts_display_swap' ) ) {
			$request .= '&display=swap';
		}

		add_option( 'ideapark_google_font_uri', [
			'version' => $hash,
			'uri'     => esc_url( $request )
		], '', 'yes' );

		return esc_url( $request );
	}
}

if ( ! function_exists( 'ideapark_get_google_font_subsets' ) ) {
	function ideapark_get_google_font_subsets() {
		global $_ideapark_google_fonts_subsets;

		$list = [
			'all' => esc_html__( 'All', 'antek' ),
		];

		foreach ( $_ideapark_google_fonts_subsets as $subset ) {
			$name = ucfirst( trim( $subset ) );
			if ( preg_match( '~-ext$~', $name ) ) {
				$name = preg_replace( '~-ext$~', ' ' . esc_html__( 'Extended', 'antek' ), $name );
			}
			$list[ $subset ] = esc_html( $name );
		}

		return $list;
	}
}

if ( ! function_exists( 'ideapark_choose_google_font_variants' ) ) {
	function ideapark_choose_google_font_variants( $font, $variants = [], $filter = [ 'regular', '700' ] ) {
		$chosen_variants = [];
		if ( empty( $variants ) ) {
			$fonts = ideapark_get_google_fonts();

			if ( array_key_exists( $font, $fonts ) ) {
				$variants = $fonts[ $font ]['variants'];
			}
		}

		foreach ( $filter as $var ) {
			if ( in_array( $var, $variants ) && ! array_key_exists( $var, $chosen_variants ) ) {
				$chosen_variants[] = $var;
			}
		}

		if ( empty( $chosen_variants ) ) {
			$variants[0];
		}

		return apply_filters( 'ideapark_font_variants', array_unique( $chosen_variants ), $font, $variants );
	}
}

if ( ! function_exists( 'ideapark_sanitize_font_choice' ) ) {
	function ideapark_sanitize_font_choice( $value ) {
		if ( is_int( $value ) ) {
			// The array key is an integer, so the chosen option is a heading, not a real choice
			return '';
		} else if ( array_key_exists( $value, ideapark_get_font_choices() ) ) {
			return $value;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'ideapark_customizer_banners' ) ) {
	function ideapark_customizer_banners() {
		$result = [];
		if ( $banners = get_posts( [
			'posts_per_page'   => - 1,
			'post_type'        => 'banner',
			'meta_key'         => '_thumbnail_id',
			'suppress_filters' => false,
			'order'            => 'ASC',
			'orderby'          => 'menu_order'
		] ) ) {
			foreach ( $banners as $banner ) {
				$attachment_id = get_post_thumbnail_id( $banner->ID );
				$image         = wp_get_attachment_image_url( $attachment_id );
				if ( $image ) {
					$result[ $banner->ID ] = $image;
				} elseif ( ! empty( $banner->post_title ) ) {
					$result[ $banner->ID ] = $banner->post_title;
				} elseif ( $image_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) ) {
					$result[ $banner->ID ] = $image_alt;
				} else {
					$result[ $banner->ID ] = '#' . $banner->ID;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_customizer_product_tab_list' ) ) {
	function ideapark_customizer_product_tab_list() {
		$list = [
			'*main'                 => esc_html__( 'Main', 'antek' ),
			'featured_products'     => esc_html__( 'Featured Products', 'antek' ),
			'sale_products'         => esc_html__( 'Sale Products', 'antek' ),
			'best_selling_products' => esc_html__( 'Best-Selling Products', 'antek' ),
			'recent_products'       => esc_html__( 'Recent Products', 'antek' ),
			'*categories'           => esc_html__( 'Categories', 'antek' ),
		];

		$args = [
			'taxonomy'     => 'product_cat',
			'orderby'      => 'term_group',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
			'exclude'      => get_option( 'default_product_cat' ),
		];
		if ( $all_categories = get_categories( $args ) ) {

			$category_name   = [];
			$category_parent = [];
			foreach ( $all_categories as $cat ) {
				$category_name[ $cat->term_id ]    = esc_html( $cat->name );
				$category_parent[ $cat->parent ][] = $cat->term_id;
			}

			$get_category = function ( $parent = 0, $prefix = '' ) use ( &$list, &$category_parent, &$category_name, &$get_category ) {
				if ( array_key_exists( $parent, $category_parent ) ) {
					$categories = $category_parent[ $parent ];
					foreach ( $categories as $category_id ) {
						$list[ $category_id ] = $prefix . $category_name[ $category_id ];
						$get_category( $category_id, $prefix . ' - ' );
					}
				}
			};

			$get_category();
		}

		return $list;
	}
}

if ( ! function_exists( 'ideapark_add_last_control' ) ) {
	function ideapark_add_last_control() {
		global $ideapark_customize;

		$ideapark_customize[ sizeof( $ideapark_customize ) - 1 ]['controls']['last_option'] = [
			'label'             => '',
			'description'       => '',
			'type'              => 'hidden',
			'default'           => '',
			'sanitize_callback' => 'ideapark_sanitize_checkbox',
			'class'             => 'WP_Customize_Hidden_Control',
		];
	}
}

if ( ! function_exists( 'ideapark_ajax_customizer_add_section' ) ) {
	function ideapark_ajax_customizer_add_section() {
		if ( current_user_can( 'customize' ) && ! empty( $_POST['section'] ) ) {
			if ( $section = ideapark_add_new_section( $_POST['section'] ) ) {
				wp_send_json( $section );
			} else {
				wp_send_json( [ 'error' => esc_html__( 'Something went wrong...', 'antek' ) ] );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_ajax_customizer_delete_section' ) ) {
	function ideapark_ajax_customizer_delete_section() {
		if ( current_user_can( 'customize' ) && ! empty( $_POST['section'] ) ) {
			if ( $section = ideapark_delete_section( $_POST['section'] ) ) {
				wp_send_json( [ 'success' => 1 ] );
			} else {
				wp_send_json( [ 'error' => esc_html__( 'Something went wrong...', 'antek' ) ] );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_parse_added_blocks' ) ) {
	function ideapark_parse_added_blocks() {
		global $ideapark_customize;
		if ( $added_blocks = get_option( 'ideapark_added_blocks' ) ) {
			foreach ( $ideapark_customize as $section_index => $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( ! empty( $section['panel'] ) && ! empty( $control['can_add_block'] ) && ! empty( $control['type'] ) && $control['type'] == 'checklist' && array_key_exists( $section['panel'], $added_blocks ) ) {
							foreach ( $added_blocks[ $section['panel'] ] as $item ) {
								$section_orig_id   = $item['section_id'];
								$index             = $item['index'];
								$checklist_control = &$ideapark_customize[ $section_index ]['controls'][ $control_name ];

								foreach ( $ideapark_customize as $_section ) {
									if ( ! empty( $_section['section_id'] ) && $_section['section_id'] == $section_orig_id ) {
										$section_new               = $_section;
										$section_new['section_id'] .= '-' . $index;
										$section_new['title']      .= ' - ' . $index;
										if ( ! empty( $section_new['refresh'] ) ) {
											$section_new['refresh'] .= '-' . $index;
										}
										$new_controls = [];
										if ( ! empty( $section_new['controls'] ) ) {
											foreach ( $section_new['controls'] as $_control_name => $_control ) {
												if ( ! empty( $_control['dependency'] ) ) {
													foreach ( $_control['dependency'] as $key => $val ) {
														if ( $key == $control_name ) {
															$_control['dependency'][ $key ] = [ 'search!=' . $section_orig_id . '-' . $index . '=1' ];
														} elseif ( array_key_exists( $key, $_section['controls'] ) ) {
															$_control['dependency'][ $key . '_' . $index ] = $val;
															unset( $_control['dependency'][ $key ] );
														}
													}
												}
												$new_controls[ $_control_name . '_' . $index ] = $_control;
											}
											$section_new['controls'] = $new_controls;
										}
										$ideapark_customize[] = $section_new;
										break;
									}
								}

								$checklist_control['default']                                    .= '|' . $section_orig_id . '-' . $index . '=0';
								$checklist_control['choices'][ $section_orig_id . '-' . $index ] = $checklist_control['choices'][ $section_orig_id ] . ' - ' . $index;
								if ( ! empty( $checklist_control['choices_edit'][ $section_orig_id ] ) ) {
									$checklist_control['choices_edit'][ $section_orig_id . '-' . $index ] = $checklist_control['choices_edit'][ $section_orig_id ] . '_' . $index;
								}
								if ( empty( $checklist_control['choices_delete'] ) ) {
									$checklist_control['choices_delete'] = [];
								}
								$checklist_control['choices_delete'][] = $section_orig_id . '-' . $index;
							}
						}
					}
				}
			}
		}

		if ( ( $languages = apply_filters( 'wpml_active_languages', [] ) ) && sizeof( $languages ) >= 2 ) {
			foreach ( $ideapark_customize as $section_index => &$section ) {
				if ( ! empty( $section['controls'] ) && isset( $section['controls']['theme_font'] ) ) {
					$orig_controls = $section['controls'];
					$default_lang  = apply_filters( 'wpml_default_language', null );
					foreach ( $languages as $lang_code => $lang ) {
						if ( $lang_code != $default_lang ) {
							$section['controls'][ 'header_font_lang_' . $lang_code ] = [
								'label'             => __( 'Fonts for', 'antek' ) . ' ' . $lang['native_name'],
								'class'             => 'WP_Customize_Info_Control',
								'sanitize_callback' => 'sanitize_text_field',
							];
							foreach ( $orig_controls as $control_name => $control ) {
								$section['controls'][ $control_name . '_' . $lang_code ] = $control;
							}
						}
					}
					break;
				}
			}
		}
	}
}

if ( ! function_exists( 'ideapark_delete_section' ) ) {
	function ideapark_delete_section( $section_id ) {
		$added_blocks = get_option( 'ideapark_added_blocks' );
		$is_changed   = false;
		if ( ! empty( $added_blocks ) ) {
			foreach ( $added_blocks as $panel_name => $items ) {
				foreach ( $items as $item_index => $item ) {
					if ( $item['section_id'] . '-' . $item['index'] == $section_id ) {
						unset( $added_blocks[ $panel_name ][ $item_index ] );
						$is_changed = true;
						break;
					}
				}
			}
		}
		if ( $is_changed ) {
			if ( ! empty( $added_blocks ) ) {
				update_option( 'ideapark_added_blocks', $added_blocks );
			} else {
				delete_option( 'ideapark_added_blocks' );
			}
			delete_option( 'ideapark_customize' );
		}

		return $is_changed;
	}
}

if ( ! function_exists( 'ideapark_add_new_section' ) ) {
	function ideapark_add_new_section( $section_orig_id ) {
		global $ideapark_customize;
		$added_blocks = get_option( 'ideapark_added_blocks' );
		if ( empty( $added_blocks ) ) {
			$added_blocks = [];
		}
		$section_name = '';
		$section_id   = '';
		foreach ( $ideapark_customize as $section ) {
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					if ( ! empty( $section['panel'] ) && ! empty( $control['can_add_block'] ) && ! empty( $control['type'] ) && $control['type'] == 'checklist' && ! empty( $control['can_add_block'] ) && in_array( $section_orig_id, $control['can_add_block'] ) ) {
						if ( array_key_exists( $section['panel'], $added_blocks ) ) {
							$index = 2;
							foreach ( $added_blocks[ $section['panel'] ] as $item ) {
								if ( $item['section_id'] == $section_orig_id && $item['index'] == $index ) {
									$index ++;
								}
							}
						} else {
							$index = 2;

							$added_blocks[ $section['panel'] ] = [];
						}
						$added_blocks[ $section['panel'] ][] = [
							'section_id' => $section_orig_id,
							'index'      => $index
						];
						$section_name                        = $control['choices'][ $section_orig_id ] . ' - ' . $index;
						$section_id                          = $section_orig_id . '-' . $index;
						break;
					}
				}
			}
		}

		if ( ! empty( $added_blocks ) ) {
			update_option( 'ideapark_added_blocks', $added_blocks );
		} else {
			delete_option( 'ideapark_added_blocks' );
		}

		delete_option( 'ideapark_customize' );

		return $section_name && $section_id ? [
			'name' => $section_name,
			'id'   => $section_id
		] : false;
	}
}

$_ideapark_google_fonts_cache   = false;
$_ideapark_google_fonts_subsets = [];

if ( ! function_exists( 'ideapark_get_google_fonts' ) ) {
	function ideapark_get_google_fonts() {
		global $_ideapark_google_fonts_cache, $_ideapark_google_fonts_subsets;

		if ( $_ideapark_google_fonts_cache ) {
			return $_ideapark_google_fonts_cache;
		}

		if ( ( $data = get_option( 'ideapark_google_fonts' ) ) && ! empty( $data['version'] ) && ! empty( $data['list'] ) && ! empty( $data['subsets'] ) ) {
			if ( $data['version'] == IDEAPARK_VERSION ) {
				$_ideapark_google_fonts_cache   = $data['list'];
				$_ideapark_google_fonts_subsets = $data['subsets'];

				return $_ideapark_google_fonts_cache;
			} else {
				delete_option( 'ideapark_google_fonts' );
			}
		}

		$decoded_google_fonts = json_decode( ideapark_fgc( IDEAPARK_DIR . '/includes/customize/webfonts.json' ), true );
		$webfonts             = [];
		foreach ( $decoded_google_fonts['items'] as $key => $value ) {
			$font_family                          = $decoded_google_fonts['items'][ $key ]['family'];
			$webfonts[ $font_family ]             = [];
			$webfonts[ $font_family ]['label']    = $font_family;
			$webfonts[ $font_family ]['variants'] = $decoded_google_fonts['items'][ $key ]['variants'];
			$webfonts[ $font_family ]['subsets']  = $decoded_google_fonts['items'][ $key ]['subsets'];
			$_ideapark_google_fonts_subsets       = array_unique( array_merge( $_ideapark_google_fonts_subsets, $decoded_google_fonts['items'][ $key ]['subsets'] ) );
		}

		sort( $_ideapark_google_fonts_subsets );
		$_ideapark_google_fonts_cache = apply_filters( 'ideapark_get_google_fonts', $webfonts );

		add_option( 'ideapark_google_fonts', [
			'version' => IDEAPARK_VERSION,
			'list'    => $_ideapark_google_fonts_cache,
			'subsets' => $_ideapark_google_fonts_subsets
		], '', 'yes' );

		return $_ideapark_google_fonts_cache;
	}
}

if ( ! function_exists( 'ideapark_clear_customize_cache' ) ) {
	function ideapark_clear_customize_cache() {
		global $ideapark_customize;
		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['class'] ) && $control['class'] == 'WP_Customize_Image_Control' ) {
							if ( ( $url = get_theme_mod( $control_name ) ) && ( $attachment_id = attachment_url_to_postid( $url ) ) ) {
								$params = wp_get_attachment_image_src( $attachment_id, 'full' );
								set_theme_mod( $control_name . '__url', $params[0] );
								set_theme_mod( $control_name . '__attachment_id', $attachment_id );
								set_theme_mod( $control_name . '__width', $params[1] );
								set_theme_mod( $control_name . '__height', $params[2] );
							} else {
								remove_theme_mod( $control_name . '__url' );
								remove_theme_mod( $control_name . '__attachment_id' );
								remove_theme_mod( $control_name . '__width' );
								remove_theme_mod( $control_name . '__height' );
							}
						}
						if ( ! empty( $control['is_option'] ) ) {
							$val = get_theme_mod( $control_name );
							if ( $val === null && isset( $control['default'] ) ) {
								$val = $control['default'];
							}
							if ( $val !== null ) {
								update_option( 'antek_mod_' . $control_name, $val );
							} else {
								delete_option( 'antek_mod_' . $control_name );
							}
						}
					}
				}
			}
		}

		delete_option( 'ideapark_customize' );
		delete_option( 'ideapark_google_fonts' );
		delete_option( 'ideapark_google_font_uri' );
		if ( ( $languages = apply_filters( 'wpml_active_languages', [] ) ) && sizeof( $languages ) >= 2 ) {
			foreach ( $languages as $lang_code => $lang ) {
				delete_option( 'ideapark_google_font_uri' . '_' . $lang_code );
			}
		}
		delete_option( 'ideapark_styles_hash' );
		delete_option( 'ideapark_editor_styles_hash' );
		ideapark_init_theme_customize();
		ideapark_editor_style();
	}
}

if ( ! function_exists( 'ideapark_mod_hex_color_norm' ) ) {
	function ideapark_mod_hex_color_norm( $option, $default = 'inherit' ) {
		if ( preg_match( '~^\#[0-9A-F]{3,6}$~i', $option ) ) {
			return $option;
		} elseif ( preg_match( '~^\#[0-9A-F]{3,6}$~i', $color = '#' . ltrim( ideapark_mod( $option ), '#' ) ) ) {
			return $color;
		} else {
			return $default;
		}
	}
}

if ( ! function_exists( 'ideapark_hex_to_rgb_overlay' ) ) {
	function ideapark_hex_to_rgb_overlay( $hex_color_1, $hex_color_2, $alpha_2 ) {
		list( $r_1, $g_1, $b_1 ) = sscanf( $hex_color_1, "#%02x%02x%02x" );
		list( $r_2, $g_2, $b_2 ) = sscanf( $hex_color_2, "#%02x%02x%02x" );

		$r = min( round( $alpha_2 * $r_2 + ( 1 - $alpha_2 ) * $r_1 ), 255 );
		$g = min( round( $alpha_2 * $g_2 + ( 1 - $alpha_2 ) * $g_1 ), 255 );
		$b = min( round( $alpha_2 * $b_2 + ( 1 - $alpha_2 ) * $b_1 ), 255 );

		return "rgb($r, $g, $b)";
	}
}

if ( ! function_exists( 'ideapark_hex_lighting' ) ) {
	function ideapark_hex_lighting( $hex_color_1 ) {
		list( $r_1, $g_1, $b_1 ) = sscanf( $hex_color_1, "#%02x%02x%02x" );

		return 0.299 * $r_1 + 0.587 * $g_1 + 0.114 * $b_1;
	}
}

if ( ! function_exists( 'ideapark_hex_to_rgb_shift' ) ) {
	function ideapark_hex_to_rgb_shift( $hex_color, $k = 1 ) {
		list( $r, $g, $b ) = sscanf( $hex_color, "#%02x%02x%02x" );

		$r = min( round( $r * $k ), 255 );
		$g = min( round( $g * $k ), 255 );
		$b = min( round( $b * $k ), 255 );

		return "rgb($r, $g, $b)";
	}
}

if ( ! function_exists( 'ideapark_hex_to_rgba' ) ) {
	function ideapark_hex_to_rgba( $hex_color, $opacity = 1 ) {
		list( $r, $g, $b ) = sscanf( $hex_color, "#%02x%02x%02x" );

		return "rgba($r, $g, $b, $opacity)";
	}
}

if ( ! function_exists( 'ideapark_set_theme_elementor_settings' ) ) {
	function ideapark_set_theme_elementor_settings() {
		ideapark_ra( 'elementor/core/files/clear_cache', 'ideapark_set_theme_elementor_settings', 2 );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		if ( ideapark_is_elementor() && ( $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit() ) ) {
			if ( $kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id() ) {
				$kit_settings                                   = $kit->get_settings();
				$kit_settings['container_width_tablet']['size'] = $kit_settings['viewport_tablet'] = 1169;
				$kit_settings['container_width']['size']        = 1140;
				$kit_settings['space_between_widgets']['size']  = 0;

				if ( ! empty( $kit_settings['system_colors'] ) ) {
					/**
					 * @var $text_color            string
					 * @var $text_color_light      string
					 * @var $background_color      string
					 * @var $accent_color          string
					 */
					extract( ideapark_theme_colors() );
					foreach ( $kit_settings['system_colors'] as $index => $color ) {
						switch ( $color['_id'] ) {
							case 'primary':
								$kit_settings['system_colors'][ $index ]['color'] = $text_color;
								$kit_settings['system_colors'][ $index ]['title'] = esc_html__( 'Headers', 'antek' );
								break;
							case 'secondary':
								$kit_settings['system_colors'][ $index ]['color'] = $background_color;
								$kit_settings['system_colors'][ $index ]['title'] = esc_html__( 'Background', 'antek' );
								break;
							case 'text':
								$kit_settings['system_colors'][ $index ]['color'] = $text_color_light;
								break;
							case 'accent':
								$kit_settings['system_colors'][ $index ]['color'] = $accent_color;
								break;
						}
					}
				}

				$page_settings_manager = Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
				$page_settings_manager->save_settings( $kit_settings, $kit_id );
			}
		}
	}

	add_action( 'after_switch_theme', 'ideapark_set_theme_elementor_settings', 2 );
	add_action( 'after_update_theme_late', 'ideapark_set_theme_elementor_settings', 2 );
	add_action( 'elementor/core/files/clear_cache', 'ideapark_set_theme_elementor_settings', 2 );
}

if ( ! function_exists( 'ideapark_mce4_options' ) ) {
	function ideapark_mce4_options( $init ) {

		$background_color = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'background_color', '#FFFFFF' ) ), '#' );
		$text_color       = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'text_color', '#000000' ) ), '#' );
		$accent_color     = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'accent_color', 'inherit' ) ), '#' );
		$headers_color    = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'headers_color', 'inherit' ) ), '#' );

		$default_colours = '
			"000000", "Black",
			"993300", "Burnt orange",
			"333300", "Dark olive",
			"003300", "Dark green",
			"003366", "Dark azure",
			"000080", "Navy Blue",
			"333399", "Indigo",
			"333333", "Very dark gray",
			"800000", "Maroon",
			"FF6600", "Orange",
			"808000", "Olive",
			"008000", "Green",
			"008080", "Teal",
			"0000FF", "Blue",
			"666699", "Grayish blue",
			"808080", "Gray",
			"FF0000", "Red",
			"FF9900", "Amber",
			"99CC00", "Yellow green",
			"339966", "Sea green",
			"33CCCC", "Turquoise",
			"3366FF", "Royal blue",
			"800080", "Purple",
			"999999", "Medium gray",
			"FF00FF", "Magenta",
			"FFCC00", "Gold",
			"FFFF00", "Yellow",
			"00FF00", "Lime",
			"00FFFF", "Aqua",
			"00CCFF", "Sky blue",
			"993366", "Brown",
			"C0C0C0", "Silver",
			"FF99CC", "Pink",
			"FFCC99", "Peach",
			"FFFF99", "Light yellow",
			"CCFFCC", "Pale green",
			"CCFFFF", "Pale cyan",
			"99CCFF", "Light sky blue",
			"CC99FF", "Plum",
			"FFFFFF", "White"
		';

		$custom_colours = "
			\"$headers_color\", \"Header color\",
			\"$text_color\", \"Text color\",
			\"$accent_color\", \"Accent color\",
			\"$background_color\", \"Background color\"
		";

		$init['textcolor_map'] = '[' . $default_colours . ', ' . $custom_colours . ']';

		$init['textcolor_rows'] = 6;

		return $init;
	}
}

add_filter( 'elementor/editor/localize_settings', function ( $config ) {
	$background_color = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'background_color', '#FFFFFF' ) ), '#' );
	$text_color       = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'text_color', '#000000' ) ), '#' );
	$accent_color     = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'accent_color', 'inherit' ) ), '#' );
	$headers_color    = ltrim( esc_attr( ideapark_mod_hex_color_norm( 'headers_color', 'inherit' ) ), '#' );

	$t = [];
	$c = &$config['initial_document']['panel']['elements_categories'];
	foreach ( $c as $name => $value ) {
		if ( ! in_array( $name, [ 'basic', 'ideapark-elements' ] ) ) {
			$t[ $name ] = $value;
			unset( $c[ $name ] );
		}
	}
	foreach ( $t as $name => $value ) {
		$c[ $name ] = $value;
	}

	$config['default_schemes']['color']['items'] = [
		'1' => $headers_color,
		'2' => $background_color,
		'3' => $text_color,
		'4' => $accent_color
	];

	$config['default_schemes']['color-picker']['items'] = [
		'1' => '#' . $headers_color,
		'2' => '#f26e21',
		'3' => '#' . $background_color,
		'4' => '#555555',
		'5' => '#' . $text_color,
		'6' => '#FFFFFF',
		'7' => '#' . $text_color,
		'8' => '#' . $accent_color,
	];

	return $config;
}, 99 );

add_action( 'init', 'ideapark_init_theme_customize', 0 );
add_action( 'customize_register', 'ideapark_register_theme_customize', 100 );
add_action( 'customize_controls_print_styles', 'ideapark_customize_admin_style' );
add_action( 'customize_save_after', 'ideapark_clear_customize_cache', 100 );
add_action( 'after_update_theme_late', 'ideapark_clear_customize_cache', 100 );
add_action( 'wp_ajax_ideapark_customizer_add_section', 'ideapark_ajax_customizer_add_section' );
add_action( 'wp_ajax_ideapark_customizer_delete_section', 'ideapark_ajax_customizer_delete_section' );
add_filter( 'tiny_mce_before_init', 'ideapark_mce4_options' );