<?php
global $wp_query;
$ignore_season_header = false;

// if ( isset( $wp_query->query_vars['streamPositions'] ) ) {
// $stream_positions = $wp_query->query_vars['streamPositions'];
// }
// SEASON SPECIFIC VARIABLE.
// if ( isset( $wp_query->query_vars['ignoreSeasonHeader'] ) ) {
// $ignore_season_header = $wp_query->query_vars['ignoreSeasonHeader'];
// }
// $franchise_id = $wp_query->query_vars['franchiseId'];
// $season      = $wp_query->query_vars['season'];
$template = get_query_var( 'pagecustom' );

$highlight_templates_enabled = array(
	'franchise' => true,
	'episode'   => true,
);

$count = 0;
// SEASON SPECIFIC VARIABLE.
// $franchise_name         = ( isset( $wp_query->query_vars['franchiseName'] ) ) ? $wp_query->query_vars['franchiseName'] : null;
$franchise_name  = ( isset( $franchise->name ) ) ? $franchise->name : null;
$franchise_total = count( $season->episodes ) - 1;
// $franchise_id           = ( isset( $wp_query->query_vars['franchise_id'] ) ) ? '/' . $wp_query->query_vars['franchise_id'] : null;
$season_name_url = ( isset( $franchise_id, $wp_query->query_vars['season_name'] ) ) ? '/' . $wp_query->query_vars['season_name'] : '/' . rljeApiWP_convertSeasonNameToURL( $season->name );
// $base_url_path           = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
$base_url_path           = home_url( $franchise_id . $season_name_url . '/' );
$is_highligthing_episode = false;
$is_logged               = isset( $_COOKIE['ATVSessionCookie'] );
$is_highligthing_enabled = ! empty( $highlight_templates_enabled[ $template ] );
$is_stream_position      = ! isset( $stream_positions );
$is_first_seasson        = ( isset( $season->seasonNumber ) && 1 == $season->seasonNumber );

if ( $is_logged && $is_highligthing_enabled && $is_stream_position && $is_first_seasson ) {
	$is_highligthing_episode = true;
}

?>
<span itemprop="containsSeason" itemscope itemtype="http://schema.org/TVSeason">
	<meta itemprop="name" content="<?php echo $season->name; ?>" />
	<meta itemprop="numberOfEpisodes" content="<?php echo count( $season->episodes ); ?>" />
	<meta itemprop="seasonNumber" content="<?php echo ( isset( $season->seasonNumber ) ) ? $season->seasonNumber : ''; ?>" />
	<meta itemprop="partOfSeries" content="<?php echo $franchise_name; ?>"/>
	<?php if ( ! empty( $ignore_season_header ) ) : ?>
	<div class="row">
		<h4 class="subnav2"><?php echo $season->name; ?></h4>
	</div>
	<?php endif; ?>
<?php
foreach ( $season->episodes as $key => $episode ) :
	$show_episode_highlighted = ( $is_highligthing_episode && 0 == $key );
	$is_new_row               = ( $key % 4 == 0 ) ? true : false;
	$count++;
	$episode_number = apply_filters( 'atv_get_episode_number', $episode, ( $key + 1 ) );
	if ( $is_new_row ) :
		$count = 0;
		?>
	<div class="row" style="margin-bottom:15px;">
<?php endif; ?>
		<span itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
			<div class="col-sm-6 col-md-3<?php echo ( $show_episode_highlighted ) ? ' highlight-episode' : ''; ?>" style="padding-top:25px;">
				<a itemprop="url" href="<?php echo esc_url( $base_url_path . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) . '/' ); ?>">
					<img src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>" id="play-episodes" />
					<img itemprop="image" width="100%" src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?w=500' ); ?>" />
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
					<meta itemprop="timeRequired" content="<?php echo ( ! empty( $episode->length ) ) ? 'T' . str_replace( ':', 'M', rljeApiWP_convertSecondsToMinSecs( $episode->length ) ) . 'S' : ''; ?>" />
					<div class="franchise-eps-bg<?php echo ( $show_episode_highlighted ) ? ' no-margin-bottom' : ''; ?>">
						<h5 itemprop="name"><?php echo esc_html( $episode->name ); ?></h5>
						<?php
						if ( 'movie' === strtolower( $episode->type ) ) {
							$episode_type_display = ' Movie ';
						} else {
							$episode_type_display = $season->name . ': Episode <span itemprop="episodeNumber">' . $episode_number . '</span>';
						}
						?>
						<h6><?php echo $episode_type_display; ?></h6>
					</div>
					<?php if ( $show_episode_highlighted ) : ?>
					<div class="continueWatching">
						<button class="js-play-resume">
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
					</div>
					<?php endif; ?>
				</a>
			</div>
		</span>
	<?php if ( $count == 3 || ( $key == $franchise_total ) ) : ?>
	</div>
		<?php
	endif;
endforeach;
?>
</span>
