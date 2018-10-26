<?php
// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';


	// if ( function_exists( 'rljeApiWP_getFranchiseById' ) ) :
		// $franchise_id = get_query_var( 'franchise_id' );
		// $franchise   = rljeApiWP_getFranchiseById( $franchise_id );

		// if ( isset( $franchise->id ) ) :
			// if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
			// 	$get_stream_positions = rljeApiWP_getStreamPositionsByFranchise( $franchise_id, $_COOKIE['ATVSessionCookie'] );
			// 	if ( isset( $get_stream_positions->streamPositions ) ) {
			// 		$stream_positions = [];
			// 		$count_positions  = 1;
			// 		foreach ( $get_stream_positions->streamPositions as $stream_position ) {
			// 			$stream_positions[ $stream_position->EpisodeID ] = [
			// 				'Position'      => $stream_position->Position,
			// 				'EpisodeLength' => $stream_position->EpisodeLength,
			// 				'Counter'       => $count_positions,
			// 			];
			// 			$count_positions++;
			// 		}
			// 	}
			// }
			// $total_episodes = 0;
			// foreach ( $franchise->seasons as $season_item ) {
			// 	$total_episodes += count( $season_item->episodes );
			// }
			get_header();
			$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'franchise' );
			$franchise_url            = home_url( $franchise->id );

			// CHECK FOR FRANCHISE TRAILER
			// Legacy Code no longer used
			// if ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) {
			// 	$franchise_art_link  = trailingslashit( $franchise_url . 'trailer' );
			// 	$franchise_art_title = 'Click to view trailer';
			// }

			// CHECK FOR STREAM POSITIONS ONLY FOR LOGGEDIN USER
			// if ( isset( $stream_positions ) ) {
			// 	$stream_position_keys = array_keys( $stream_positions );
			// 	$stream_position_data = array_values( $stream_positions );
			// 	$break              = false;
			// 	if ( $stream_position_data[0]['Position'] < ( $stream_position_data[0]['EpisodeLength'] - 60 ) ) {
			// 		// Select the episode to continue watching when artwork is clicked.
			// 		foreach ( $franchise->seasons as $season ) {
			// 			foreach ( $season->episodes as $key_episode => $episode ) {
			// 				if ( $episode->id === $stream_position_keys[0] ) {
			// 					$episode_number    = apply_filters( 'atv_get_episode_number', $episode, ( $key_episode + 1 ) );
			// 					$franchise_art_link = $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name );

			// 					$franchise_art_title = 'Click to continue watching ' . $season->name . ': Episode ' . $episode_number;
			// 					$break             = true;
			// 					break;
			// 				}
			// 			}
			// 			if ( $break ) {
			// 				break;
			// 			}
			// 		}
			// 	} else {
			// 		// Select the next episode to watch when artwork is clicked.
			// 		$is_next_episode = false;
			// 		foreach ( $franchise->seasons as $season ) {
			// 			foreach ( $season->episodes as $key_episode => $episode ) {
			// 				$episode_number          = apply_filters( 'atv_get_episode_number', $episode, ( $key_episode + 1 ) );
			// 				$is_set_position_and_length = isset( $stream_positions[ $episode->id ], $stream_positions[ $episode->id ]['Position'], $stream_positions[ $episode->id ]['EpisodeLength'] );
			// 				$isResume               = ( ! isset( $stream_positions[ $episode->id ] ) || ( $is_set_position_and_length && $stream_positions[ $episode->id ]['Position'] < ( $stream_positions[ $episode->id ]['EpisodeLength'] - 60 ) ) );
			// 				if ( $is_next_episode && $isResume ) {
			// 					$franchise_art_link  = $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name );
			// 					$franchise_art_title = 'Click to watch ' . $season->name . ': Episode ' . $episode_number;
			// 					$break             = true;
			// 					break;
			// 				}
			// 				if ( ! $is_next_episode ) {
			// 					$is_next_episode = ( $episode->id === $stream_position_keys[0] );
			// 				}
			// 			}
			// 			if ( $break ) {
			// 				break;
			// 			}
			// 		}
			// 	}
			// } elseif ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) {
			// 	$franchise_art_link  = $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $franchise->seasons[0]->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $franchise->seasons[0]->episodes[0]->name );
			// 	$franchise_art_title = 'Click to watch the first episode';
			// }
			?>
<div id="franchise-container" itemscope itemtype="http://schema.org/TVSeries">
	<div class="secondary-bg">
		<div class="container franchise">
			<h4 class="subnav">
				<span class="subnav-prev hidden-xs hidden-sm">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span>Back to Home</span>
					</a>
				</span>
				<span itemprop="name"><?php echo $franchise->name; ?></span>
				<meta itemprop="numberOfEpisodes" content="<?php echo $total_episodes; ?>" />
				<meta itemprop="numberOfSeasons" content="<?php echo count( $franchise->seasons ); ?>" />
				<span  class="subnav-next hidden-xs hidden-sm">
					<?php if ( ! empty( $first_episode_link ) ) : ?>
					<a href="<?php echo esc_url( trailingslashit( $first_episode_link ) ); ?>">
						<span>Watch Episode</span>
					</a>
					<?php endif; ?>
				</span>
			</h4>
			<div class="row" >
				<?php require_once plugin_dir_path( __FILE__ ) . '../partials/franchises/artwork.php'; ?>
				<?php require_once plugin_dir_path( __FILE__ ) . '../partials/franchises/synopsis.php'; ?>
			</div>
		</div>
	</div>

			<?php
			if ( $have_franchises_available ) :
				if ( ! empty( $stream_positions ) ) :
					?>
	<!-- Continue Watching  -->
	<div class="container">
		<?php
		// set_query_var( 'continueWatchingItems', $franchise->seasons );
		// set_query_var( 'totalEpisodes', $total_episodes );
		// set_query_var( 'streamposition', $stream_positions );
		// get_template_part( 'partials/continue-watching-carousel' );
		$continue_watching_items = $franchise->seasons;// get_query_var('continueWatchingItems');
		$total_episodes          = $total_episodes;// get_query_var('totalEpisodes');
		// $stream_positions        = $stream_positions;// get_query_var('streamposition');
		if ( ! empty( $continue_watching_items ) && count( $continue_watching_items ) > 0 )  {
			require_once plugin_dir_path( __FILE__ ) . '../partials/shared/continue-watching-carousel.php';
		}
		?>
	</div>
					<?php
				endif;
				?>
	<div class="container episode">

		<!-- Seasons and Episodes -->
				<?php
				foreach ( $franchise->seasons as $season_key => $season ) {
					$season->seasonNumber = $season_key + 1;
					// set_query_var( 'season', $season );
					// if ( isset( $stream_positions ) ) {
					// 	set_query_var( 'streamPositions', $stream_positions );
					// }
					// get_template_part( 'partials/list-episode-items' );
					require plugin_dir_path( __FILE__ ) . '../partials/shared/list-episode-items.php';
				}
				?>

		<!-- Viewers Also Watched -->
				<?php
				// THIS DID NOTHING. WHY IS IT HERE.
				// set_query_var( 'also_watched_items', rljeApiWP_getViewersAlsoWatched( $franchise_id ) );
				// get_template_part( 'partials/viewers-also-watched' );
				?>
	</div>
				<?php
		else :
				get_template_part( 'partials/franchises-unavailable-message' );
		endif;
		?>
</div>
			<?php
			get_footer();
		// else :
		// 	require_once get_404_template();
		// endif;
	// else :
	// 	get_header();
	// 	get_template_part( 'partials/plugin-deactivated-message' );
	// 	get_footer();
	// endif;

