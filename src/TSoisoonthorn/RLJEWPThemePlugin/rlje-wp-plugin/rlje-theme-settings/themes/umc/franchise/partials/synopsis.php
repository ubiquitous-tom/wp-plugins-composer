<?php
$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'franchise' );
?>
<div id="fanchise-synopsis" class="col-xs-12 col-sm-6 col-lg-4" >
	<h2>
		<span itemprop="name"><?php echo $franchise->name; ?></span>
		<meta itemprop="numberOfEpisodes" content="<?php echo $total_episodes; ?>" />
		<meta itemprop="numberOfSeasons" content="<?php echo count( $franchise->seasons ); ?>" />
	</h2>
	<p id="franchise-description" itemprop="description"><?php echo esc_html( $franchise->longDescription ); ?></p>
	<?php if ( $this->is_user_active() && $have_franchises_available ) : ?>
		<?php if ( ! rljeApiWP_isFranchiseAddedToWatchlist( $franchise->id, $_COOKIE['ATVSessionCookie'] ) ) : ?>
		<a id="watchlist-button" class="inline"><button id="watchlistActionButton" data-action="add">Add to Watchlist</button></a>
		<?php else : ?>
		<a id="watchlist-button" class="inline"><button id="watchlistActionButton" data-action="remove">Remove from Watchlist</button></a>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $this->is_trailer_available( $franchise ) ) : ?>
	<span itemprop="trailer" itemscope itemtype="http://schema.org/VideoObject">
		<meta itemprop="thumbnailUrl" content="<?php echo esc_url( rljeApiWP_getImageUrlFromServices( $franchise->image .'?w=750' ) ); ?>" />
		<meta itemprop="description" content="<?php echo esc_html( $franchise->longDescription ); ?>" />
		<meta itemprop="name" content="<?php echo esc_html( $franchise->name ); ?>" />
		<meta itemprop="uploadDate" content="<?php echo ( isset( $franchise->episodes[0]->startDate ) && $franchise->episodes[0]->startDate != '' ) ? date( 'Y-m-d', $franchise->episodes[0]->startDate ) : ''; ?>" />
		<a class="inline" href="<?php echo $franchise_url . '/trailer/'; ?>">
			<button>View Trailer</button>
		</a>
	</span>
	<?php endif; ?>

	<?php if ( ! empty( $first_episode_link ) ) : ?>
	<a class="inline" href="<?php echo esc_url( trailingslashit( $first_episode_link ) ); ?>">
		<button>Watch Episode</button>
	</a>
	<?php endif; ?>

	<?php require_once plugin_dir_path( __FILE__ ) . '../partials/seasons-dropdown.php'; ?>

	<?php if ( ! empty( $franchise->actors ) ) : ?>
	<p id="franchise-actor" itemprop="actor">
		<strong>Cast:</strong>
		<?php echo esc_html( join( ', ', $franchise->actors ) ); ?>
	</p>
	<?php endif; ?>

	<?php if ( ! empty( $franchise->director ) ) : ?>
	<p id="franchise-director" itemprop="director">
		<strong>Director:</strong>
		<?php echo esc_html( join( ', ', $franchise->director ) ); ?>
	</p>
	<?php endif; ?>
</div>
