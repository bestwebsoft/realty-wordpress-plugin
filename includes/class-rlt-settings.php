<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Rlt_Settings_Tabs' ) ) {
	class Rlt_Settings_Tabs extends Bws_Settings_Tabs {
		private $currencies, $area_units;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $wpdb, $rlt_options, $rlt_plugin_info;

			$tabs = array(
				'settings' 		=> array( 'label' => __( 'Settings', 'realty' ) ),
				'misc' 			=> array( 'label' => __( 'Misc', 'realty' ) ),
				'custom_code' 	=> array( 'label' => __( 'Custom Code', 'realty' ) ),
				'import-export' => array( 'label' => __( 'Import / Export', 'realty' ), 'is_pro' => 1 ),
				'license'       => array( 'label' => __( 'Licence Key', 'realty' ) )
			);

			parent::__construct( array(
				'plugin_basename' 	 => $plugin_basename,
				'plugins_info'		 => $rlt_plugin_info,
				'prefix' 			 => 'rlt',
				'default_options' 	 => rlt_get_options_default(),
				'options' 			 => $rlt_options,
				'tabs' 				 => $tabs,
				'wp_slug'			 => 'realty',
				'link_key'			 => '3936d03a063bccc2a2fa09a26aba0679',
				'link_pn'			 => '205'
			) );

			add_action( get_parent_class( $this ) . '_additional_import_export_options', array( $this, 'additional_import_export_options' ) );

			$this->currencies = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "realty_currency`", ARRAY_A );
			$this->area_units = array(
				'ft2'   => 'ft&sup2',
                'm2'    => 'm&sup2'
            );
		}

		public function save_options(){

			$message = $notice = $error = '';

			$this->options['rlt_price']					= isset( $_POST['rlt_price'] ) ? 'show' : 'hide';
			$this->options['currency_unicode']			= absint( $_POST['rlt_currency'] );
			$this->options['currency_position']			= in_array( $_POST['rlt_currency_position'], array( 'before', 'after' ) ) ? $_POST['rlt_currency_position'] : 'before';
			$this->options['unit_area']					= array_key_exists( $_POST['rlt_unit_area'], $this->area_units ) ? $_POST['rlt_unit_area'] : 'ft2';
			$this->options['per_page']					= intval( $_POST['rlt_per_page'] );
			$this->options['maps_key']					= sanitize_text_field( $_POST['rlt_maps_key'] );

			update_option( 'rlt_options' , $this->options );
			$message = __( 'Settings saved.', 'realty' );

			return compact( 'message', 'notice', 'error' );
		}

		public function tab_settings() { ?>
            <h3 class="bws_tab_label"><?php _e( 'Realty Settings', 'realty' ); ?></h3>
			<?php $this->help_phrase(); ?>
            <hr>
            <table class="form-table">
                <tr class="rlt_price_labels">
                    <th scope="row"><?php _e( 'Display Price', 'realty' ); ?></th>
                    <td>
                        <label><input type="checkbox" name="rlt_price" value="show" <?php checked('show', $this->options['rlt_price']); ?> /><span class="bws_info"><?php _e( 'Enable to display price in the front-end.', 'realty' ); ?></span></label>
                    </td>
                </tr>
                <tr class="rlt_currency_labels">
                    <th scope="row"><label for="rlt_currency"><?php _e( 'Currency', 'realty' ); ?></label></th>
                    <td>
                        <select name="rlt_currency" id="rlt_currency" style="width: 100%;">
                            <?php foreach ( $this->currencies as $currency ) { ?>
                                <option value="<?php echo $currency['currency_id']; ?>" <?php selected( $currency['currency_id'], $this->options['currency_unicode'] ); ?>><?php echo $currency['currency_unicode'] . ' ( ' . $currency['country_currency'] . " - " . $currency['currency_code'] . ' ) '; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr class="rlt_custom_currency_position_labels">
                    <th scope="row"><?php _e( 'Currency Position', 'realty' ); ?></th>
                    <td>
                        <fieldset>
                            <label><input type="radio" id="rlt_currency_position_before" name="rlt_currency_position" value="before" <?php checked( 'before', $this->options['currency_position'] ); ?> /> <?php _e( 'Before numerals', 'realty' ); ?></label><br />
                            <label><input type="radio" id="rlt_currency_position_after" name="rlt_currency_position" value="after" <?php checked( 'after', $this->options['currency_position'] ); ?> /> <?php _e( 'After numerals', 'realty' ); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr class="rlt_unit_area_labels">
                    <th scope="row"><label for="rlt_unit_area"><?php _e( 'Area Units', 'realty' ); ?></label></th>
                    <td>
                        <select name="rlt_unit_area" id="rlt_unit_area">
	                        <?php foreach ( $this->area_units as $key => $area_unit ) { ?>
                                <option value="<?php echo $key; ?>" <?php selected( $key, $this->options['unit_area'] ); ?>><?php echo $area_unit; ?></option>
	                        <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr class="rlt_per_page_labels">
                    <th scope="row"><label for="rlt_per_page"><?php _e( 'Show at Most', 'realty' ); ?></label></th>
                    <td>
                        <input type="number" class="small-text" min="1" max="100" step="1" id="rlt_per_page" name="rlt_per_page" value="<?php echo $this->options['per_page']; ?>" /><br />
                        <span class="bws_info"><?php _e( 'Number of Properties and Agents.', 'realty' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="crrntl_maps_key"><?php _e( 'Google Maps API Key', 'realty' ); ?></label>
                    </th>
                    <td>
                        <input id="rlt_maps_key" type="text" value="<?php echo ( ! empty( $this->options['maps_key'] ) ) ? $this->options['maps_key'] : ''; ?>" name="rlt_maps_key" /><br>
                        <span class="bws_info">
                        <?php printf(
                            __( "Including a key in your request allows you to monitor your application's API usage in the %s.", 'realty' ),
                            sprintf(
                                '<a href="https://console.developers.google.com/" target="_blank">%s</a>',
                                __( 'Google API Console', 'realty' )
                            )
                        ); ?><br />
                        <?php _e( "Don't have an API key?", 'realty' ); ?>
                        <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><?php _e( 'Get it now!', 'realty' ); ?></a>
                    </span>
                    </td>
                </tr>
            </table>
        <?php }

		public function additional_import_export_options() { ?>
            <div class="bws_pro_version_bloc">
                <div class="bws_pro_version_table_bloc">
                    <button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'realty' ); ?>"></button>
                    <div class="bws_table_bg"></div>
                    <table class="form-table bws_pro_version">
                        <tr>
                            <th scope="row"><?php _e( 'Demo Data', 'realty' ); ?></th>
                            <td>
                                <button class="button" name="bws_handle_demo" value="install" disabled="disabled"><?php _e( 'Install Demo Data', 'realty' ); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
				<?php $this->bws_pro_block_links(); ?>
            </div>
		<?php }

	}
}