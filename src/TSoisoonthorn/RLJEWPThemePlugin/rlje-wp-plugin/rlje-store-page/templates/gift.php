<?php
get_header();
?>

<section class="content page-body">
	<div class="container">
		<header>
			<h2>Give <?php bloginfo( 'name' ); ?></h2>
			<h3>Share the best in Black film &amp; television with someone special</h3>
			<p>Your gift purchase entitles the recipient to a one year <?php bloginfo( 'name' ); ?> membership. They'll enjoy hundreds of hours of the best drama, romance, comedy and much more — all in one place, always available, and always commercial-free.</p>
			<p>You’ll receive an email confirming your gift purchase with instructions on how to redeem the gift subscription. This can be forwarded directly to the recipient.</p>
		</header>
		<div class="gift-section">
			<form id="gift-items" action="">
				<div class="gift-item row">
					<div class="col-md-2">
						<img class="gift-icon center-block" src="<?php echo get_template_directory_uri() . '/img/gift_icon.svg'; ?>" alt="Gift Icon">
					</div>
					<div class="col-md-6">
						<h3 class="title"><?php bloginfo( 'name' ); ?> Gift Membership</h3>
						<p class="description">Give 12 months of unlimited access to the best in black films &amp; TV</p>
					</div>
					<div class="col-md-2">
						<input id="gift-quantity" value="1" type="number" min="1" max="3">
					</div>
					<div class="col-md-2">
						$<span id="membership-cost"></span>
					</div>
				</div>
			</form>
			<div class="row">
				<div class="checkout-total pull-right">
					<strong>Total: $<span id="total"></span></strong>
				</div>
			</div>
		</div>
		<button form="gift-items" class="btn btn-primary btn-lg pull-right">Checkout</button>
	</div>
</section>
<script type="text/x-tmpl" id="tmpl-demo">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<header>
					<h2>UMC Checkout</h2>
					<h4>To make your purchase, please fill in the form below.</h4>
				</header>
				<form id="purchase-gift" action="">
					<div class="form-section">
						<h4 class="form-head">Billing information</h4>
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
						<div class="row">
							<div class="form-group col-md-6">
								<label for="billing-country">Country *</label>
								<select id="billing-country" class="form-control" disabled>
									<option value="US">United States</option>
								</select>
							</div>
							<div class="form-group col-md-6">
								<label for="billing-zip">Zip / Postal Code *</label>
								<input id="billing-zip" class="form-control" type="text" required>
							</div>
						</div>
						<div class="form-group">
							<label for="billing-email">Email Address *</label>
							<input id="billing-email" class="form-control" type="email" min="6" required>
						</div>
						<div class="form-group">
							<label for="billing-email-confirm">Confirm Email Address *</label>
							<input id="billing-email-confirm" class="form-control" type="email" min="6" required>
						</div>
					</div>
					<div class="form-section">
						<h4 class="form-head">Credit Card Information</h4>
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
					</div>
					<button class="submit btn btn-primary btn-lg">Review &amp; Place Order</button>
				</form>
			</div>
			<div class="col-md-4">
				<div class="order-summary bg-info">
				{% include( 'tmpl-confirmation-dialog', {orderItemCount: o.quantity, orderItemCost: o.cost} ); %}
				</div>
				<div class="faqs well">
					<h4>Frequently Asked Questions</h4>
					<p>
						<strong>How do I watch UMC</strong><br>
						All you need is an Internet connection and a device to watch on.
					</p>
					<p>
						<strong>When do you add new shows?</strong><br>
						UMC adds new shows every week.
					</p>
					<p>
						<strong>Are there closed captions or subtitles available on UMC programs?</strong><br>
						Yes! All of our programs include closed captions and/or SDH subtitles.
					</p>
					<p>For more FAQs, <a href="/faqs">CLICK HERE</a></p>
				</div>
			</div>
		</div>
	</div>
</script>
<script type="text/x-tmpl" id="tmpl-confirmation-page">
	<div class="container">
		<header>
			<h2>Thank You for your order!</h2>
			<h4>You will soon recieve a confirmation email that includes instructions for the recipient.</h4>
		</header>
		<h3>Giving Your Gift is Easy</h3>
		<ul>
			<li>Open your confirmation email</li>
			<li>Copy the links and paste them into separate emails to your recipients (make sure you don't send the same link to different people)</li>
		</ul>
		<div class="order-detail">
			<h4>Order {%=o.orderNumber%}</h4>
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Promo Code</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
				{% for (var i=0; i<o.promos.length; i++) { %}
					<tr>
						<td>{%= i+1 %}</td>
						<td>{%=o.promos[i].GiftCode.Code%}</td>
						<td>{%=o.siteName%} - 1 year gift</td>
					</tr>
				{% } %}
				</tbody>
			</table>
		</div>
	</div>
</script>

<script type="text/x-tmpl" id="tmpl-confirmation-dialog">
	<table class="gift-order-summary table">
		<thead>
			<tr>
				<th class="text-right" colspan="2">Quantity</th>
				<th class="text-right">Price</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong>Yearly UMC Subscritption</strong></td>
				<td class="text-right">{%=o.orderItemCount%}</td>
				<td class="text-right">${%=o.orderItemCost%}</td>
			</tr>
		</tbody>
	</table>
	<div class="text-right">Subtotal: <strong>${%=o.orderItemCount * o.orderItemCost%}</strong></div>
</script>

<!-- Modal -->
<div class="modal fade" id="confirmPurchaseModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Confirm Purchase</h4>
		</div>
		<div class="modal-body">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			<button type="button" class="btn btn-primary" id="confirmPurchase">Confirm</button>
		</div>
		</div>
	</div>
</div>

<?php
get_footer();
