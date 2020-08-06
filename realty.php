<?php
/*
Plugin Name: Realty by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/realty/
Description: Create your personal real estate WordPress website. Sell, rent and buy properties. Add, search and browse listings easily.
Author: BestWebSoft
Text Domain: realty
Domain Path: /languages
Version: 1.1.5
Author URI: https://bestwebsoft.com/
License: GPLv3 or later
*/

/*  © Copyright 2020  BestWebSoft  ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add option page in admin menu */
if ( ! function_exists( 'rlt_admin_menu' ) ) {
	function rlt_admin_menu() {
		global $submenu, $rlt_plugin_info, $wp_version;

		if ( isset( $submenu['edit.php?post_type=property'] ) ) {
			$submenu['edit.php?post_type=property'][] = add_submenu_page(
				'edit.php?post_type=property',
				__( 'Features', 'realty' ),
				__( 'Features', 'realty' ),
				'manage_options',
				'rlt_features',
				'rlt_features_demo'
			);
			$submenu['edit.php?post_type=property'][] = add_submenu_page(
				'edit.php?post_type=property',
				__( 'Agents', 'realty' ),
				__( 'Agents', 'realty' ),
				'manage_options',
				'rlt_agents',
				'rlt_agents_demo'
			);

			$submenu['edit.php?post_type=property'][] = add_submenu_page(
				'edit.php?post_type=property',
				__( 'Realty Settings', 'realty' ),
				__( 'Settings', 'realty' ),
				'manage_options',
				'realty_settings',
				'rlt_settings_page'
			);

			$submenu['edit.php?post_type=property'][] = add_submenu_page(
				'edit.php?post_type=property',
				'BWS Panel',
				'BWS Panel',
				'manage_options',
				'rlt-bws-panel',
				'bws_add_menu_render'
			);

            $submenu['edit.php?post_type=property'][] = array(
                '<span style="color:#d86463"> ' . __( 'Upgrade to Pro', 'realty' ) . '</span>',
                'manage_options',
                'https://bestwebsoft.com/products/wordpress/plugins/realty/?k=fdac994c203b41e499a2818c409ff2bc&pn=205&v=' . $rlt_plugin_info["Version"] . '&wp_v=' . $wp_version
            );
		}

		add_action( 'load-post.php', 'rlt_add_tabs' );
		add_action( 'load-edit.php', 'rlt_add_tabs' );
		add_action( 'load-post-new.php', 'rlt_add_tabs' );
		add_action( 'load-edit-tags.php', 'rlt_add_tabs' );
	}
}

if ( ! function_exists( 'rlt_plugins_loaded' ) ) {
	function rlt_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'realty', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'rlt_init' ) ) {
	function rlt_init() {
		global $rlt_plugin_info;
		rlt_register_post_type();

		add_image_size( 'realty_search_result', 200, 110, true );
		add_image_size( 'realty_listing', 420, 320, true );
		add_image_size( 'realty_small_photo', 110, 80, true );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $rlt_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$rlt_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $rlt_plugin_info, '4.5' );

		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_REQUEST['page'] ) && 'realty_settings' == $_REQUEST['page'] ) || ( isset( $_REQUEST['post_type'] ) && 'property' == $_REQUEST['post_type'] ) ) {
			rlt_settings();
		}

		if ( ! is_admin() ) {
			/* add template for realty pages */
			add_action( 'template_include', 'rlt_template_include' );
		}

		if ( ! isset( $_SESSION ) ) {
			session_start();
		}
	}
}

if ( ! function_exists ( 'rlt_admin_init' ) ) {
	function rlt_admin_init() {
		global $bws_plugin_info, $rlt_plugin_info, $pagenow, $rlt_options;
		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '205', 'version' => $rlt_plugin_info['Version'] );

		add_rewrite_endpoint( 'realty', EP_PERMALINK );

		add_meta_box( 'property-custom-metabox', __( 'Property Info', 'realty' ), 'rlt_property_custom_metabox', 'property', 'normal', 'high' );

		if ( 'plugins.php' == $pagenow ) {
			/* Install the option defaults */
			if ( function_exists( 'bws_plugin_banner_go_pro' ) ) {
				rlt_settings();
				bws_plugin_banner_go_pro( $rlt_options, $rlt_plugin_info, 'rlt', 'realty', '3936d03a063bccc2a2fa09a26aba0679', '205', 'realty' );
			}
		}

	}
}

if ( ! function_exists ( 'rlt_install' ) ) {
	function rlt_install() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		load_plugin_textdomain( 'realty', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'realty_property_info` (
			`property_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`property_info_post_id` int(10) unsigned NOT NULL,
			`property_info_location` char(255) NOT NULL,
			`property_info_coordinates` char(30) NOT NULL,
			`property_info_type` char(10) NOT NULL,
			`property_info_period` char(10),
			`property_info_price` decimal(15,2) NOT NULL,
			`property_info_bathroom` tinyint(3) unsigned NOT NULL,
			`property_info_bedroom` tinyint(3) unsigned NOT NULL,
			`property_info_square` decimal(10,2) NOT NULL,
			`property_info_photos` varchar(1000) NOT NULL,
			PRIMARY KEY (`property_info_id`)
		) ' . $charset_collate . ' AUTO_INCREMENT=1';
		dbDelta( $sql );

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'realty_currency` (
			`currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`country_currency` char(50) NOT NULL,
			`currency_code` char(3) NOT NULL,
			`currency_hex` char(20) NOT NULL,
			`currency_unicode` char(30) NOT NULL,
			PRIMARY KEY (`currency_id`)
		) ' . $charset_collate;
		dbDelta( $sql );

		$wpdb->query( "INSERT IGNORE INTO `" . $wpdb->prefix . "realty_currency` (`currency_id`, `country_currency`, `currency_code`, `currency_hex`, `currency_unicode`) VALUES
		(1, 'Albania Lek', 'ALL', '4c, 65, 6b', '&#76;&#101;&#107;'),
		(2, 'Afghanistan Afghani', 'AFN', '60b', '&#1547;'),
		(3, 'Argentina Peso', 'ARS', '24', '&#36;'),
		(4, 'Aruba Guilder', 'AWG', '192', '&#402;'),
		(5, 'Australia Dollar', 'AUD', '24', '&#36;'),
		(6, 'Azerbaijan New Manat', 'AZN', '43c, 430, 43d', '&#1084;&#1072;&#1085;'),
		(7, 'Bahamas Dollar', 'BSD', '24', '&#36;'),
		(8, 'Barbados Dollar', 'BBD', '24', '&#36;'),
		(9, 'Belarus Ruble', 'BYR', '70, 2e', '&#112;&#46;'),
		(10, 'Belize Dollar', 'BZD', '42, 5a, 24', '&#66;&#90;&#36;'),
		(11, 'Bermuda Dollar', 'BMD', '24', '&#36;'),
		(12, 'Bolivia Boliviano', 'BOB', '24, 62', '&#36;&#98;'),
		(13, 'Bosnia and Herzegovina Convertible Marka', 'BAM', '4b, 4d', '&#75;&#77;'),
		(14, 'Botswana Pula', 'BWP', '50', '&#80;'),
		(15, 'Bulgaria Lev', 'BGN', '43b, 432', '&#1083;&#1074;'),
		(16, 'Brazil Real', 'BRL', '52, 24', '&#82;&#36;'),
		(17, 'Brunei Darussalam Dollar', 'BND', '24', '&#36;'),
		(18, 'Cambodia Riel', 'KHR', '17db', '&#6107;'),
		(19, 'Canada Dollar', 'CAD', '24', '&#36;'),
		(20, 'Cayman Islands Dollar', 'KYD', '24', '&#36;'),
		(21, 'Chile Peso', 'CLP', '24', '&#36;'),
		(22, 'China Yuan Renminbi', 'CNY', 'a5', '&#165;'),
		(23, 'Colombia Peso', 'COP', '24', '&#36;'),
		(24, 'Costa Rica Colon', 'CRC', '20a1', '&#8353;'),
		(25, 'Croatia Kuna', 'HRK', '6b, 6e', '&#107;&#110;'),
		(26, 'Cuba Peso', 'CUP', '20b1', '&#8369;'),
		(27, 'Czech Republic Koruna', 'CZK', '4b, 10d', '&#75;&#269;'),
		(28, 'Denmark Krone', 'DKK', '6b, 72', '&#107;&#114;'),
		(29, 'Dominican Republic Peso', 'DOP', '52, 44, 24', '&#82;&#68;&#36;'),
		(30, 'East Caribbean Dollar', 'XCD', '24', '&#36;'),
		(31, 'Egypt Pound', 'EGP', 'a3', '&#163;'),
		(32, 'El Salvador Colon', 'SVC', '24', '&#36;'),
		(33, 'Estonia Kroon', 'EEK', '6b, 72', '&#107;&#114;'),
		(34, 'Euro Member Countries', 'EUR', '20ac', '&#8364;'),
		(35, 'Falkland Islands (Malvinas) Pound', 'FKP', 'a3', '&#163;'),
		(36, 'Fiji Dollar', 'FJD', '24', '&#36;'),
		(37, 'Ghana Cedi', 'GHC', 'a2', '&#162;'),
		(38, 'Gibraltar Pound', 'GIP', 'a3', '&#163;'),
		(39, 'Guatemala Quetzal', 'GTQ', '51', '&#81;'),
		(40, 'Guernsey Pound', 'GGP', 'a3', '&#163;'),
		(41, 'Guyana Dollar', 'GYD', '24', '&#36;'),
		(42, 'Honduras Lempira', 'HNL', '4c', '&#76;'),
		(43, 'Hong Kong Dollar', 'HKD', '24', '&#36;'),
		(44, 'Hungary Forint', 'HUF', '46, 74', '&#70;&#116;'),
		(45, 'Iceland Krona', 'ISK', '6b, 72', '&#107;&#114;'),
		(46, 'India Rupee', 'INR', '', ''),
		(47, 'Indonesia Rupiah', 'IDR', '52, 70', '&#82;&#112;'),
		(48, 'Iran Rial', 'IRR', 'fdfc', '&#65020;'),
		(49, 'Isle of Man Pound', 'IMP', 'a3', '&#163;'),
		(50, 'Israel Shekel', 'ILS', '20aa', '&#8362;'),
		(51, 'Jamaica Dollar', 'JMD', '4a, 24', '&#74;&#36;'),
		(52, 'Japan Yen', 'JPY', 'a5', '&#165;'),
		(53, 'Jersey Pound', 'JEP', 'a3', '&#163;'),
		(54, 'Kazakhstan Tenge', 'KZT', '43b, 432', '&#1083;&#1074;'),
		(55, 'Korea (North) Won', 'KPW', '20a9', '&#8361;'),
		(56, 'Korea (South) Won', 'KRW', '20a9', '&#8361;'),
		(57, 'Kyrgyzstan Som', 'KGS', '43b, 432', '&#1083;&#1074;'),
		(58, 'Laos Kip', 'LAK', '20ad', '&#8365;'),
		(59, 'Latvia Lat', 'LVL', '4c, 73', '&#76;&#115;'),
		(60, 'Lebanon Pound', 'LBP', 'a3', '&#163;'),
		(61, 'Liberia Dollar', 'LRD', '24', '&#36;'),
		(62, 'Lithuania Litas', 'LTL', '4c, 74', '&#76;&#116;'),
		(63, 'Macedonia Denar', 'MKD', '434, 435, 43d', '&#1076;&#1077;&#1085;'),
		(64, 'Malaysia Ringgit', 'MYR', '52, 4d', '&#82;&#77;'),
		(65, 'Mauritius Rupee', 'MUR', '20a8', '&#8360;'),
		(66, 'Mexico Peso', 'MXN', '24', '&#36;'),
		(67, 'Mongolia Tughrik', 'MNT', '20ae', '&#8366;'),
		(68, 'Mozambique Metical', 'MZN', '4d, 54', '&#77;&#84;'),
		(69, 'Namibia Dollar', 'NAD', '24', '&#36;'),
		(70, 'Nepal Rupee', 'NPR', '20a8', '&#8360;'),
		(71, 'Netherlands Antilles Guilder', 'ANG', '192', '&#402;'),
		(72, 'New Zealand Dollar', 'NZD', '24', '&#36;'),
		(73, 'Nicaragua Cordoba', 'NIO', '43, 24', '&#67;&#36;'),
		(74, 'Nigeria Naira', 'NGN', '20a6', '&#8358;'),
		(75, 'Korea (North) Won', 'KPW', '20a9', '&#8361;'),
		(76, 'Norway Krone', 'NOK', '6b, 72', '&#107;&#114;'),
		(77, 'Oman Rial', 'OMR', 'fdfc', '&#65020;'),
		(78, 'Pakistan Rupee', 'PKR', '20a8', '&#8360;'),
		(79, 'Panama Balboa', 'PAB', '42, 2f, 2e', '&#66;&#47;&#46;'),
		(80, 'Paraguay Guarani', 'PYG', '47, 73', '&#71;&#115;'),
		(81, 'Peru Nuevo Sol', 'PEN', '53, 2f, 2e', '&#83;&#47;&#46;'),
		(82, 'Philippines Peso', 'PHP', '20b1', '&#8369;'),
		(83, 'Poland Zloty', 'PLN', '7a, 142', '&#122;&#322;'),
		(84, 'Qatar Riyal', 'QAR', 'fdfc', '&#65020;'),
		(85, 'Romania New Leu', 'RON', '6c, 65, 69', '&#108;&#101;&#105;'),
		(86, 'Russia Ruble', 'RUB', '440, 443, 431', '&#1088;&#1091;&#1073;'),
		(87, 'Saint Helena Pound', 'SHP', 'a3', '&#163;'),
		(88, 'Saudi Arabia Riyal', 'SAR', 'fdfc', '&#65020;'),
		(89, 'Serbia Dinar', 'RSD', '414, 438, 43d, 2e', '&#1044;&#1080;&#1085;&#46;'),
		(90, 'Seychelles Rupee', 'SCR', '20a8', '&#8360;'),
		(91, 'Singapore Dollar', 'SGD', '24', '&#36;'),
		(92, 'Solomon Islands Dollar', 'SBD', '24', '&#36;'),
		(93, 'Somalia Shilling', 'SOS', '53', '&#83;'),
		(94, 'South Africa Rand', 'ZAR', '52', '&#82;'),
		(95, 'Korea (South) Won', 'KRW', '20a9', '&#8361;'),
		(96, 'Sri Lanka Rupee', 'LKR', '20a8', '&#8360;'),
		(97, 'Sweden Krona', 'SEK', '6b, 72', '&#107;&#114;'),
		(98, 'Switzerland Franc', 'CHF', '43, 48, 46', '&#67;&#72;&#70;'),
		(99, 'Suriname Dollar', 'SRD', '24', '&#36;'),
		(100, 'Syria Pound', 'SYP', 'a3', '&#163;'),
		(101, 'Taiwan New Dollar', 'TWD', '4e, 54, 24', '&#78;&#84;&#36;'),
		(102, 'Thailand Baht', 'THB', 'e3f', '&#3647;'),
		(103, 'Trinidad and Tobago Dollar', 'TTD', '54, 54, 24', '&#84;&#84;&#36;'),
		(104, 'Turkey Lira', 'TRY', '', ''),
		(105, 'Turkey Lira', 'TRL', '20a4', '&#8356;'),
		(106, 'Tuvalu Dollar', 'TVD', '24', '&#36;'),
		(107, 'Ukraine Hryvnia', 'UAH', '20b4', '&#8372;'),
		(108, 'United Kingdom Pound', 'GBP', 'a3', '&#163;'),
		(109, 'United States Dollar', 'USD', '24', '&#36;'),
		(110, 'Uruguay Peso', 'UYU', '24, 55', '&#36;&#85;'),
		(111, 'Uzbekistan Som', 'UZS', '43b, 432', '&#1083;&#1074;'),
		(112, 'Venezuela Bolivar', 'VEF', '42, 73', '&#66;&#115;'),
		(113, 'Viet Nam Dong', 'VND', '20ab', '&#8363;'),
		(114, 'Yemen Rial', 'YER', 'fdfc', '&#65020;'),
		(115, 'Zimbabwe Dollar', 'ZWD', '5a, 24', '&#90;&#36;');" );
	}
}

if ( ! function_exists( 'rlt_update_db' ) ) {
	function rlt_update_db() {
		global $wpdb;
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}realty_property_info` LIKE 'property_info_type'" );
		if ( 0 == $column_exists ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}realty_property_info` ADD `property_info_type` CHAR(10) AFTER `property_info_coordinates`;" );
			$wpdb->update(
				"{$wpdb->prefix}realty_property_info",
				array(
					'property_info_type'		=> 'rent'
				),
				array( 'property_info_type_id'	=> '1' )
			);
			$wpdb->update(
				"{$wpdb->prefix}realty_property_info",
				array(
					'property_info_type'		=> 'sale'
				),
				array( 'property_info_type_id'	=> '2' )
			);
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}realty_property_info`
				DROP `property_info_type_id`;"
			);
		}
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM `{$wpdb->prefix}realty_property_info` LIKE 'property_info_period'" );
		if ( 0 == $column_exists ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}realty_property_info` ADD `property_info_period` CHAR(10) AFTER `property_info_coordinates`;" );
			$wpdb->update(
				"{$wpdb->prefix}realty_property_info",
				array(
					'property_info_period'			=> 'month'
				),
				array( 'property_info_period_id'	=> '1' )
			);
			$wpdb->update(
				"{$wpdb->prefix}realty_property_info",
				array(
					'property_info_period'			=> 'year'
				),
				array( 'property_info_period_id'	=> '2' )
			);
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}realty_property_info`
				DROP `property_info_period_id`;"
			);
		}
		$wpdb->query ( "ALTER TABLE `{$wpdb->prefix}realty_property_info` CHANGE `property_info_price` `property_info_price` DECIMAL (10,2)" );
	}
}

/* Registing Widget */
if ( ! function_exists ( 'rlt_register_widgets' ) ) {
	function rlt_register_widgets() {
		register_widget( 'Realty_Widget' );
		register_widget( 'Realty_Resent_Items_Widget' );
	}
}

if ( ! function_exists ( 'rlt_register_post_type' ) ) {
	function rlt_register_post_type() {

		$args = array(
			'public'			=> true,
			'show_ui'			=> true,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> true,
			'supports'			=> array( 'title', 'editor', 'thumbnail' ),
			'labels'			=> array(
				'name'					=> _x( 'Properties', 'post type general name', 'realty' ),
				'singular_name'			=> _x( 'Property', 'post type singular name', 'realty' ),
				'menu_name'				=> _x( 'Realty', 'admin menu', 'realty' ),
				'all_items'             => _x( 'Properties', 'admin menu', 'realty' ),
				'name_admin_bar'		=> _x( 'Property', 'add new on admin bar', 'realty' ),
				'add_new'				=> _x( 'Add New', 'property', 'realty' ),
				'add_new_item'			=> __( 'Add New Property', 'realty' ),
				'edit_item'				=> __( 'Edit Properties', 'realty' ),
				'new_item'				=> __( 'New Property', 'realty' ),
				'view_item'				=> __( 'View Properties', 'realty' ),
				'search_items'			=> __( 'Search Properties', 'realty' ),
				'not_found'				=> __( 'No Properties found', 'realty' ),
				'not_found_in_trash'	=> __( 'No Properties found in Trash', 'realty' ),
				'filter_items_list'		=> __( 'Filter Properties list', 'realty' ),
				'items_list_navigation'	=> __( 'Properties list navigation', 'realty' ),
				'items_list'			=> __( 'Properties list', 'realty' )
			)
		);

		register_post_type( 'property' , $args );

		$labels = array(
			'name'							=> _x( 'Categories', 'taxonomy general name', 'realty' ),
			'singular_name'					=> _x( 'Category', 'taxonomy singular name', 'realty' ),
			'menu_name'						=> __( 'Categories', 'realty' ),
			'all_items'						=> __( 'All Categories', 'realty' ),
			'edit_item'						=> __( 'Edit Category', 'realty' ),
			'view_item'						=> __( 'View Category', 'realty' ),
			'update_item'					=> __( 'Update Category', 'realty' ),
			'add_new_item'					=> __( 'Add New Category', 'realty' ),
			'new_item_name'					=> __( 'New Category Name', 'realty' ),
			'parent_item'					=> __( 'Parent Category', 'realty' ),
			'parent_item_colon'				=> __( 'Parent Category:', 'realty' ),
			'search_items'					=> __( 'Search Categories', 'realty' ),
			'popular_items'					=> __( 'Popular Categories', 'realty' ),
			'separate_items_with_commas'	=> __( 'Separate Categories with commas', 'realty' ),
			'add_or_remove_items'			=> __( 'Add or remove Category', 'realty' ),
			'choose_from_most_used'			=> __( 'Choose from the most used Category', 'realty' ),
			'not_found'						=> __( 'No Category found', 'realty' ),
			'items_list_navigation'			=> __( 'Categories list navigation', 'realty' ),
			'items_list'					=> __( 'Categories list', 'realty' )
		);

		$args = array(
			'hierarchical'		=> true,
			'labels'			=> $labels,
			'show_ui'			=> true,
			'show_tagcloud'		=> false,
			'show_admin_column'	=> true,
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'property_type' ),
		);

		register_taxonomy( 'property_type', array( 'property' ), $args );
	}
}

if ( ! function_exists( 'rlt_get_options_default' ) ) {
	function rlt_get_options_default() {
		global $rlt_plugin_info;

		if ( empty( $rlt_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$rlt_plugin_info = get_plugin_data( __FILE__ );
		}

		$default_options = array(
			'plugin_option_version'			=> $rlt_plugin_info['Version'],
			'display_settings_notice'		=> 1,
			'first_install'					=> strtotime( 'now' ),
			'suggest_feature_banner'		=> 1,
			'currency_unicode'				=> '109',
			'currency_position'				=> 'before',
			'unit_area_custom_display'		=> 0,
			'unit_area'						=> 'ft&sup2',
			'custom_unit_area'				=> '',
			'per_page'						=> get_option( 'posts_per_page' ),
			'theme_banner'					=> 1,
			'maps_key'						=> '',
			'rlt_price'					    => 'show',
		);

		return $default_options;
	}
}

if ( ! function_exists( 'rlt_settings' ) ) {
	function rlt_settings() {
		global $rlt_options, $rlt_plugin_info;
		$db_version = '1.1';

		/* Install the option defaults */
		if ( ! get_option( 'rlt_options' ) )
			add_option( 'rlt_options', rlt_get_options_default() );

		$rlt_options = get_option( 'rlt_options' );

		if ( ! isset( $rlt_options['plugin_option_version'] ) || $rlt_options['plugin_option_version'] != $rlt_plugin_info['Version'] ) {
			$rlt_options = array_merge( rlt_get_options_default(), $rlt_options );
			$rlt_options['plugin_option_version'] = $rlt_plugin_info['Version'];

			/* show pro features */
			$rlt_options['hide_premium_options'] = array();

			$update_option = true;

			/**
			 * @deprecated since 1.1.5
			 * @todo remove after 31.02.2021
			 */
			if ( version_compare( $rlt_plugin_info['Version'], '1.1.5', '>=' ) ) {
				$themepath = get_stylesheet_directory() . '/';
				$templates = array( 'rlt-listing.php', 'rlt-search-listing-results.php', 'rlt-nothing-found.php' );
				// The rlt-search-form.php file is not migrated, because it must be located at the root of the theme to work correctly
				foreach ( $templates as $filename ) {
					if ( file_exists( $themepath . $filename ) ) {
						if ( ! file_exists( $themepath  . 'bws-templates/' ) )
							@mkdir( $themepath  . 'bws-templates/', 0755 );

						if ( 'rlt-nothing-found.php' == $filename ) {
							@unlink( $themepath . $filename );
							continue;
						}

						if ( rename( $themepath . $filename, $themepath . 'bws-templates/' . $filename ) )
							@unlink( $themepath . $filename );
					}
				}
			}
			/* end todo */
		}

		if ( ! isset( $rlt_options['plugin_db_version'] ) || $rlt_options['plugin_db_version'] != $db_version ) {
			rlt_install();
			if ( version_compare( $rlt_options['plugin_db_version'], '1.1', '<' ) ) {
				rlt_update_db();
			}
			$rlt_options['plugin_db_version'] = $db_version;
			$update_option = true;
		}

		if ( isset( $update_option ) ) {
			update_option( 'rlt_options', $rlt_options );
		}
	}
}

if ( ! function_exists( 'rlt_plugin_activation' ) ) {
	function rlt_plugin_activation( $networkwide ) {
		global $wpdb;
		/* Activation function for network */
		if ( is_multisite() ) {
			/* Check if it is a network activation - if so, run the activation function for each blog id */
			if ( $networkwide ) {
				$old_blog = $wpdb->blogid;

				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					rlt_install();
				}
				switch_to_blog( $old_blog );
				return;
			} else {
				rlt_install();
			}
		} else {
			rlt_install();
		}
	}
}

if ( ! function_exists( 'rlt_features_demo' ) ) {
	function rlt_features_demo() {
		global $rlt_plugin_info, $wp_version, $cptch_options;

		if ( empty( $cptch_options ) )
			rlt_settings();
		$bws_hide_premium = bws_hide_premium_options_check( $cptch_options ); ?>
        <div class="wrap">
            <h1><?php echo __( 'Features', 'realty' ); ?></h1>
            <br>
			<?php if ( $bws_hide_premium ) { ?>
                <p>
					<?php _e( 'This tab contains Pro options only.', 'realty' );
					echo ' ' . sprintf(
							__( '%sChange the settings%s to view the Pro options.', 'realty' ),
							'<a href="edit.php?post_type=property&page=realty_settings&bws_active_tab=misc">',
							'</a>'
						); ?>
                </p>
			<?php } else { ?>
                <div class="bws_pro_version_bloc">
                    <div class="bws_pro_version_table_bloc">
                        <div class="bws_table_bg"></div>
                        <div class="bws_pro_version">
							<?php require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
							rlt_features_block(); ?>
                        </div>
                    </div>
                    <div class="bws_pro_version_tooltip">
                        <a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/realty/?k=fdac994c203b41e499a2818c409ff2bc&pn=205&v=<?php echo $rlt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Realty Pro Plugin"><?php _e( 'Upgrade to Pro', 'realty' ); ?></a>
                    </div>
                </div>
			<?php } ?>
        </div>
	<?php }
}

if ( ! function_exists( 'rlt_agents_demo' ) ) {
	function rlt_agents_demo() {
		global $rlt_plugin_info, $wp_version, $rlt_options;

		if ( empty( $rlt_options ) )
			rlt_settings();
		$bws_hide_premium = bws_hide_premium_options_check( $rlt_options ); ?>
        <div class="wrap">
            <h1><?php ( isset( $_POST['rlt_agents_add_new_demo'] ) ) ? _e( 'Add a new Agent', 'realty' ) : _e( 'Agents', 'realty' ); ?>
                <form method="post" action="" style="display: inline;">
                    <button class="page-title-action add-new-h2 hide-if-no-js" name="rlt_agents_add_new_demo" value="on"<?php echo ( isset( $_POST['rlt_agents_add_new_demo'] ) ) ? ' style="display: none;"' : ''; ?>><?php _e( 'Add New', 'realty' ); ?></button>
                </form>
            </h1>
            <br>
			<?php if ( $bws_hide_premium ) { ?>
                <p>
					<?php _e( 'This tab contains Pro options only.', 'realty' );
					echo ' ' . sprintf(
							__( '%sChange the settings%s to view the Pro options.', 'realty' ),
							'<a href="edit.php?post_type=property&page=realty_settings&bws_active_tab=misc">',
							'</a>'
						); ?>
                </p>
			<?php } else { ?>
                <div class="bws_pro_version_bloc">
                    <div class="bws_pro_version_table_bloc">
                        <div class="bws_table_bg"></div>
                        <div class="bws_pro_version">
							<?php require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
							if ( isset( $_POST['rlt_agents_add_new_demo'] ) ) {
								rlt_agents_add_new_block();
							} else {
								rlt_agents_block();
							} ?>
                        </div>
                    </div>
                    <div class="bws_pro_version_tooltip">
                        <a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/realty/?k=fdac994c203b41e499a2818c409ff2bc&pn=205&v=<?php echo $rlt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Realty Pro Plugin"><?php _e( 'Upgrade to Pro', 'realty' ); ?></a>
                    </div>
                </div>
			<?php } ?>
        </div>
	<?php }
}

if ( ! function_exists( 'rlt_settings_page' ) ) {
	function rlt_settings_page() {
		if ( ! class_exists( 'Bws_Settings_Tabs' ) )
			require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-rlt-settings.php' );
		$page = new Rlt_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
        <div class="wrap">
            <h1><?php _e( 'Realty Settings', 'realty' ); ?></h1>
			<?php $page->display_content(); ?>
        </div>
	<?php }
}

/* Realty Widget */
if ( ! class_exists( 'Realty_Widget' ) ) {
	class Realty_Widget extends WP_Widget {

		function __construct() {
			/* Instantiate the parent object */
			parent::__construct(
				'realty_widget',
				__( 'Realty Widget', 'realty' ),
				array( 'description' => __( 'Widget for displaying Sale/Rent Form.', 'realty' ) )
			);
		}

		function widget( $args, $instance ) {
			global $wpdb, $wp_query, $rlt_form_action, $rlt_form_vars;
			  if ( ! wp_script_is( 'rlt_script', 'registered' ) ) {
				wp_register_script( 'rlt_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ), false, true );
			} if ( empty( $rlt_form_vars ) ) {
				do_action( 'rlt_check_form_vars' );
			}
			$tab_1_class = $tab_2_class = '';

			echo $args['before_widget'];

			$taxonomies = array(
				'property_type'
			);

			$taxonomy_args = array(
				'orderby'		=> 'name',
				'order'			=> 'ASC',
				'hide_empty'	=> false
			);

			$terms_property_type = get_terms( $taxonomies, $taxonomy_args );

			$bedrooms_bathrooms = $wpdb->get_row( 'SELECT MIN( `property_info_bedroom` ) AS `min_bedroom`,
					MAX(`property_info_bedroom`) AS `max_bedroom`,
					MIN(`property_info_bathroom`) AS `min_bathroom`,
					MAX(`property_info_bathroom`) AS `max_bathroom`,
					MIN(`property_info_price`) AS `min_price`,
					MAX(`property_info_price`) AS `max_price`
				FROM `' . $wpdb->prefix . 'realty_property_info`',
			ARRAY_A );
			if ( ! isset( $rlt_form_vars['property_type_info'] ) || ( isset( $rlt_form_vars['property_type_info'] ) && 'sale' == $rlt_form_vars['property_type_info'] ) ) {
				$tab_1_class = ' active';
			} else {
				$tab_2_class = ' active';
			}
			$rlt_form_action = get_option( 'permalink_structure' ) == '' ? '' : 'property_search_results';
			$min_price = ! empty( $rlt_form_vars['property_min_price'] ) ? $rlt_form_vars['property_min_price'] : $bedrooms_bathrooms['min_price'];
			$max_price = ! empty( $rlt_form_vars['property_max_price'] ) ? $rlt_form_vars['property_max_price'] : $bedrooms_bathrooms['max_price']; ?>

			<div class="rlt_tab_wrapper">
				<div id="rlt_body_tabs">
					<div id="main_tabs">
						<div class="rlt_tabs">
							<div class="tab tab_1<?php echo $tab_1_class; ?>"><span><?php _e( 'For Sale', 'realty' ); ?></span></div>
							<div class="tab tab_2<?php echo $tab_2_class; ?>"><span><?php _e( 'For Rent', 'realty' ); ?></span></div>
						</div><!-- .tabs -->
						<div class="for_sale rlt_tab_block rlt_tab_block_1<?php echo $tab_1_class; ?>">
							<form action="<?php echo home_url() . '/' . $rlt_form_action; ?>" method="get" id="property_sale_search_form">
								<div>
									<input placeholder="<?php _e( 'Location', 'realty' ); ?>" type="text" name="rlt_location" id="rlt_location" value="<?php if ( ! empty( $rlt_form_vars['property_location'] ) ) echo $rlt_form_vars['property_location']; ?>" />
									<select class="property rlt_select" name="rlt_property">
										<option value="all" selected="selected"><?php _e( 'Property Type', 'realty' ); ?></option>
										<?php foreach ( $terms_property_type as $term_property_type ) { ?>
											<option value="<?php echo $term_property_type->slug; ?>" <?php if ( ! empty( $rlt_form_vars['property_type'] ) && $rlt_form_vars['property_type'] == $term_property_type->slug ) echo 'selected="selected"'; ?>><?php echo $term_property_type->name; ?></option>
										<?php } ?>
									</select>
									<div class="rlt_prices">
										<?php _e( 'Price', 'realty' ); ?>: <span class="rlt_min_price"><?php echo apply_filters( 'rlt_formatting_price', $min_price ); ?></span> - <span class="rlt_max_price"><?php echo apply_filters( 'rlt_formatting_price', $max_price ); ?></span>
										<div class="rlt_scroller">
											<div class="rlt_scroller_path">
												<div id="rlt_price"></div>
											</div><!-- .rlt_scroller_path -->
										</div><!-- .rlt_scroller -->
									</div>
									<input type="hidden" id="rlt_min_price" name="rlt_min_price" value="<?php echo $bedrooms_bathrooms['min_price']; ?>" />
									<input type="hidden" id="rlt_max_price" name="rlt_max_price" value="<?php echo $bedrooms_bathrooms['max_price']; ?>" />
									<input type="hidden" id="rlt_current_min_price" value="<?php echo $min_price; ?>" />
									<input type="hidden" id="rlt_current_max_price" value="<?php echo $max_price; ?>" />
									<select class="bathrooms rlt_select" name="rlt_bathrooms">
										<option value="" disabled="disabled" selected="selected"><?php _e( 'Bathrooms', 'realty' ); ?></option>
										<?php $and_more = __( 'and more', 'realty' );
										for ( $i = $bedrooms_bathrooms['min_bathroom']; $i <= $bedrooms_bathrooms['max_bathroom']; $i++ ){
											if ( $i == $bedrooms_bathrooms['max_bathroom'] )
												$and_more = ''; ?>
											<option value="<?php echo $i; ?>" <?php if ( ! empty( $rlt_form_vars['property_bath'] ) && $rlt_form_vars['property_bath'] == $i && $rlt_form_vars['property_bath'] != $bedrooms_bathrooms['min_bathroom'] ) echo 'selected="selected"'; ?>><?php echo $i; ?> <?php echo $and_more; ?></option>
										<?php } ?>
									</select>
									<select class="bedrooms rlt_select" name="rlt_bedrooms">
										<option value="" disabled="disabled" selected="selected"><?php _e( 'Bedrooms', 'realty' ); ?></option>
										<?php $and_more = __( 'and more', 'realty' );
										for ( $i = $bedrooms_bathrooms['min_bedroom']; $i <= $bedrooms_bathrooms['max_bedroom']; $i++ ) {
											if ( $i == $bedrooms_bathrooms['max_bedroom'] )
												$and_more = ''; ?>
											<option value="<?php echo $i; ?>" <?php if ( ! empty( $rlt_form_vars['property_bed'] ) && $rlt_form_vars['property_bed'] == $i && $rlt_form_vars['property_bed'] != $bedrooms_bathrooms['min_bedroom'] ) echo 'selected="selected"'; ?>><?php echo $i; ?> <?php echo $and_more; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" id="rlt_info_type" name="rlt_info_type" value="sale" />
									<input type="hidden" name="rlt_action" value="listing_search" />
									<input type="submit" value="<?php _e( 'update filters', 'realty' ); ?>">
									<div class="clear"></div>
								</div>
							</form>
						</div><!--end of .for_sale-->
						<div class="for_rent rlt_tab_block rlt_tab_block_2<?php echo $tab_2_class; ?>">
							<form action="<?php echo home_url() . '/' . $rlt_form_action; ?>" method="get" id="property_rent_search_form">
								<div>
									<input placeholder="<?php _e( 'Location', 'realty' ); ?>" type="text" name="rlt_location" id="rlt_location" value="<?php if ( ! empty( $rlt_form_vars['property_location'] ) ) echo $rlt_form_vars['property_location']; ?>" />
									<select class="property rlt_select" name="rlt_property">
										<option value="all" selected="selected"><?php _e( 'Property Type', 'realty' ); ?></option>
										<?php foreach ( $terms_property_type as $term_property_type ) { ?>
											<option value="<?php echo $term_property_type->slug; ?>" <?php if ( ! empty( $rlt_form_vars['property_type'] ) && $rlt_form_vars['property_type'] == $term_property_type->slug ) echo 'selected="selected"'; ?>><?php echo $term_property_type->name; ?></option>
										<?php } ?>
									</select>
									<select class="bathrooms rlt_select" name="rlt_bathrooms">
										<option value="" disabled="disabled" selected="selected"><?php _e( 'Bathrooms', 'realty' ); ?></option>
										<?php $and_more = __( 'and more', 'realty' );
										for ( $i = $bedrooms_bathrooms['min_bathroom']; $i <= $bedrooms_bathrooms['max_bathroom']; $i++ ) {
											if ( $i == $bedrooms_bathrooms['max_bathroom'] )
												$and_more = ''; ?>
											<option value="<?php echo $i; ?>" <?php if ( ! empty( $rlt_form_vars['property_bath'] ) && $rlt_form_vars['property_bath'] == $i && $rlt_form_vars['property_bath'] != $bedrooms_bathrooms['min_bathroom'] ) echo 'selected="selected"'; ?>><?php echo $i; ?> <?php echo $and_more; ?></option>
										<?php } ?>
									</select>
									<select class="bedrooms rlt_select" name="rlt_bedrooms">
										<option value="" disabled="disabled" selected="selected"><?php _e( 'Bedrooms', 'realty' ); ?></option>
										<?php $and_more = __( 'and more', 'realty' );
										for ( $i = $bedrooms_bathrooms['min_bedroom']; $i <= $bedrooms_bathrooms['max_bedroom']; $i++ ) {
											if ( $i == $bedrooms_bathrooms['max_bedroom'] )
												$and_more = ''; ?>
											<option value="<?php echo $i; ?>" <?php if ( ! empty( $rlt_form_vars['property_bed'] ) && $rlt_form_vars['property_bed'] == $i && $rlt_form_vars['property_bed'] != $bedrooms_bathrooms['min_bedroom'] ) echo 'selected="selected"'; ?>><?php echo $i; ?> <?php echo $and_more; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" id="rlt_info_type" name="rlt_info_type" value="rent" />
									<input type="hidden" name="rlt_action" value="listing_search" />
									<input type="submit" value="<?php _e( 'update filters', 'realty' ); ?>">
								</div>
							</form>
						</div><!--end of .for_rent-->
					</div><!-- #main_tabs -->
				</div><!-- #rlt_body_tabs -->
			</div><!-- .rlt_tab_wrapper -->
			<?php $permalink_structure = get_option( 'permalink_structure' );
			if ( is_single() && 'property' == get_post_type() && ! empty( $_SESSION['current_page'] ) ) {
				if ( '' == $permalink_structure ) {
					$link = realty_request_uri( esc_url( home_url( '/' ) ), 'property', $permalink_structure ) . ( $_SESSION['current_page'] > 1 ? '&property_paged=' . $_SESSION['current_page'] : '' );
				} else {
					$link = realty_request_uri( esc_url( home_url( '/' ) ) , 'property', $permalink_structure ) . ( $_SESSION['current_page'] > 1 ? 'page/' . $_SESSION['current_page'] . '/' : '' );
				}
				?><div class="rlt_back_to_results"><a href="<?php echo $link; ?>" class="more"><?php _e( 'back to search results', 'realty' ); ?></a></div>
			<?php }
			wp_reset_query();
			echo $args['after_widget'];
		}
	}
}

/* Realty Resent Items Widget */
if ( ! class_exists( 'Realty_Resent_Items_Widget' ) ) {
	class Realty_Resent_Items_Widget extends WP_Widget {

		function __construct() {
			/* Instantiate the parent object */
			parent::__construct(
				'realty_recent_items_widget',
				__( 'Realty Recent Items', 'realty' ),
				array( 'description' => __( 'Widget for displaying Recent Items block.', 'realty' ) )
			);
		}

		function widget( $args, $instance ) {
			global $wpdb, $wp_query, $rlt_form_vars;
			if ( ! wp_script_is( 'rlt_script', 'registered' ) ) {
				wp_register_script( 'rlt_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ), false, true );
			}
			$widget_title	= ( ! empty( $instance['widget_title'] ) ) ? apply_filters( 'widget_title', $instance['widget_title'], $instance, $this->id_base ) : __( 'Recent items', 'realty' );
			$count_items	= isset( $instance['count_items'] ) ? $instance['count_items'] : 4;
			$types = rlt_get_types();
			$periods = rlt_get_periods();

			echo $args['before_widget']; ?>
			<div id="rlt_heading_recent_items">
				<div class="widget_content rlt_widget_content">
					<?php if ( ! empty( $widget_title ) ) {
						echo $args['before_title'] . $widget_title . $args['after_title'];
					}

					$recent_items_sql = 'SELECT ' . $wpdb->posts . '.ID,
							' . $wpdb->posts . '.post_title,
							' . $wpdb->prefix . 'realty_property_info.*
						FROM ' . $wpdb->posts . '
							INNER JOIN ' . $wpdb->prefix . 'realty_property_info ON ' . $wpdb->prefix . 'realty_property_info.property_info_post_id = ' . $wpdb->posts . '.ID
						ORDER BY ' . $wpdb->posts . '.post_date DESC
						LIMIT ' . $count_items . '';

					$recent_items_results = $wpdb->get_results( $recent_items_sql, ARRAY_A );

					$permalink_structure = get_option( 'permalink_structure' );
					if ( ! empty( $rlt_form_vars ) ) {
						$form_vars_old = $rlt_form_vars;
						$rlt_form_vars = array();
					}
					rlt_check_form_vars( true ); ?>
					<div id="rlt_home_preview">
						<div class="view_more">
							<a href="<?php echo realty_request_uri( esc_url( home_url( '/' ) ), 'property', $permalink_structure ); ?>"><?php _e( 'view all', 'realty' ); ?></a>
						</div>
						<?php if ( isset( $form_vars_old ) ) {
							$rlt_form_vars = $form_vars_old;
						}
						foreach ( $recent_items_results as $recent_item ) {
							$recent_item['property_info_photos'] = unserialize( $recent_item['property_info_photos'] ); ?>
							<div class="rlt_home_preview">
								<a href="<?php echo get_permalink( $recent_item['ID'] ); ?>">
									<?php if ( has_post_thumbnail( $recent_item['ID'] ) ){
										echo get_the_post_thumbnail( $recent_item['ID'], 'realty_search_result' );
									} else {
										if ( isset( $recent_item['property_info_photos'][0] ) ) {
											$small_photo = wp_get_attachment_image_src( $recent_item['property_info_photos'][0], 'realty_search_result' ); ?>
											<img src="<?php echo $small_photo[0]; ?>" alt="home" />
										<?php } else { ?>
											<img src="http://placehold.it/200x110" alt="default image" />
										<?php }
									} ?>
								</a>
								<div class="rlt_home_info">
									<h4><a href="<?php echo get_permalink( $recent_item['ID'] ); ?>"><?php echo $recent_item['post_title']; ?></a></h4>
									<ul>
										<li><?php echo $recent_item['property_info_location']; ?></li>
										<li><?php echo $recent_item['property_info_bedroom'] . ' ' . _n( 'bedroom', 'bedrooms', absint( $recent_item['property_info_bedroom'] ), 'realty' ) . ', ' . $recent_item['property_info_bathroom'] .' ' . _n( 'bathroom', 'bathrooms', absint( $recent_item['property_info_bathroom'] ), 'realty' ); ?></li>
										<li><?php echo $recent_item['property_info_square'] . ' ' . rlt_get_unit_area(); ?></li>
									</ul>
								</div>
								<div class="home_footer">
									<a class="<?php echo ( ! empty( $periods[ $recent_item['property_info_period'] ] ) ) ? "rent" : "sale"; ?>" href="<?php echo get_permalink( $recent_item['ID'] ); ?>"><?php echo $types[$recent_item['property_info_type'] ];?></a>
									<a href="<?php the_permalink(); ?>" class="add">&#160;</a>
									<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $recent_item['property_info_price'], rlt_get_currency() ); ?><sup><?php if ( ! empty( $recent_item['property_info_period'] ) ) echo "/" . $periods[ $recent_item['property_info_period'] ]; ?></sup></span>
									<div class="clear"></div>
								</div><!-- .home_footer -->
							</div><!-- .rlt_home_preview -->
						<?php } ?>
						<div class="clear"></div>
					</div><!--end of #rlt_home_preview-->
				</div><!-- .rlt_widget_content-->
			</div><!-- #rndmftrdpsts_heading_featured_post -->
			<?php wp_reset_query();
			echo $args['after_widget'];
		}

		function form( $instance ) {
			$widget_title	= isset( $instance['widget_title'] ) ? $instance['widget_title'] : null;
			$count_items	= isset( $instance['count_items'] ) ? $instance['count_items'] : 4; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Widget Title', 'realty' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count_items' ); ?>"><?php _e( 'Number of items to be displayed', 'realty' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'count_items' ); ?>" name="<?php echo $this->get_field_name( 'count_items' ); ?>" type="number" value="<?php echo esc_attr( $count_items ); ?>"/>
			</p>
		<?php }

		function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['widget_title']	= ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : null;
			$instance['count_items']	= ( ! empty( $new_instance['count_items'] ) ) ? strip_tags( $new_instance['count_items'] ) : 4;
			return $instance;
		}
	}
}

if ( ! function_exists ( 'rlt_property_columns' ) ) {
	function rlt_property_columns( $columns ) {
		unset( $columns['date'] );
		$columns['date'] = __( 'Date', 'realty' );
		return $columns;
	}
}

if ( ! function_exists ( 'rlt_restrict_manage_property' ) ) {
	function rlt_restrict_manage_property() {
		/* only display these taxonomy filters on desired custom post_type listings*/
		global $typenow;
		if ( 'property' == $typenow ) {
			/* create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list*/
			$filters = array( 'property_type' );

			foreach ( $filters as $tax_slug ) {
				/* retrieve the taxonomy object */
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				/* retrieve array of term objects per taxonomy */
				$terms = get_terms(
					array( $tax_slug ),
					array(
						'orderby'		=> 'name',
						'order'			=> 'ASC',
						'hide_empty'	=> false
					)
				);
				$current_id = ! empty( $_GET['rlt_' . $tax_slug . '_filter'] ) ? intval( $_GET['rlt_' . $tax_slug . '_filter'] ) : 0;
				/* output html for taxonomy dropdown filter */ ?>
				<select name='rlt_<?php echo $tax_slug; ?>_filter' id='rlt_<?php echo $tax_slug; ?>_filter' class='postform'>
					<option value=''><?php _e( 'Show All', 'realty' ); echo ' ' . $tax_name; ?></option>
					<?php foreach ( $terms as $term ) {
						/* output each select option line, check against the last $_GET to show the current option selected */ ?>
						<option value='<?php echo $term->term_id; ?>' <?php echo $current_id == $term->term_id ? ' selected="selected"' : ''; ?>>
							<?php echo $term->name .' ( ' . $term->count .' )'; ?>
						</option>
					<?php } ?>
				</select>
			<?php }
		}
	}
}

if ( ! function_exists ( 'rlt_property_pre_get_posts' ) ) {
	function rlt_property_pre_get_posts( $query ) {
		if ( is_admin() && ! empty( $_GET['rlt_property_type_filter'] ) ) {
			if ( 0 != intval( $_GET['rlt_property_type_filter'] ) ) {
				$property_type = intval( $_GET['rlt_property_type_filter'] );
				$tax_query = array(
					array(
						'taxonomy'	=> 'property_type',
						'field'		=> 'id',
						'terms'		=> $property_type
					)
				);
				$query->set( 'tax_query', $tax_query );
			}
		}
	}
}

if ( ! function_exists( 'rlt_property_custom_metabox' ) ) {
	function rlt_property_custom_metabox() {
		global $post, $wpdb;
		$property_info = $wpdb->get_row( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_info` WHERE `property_info_post_id` = ' . $post->ID, ARRAY_A );
		$currency = rlt_get_currency();
		$types = rlt_get_types();
		$periods = rlt_get_periods(); ?>
		<table class="form-table rlt-info">
			<tr>
				<th><label for="rlt_location"><?php _e( 'Location', 'realty' ); ?></label></th>
				<td>
					<input type="text" id="rlt_location" size="40" name="rlt_location" value="<?php if ( ! empty( $property_info['property_info_location'] ) ) echo $property_info['property_info_location']; ?>"/><br />
					<span class="bws_info"><?php _e( 'For example', 'realty' ); ?>: 6753 Gregory Court, Wheatfield, NY 14120</span>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_coordinates"><?php _e( 'Latitude and longitude coordinates', 'realty' ); ?></label></th>
				<td>
					<input type="text" id="rlt_coordinates" size="40" name="rlt_coordinates" value="<?php if ( ! empty( $property_info['property_info_coordinates'] ) ) echo $property_info['property_info_coordinates']; ?>"/><br />
					<span class="bws_info"><?php _e( 'For example', 'realty' ); ?>: 43.097585, -78.870621</span>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_type"><?php _e( 'Type', 'realty' ); ?></label></th>
				<td>
					<select name="rlt_type" id="rlt_type">
					<?php $property_info_type = ! empty( $property_info['property_info_type'] ) ? $property_info['property_info_type'] : 0;
					foreach( $types as $key => $value ) {
						printf( '<option value="%1$s" %2$s>%3$s</option><br />',
							$key,
							selected( $key, $property_info_type, false ),
							$value
						);

					} ?>
				</select>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_period"><?php _e( 'Period', 'realty' ); ?></label></th>
				<td>
					<select name="rlt_period" id="rlt_period">
						<?php $property_info_period = ! empty( $property_info['property_info_period'] ) ? $property_info['property_info_period'] : 0; ?>
                        <option value="" <?php selected( '', $property_info_period, false ); ?>></option>
						<?php foreach( $periods as $key => $value ) {
							printf( '<option value="%1$s" %2$s>%3$s</option><br />',
								$key,
								selected( $key, $property_info_period, false ),
								$value
							);
						} ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_price"><?php _e( 'Price', 'realty' ); ?>( <?php echo $currency[0]; ?> )</label></th>
				<td>
					<input type="text" id="rlt_price" name="rlt_price" value="<?php if ( ! empty( $property_info['property_info_price'] ) ) echo $property_info['property_info_price']; ?>"/><br />
					<span class="bws_info"><?php _e( 'For example', 'realty' ); ?>: 25852.00</span>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_square"><?php _e( 'Floor area', 'realty' ); ?>( <?php echo rlt_get_unit_area(); ?> )</label></th>
				<td>
					<input type="text" id="rlt_square" name="rlt_square" value="<?php if ( ! empty( $property_info['property_info_square'] ) ) echo $property_info['property_info_square']; ?>"/><br />
				<span class="bws_info"><?php _e( 'For example', 'realty' ); ?>: 21820.00</span>
				</td>
			</tr>
			<tr>
				<th><label for="rlt_bedroom"><?php _e( 'Bedrooms', 'realty' ); ?></label></th>
				<td>
					<input type="number" id="rlt_bedroom" min="1" name="rlt_bedroom" value="<?php echo ! empty( $property_info['property_info_bedroom'] ) ? $property_info['property_info_bedroom'] : "1"; ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="rlt_bathroom"><?php _e( 'Bathrooms', 'realty' ); ?></label></th>
				<td>
					<input type="number" id="rlt_bathroom" min="1" name="rlt_bathroom" value="<?php echo ! empty( $property_info['property_info_bathroom'] ) ? $property_info['property_info_bathroom'] : "1"; ?>" />
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Photos', 'realty' ); ?></th>
				<td>
					<button class="rlt_add_photo button"><?php _e( 'Add photo', 'realty' ); ?></button>
				</td>
			</tr>
            <tr>
                <th></th>
                <td>
                    <ul class="rlt-gallery clearfix" id="rlt_gallery">
		                <?php if ( ! empty( $property_info['property_info_photos'] ) ) {
			                $property_info['property_info_photos'] = unserialize( $property_info['property_info_photos'] );
			                foreach ( $property_info['property_info_photos'] as $rlt_photo ) { ?>
                                <li id="<?php echo $rlt_photo; ?>" class="rlt_image_block">
                                    <div class="rlt_drag">
                                        <div class="rlt_image">
							                <?php $image_attributes = wp_get_attachment_image_src( $rlt_photo, 'thumbnail' ); ?>
                                            <img src="<?php echo $image_attributes[0]; ?>" title="" width="150" />
                                        </div>
                                        <div class="rlt_delete"><a href="javascript:void(0);" onclick="rlt_img_delete( <?php echo $rlt_photo; ?> );"><?php _e( 'Delete', 'realty' ) ; ?></a></div>
                                        <input type="hidden" name="rlt_photos[]" value="<?php echo $rlt_photo; ?>" />
                                    </div>
                                </li>
			                <?php }
		                } ?>
                    </ul>
                    <div id="rlt_add_images" class="clear"></div>
                    <div id="rlt_delete_images"></div>
	                <?php if ( ! empty( $property_info ) ) { ?>
                        <input type="hidden" value="<?php echo $property_info['property_info_id']; ?>" name="property_info_id" />
	                <?php } ?>
                    <div class="clear"></div>
                </td>
            </tr>
		</table>
	<?php }
}

if ( ! function_exists( 'rlt_save_postdata' ) ) {
	function rlt_save_postdata( $post_id, $post ) {
		global $post_type;
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		/* Check if our nonce is set. */
		if ( 'property' != $post_type ) {
			return $post_id;
		} else {
			global $wpdb;
			if ( isset( $_POST[ 'rlt_location' ] ) ) {
				$property_info = array();
				$property_info['property_info_post_id']			= $post_id;
				$property_info['property_info_location']		= sanitize_text_field( $_POST['rlt_location'] );
				$property_info['property_info_coordinates']		= preg_match( '/^[-]?[1-9]{1}[\d]{1}[.][\d]{3,9}[,][ ]?[-]?[1-9]{1}[\d]{1,2}[.][\d]{3,9}$/', trim( $_POST['rlt_coordinates'] ) ) ? trim( $_POST['rlt_coordinates'] ) : '';
				$property_info['property_info_type']			= sanitize_text_field( $_POST['rlt_type'] );
				$property_info['property_info_period']			= sanitize_text_field( $_POST['rlt_period'] );
				$property_info['property_info_price']			= sanitize_text_field( $_POST['rlt_price'] );
				$property_info['property_info_bathroom']		= ! empty( $_POST['rlt_bathroom'] ) ? sanitize_text_field( $_POST['rlt_bathroom'] ) : 1;
				$property_info['property_info_bedroom']			= ! empty( $_POST['rlt_bedroom'] ) ? sanitize_text_field( $_POST['rlt_bedroom'] ) : 1;
				$property_info['property_info_square']			= sanitize_text_field( $_POST['rlt_square'] );
				$property_info['property_info_photos']			= isset( $_POST['rlt_photos'] ) ? $_POST['rlt_photos'] : array();
				if ( ! empty( $_POST[ 'rlt_add_images' ] ) ) {
					$property_info['property_info_photos']		= array_merge( $property_info['property_info_photos'], $_POST['rlt_add_images'] );
				}
				if ( ! empty( $_POST[ 'rlt_delete_images' ] ) ) {
					$property_info['property_info_photos']		= array_diff( $property_info['property_info_photos'], $_POST['rlt_delete_images'] );
				}
				$post_thumbnail = get_the_post_thumbnail( $post->id );
				if ( empty( $post_thumbnail ) && ! empty( $property_info['property_info_photos'] ) ) {
					set_post_thumbnail( $post->id, $property_info['property_info_photos'][0] );
				}
				$property_info['property_info_photos'] = serialize( $property_info['property_info_photos'] );
				/* Update the meta field in the database. */
				if ( isset( $_POST['property_info_id'] ) ) {
					$wpdb->update(
						$wpdb->prefix . 'realty_property_info',
						$property_info,
						array( 'property_info_id' => $_POST['property_info_id'] ),
						array( '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%f', '%s', '%d' ),
						array( '%d' )
					);
				} else {
					$wpdb->insert(
						$wpdb->prefix . 'realty_property_info',
						$property_info
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'rlt_delete_post' ) ) {
	function rlt_delete_post( $post_id ) {
		/* We check if the global post type isn't ours and just return */
		global $post_type, $wpdb;
		if ( 'property' != $post_type ) {
			return;
		}

		/* Delete information from custom table */
		$wpdb->delete(
			$wpdb->prefix . 'realty_property_info',
			array( 'property_info_post_id' => $post_id )
		);
	}
}

if ( ! function_exists( 'rlt_enqueue_styles' ) ) {
	function rlt_enqueue_styles() {
		wp_enqueue_style( 'rlt_select_stylesheet', plugins_url( 'css/select2.css', __FILE__ ) );
		wp_enqueue_style( 'slick.css', plugins_url( 'css/slick.css', __FILE__ ) ); /* including css for slick*/
		wp_enqueue_style( 'rlt_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );

	}
}

if ( ! function_exists( 'rlt_enqueue_scripts' ) ) {
	function rlt_enqueue_scripts() {
	    global $rlt_options;
		if ( wp_script_is( 'rlt_script', 'registered' ) ) {
			$realestate_active = 'RealEstate' == wp_get_theme();
			if ( ! $realestate_active ) {
				wp_enqueue_script( 'rlt_select_script', plugins_url( 'js/select2.min.js', __FILE__ ), array( 'jquery' ) );
			}
			wp_enqueue_script( 'slick.min.js', plugins_url( 'js/slick.min.js', __FILE__ ) ); /* including scripts for slick*/

			/* All dependencies ( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ) are described in the registration 'rlt_script' */
			wp_enqueue_script( 'rlt_script' );
			$translation_array = array(
				'rlt_permalink'		=> get_option( 'rewrite_rules' ),
				'realestate_active'	=> $realestate_active
			);
			wp_localize_script( 'rlt_script', 'rlt_translation', $translation_array );
		}
	}
}

if ( ! function_exists ( 'rlt_admin_enqueue_scripts' ) ) {
	function rlt_admin_enqueue_scripts() {
		if ( isset( $_REQUEST['page'] ) && 'realty_settings' == $_REQUEST['page'] ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
		wp_enqueue_style( 'rlt_stylesheet', plugins_url( 'css/admin-style.css', __FILE__ ) );
		wp_enqueue_script( 'rlt_script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ) );
		wp_enqueue_style( 'rlt_icon_stylesheet', plugins_url( 'css/icon.css', __FILE__ ) );

		$translation_array = array(
			'rlt_delete_image' => __( 'Delete', 'realty' )
		);
		wp_localize_script( 'rlt_script', 'rlt_translation', $translation_array );
	}
}

if ( ! function_exists ( 'rlt_theme_body_classes' ) ) {
	function rlt_theme_body_classes( $classes ) {
        global $rlt_options;
		$current_theme = wp_get_theme();
		$classes[] = 'rlt_' . basename( $current_theme->get( 'ThemeURI' ) );
        if( isset( $rlt_options['rlt_price'] ) && 'hide' == $rlt_options['rlt_price'] ){
         $classes[] = 'rlt_hide_price';
        }
		return $classes;
	}
}

if ( ! function_exists( 'rlt_template_include' ) ) {
	function rlt_template_include( $template ) {
		global $post, $wp_query;

		if ( function_exists( 'is_embed' ) && is_embed() )
			return $template;

		if ( ! empty( $post->ID ) && 'property' == get_post_type( $post->ID ) && ! isset( $_POST['rlt_action'] ) ) {
			$file = 'rlt-listing.php';
		} elseif ( isset( $wp_query->query_vars['property_search_results'] ) || ( isset( $_POST['rlt_action'] ) && 'listing_search' == $_POST['rlt_action'] ) || isset( $wp_query->query_vars['property_paged'] ) ) {
			$file = 'rlt-search-listing-results.php';
		}

		if ( isset( $file ) ) {
			wp_register_script( 'rlt_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ), false, true );

			$find = array( $file, 'bws-templates/' . $file );
			$template = locate_template( $find );

			if ( ! $template )
				$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' . $file;
		}

		return $template;
	}
}

if ( ! function_exists( 'rlt_query_vars' ) ) {
	function rlt_query_vars( $query_vars ) {
		$query_vars[] = 'property_paged';
		$query_vars[] = 'property_search_results';
		$query_vars[] = 'property_sortby';
		$query_vars[] = 'property_location';
		$query_vars[] = 'property_type';
		$query_vars[] = 'property_min_price';
		$query_vars[] = 'property_max_price';
		$query_vars[] = 'property_bath';
		$query_vars[] = 'property_bed';
		$query_vars[] = 'property_type_info';
		return $query_vars;
	}
}

if ( ! function_exists( 'rlt_custom_permalinks' ) ) {
	function rlt_custom_permalinks( $rules ) {
		$newrules = array();
		/* Property page */
		if ( ! isset( $rules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] ) ) {
			/* Property search results with all fields */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[8]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_min_price=$matches[3]&property_max_price=$matches[4]&property_bath=$matches[5]&property_bed=$matches[6]&property_type_info=$matches[7]';
			/* Property search results with all fields and paged */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[8]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_min_price=$matches[3]&property_max_price=$matches[4]&property_bath=$matches[5]&property_bed=$matches[6]&property_type_info=$matches[7]&property_paged=$matches[9]';
			/* Property search results without location field */
			$newrules['property_search_results/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[7]&property_search_results=1&property_type=$matches[1]&property_min_price=$matches[2]&property_max_price=$matches[3]&property_bath=$matches[4]&property_bed=$matches[5]&property_type_info=$matches[6]';
			/* Property search results without location field and with paged */
			$newrules['property_search_results/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[7]&property_search_results=1&property_type=$matches[1]&property_min_price=$matches[2]&property_max_price=$matches[3]&property_bath=$matches[4]&property_bed=$matches[5]&property_type_info=$matches[6]&property_paged=$matches[8]';
			/* Property search results without price field and with paged */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_type_info=$matches[5]&property_paged=$matches[7]';
			/* Property search results without price field */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_type_info=$matches[5]';
			/* Property search results without location and price field */
			$newrules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_type=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_type_info=$matches[4]';
			/* Property search results without location and price field and with paged */
			$newrules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_type=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_type_info=$matches[4]&property_paged=$matches[6]';
			/* Property search results without location and property type */
			$newrules['property_search_results/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_min_price=$matches[1]&property_max_price=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_type_info=$matches[5]';
			/* Property search results without location and property type with paged */
			$newrules['property_search_results/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_min_price=$matches[1]&property_max_price=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_type_info=$matches[5]&property_paged=$matches[7]';
			/* Property search results without price field and property type with paged */
			$newrules['property_search_results/loc-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_location=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_type_info=$matches[4]&property_paged=$matches[6]';
			/* Property search results without price field property type */
			$newrules['property_search_results/loc-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_location=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_type_info=$matches[4]';
			/* Property search results without location, property type and price field */
			$newrules['property_search_results/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[4]&property_search_results=1&property_bath=$matches[1]&property_bed=$matches[2]&property_type_info=$matches[3]';
			/* Property search results without location, property type and price field and with paged */
			$newrules['property_search_results/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[4]&property_search_results=1&property_bath=$matches[1]&property_bed=$matches[2]&property_type_info=$matches[3]&property_paged=$matches[5]';
		}
		if ( false === $rules ) {
			return $newrules;
		}

		return $newrules + $rules;
	}
}

/* flush_rules() if our rules are not yet included */
if ( ! function_exists( 'rlt_flush_rules' ) ) {
	function rlt_flush_rules() {
		$rules = get_option( 'rewrite_rules' );
		if ( ! isset( $rules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}
}

if ( ! function_exists( 'realty_request_uri' ) ) {
	function realty_request_uri( $url, $type, $permalink_structure, $sort = '' ) {
		global $rlt_form_vars;
		if ( 'property' == $type ) {
			if ( empty( $permalink_structure ) ) {
				$url .= '?post_type=property&s=properties&property_search_results=1';
				if ( isset( $rlt_form_vars['property_location'] ) ) {
					$url .= '&property_location=' . $rlt_form_vars['property_location'];
				} if ( isset( $rlt_form_vars['property_type'] ) && 'all' != $rlt_form_vars['property_type'] ){ 
					$url .= '&property_type=' . $rlt_form_vars['property_type'];
				} if ( isset( $rlt_form_vars['property_min_price'] ) ) {
					$url .= '&property_min_price=' . $rlt_form_vars['property_min_price'];
				} if ( isset( $rlt_form_vars['property_max_price'] ) ) {
					$url .= '&property_max_price=' . $rlt_form_vars['property_max_price'];
				} if ( isset( $rlt_form_vars['property_bath'] ) ) {
					$url .= '&property_bath=' . $rlt_form_vars['property_bath'];
				} if ( isset( $rlt_form_vars['property_bed'] ) ) {
					$url .= '&property_bed=' . $rlt_form_vars['property_bed'];
				} if ( isset( $rlt_form_vars['property_type_info'] ) ) {
					$url .= '&property_type_info=' . $rlt_form_vars['property_type_info'];
				} if ( ! empty( $sort ) && 'price' == $rlt_form_vars['property_sort_by'] ) {
					$url .= '&property_sort_by=newest';
				} else if ( ! empty( $sort ) && 'newest' == $rlt_form_vars['property_sort_by'] ) {
					$url .= '&property_sort_by=price';
				} else if ( ! empty( $rlt_form_vars['property_sort_by'] ) )
					$url .= '&property_sort_by=' . $rlt_form_vars['property_sort_by'];
			} else {
				$url .= 'property_search_results/';
				if ( isset( $rlt_form_vars['property_location'] ) ) {
					$url .= 'loc-' . $rlt_form_vars['property_location'] . '/';
				} if ( isset( $rlt_form_vars['property_type'] ) && 'all' != $rlt_form_vars['property_type'] ) {
					$url .= 'prop-' . $rlt_form_vars['property_type'] . '/';
				} if ( isset( $rlt_form_vars['property_min_price'] ) ) {
					$url .= 'minp-' . $rlt_form_vars['property_min_price'] . '/';
				} if ( isset( $rlt_form_vars['property_max_price'] ) ) {
					$url .= 'maxp-' . $rlt_form_vars['property_max_price'] . '/';
				} if ( isset( $rlt_form_vars['property_bath'] ) ) {
					$url .= 'bath-' . $rlt_form_vars['property_bath'] . '/';
				} if ( isset( $rlt_form_vars['property_bed'] ) ) {
					$url .= 'bed-' . $rlt_form_vars['property_bed'] . '/';
				} if ( isset( $rlt_form_vars['property_type_info'] ) ) {
					$url .= 'type-' . $rlt_form_vars['property_type_info'] . '/';
				} if ( ! empty( $sort ) && ( 'price' == $rlt_form_vars['property_sort_by'] || 'property_info_price' == $rlt_form_vars['property_sort_by'] ) )
					$url .= 'sort-newest/';
				else if ( ! empty( $sort ) && ( 'newest' == $rlt_form_vars['property_sort_by'] || 'post_date' == $rlt_form_vars['property_sort_by'] ) ) {
					$url .= 'sort-price/';
				} else if ( ! empty( $rlt_form_vars['property_sort_by'] ) )
					$url .= 'sort-' . $rlt_form_vars['property_sort_by'] . '/';
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'rlt_formatting_price' ) ) {
	function rlt_formatting_price( $price, $with_currency = false ) {
		if ( 0 == fmod( $price, 1 ) ) {
			$price = number_format( intval( $price ), 0, '.', ',' );
		}
		$currency_position = rlt_get_currency();
		if ( ! empty( $currency_position ) && true == $with_currency ) {
			if ( 'before' == $currency_position[1] )
				return $currency_position[0] . $price;
			else
				return $price . ' ' . $currency_position[0];
		} else
			return $price;
	}
}

if ( ! function_exists( 'rlt_check_form_vars' ) ) {
	function rlt_check_form_vars( $view_all = false ) {
		global $rlt_form_vars, $wp_query, $wpdb;
		if ( true == $view_all ) {
			if ( empty( $rlt_form_vars ) ) {
				$rlt_form_vars = array(
					'property_type'			=> 'all',
					'property_min_price'	=> 0,
					'property_max_price'	=> 0,
					'property_bath'			=> 1,
					'property_bed'			=> 1,
					'property_type_info'	=> 'sale',
					'property_sort_by'		=> 'newest'
				);
			}
		} else if ( isset( $wp_query->query_vars['property_search_results'] ) || ( isset( $_REQUEST['rlt_action'] ) && 'listing_search' == $_REQUEST['rlt_action'] ) ) {
			$rlt_form_vars['current_page'] = $_SESSION['current_page'] = isset( $wp_query->query_vars['property_paged'] ) ? $wp_query->query_vars['property_paged'] : ( isset( $_REQUEST['property_paged'] ) ? $_REQUEST['property_paged'] : 1 );
			$rlt_form_vars['property_sort_by'] = $_SESSION['property_sort_by'] = isset( $wp_query->query_vars['property_sortby'] ) ? $wp_query->query_vars['property_sortby'] : ( isset( $_REQUEST['property_sort_by'] ) ? $_REQUEST['property_sort_by'] : 'newest' );
			$rlt_form_vars['property_type'] = $_SESSION['property_type'] = isset( $wp_query->query_vars['property_type'] ) ? esc_attr( urldecode( $wp_query->query_vars['property_type'] ) ) : ( isset( $_REQUEST['rlt_property'] ) ? esc_attr( urldecode( $_REQUEST['rlt_property'] ) ) : null );
			$rlt_form_vars['property_location'] = $_SESSION['property_location'] = ! empty( $wp_query->query_vars['property_location'] ) ? esc_attr( urldecode( $wp_query->query_vars['property_location'] ) ) : ( ! empty( $_REQUEST['rlt_location'] ) ? esc_attr( urldecode( $_REQUEST['rlt_location'] ) ) : null );
			$rlt_form_vars['property_bath'] = $_SESSION['property_bath'] = isset( $wp_query->query_vars['property_bath'] ) ? $wp_query->query_vars['property_bath'] : ( isset( $_REQUEST['rlt_bathrooms'] ) ? $_REQUEST['rlt_bathrooms'] : null );
			$rlt_form_vars['property_bed'] = $_SESSION['property_bed'] = isset( $wp_query->query_vars['property_bed'] ) ? $wp_query->query_vars['property_bed'] : ( isset( $_REQUEST['rlt_bedrooms'] ) ? $_REQUEST['rlt_bedrooms'] : null );
			$rlt_form_vars['property_min_price'] = $_SESSION['property_min_price'] = isset( $wp_query->query_vars['property_min_price'] ) ? $wp_query->query_vars['property_min_price'] : ( isset( $_REQUEST['rlt_min_price'] ) ? $_REQUEST['rlt_min_price'] : null );
			$rlt_form_vars['property_max_price'] = $_SESSION['property_max_price'] = isset( $wp_query->query_vars['property_max_price'] ) ? $wp_query->query_vars['property_max_price'] : ( isset( $_REQUEST['rlt_max_price'] ) ? $_REQUEST['rlt_max_price'] : null );
			$rlt_form_vars['property_type_info'] = $_SESSION['property_type_info'] = isset( $wp_query->query_vars['property_type_info'] ) ? $wp_query->query_vars['property_type_info'] : ( isset( $_REQUEST['rlt_info_type'] ) ? $_REQUEST['rlt_info_type'] : null );
		} else if ( is_single() && 'property' == get_post_type() ) {
			$rlt_form_vars['current_page']			= isset( $_SESSION['current_page'] ) ? $_SESSION['current_page'] : 1;
			$rlt_form_vars['property_sort_by']		= isset( $_SESSION['property_sort_by'] ) ? $_SESSION['property_sort_by'] : 'newest';
			$rlt_form_vars['property_type']			= isset( $_SESSION['property_type'] ) ? esc_attr( urldecode( $_SESSION['property_type'] ) ) : null;
			$rlt_form_vars['property_location']		= isset( $_SESSION['property_location'] ) ? esc_attr( urldecode( $_SESSION['property_location'] ) ) : null;
			$rlt_form_vars['property_bath']			= isset( $_SESSION['property_bath'] ) ? $_SESSION['property_bath'] : null;
			$rlt_form_vars['property_bed']			= isset( $_SESSION['property_bed'] ) ? $_SESSION['property_bed'] : null;
			$rlt_form_vars['property_min_price']	= isset( $_SESSION['property_min_price'] ) ? $_SESSION['property_min_price'] : null;
			$rlt_form_vars['property_max_price']	= isset( $_SESSION['property_max_price'] ) ? $_SESSION['property_max_price'] : null;
			$rlt_form_vars['property_type_info']	= isset( $_SESSION['property_type_info'] ) ? $_SESSION['property_type_info'] : null;
		}
	}
}

if ( ! function_exists ( 'rlt_search_nav' ) ) {
	function rlt_search_nav(){
		global $rlt_property_info_count_all_results, $limit, $current_page;
		if ( ! empty( $rlt_property_info_count_all_results ) ) {
			$all_results = $rlt_property_info_count_all_results;
			$replace_paged = 'property_paged=';

			$max_num_pages = $all_results % $limit > 0 ? intval( $all_results / $limit ) + 1 : intval( $all_results / $limit );
			if ( '' == get_option('permalink_structure') ) {
				$base = str_replace( 'paged=', $replace_paged, preg_replace( '/&#038;' . $replace_paged . '(\d+)/i', '', esc_url( get_pagenum_link( 99999 ) ) ) );
				$base = preg_replace( '/&#038;s&#038;/i', '&#038;s=&#038;', $base );
				$search = "property_paged=99999";
				$replacement = "property_paged=%#%";
			} else {
				$base = esc_url( get_pagenum_link( 99999 ) );
				$search = "page/99999";
				$replacement = "page/%#%";
			}

			$args = array(
				'base'			=> str_replace( $search, $replacement, $base ),
				'total'			=> $max_num_pages,
				'current'		=> $current_page,
				'end_size'		=> 1, /* How many pages at start and at the end. */
				'mid_size'		=> 1, /* How many pages before and after current page. */
				'prev_text'		=> __( 'Prev', 'realty' ),
				'next_text'		=> __( 'Next', 'realty' ),
				'type'			=> 'plain'
			);

			if ( 1 != $current_page || $all_results > $limit * $current_page ) { ?>
				<div class="page-link">
					<?php echo paginate_links( $args ); ?>
				</div>
			<?php }
		}
	}
}

if ( ! function_exists( 'rlt_paginate_links' ) ) {
	function rlt_paginate_links( $link ) {
		global $wp_current_filter;
		if ( ! in_array( 'rlt_search_nav', $wp_current_filter ) ) {
			return $link;
		}
		if ( '' != get_option( 'permalink_structure' ) ) {
			return $link;
		}
		$array_link = explode( '?', str_replace( '#038;', '&', $link ) );

		if ( ! is_array( $array_link ) ) {
			return $link;
		}
		parse_str( $array_link[1], $array );
		$string = '';
		foreach( $array as $key => $value ) {
			if ( $string ) {
				$string .= '&';
			}
			$string .= $key . '=' . $value;
		}
		$link = $array_link[0] . '?' . $string;
		return $link;
	}
}

if ( ! function_exists( 'rlt_get_currency' ) ) {
	function rlt_get_currency() {
		global $rlt_options, $wpdb;
		if ( empty( $rlt_options ) ) {
			$rlt_options = get_option( 'rlt_options' );
		}

        $currency = $wpdb->get_var( 'SELECT `currency_unicode` FROM `' . $wpdb->prefix . 'realty_currency` WHERE `currency_id` = ' . $rlt_options['currency_unicode'] );
        if ( empty( $currency ) ) {
            $currency = '&#36;';
        }

		$position = $rlt_options['currency_position'];
		return array( $currency, $position );
	}
}

if ( ! function_exists( 'rlt_get_unit_area' ) ) {
	function rlt_get_unit_area() {
		global $rlt_options;
		if ( empty( $rlt_options ) ) {
			$rlt_options = get_option( 'rlt_options' );
		}

        if ( 'm2' == $rlt_options['unit_area'] ) {
            return 'm&sup2';
        } else {
            return 'ft&sup2';
        }
	}
}

/* this function add custom fields and images for PDF&Print plugin in Agent post and Property post */
if ( ! function_exists( 'rlt_add_pdf_print_content' ) ) {
	function rlt_add_pdf_print_content( $content ) {
		global $post, $wp_query, $wpdb;
		$current_post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : get_post_type();
		$custom_content = '';
		$types = rlt_get_types();
		$periods = rlt_get_periods();
		if ( 'property' == $current_post_type ) {
			$property_info = $wpdb->get_row( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_info` WHERE `property_info_post_id` = ' . $post->ID, ARRAY_A );
			$custom_content .= '<div class="rlt_home_info">
					<ul>
						<li>' . $property_info['property_info_location'] . '</li>
						<li>' . $property_info['property_info_bedroom'] . ' ' . _n( 'bedroom', 'bedrooms', absint( $property_info['property_info_bedroom'] ), 'realty' ) . ', ' . $property_info['property_info_bathroom'] . ' ' . _n( 'bathroom', 'bathrooms', absint( $property_info['property_info_bathroom'] ), 'realty' ) . '</li>
						<li>' . $property_info['property_info_square'] . ' ' . rlt_get_unit_area() . '</li>
					</ul>
				</div>
				<div class="home_footer">
					<a class="' . ( ! empty( $property_info['property_info_period'] ) ? "rent" : "sale" ) . '" href="' . get_permalink() . '">' . $types[ $property_info['property_info_type'] ] . '</a>
					<span class="home_cost">' . apply_filters( 'rlt_formatting_price', $property_info['property_info_price'], true );
						if ( ! empty( $property_info['property_info_period'] ) ) {
							$custom_content .= '<sup>' . "/" . $periods[ $property_info['property_info_period'] ] . '</sup>';
						}
					$custom_content .= '</span>
				</div>';
		}
		return $content . $custom_content;
	}
}

/* add help tab */
if ( ! function_exists( 'rlt_add_tabs' ) ) {
	function rlt_add_tabs() {
		$screen = get_current_screen();
		if ( ( ! empty( $screen->post_type ) && 'property' == $screen->post_type ) ||
			( isset( $_GET['page'] ) && 'realty_settings' == $_GET['page'] ) ) {
			$args = array(
				'id'		=> 'rlt',
				'section'	=> '200930549'
			);
			bws_help_tab( $screen, $args );
		}
	}
}

if ( ! function_exists ( 'rlt_plugin_action_links' ) ) {
	function rlt_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
			if ( $file == $this_plugin ){
				$settings_link = '<a href="edit.php?post_type=property&page=realty_settings">' . __( 'Settings', 'realty' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists ( 'rlt_register_plugin_links' ) ) {
	function rlt_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="edit.php?post_type=property&page=realty_settings">' . __( 'Settings', 'realty' ) . '</a>';
			}
			$links[] = '<a href="https://wordpress.org/plugins/realty/faq/" target="_blank">' . __( 'FAQ', 'realty' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'realty' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists( 'rlt_theme_banner' ) ) {
	function rlt_theme_banner() {
		global $rlt_options;

		if ( empty( $rlt_options ) )
			$rlt_options = get_option( 'rlt_options' );

		if ( isset( $_REQUEST['rlt_hide_theme_banner'] ) ) {
			$rlt_options['theme_banner'] = 0;
			update_option( 'rlt_options', $rlt_options );
			return;
		}

		if ( 'RealEstate' != wp_get_theme() && isset( $rlt_options['theme_banner'] ) && $rlt_options['theme_banner'] ) { ?>
            <div class="updated bws-notice" style="position: relative;">
                <form action="" method="post">
                    <button class="notice-dismiss bws_hide_demo_notice" title="<?php _e( 'Close notice', 'bestwebsoft' ); ?>"></button>
                    <input type="hidden" name="rlt_hide_theme_banner" value="hide" />
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'rlt_nonce_name' ); ?>
                </form>
                <p>
					<?php printf(
						__( "Your theme may not fully support the Realty plugin features. We recommend to install the %s theme that is fully compatible with the plugin.", 'realty' ),
						'<a href="https://bestwebsoft.com/products/real-estate-creative-wordpress-theme/" target="_blank">Real Estate</a>'
					); ?>
                </p>
            </div>
		<?php }
	}
}

/*
 * Function for adding all functionality for updating
 */
if ( ! function_exists ( 'rlt_plugin_banner' ) ) {
	function rlt_plugin_banner() {
		global $hook_suffix, $rlt_plugin_info;
		if ( 'plugins.php' == $hook_suffix ) {
			bws_plugin_banner_to_settings( $rlt_plugin_info, 'rlt_options', 'realty', 'edit.php?post_type=property&page=realty_settings', 'post-new.php?post_type=property' );
		}

		if ( isset( $_REQUEST['page'] ) && 'realty_settings' == $_REQUEST['page'] ) {
			bws_plugin_suggest_feature_banner( $rlt_plugin_info, 'rlt_options', 'realty' );
		}
		rlt_theme_banner();
	}
}

if ( ! function_exists( 'rlt_plugin_uninstall' ) ) {
	function rlt_plugin_uninstall() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugins_list = get_plugins();

		if ( ! array_key_exists( 'realty-pro/realty-pro.php', $plugins_list ) ) {
			if ( is_multisite() ) {
				$old_blog = $wpdb->blogid;
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					rlt_plugin_uninstall_single();
				}
				switch_to_blog( $old_blog );
			} else {
				rlt_plugin_uninstall_single();
			}
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

if ( ! function_exists( 'rlt_plugin_uninstall_single' ) ) {
	function rlt_plugin_uninstall_single() {
		global $wpdb;

		/* Delete any tables */
		$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_info`' );
		$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_currency`' );
		$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_period`' );
		$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_type`' );

		$customs = get_posts( array( 'post_type' => array( 'property' ), 'posts_per_page' => -1 ) );
		foreach ( $customs as $custom ) {
			/* Delete's each post. */
			wp_delete_post( $custom->ID, true );
		}

		$terms = get_terms( array( 'property_type' ), array( 'hide_empty' => 0 ) );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				wp_delete_term( $term->term_id, $term->taxonomy );
			}
		}
		/* Delete any options thats stored */
		delete_option( 'rlt_options' );
	}
}
if ( ! function_exists( 'rlt_search_form' ) ) {
	function rlt_search_form() {
		global $rlt_count_results, $rlt_property_info_count_all_results; ?>

        <div class="search_results">
            <?php if ( $rlt_count_results > 0 ) { ?>
                <span><?php echo $rlt_count_results; ?></span><?php _e( 'results from', 'realty' ); ?> <span><?php echo $rlt_property_info_count_all_results; ?></span> <?php _e( 'total', 'realty' ); ?>
            <?php } ?>
        </div>
        <?php the_widget( 'Realty_Widget' ); ?>
	<?php }
}

if ( ! function_exists( 'rlt_nothing_found' ) ) {
	function rlt_nothing_found() { ?>
        <div class="entry-content nothing-found rlt-nothing-found">
            <h2><?php _e( 'Nothing Found!', 'realty' ); ?></h2>
        </div><!-- .entry-content -->
	<?php }
}


if ( ! function_exists( 'rlt_get_search_listing_results' ) ) {
	function rlt_get_search_listing_results() {
		global $post, $rlt_count_results, $rlt_property_info_count_all_results, $rlt_form_action, $rlt_form_vars, $limit, $current_page, $rlt_options, $wpdb;
		if ( empty( $rlt_options ) ) {
			$rlt_options = get_option( 'rlt_options' );
		}

		do_action( 'rlt_check_form_vars' );

		$current_page = $rlt_form_vars['current_page'];

		$property_sort_by = 'newest' == $rlt_form_vars['property_sort_by'] ? $wpdb->posts . '.post_date' : $wpdb->prefix . 'realty_property_info.property_info_price';

		if ( ! empty( $rlt_form_vars['property_type'] ) && 'all' != $rlt_form_vars['property_type'] ) {
			$property_args = array(
				'post_type'			=> 'property',
				'property_type'		=> $rlt_form_vars['property_type'],
				'fields'			=> 'ids',
				'posts_per_page'	=> -1
			);
		} else {
			$property_args = array(
				'post_type'			=> 'property',
				'fields'			=> 'ids',
				'posts_per_page'	=> -1
			);

		}

		$query = new WP_Query( $property_args );

		$rlt_count_results = $rlt_property_info_count_all_results = 0;
		$limit = $rlt_options['per_page'];
		$property_info_results = array();
		$types = rlt_get_types();
		$periods = rlt_get_periods();
		if ( $query->post_count > 0 ) {
			$posts_id = implode( ',', $query->posts );
			$where = '';
			if ( ! empty( $rlt_form_vars['property_location'] ) ) {
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_location LIKE "%' . $rlt_form_vars['property_location'] . '%"';
			} if ( ! empty( $rlt_form_vars['property_bath'] ) ) {
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_bathroom >= ' . $rlt_form_vars['property_bath'];
			} if ( ! empty( $rlt_form_vars['property_bed'] ) ) {
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_bedroom >= ' . $rlt_form_vars['property_bed'];
			} if ( ! empty( $rlt_form_vars['property_min_price'] ) ) {
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_price >= ' . ( $rlt_form_vars['property_min_price'] );
			} if ( ! empty( $rlt_form_vars['property_max_price'] ) ) {
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_price <= ' . ( $rlt_form_vars['property_max_price'] );
			} if ( ! empty( $rlt_form_vars['property_type_info'] ) )
				$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_type = '.'"'. ( $rlt_form_vars['property_type_info'] ).'"';

			$search_propety_sql = 'SELECT ' . $wpdb->posts . '.ID,
					' . $wpdb->posts . '.post_title,
					' . $wpdb->prefix . 'realty_property_info.*
				FROM ' . $wpdb->posts . '
					INNER JOIN ' . $wpdb->prefix . 'realty_property_info ON ' . $wpdb->prefix . 'realty_property_info.property_info_post_id = ' . $wpdb->posts . '.ID
					WHERE ' . $wpdb->posts . '.ID IN (' . $posts_id . ')
				' . $where . '
				ORDER BY ' . $property_sort_by . ' DESC
				LIMIT ' . ( $current_page - 1 ) * $limit . ', ' . $limit . '
			';

			$property_info_results = $wpdb->get_results( $search_propety_sql, ARRAY_A );

			$rlt_count_results = count( $property_info_results );

			if ( $rlt_count_results == $limit || $current_page > 1 ) {
				$search_propety_count_sql = 'SELECT COUNT(*)
					FROM ' . $wpdb->posts . '
						INNER JOIN ' . $wpdb->prefix . 'realty_property_info ON ' . $wpdb->prefix . 'realty_property_info.property_info_post_id = ' . $wpdb->posts . '.ID
						WHERE ' . $wpdb->posts . '.ID IN (' . $posts_id . ')
						' . $where . '
				';
				$rlt_property_info_count_all_results = $wpdb->get_var( $search_propety_count_sql );
			} else {
				$rlt_property_info_count_all_results = $rlt_count_results;
			}
		}

		wp_reset_query();

		$class_sort_newest = $class_sort_price = '';
		if ( isset( $rlt_form_vars['property_sort_by'] ) && count( $property_info_results ) > 0 ) {
			if ( 'newest' == $rlt_form_vars['property_sort_by'] ) {
				$class_sort_newest = 'current';
				$rlt_newest_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), '' );
				$rlt_price_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), 'sort' );
			} else if ( 'price' == $rlt_form_vars['property_sort_by'] ) {
				$class_sort_price = 'current';
				$rlt_newest_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), 'sort' );
				$rlt_price_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), '' );
			}
		}

		if ( isset( $rlt_newest_link ) && isset( $rlt_price_link ) ) { ?>
			<div class="view_more sort_by"><span><?php _e( 'sort by', 'realty' ); ?>:</span><a class="<?php echo $class_sort_newest; ?>" href="<?php echo home_url() . '/' . $rlt_newest_link; ?>"><?php _e( 'newest', 'realty' ); ?> </a> | <a class="<?php echo $class_sort_price; ?>" href="<?php echo home_url() . '/' . $rlt_price_link; ?>"><?php _e( 'price', 'realty' ); ?></a></div>
		<?php }

		if ( count( $property_info_results ) > 0 ) {
			foreach ( $property_info_results as $property_info ) {
				$property_info['property_info_photos'] = unserialize( $property_info['property_info_photos'] ); ?>
				<div class="rlt_home_preview">
					<a href="<?php echo get_permalink( $property_info['ID'] ); ?>">
						<?php if ( has_post_thumbnail( $property_info['ID'] ) ) {
							echo get_the_post_thumbnail( $property_info['ID'], 'realty_search_result' );
						} else {
                            if ( isset( $property_info['property_info_photos'][0] ) ) {
	                            $small_photo = wp_get_attachment_image_src( $property_info['property_info_photos'][0], 'realty_search_result' );
                            }
							if ( isset( $small_photo[0] ) ) { ?>
								<img src="<?php echo $small_photo[0]; ?>" alt="home" />
							<?php } else { ?>
								<img src="http://placehold.it/200x110" alt="default image" />
							<?php }
						} ?>
					</a>
					<div class="rlt_home_info">
						<h4><a href="<?php echo get_permalink( $property_info['ID'] ); ?>"><?php echo $property_info['post_title']; ?></a></h4>
						<ul>
							<li><?php echo $property_info['property_info_location']; ?></li>
							<li><?php echo $property_info['property_info_bedroom'] . ' ' . _n( 'bedroom', 'bedrooms', absint( $property_info['property_info_bedroom'] ), 'realty' ) . ', ' . $property_info['property_info_bathroom'] . ' ' . _n( 'bathroom', 'bathrooms', absint( $property_info['property_info_bathroom'] ), 'realty' ); ?></li>
							<li><?php echo $property_info['property_info_square'] . ' ' . rlt_get_unit_area(); ?></li>
						</ul>
					</div>
					<div class="home_footer">
						<a class="<?php if( ! empty( $property_info['property_info_type'] ) ) echo "rent"; else echo "sale"; ?>" href="<?php echo get_permalink( $property_info['ID'] ); ?>"><?php echo $types[ $property_info['property_info_type'] ]; ?></a>
						<a href="<?php the_permalink(); ?>" class="add">&#160;</a>
							<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $property_info['property_info_price'], true ); ?><sup><?php if ( ! empty( $property_info['property_info_period'] ) ) echo "/" . $periods[ $property_info['property_info_period'] ]; ?></sup></span>
							<div class="clear"></div>
					</div><!-- .home_footer -->
				</div><!-- .rlt_home_preview -->
			<?php } ?>
			<div class="clear"></div>
			<div class="more_rooms"><?php do_action( 'rlt_search_nav' ); ?></div>
		<?php } else {
			rlt_nothing_found();
		}
	}
}

if ( ! function_exists( 'rlt_get_search_listing' ) ) {
	function rlt_get_search_listing() {
		global $post, $wpdb;
		global $realestate_options, $rlt_options, $rlt_plugin_info,$wpdb;
		$taxonomies = array( 'property_type' );
		$args = array(
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> false
		);
		$terms_property_type = get_terms( $taxonomies, $args );
		$types = rlt_get_types();
		$periods = rlt_get_periods();
		$property_info = $wpdb->get_row( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_info` WHERE `property_info_post_id` = ' . $post->ID, ARRAY_A );

		$property_info['property_info_photos'] = unserialize( $property_info['property_info_photos'] );
		$property_type_name = $types[ $property_info['property_info_type'] ];
		$property_period_name = ! empty( $periods[ $property_info['property_info_period'] ] ) ? $periods[ $property_info['property_info_period'] ] : '';
		$count_photos = count( $property_info['property_info_photos'] );
		$bedrooms_bathrooms = $wpdb->get_row( 'SELECT MIN(`property_info_bedroom`) AS `min_bedroom`, MAX(`property_info_bedroom`) AS `max_bedroom`,
				MIN(`property_info_price`) AS `min_price`, MAX(`property_info_price`) AS `max_price`
			FROM `' . $wpdb->prefix . 'realty_property_info`', ARRAY_A );
		if ( 'RealEstate' == wp_get_theme() ) {
			if ( ! empty ( $realestate_options['maps_key'] ) ) {
				$rlt_api_key = $realestate_options['maps_key'];
			} else {
				$rlt_api_key = ( ! empty( $rlt_options['maps_key'] ) ) ? $rlt_options['maps_key'] : '';
			}
		} else {
			$rlt_api_key = ( ! empty( $rlt_options['maps_key'] ) ) ? $rlt_options['maps_key'] : '';
		}
		$form_action = ! get_option( 'permalink_structure' ) ? '?property=property_search_results' : 'property_search_results'; ?>

		<div id="rlt_home_info_full">
			<div class="rlt_home_content_full">
				<div class="rlt_tabs">
					<div class="tab tab_1 active"><?php _e( 'photos', 'realty' ); ?><?php if ( $count_photos > 0 ) { ?> <span>( 1 <?php _e( 'of', 'realty' ); ?> <?php echo $count_photos; ?> )</span><?php } ?></div>
					<?php if ( ! empty( $property_info['property_info_coordinates'] ) && ! empty( $rlt_api_key ) ) { ?>
						<div class="tab tab_2" style="display: none;"><?php _e( 'view street', 'realty' ); ?></div>
						<div class="tab tab_3"><?php _e( 'map', 'realty' ); ?></div>
					<?php } ?>
				</div>
				<div class="rlt_home_content_tab rlt_home_content_1 active">
					<div class="cover"></div>
					<div class="rlt_home_slides_thumbnail">
						<div class="home_image">
							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail();
							} else if ( count( $property_info['property_info_photos'] ) > 0 ) {
								$big_photo = wp_get_attachment_image_src( $property_info['property_info_photos'][0], 'realty_listing' ); ?>
								<img src="<?php echo $big_photo[0]; ?>" alt="home" />
							<?php } ?>
						</div>
					</div>
					<?php if ( $count_photos > 0 ) { ?>
						<div class="rlt_home_slides">
							<div class="rlt_thumbnails">
								<div id="rlt_thumbnails_holder">
									<?php foreach ( $property_info['property_info_photos'] as $photo_id ) {
										$small_photo = wp_get_attachment_image_src( $photo_id, 'realty_small_photo' );
										$big_photo = wp_get_attachment_image_src( $photo_id, 'realty_listing' ); ?>
										<img src="<?php echo $small_photo[0]; ?>" rel="<?php echo $big_photo[0]; ?>" alt="home" />
									<?php } ?>
								</div>
							</div>
						</div><!--end of .rlt_home_slides-->
					<?php }
					wp_reset_postdata(); ?>
				</div><!--end of #rlt_home_content_1-->
				<?php if ( ! empty( $property_info['property_info_coordinates'] ) && ! empty( $rlt_api_key ) ) { ?>
					<div class="rlt_home_content_tab rlt_home_content_2">
						<div class="cover"></div>
						<div style="width:100%; height:420px;">
							<div id="map-canvas"></div>
						</div>
					</div>
					<div class="rlt_home_content_tab rlt_home_content_3">
						<div class="cover"></div>
						<div style="width:100%; height:420px;">
							<div id="map-canvas2"></div>
						</div>
					</div>
                    <?php
                    wp_register_script( 'rlt_google_maps_script', 'https://maps.googleapis.com/maps/api/js?key=' . $rlt_api_key );
                    wp_enqueue_script( 'rlt_google_maps_script' );

                    $script = "var propertyLatlng;
						var map;
						function initialize() {
							propertyLatlng = new google.maps.LatLng(" . $property_info['property_info_coordinates'] . ");
							var mapOptions = {
									zoom: 14,
									center: propertyLatlng
								}
							map = new google.maps.Map( document.getElementById( 'map-canvas2' ), mapOptions );

							var marker = new google.maps.Marker( {
								position: propertyLatlng,
								map: map
							} );

							var panoramaOptions = {
								position: propertyLatlng,
								pov: {
									heading: 30,
									pitch: 10
								}
							};
							var panorama = new google.maps.StreetViewPanorama( document.getElementById( 'map-canvas' ), panoramaOptions );

							var client = new google.maps.StreetViewService();
							var view_tab = document.querySelectorAll( '.tab_2' );

							client.getPanoramaByLocation( propertyLatlng, 50, function( result, status ) {
								if ( status === google.maps.StreetViewStatus.OK ) {
									map.setStreetView( panorama );
									view_tab[1].style.display = 'block';
								}
							} );
						}
						google.maps.event.addDomListener( window, 'load', initialize );";

                    wp_register_script( 'rlt_google_maps_coordinates_script', '//' );
                    wp_enqueue_script( 'rlt_google_maps_coordinates_script' );
                    wp_add_inline_script( 'rlt_google_maps_coordinates_script', sprintf( $script ) );
				} ?>
			</div>
			<div class="rlt_home_description">
				<h3><?php _e( 'General Information', 'realty' ); ?></h3>
				<p><?php the_content(); ?></p>
			</div>
		</div>
		<div class="rlt_search_options rlt_home_info_full">
			<div class="rlt_home_preview">
				<div class="rlt_home_info">
					<h4><?php the_title(); ?></h4>
						<ul>
							<li><?php echo $property_info['property_info_location']; ?></li>
							<li><?php echo $property_info['property_info_bedroom'] . ' ' . _n( 'bedroom', 'bedrooms', absint( $property_info['property_info_bedroom'] ), 'realty' ) . ', ' . $property_info['property_info_bathroom'] . ' ' . _n( 'bathroom', 'bathrooms', absint( $property_info['property_info_bathroom'] ), 'realty' ); ?></li>
							<li><?php echo $property_info['property_info_square'] . ' ' . rlt_get_unit_area(); ?></li>
						</ul>
				</div>
				<div class="home_footer">
					<!-- <a class="<?php if ( ! empty( $property_period_name ) ) echo "rent"; else echo "sale"; ?>" href="<?php the_permalink(); ?>"><?php echo $property_type_name; ?></a> -->
					<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $property_info['property_info_price'], true ); ?><sup><?php if ( ! empty( $property_period_name ) ) echo "/" . $property_period_name; ?></sup></span>
				</div>
			</div>
		</div><!--end of .rlt_search_options-->
	<?php }
}

if ( ! function_exists( 'rlt_get_periods' ) ) {
	function rlt_get_periods() {
		return apply_filters( 'rlt_periods', array( 'month' => __( 'month', 'realty' ), 'year' => __( 'year', 'realty' ) ) );
	}
}

if ( ! function_exists( 'rlt_get_types' ) ) {
	function rlt_get_types() {
		return apply_filters( 'rlt_types', array( 'sale' => __( 'For Sale', 'realty' ), 'rent' => __( 'For Rent', 'realty' ) ) );
	}
}

/* Activate plugin */
register_activation_hook( __FILE__, 'rlt_plugin_activation' );

add_action( 'init', 'rlt_init' );
add_action( 'admin_init', 'rlt_admin_init' );
add_action( 'plugins_loaded', 'rlt_plugins_loaded' );

add_action( 'widgets_init', 'rlt_register_widgets' );
add_action( 'admin_menu', 'rlt_admin_menu' );
add_filter( 'manage_edit-property_columns', 'rlt_property_columns' );
add_action( 'restrict_manage_posts', 'rlt_restrict_manage_property' );
add_action( 'pre_get_posts', 'rlt_property_pre_get_posts' );
add_action( 'save_post', 'rlt_save_postdata', 10, 2 );
add_action( 'before_delete_post', 'rlt_delete_post' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'rlt_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'rlt_register_plugin_links', 10, 2 );

add_action( 'admin_enqueue_scripts', 'rlt_admin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'rlt_enqueue_styles' );
add_action( 'wp_footer', 'rlt_enqueue_scripts' );

add_filter( 'body_class', 'rlt_theme_body_classes' );

add_filter( 'rewrite_rules_array', 'rlt_custom_permalinks' ); /* Add custom permalink for plugin */
add_action( 'wp_loaded', 'rlt_flush_rules' );
add_filter( 'query_vars', 'rlt_query_vars' );
add_filter( 'realty_request_uri', 'realty_request_uri', 10, 4 );
add_filter( 'paginate_links', 'rlt_paginate_links', 10, 1 );

/* this function add custom fields and images for PDF&Print plugin in Property post */
add_filter( 'bwsplgns_get_pdf_print_content', 'rlt_add_pdf_print_content' );

add_filter( 'rlt_formatting_price', 'rlt_formatting_price', 10, 2 );
add_action( 'rlt_check_form_vars', 'rlt_check_form_vars' );
add_action( 'rlt_search_nav', 'rlt_search_nav' );

add_action( 'admin_notices', 'rlt_plugin_banner' );

/* Delete plugin */
register_uninstall_hook( __FILE__, 'rlt_plugin_uninstall' );