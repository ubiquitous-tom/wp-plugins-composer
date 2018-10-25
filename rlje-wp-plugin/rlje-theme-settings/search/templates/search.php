<?php
$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'search' );
if ( $have_franchises_available ) :
	$search_title = $search_query;
	// HACK: Search API breaks with single characters in search terms so take it out.
	$search_query = trim( preg_replace( [ '/^\w{1} /', '/ \w{1} /', '/ \w{1}$/' ], " ", $search_query ) );
	get_header();
	$search_by_franchises_result = null;
	$search_by_episodes_result   = null;
	$total_franchises_result     = 0;
	$limit_character             = array(
		'number' => 2,
		'text'   => 'two',
	);

	if ( function_exists( 'rljeApiWP_searchByFranchises' ) ) {
		$have_min_character = ( strlen( $search_query ) < $limit_character['number'] );
		if ( ! $have_min_character || strtolower( $search_query ) === 'qi' ) { // Qi is a franchise's name.
			$search_by_franchises_result = rljeApiWP_searchByFranchises( $search_query );
			$search_by_episodes_result   = rljeApiWP_searchByEpisodes( $search_query );
			$total_franchises_result     = ( isset( $search_by_franchises_result->franchises ) ) ? count( $search_by_franchises_result->franchises ) : 0;
			$total_episodes_result       = ( isset( $search_by_episodes_result->episodes ) ) ? count( $search_by_episodes_result->episodes ) : 0;
			$show_franchises_carousel    = ( 4 < $total_franchises_result );
			$show_episodes_carousel      = ( 4 < $total_episodes_result );
			if ( $total_franchises_result > 0 ) :
				?>
<section class="search">
	<div class="container">
		<h4 class="subnav">Franchise Results for <?php echo $search_title; ?></h4>
		<div class="<?php echo ( $show_franchises_carousel ) ? ' hidden-lg' : ''; ?>">
				<?php
				foreach ( $search_by_franchises_result->franchises as $key => $franchise ) :
					if ( $key % 4 == 0 ) :
						?>
			<div class="row">
						<?php
					endif;
					?>
				<div class="col-sm-6 col-md-6 col-lg-3">
					<a href="<?php echo esc_url( trailingslashit( home_url( $franchise->id ) ) ); ?>">
						<?php $franchise->image = apply_filters( 'rlje_franchise_artwork', $franchise->image, $franchise ); ?>
						<img title="<?php echo $franchise->name; ?>" alt="thumb franchise image" class="wp-post-image" src="<?php echo apply_filters( 'atv_get_image_url', $franchise->image . '?t=titled-avatars&w=550' ); ?>" style="width:100%; height:auto; ">
					</a>
				</div>
					<?php
					if ( ( $key + 1 ) % 4 == 0 || $key == $total_franchises_result - 1 ) :
						?>
			</div>
						<?php
					endif;
				endforeach;
				?>
		</div>
				<?php if ( $show_franchises_carousel ) : ?>
		<div class="carousel carousel-block-slide slide visible-lg" id="<?php echo $section_key = 'franchises-results'; ?>" data-interval="false" data-wrap="false">
			<div class="row">
				<div class="carousel-inner">
					<?php
						$franchises_items        = apply_filters( 'atv_get_completed_carousel_items', $search_by_franchises_result->franchises );
						$total_franchises_result = count( $franchises_items );
					foreach ( $franchises_items as $key => $franchise ) :
						if ( $key % 4 == 0 ) :
							?>
					<div class="item <?php echo ( $key == 0 ) ? 'active' : ''; ?>">
							<?php
							endif;
						?>
						<div class="col-sm-6 col-md-6 col-lg-3">
							<a href="<?php echo esc_url( trailingslashit( home_url( $franchise->id ) ) ); ?>">
								<?php $franchise->image = apply_filters( 'rlje_franchise_artwork', $franchise->image, $franchise ); ?>
								<img title="<?php echo $franchise->name; ?>" alt="thumb franchise image" class="wp-post-image" src="<?php echo apply_filters( 'atv_get_image_url', $franchise->image . '?t=titled-avatars&w=550' ); ?>" style="width:100%; height:auto; ">
							</a>
						</div>
						<?php
						if ( ( $key + 1 ) % 4 == 0 || $key == $total_franchises_result - 1 ) :
							?>
					</div>
							<?php
							endif;
						endforeach;
					?>
				</div>
			</div>
			<a class="left carousel-control" href="#<?php echo $section_key; ?>" id="carousel-arrow" data-slide="prev"></a>
			<a class="right carousel-control" href="#<?php echo $section_key; ?>" id="carousel-arrow" data-slide="next"></a>
		</div>
		<?php endif; ?>
	</div>
</section>
				<?php
		endif;
			if ( $total_episodes_result > 0 ) {
				?>
<section class="search-episode">
	<div class="container">
		<h4 class="subnav">Episode Results for <?php echo $search_title; ?></h4>
		<div class="<?php echo ( $show_episodes_carousel ) ? ' hidden-lg' : ''; ?>">
				<?php
				foreach ( $search_by_episodes_result->episodes as $key => $episode ) :
					if ( ! empty( $episode->image ) ) { // Workaround to get episode number
						preg_match( '/.+ep([\d]{2}).+/i', $episode->image, $episode_number );
					}
					$episode_number = apply_filters( 'atv_get_episode_number', $episode, (int) $episode_number[1] );
					if ( $key % 4 == 0 ) :
						?>
			<div class="row">
						<?php
					endif;
					?>
				<a href="<?php echo esc_url( trailingslashit( home_url( $episode->franchiseId . '/' . rljeApiWP_convertSeasonNameToURL( $episode->seriesName ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) ) ) ); ?>">
					<div class="col-sm-6 col-md-3" style="margin-top:15px;">
						<img id="play-episodes" src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>"/>
						<img width="100%" title="<?php echo $episode->name; ?>" alt="thumb episode image"  src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?t=titled-avatar&w=500' ); ?>"/>
						<div class="franchise-eps-bg">
							<h4 class="text-center"><?php echo $episode->franchiseName; ?></h4>
							<h5><?php echo $episode->name; ?></h5>
							<h6>
							<?php
							echo ( isset( $episode->seriesName ) ) ? $episode->seriesName : '';
							echo ( ! empty( $episode_number ) ) ? ': Episode ' . $episode_number : '';
							?>
							</h6>
						</div>
					</div>
				</a>
					<?php
					if ( ( $key + 1 ) % 4 == 0 || $key == $total_episodes_result - 1 ) :
						?>
			</div>
						<?php
					endif;
					endforeach;
				?>
		</div>
				<?php if ( $show_episodes_carousel ) : ?>
		<div class="carousel carousel-block-slide slide visible-lg" id="<?php echo $section_key = 'episodes-results'; ?>" data-interval="false" data-wrap="false">
			<div class="row">
				<div class="carousel-inner">
					<?php
						$Episodes_items        = apply_filters( 'atv_get_completed_carousel_items', $search_by_episodes_result->episodes );
						$total_episodes_result = count( $Episodes_items );
					foreach ( $Episodes_items as $key => $episode ) :
						if ( ! empty( $episode->image ) ) { // Workaround to get episode number
							preg_match( '/.+ep([\d]{2}).+/i', $episode->image, $episode_number );
						}
						$episode_number = apply_filters( 'atv_get_episode_number', $episode, (int) $episode_number[1] );
						if ( $key % 4 == 0 ) :
							?>
					<div class="item <?php echo ( $key == 0 ) ? 'active' : ''; ?>">
							<?php
							endif;
						?>
						<a href="<?php echo esc_url( trailingslashit( home_url( $episode->franchiseId . '/' . rljeApiWP_convertSeasonNameToURL( $episode->seriesName ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $episode->name ) ) ) ); ?>">
							<div class="col-xs-12 col-sm-6 col-md-3" style="margin-top:15px;">
								<img id="play-episodes" src="<?php echo apply_filters( 'atv_get_image_url', 'play-icon?t=Icons' ); ?>"/>
								<img width="100%" title="<?php echo $episode->name; ?>" alt="thumb episode image"  src="<?php echo apply_filters( 'atv_get_image_url', $episode->image . '?t=titled-avatar&w=500' ); ?>"/>
								<div class="franchise-eps-bg">
									<h4 class="text-center"><?php echo $episode->franchiseName; ?></h4>
									<h5><?php echo $episode->name; ?></h5>
									<h6>
									<?php
									echo ( isset( $episode->seriesName ) ) ? $episode->seriesName : '';
									echo ( ! empty( $episode_number ) ) ? ': Episode ' . $episode_number : '';
									?>
									</h6>
								</div>
							</div>
						</a>
						<?php
						if ( ( $key + 1 ) % 4 == 0 || $key == $total_episodes_result - 1 ) :
							?>
					</div>
							<?php
							endif;
						endforeach;
					?>
				</div>
			</div>
			<a class="left carousel-control" href="#<?php echo $section_key; ?>" id="carousel-arrow" data-slide="prev"></a>
			<a class="right carousel-control" href="#<?php echo $section_key; ?>" id="carousel-arrow" data-slide="next"></a>
		</div>
		<?php endif; ?>
	</div>
</section>
				<?php
			}
			if ( $total_episodes_result == 0 && $total_franchises_result == 0 ) {
				showMessage( 'Your search did not match any shows. Please try again.' );
			}
		} else {
			showMessage( 'Please enter at least <b>' . $limit_character['text'] . '</b> characters.' );
		}
	} else {
		get_template_part( 'partials/plugin-deactivated-message' );
	}
	get_footer();
else :
	get_template_part( 'templates/franchisesUnavailable' );
endif;

function showMessage( $message ) {
	?>
<section class="search">
	<div class="container">
		<div class="row">
			<h4 class="subnav">
				<?php echo $message; ?>
			</h4>
		</div>
	</div>
</section>
	<?php
}
