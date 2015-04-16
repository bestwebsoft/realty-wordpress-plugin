<?php
/*
Plugin Name: Realty by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: A convenient plugin that adds Real Estate functionality.
Author: BestWebSoft
Version: 1.0.1
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  © Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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

/* Add global variables */
global $rlt_filenames, $rlt_filepath, $rlt_themepath;
$rlt_filepath = WP_PLUGIN_DIR . '/realty/templates/';
$rlt_themepath = get_stylesheet_directory() . '/';
$rlt_filenames[]	=	'rlt-listing.php';
$rlt_filenames[]	=	'rlt-nothing-found.php';
$rlt_filenames[]	=	'rlt-search-form.php';
$rlt_filenames[]	=	'rlt-search-listing-results.php';

/* Add option page in admin menu */
if ( ! function_exists( 'rlt_admin_menu' ) ) {
	function rlt_admin_menu() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Realty Settings', 'realty' ), 'Realty', 'manage_options', 'realty_settings', 'rlt_settings_page' );
	}
}

if ( ! function_exists ( 'rlt_init' ) ) {
	function rlt_init() {
		global $rlt_plugin_info;
		rlt_register_post_type();

		add_image_size( 'realty_search_result', 200, 110, true );
		add_image_size( 'realty_listing', 420, 320, true );
		add_image_size( 'realty_small_photo', 110, 80, true );

		load_plugin_textdomain( 'realty', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );	

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );

		if ( empty( $rlt_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$rlt_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_version_check( plugin_basename( __FILE__ ), $rlt_plugin_info, "3.8" );

		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_REQUEST['page'] ) && 'realty_settings' == $_REQUEST['page'] ) )
			rlt_settings();
		
		if ( ! isset( $_SESSION ) )
			session_start();
	}
}

if ( ! function_exists ( 'rlt_admin_init' ) ) {
	function rlt_admin_init() {
		global $bws_plugin_info, $rlt_plugin_info;
		/* Add variable for bws_menu */
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '205', 'version' => $rlt_plugin_info['Version'] );

		add_rewrite_endpoint( 'realty', EP_PERMALINK );

		add_meta_box( 'property-custom-metabox', __( 'Property Info', 'realty' ), 'rlt_property_custom_metabox', 'property', 'normal', 'high' );
	
		if ( ( isset( $_REQUEST['post_type'] ) && 'property' == $_REQUEST['post_type'] ) || 
			( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] && 'property' == get_post_type() ) ) {
			/* add error if templates were not found in the theme directory */
			rlt_admin_error();
		}
	}
}

if ( ! function_exists ( 'rlt_install' ) ) {
	function rlt_install() {
		global $wpdb;

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
			`property_info_type_id` int(10) unsigned NOT NULL,
			`property_info_period_id` int(10) unsigned NOT NULL,
			`property_info_price` decimal(15,3) NOT NULL,
			`property_info_bathroom` tinyint(3) unsigned NOT NULL,
			`property_info_bedroom` tinyint(3) unsigned NOT NULL,
			`property_info_square` decimal(10,2) NOT NULL,
			`property_info_photos` varchar(1000) NOT NULL,
			PRIMARY KEY (`property_info_id`)
		)ENGINE=InnoDB ' . $charset_collate . ' AUTO_INCREMENT=1';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'realty_currency` (
			`currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`country_currency` char(50) NOT NULL,
			`currency_code` char(3) NOT NULL,
			`currency_hex` char(20) NOT NULL,
			`currency_unicode` char(30) NOT NULL,
			PRIMARY KEY (`currency_id`)
		) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'realty_property_period` (
			`property_period_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`property_period_name` char(100) NOT NULL,
			PRIMARY KEY (`property_period_id`)
		) ENGINE=InnoDB ' . $charset_collate . '';
		dbDelta( $sql );

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'realty_property_type` (
			`property_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`property_type_name` char(100) NOT NULL,
			PRIMARY KEY (`property_type_id`)
		) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );
		
		$wpdb->query( "INSERT INTO `" . $wpdb->prefix . "realty_currency` (`currency_id`, `country_currency`, `currency_code`, `currency_hex`, `currency_unicode`) VALUES
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
				
		$wpdb->query( "INSERT INTO `" . $wpdb->prefix . "realty_property_period` (`property_period_id`, `property_period_name`) VALUES
		(1, 'month'),
		(2, 'year');" );

		$wpdb->query( "INSERT INTO `" . $wpdb->prefix . "realty_property_type` (`property_type_id`, `property_type_name`) VALUES
		(1, 'For Rent'),
		(2, 'For Sale');" );
	}
}

if ( ! function_exists ( 'rlt_register_widgets' ) ) {
	function rlt_register_widgets() {
		register_widget( 'Realty_Widget' );
		register_widget( 'Realty_Resent_Items_Widget' );
	}
}

if ( ! function_exists( 'rlt_plugin_install' ) ) {
	function rlt_plugin_install() {
		global $rlt_filenames, $rlt_filepath, $rlt_themepath;
		foreach ( $rlt_filenames as $filename ) {
			if ( ! file_exists( $rlt_themepath . $filename ) ) {
				$handle		=	@fopen( $rlt_filepath . $filename, "r" );
				$contents	=	@fread( $handle, filesize( $rlt_filepath . $filename ) );
				@fclose( $handle );
				if ( ! ( $handle = @fopen( $rlt_themepath . $filename, 'w' ) ) )
					return false;
				@fwrite( $handle, $contents );
				@fclose( $handle );
				@chmod( $rlt_themepath . $filename, octdec( 644 ) );
			} else {
				$handle		=	@fopen( $rlt_themepath . $filename, "r" );
				$contents	=	@fread( $handle, filesize( $rlt_themepath . $filename ) );
				@fclose( $handle );
				if ( ! ( $handle = @fopen( $rlt_themepath . $filename . '.bak', 'w' ) ) )
					return false;
				@fwrite( $handle, $contents );
				@fclose( $handle );
				
				$handle		=	@fopen( $rlt_filepath . $filename, "r" );
				$contents	=	@fread( $handle, filesize( $rlt_filepath . $filename ) );
				@fclose( $handle );
				if ( ! ( $handle = @fopen( $rlt_themepath . $filename, 'w' ) ) )
					return false;
				@fwrite( $handle, $contents );
				@fclose( $handle );
				@chmod( $rlt_themepath . $filename, octdec( 644 ) );
			}
		}
	}
}

if ( ! function_exists( 'rlt_admin_error' ) ) {
	function rlt_admin_error() {
		global $rlt_filenames, $rlt_filepath, $rlt_themepath;

		$post		=	isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : "" ;
		$post_type	=	isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : get_post_type( $post );

		$file_exists_flag = true;

		if ( 'property' == $post_type || ( isset( $_REQUEST['page'] ) && 'realty_settings' == $_REQUEST['page'] ) ) {
			foreach ( $rlt_filenames as $filename ) {
				if ( ! file_exists( $rlt_themepath . $filename ) ) {
					$file_exists_flag = false;
				}
			}
		}
		if ( ! $file_exists_flag )
			echo '<div class="error"><p><strong>' . __( 'The following files', 'realty' ) . ' "rlt-listing.php" ' . __( 'or', 'realty' ) . ' "rlt-nothing-found.php" ' . __( 'or', 'realty' ) . ' "rlt-search-form.php" ' . __( 'or', 'realty' ) . ' "rlt-search-listing-results.php" ' . __( 'were not found in your theme directory. Please copy them from the directory', 'realty' ) . ' `/wp-content/plugins/realty/templates/` ' . __( 'to your theme directory to make sure Realty plugin works correctly', 'realty' ) . '</strong></p></div>';
	}
}

if ( ! function_exists ( 'rlt_register_post_type' ) ) {
	function rlt_register_post_type() {
		$args = array(
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type'	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor', 'thumbnail' ),
			'labels'			=>	array(
				'name'					=> _x( 'Properties', 'post type general name', 'realty' ),
				'singular_name' 		=> _x( 'Property', 'post type singular name', 'realty' ),		
				'menu_name'				=> _x( 'Properties', 'admin menu', 'realty' ),
				'name_admin_bar'		=> _x( 'Property', 'add new on admin bar', 'realty' ),
				'add_new'				=> _x( 'Add New', 'property', 'realty' ),
				'add_new_item'			=> __( 'Add a new Property', 'realty' ),
				'edit_item'				=> __( 'Edit Properties', 'realty' ),
				'new_item'				=> __( 'New Property', 'realty' ),
				'view_item'				=> __( 'View Properties', 'realty' ),
				'search_items'			=> __( 'Search Properties', 'realty' ),
				'not_found'				=> __( 'No Properties found', 'realty' ),
				'not_found_in_trash'	=> __( 'No Properties found in Trash', 'realty' )
			)
		);
		register_post_type( 'property' , $args );

		$labels = array(
			'name'							=> _x( 'Property types', 'taxonomy general name', 'realty' ),
			'singular_name'	 				=> _x( 'Property type', 'taxonomy singular name', 'realty' ),
			'menu_name'		 				=> __( 'Property type', 'realty' ),
			'all_items'						=> __( 'All Property types', 'realty' ),
			'edit_item'						=> __( 'Edit Property type', 'realty' ),
			'view_item'						=> __( 'View Property type', 'realty' ),
			'update_item'					=> __( 'Update Property type', 'realty' ),
			'add_new_item'		 			=> __( 'Add New Property type', 'realty' ),
			'new_item_name'					=> __( 'New Property type Name', 'realty' ),
			'parent_item'		 			=> __( 'Parent Property type', 'realty' ),
			'parent_item_colon' 			=> __( 'Parent Property type:', 'realty' ),
			'search_items'					=> __( 'Search Property types', 'realty' ),
			'popular_items'	 				=> __( 'Popular Property types', 'realty' ),
			'separate_items_with_commas'	=> __( 'Separate Property types with commas', 'realty' ),
			'add_or_remove_items'			=> __( 'Add or remove Property type', 'realty' ),
			'choose_from_most_used'			=> __( 'Choose from the most used Property type', 'realty' ),
			'not_found'						=> __( 'No Property type found', 'realty' )
		);

		$args = array(
			'hierarchical'		=> true,
			'labels'			=> $labels,
			'show_ui'			=> true,
			'show_tagcloud'		=> false,
			'show_admin_column' => true,
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'property_type' ),
		);

		register_taxonomy( 'property_type', array( 'property' ), $args );
	}
}

if ( ! function_exists( 'rlt_settings' ) ) {
	function rlt_settings() {
		global $rlt_options, $rlt_option_defaults, $wpdb, $bws_plugin_info, $rlt_plugin_info;
		$rlt_db_version = "1.0";	
		
		$rlt_option_defaults = array(
			'plugin_option_version' 		=> $rlt_plugin_info['Version'],
			'currency_custom_display'		=> 0,
			'currency_unicode'				=> '109',
			'custom_currency' 				=> '',
			'currency_position' 			=> 'before',
			'unit_area_custom_display'		=> 0,
			'unit_area'						=> 'sq&#160;ft',
			'custom_unit_area' 				=> '',
			'per_page'						=> get_option( 'posts_per_page' )
		);

		/* Install the option defaults */
		if ( ! get_option( 'rlt_options' ) )
			add_option( 'rlt_options', $rlt_option_defaults );
		$rlt_options = get_option( 'rlt_options' );
		
		if ( ! isset( $rlt_options['plugin_option_version'] ) || $rlt_options['plugin_option_version'] != $rlt_plugin_info['Version'] ) {
			rlt_plugin_install();
			$rlt_options = array_merge( $rlt_option_defaults, $rlt_options );
			$rlt_options['plugin_option_version'] = $rlt_plugin_info['Version'];
			update_option( 'rlt_options', $rlt_options );				
		}	

		if ( ! isset( $rlt_options['plugin_db_version'] ) || $rlt_options['plugin_db_version'] != $rlt_db_version ) {
			rlt_install();
			$rlt_options['plugin_db_version'] = $rlt_db_version;
			update_option( 'rlt_options', $rlt_options );				
		}		
	}
}
if ( ! function_exists( 'rlt_plugin_activation' ) ) {
	function rlt_plugin_activation() {
		rlt_install();
		rlt_plugin_install();
	}
}

if ( ! function_exists( 'rlt_settings_page' ) ) {
	function rlt_settings_page() {
		global $wpdb, $title, $rlt_options, $rlt_filenames, $rlt_filepath, $rlt_themepath, $rlt_plugin_info;
		$error = $message = "";	

		if ( ! isset( $_GET['action'] ) ) {
			$currencies = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "realty_currency`", ARRAY_A );

			if ( isset( $_POST['rlt_form_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'rlt_nonce_name' ) ) {
				$rlt_options['currency_custom_display']		= $_POST['rlt_currency_custom_display'];
				$rlt_options['currency_unicode']			= $_POST['rlt_currency'];
				$rlt_options['custom_currency']				= esc_html( $_POST['rlt_custom_currency'] );
				$rlt_options['currency_position']			= esc_html( $_POST['rlt_currency_position'] );
				$rlt_options['unit_area_custom_display']	= $_POST['rlt_unit_area_custom_display'];
				$rlt_options['unit_area']					= $_POST['rlt_unit_area'];
				$rlt_options['custom_unit_area']			= esc_html( $_POST['rlt_custom_unit_area'] );
				$rlt_options['per_page']					= $_POST['rlt_per_page'];

				if ( $rlt_options['currency_custom_display'] == 1 && empty( $rlt_options['custom_currency'] ) ) {
					$rlt_options['currency_custom_display'] = 0;
					$error = __( 'Please, enter the correct value for custom currency field. Settings not saved.', 'realty' );
				} else {
					update_option( 'rlt_options' , $rlt_options );
					$message = __( 'Settings saved.', 'realty' );
				}
			}
			$file_exists_flag = true;

			foreach ( $rlt_filenames as $filename ) {
				if ( ! file_exists( $rlt_themepath . $filename ) ) {
					$file_exists_flag = false;
				}
			}
			if ( ! $file_exists_flag ) {
				rlt_plugin_install();
				rlt_admin_error();
			}
		}
		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {		
			$go_pro_result = bws_go_pro_tab_check( plugin_basename(__FILE__) );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
		}
		/* Display form on the setting page */ ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php echo $title; ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=realty_settings"><?php _e( 'Settings', 'realty' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/realty/faq/" target="_blank"><?php _e( 'FAQ', 'realty' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=realty_settings&amp;action=go_pro"><?php _e( 'Go PRO', 'realty' ); ?></a>
			</h2>
			<div id="rlt_settings_message" class="updated fade" <?php if ( ! isset( $_REQUEST['rlt_form_submit'] ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { ?>
				<div id="rlt_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'realty' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'realty' ); ?></p></div>
				<form id="rlt_settings_form" method="post" action="admin.php?page=realty_settings">
					<table class="form-table">
						<tr valign="top" class="rlt_currency_labels">
							<th scope="row"><label for="rlt_currency"><?php _e( 'Currency', 'realty' ); ?></label></th>
							<td>
								<input type="radio" name="rlt_currency_custom_display" id="rlt_currency_custom_display_false" value="0" <?php if ( $rlt_options['currency_custom_display'] == 0 ) echo 'checked="checked"'; ?> /> 
								<select name="rlt_currency" id="rlt_currency">
									<?php foreach ( $currencies as $currency ) { ?>
										<option value="<?php echo $currency['currency_id']; ?>" <?php if ( $currency['currency_id'] == $rlt_options['currency_unicode'] ) echo 'selected="selected"'; ?>><?php echo $currency['currency_unicode'] . ' ('.$currency['country_currency'] . " - " . $currency['currency_code'] . ')'; ?></option>
									<?php } ?>
								</select><br />
								<input type="radio" name="rlt_currency_custom_display" id="rlt_currency_custom_display_true" value="1" <?php if ( $rlt_options['currency_custom_display'] == 1 ) echo 'checked="checked"'; ?> /> <input type="text" id="rlt_custom_currency" name="rlt_custom_currency" value="<?php echo $rlt_options['custom_currency']; ?>" /> <span class="rlt_info"><?php _e( 'Custom currency, for example', 'realty' ); ?> $</span>
							</td>
						</tr>
						<tr valign="top" class="rlt_custom_currency_position_labels">
							<th scope="row"><?php _e( 'Currency Position', 'realty' ); ?></th>
							<td>
								<label for="rlt_currency_position_before"><input type="radio" id="rlt_currency_position_before" name="rlt_currency_position" value="before" <?php if( $rlt_options['currency_position'] == 'before' ) echo 'checked="checked"'; ?> /> <?php _e( 'before numerals', 'realty' ); ?></label><br />
								<label for="rlt_currency_position_after"><input type="radio" id="rlt_currency_position_after" name="rlt_currency_position" value="after" <?php if( $rlt_options['currency_position'] == 'after' ) echo 'checked="checked"'; ?> /> <?php _e( 'after numerals', 'realty' ); ?></label>
							</td>
						</tr>					
						<tr valign="top" class="rlt_unit_area_labels">
							<th scope="row"><label for="rlt_unit_area"><?php _e( 'Unit of area', 'realty' ); ?></label></th>
							<td>
								<input type="radio" name="rlt_unit_area_custom_display" id="rlt_unit_area_custom_display_false" value="0" <?php if ( $rlt_options['unit_area_custom_display'] == 0 ) echo 'checked="checked"'; ?> /> 
								<select name="rlt_unit_area" id="rlt_unit_area">
									<option value="sq&#160;ft" <?php if ( 'sq&#160;ft' == $rlt_options['unit_area'] ) echo 'selected="selected"'; ?>>sq&#160;ft</option>
									<option value="m&#178;" <?php if ( 'm&#178;' == $rlt_options['unit_area'] ) echo 'selected="selected"'; ?>>m&#178;</option>
								</select><br />
								<input type="radio" name="rlt_unit_area_custom_display" id="rlt_unit_area_custom_display_true" value="1" <?php if ( $rlt_options['unit_area_custom_display'] == 1 ) echo 'checked="checked"'; ?> /> <input type="text" id="rlt_custom_unit_area" name="rlt_custom_unit_area" value="<?php echo $rlt_options['custom_unit_area']; ?>" /> <span class="rlt_info"><?php _e( 'Custom unit area', 'realty' ); ?></span>
							</td>
						</tr>
						<tr valign="top" class="rlt_per_page_labels">
							<th scope="row"><label for="rlt_per_page"><?php _e( 'Search pages show at most', 'realty' ); ?></label></th>
							<td>
								<input type="number" class="small-text" min="1" step="1" id="rlt_per_page" name="rlt_per_page" value="<?php echo $rlt_options['per_page']; ?>" />
							</td>
						</tr>
					</table>					
					<div class="submit">
						<input type="hidden" name="rlt_form_submit" value="submit" />
						<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'realty' ); ?>" />
						<?php wp_nonce_field( plugin_basename( __FILE__ ), 'rlt_nonce_name' ); ?>
					</div>
				</form>
				<?php bws_plugin_reviews_block( $rlt_plugin_info['Name'], 'realty' );
			} elseif ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab( $rlt_plugin_info, plugin_basename( __FILE__ ), 'realty_settings', 'realty_pro_settings', 'realty-pro/realty-pro.php', 'realty', '', '205', isset( $go_pro_result['pro_plugin_is_activated'] ) ); 
			} ?>
		</div>
	<?php } 
}

if ( ! function_exists ( 'rlt_property_columns' ) ) {
	function rlt_property_columns( $columns ) {
		unset( $columns['date'] );
		$columns['date'] = 'Date';
		return $columns;
	}
}

if ( ! function_exists ( 'rlt_restrict_manage_property' ) ) {
	function rlt_restrict_manage_property() {
		/* only display these taxonomy filters on desired custom post_type listings*/
		global $typenow;
		if ( $typenow == 'property' ) {
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
						<option value='<?php echo $term->term_id; ?>' <?php echo $current_id == $term->term_id ? ' selected="selected"' : ''; ?>><?php echo $term->name .' (' . $term->count .')'; ?></option>
					<?php } ?>
				</select>
			<?php }
		}
	}
}

if ( ! function_exists ( 'rlt_property_pre_get_posts' ) ) {
	function rlt_property_pre_get_posts( $query ) {
		if ( is_admin() && ! empty( $_GET['rlt_property_type_filter'] ) ) {
			if ( intval( $_GET['rlt_property_type_filter'] ) != 0 ) {
				$property_type = intval( $_GET['rlt_property_type_filter'] );
				$tax_query = array(
					array(
						'taxonomy' => 'property_type',
						'field' => 'id',
						'terms' => $property_type
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
		$property_type = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_type`', ARRAY_A );
		$property_periods = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_period`', ARRAY_A );
		$currency = rlt_get_currency(); ?>
		<div class="rlt_left_column">
			<p>
				<label for="rlt_location"><?php _e( 'Location', 'realty' ); ?>:<br />
					<input type="text" id="rlt_location" size="50" name="rlt_location" value="<?php if ( ! empty( $property_info['property_info_location'] ) ) echo $property_info['property_info_location']; ?>"/>
				</label><br />
				<span class="rlt_info"><?php _e( 'For example', 'realty' ); ?>: 6753 Gregory Court, Wheatfield, NY 14120</span>
			</p>
			<p>
				<label for="rlt_type"><?php _e( 'Type', 'realty' ); ?>:<br />
					<select name="rlt_type" id="rlt_type">
						<?php foreach ( $property_type as $p_type ) { ?>
							<option value="<?php echo $p_type['property_type_id']; ?>" <?php if ( ! empty( $property_info['property_info_type_id'] ) && $property_info['property_info_type_id'] == $p_type['property_type_id'] ) echo 'selected="selected"' ?>><?php echo $p_type['property_type_name']; ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<p>
				<label for="rlt_price"><?php _e( 'Price', 'realty' ); ?>:<br />
					<input type="text" id="rlt_price" size="10" name="rlt_price" value="<?php if ( ! empty( $property_info['property_info_price'] ) ) echo $property_info['property_info_price']; ?>"/>
				</label> (<?php echo $currency[0]; ?>)<br />
				<span class="rlt_info"><?php _e( 'For example', 'realty' ); ?>: 25852.000</span>
			</p>
			<p>
				<label for="rlt_bedroom"><?php _e( 'Bedrooms', 'realty' ); ?>:<br />
					<input type="number" id="rlt_bedroom" min="1" name="rlt_bedroom" value="<?php if ( ! empty( $property_info['property_info_bedroom'] ) ) echo $property_info['property_info_bedroom']; ?>"/>
				</label>
			</p>
		</div>
		<div class="rlt_right_column">
			<p>
				<label for="rlt_coordinates"><?php _e( 'Latitude and longitude coordinates', 'realty' ); ?>:<br />
					<input type="text" id="rlt_coordinates" size="50" name="rlt_coordinates" value="<?php if ( ! empty( $property_info['property_info_coordinates'] ) ) echo $property_info['property_info_coordinates']; ?>"/>
				</label><br />
				<span class="rlt_info"><?php _e( 'For example', 'realty' ); ?>: 43.097585,-78.870621</span>
			</p>
			<p>
				<label for="rlt_period"><?php _e( 'Period', 'realty' ); ?>:<br />
					<select name="rlt_period" id="rlt_period">
						<option value="0" <?php if ( ! empty( $property_info['property_info_period_id'] ) && $property_info['property_info_period_id'] == 0 ) echo 'selected="selected"' ?>></option>
						<?php foreach ( $property_periods as $p_period ){ ?>
							<option value="<?php echo $p_period['property_period_id']; ?>" <?php if ( ! empty( $property_info['property_info_period_id'] ) && $property_info['property_info_period_id'] == $p_period['property_period_id'] ) echo 'selected="selected"' ?>><?php echo $p_period['property_period_name']; ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<p>
				<label for="rlt_bathroom"><?php _e( 'Bathrooms', 'realty' ); ?>:<br />
					<input type="number" id="rlt_bathroom" min="1" name="rlt_bathroom" value="<?php if ( ! empty( $property_info['property_info_bathroom'] ) ) echo $property_info['property_info_bathroom']; ?>"/>
				</label>
			</p>
			<p>
				<label for="rlt_square"><?php _e( 'Floor area', 'realty' ); ?>:<br />
					<input type="text" id="rlt_square" name="rlt_square" value="<?php if ( ! empty( $property_info['property_info_square'] ) ) echo $property_info['property_info_square']; ?>"/>
				</label> (<?php echo rlt_get_unit_area(); ?>)<br />
				<span class="rlt_info"><?php _e( 'For example', 'realty' ); ?>: 21820.00</span>
			</p>		
		</div>
		<p>
			<label for="rlt_photos"><?php _e( 'Photos', 'realty' ); ?>:</label><br />
			<button class="rlt_add_photo button"><?php _e( 'Add photo', 'realty' ); ?></button>
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
								<div class="rlt_delete"><a href="javascript:void(0);" onclick="img_delete( <?php echo $rlt_photo; ?> );"><?php _e( 'Delete', 'realty' ) ; ?></a><div/>
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
		</p>	
		<div class="clear"></div>
	<?php }
}

if ( ! function_exists( 'rlt_save_postdata' ) ) {
	function rlt_save_postdata( $post_id ) {
		global $post_type;
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		/* Check if our nonce is set. */
		if ( $post_type != 'property' )
			return $post_id;
		else {
			global $wpdb;
			if ( isset( $_POST[ 'rlt_location' ] ) ) {
				$property_info = array();
				$property_info['property_info_post_id']			= $post_id;
				$property_info['property_info_location']		= esc_js( $_POST['rlt_location'] );
				$property_info['property_info_coordinates'] 	= esc_js( $_POST['rlt_coordinates'] );
				$property_info['property_info_type_id']			= esc_js( $_POST['rlt_type'] );
				$property_info['property_info_period_id']		= esc_js( $_POST['rlt_period'] );
				$property_info['property_info_price']			= esc_js( $_POST['rlt_price'] );
				$property_info['property_info_bathroom']		= esc_js( $_POST['rlt_bathroom'] );
				$property_info['property_info_bedroom']			= esc_js( $_POST['rlt_bedroom'] );
				$property_info['property_info_square']			= esc_js( $_POST['rlt_square'] );
				$property_info['property_info_photos']			= isset( $_POST['rlt_photos'] ) ? $_POST['rlt_photos'] : array();
				if ( ! empty( $_POST[ 'rlt_add_images' ] ) )
					$property_info['property_info_photos'] = array_merge( $property_info['property_info_photos'], $_POST['rlt_add_images'] );

				if ( ! empty( $_POST[ 'rlt_delete_images' ] ) )
					$property_info['property_info_photos'] = array_diff( $property_info['property_info_photos'], $_POST['rlt_delete_images'] );

				$property_info['property_info_photos'] = serialize( $property_info['property_info_photos'] );
				/* Update the meta field in the database. */
				if ( isset( $_POST['property_info_id'] ) ) {
					$wpdb->update( 
						$wpdb->prefix . 'realty_property_info', 
						$property_info, 
						array( 'property_info_id' => $_POST['property_info_id'] ),
						array( '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%f', '%s', '%d' ),
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
		if ( $post_type != 'property' ) 
			return;

		/* Delete information from custom table */
		$wpdb->delete( 
			$wpdb->prefix . 'realty_property_info', 
			array( 'property_info_post_id' => $post_id )
		);
	}
}

if ( ! class_exists( 'Realty_Widget' ) ) {
	class Realty_Widget extends WP_Widget {

		function Realty_Widget() {
			/* Instantiate the parent object */
			parent::__construct( 
				'realty_widget', 
				__( 'Realty Widget', 'realty' ),
				array( 'description' => __( 'Widget for displaying Sale/Rent Form.', 'realty' ) )
			);
		}

		function widget( $args, $instance ) {
			global $wpdb, $wp_query, $rlt_form_action, $rlt_form_vars;
			if ( empty( $rlt_form_vars ) )
				do_action( 'rlt_check_form_vars' );
			$widget_title = $tab_1_class = $tab_2_class = '';

			echo $args['before_widget'];
			if ( ! empty( $widget_title ) )
				echo $args['before_title'] . $widget_title . $args['after_title'];
			
			$taxonomies = array( 
				'property_type'
			);

			$taxonomy_args = array(
				'orderby'		=> 'name', 
				'order'			=> 'ASC',
				'hide_empty'	=> false
			); 

			$terms_property_type = get_terms( $taxonomies, $taxonomy_args ); 

			$bedrooms_bathrooms = $wpdb->get_row( 'SELECT MIN(`property_info_bedroom`) AS `min_bedroom`, 
					MAX(`property_info_bedroom`) AS `max_bedroom`,
					MIN(`property_info_bathroom`) AS `min_bathroom`,
					MAX(`property_info_bathroom`) AS `max_bathroom`,
					MIN(`property_info_price`) AS `min_price`,
					MAX(`property_info_price`) AS `max_price`
				FROM `' . $wpdb->prefix . 'realty_property_info`', 
			ARRAY_A ); 
			
			if ( ! isset( $rlt_form_vars['property_type_id'] ) || ( isset( $rlt_form_vars['property_type_id'] ) && $rlt_form_vars['property_type_id'] == '2' ) )
				$tab_1_class = ' active';
			else
				$tab_2_class = ' active';
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
											<option value="<?php echo $term_property_type->name; ?>" <?php if ( ! empty( $rlt_form_vars['property_type'] ) && $rlt_form_vars['property_type'] == $term_property_type->name ) echo 'selected="selected"'; ?>><?php echo $term_property_type->name; ?></option>
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
									<input type="hidden" id="rlt_min_price" name="rlt_min_price" value="<?php echo apply_filters( 'rlt_formatting_price_print', $bedrooms_bathrooms['min_price'] ); ?>" />
									<input type="hidden" id="rlt_max_price" name="rlt_max_price" value="<?php echo apply_filters( 'rlt_formatting_price_print', $bedrooms_bathrooms['max_price'] ); ?>" />
									<input type="hidden" id="rlt_current_min_price" value="<?php echo apply_filters( 'rlt_formatting_price_print', $min_price ); ?>" />
									<input type="hidden" id="rlt_current_max_price" value="<?php echo apply_filters( 'rlt_formatting_price_print', $max_price ); ?>" />
									<select class="bathrooms rlt_select" name="rlt_bathrooms">
										<option value="" disabled="disabled" selected="selected"><?php _e( 'Bathrooms', 'realty' ); ?></option>
										<?php $and_more = __( 'and more', 'realty' );
										for ( $i = $bedrooms_bathrooms['min_bathroom']; $i <= $bedrooms_bathrooms['max_bathroom']; $i++ ){ 
											if ( $i == $bedrooms_bathrooms['max_bathroom'] )
												$and_more = ''; ?>
											<option value="<?php echo $i; ?>" <?php if( ! empty( $rlt_form_vars['property_bath'] ) && $rlt_form_vars['property_bath'] == $i && $rlt_form_vars['property_bath'] != $bedrooms_bathrooms['min_bathroom'] ) echo 'selected="selected"'; ?>><?php echo $i; ?> <?php echo $and_more; ?></option>
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
									<input type="hidden" id="rlt_type_id" name="rlt_type_id" value="2" />
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
											<option value="<?php echo $term_property_type->name; ?>" <?php if ( ! empty( $rlt_form_vars['property_type'] ) && $rlt_form_vars['property_type'] == $term_property_type->name ) echo 'selected="selected"'; ?>><?php echo $term_property_type->name; ?></option>
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
									<input type="hidden" id="rlt_type_id" name="rlt_type_id" value="1" />
									<input type="hidden" name="rlt_action" value="listing_search" />
									<input type="submit" value="<?php _e( 'update filters', 'realty' ); ?>">
								</div>
							</form>
						</div><!--end of .for_rent-->
					</div><!-- #main_tabs -->
				</div><!-- #rlt_body_tabs -->
			</div><!-- .rlt_tab_wrapper -->
			<?php $permalink_structure = get_option('permalink_structure');
			if ( is_single() && get_post_type() == 'property' && !empty( $_SESSION['current_page'] ) ) { 
				if ( $permalink_structure == '' )
					$link = realty_request_uri( esc_url( home_url( '/' ) ), 'property', $permalink_structure ) . ( $_SESSION['current_page'] > 1 ? '&property_paged=' . $_SESSION['current_page'] : '' );
				else
					$link = realty_request_uri( esc_url( home_url( '/' ) ) . 'property_search_results/', 'property', $permalink_structure ) . ( $_SESSION['current_page'] > 1 ? 'page/' . $_SESSION['current_page'] . '/' : '' );
				?><div class="rlt_back_to_results"><a href="<?php echo $link; ?>" class="more"><?php _e( 'back to search results', 'realty' ); ?></a></div>
			<?php }
			wp_reset_query();
			echo $args['after_widget'];
		}
	}
}

if ( ! class_exists( 'Realty_Resent_Items_Widget' ) ) {
	class Realty_Resent_Items_Widget extends WP_Widget {

		function Realty_Resent_Items_Widget() {
			/* Instantiate the parent object */
			parent::__construct( 
				'realty_recent_items_widget', 
				__( 'Realty Recent Items', 'realty' ),
				array( 'description' => __( 'Widget for displaying Recent Items block.', 'realty' ) )
			);
		}

		function widget( $args, $instance ) {
			global $wpdb, $wp_query;
			$widget_title	= isset( $instance['widget_title'] ) ? $instance['widget_title'] : 'Recent items';
			$count_items	= isset( $instance['count_items'] ) ? $instance['count_items'] : 4;

			echo $args['before_widget']; ?>
			<div id="rlt_heading_recent_items">
				<div class="widget_content">
					<?php if ( ! empty( $widget_title ) ) { 
						echo $args['before_title'] . $widget_title . $args['after_title'];
					} 
					$recent_items_sql = 'SELECT ' . $wpdb->posts . '.ID,
							' . $wpdb->posts . '.post_title,
							' . $wpdb->prefix . 'realty_property_info.*, 
							' . $wpdb->prefix . 'realty_property_period.property_period_name, 
							' . $wpdb->prefix . 'realty_property_type.property_type_name 
						FROM ' . $wpdb->posts . '
							INNER JOIN ' . $wpdb->prefix . 'realty_property_info ON ' . $wpdb->prefix . 'realty_property_info.property_info_post_id = ' . $wpdb->posts . '.ID
							LEFT JOIN ' . $wpdb->prefix . 'realty_property_period ON ' . $wpdb->prefix . 'realty_property_info.property_info_period_id = ' . $wpdb->prefix . 'realty_property_period.property_period_id
							LEFT JOIN ' . $wpdb->prefix . 'realty_property_type ON ' . $wpdb->prefix . 'realty_property_info.property_info_type_id = ' . $wpdb->prefix . 'realty_property_type.property_type_id
						ORDER BY ' . $wpdb->posts . '.post_date DESC
						LIMIT ' . $count_items . '
					';

					$recent_items_results = $wpdb->get_results( $recent_items_sql, ARRAY_A ); 
					$permalink_structure = get_option('permalink_structure');
					rlt_check_form_vars(); ?>		
					<div id="rlt_home_preview">
						<!--<div class="view_more"><a href="<?php echo realty_request_uri( esc_url( home_url( '/' ) ), 'property', $permalink_structure ); ?>"><?php _e( 'view all', 'realty' ); ?></a></div>-->
						<?php foreach ( $recent_items_results as $recent_item ) {
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
										<li><?php echo $recent_item['property_info_bedroom']; ?> <?php _e( 'bedrooms', 'realty' ); ?>, <?php echo $recent_item['property_info_bathroom']; ?> <?php _e( 'bathroom', 'realty' ); ?></li>
										<li><?php echo $recent_item['property_info_square'] . ' ' . rlt_get_unit_area(); ?></li>
									</ul>
								</div>
								<div class="home_footer">
									<a class="<?php if( ! empty( $recent_item['property_period_name'] ) ) echo "rent"; else echo "sale"; ?>" href="<?php echo get_permalink( $recent_item['ID'] ); ?>"><?php echo $recent_item['property_type_name']; ?></a>
									<a href="<?php the_permalink(); ?>" class="add">&#160;</a>
									<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $recent_item['property_info_price'], rlt_get_currency() ); ?><sup><?php if( ! empty( $recent_item['property_period_name'] ) ) echo "/" . $recent_item['property_period_name']; ?></sup></span>
									<div class="clear"></div>
								</div><!-- .home_footer -->
							</div><!-- .rlt_home_preview -->
						<?php } ?>
						<div class="clear"></div>
					</div><!--end of #rlt_home_preview-->
				</div><!-- .widget_content -->
			</div><!-- #rndmftrdpsts_heading_featured_post -->
			<?php wp_reset_query();
			echo $args['after_widget'];
		}

		function form( $instance ) {
			$widget_title 	= isset( $instance['widget_title'] ) ? $instance['widget_title'] : null; 
			$count_items	= isset( $instance['count_items'] ) ? $instance['count_items'] : 4; ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e( 'Widget Title', 'realty' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'count_items' ); ?>"><?php _e( 'Number of items to be displayed', 'realty' ); ?>: </label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'count_items' ); ?>" name="<?php echo $this->get_field_name( 'count_items' ); ?>" type="number" value="<?php echo esc_attr( $count_items ); ?>"/>
			</p>
		<?php }

		function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['widget_title'] = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : null;
			$instance['count_items']	= ( ! empty( $new_instance['count_items'] ) ) ? strip_tags( $new_instance['count_items'] ) : 4; 
			return $instance;
		}
	}
}


if ( ! function_exists ( 'rlt_wp_head' ) ) {
	function rlt_wp_head() {
		wp_enqueue_style( 'rlt_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_style( 'rlt_select_stylesheet', plugins_url( 'css/select2.css', __FILE__ ) );
		wp_enqueue_script( 'rlt_select_script', plugins_url( 'js/select2.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'rlt_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ) );
		$translation_array = array( 
			'rlt_permalink' => get_option( 'rewrite_rules' )
		);
		wp_localize_script( 'rlt_script', 'rlt_translation', $translation_array );
	}
}

if ( ! function_exists ( 'rlt_enqueue_scripts' ) ) {
	function rlt_enqueue_scripts() {
		global $wp_version;
		wp_enqueue_style( 'rlt_stylesheet', plugins_url( 'css/admin-style.css', __FILE__ ) );
		wp_enqueue_script( 'rlt_script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ) );
		
		$translation_array = array( 
			'rlt_delete_image' => __( 'Delete', 'realty' )
		);
		wp_localize_script( 'rlt_script', 'rlt_translation', $translation_array );
	}
}

if ( ! function_exists ( 'rlt_theme_body_classes' ) ) {
	function rlt_theme_body_classes( $classes ) {
		$current_theme = wp_get_theme();
		$classes[] = basename( $current_theme->get( 'ThemeURI' ) );
		return $classes;
	}
}

if ( ! function_exists( 'rlt_template_redirect' ) ) {
	function rlt_template_redirect(){		
		global $post, $wp_query, $rlt_filenames, $rlt_themepath;
		$file_exists_flag = true;
		foreach ( $rlt_filenames as $filename ) {
			if ( ! file_exists( $rlt_themepath . $filename ) ) {
				$file_exists_flag = false;
			}
		}
		if ( $file_exists_flag ) {
			if ( isset( $wp_query->query_vars['property_search_results'] ) || ( isset( $_POST['rlt_action'] ) && $_POST['rlt_action'] == 'listing_search' ) || isset( $wp_query->query_vars['property_paged'] ) ) { 
				get_template_part( 'rlt-search-listing-results' );
				exit();
			} else if ( ! empty( $post->ID ) && get_post_type( $post->ID ) == 'property' && ! isset( $_POST['rlt_action'] ) ) {
				get_template_part( 'rlt-listing' );
				exit();
			}
		}
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
		$query_vars[] = 'property_typeid';
		return $query_vars;
	}
}

if ( ! function_exists( 'rlt_custom_permalinks' ) ) {
	function rlt_custom_permalinks( $rules ) {
		$newrules = array();
		/* Property page */
		if ( ! isset( $rules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] ) ) {
			/* Property search results with all fields */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[8]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_min_price=$matches[3]&property_max_price=$matches[4]&property_bath=$matches[5]&property_bed=$matches[6]&property_typeid=$matches[7]';
			/* Property search results with all fields and paged */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[8]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_min_price=$matches[3]&property_max_price=$matches[4]&property_bath=$matches[5]&property_bed=$matches[6]&property_typeid=$matches[7]&property_paged=$matches[9]';
			/* Property search results without location field */
			$newrules['property_search_results/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[7]&property_search_results=1&property_type=$matches[1]&property_min_price=$matches[2]&property_max_price=$matches[3]&property_bath=$matches[4]&property_bed=$matches[5]&property_typeid=$matches[6]';
			/* Property search results without location field and with paged */
			$newrules['property_search_results/prop-([^/]+)/minp-([^/]+)/maxp-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[7]&property_search_results=1&property_type=$matches[1]&property_min_price=$matches[2]&property_max_price=$matches[3]&property_bath=$matches[4]&property_bed=$matches[5]&property_typeid=$matches[6]&property_paged=$matches[8]';
			/* Property search results without price field and with paged */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_typeid=$matches[5]&property_paged=$matches[7]';
			/* Property search results without price field */
			$newrules['property_search_results/loc-([^/]+)/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[6]&property_search_results=1&property_location=$matches[1]&property_type=$matches[2]&property_bath=$matches[3]&property_bed=$matches[4]&property_typeid=$matches[5]';
			/* Property search results without location and price field */
			$newrules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_type=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_typeid=$matches[4]';
			/* Property search results without location and price field and with paged */
			$newrules['property_search_results/prop-([^/]+)/bath-([^/]+)/bed-([^/]+)/type-([^/]+)/sort-([^/]+)/page/([^/]+)/?$'] = 'index.php?post_type=property&s=properties&property_sortby=$matches[5]&property_search_results=1&property_type=$matches[1]&property_bath=$matches[2]&property_bed=$matches[3]&property_typeid=$matches[4]&property_paged=$matches[6]';
		}		
		if ( false === $rules )
			return $newrules;

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
		if ( $type == 'property' ) {
			if ( $permalink_structure == '' ) {
				$url .= '?post_type=property&s=properties&property_search_results=1';
				if ( ! empty( $rlt_form_vars['property_location'] ) )
					$url .= '&property_location=' . $rlt_form_vars['property_location'];
				if ( ! empty( $rlt_form_vars['property_type'] ) )
					$url .= '&property_type=' . $rlt_form_vars['property_type'];
				if ( ! empty( $rlt_form_vars['property_min_price'] ) )
					$url .= '&property_min_price=' . $rlt_form_vars['property_min_price'];
				if ( ! empty( $rlt_form_vars['property_max_price'] ) )
					$url .= '&property_max_price=' . $rlt_form_vars['property_max_price'];
				if ( ! empty( $rlt_form_vars['property_bath'] ) )
					$url .= '&property_bath=' . $rlt_form_vars['property_bath'];
				if ( ! empty( $rlt_form_vars['property_bed'] ) )
					$url .= '&property_bed=' . $rlt_form_vars['property_bed'];
				if ( ! empty( $rlt_form_vars['property_type_id'] ) )
					$url .= '&property_typeid=' . $rlt_form_vars['property_type_id'];
				if ( ! empty( $sort ) && $rlt_form_vars['property_sort_by'] == 'price' )
					$url .= '&property_sort_by=newest';
				else if ( ! empty( $sort ) && $rlt_form_vars['property_sort_by'] == 'newest' )
					$url .= '&property_sort_by=price';
				else if ( ! empty( $rlt_form_vars['property_sort_by'] ) )
					$url .= '&property_sort_by=' . $rlt_form_vars['property_sort_by'];
			} else {
				if ( ! empty( $rlt_form_vars['property_location'] ) )
					$url .= 'loc-' . $rlt_form_vars['property_location'] . '/';
				if ( ! empty( $rlt_form_vars['property_type'] ) )
					$url .= 'prop-' . $rlt_form_vars['property_type'] . '/';
				if ( ! empty( $rlt_form_vars['property_min_price'] ) )
					$url .= 'minp-' . $rlt_form_vars['property_min_price'] . '/';
				if ( ! empty( $rlt_form_vars['property_max_price'] ) )
					$url .= 'maxp-' . $rlt_form_vars['property_max_price'] . '/';
				if ( ! empty( $rlt_form_vars['property_bath'] ) )
					$url .= 'bath-' . $rlt_form_vars['property_bath'] . '/';
				if ( ! empty( $rlt_form_vars['property_bed'] ) )
					$url .= 'bed-' . $rlt_form_vars['property_bed'] . '/';
				if ( ! empty( $rlt_form_vars['property_type_id'] ) )
					$url .= 'type-' . $rlt_form_vars['property_type_id'] . '/';
				if ( ! empty( $sort ) && ( $rlt_form_vars['property_sort_by'] == 'price' || $rlt_form_vars['property_sort_by'] == 'property_info_price' ) )
					$url .= 'sort-newest/';
				else if ( ! empty( $sort ) && ( $rlt_form_vars['property_sort_by'] == 'newest' || $rlt_form_vars['property_sort_by'] == 'post_date' ) )
					$url .= 'sort-price/';
				else if ( ! empty( $rlt_form_vars['property_sort_by'] ) )
					$url .= 'sort-' . $rlt_form_vars['property_sort_by'] . '/';
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'rlt_formatting_price' ) ) {
	function rlt_formatting_price( $price, $with_currency = false ) {
		if ( fmod( $price, 1 ) == 0 ) {
			$price = number_format( intval( $price ), 0, '.', ',' );
		}
		$currency_position = rlt_get_currency();
		if ( ! empty( $currency_position ) && true == $with_currency ) {
			if ( $currency_position[1] == 'before' )
				return $currency_position[0] . $price;
			else
				return $price . ' ' . $currency_position[0];
		} else
			return $price;
	}
}

if ( ! function_exists( 'rlt_formatting_price_print' ) ) {
	function rlt_formatting_price_print( $price ) {
		if ( fmod( $price, 1 ) == 0 ) {
			$price = number_format( intval( $price ), 0, '.', '' );
		}
		return $price;
	}
}

if ( ! function_exists( 'rlt_check_form_vars' ) ) {
	function rlt_check_form_vars() {
		global $rlt_form_vars, $wp_query;

		if ( isset( $wp_query->query_vars['property_search_results'] ) || ( isset( $_REQUEST['rlt_action'] ) && $_REQUEST['rlt_action'] == 'listing_search' ) ) { 
			$rlt_form_vars['current_page'] = $_SESSION['current_page'] = isset( $wp_query->query_vars['property_paged'] ) ? $wp_query->query_vars['property_paged'] : ( isset( $_REQUEST['property_paged'] ) ? $_REQUEST['property_paged'] : 1 );

			$rlt_form_vars['property_sort_by'] = $_SESSION['property_sort_by'] = isset( $wp_query->query_vars['property_sortby'] ) ? $wp_query->query_vars['property_sortby'] : ( isset( $_REQUEST['property_sort_by'] ) ? $_REQUEST['property_sort_by'] : 'newest' );

			$rlt_form_vars['property_type'] = $_SESSION['property_type'] = isset( $wp_query->query_vars['property_type'] ) ? esc_attr( urldecode( $wp_query->query_vars['property_type'] ) ) : ( isset( $_REQUEST['rlt_property'] ) ? esc_attr( urldecode( $_REQUEST['rlt_property'] ) ) : null );

			$rlt_form_vars['property_location'] = $_SESSION['property_location'] = ! empty( $wp_query->query_vars['property_location'] ) ? esc_attr( urldecode( $wp_query->query_vars['property_location'] ) ) : ( ! empty( $_REQUEST['rlt_location'] ) ? esc_attr( urldecode( $_REQUEST['rlt_location'] ) ) : null );

			$rlt_form_vars['property_bath'] = $_SESSION['property_bath'] = isset( $wp_query->query_vars['property_bath'] ) ? $wp_query->query_vars['property_bath'] : ( isset( $_REQUEST['rlt_bathrooms'] ) ? $_REQUEST['rlt_bathrooms'] : null );

			$rlt_form_vars['property_bed'] = $_SESSION['property_bed'] = isset( $wp_query->query_vars['property_bed'] ) ? $wp_query->query_vars['property_bed'] : ( isset( $_REQUEST['rlt_bedrooms'] ) ? $_REQUEST['rlt_bedrooms'] : null );

			$rlt_form_vars['property_min_price'] = $_SESSION['property_min_price'] = isset( $wp_query->query_vars['property_min_price'] ) ? $wp_query->query_vars['property_min_price'] : ( isset( $_REQUEST['rlt_min_price'] ) ? $_REQUEST['rlt_min_price'] : null );

			$rlt_form_vars['property_max_price'] = $_SESSION['property_max_price'] = isset( $wp_query->query_vars['property_max_price'] ) ? $wp_query->query_vars['property_max_price'] : ( isset( $_REQUEST['rlt_max_price'] ) ? $_REQUEST['rlt_max_price'] : null );

			$rlt_form_vars['property_type_id'] = $_SESSION['property_type_id'] = isset( $wp_query->query_vars['property_typeid'] ) ? $wp_query->query_vars['property_typeid'] : ( isset( $_REQUEST['rlt_type_id'] ) ? $_REQUEST['rlt_type_id'] : null );
		} else if ( is_single() && get_post_type() == 'property' ) {
			$rlt_form_vars['current_page']		= isset( $_SESSION['current_page'] ) ? $_SESSION['current_page'] : 1;
			$rlt_form_vars['property_sort_by']	= isset( $_SESSION['property_sort_by'] ) ? $_SESSION['property_sort_by'] : 'newest';
			$rlt_form_vars['property_type']		= isset( $_SESSION['property_type'] ) ? esc_attr( urldecode( $_SESSION['property_type'] ) ) : null;
			$rlt_form_vars['property_location']	= ! empty( $_SESSION['property_location'] ) ? esc_attr( urldecode( $_SESSION['property_location'] ) ) : null;
			$rlt_form_vars['property_bath']		= isset( $_SESSION['property_bath'] ) ? $_SESSION['property_bath'] : null;
			$rlt_form_vars['property_bed']		= isset( $_SESSION['property_bed'] ) ? $_SESSION['property_bed'] : null;
			$rlt_form_vars['property_min_price']	= isset( $_SESSION['property_min_price'] ) ? $_SESSION['property_min_price'] : null;
			$rlt_form_vars['property_max_price']	= isset( $_SESSION['property_max_price'] ) ? $_SESSION['property_max_price'] : null;
			$rlt_form_vars['property_type_id']	= isset( $_SESSION['property_type_id'] ) ? $_SESSION['property_type_id'] : null;
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
			if ( get_option('permalink_structure') == '' ) {
				$base = str_replace( 'paged=', $replace_paged, preg_replace( '/&#038;' . $replace_paged . '(\d+)/i', '', esc_url( get_pagenum_link( 999999 ) ) ) );
				$base = preg_replace( '/&#038;s&#038;/i', '&#038;s=&#038;', $base );
			} else
				$base = esc_url( get_pagenum_link( 999999 ) );

			$args = array(
				'base' 			=> str_replace( 999999, '%#%', $base ),
				'total' 		=> $max_num_pages,
				'current' 		=> $current_page,
				'end_size' 		=> 1, /* How many pages at start and at the end. */
				'mid_size' 		=> 1, /* How many pages before and after current page. */
				'prev_text'		=> __( 'Prev', 'realty' ), 
				'next_text'		=> __( 'Next', 'realty' ),
				'type'			=> 'plain',
				'add_args'		=> ''
			);

			if ( $current_page != 1 || $all_results > $limit * $current_page ) { ?>
				<div class="page-link">
					<?php echo paginate_links( $args ); ?>
				</div>
			<?php }
		}
	}
}

if ( ! function_exists( 'rlt_get_currency' ) ) {
	function rlt_get_currency() {
		global $rlt_options, $wpdb;
		if ( empty( $rlt_options ) )
			$rlt_options = get_option( 'rlt_options' );

		if ( empty( $rlt_options['custom_currency'] ) || $rlt_options['currency_custom_display'] == 0 ) {
			$currency = $wpdb->get_var( 'SELECT `currency_unicode` FROM `' . $wpdb->prefix . 'realty_currency` WHERE `currency_id` = ' . $rlt_options['currency_unicode'] );
			if ( empty( $currency ) )
				$currency = '&#36;';
		} else
			$currency = $rlt_options['custom_currency'];

		$position = $rlt_options['currency_position'];
		return array( $currency, $position );
	}
}

if ( ! function_exists( 'rlt_get_unit_area' ) ) {
	function rlt_get_unit_area() {
		global $rlt_options;
		if ( empty( $rlt_options ) )
			$rlt_options = get_option( 'rlt_options' );

		if ( empty( $rlt_options['custom_unit_area'] ) || $rlt_options['unit_area_custom_display'] == 0 )
			return $rlt_options['unit_area'];
		else
			return $rlt_options['custom_unit_area'];
	}
}

if ( ! function_exists ( 'rlt_plugin_action_links' ) ) {
	function rlt_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) 
				$this_plugin = plugin_basename( __FILE__ );

			if ( $file == $this_plugin ){
				$settings_link = '<a href="admin.php?page=realty_settings">' . __( 'Settings', 'realty' ) . '</a>';
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
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=realty_settings">' . __( 'Settings', 'realty' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/realty/faq/" target="_blank">' . __( 'FAQ', 'realty' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'realty' ) . '</a>';
		}
		return $links;
	}
}

/* 
 * Function for adding all functionality for updating 
 */

if ( ! function_exists ( 'rlt_plugin_banner' ) ) {
	function rlt_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix ) {
			global $rlt_plugin_info;
			bws_plugin_banner( $rlt_plugin_info, 'rlt', 'realty', '3936d03a063bccc2a2fa09a26aba0679', '205', plugins_url( 'images/banner.png', __FILE__ ) ); 
		} 
	}
}

if ( ! function_exists( 'rlt_plugin_uninstall' ) ) {
	function rlt_plugin_uninstall() {
		global $wpdb, $rlt_filenames, $rlt_themepath;

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugins_list = get_plugins();

		$check_realty_pro_install = ( array_key_exists( 'realty-pro/realty-pro.php', $plugins_list ) ) ? true : false;

		if ( $check_realty_pro_install ) {
			/* Delete any tables */
			$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_info`' );
			$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_currency`' );
			$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_period`' );
			$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'realty_property_type`' );

			$customs = get_posts( array( 'post_type' => array( 'property' ), 'posts_per_page' => -1 ) );
			foreach ( $customs as $custom ) {
				/* Delete's each post. */
				wp_delete_post( $custom->ID, true);
			}

			$terms = get_terms( array( 'property_type' ), array( 'hide_empty' => 0 ) );
			if ( count( $terms ) > 0 ) {
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, $term->taxonomy );
				}
			}
		}

		/* Delete any templates */
		foreach ( $rlt_filenames as $filename ){			
			if ( file_exists( $rlt_themepath . $filename ) && ! unlink( $rlt_themepath . $filename ) ) {
				add_action( 'admin_notices', create_function( '', ' return "Error delete template file";' ) );
			}
		}

		/* Delete any options thats stored */
		delete_option( 'rlt_options' );
	}
}


/* Activate plugin */
register_activation_hook( __FILE__, 'rlt_plugin_activation' );

add_action( 'init', 'rlt_init' );
add_action( 'admin_init', 'rlt_admin_init' );
add_action( 'after_switch_theme', 'rlt_plugin_install', 10, 2 );
add_action( 'widgets_init', 'rlt_register_widgets' );
add_action( 'admin_menu', 'rlt_admin_menu' );
add_filter( 'manage_edit-property_columns', 'rlt_property_columns' );
add_action( 'restrict_manage_posts', 'rlt_restrict_manage_property' );
add_action( 'pre_get_posts', 'rlt_property_pre_get_posts' );
add_action( 'save_post', 'rlt_save_postdata' );
add_action( 'before_delete_post', 'rlt_delete_post' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'rlt_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'rlt_register_plugin_links', 10, 2 );

add_action( 'admin_enqueue_scripts', 'rlt_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'rlt_wp_head' );

add_filter( 'body_class', 'rlt_theme_body_classes' );

add_action( 'template_redirect', 'rlt_template_redirect' );
add_filter( 'rewrite_rules_array', 'rlt_custom_permalinks' ); /* Add custom permalink for plugin */
add_action( 'wp_loaded', 'rlt_flush_rules' );
add_filter( 'query_vars', 'rlt_query_vars' );
add_filter( 'realty_request_uri', 'realty_request_uri', 10, 4 );

add_filter( 'rlt_formatting_price', 'rlt_formatting_price', 10, 2 );
add_filter( 'rlt_formatting_price_print', 'rlt_formatting_price_print' );
add_action( 'rlt_check_form_vars', 'rlt_check_form_vars' );
add_action( 'rlt_search_nav', 'rlt_search_nav' );

add_action( 'admin_notices', 'rlt_plugin_banner' );

/* Delete plugin */
register_uninstall_hook( __FILE__, 'rlt_plugin_uninstall' );