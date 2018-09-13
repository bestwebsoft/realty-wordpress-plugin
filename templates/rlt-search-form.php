<?php global $rlt_count_results, $rlt_property_info_count_all_results; ?>
<div class="rlt_search_options">
	<div class="search_results">
		<?php if ( $rlt_count_results > 0 ) { ?>
			<span><?php echo $rlt_count_results; ?></span><?php _e( 'results from', 'realty' ); ?> <span><?php echo $rlt_property_info_count_all_results; ?></span> <?php _e( 'total', 'realty' ); ?>
			<?php } ?>
		</div>
	<?php the_widget( 'Realty_Widget' ); ?>
</div><!--end of .rlt_search_options-->