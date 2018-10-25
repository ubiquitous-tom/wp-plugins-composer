<?php get_header(); ?>

<?php
while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();
	// if ( isset( $_GET['preview_id'], $_GET['preview'] ) && $_GET['preview'] ) {
	// $autoSave = wp_get_post_autosave( $post_id );
	// $post_id   = $autoSave->ID;
	// }
	$featured_img   = get_post_meta( $post_id, '_atv_featuredImageUrl', true );
	$franchise_id   = get_post_meta( $post_id, '_atv_franchiseId', true );
	$quote_desc     = get_post_meta( $post_id, '_atv_quote_desc', true );
	$quote_auth     = get_post_meta( $post_id, '_atv_quote_auth', true );
	$trailer_id     = get_post_meta( $post_id, '_atv_trailer_id', true );
	// $base_url_path  = ( function_exists( 'rljeApiWP_getBaseUrlPath' ) ) ? rljeApiWP_getBaseUrlPath() : '';
	// $franchise_link = ( ! empty( $franchise_id ) ) ? $base_url_path . '/' . $franchise_id : null;
	$franchise_link = ( ! empty( $franchise_id ) ) ? trailingslashit( home_url( $franchise_id ) ) : home_url( '/' );
	$environment    = apply_filters( 'atv_get_extenal_subdomain', '' ); // Leave empty value to production else set -dev or -qa.

	if ( ! empty( $featured_img ) ) {
		$hero_class = 'hidden-xs hidden-sm col-md-4 col-md-offset-8';
	} else {
		$hero_class = 'col-sm-12 col-md-6 col-md-offset-3 heroText';
	}
	?>

<div class="hero">
	<div class="container">
		<?php if ( ! empty( $franchise_link ) ) : ?>
		<a href="<?php echo esc_url( $franchise_link ); ?>">
			<img title="<?php echo get_the_title(); ?>" alt="thumb image" class="wp-post-image" src="<?php echo $featured_img; ?>" style="width:100%; height:auto;">
		</a>
		<?php endif; ?>
		<div class="<?php echo esc_attr( $hero_class ); ?>" id="hero-content">
			<img width="165" src="https://s3.amazonaws.com/acorntv-artwork-storage/atvlogo_v2_small.png">
			<h1 class="hero-header"><?php the_title(); ?> </h1>
			<p class="hero-callout"><?php echo get_the_excerpt(); ?></p>
			<a href="<?php echo 'https://signup' . $environment . '.acorn.tv/createaccount.html'; ?>" class="button-link">
				<button class=".btn .btn-primary">Start Free Month</button>
			</a>
		</div>
	</div>
</div>

<section class="landing-body">
	<div class="container">
		<div class="col-md-7 col-sm-12 column ">
			<?php the_content(); ?>
			<a href="https://signup<?php echo $environment; ?>.acorn.tv/createaccount.html" class="landing-link">Start free month</a>
			<?php if ( ! empty( $franchise_link ) ) : ?>
			<span class="landing-or">or </span>
			<a href="<?php echo esc_url( $franchise_link ); ?>" class="landing-link">View Episode</a>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $trailer_id ) ) : ?>
		<div class="hidden-xs hidden-sm col-md-5 column">
			<div style="display: block; position: relative; max-width: 100%;">
				<div style="padding-top: 56.25%;">
					<video style="width: 100%; height: 100%; position: absolute; top: 0px; bottom: 0px; right: 0px; left: 0px;"
						data-video-id="<?php echo esc_attr( $trailer_id ); ?>"
						data-account="3392051363001"
						data-player="default"
						data-embed="default"
						class="video-js"
						controls></video>
					<script src="//players.brightcove.net/3392051363001/default_default/index.min.js"></script>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<section class="quote-container">
	<img class="quote-img" src="https://atv3.s3.amazonaws.com/landing/quote.svg" />
	<p class="quote"><?php echo esc_html( $quote_desc ); ?></p>
	<p class="quote-contributor"><?php echo esc_html( $quote_auth ); ?></p>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
