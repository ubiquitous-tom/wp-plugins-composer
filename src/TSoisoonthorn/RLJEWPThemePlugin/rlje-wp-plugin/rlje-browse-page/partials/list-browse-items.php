<?php
// global $wp_query;
$list_items       = get_query_var( 'array_list_items' ); //$wp_query->query_vars['array_list_items'];
$base_url_path    = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
$total_list_items = count( $list_items );
if ( $total_list_items > 0 ) {
	$item_count = 0;
	foreach ( $list_items as $key => $item ) {
		if ( ! isset( $item->id ) ) {
			$item->id = $item->franchiseID;
		}
		if ( ! isset( $item->id ) ) {
			continue;
		}
		if ( ! isset( $item->name ) ) {
			$item->name = ( isset( $item->id ) ) ? $item->id : $item->franchiseID;
		}
		if ( ! isset( $item->image ) ) {
			$item->image = $item->id . '_avatar';
		}
		$item = apply_filters( 'atv_add_img_and_href', $item );
		$is_new_row = ( $key % 4 == 0 ) ? true : false;
		$item_count++;
		$data_a_z = preg_replace( '/^The\s(.+)/i', '$1', strtolower( $item->name ) );
		if ( $is_new_row ) {
			$item_count = 0;
			?>
			<div class="row">
		<?php } ?>
		<div class="col-sm-6 col-md-6 col-lg-3" itemscope itemtype="http://schema.org/TVSeries" data-az="<?php echo esc_attr( $data_a_z ); ?>" data-added="<?php echo esc_attr( $key + 1 ); ?>">
			<a itemprop="url" href="<?php echo esc_url( trailingslashit( home_url( $item->id ) ) ); ?>">
				<?php $item_image = apply_filters( 'rlje_franchise_artwork', $item->image, $item ); ?>
				<img title="<?php echo esc_attr( $item->name ); ?>" alt="<?php echo esc_attr( $item->id ); ?>" class="wp-post-image" itemprop="image" src="<?php echo esc_url( rljeApiWP_getImageUrlFromServices( $item_image . '?t=titled-avatars&w=550' ) ); ?>" style="width:100%; height:auto;" />
			</a>
			<p itemprop="name" class="franchise-title"><?php echo esc_html( $item->name ); ?></p>
		</div>
		<?php if ( $item_count === 3 || ( $key === $total_list_items - 1 ) ) { ?>
			</div>
		<?php
		}
	}
} else {
	?>
<div class="row">
	<?php
	switch ( get_query_var( 'section' ) ) {
		case 'yourwatchlist' :
			$message = 'Click the "Add to Watchlist" button to add your favorite shows to your watchlist. <br/>You\'ll be able to access your watchlist from any device.<br/>';
			break;

		case 'comingsoon' :
			$message = "We don't have any upcoming shows at this time.";
			break;

		default:
			$message = "section is empty";
	}
	set_query_var( 'no_result_message', $message );
	set_query_var( 'no_result_inline', true );
	get_template_part( 'partials/no-result-message' );
	?>
</div>
<?php }
