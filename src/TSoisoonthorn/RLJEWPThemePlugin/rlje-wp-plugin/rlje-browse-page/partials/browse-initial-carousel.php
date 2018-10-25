<?php
/*
if ( ! empty( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) :
	$watch_spotlight_items = apply_filters( 'atv_get_watch_spotlight_items', 'recentlyWatched' );
	if ( 0 < count( $watch_spotlight_items ) ) :
		?>
<!-- RECENTLY WATCHED || WATCHLIST SPOTLIGHT-->
<div class="col-md-12">
<?php
	set_query_var( 'carousel-items', $watch_spotlight_items );
	// get_template_part( 'partials/section-generic-carousel' );
	require plugin_dir_path( __FILE__ ) . '../partials/section-generic-carousel.php';
?>
</div>
	<?php
	endif;
endif;
*/

do_action( 'rlje_display_watchlist_section_on_browse_page' );

$spotlight_items = get_query_var( 'spotlight_items' );

foreach ( $spotlight_items as $spotlight ) :
	$spotlight_name = ( ! empty( $spotlight->name ) ) ? $spotlight->name : '';
	?>
<!-- <?php echo strtoupper( $spotlight_name ); ?> SERIES SPOTLIGHT-->
<div class="col-md-12">
	<?php
	set_query_var(
		'carousel-section', array(
			'title'       => $spotlight_name,
			'categoryObj' => $spotlight,
		)
	);
	// get_template_part( 'partials/section-carousel-pagination' );
	ob_start();
	require plugin_dir_path( __FILE__ ) . '../partials/section-carousel-pagination.php';
	$html = ob_get_clean();
	echo $html;
	?>
</div>
<?php endforeach; ?>
