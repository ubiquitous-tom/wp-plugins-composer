<?php
// TODO: replace the hardcoded support email address with one coming from wp_settings
get_header();
?>
<section id="forgotpassword" class="content page-body">
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2">
			<header class="row">
				<h2 class="text-center">Reset Your Password</h2>
			</header>
			<p>Please enter the email address you used to create your account. Within a few minutes, we will send you a link so you can create a new password.</p>
			<p>Please ensure that <strong>support@umc.tv</strong> is in your allowed senders list or else the reset link might end up in your spam folder.</p>
			<div class="row">
				<div class="col-sm-8 password-rest-form">
					<form class="password-reset">
						<div class="form-group">
							<label for="email">E-Mail Address</label>
							<input id="email" class="form-control" name="user_email" type="email" required>
						</div>
						<button class="btn btn-primary">Send me a reset link</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/x-tmpl" id="tmpl-reset-success">
	<p>We have sent you an email with a link you can use to create a new password. If you do not see the email please check your promotions and spam folders.</p>
	If the email is not in any of these locations, please <a href="<?php echo home_url( 'contact-us' ); ?>">Contact Us</a>.
</script>
<?php
get_footer();
