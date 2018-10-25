<?php

class RLJE_Footer {
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'add_stunning' ) );
	}

	// TODO: This needs to be moved to account plugins.
	public function add_stunning() {
		$session_cookie = ( ! empty( $_COOKIE['ATVSessionCookie'] ) ) ? stripslashes( $_COOKIE['ATVSessionCookie'] ) : '';
		$stripe_customer_id = ( ! empty( $session_cookie ) ) ? rljeApiWP_getStripeCustomerId( $session_cookie ) : false;
		if ( $stripe_customer_id ) :
			?>
		<style>
		div#the-stunning-bar {
			bottom: 0;
			top: initial;
		}
		</style>
		<script type="text/javascript">
		(function(d, t) {
			var e = d.createElement(t),
			s = d.getElementsByTagName(t)[0];
			e.src = 'https://d1gqkepxkcxgvm.cloudfront.net/stunning-bar.js';
			e.id  = 'stunning-bar';
			e.setAttribute('data-app-ckey', '1742pkulzsyysulfkngkfulcd');
			e.setAttribute('data-stripe-id', '<?php echo $stripe_customer_id; ?>');
			s.parentNode.insertBefore(e, s);
		}(document, 'script'));
		</script>
			<?php endif;
	}
}

$rlje_footer = new RLJE_Footer();
