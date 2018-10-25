<?php
get_header();
$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'franchise' );
$franchise_url             = home_url( $franchise->id );
$franchise_wallpaper       = rljeApiWP_getImageUrlFromServices( $franchise->image . '?t=franchise-wallpaper' );
?>
<style>
#franchise-container .secondary-bg {
	background-image: linear-gradient(to top, rgba(1, 31, 54, 1) 10%, rgba(0, 0, 0, 0) 70%), url('<?php echo esc_url( $franchise_wallpaper ); ?>');
}
</style>
<div id="franchise-container" itemscope itemtype="http://schema.org/TVSeries">
	<div class="secondary-bg">
		<div class="container franchise">
			<!-- <p id="franchise-cast" itemprop="cast"><?php echo esc_html( $franchise->longDescription ); ?></p> -->
			<h4 class="subnav hidden">
				<span class="subnav-prev hidden-xs hidden-sm">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span>Back to Home</span>
					</a>
				</span>
				<span itemprop="name"><?php echo esc_html( $franchise->name ); ?></span>
				<meta itemprop="numberOfEpisodes" content="<?php echo esc_html( $total_episodes ); ?>" />
				<meta itemprop="numberOfSeasons" content="<?php echo count( $franchise->seasons ); ?>" />
				<span class="subnav-next hidden-xs hidden-sm">
					<?php if ( ! empty( $first_episode_link ) ) : ?>
					<a href="<?php echo esc_url( trailingslashit( $first_episode_link ) ); ?>">
						<span>Watch Episode</span>
					</a>
					<?php endif; ?>
				</span>
			</h4>
			<div class="row">
				<?php require_once plugin_dir_path( __FILE__ ) . '../partials/synopsis.php'; ?>
			</div>
		</div>
	</div>

	<?php
	if ( $have_franchises_available ) :
		if ( ! empty( $stream_positions ) ) :
			?>
	<!-- Continue Watching  -->
	<div class="container">
		<?php require_once plugin_dir_path( __FILE__ ) . '../partials/continue-watching-carousel.php'; ?>
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
			// set_query_var( 'streamPositions', $stream_positions );
			// }
			// get_template_part( 'partials/list-episode-items' );
			require plugin_dir_path( __FILE__ ) . '../partials/list-episode-items.php';
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


