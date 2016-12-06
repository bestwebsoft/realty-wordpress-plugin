<?php get_header();
	global $post;
	$taxonomies = array( 'property_type' );

	$args = array(
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		'hide_empty'	=> false
	);

	$terms_property_type = get_terms( $taxonomies, $args );
	$property_info = $wpdb->get_row( 'SELECT * FROM `' . $wpdb->prefix . 'realty_property_info`
		LEFT JOIN `' . $wpdb->prefix . 'realty_property_period` ON `' . $wpdb->prefix . 'realty_property_info`.`property_info_period_id` = `' . $wpdb->prefix . 'realty_property_period`.`property_period_id`
		LEFT JOIN `' . $wpdb->prefix . 'realty_property_type` ON `' . $wpdb->prefix . 'realty_property_info`.`property_info_type_id` = `' . $wpdb->prefix . 'realty_property_type`.`property_type_id`
		WHERE `property_info_post_id` = ' . $post->ID,
	ARRAY_A );
	$property_info['property_info_photos'] = unserialize( $property_info['property_info_photos'] );
	$count_photos = count( $property_info['property_info_photos'] );

	$bedrooms_bathrooms = $wpdb->get_row( 'SELECT MIN(`property_info_bedroom`) AS `min_bedroom`, MAX(`property_info_bedroom`) AS `max_bedroom`,
			MIN(`property_info_price`) AS `min_price`, MAX(`property_info_price`) AS `max_price`
		FROM `' . $wpdb->prefix . 'realty_property_info`', ARRAY_A );
	$form_action = ! get_option( 'permalink_structure' ) ? '?property=property_search_results' : 'property_search_results';  ?>
	<aside class="content rlt-clearfix">
		<div class="content-wrapper">
			<div class="rlt_home_full_wrapper">
				<?php get_template_part( 'rlt-search-form', 'single' ); ?>
				<div id="rlt_home_info_full">
					<div class="rlt_home_content_full">
						<div class="rlt_tabs">
							<div class="tab tab_1 active"><?php _e( 'photos', 'realty' ); ?><?php if ( $count_photos > 0 ) { ?> <span>(1 <?php _e( 'of', 'realty' ); ?> <?php echo $count_photos; ?>)</span><?php } ?></div>
							<?php if ( ! empty( $property_info['property_info_coordinates'] ) ) { ?>
								<div class="tab tab_2"><?php _e( 'view street', 'realty' ); ?></div>
								<div class="tab tab_3"><?php _e( 'map', 'realty' ); ?></div>
							<?php } ?>
						</div>
						<div class="rlt_home_content_tab rlt_home_content_1 active">
							<div class="cover"></div>
							<div class="rlt_home_slides_thumbnail">
								<div class="home_image">
									<?php if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'realty_listing', array('srcset' => get_the_post_thumbnail_url() ) );
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
						<?php if ( ! empty( $property_info['property_info_coordinates'] ) ) { ?>
							<div class="rlt_home_content_tab rlt_home_content_2">
								<div class="cover"></div>
								<div style="width:100%; height:420px;">
									<div id="map-canvas">
									</div>
								</div>
							</div>
							<div class="rlt_home_content_tab rlt_home_content_3">
								<div class="cover"></div>
								<div style="width:100%; height:420px;">
									<div id="map-canvas2"></div>
									<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
									<script>
										var propertyLatlng;
										var map;
										function initialize() {
											propertyLatlng = new google.maps.LatLng(<?php echo $property_info['property_info_coordinates']; ?>);
											var mapOptions = {
												zoom: 14,
												center: propertyLatlng
											}
											map = new google.maps.Map( document.getElementById( 'map-canvas2' ), mapOptions );

											var marker = new google.maps.Marker({
													position: propertyLatlng,
													map: map
											});

											var panoramaOptions = {
												position: propertyLatlng,
												pov: {
													heading: 30,
													pitch: 10
												}
											};
											var panorama = new google.maps.StreetViewPanorama( document.getElementById( 'map-canvas' ), panoramaOptions );

											var client = new google.maps.StreetViewService();

											client.getPanoramaByLocation( propertyLatlng, 50, function( result, status ) {
												if ( status == google.maps.StreetViewStatus.OK ) {
													map.setStreetView( panorama );
												} else {
													var view_tab = document.querySelectorAll( '.tab_2' );
													if( view_tab.length > 0 )
														view_tab[1].style.display = "none";
												}
											});
										}
										google.maps.event.addDomListener( window, 'load', initialize );
									</script>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="rlt_home_description">
						<h3><?php _e( 'General Information', 'realty' ); ?></h3>
						<p><?php the_content(); ?></p>
					</div>
				</div><!--end of #rlt_home_preview-->
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
							<a class="<?php if ( ! empty( $property_info['property_period_name'] ) ) echo "rent"; else echo "sale"; ?>" href="<?php the_permalink(); ?>"><?php echo $property_info['property_type_name']; ?></a>
							<span class="home_cost"><?php echo apply_filters( 'rlt_formatting_price', $property_info['property_info_price'], true ); ?><sup><?php if ( ! empty( $property_info['property_period_name'] ) ) echo "/" . $property_info['property_period_name']; ?></sup></span>
						</div>
					</div>
				</div><!--end of .rlt_search_options-->
			</div><!-- .rlt_home_full_wrapper -->
		</div><!-- .content-wrapper -->
	</aside>
<?php get_footer(); ?>
