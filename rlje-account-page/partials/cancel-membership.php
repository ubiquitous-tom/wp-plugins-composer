<h3 class="section-header">Cancel Membership</h3>
<?php if ( $this->account_cancelable() ) { ?>
<h4>Hello <?php echo $this->get_user_name(); ?>!</h4>
<div class="alert alert-info">
	<p>You've been a member of <?php bloginfo( 'name' ); ?> since <strong><?php echo $this->get_user_join_date(); ?></strong>.</p>
	<p>On <strong><?php echo $this->get_next_billing_date(); ?></strong>, your card will be charged <strong><?php echo $this->get_next_billing_amount(); ?></strong> to continue with another month of <?php bloginfo( 'name' ); ?> benefits.</p>
	<p>If you wish to cancel, please select the button below. You may be asked to sign in again.</p>
</div>
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelMembershipModal">
Cancel <?php bloginfo( 'name' ); ?> Membership
</button>
<?php } else { ?>
<div class="alert alert-danger">
	Your account can't be canceled
</div>
<?php }  ?>
<!-- Modal -->
<div class="modal fade" id="cancelMembershipModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Confirm Cancellation</h4>
		</div>
		<div class="modal-body">
			<h4>Are you sure you'd like to cancel your account?</h4>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Nervermind!</button>
			<button type="button" class="btn btn-danger" id="confirmCancel">Cancel <?php bloginfo( 'name' ); ?></button>
		</div>
		</div>
	</div>
</div>
