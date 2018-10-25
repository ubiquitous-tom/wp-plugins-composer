<ul class="nav pull-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user-circle-o" aria-hidden="true"></i> My <?php bloginfo( 'name' ); ?> <span class="caret"></span></a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'browse/recentlywatched' ) ) ); ?>">Recently Watched</a></li>
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'browse/yourwatchlist' ) ) ); ?>">My Watchlist</a></li>
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'account/status' ) ) ); ?>">Manage Account</a></li>
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'account/editPassword' ) ) ); ?>">Change Password</a></li>
			<?php if ( $web_payment_edit ) { ?>
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'account/editEmail' ) ) ); ?>">Change Email</a></li>
			<?php } ?>
			<li><a href="<?php echo esc_url( trailingslashit( home_url( 'account/logout' ) ) ); ?>">Log Out</a></li>
		</ul>
	</li>
</ul>
