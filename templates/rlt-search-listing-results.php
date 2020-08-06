<?php
/*
* Template - Property Search
* Version: 2.0.0
*/
get_header(); ?>
	<aside class="content rlt-clearfix <?php echo 'RealEstate' != wp_get_theme() ? 'rlt_content_center' : ''; ?>">
		<div class="content-wrapper">
			<div class="rlt_home_full_wrapper">
                <div class="rlt_search_options">
                    <?php if ( function_exists( 'rlt_search_form' ) ) {
                        rlt_search_form();
                    } ?>
                </div><!-- .rlt_search_options-->
				<div id="rlt_home_preview">
					<?php if ( function_exists( 'rlt_get_search_listing_results' ) ) {
						rlt_get_search_listing_results();
					} ?>
				</div><!--end of #rlt_home_preview-->
				<div class="clear"></div>
			</div><!-- .rlt_home_full_wrapper -->
		</div><!-- .content-wrapper -->
	</aside>
<?php get_footer(); ?>
