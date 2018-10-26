<?php
$section             = get_query_var( 'carousel-section' );
$section_title       = $section['title'];
$category_obj        = $section['categoryObj'];
$category_id         = $category_obj->id;
$is_showing_view_all = ( isset( $section['showViewAllLink'] ) ) ? $section['showViewAllLink'] : true;
$browse_id           = apply_filters( 'atv_get_browse_section_id', $category_id );
$content_page_id     = apply_filters( 'atv_get_contentPage_section_id', $category_id );

$carousel_items = ( isset( $category_obj->media ) ) ? $category_obj->media : null;
$total_page     = ( isset( $category_obj->totalpages ) ) ? $category_obj->totalpages : 0;

// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
$is_showing_arrows = true;

if ( isset( $carousel_items ) && count( $carousel_items ) > 0 ) { ?>
	<h4 <?php echo ( $browse_id === 'mystery' ) ? 'id="third-spotlight"' : ''; ?> class="subnav2"><?php echo $section_title; ?></h4>

	<?php if ( $is_showing_view_all ) { ?>
		<div class="view-all hidden-xs">
			<a href="<?php echo esc_url( trailingslashit( home_url( '/browse/' . $browse_id ) ) ); ?>"> View all </a>
		</div>
	<?php } ?>

	<div class="carousel carousel-pagination-slide slide" id="<?php echo esc_attr( $browse_id ); ?>" data-interval="false" data-total-pages="<?php echo esc_attr( $total_page ); ?>" data-page-loaded="1" data-content="<?php echo esc_attr( $content_page_id ); ?>" data-section-desc="<?php echo esc_attr( $section_title ); ?>">
		<div class="row">
			<div class="carousel-inner">
				<?php
				if ( count( $carousel_items ) > 4 ) {
					foreach ( $carousel_items as $key => $item ) {
						?>
						<div class="item<?php echo ( 0 === $key ) ? ' active' : ''; ?>" data-item="<?php echo $key; ?>">
							<?php
							for ( $i = 0,$j = 0; $i < 4; $i++ ) {
								if ( 0 < $i ) {
									if ( isset( $carousel_items[ $key + 1 ] ) ) {
										$key++;
										$item = $carousel_items[ $key ];
									} else {
										$item = ( isset( $carousel_items[ $j ] ) ) ? $carousel_items[ $j ] : new stdClass();
										$j++;
									}
								}
								// Set href id and image.
								if ( isset( $item->id ) || isset( $item->franchiseID ) ) {
									$item = apply_filters( 'atv_add_img_and_href', $item );
								} else {
									// Next item.
									break;
								}
								?>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3<?php echo ( 0 < $i ) ? ' cloneditem-' . $i : ''; ?>" id="avatar">
									<a href="<?php echo esc_url( trailingslashit( home_url( $item->href ) ) ); ?>">
										<img title="<?php echo esc_attr( $item->name ); ?>" alt="<?php echo esc_attr( $item->name ); ?> - <?php echo esc_attr( $section_title ); ?> category image" class="wp-post-image" id="avatar-rollover" src="<?php echo esc_url( $item->img . '?t=titled-avatars&w=400&h=225' ); ?>"/>
									</a>
								</div>
							<?php } ?>
						</div>
					<?php
					}
				} else {
					$is_showing_arrows = false;
					?>
					<div class="item active">
						<?php
						foreach ( $carousel_items as $key => $item ) {
							$item = apply_filters( 'atv_add_img_and_href', $item );
							?>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" id="avatar">
								<a href="<?php echo esc_url( trailingslashit( home_url( $item->href ) ) ); ?>">
									<img title="<?php echo esc_attr( $item->name ); ?>" alt="<?php echo esc_attr( $item->name ); ?> - <?php echo esc_attr( $section_title ); ?> category image" class="wp-post-image" id="avatar-rollover" src="<?php echo esc_url( $item->img . '?t=titled-avatars&w=400&h=225' ); ?>"/>
								</a>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php if ( $is_showing_arrows ) { ?>
			<a class="left carousel-control" href="#<?php echo esc_attr( $browse_id ); ?>" id="carousel-arrow" data-slide="prev"></a>
			<a class="right carousel-control" href="#<?php echo esc_attr( $browse_id ); ?>" id="carousel-arrow" data-slide="next"></a>
		<?php } ?>
	</div>
<?php
}
