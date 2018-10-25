<?php
$brightcove_settings   = get_option( 'rlje_theme_brightcove_shared_settings' );
$brightcove_account_id = $brightcove_settings['shared_account_id'];
$brightcove_player_id  = $brightcove_settings['shared_player_id'];
?>
<section id="home-signup-promotion">
	<div class="container">
		<div class="col-md-10 col-md-offset-1">
			<div class="row">
				<div class="col-sm-6">
					<div class="embed-responsive embed-responsive-16by9">
						<video
							id="umc-about"
							data-video-id="<?php echo esc_attr( $about_video_id ); ?>"
							data-account="<?php echo esc_attr( $brightcove_account_id ); ?>"
							data-player="<?php echo esc_attr( $brightcove_player_id ); ?>"
							data-embed="default"
							class="vide-js embed-responsive-item"
							controls
						></video>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="pitch">
						<h5><?php echo esc_html( $pitch ); ?></h5>
						<a class="btn btn-primary btn-lg" href="/signup/">
							<i class="fa fa-play" aria-hidden="true"></i>Sign Up now!
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
