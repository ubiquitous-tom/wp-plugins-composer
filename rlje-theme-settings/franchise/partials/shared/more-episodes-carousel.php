<?php
// global $wp_query;

// if ( isset( $wp_query->query_vars['streamPositions'] ) ) {
// 	$stream_positions = $wp_query->query_vars['streamPositions'];
// }

// $base_url_path          = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
// $franchise_name        = ( isset( $wp_query->query_vars['franchiseName'] ) ) ? $wp_query->query_vars['franchiseName'] : null;
// $franchise_id          = $wp_query->query_vars['franchise_id'];
// $current_episode_number = $wp_query->query_vars['current_episode_id'];
// $seasons              = $wp_query->query_vars['seasons_carousel'];
?>
<div class="carousel carousel-respond-slide slide" id="popularseries" data-interval="false">
	<div class="row">
		<div class="carousel-inner">
		<?php foreach ( $seasons as $season_key => $season ) : ?>
			<?php
			foreach ( $season->episodes as $key => $episode ) :
				$episode_url    = trailingslashit( home_url( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) ) );
				$is_active      = ( $episode->id === $current_episode_number ) ? 'active' : '';
				$episode_number = apply_filters( 'atv_get_episode_number', $episode, ( $key + 1 ) );
				?>
			<div class="item <?php echo sanitize_html_class( $is_active ); ?>" itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
				<div>
					<a itemprop="url" href="<?php echo esc_url( $episode_url ); ?>">
						<div class="col-xs-12 col-sm-6 col-md-3">
							<img src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>" id="play-episodes">
							<img itemprop="image" style="margin-top:10px;" width="100%" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=500' ); ?>"/>
						<?php
						if ( isset( $stream_positions, $stream_positions[ $episode->id ] ) ) :
							$stream_position_data = $stream_positions[ $episode->id ];
							?>
							<div class="progress progress-danger">
								<div class="bar" style="width: <?php echo $progress = ( ( $stream_position_data['Position'] / $stream_position_data['EpisodeLength'] ) * 100 ); ?>%;">
									<span class="watched"><?php echo rljeApiWP_convertSecondsToMinSecs( $stream_position_data['Position'] ); ?></span>
								</div>
								<span class="length"><?php echo rljeApiWP_convertSecondsToMinSecs( $stream_position_data['EpisodeLength'] ); ?></span>
							</div>
							<?php endif; ?>
							<meta itemprop="timeRequired" content="<?php echo 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S'; ?>"/>
							<meta itemprop="partOfSeries" content="<?php echo esc_html( $franchise_name ); ?>" />
							<meta itemprop="partOfSeason" content="<?php echo esc_html( $season->name ); ?>" />
							<div class="franchise-eps-bg">
								<h5 itemprop="name"><?php echo esc_html( $episode->name ); ?></h5>
								<?php
								if ( 'movie' === strtolower( $episode->type ) ) {
									$more_episode_type_display = ' Movie ';
								} else {
									$more_episode_type_display = $season->name . ': Episode <span itemprop="episodeNumber">' . $episode_number . '</span>';
								}
								?>
								<h6><?php echo $more_episode_type_display; ?></h6>
							</div>
						</div>
					</a>
					</div>
			</div>
				<?php
				endforeach;
			?>
			<?php
			endforeach;
		?>
		</div>
	</div>
	<a class="left carousel-control" href="#popularseries" id="carousel-arrow" data-slide="prev"></a>
	<a class="right carousel-control" href="#popularseries" id="carousel-arrow" data-slide="next"></a>
</div>
