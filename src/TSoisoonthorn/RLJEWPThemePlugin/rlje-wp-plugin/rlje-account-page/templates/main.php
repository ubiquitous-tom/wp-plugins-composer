<?php
get_header();
?>
<section id="account-settings" class="content page-body">
	<div class="container">
		<nav class="col-sm-4">
			<ul class="nav nav-pills nav-stacked">
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'status' ? 'active' : '' ) ); ?> ">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/status' ) ) ); ?>">Account Status</a>
				</li>
				<?php if ( $this->user_profile->Membership->WebPaymentEdit ) { ?>
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'editEmail' ? 'active' : '' ) ); ?>">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/editEmail' ) ) ); ?>">Change e-mail</a>
				</li>
				<?php } ?>
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'editPassword' ? 'active' : '' ) ); ?>">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/editPassword' ) ) ); ?>">Change Password</a>
				</li>
				<?php if ( $this->user_profile->Membership->WebPaymentEdit ) { ?>
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'editBilling' ? 'active' : '' ) ); ?>">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/editBilling' ) ) ); ?>">Update Billing Info</a>
				</li>
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'cancelMembership' ? 'active' : '' ) ); ?>">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/cancelMembership' ) ) ); ?>">Cancel Membership</a>
				</li>
				<li class="<?php echo sanitize_html_class( ( $this->account_action == 'applyCode' ? 'active' : '' ) ); ?>">
					<a href="<?php echo esc_url( trailingslashit( home_url( 'account/applyCode' ) ) ); ?>">Apply Code</a>
				</li>
				<?php } ?>
			</ul>
		</nav>
		<section class="col-sm-8">
		<?php
		$partial = $this->show_subsection();
		require $partial;
		?>
		</section>
	</div>
</section>


<?php
get_footer();
