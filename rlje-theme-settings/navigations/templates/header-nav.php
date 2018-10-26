<?php $logo_url = apply_filters( 'rlje_theme_header_logo', '' ); ?>
<!-- Fixed Bootstrap navbar -->
<div id="top-nav" class="navbar navbar-fixed-top" role="navigation">
	<div class="container">

		<div class="navbar-header navbar-left pull-left">
			<button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#navbar-collapse-1">
				<div class="button-bars">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</div>
			</button>
			<a class="navbar-brand pull-right" href="/">
				<img src="<?php echo esc_url( $logo_url ); ?>">
			</a>
		</div>

		<div class="navbar-header navbar-right pull-right">
			<?php do_action( 'rlje_before_header_navigation' ); ?>

			<?php do_action( 'rlje_after_header_navigation' ); ?>
		</div>
		<div class="visible-xs-block visible-sm-block clearfix"></div>
		<div class="collapse navbar-collapse" id="navbar-collapse-1">
			<?php wp_nav_menu( $menu_args ); ?>

			<div class="nav navbar-right">
			<?php
				get_search_form();
			?>
			</div>
		</div>


	</div>
</div>
