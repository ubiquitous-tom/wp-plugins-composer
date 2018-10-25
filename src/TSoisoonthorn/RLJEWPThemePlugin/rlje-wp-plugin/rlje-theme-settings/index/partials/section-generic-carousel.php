<?php
$section_title  = get_query_var( 'carousel-section' );
$carousel_items = get_query_var( 'carousel-items' );
$section_items  = false;
$section_key    = '';

$redefined_key = array(
	'mystery'         => 'Mysteries',
	'drama'           => 'Dramas',
	'comedy'          => 'Comedies',
	'documentary'     => 'Documentaries',
	'exclusive'       => 'Only On Acorn TV',
	'recentlywatched' => 'Recently Watched',
	'yourwatchlist'   => 'Watchlist',
);

foreach ( $redefined_key as $key => $v ) {
	if ( $section_title == $v ) {
		$section_key = $key;
	}
}

if ( empty( trim( $section_key ) ) ) {
	$section_v   = str_replace( ' ', '', $section_title );
	$section_key = strtolower( $section_v );
}

if ( ! empty( $carousel_items ) && ( ! isset( $carousel_items['code'] ) ) ) {
	$section_items               = new stdClass();
	$section_items->$section_key = $carousel_items;
	set_query_var( 'carousel-items', '' );
}

$all_carousel = ( $section_items ) ? $section_items : rljeApiWP_getCarouselItems();

$is_browse_page = ( get_query_var( 'pagecustom' ) === 'browse' );
$h              = 'h4';
if ( $is_browse_page ) {
	$h                          = 'h3';
	$disable_orderby_date_added = array(
		'recentlywatched' => true,
		'yourwatchlist'   => true,
		'mostpopular'     => true,
	);
	if ( ! isset( $disable_orderby_date_added[ $section_key ] ) && ! empty( $all_carousel->$section_key ) ) {
		$all_carousel->$section_key = rljeApiWP_orderFranchisesByCreatedDate( $all_carousel->$section_key );
	}
}

$is_showing_arrows = true;

// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';

if ( isset( $all_carousel->$section_key ) && count( $all_carousel->$section_key ) > 0 ) :
?>
<<?php echo $h; ?> <?php echo ( $section_key === 'mystery' && ! $is_browse_page ) ? 'id="third-spotlight"' : ''; ?> class="subnav2"><?php echo esc_html( $section_title ); ?></<?php echo $h; ?>>

<?php if ( $section_key !== 'mostpopular' ) : ?>
<div class="view-all hidden-xs">
	<a href="<?php echo esc_url( trailingslashit( home_url( '/browse/' . $section_key ) ) ); ?>"> View all </a>
</div>
<?php endif; ?>

	<div class="carousel carousel-respond-slide slide" id="<?php echo esc_attr( $section_key ); ?>" data-interval="false">
		<div class="row">
			<div class="carousel-inner">
				<?php
				if ( count( $all_carousel->$section_key ) > 4 ) :
					foreach ( $all_carousel->$section_key as $item ) :
						$item = apply_filters( 'atv_add_img_and_href', $item );
						?>
				<div class="item <?php echo ( $item === reset( $all_carousel->$section_key ) ) ? 'active' : ''; ?>">
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" id="avatar">
						<a href="<?php echo esc_url( trailingslashit( home_url( $item->href ) ) ); ?>">
							<img title="<?php echo esc_attr( $item->name ); ?>" alt="<?php echo esc_attr( $item->name ); ?> - <?php echo esc_attr( $section_title ); ?> category image" class="wp-post-image" id="avatar-rollover" src="<?php echo esc_url( $item->img . '?t=titled-avatars&w=400&h=225' ); ?>"/>
						</a>
					</div>
				</div>
						<?php
					endforeach;
				else :
					$is_showing_arrows = false;
					?>
				<div class="item active">
					<?php
					foreach ( $all_carousel->$section_key as $item ) :
						$item = apply_filters( 'atv_add_img_and_href', $item );
						?>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" id="avatar">
						<a href="<?php echo esc_url( trailingslashit( home_url( $item->href ) ) ); ?>">
							<img title="<?php echo esc_attr( $item->name ); ?>" alt="<?php echo esc_attr( $item->name ); ?> - <?php echo esc_attr( $section_title ); ?> category image" class="wp-post-image" id="avatar-rollover" src="<?php echo esc_url( $item->img . '?t=titled-avatars&w=400&h=225' ); ?>"/>
						</a>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $is_showing_arrows ) : ?>
		<a class="left carousel-control" href="#<?php echo esc_attr( $section_key ); ?>" id="carousel-arrow" data-slide="prev"></a>
		<a class="right carousel-control" href="#<?php echo esc_attr( $section_key ); ?>" id="carousel-arrow" data-slide="next"></a>
		<?php endif; ?>
	</div>
<?php
endif;
