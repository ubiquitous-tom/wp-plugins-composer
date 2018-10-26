<?php

$filter_by_text = apply_filters( 'rlje_seasons_dropdown_filter_by_text', 'Filter By Series' );

if ( ! empty( $franchise->seasons ) ) {
	$seasons = $franchise->seasons;
}

$seasons_count = count( $seasons );
if ( $seasons_count > 1 ) :
	?>
<!-- Drop Down Series Filter -->
<div id="cover">
	<div id="options">
		<a><?php echo esc_html( ( isset( $season_name ) ) ? $season_name : $filter_by_text ); ?></a>
		<span id="clicker"></span>
	</div>
	<ul id="drop-select" class="closed">
		<?php for ( $i = 0; $i < $seasons_count; $i++ ) : ?>
		<li>
			<a href="<?php echo esc_url( home_url( trailingslashit( $franchise_id . '/' . rljeApiWP_convertSeasonNameToURL( $seasons[ $i ]->name ) ) ) ); ?>">
				<?php echo $seasons[ $i ]->name; ?>
			</a>
		</li>
		<?php endfor; ?>
		<li>
			<a href="<?php echo esc_url( home_url( trailingslashit( $franchise_id ) ) ); ?>">View All</a>
		</li>
	</ul>
</div>

	<?php
endif;
