<?php
get_header();
?>
<!-- Filter JS Sub Navigation base on category -->
<section class="browse">
	<div class="container">
		<ul class="subnav">
			<?php foreach ( $list_sections as $key => $property ) : ?>
			<li class="browse-menu<?php echo ( $key === $active_section ) ? ' active' : ''; ?>"><a href="<?php echo esc_url( '/browse/' . $key . '/' ); ?>"><?php echo $property; ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
		$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'section' );
		if ( $have_franchises_available ) {
			if ( $is_order_by_enabled ) {
			?>
			<div class="page-subhead browse-order">
				<span>SORT BY:</span>
				<a class="browse-order-option active js-orderby-added" href="#date-added">Date Added</a>
				<a class="browse-order-option js-orderby-az" href="#a-z">A to Z</a>
			</div>
			<?php
			}
			if ( empty( $active_section ) ) {
				// Show initial Browse Page.
				set_query_var( 'spotlight_items', $guide_items );
				// get_template_part( 'partials/browse-initial-carousel' );
				require plugin_dir_path( __FILE__ ) . '../partials/browse-initial-carousel.php';
			} else {
				// Show Browse Page filtered
				?>
				<!-- Category Content Column Grid -->
				<div class="objects" >
					<?php
					$array_list_items = array();
					switch ( $active_section ) {
						case 'all':
							$array_list_items = rljeApiWP_getBrowseAll();
							break;
						case 'recentlywatched':
							$array_list_items = rljeApiWP_getUserRecentlyWatched( $atv_session_cookie );
							break;
						case 'yourwatchlist':
							$array_list_items = rljeApiWP_getUserWatchlist( $atv_session_cookie );
							break;
						default:
							// $list_items       = rljeApiWP_getItemsByCategoryOrCollection( apply_filters( 'atv_convert_browseSlug_to_contentID', $active_section ) );
							$list_items       = rljeApiWP_getItemsByCategoryOrCollection( $active_section );
							$array_list_items = rljeApiWP_orderFranchisesByCreatedDate( $list_items );
							break;
					}
					set_query_var( 'array_list_items', $array_list_items );
					?>
					<div class="item" style="margin-left:0px;width:100%;padding-bottom:30px;">
						<?php //get_template_part( 'partials/list-browse-items' ); ?>
						<?php require plugin_dir_path( __FILE__ ) . '../partials/list-browse-items.php'; ?>
					</div>
				</div>
				<?php
			}
		} else {
			?>
			<div id="contentPane" class="message-block">
				<div class="row-fluid">
					<p>We’re sorry, but there are no shows to display at this time.</p>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</section>
<?php
get_footer();
