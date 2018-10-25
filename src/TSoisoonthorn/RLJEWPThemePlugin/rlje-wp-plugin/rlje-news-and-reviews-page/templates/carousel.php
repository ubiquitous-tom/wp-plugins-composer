<?php
$base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
?>
<div class="col-md-12 col-lg-6">
	<div class="home-hero-carousel">
		<div id="<?php echo sanitize_html_class( 'carousel_fade_' . $news_group );?>" class="carousel slide carousel-fade " data-pause="true"  data-interval="false" data-ride="carousel">
			<?php if ( count( $marketing_places ) > 1 ) : ?>
			<a class="left carousel-control" href="#<?php echo sanitize_html_class( 'carousel_fade_' . $news_group );?>" data-slide="prev" id="carousel-arrow">
				<img src="<?php echo esc_url( plugins_url( '../img/carousel-left.png', __FILE__ ) ); ?>" width="35px">
			</a>
			<a class="right carousel-control" href="#<?php echo sanitize_html_class( 'carousel_fade_' . $news_group );?>" data-slide="next" id="carousel-arrow">
				<img src="<?php echo esc_url( plugins_url( '../img/carousel-right.png', __FILE__ ) ); ?>" width="35px">
			</a>
			<?php endif; ?>

			<div class="carousel-inner">
			<?php foreach ( $marketing_places as $key => $item ) : ?>
				<div class="item <?php echo ( $key == 0 ) ? 'active' : ''; ?>">
					<?php if ( 'image' === $item['type'] ) : ?>
					<a <?php if ( ! empty( $item['franchiseId'] ) ) : ?> href="<?php echo esc_url( trailingslashit( home_url( $item['franchiseId'] ) ) ); ?>" <?php endif; ?>>
						<img alt="thumb marketing image" class="sliderimage" src="<?php echo esc_url( $item['src'] ); ?>">
					</a>
					<?php
						elseif ( 'extImage' === $item['type'] ) :
						$external_link = ( preg_match( '/^http[s]*\:[\/]{2}/i', $item['externalLink'] ) ) ? $item['externalLink'] : 'http://' . str_replace( [ ':', '//' ], '', $item['externalLink'] );
					?>
					<a <?php if ( ! empty( $item['externalLink'] ) ) : ?> href="<?php echo esc_url( $external_link ); ?>" <?php endif; ?> onclick="window.open('<?php echo esc_url( $external_link ); ?>', 'newwindow', 'scrollbars=yes,top=200, left=100,width=850, height=600'); return false;" target="_blank">
						<img alt="thumb marketing image" class="sliderimage" src="<?php echo $item['src']; ?>">
					</a>
					<?php else : ?>
					<div class="news-carousel">
						<video preload
							data-account="<?php echo get_option( 'rlje_theme_brightcove_shared_settings' )['shared_account_id']; ?>"
							data-player="<?php echo get_option( 'rlje_theme_brightcove_shared_settings' )['shared_player_id']; ?>"
							data-embed="default"
							data-video-id="<?php echo esc_attr( $item['src'] ); ?>"
							class="brightcove-news-carousel-player video-js embed-responsive embed-responsive-16by9"
							controls></video>
					</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
