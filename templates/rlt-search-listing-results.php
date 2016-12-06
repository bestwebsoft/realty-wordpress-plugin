<?php get_header();
	global $post, $rlt_count_results, $rlt_property_info_count_all_results, $rlt_form_action, $rlt_form_vars, $limit, $current_page, $rlt_options;
	if ( empty( $rlt_options ) )
		$rlt_options = get_option( 'rlt_options' );

	do_action( 'rlt_check_form_vars' );

	$current_page = $rlt_form_vars['current_page'];

	$property_sort_by = $rlt_form_vars['property_sort_by'] == 'newest' ? $wpdb->posts . '.post_date' : $wpdb->prefix . 'realty_property_info.property_info_price';

	if ( ! empty( $rlt_form_vars['property_type'] ) && $rlt_form_vars['property_type'] != 'all' )
		$property_args = array(
			'post_type'				=> 'property',
			'property_type'			=> $rlt_form_vars['property_type'],
			'fields'				=> 'ids',
			'posts_per_page'		=> -1
		);
	else
		$property_args = array(
			'post_type'				=> 'property',
			'fields'				=> 'ids',
			'posts_per_page'		=> -1
		);
	$query = new WP_Query( $property_args );

	$rlt_count_results = $rlt_property_info_count_all_results = 0;
	$limit = $rlt_options['per_page'];
	$property_info_results = array();
	if ( $query->post_count > 0 ) {
		$posts_id = implode( ',', $query->posts );
		$where = '';
		if ( ! empty( $rlt_form_vars['property_location'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_location LIKE "%' . $rlt_form_vars['property_location'] . '%"';
		if ( ! empty( $rlt_form_vars['property_bath'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_bathroom >= ' . $rlt_form_vars['property_bath'];
		if ( ! empty( $rlt_form_vars['property_bed'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_bedroom >= ' . $rlt_form_vars['property_bed'];
		if ( ! empty( $rlt_form_vars['property_min_price'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_price >= ' . ( $rlt_form_vars['property_min_price'] );
		if ( ! empty( $rlt_form_vars['property_max_price'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_price <= ' . ( $rlt_form_vars['property_max_price'] );
		if ( ! empty( $rlt_form_vars['property_type_id'] ) )
			$where .= ' AND ' . $wpdb->prefix . 'realty_property_info.property_info_type_id = ' . ( $rlt_form_vars['property_type_id'] );
		$search_propety_sql = 'SELECT ' . $wpdb->posts . '.ID,
				' . $wpdb->posts . '.post_title,
				' . $wpdb->prefix . 'realty_property_info.*,
				' . $wpdb->prefix . 'realty_property_period.property_period_name,
				' . $wpdb->prefix . 'realty_property_type.property_type_name
			FROM ' . $wpdb->posts . '
				INNER JOIN ' . $wpdb->prefix . 'realty_property_info ON ' . $wpdb->prefix . 'realty_property_info.property_info_post_id = ' . $wpdb->posts . '.ID
				LEFT JOIN ' . $wpdb->prefix . 'realty_property_period ON ' . $wpdb->prefix . 'realty_property_info.property_info_period_id = ' . $wpdb->prefix . 'realty_property_period.property_period_id
				LEFT JOIN ' . $wpdb->prefix . 'realty_property_type ON ' . $wpdb->prefix . 'realty_property_info.property_info_type_id = ' . $wpdb->prefix . 'realty_property_type.property_type_id
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
					LEFT JOIN ' . $wpdb->prefix . 'realty_property_period ON ' . $wpdb->prefix . 'realty_property_info.property_info_period_id = ' . $wpdb->prefix . 'realty_property_period.property_period_id
					LEFT JOIN ' . $wpdb->prefix . 'realty_property_type ON ' . $wpdb->prefix . 'realty_property_info.property_info_type_id = ' . $wpdb->prefix . 'realty_property_type.property_type_id
				WHERE ' . $wpdb->posts . '.ID IN (' . $posts_id . ')
					' . $where . '
			';
			$rlt_property_info_count_all_results = $wpdb->get_var( $search_propety_count_sql );
		} else
			$rlt_property_info_count_all_results = $rlt_count_results;
	}
	wp_reset_query();
	$class_sort_newest = $class_sort_price = '';
	if ( isset( $rlt_form_vars['property_sort_by'] ) && count( $property_info_results ) > 0 ) {
		if ( $rlt_form_vars['property_sort_by'] == 'newest' ) {
			$class_sort_newest = 'current';
			$rlt_newest_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), '' );
			$rlt_price_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), 'sort' );
		} else if ( $rlt_form_vars['property_sort_by'] == 'price' ) {
			$class_sort_price = 'current';
			$rlt_newest_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), 'sort' );
			$rlt_price_link = apply_filters( 'realty_request_uri', '', 'property', get_option( 'permalink_structure' ), '' );
		}
	} ?>
	<aside class="content rlt-clearfix">
		<div class="content-wrapper">
			<div class="rlt_home_full_wrapper">
				<?php get_template_part( 'rlt-search-form' ); ?>
				<div id="rlt_home_preview">
					<?php if ( isset( $rlt_newest_link ) && isset( $rlt_price_link ) ) { ?>
						<div class="view_more sort_by"><span><?php _e( 'sort by', 'realty' ); ?>:</span> <a class="<?php echo $class_sort_newest; ?>" href="<?php echo home_url() . '/' . $rlt_newest_link; ?>"><?php _e( 'newest', 'realty' ); ?> </a> | <a class="<?php echo $class_sort_price; ?>" href="<?php echo home_url() . '/' . $rlt_price_link; ?>"><?php _e( 'price', 'realty' ); ?></a></div>
					<?php }
					if ( count( $property_info_results ) > 0 ) {
						foreach ( $property_info_results as $property_info ) {
							$property_info['property_info_photos'] = unserialize( $property_info['property_info_photos'] ); ?>
							<div class="rlt_home_preview">
								<a href="<?php echo get_permalink( $property_info['ID'] ); ?>">
									<?php if ( has_post_thumbnail( $property_info['ID'] ) )
										echo get_the_post_thumbnail( $property_info['ID'], 'realty_search_result' );
									else {
										if ( isset( $property_info['property_info_photos'][0] ) ) {
											$small_photo = wp_get_attachment_image_src( $property_info['property_info_photos'][0], 'realty_search_result' ); ?>
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
									<a class="<?php if( ! empty( $property_info['property_period_name'] ) ) echo "rent"; else echo "sale"; ?>" href="<?php echo get_permalink( $property_info['ID'] ); ?>"><?php echo $property_info['property_type_name']; ?></a>
									<a href="<?php the_permalink(); ?>" class="add">&#160;</a>
									<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $property_info['property_info_price'], true ); ?><sup><?php if ( ! empty( $property_info['property_period_name'] ) ) echo "/" . $property_info['property_period_name']; ?></sup></span>
									<div class="clear"></div>
								</div><!-- .home_footer -->
							</div><!-- .rlt_home_preview -->
						<?php } ?>
						<div class="clear"></div>
						<div class="more_rooms"><?php do_action( 'rlt_search_nav' ); ?></div>
					<?php } else {
						get_template_part( 'rlt-nothing-found' );
					} ?>
				</div><!--end of #rlt_home_preview-->
				<div class="clear"></div>
			</div><!-- .rlt_home_full_wrapper -->
		</div><!-- .content-wrapper -->
	</aside>
<?php get_footer(); ?>
