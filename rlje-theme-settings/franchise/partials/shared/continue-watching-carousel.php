<?php
// set_query_var( 'continueWatchingItems', $franchise->seasons );
// set_query_var( 'totalEpisodes', $total_episodes );
// set_query_var( 'streamposition', $stream_positions );
// $continue_watching_items = $franchise->seasons;// get_query_var('continueWatchingItems');
// $total_episodes          = $total_episodes;// get_query_var('totalEpisodes');
// $stream_positions        = $stream_positions;// get_query_var('streamposition');
// $franchise_id = get_query_var('franchise_id');
// $base_url_path = (function_exists('rljeApiWP_getBaseUrlPath')) ? rljeApiWP_getBaseUrlPath() : '';
$showing_carosel = ( $total_episodes > 4 );
// $episode_id = get_query_var('episodeID');
// if ( ! empty( $continue_watching_items ) && count( $continue_watching_items ) > 0 ) :
?>
<div id="continueWatching" class="col-md-12 episode">
	<h4 class="subnav2">CONTINUE WATCHING</h4>
	<?php if ( $showing_carosel ) : ?>
	<div class="carousel carousel-respond-slide slide" id="newreleases" data-interval="false">
	<?php endif; ?>
		<div class="row">
			<?php if ( $showing_carosel ) : ?>
			<div class="carousel-inner">
			<?php endif; ?>
				<?php
					$highlight_next_episode = false;
				foreach ( $continue_watching_items as $season_key => $season ) :
					foreach ( $season->episodes as $key => $episode ) :
						$show_episode_highlighted = false;
						$show_episode_active      = false;
						$is_resume               = false;
						$stream_position_data     = array();
						$episode_number          = apply_filters( 'atv_get_episode_number', $episode, ( $key + 1 ) );
						if ( ! empty( $episode_id ) && $episode->id === $episode_id ) {
							$show_episode_active      = true;
							$show_episode_highlighted = true;
						}
						if ( $highlight_next_episode ) {
							$show_episode_highlighted = true;
							$highlight_next_episode   = false;
						}
						if ( isset( $stream_positions, $stream_positions[ $episode->id ] ) ) {
							$stream_position_data = $stream_positions[ $episode->id ];
							$is_last_streamed     = ( isset( $stream_position_data['Counter'] ) && 1 === $stream_position_data['Counter'] );
							if ( $is_last_streamed || $show_episode_highlighted ) {
								$show_episode_active      = ( ! $show_episode_active ) ? ! $show_episode_highlighted : true;
								$show_episode_highlighted = true;
								if ( $stream_position_data['Position'] < ( $stream_position_data['EpisodeLength'] - 60 ) ) {
									$is_resume = true;
								} else {
									$show_episode_highlighted = false;
									$highlight_next_episode   = true;
								}
							}
						}
						?>
				<div class="item<?php echo ( $show_episode_active ) ? ' active' : ''; ?>" itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
					<div class="col-sm-6 col-md-3<?php echo ( $show_episode_highlighted ) ? ' highlight-episode' : ''; ?> " style="padding-top:25px;">
						<a itemprop="url" href="<?php echo esc_url( home_url( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) . '/' ) ); ?>">
							<img src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>" id="play-episodes" />
							<img itemprop="image" width="100%" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=500' ); ?>" />
						<?php if ( 0 < count( $stream_position_data ) ) : ?>
							<div class="progress progress-danger">
								<div class="bar" style="width: <?php echo $progress = ( ( $stream_position_data['Position'] / $stream_position_data['EpisodeLength'] ) * 100 ); ?>%;">
									<span class="watched"><?php echo rljeApiWP_convertSecondsToMinSecs( $stream_position_data['Position'] ); ?></span>
								</div>
								<span class="length"><?php echo rljeApiWP_convertSecondsToMinSecs( $stream_position_data['EpisodeLength'] ); ?></span>
							</div>
							<?php endif; ?>
							<meta itemprop="timeRequired" content="<?php echo ( ! empty( $episode->length ) ) ? 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S' : ''; ?>" />
							<div class="franchise-eps-bg<?php echo ( $show_episode_highlighted ) ? ' no-margin-bottom' : ''; ?>">
								<h5 itemprop="name"><?php echo $episode->name; ?></h5>
								<?php
								if ( 'movie' === strtolower( $episode->type ) ) {
									$episode_type_display = ' Movie ';
								} else {
									$episode_type_display = $season->name .': Episode <span itemprop="episodeNumber">' . $episode_number . '</span>';
								}
								?>
								<h6><?php echo $episode_type_display; ?></h6>
							</div>
						<?php
						if ( $show_episode_highlighted ) :
							$playType = ( ! empty( $episode_id ) ) ? 'player' : 'play';
							?>
							<div class="continueWatching">
								<?php if ( $is_resume ) : ?>
								<button class="continueEpisodeBtn js-<?php echo $playType; ?>-start">
									<span>PLAY FROM START</span>
									<i class="fa fa-refresh" aria-hidden="true"></i>
								</button>
								<button class="continueEpisodeBtn js-<?php echo $playType; ?>-resume">
									<span>RESUME</span>
									<i class="fa fa-play-circle-o" aria-hidden="true"></i>
								</button>
								<?php else : ?>
								<button class="js-<?php echo $playType; ?>-resume">
									<?php
									if ( 'movie' === strtolower( $episode->type ) ) {
										$continue_watching_episode_type_display = ' Movie ';
									} else {
										$continue_watching_episode_type_display = $season->name . ': Episode ' . $episode_number;
									}
									?>
									<span>Play <?php echo esc_html( $continue_watching_episode_type_display ); ?></span>
									<i class="fa fa-play-circle-o" aria-hidden="true"></i>
								</button>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						</a>
					</div>
				</div>
						<?php
						endforeach;
					endforeach;
				?>
			<?php if ( $showing_carosel ) : ?>
			</div>
			<a class="left carousel-control" href="#newreleases" id="carousel-arrow" data-slide="prev"></a>
			<a class="right carousel-control" href="#newreleases" id="carousel-arrow" data-slide="next"></a>
			<?php endif; ?>
		</div>
	<?php if ( $showing_carosel ) : ?>
	</div>
	<?php endif; ?>
</div>
	<?php
// endif;
?>
