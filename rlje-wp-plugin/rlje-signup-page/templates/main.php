<?php
get_header();
?>
<section id="signup" class="content page-body">
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
			<header class="row">
				<h3 class="text-center">Ready to start your free trial?</h3>
				<p class="text-center side">Watch the best in Black films &amp; television all in one place, always commercial free.</p>
			</header>
			<div id="progress-steps" class="row">
				<div class="step active text-center">
					<span>Step 1</span>
				</div>
				<div class="step text-center">
					<span>Step 2</span>
				</div>
				<div class="step text-center">
					<span>Finish</span>
				</div>
			</div>
			<form class="signup step-one row">
				<h4 class="form-head">Create your account</h4>
				<div class="signup-form-group">
					<div class="form-group">
						<label for="signup-email">E-Mail Address *</label>
						<input id="signup-email" class="form-control" required name="signup_email" type="email" pattern="[a-zA-Z0-9.-_+]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" title="Please provide a valid email address">
					</div>
					<div class="form-group">
						<label for="signup-email-confirm">Confirm E-Mail Address *</label>
						<input id="signup-email-confirm" class="form-control" required name="signup_email_confirm" type="email" pattern="[a-zA-Z0-9.-_+]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" title="Please provide a valid email address">
					</div>
					<div class="form-group">
						<label for="signup-password">Password *</label>
						<input id="signup-password" class="form-control" required name="signup_password" type="password" minlength="6" placeholder="6 characters minimum">
					</div>
					<div class="form-group">
						<label for="signup-password-confirm">Confirm Password *</label>
						<input id="signup-password-confirm" class="form-control" required name="signup_password_confirm" type="password" minlength="6" placeholder="6 characters minimum">
					</div>
					<div class="form-group">
						<label for="promo-code">Promo Code</label>
						<input id="promo-code" class="form-control" name="promo_code" type="text" >
					</div>
					<div class="checkbox">
						<label>
							<input checked type="checkbox">Sign me up for the <?php bloginfo( 'name' ); ?> newsletter
						</label>
					</div>
				</div>
				<div class="text-center">
					<button class="submit-step btn btn-primary btn-lg">Continue to step 2 →</button>
				</div>
			</form>
		</div>
	</div>
</section>

<?php
get_footer();
