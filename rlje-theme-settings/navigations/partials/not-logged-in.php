<ul class="nav pull-right">
	<li class="pull-left">
		<a class="sign-in" href="<?php echo esc_url( trailingslashit( home_url( 'signin' ) ) ); ?>"><?php echo esc_html( $navigation_text['login'] ); ?></a>
	</li>
	<li class="pull-right" >
		<a class="sign-up hidden-lg" href="<?php echo esc_url( trailingslashit( home_url( 'signup' ) ) ); ?>"><?php echo esc_html( $navigation_text['signup'] ); ?></a>
		<a class="sign-up btn btn-primary visible-lg-block" href="<?php echo esc_url( trailingslashit( home_url( 'signup' ) ) ); ?>"><?php echo esc_html( $navigation_text['free_trial'] ); ?></a>
	</li>
</ul>
