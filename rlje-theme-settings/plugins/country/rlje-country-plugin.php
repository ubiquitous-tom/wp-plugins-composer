<?php

class RLJE_Country_Switcher_Plugin {

	public function __construct() {
		add_action( 'init', array( $this, 'add_country_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'country_template_redirect' ) );
	}

	public function add_country_rewrite_rules() {
		add_rewrite_rule( 'country/clear/?', 'index.php?pagename=countryfilter&section=clear', 'top' );
		add_rewrite_rule( 'country/([\w]{2})/?', 'index.php?pagename=countryfilter&section=$matches[1]', 'top' );
	}

	public function country_template_redirect() {
		$pagename = get_query_var( 'pagename' );
		if ( 'countryfilter' === $pagename ) {
			$country_filter = get_query_var( 'section' );
			rljeApiWP_setCountryFilter( $country_filter );
			wp_redirect( home_url() ); /* Redirect browser to homepage */
			exit();
		}
	}
}

$rlje_country_switcher_plugin = new RLJE_Country_Switcher_Plugin();
