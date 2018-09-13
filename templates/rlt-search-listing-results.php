<?php get_header(); ?>
	<aside class="content rlt-clearfix">
		<div class="content-wrapper">
			<div class="rlt_home_full_wrapper">
				<?php get_template_part( 'rlt-search-form' ); ?>
				<div id="rlt_home_preview">
					<?php echo rlt_get_search_listing_results(); ?>
				</div><!--end of #rlt_home_preview-->
				<div class="clear"></div>
			</div><!-- .rlt_home_full_wrapper -->
		</div><!-- .content-wrapper -->
	</aside>
<?php get_footer(); ?>
