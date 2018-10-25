<?php
set_query_var( 'continueWatchingItems', $franchise->seasons );
set_query_var( 'totalEpisodes', $total_episodes );
set_query_var( 'streamposition', $stream_positions );
$continue_watching_items = $franchise->seasons;// get_query_var('continueWatchingItems');
$total_episodes          = $total_episodes;// get_query_var('totalEpisodes');
$stream_positions        = $stream_positions;// get_query_var('streamposition');
// $franchise_id = get_query_var('franchise_id');
// $base_url_path = (function_exists('rljeApiWP_getBaseUrlPath')) ? rljeApiWP_getBaseUrlPath() : '';
$showing_carosel = ( $total_episodes > 4 );
// $episode_id = get_query_var('episodeID');
if ( ! empty( $continue_watching_items ) && count( $continue_watching_items ) > 0 ) :
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
					$highlightNextEpisode = false;
				foreach ( $continue_watching_items as $seasonKey => $season ) :
					foreach ( $season->episodes as $key => $episode ) :
						$showEpisodeHighlighted = false;
						$showEpisodeActive      = false;
						$isResume               = false;
						$streamPositionData     = array();
						$episodeNumber          = apply_filters( 'atv_get_episode_number', $episode, ( $key + 1 ) );
						if ( ! empty( $episode_id ) && $episode->id === $episode_id ) {
							$showEpisodeActive      = true;
							$showEpisodeHighlighted = true;
						}
						if ( $highlightNextEpisode ) {
							$showEpisodeHighlighted = true;
							$highlightNextEpisode   = false;
						}
						if ( isset( $stream_positions, $stream_positions[ $episode->id ] ) ) {
							$streamPositionData = $stream_positions[ $episode->id ];
							$isLastStreamed     = ( isset( $streamPositionData['Counter'] ) && 1 === $streamPositionData['Counter'] );
							if ( $isLastStreamed || $showEpisodeHighlighted ) {
								$showEpisodeActive      = ( ! $showEpisodeActive ) ? ! $showEpisodeHighlighted : true;
								$showEpisodeHighlighted = true;
								if ( $streamPositionData['Position'] < ( $streamPositionData['EpisodeLength'] - 60 ) ) {
									$isResume = true;
								} else {
									$showEpisodeHighlighted = false;
									$highlightNextEpisode   = true;
								}
							}
						}
						?>
				<div class="item<?php echo ( $showEpisodeActive ) ? ' active' : ''; ?>" itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
					<div class="col-sm-6 col-md-3<?php echo ( $showEpisodeHighlighted ) ? ' highlight-episode' : ''; ?> " style="padding-top:25px;">
						<a itemprop="url" href="<?php echo esc_url( home_url( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) . '/' ) ); ?>">
							<img src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>" id="play-episodes" />
							<img itemprop="image" width="100%" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=500' ); ?>" />
						<?php if ( 0 < count( $streamPositionData ) ) : ?>
							<div class="progress progress-danger">
								<div class="bar" style="width: <?php echo $progress = ( ( $streamPositionData['Position'] / $streamPositionData['EpisodeLength'] ) * 100 ); ?>%;">
									<span class="watched"><?php echo rljeApiWP_convertSecondsToMinSecs( $streamPositionData['Position'] ); ?></span>
								</div>
								<span class="length"><?php echo rljeApiWP_convertSecondsToMinSecs( $streamPositionData['EpisodeLength'] ); ?></span>
							</div>
							<?php endif; ?>
							<meta itemprop="timeRequired" content="<?php echo ( ! empty( $episode->length ) ) ? 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S' : ''; ?>" />
							<div class="franchise-eps-bg<?php echo ( $showEpisodeHighlighted ) ? ' no-margin-bottom' : ''; ?>">
								<h5 itemprop="name"><?php echo $episode->name; ?></h5>
								<?php
								if ( 'movie' === strtolower( $episode->type ) ) {
									$episode_type_display = ' Movie ';
								} else {
									$episode_type_display = $season->name .': Episode <span itemprop="episodeNumber">' . $episodeNumber . '</span>';
								}
								?>
								<h6><?php echo $episode_type_display; ?></h6>
							</div>
						<?php
						if ( $showEpisodeHighlighted ) :
							$playType = ( ! empty( $episode_id ) ) ? 'player' : 'play';
							?>
							<div class="continueWatching">
								<?php if ( !$isResume ) { ?>
									<button class="js-<?php echo $playType; ?>-resume">
										<?php
										if ( 'movie' === strtolower( $episode->type ) ) {
											$continue_watching_episode_type_display = ' Movie ';
										} else {
											$continue_watching_episode_type_display = $season->name . ': Episode ' . $episodeNumber;
										}
										?>
										<span>Play <?php echo esc_html( $continue_watching_episode_type_display ); ?></span>
										<i class="fa fa-play-circle-o" aria-hidden="true"></i>
									</button>
								<?php } ?>
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
endif;
?>
