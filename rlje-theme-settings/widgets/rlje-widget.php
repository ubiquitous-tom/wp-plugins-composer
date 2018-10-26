<?php

class RLJE_Widget {

	private $footer_areas = array(
		array(
			'name'          => 'Footer Area 1',
			'id'            => 'footer-area-1',
			'description'   => 'Left widget area for the footer',
			'class'         => '',
			'before_widget' => '<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>',
		),
		array(
			'name'          => 'Footer Area 2',
			'id'            => 'footer-area-2',
			'description'   => 'Middle widget area for the footer',
			'class'         => '',
			'before_widget' => '<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>',
		),
		array(
			'name'          => 'Footer Area 3',
			'id'            => 'footer-area-3',
			'description'   => 'Right widget area for the footer',
			'class'         => '',
			'before_widget' => '<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'   => '</h5>',
		),
	);

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'rlje_footer_widget_area', array( $this, 'display_footer_widget' ) );
	}

	public function widgets_init() {
		$this->footer_areas = apply_filters( 'rlje_widget_footer_areas', $this->footer_areas );
		foreach ( $this->footer_areas as $footer_area ) {
			register_sidebar( $footer_area );
		}
	}

	public function display_footer_widget() {
		?>
		<div class="sub-footer">
			<div class="container" style="margin-bottom: 45px; margin-top: 45px;">
			<?php
			foreach ( $this->footer_areas as $widget ) {
				dynamic_sidebar( $widget['id'] );
			}
			?>
			</div>
		</div>
		<?php
	}
}

$rlje_widget = new RLJE_Widget();

require_once 'newsletter/rlje-newsletter-widget.php';
