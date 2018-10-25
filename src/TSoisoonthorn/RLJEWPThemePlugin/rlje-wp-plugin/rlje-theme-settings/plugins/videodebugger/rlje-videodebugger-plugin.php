<?php

class RLJE_Video_Debugger_Plugin {

	public function __construct() {
		add_action( 'init', array( $this, 'add_video_debugger_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'video_debugger_template_redirect' ) );
	}

	public function add_video_debugger_rewrite_rules() {
		add_rewrite_rule( 'videodebugger/([^/]+)/?', 'index.php?pagename=videodebugger&section=$matches[1]', 'top' );
	}

	public function video_debugger_template_redirect() {
		$pagename = get_query_var( 'pagename' );
		if ( 'videodebugger' === $pagename ) {
			$video_debugger = get_query_var( 'section' );
			rljeApiWP_setVideoDebugger( $video_debugger );
			wp_redirect( home_url() ); /* Redirect browser to homepage */
			exit();
		}
	}
}

$rlje_video_debugger_plugin = new RLJE_Video_Debugger_Plugin();
