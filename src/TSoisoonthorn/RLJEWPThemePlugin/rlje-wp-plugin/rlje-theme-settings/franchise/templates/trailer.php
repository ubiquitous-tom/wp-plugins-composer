<?php
get_header();

	$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'franchise' );
	if ( $have_franchises_available ) :
		$environment = apply_filters( 'atv_get_extenal_subdomain', '' );

		// $franchise_id = get_query_var( 'franchise_id' );

		$franchise = rljeApiWP_getFranchiseById( $franchise_id );

		$stream_positions = null;

		if ( isset( $franchise->id ) ) :
			if ( isset( $_COOKIE['ATVSessionCookie'] ) && rljeApiWP_isUserActive( $_COOKIE['ATVSessionCookie'] ) ) {
				$get_stream_positions = rljeApiWP_getStreamPositionsByFranchise( $franchise_id, $_COOKIE['ATVSessionCookie'] );
				if ( isset( $get_stream_positions->streamPositions ) ) {
					$stream_positions = [];
					foreach ( $get_stream_positions->streamPositions as $stream_position ) {
						$stream_positions[ $stream_position->EpisodeID ] = [
							'Position'      => $stream_position->Position,
							'EpisodeLength' => $stream_position->EpisodeLength,
						];
					}
				}
			}
			$total_episodes = 0;
			foreach ( $franchise->seasons as $season_item ) {
				$total_episodes += count( $season_item->episodes );
			}
			?>

<div class="secondary-bg">
	<div class="container franchise">
		<div class="col-md-12" itemscope itemtype="http://schema.org/TVSeries">
			<h4 class="subnav">
				<!-- Previous link -->
				<span class="subnav-prev hidden-xs hidden-sm">
					<a href="<?php echo esc_url( trailingslashit( home_url( $franchise->id ) ) ); ?>">
						<span>Back <!-- to Series--></span>
					</a>
				</span>
				<a href="<?php echo esc_url( trailingslashit( home_url( $franchise->id ) ) ); ?>" id="subnav-title"><span itemprop="name"><?php echo $franchise->name; ?></span></a> Trailer   <!-- Next link -->
				<meta itemprop="image" content="<?php echo apply_filters( 'atv_get_image_url', $franchise->image ); ?>" />
				<meta itemprop="description" content="<?php echo $franchise->longDescription; ?>" />
				<meta itemprop="numberOfEpisodes" content="<?php echo $total_episodes; ?>" />
				<meta itemprop="numberOfSeasons" content="<?php echo count( $franchise->seasons ); ?>" />

				<span class="subnav-next hidden-xs hidden-sm">
					<?php if ( isset( $franchise->seasons[0], $franchise->seasons[0]->episodes[0] ) ) : ?>
					<a href="<?php echo esc_url( trailingslashit( home_url( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $franchise->seasons[0]->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $franchise->seasons[0]->episodes[0]->name ) ) ) ); ?>">
						<span>Watch Episode</span>
					</a>
					<?php endif; ?>
				</span>
			</h4>
			<!-- Brightcove Episode Player -->
			<div class="outer-container episode-player">
				<?php
				if ( isset( $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) :
					$trailerId = $franchise->episodes[0]->id;
					?>
				<span itemprop="trailer" itemscope itemtype="http://schema.org/VideoObject">
					<?php $franchise->image = apply_filters( 'rlje_franchise_artwork', $franchise->image, $franchise ); ?>
					<meta itemprop="thumbnailUrl" content="<?php echo apply_filters( 'atv_get_image_url', $franchise->image . '?w=750' ); ?>" />
					<meta itemprop="description" content="<?php echo $franchise->longDescription; ?>" />
					<meta itemprop="name" content="<?php echo $franchise->name; ?>" />
					<meta itemprop="uploadDate" content="<?php echo ( isset( $franchise->episodes[0]->startDate ) && $franchise->episodes[0]->startDate != '' ) ? date( 'Y-m-d', $franchise->episodes[0]->startDate ) : ''; ?>" />
					<!-- <div id="trailer-video" class="video" data-embedcode="<iframe style='border:none;z-index:4' src='//players.brightcove.net/3392051363001/0066661d-8f08-4e7b-a5b4-8d48755a3057_default/index.html?videoId=<?php echo $trailerId; ?>'
						allowfullscreen
						webkitallowfullscreen
						mozallowfullscreen></iframe>">
						<img title="image title" alt="thumb image" class="wp-post-image" src="<?php echo apply_filters( 'atv_get_image_url', $franchise->image . '?w=750' ); ?>"/>

						<script>
						(function() {

							var video = document.querySelector('#trailer-video');
							var embedcode = video.dataset.embedcode;
							var hasvideo = video.dataset.hasvideo;

							// Only append the video if it isn't yet appended.
							if (!hasvideo) {
								video.insertAdjacentHTML('afterbegin', embedcode);
								video.dataset.hasvideo = "true";
							}
						})();
						</script>
					</div> -->
					<div id="trailer-video" class="video">
						<video preload
							id="brightcove-trailer-player"
							data-account="3392051363001"
							data-player="0066661d-8f08-4e7b-a5b4-8d48755a3057"
							data-embed="default"
							data-video-id="ref:<?php echo $trailerId; ?>"
							poster="<?php echo esc_url( apply_filters( 'atv_get_image_url', $franchise->image . '?w=750' ) ); ?>"
							class="video-js embed-responsive embed-responsive-16by9"
							controls></video>
					</div>
				</span>
					<?php
				else :
					$rlje_theme_text_settings = get_option( 'rlje_theme_text_settings' );
					$video_placeholder = ( ! empty( $rlje_theme_text_settings['video_placeholder'] ) ) ? $rlje_theme_text_settings['video_placeholder'] : array();
					$video_placeholder_title_text = ( ! empty( $video_placeholder['title_text'] ) ) ? $video_placeholder['title_text'] : 'Watch world-class TV from Britain and beyond';
					$video_placeholder_text = ( ! empty( $video_placeholder['text'] ) ) ? $video_placeholder['text'] : 'Always available, always commercial free';
					?>
				<img title="Play trailer" class="wp-post-image" src="<?php echo esc_url( apply_filters( 'atv_get_image_url', $franchise->image . '?w=750' ) ); ?>"/>
				<div class="acorntv-slogan">
					<!-- <h3>Watch world-class TV from Britain and beyond</h3> -->
					<!-- <h4>Always available, always commercial free</h4> -->
					<h3><?php echo esc_html( $video_placeholder_title_text ); ?></h3>
					<h4><?php echo esc_html( $video_placeholder_text ); ?></h4>
					<a class="btn btn-primary" href="<?php echo home_url('/signup') ?>">Start Free Trial</a>
				</div>
					<?php
				endif;
				?>
			</div>
		</div>
	</div>
</div>

<!-- Episode content begins (descriptions, tags, more episodes, and related titles) -->
<div class="container episode">
	<!-- More Episodes Carousel -->
	<!-- Multiple carousels. If there is more than four episodes use bootstrap carousel-->

	<div class="col-md-12">
		<h4 class="subnav2" >Episodes</h4>
			<?php
			// $wp_query->query_vars['franchiseName'] = $franchise->name;
			$franchise_name      = $franchise->name;
			$isLessThan4Episodes = apply_filters( 'atv_is_less_than_4_episodes', $franchise->seasons );

			if ( $isLessThan4Episodes ) {
				// $wp_query->query_vars['season']             = $franchise->seasons[0];
				$season = $franchise->seasons[0];
				// $wp_query->query_vars['ignoreSeasonHeader'] = true;
				$ignore_season_header = true;
				// get_template_part( 'partials/list-episode-items' );
				require_once plugin_dir_path( __FILE__ ) . '../partials/shared/list-episode-items.php';
			} else {
				// $wp_query->query_vars['current_episode_id'] = $franchise->seasons[0]->episodes[0]->id;
				// $wp_query->query_vars['seasons_carousel']   = $franchise->seasons;
				// $wp_query->query_vars['streamPositions']    = $stream_positions;
				$current_episode_number =  $franchise->seasons[0]->episodes[0]->id;
				$seasons                = $franchise->seasons;
				// get_template_part( 'partials/more-episodes-carousel' );
				require_once plugin_dir_path( __FILE__ ) . '../partials/shared/more-episodes-carousel.php';
			}
			?>
	</div>

	<!-- Multiple carousels. If there is less than four episodes display basic column grid-->


	<!-- You May Also Like Carousel -->
			<?php
			// THIS DID NOTHING. WHY IS IT HERE.
			// $wp_query->query_vars['also_watched_items'] = rljeApiWP_getViewersAlsoWatched( $franchise_id );
			// get_template_part( 'partials/viewers-also-watched' );
			?>
</div>
			<?php
		else :
			get_template_part( 'partials/no-result-message' );
		endif;
	else :
		get_template_part( 'templates/franchisesUnavailable' );
	endif;
get_footer();
