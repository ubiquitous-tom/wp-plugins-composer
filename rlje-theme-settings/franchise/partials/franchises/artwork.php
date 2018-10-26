<?php
// CHECK FOR FRANCHISE TRAILER
// Legacy Code no longer used. now trailer link is in episode when not logged in
// if ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) {
// $franchise_art_link  = trailingslashit( $franchise_url . 'trailer' );
// $franchise_art_title = 'Click to view trailer';
// }
// CHECK FOR STREAM POSITIONS ONLY FOR LOGGEDIN USER
if ( isset( $stream_positions ) ) {
	$stream_position_keys = array_keys( $stream_positions );
	$stream_position_data = array_values( $stream_positions );
	$break                = false;
	if ( $stream_position_data[0]['Position'] < ( $stream_position_data[0]['EpisodeLength'] - 60 ) ) {
		// Select the episode to continue watching when artwork is clicked.
		foreach ( $franchise->seasons as $season ) {
			foreach ( $season->episodes as $key_episode => $episode ) {
				if ( $episode->id === $stream_position_keys[0] ) {
					$episode_number      = apply_filters( 'atv_get_episode_number', $episode, ( $key_episode + 1 ) );
					$franchise_art_link  = trailingslashit( $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) );
					$franchise_art_title = 'Click to continue watching ' . $season->name . ': Episode ' . $episode_number;
					$break               = true;
					break;
				}
			}
			if ( $break ) {
				break;
			}
		}
	} else {
		// Select the next episode to watch when artwork is clicked.
		$is_next_episode = false;
		foreach ( $franchise->seasons as $season ) {
			foreach ( $season->episodes as $key_episode => $episode ) {
				$episode_number             = apply_filters( 'atv_get_episode_number', $episode, ( $key_episode + 1 ) );
				$is_set_position_and_length = isset( $stream_positions[ $episode->id ], $stream_positions[ $episode->id ]['Position'], $stream_positions[ $episode->id ]['EpisodeLength'] );
				$isResume                   = ( ! isset( $stream_positions[ $episode->id ] ) || ( $is_set_position_and_length && $stream_positions[ $episode->id ]['Position'] < ( $stream_positions[ $episode->id ]['EpisodeLength'] - 60 ) ) );
				if ( $is_next_episode && $isResume ) {
					$franchise_art_link  = trailingslashit( $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $season->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) );
					$franchise_art_title = 'Click to watch ' . $season->name . ': Episode ' . $episode_number;
					$break               = true;
					break;
				}
				if ( ! $is_next_episode ) {
					$is_next_episode = ( $episode->id === $stream_position_keys[0] );
				}
			}
			if ( $break ) {
				break;
			}
		}
	}
} elseif ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) {
	$franchise_art_link  = trailingslashit( $franchise_url . '/' . rljeApiWP_convertSeasonNameToURL( $franchise->seasons[0]->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $franchise->seasons[0]->episodes[0]->name ) );
	$franchise_art_title = 'Click to watch the first episode';
}

$franchise_image     = apply_filters( 'rlje_franchise_artwork', $franchise->image, $franchise );
$franchise_image_url = rljeApiWP_getImageUrlFromServices( $franchise_image . '?w=460' );
?>
<div class="col-xs-12 col-sm-6 col-lg-4">
	<?php if ( ! empty( $franchise_art_link ) ) : ?>
	<a href="<?php echo esc_url( $franchise_art_link ); ?>" class="js-play-resume">
		<img itemprop="image" class="wp-post-image" id="franchise-avatar" title="<?php echo esc_html( $franchise_art_title ); ?>" src="<?php echo esc_url( $franchise_image_url ); ?>" />
	</a>
	<?php else : ?>
	<img itemprop="image" class="wp-post-image" id="franchise-avatar" src="<?php echo esc_url( $franchise_image_url ); ?>" />
	<?php endif; ?>
</div>
