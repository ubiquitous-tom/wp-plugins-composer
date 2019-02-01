<?php
get_header();
?>

<section id="account-renewal" class="content page-body">
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
			<header><h4>Purchase a subscription plan to start watching <?php bloginfo( 'name' ); ?></h4></header>
			<form class="" action="">
				<h3 class="form-head">Available plans</h3>
				<div class="form-group">
					<label for="promo-code">Promotion code</label>
					<input id="promo-code" class="form-control" type="text">
				</div>
				<div class="form-group">
					<label for="sub-plan">Select a plan</label>
					<select id="sub-plan" class="form-control" >
					<?php foreach ( $this->membership_plans as $plan ) : ?>
						<option value="<?php echo strtolower( $plan['title'] ); ?>">
							<?php echo ucfirst( $plan[ 'title' ] ) . ' - $' . $plan['cost'] ; ?>
						</option>
					<?php endforeach; ?>
					</select>
				</div>
				<h3 class="form-head">Billing information</h3>
				<div class="row">
					<div class="form-group col-md-6">
						<label for="billing-first-name">First Name *</label>
						<input id="billing-first-name" class="form-control" type="text" required>
					</div>
					<div class="form-group col-md-6">
						<label for="billing-last-name">Last Name *</label>
						<input id="billing-last-name" class="form-control" type="text" required>
					</div>
				</div>
				<div class="form-group">
					<label for="card-name">Name on Card *</label>
					<input id="card-name" class="form-control" type="text" required>
				</div>
				<div class="form-group">
					<label for="card-number">Card Number *</label>
					<div id="card-number"></div>
				</div>
				<div class="row">
					<div class="form-group col-md-6">
						<label for="card-expiration">Expiration *</label>
						<div id="card-expiration"></div>
					</div>
					<div class="form-group col-md-6">
						<label for="card-cvc">CVC *</label>
						<div id="card-cvc"></div>
					</div>
				</div>
				<button class="submit btn btn-primary btn-lg">Purchase plan</button>
			</form>
		</div>
	</div>
</section>

<?php
get_footer();
