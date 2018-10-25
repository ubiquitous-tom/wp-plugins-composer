<section class="home-hero-carousel">
	<div class="carousel feature-slide slide carousel-fade" id="heroCarousel">
		<?php $total_items = count( $data_carousel ); ?>
		<ol class="carousel-indicators">
			<?php for ( $j = 0; $j < $total_items; $j++ ) : ?>
			<li data-target="#heroCarousel" data-slide-to="<?php echo $j; ?>" class="<?php echo ( $j === 0 ) ? 'active' : ''; ?>"></li>
			<?php endfor; ?>
		</ol>
		<div class="carousel-inner">
			<?php
			$i = 0;
			foreach ( $data_carousel as $item ) :
				$prev_item = ( $i > 0 ) ? $data_carousel[ $i - 1 ] : $data_carousel[ $total_items - 1 ];
				$next_item = ( $i < $total_items - 1 ) ? $data_carousel[ $i + 1 ] : $data_carousel[0];

				// Gets Links.
				$prev_link = apply_filters( 'atv_heroCarusel_link', $prev_item );
				$next_link = apply_filters( 'atv_heroCarusel_link', $next_item );
				$curr_link = apply_filters( 'atv_heroCarusel_link', $item );

				// Images.
				$curr_img = ( ! empty( $item->image ) ) ? $item->image : '';
				$prev_img = ( ! empty( $prev_item->image ) ) ? $prev_item->image : '';
				$next_img = ( ! empty( $next_item->image ) ) ? $next_item->image : '';

				$i++;
			?>
			<div class="item <?php echo ( $item === reset( $data_carousel ) ) ? 'active' : ''; ?>">
				<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 left">
					<a href="<?php echo esc_url( $prev_link ); ?>">
						<?php echo apply_filters( 'rlje_carousel_slide_image', $prev_img, 'previous', $prev_item ); ?>
					</a>
				</div>
				<div class="container">
					<a href="<?php echo esc_url( $curr_link ); ?>">
						<?php echo apply_filters( 'rlje_carousel_slide_image', $curr_img, 'current', $item ); ?>
					</a>
				</div>
				<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 right">
					<a href="<?php echo esc_url( $next_link ); ?>">
						<?php echo apply_filters( 'rlje_carousel_slide_image', $next_img, 'next', $next_item ); ?>
					</a>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="container carousel-controls">
			<div class="control-position">
				<a class="left carousel-control" href="#heroCarousel" data-slide="prev">
					<img class="hero-left-arrow" src="<?php echo apply_filters( 'rlje_front_page_homepage_hero_left_arrow', plugins_url( '../img/hero-left.png', __FILE__ ) ); //rljeApiWP_getImageUrlFromServices( 'hero-left?t=Icons' ); ?>"/>
				</a>
			</div>

			<div class="control-position">
				<a class="right carousel-control" href="#heroCarousel" data-slide="next">
					<img class="hero-right-arrow" src="<?php echo apply_filters( 'rlje_front_page_homepage_hero_right_arrow', plugins_url( '../img/hero-right.png', __FILE__ ) );//rljeApiWP_getImageUrlFromServices( 'hero-right?t=Icons' ); ?>"/>
				</a>
			</div>
		</div>
	</div>
</section>
