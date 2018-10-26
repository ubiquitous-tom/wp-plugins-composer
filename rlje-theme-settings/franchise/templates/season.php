<?php
// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
// if ( function_exists( 'rljeApiWP_getFranchiseById' ) ) :

	// $franchise_id   = get_query_var( 'franchise_id' );
	// $season_name_url = get_query_var( 'season_name' );

	$franchise = rljeApiWP_getFranchiseById( $franchise_id );
	$season    = rljeApiWP_getCurrentSeason( $franchise_id, $season_name_url );

	if ( isset( $season->id ) ) :
		get_header();
		?>
<div class="secondary-bg" style="padding-bottom:50px;">
	<div class="container franchise">
		<h4 class="subnav">
			<span class="subnav-prev hidden-xs hidden-sm">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span>Back to Home</span>
				</a>
			</span>
			<span><?php echo esc_html( $franchise->name ); ?></span>
			<span class="subnav-next hidden-xs hidden-sm">
				<a href="<?php echo esc_url( trailingslashit( home_url( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $franchise->seasons[0]->name ) . '/' . rljeApiWP_convertEpisodeNameToURLFriendly( $franchise->seasons[0]->episodes[0]->name ) ) ) ); ?>">
					<span>Watch Episode</span>
				</a>
			</span>
		</h4>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<?php if ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) : ?>
				<a href="<?php echo esc_url( home_url( $franchise->id . '/trailer/' ) ); ?>">
					<img class="wp-post-image" id="franchise-avatar" title="Clicks to view trailer" src="<?php echo esc_url( rljeApiWP_getImageUrlFromServices( $season->image . '?w=460' ) ); ?>" />
				</a>
				<?php else : ?>
				<img class="wp-post-image" id="franchise-avatar" src="<?php echo esc_url( rljeApiWP_getImageUrlFromServices( $season->image . '?w=460' ) ); ?>" />
				<?php endif; ?>
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-8">
				<p id="franchise-description"><?php echo esc_html( $franchise->longDescription ); ?></p>
				<?php if ( isset( $franchise->episodes[0], $franchise->episodes[0]->id ) && ( ! empty( $franchise->episodes[0]->id ) ) ) : ?>
				<a id="inline" class="view-trailer" href="<?php echo esc_url( home_url( $franchise->id . '/trailer/' ) ); ?>">
					<button>View Trailer</button>
				</a>
					<?php
					endif;
					// set_query_var( 'seasonName', $season->name );
					// set_query_var( 'seasons', $franchise->seasons );
					$season_name = $season->name;
					// get_template_part( 'partials/seasons-dropdown' );
					require_once plugin_dir_path( __FILE__ ) . '../partials/shared/seasons-dropdown.php';
?>
			</div>
		</div>
	</div>
</div>

<div class="container episode">
		<?php
		// set_query_var( 'season', $season );
		// set_query_var( 'franchiseName', $franchise->name );
		$franchise_name = $franchise->name;
		// get_template_part( 'partials/list-episode-items' );
		require_once plugin_dir_path( __FILE__ ) . '../partials/shared/list-episode-items.php';
		?>

	<span style="padding-right:5px;"> Filter By Series:</span>
	<?php for ( $i = 0; $i < count( $franchise->seasons ); $i++ ) : ?>
	<a href="/<?php echo $franchise_id; ?>/<?php echo rljeApiWP_convertSeasonNameToURL( $franchise->seasons[ $i ]->name ); ?>/"> <button><?php echo $i + 1; ?></button></a>
	<?php endfor; ?>
	<a href="/<?php echo $franchise_id; ?>/"> <button>View All</button></a>
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
