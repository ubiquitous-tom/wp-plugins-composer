<?php
get_header();
$base_url_path   = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
// $active_section = get_query_var( 'section' );
// $list_sections  = array(
// 	'featured'    => 'Recently Added',
// 	'comingsoon'  => 'Coming Soon',
// 	'leavingsoon' => 'Leaving Soon',
// );
?>
<section class="container schedule">
	<?php require_once plugin_dir_path( __FILE__ ) . '../partials/navigation.php'; ?>
	<?php
	$have_franchises_available = apply_filters( 'atv_haveFranchisesAvailableByCountry', 'section' );
	if ( $have_franchises_available ) :
		$a_schedule_items = rljeApiWP_getScheduleItems( $active_section );
		if ( isset( $a_schedule_items ) && count( $a_schedule_items ) > 0 ) :
			$total_franchises_result = count( $a_schedule_items );
			foreach ( $a_schedule_items as $key => $item ) :
				$img = rljeApiWP_getImageUrlFromServices( $item->image . '?w=750' );
				if ( $key % 2 == 0 ) :
					?>
	<div class="row">
	<?php endif; ?>
		<div class="col-md-6" itemprop="containsSeason" itemscope itemtype="http://schema.org/TVSeries">
				<?php if ( isset( $item->trailerId ) && ( ! empty( $item->trailerId ) ) ) : ?>
			<span itemprop="trailer" itemscope itemtype="http://schema.org/VideoObject">
				<meta itemprop="thumbnailUrl" content="<?php echo $img; ?>" />
				<meta itemprop="description" content="<?php echo $item->longDescription; ?>" />
				<meta itemprop="name" content="<?php echo $item->name; ?>" />
				<meta itemprop="uploadDate" content="<?php echo ( isset( $item->startDate ) && $item->startDate != '' ) ? date( 'Y-m-d', $item->startDate ) : ''; ?>" />
			</span>
			<!-- <div class="video" data-embedcode="<iframe style='border:none;z-index:4' src='//players.brightcove.net/<?php echo esc_attr( $bc_account_id ); ?>/<?php echo esc_attr( $bc_player_id ); ?>_default/index.html?videoId=<?php echo $item->trailerId; ?>'
				allowfullscreen
				webkitallowfullscreen
				mozallowfullscreen></iframe>">
				<img title="<?php echo $item->name; ?>" alt="thumb image" class="wp-post-image" src="<?php echo $img; ?>" style="width:100%; height:auto;z-index:1;opacity:.75">
				<button class="transparent js-play"><img height="35" src="<?php echo esc_url( get_template_directory_uri() . '/img/play-icon.png' ); ?>" style="opacity:1"><span>Watch Trailer</span></button>
			</div> -->
			<div id="schedule-trailer-video" class="video">
				<img title="<?php echo $item->name; ?>" alt="thumb image" class="wp-post-image" src="<?php echo $img; ?>" style="width:100%; height:auto;z-index:1;opacity:.75">
				<button class="transparent js-play"><img height="35" src="<?php echo esc_url( get_template_directory_uri() . '/img/play-icon.png' ); ?>" style="opacity:1"><span>Watch Trailer</span></button>
				<video
					id="brightcove-schedule-trailer-player"
					class="hidden"
					data-account="<?php echo esc_attr( $bc_account_id ); ?>"
					data-player="<?php echo esc_attr( $bc_player_id ); ?>"
					data-embed="default"
					data-video-id="ref:<?php echo $item->trailerId; ?>"
					poster="<?php echo apply_filters( 'atv_get_image_url', $item->image . '?w=750' ); ?>"
					class="video-js embed-responsive embed-responsive-16by9"
					controls></video>
			</div>
			<?php else : ?>
			<div class="video">
				<img title="<?php echo $item->name; ?>" alt="thumb image" itemprop="image" class="wp-post-image" src="<?php echo $img; ?>" style="width:100%; height:auto;z-index:1;opacity:.75">
			</div>
			<?php endif; ?>
			<div class="franchise-eps-bg"  style="margin-bottom:25px">
				<h5 itemprop="name"><?php echo $item->name; ?></h5>
				<p itemprop="description"><?php echo $item->longDescription; ?></p>
			</div>
		</div>
				<?php if ( ( $key + 1 ) % 2 == 0 || $key == $total_franchises_result - 1 ) : ?>
	</div>
	<?php endif; ?>
				<?php
			endforeach;
		else :
			set_query_var( 'no_result_inline', true );
			get_template_part( 'partials/no-result-message' );
		endif;
	else :
		get_template_part( 'partials/franchises-unavailable-message' );
	endif;
	?>
</section>
<?php
get_footer();
