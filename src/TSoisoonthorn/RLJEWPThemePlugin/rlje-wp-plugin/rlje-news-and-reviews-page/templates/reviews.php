<?php
// $base_url_path = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
?>
<div class="col-md-12 col-sm-12 column col-lg-6 press">
	<?php
	if ( is_array( $reviews ) && count( $reviews ) > 1 ) :
		foreach ( $reviews as $item ) :
			$external_link = ( preg_match( '/^http[s]*\:[\/]{2}/i', $item['link'] ) ) ? $item['link'] : 'http://' . str_replace( [ ':', '//' ], '', $item['link'] );
	?>
	<div class="press-border">
		<a href="<?php echo esc_url( $external_link ); ?>" onclick="window.open('<?php echo esc_url( $external_link ); ?>', 'newwindow', 'scrollbars=yes,top=200, left=100,width=850, height=600'); return false;" target="_blank">
			<div class="col-xs-4 col-sm-4 col-md-3">
				<?php if ( ! empty( $item['image'] ) ) : ?>
				<img title="<?php echo esc_html( $item['title'] ); ?>" alt="thumb image" class="wp-post-image" src="<?php echo esc_url( $item['image'] ); ?>">
				<?php endif; ?>
			</div>

			<div class="col-xs-8 col-sm-8 col-md-9 press-title">
				<span><?php echo esc_html( $item['title'] ); ?></span>
				<span class="fa fa-angle-right icon-sm"></span>
			</div>
		</a>
	</div>
	<?php
		endforeach;
	endif;
	?>
</div>
