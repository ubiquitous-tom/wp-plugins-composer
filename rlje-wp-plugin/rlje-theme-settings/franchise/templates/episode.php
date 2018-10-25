<?php
// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
// if ( function_exists( 'rljeApiWP_getFranchiseById' ) ) :
	// $environment = apply_filters( 'atv_get_extenal_subdomain', '' );

	// $franchise_id     = get_query_var( 'franchise_id' );
	// $season_name_url  = get_query_var( 'season_name' );
	// $episode_name_url = get_query_var( 'episode_name' );

	$franchise = rljeApiWP_getFranchiseById( $franchise_id );
	$season    = rljeApiWP_getCurrentSeason( $franchise_id, $season_name_url );
	$episode   = rljeApiWP_getCurrentEpisode( $franchise_id, $season_name_url, $episode_name_url );

	$prev_episode_url     = rljeApiWP_getPreviousEpisodeURL( $franchise_id, $season_name_url, $episode_name_url );
	$next_episode_url     = rljeApiWP_getNextEpisodeURL( $franchise_id, $season_name_url, $episode_name_url );
	$next_episode_data    = apply_filters( 'atv_get_next_episode_data', $franchise, $season->id, $episode->id );
	$stream_positions     = null;
	$is_cast              = ( isset( $episode->actors ) && count( $episode->actors ) > 0 ) ? true : false;
	$is_video_debugger_on = rljeApiWP_isVideoDebuggerOn();
	$is_user_above_concurrent_limit = false;

	$total_episodes = 0;
	foreach ( $franchise->seasons as $season_item ) {
		$total_episodes += count( $season_item->episodes );
	}

	if ( isset( $episode->id ) ) :
		$atv_session_cookie = ( isset( $_COOKIE['ATVSessionCookie'] ) ) ? $_COOKIE['ATVSessionCookie'] : false;
		$is_user_active     = ( $atv_session_cookie && rljeApiWP_isUserActive( $atv_session_cookie ) );
		if ( $is_user_active ) {
			$watchlist            = rljeApiWP_getUserWatchlist( $atv_session_cookie );
			$get_stream_positions = rljeApiWP_getStreamPositionsByFranchise( $franchise_id, $atv_session_cookie );
			if ( isset( $get_stream_positions->streamPositions ) ) {
				$stream_positions = [];
				foreach ( $get_stream_positions->streamPositions as $stream_position ) {
					$stream_positions[ $stream_position->EpisodeID ] = [
						'Position'      => $stream_position->Position,
						'EpisodeLength' => $stream_position->EpisodeLength,
					];
				}
			}
			$is_user_above_concurrent_limit = rljeApiWP_isUserAboveConcurrentStreams( $atv_session_cookie );
		}
		get_header();
		?>

<script>dataLayer=[];</script>

<div class="secondary-bg">
	<div class="container franchise">
		<div id="episode" class="col-md-12" itemscope itemtype="http://schema.org/TVEpisode">
		<h4 class="subnav">
			<!-- Previous link -->
			<span class="subnav-prev hidden-xs hidden-sm">
				<?php if ( ! isset( $prev_episode_url ) ) : ?>
				<a href="<?php echo esc_url( trailingslashit( home_url( $franchise_id ) ) ); ?>">
					<span>Back <!-- to Series --></span>
				</a>
				<?php else : ?>
				<a href="<?php echo esc_url( trailingslashit( home_url( $prev_episode_url ) ) ); ?>">
					<span>Last Episode</span>
				</a>
				<?php endif; ?>
			</span>
			<a href="<?php echo esc_url( trailingslashit( home_url( $franchise_id ) ) ); ?>" id="subnav-title"><span itemprop="partOfSeries"><?php echo esc_html( $franchise->name ); ?></span></a>,
			<?php
			if ( 'movie' === strtolower( $episode->type ) ) {
				echo esc_html( $episode->type );
			} else {
				echo '<span itemprop="partOfSeason">' . esc_html( $season->name ) . '</span> : <span itemprop="name">' . esc_html( ( strlen( $episode->name ) > 45 ) ? substr( $episode->name, 0, 45 ) . '...' : $episode->name ) .'</span>   <!-- Next link -->';
			}
			?>
			<meta itemprop="image" content="<?php echo apply_filters( 'atv_get_image_url', $episode->image ); ?>" />
			<meta itemprop="description" content="<?php echo esc_attr( $episode->longDescription ); ?>" />
			<meta itemprop="episodeNumber" content="<?php echo esc_attr( $episode->episodeNumber ); ?>" />
			<meta itemprop="endDate" content="<?php echo esc_attr( ( isset( $episode->endDate ) && $episode->endDate != '' ) ? date( 'Y-m-d', $episode->endDate ) : '' ); ?>" />
			<meta itemprop="startDate" content="<?php echo esc_attr( ( isset( $episode->endDate ) && $episode->endDate != '' ) ? date( 'Y-m-d', $episode->startDate ) : '' ); ?>" />
			<meta itemprop="timeRequired" content="<?php echo esc_attr( 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S' ); ?>"/>

			<span class="subnav-next hidden-xs hidden-sm
			<?php
			if ( ! isset( $next_episode_url ) ) {
				echo 'invisible'; }
			?>
			">
			<?php if ( isset( $next_episode_url ) ) : ?>
			<a href="<?php echo esc_url( trailingslashit( home_url( $next_episode_url ) ) ); ?>">
				<span>Next Episode</span>
			</a>
			<?php endif; ?>
			</span>
		</h4>
		<!-- Brightcove Episode Player -->
		<div class="outer-container episode-player">
		<?php
		if ( $is_user_active ) {
			if ($is_user_above_concurrent_limit) { ?>
				<div id="episode-trailer" class="video">
					<img title="image title" alt="thumb image" class="wp-post-image" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ); ?>"/>
					<div class="acorntv-slogan">
						<h3>Concurrent stream limit exceeded</h3>
						<h4>You are currently above concurrent stream limit.</h4>
					</div>
				</div>
			<?php
			} else {
				?>
				<span itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
				<meta itemprop="thumbnailUrl" content="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ); ?>" />
				<meta itemprop="description" content="<?php echo $episode->longDescription; ?>" />
				<meta itemprop="name" content="<?php echo $episode->name; ?>" />
				<meta itemprop="uploadDate" content="<?php echo ( isset( $episode->endDate ) && $episode->endDate != '' ) ? date( 'Y-m-d', $episode->startDate ) : ''; ?>" />
				<meta itemprop="duration" content="<?php echo 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S'; ?>" />
				<video
					id="brightcove-episode-player"
					data-account="<?php echo esc_attr( $bc_account_id ); ?>"
					data-player="<?php echo esc_attr( $bc_player_id ); ?>"
					data-embed="default"
					data-video-id="ref:<?php echo $episode->id; ?>"
					poster="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ); ?>"
					class="video-js embed-responsive embed-responsive-16by9"
					controls></video>
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						episodePlayer(<?php echo '"' . $episode->id . '"'; echo ( isset( $stream_positions[ $episode->id ], $stream_positions[ $episode->id ]['Position'] ) ) ? ', ' . $stream_positions[ $episode->id ]['Position'] : ''; ?>);

						videojs('brightcove-episode-player').ready(function(){
							var player = this;

							function geoErrorMessage() {

								videojs.log('RLJ:ATV - Error');
								try {
									var errorMessage = '';
									if (typeof player.error().code != 'undefined') {
										errorMessage += player.error().code;
									}
									if (typeof player.error().message != 'undefined') {
										errorMessage += ' - ' + player.error().message;
									}
									if (typeof player.error().type != 'undefined') {
										errorMessage += ' - ' + player.error().type;
									}
									dataLayer.push({'event': '' + errorMessage});
									videojs.log('RLJ:ATV - Error: ' + errorMessage);
								} catch (err) {
									console.log('Raised error: ' + err);
								}

								if(typeof player.catalog.error !== 'undefined' && player.catalog.error.data[0].error_subcode === 'CLIENT_GEO') {
									var message = 'Content is not available in your current location';
									console.log(message);
									player.errors({
										"errors": {
										"-1": {
											"headline": player.catalog.error.data[0].message,
											"type": player.catalog.error.data[0].error_subcode,
											"message": message
										}
										}
									});
								}
							}

							player.on("error", geoErrorMessage);

							player.on("ended", function() {
								// hack for BC issue
								if (player.autoplay()) {
									player.autoplay(false);
								}
								if (player.tech_ && player.tech_.hls) {
									player.tech_.hls.mediaSource.videoBuffer_.remove(0, Infinity);
								}
							});
						});
					});
				</script>
				<?php if ( isset( $stream_positions[ $episode->id ] ) ) : ?>
				<div class="continueWatching">
					<div class="playerButtons">
						<button class="continueEpisodeBtn js-player-start" data-no-scroll>
							<span>PLAY FROM START</span>
							<i class="fa fa-refresh" aria-hidden="true"></i>
						</button>
						<button class="continueEpisodeBtn js-player-resume" data-no-scroll>
							<span>RESUME</span>
							<i class="fa fa-play-circle-o" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<?php endif; ?>
				<div class="playNextEpisode">
					<?php if ( isset( $next_episode_data ) ) : ?>
					<button class="js-play-next" data-next="<?php echo esc_url( trailingslashit( home_url( $next_episode_url ) ) ); ?>">
						<span>PLAY NEXT EPISODE</span>
						<i class="fa fa-play-circle-o" aria-hidden="true"></i>
					</button>
					<?php elseif ( is_array( $watchlist ) && 0 < count( $watchlist ) ) : ?>
					<button class="js-go-to" data-url="<?php echo esc_url( home_url( '/browse/yourwatchlist/' ) ); ?>">
						<span>MY WATCHLIST</span>
					</button>
					<?php else : ?>
					<button class="js-go-to" data-url="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span>HOME</span>
					</button>
					<?php endif; ?>
				</div>
				<?php if ( isset( $next_episode_data ) ) { ?>
				<div id="nextEpisodeOverlay" class="episode">
					<a class="js-play-start js-episode-loading" href="<?php echo esc_url( trailingslashit( home_url( $next_episode_url ) ) ); ?>">
						<p class="headerText">Play Next Episode</p>
						<div class="episodeOverlay">
							<div class="imgOverlayContainer">
							<span class="loading">
								<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
								<span class="sr-only">Loading...</span>
							</span>
							<img src="<?php echo esc_url( plugins_url( '../img/play-icon.png', __FILE__ ) ); ?>" id="play-episodes">
							<img class="episodeImgOverlay" src="<?php echo apply_filters( 'atv_get_image_url', $next_episode_data->image . '?w=250' ); ?>" alt="<?php echo $next_episode_data->name; ?>"/>
							</div>
							<div class="episodeDetailOverlay">
								<p><?php echo $next_episode_data->name; ?></p>
								<p><?php echo $next_episode_data->seasonName; ?>: <?php echo 'Episode ' . $next_episode_data->episodeNumber; ?></p>
							</div>
						</div>
					</a>
				</div>
				<?php }
			}
		} else {
			if ( isset( $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) :
				$trailer_id = $franchise->episodes[0]->id;
				$rlje_theme_text_settings = get_option( 'rlje_theme_text_settings' );
				$video_placeholder = ( ! empty( $rlje_theme_text_settings['video_placeholder'] ) ) ? $rlje_theme_text_settings['video_placeholder'] : array();
				$video_placeholder_title_text = ( ! empty( $video_placeholder['title_text'] ) ) ? $video_placeholder['title_text'] : 'Watch world-class TV from Britain and beyond';
				$video_placeholder_text = ( ! empty( $video_placeholder['text'] ) ) ? $video_placeholder['text'] : 'Always available, always commercial free';
				?>
			<span itemprop="trailer" itemscope itemtype="http://schema.org/VideoObject">
				<meta itemprop="thumbnailUrl" content="<?php echo esc_url( apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ) ); ?>" />
				<meta itemprop="description" content="<?php echo esc_html( $episode->longDescription ); ?>" />
				<meta itemprop="name" content="<?php echo esc_html( $episode->name ); ?>" />
				<meta itemprop="uploadDate" content="<?php echo esc_attr( ( isset( $episode->startDate ) && $episode->startDate != '' ) ? date( 'Y-m-d', $episode->startDate ) : '' ); ?>" />
				<!-- <div class="video" data-embedcode="<iframe style='border:none;z-index:4' src='//players.brightcove.net/<?php echo esc_attr( $bc_account_id ); ?>/<?php echo esc_attr( $bc_player_id ); ?>_default/index.html?videoId=<?php echo $trailer_id; ?>'
					allowfullscreen
					webkitallowfullscreen
					mozallowfullscreen></iframe>"> -->
				<div id="episode-trailer" class="video">
					<img title="image title" alt="thumb image" class="wp-post-image" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ); ?>"/>
					<div class="acorntv-slogan">
						<!-- <h3>Watch world-class TV from Britain and beyond</h3> -->
						<h3><?php echo esc_html( $video_placeholder_title_text ); ?></h3>
						<!-- <h4>Always available, always commercial free</h4> -->
						<h4><?php echo esc_html( $video_placeholder_text ); ?></h4>
						<a class="btn btn-primary" href="<?php echo esc_url( trailingslashit( home_url( 'signup' ) ) ); ?>">Start Your Free Trial</a>
						<h5>
							<button class="transparent js-play">
								<img src="<?php echo esc_url( plugins_url( '../img/play-icon.png', __FILE__ ) ); ?>" height="35" style="opacity:1">
								<span>WATCH TRAILER</span>
							</button>
						</h5>
					</div>
					<video preload
						id="brightcove-episode-trailer-player"
						class="hidden"
						data-account="<?php echo esc_attr( $bc_account_id ); ?>"
						data-player="<?php echo esc_attr( $bc_player_id ); ?>"
						data-embed="default"
						data-video-id="ref:<?php echo esc_attr( $trailer_id ); ?>"
						poster="<?php echo esc_url( apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ) ); ?>"
						class="video-js embed-responsive embed-responsive-16by9"
						controls></video>
					<script>
					// (function() {

					// 	var videos = document.querySelectorAll('.video');

					// 	for (var i = 0; i < videos.length; i++) {

					// 		// Closure to call the playVideo function.
					// 		if(videos[i].querySelector('.js-play')) {
					// 			videos[i].querySelector('.js-play').onclick = (function(index) {
					// 				return function() {
					// 				loadVideo(this, videos[index]);
					// 				};
					// 			})(i);
					// 		}
					// 	}

					// 	function loadVideo(button, video) {
					// 		var embedcode = video.dataset.embedcode;
					// 		var hasvideo = video.dataset.hasvideo;

					// 		// Only append the video if it isn't yet appended.
					// 		if (!hasvideo) {
					// 		video.insertAdjacentHTML('afterbegin', embedcode);
					// 		video.dataset.hasvideo = "true";
					// 		button.remove();
					// 		}
					// 	}

					// })();
					</script>
				</div>
			</span>
				<?php
			else :
				?>
			<img title="Play trailer" class="wp-post-image" src="<?php echo esc_url( apply_filters( 'atv_get_image_url', $episode->image . '?w=750' ) ); ?>"/>
			<div class="acorntv-slogan">
				<!-- <h3>Watch world-class TV from Britain and beyond</h3> -->
				<h3>Stream UMC Anywhere!</h3>
				<!-- <h4>Always available, always commercial free</h4> -->
				<h4>Start your FREE 7-day trial to watch the best in Black film & television with new and exclusive content added weekly! Download UMC on your favorite Apple and Android mobile devices or stream on Roku or Amazon Prime Video Channels. Drama, romance, comedy and much more - it’s all on UMC!</h4>
				<a class="btn btn-primary" href="<?php echo esc_url( trailingslashit( home_url( 'signup' ) ) ); ?>">Start Your Free Trial</a>
			</div>
				<?php
			endif;
		}
		?>
		</div>
		</div>
	</div>
</div>

<!-- Episode content begins (descriptions, tags, more episodes, and related titles) -->
<div class="container episode">
	<div class="row">
		<!-- Episode Title and Description -->
		<div class="col-md-<?php echo ( $is_cast ) ? '6' : '12'; ?>" id="eps-desc">
			<h4 class="subnav2">Description</h4>
			<p><?php echo esc_html( $episode->name ); ?>: <?php echo esc_html( $episode->longDescription ); ?></p>
		</div>

			<?php if ( $is_cast ) : ?>
		<!-- Related Themes (wp tags) -->
		<div class="col-md-6 col-sm-12 column " id="eps-tags">
			<h4 class="subnav2">Cast</h4>
			<div class="episode-starring">
				<span>Starring: </span>
				<?php foreach ( $episode->actors as $key => $value ) : ?>
				<span itemprop="actor" itemscope itemtype="http://schema.org/Person">
					<span itemprop="name"><?php echo ( $key + 1 < count( $episode->actors ) ) ? $value . ', ' : $value . '.'; ?> </span>
				</span>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>

		<?php
		if ( ! empty( $stream_positions ) ) :
			// set_query_var( 'continueWatchingItems', $franchise->seasons );
			// set_query_var( 'episodeID', $episode->id );
			// set_query_var( 'totalEpisodes', $total_episodes );
			// set_query_var( 'streamposition', $stream_positions );
			$continue_watching_items = $franchise->seasons;// get_query_var('continueWatchingItems');
			$total_episodes          = $total_episodes;// get_query_var('totalEpisodes');
			$stream_positions        = $stream_positions;// get_query_var('streamposition');
			$episode_id              = $episode->id;
			// get_template_part( 'partials/continue-watching-carousel' );
			if ( ! empty( $continue_watching_items ) && count( $continue_watching_items ) > 0 ) {
				require_once plugin_dir_path( __FILE__ ) . '../partials/shared/continue-watching-carousel.php';
			}
		?>
		<?php else : ?>
	<!-- More Episodes Carousel -->
	<!-- Multiple carousels. If there is more than four episodes use bootstrap carousel-->

	<div class="col-md-12">
		<h4 class="subnav2">More Episodes</h4>
			<?php
			// set_query_var( 'franchiseName', $franchise->name );
			$franchise_name = $franchise->name;
			// set_query_var( 'streamPositions', $stream_positions );

			$is_less_than_4_episodes = apply_filters( 'atv_is_less_than_4_episodes', $franchise->seasons );

			if ( $is_less_than_4_episodes ) {
				// set_query_var( 'season', $franchise->seasons[0] );
				$season = $franchise->seasons[0];
				// set_query_var( 'ignoreSeasonHeader', true );
				$ignore_season_header = true;
				// get_template_part( 'partials/list-episode-items' );
				require_once plugin_dir_path( __FILE__ ) . '../partials/shared/list-episode-items.php';
			} else {
				// set_query_var( 'current_episode_id', $episode->id );
				// set_query_var( 'seasons_carousel', $franchise->seasons );
				// $franchise_id           = $wp_query->query_vars['franchise_id'];
				$current_episode_number = $episode->id;
				$seasons                = $franchise->seasons;
				// get_template_part( 'partials/more-episodes-carousel' );
				require_once plugin_dir_path( __FILE__ ) . '../partials/shared/more-episodes-carousel.php';
			}
			?>
	</div>

	<!-- Multiple carousels. If there is less than four episodes display basic column grid-->
	<?php endif; ?>


	<!-- You May Also Like Carousel -->
		<?php
		// THIS DID NOTHING. WHY IS IT HERE.
		// set_query_var( 'also_watched_items', rljeApiWP_getViewersAlsoWatched( $franchise_id ) );
		// get_template_part( 'partials/viewers-also-watched' );
		?>
</div>
		<?php
		get_footer();
	else :
		$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'franchise' );
		if ( $have_franchises_available ) {
			require_once get_404_template();
		} else {
			get_template_part( 'templates/franchisesUnavailable' );
		}
	endif;
// else :
// 	get_header();
// 	get_template_part( 'partials/plugin-deactivated-message' );
// 	get_footer();
// endif;
