<?php

class RLJE_Future_Date_Plugin {

	public function __construct() {
		add_action( 'init', array( $this, 'add_future_date_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'future_date_template_redirect' ) );
	}

	public function add_future_date_rewrite_rules() {
		add_rewrite_rule( 'futuredate/today/?', 'index.php?pagename=futuredate&section=today', 'top' );
		add_rewrite_rule( 'futuredate/([\d]{8})/?', 'index.php?pagename=futuredate&section=$matches[1]', 'top' );
	}

	public function future_date_template_redirect() {
		$pagename = get_query_var( 'pagename' );
		if ( 'futuredate' === $pagename ) {
			$future_date = get_query_var( 'section' );
			rljeApiWP_setFutureDate( $future_date );
			wp_redirect( home_url() ); /* Redirect browser to homepage */
			exit();
		}
	}
}

$rlje_future_date_plugin = new RLJE_Future_Date_Plugin();
