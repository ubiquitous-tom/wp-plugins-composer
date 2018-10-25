<?php
get_header();
?>
<section id="signin" class="content page-body">
	<div class="container">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
			<header class="row">
				<h2 class="text-center">Welcome to <?php bloginfo( 'name' ); ?></h2>
				<p class="text-center">Please use your current <?php bloginfo( 'name' ); ?> e-mail and password.</p>
			</header>
			<?php
			if ( isset( $message_error ) ) {
				?>
				<div class="row alert alert-danger"><?php echo $message_error; ?></div>
				<?php
			}
			?>
			<div class="login-form row">
				<form class="signin" method="post">
					<!-- <h3>Sign In</h3> -->
					<div class="form-group">
						<label for="login-email">E-Mail</label>
						<input id="login-email" class="form-control" required name="user_email" type="email">
					</div>
					<div class="form-group">
						<label for="login-password">Password<small>case-sensitive</small></label>
						<input id="login-password" class="form-control" required name="user_password" type="password">
					</div>
					<button class="btn btn-primary btn-lg center-block">Sign In</button>
					<p class="text-center forgot-password">
						<a href="<?php echo home_url( 'forgotpassword' ); ?>">Forgot your password?</a>
					</p>
				</form>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
