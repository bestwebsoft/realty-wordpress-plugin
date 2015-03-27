				<?php global $rlt_count_results, $rlt_property_info_count_all_results; ?>
				<div class="search_options">
					<div class="search_results">
						<?php if ( $rlt_count_results > 0 ) { ?>
							<span><?php echo $rlt_count_results; ?></span> <?php _e( 'results from', 'realty' ); ?> <span><?php echo $rlt_property_info_count_all_results; ?></span> <?php _e( 'total', 'realty' ); ?>
						<?php } ?>
					</div>
					<?php the_widget( 'Renty_Widget' ); ?>					
				</div><!--end of .search_options-->