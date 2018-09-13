<?php
/**
* Includes deprecated functions
*/

/**
 * Update options
 * @deprecated since 1.1.2
 * @todo remove after 31.10.2018
 */
if ( ! function_exists( 'rlt_update_options' ) ) {
	function rlt_update_options() {
		global $rlt_options;
		if ( isset( $rlt_options['unit_area'] ) ) {
			if ( 'sq&nbsp;ft' == $rlt_options['unit_area'] ){
				$rlt_options['unit_area'] = 'ft&sup2';
			} else if ( 'm2' == $rlt_options['unit_area'] ) {
				$rlt_options['unit_area'] = 'm&sup2';
			} else {
				$rlt_options['unit_area'] = $rlt_options['unit_area'];
			}
		}
	}
}