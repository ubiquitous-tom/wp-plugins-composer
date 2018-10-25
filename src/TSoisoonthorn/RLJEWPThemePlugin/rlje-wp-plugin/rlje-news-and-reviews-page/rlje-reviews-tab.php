<?php

class RLJE_Reviews_Tab extends RLJE_News_And_Reviews {

	protected $transient_key = 'rlje_news_and_review_';
	protected $rlje_logos;
	protected $rlje_reviews;
	protected $rlje_reviews_defaults;

	public function __construct() {
		$this->rlje_reviews_defaults = [
			[
				'title' => 'Variety reviews British Miniseries ‘New Worlds’',
				'image' => plugins_url( 'img/variety.png', __FILE__ ),
				'link'  => 'http://variety.com/2015/digital/news/british-miniseries-new-worlds-starring-jamie-dornan-coming-to-acorn-tv-svod-service-1201410610/',
			],
			[
				'title' => 'Wall Street Journal  ‘Serangoon Road’ Review',
				'image' => plugins_url( 'img/wsj_v4.png', __FILE__ ),
				'link'  => 'http://www.wsj.com/articles/tv-review-serangoon-roadsleuthing-in-singapore-1418351931',
			],
			[
				'title' => 'NY Daily News 4 Star ‘Jamaica Inn’ Review',
				'image' => plugins_url( 'img/dailynews_v1.png', __FILE__ ),
				'link'  => 'http://www.nydailynews.com/entertainment/tv/review-jamaica-inn-article-1.2148676',
			],
			[
				'title' => 'Variety reviews British Miniseries ‘New Worlds’',
				'image' => plugins_url( 'img/variety.png', __FILE__ ),
				'link'  => 'http://variety.com/2015/digital/news/british-miniseries-new-worlds-starring-jamie-dornan-coming-to-acorn-tv-svod-service-1201410610/',
			],
		];

		add_action( 'admin_init', array( $this, 'display_options' ) );
	}

	public function display_options() {
		register_setting( 'rlje_news_and_reviews_reviews_section', 'rlje_news_and_reviews_reviews' );

		// Here we display the sections and options in the settings page based on the active tab.
		$tab = ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : '';
		if ( 'reviews-section' === $tab ) {

			add_settings_section(
				'rlje_news_and_reviews_reviews_section',
				'Reviews',
				array( $this, 'display_latest_news_options' ),
				'rlje-news-and-reviews'
			);

			$reviews_items = array(
				'1st',
				'2nd',
				'3rd',
				'4th',
				'5th',
			);

			for ( $i = 0; $i < 5; $i++ ) {
				if ( $i < 4 ) {
					add_settings_field(
						'rlje_reviews_' . $i,
						$reviews_items[ $i ] . ' Review:',
						array( $this, 'display_rlje_reviews_fields' ),
						'rlje-news-and-reviews',
						'rlje_news_and_reviews_reviews_section',
						array( $i )
					);
				}
			}
		}
	}

	public function display_latest_news_options() {
		$this->rlje_logos   = get_option( 'acorntv_news_options' );
		$this->rlje_reviews = get_option( 'rlje_news_and_reviews_reviews' );
		var_dump( $this->rlje_logos );
		var_dump( $this->rlje_reviews );
		if ( false === $this->rlje_reviews ) {
			$this->rlje_reviews = $this->rlje_reviews_defaults;
			var_dump( $this->rlje_reviews );
		}
		print '<p>Load the Reviews with logo, title and link to show in homepage.</p>';
	}

	public function display_rlje_reviews_fields( $key_param ) {
		$key         = absint( $key_param[0] );
		$value_title = ( ! empty( $this->rlje_reviews[ $key ]['title'] ) ) ? esc_attr( $this->rlje_reviews[ $key ]['title'] ) : '';
		$value_image = ( ! empty( $this->rlje_reviews[ $key ]['image'] ) ) ? esc_attr( $this->rlje_reviews[ $key ]['image'] ) : '';
		$value_link  = ( ! empty( $this->rlje_reviews[ $key ]['link'] ) ) ? esc_attr( $this->rlje_reviews[ $key ]['link'] ) : '';
		?>
		<table class="latest_news_table">
			<tbody>
				<tr>
					<th scope="row">Review Logo:</th>
					<td>
						<select class="lnwsImage" name="rlje_news_and_reviews_reviews[<?php echo esc_attr( $key ); ?>][image]" style="vertical-align:top">
							<option value="">-- Select a Review Logo --</option>
							<?php if ( is_array( $this->rlje_logos ) && count( $this->rlje_logos ) > 0 ) : ?>
								<?php foreach ( $this->rlje_logos as $rlje_logo ) : ?>
							<option value="<?php echo esc_attr( $rlje_logo['image'] ); ?>" <?php selected( $value_image, $rlje_logo['image'] ); ?>><?php echo esc_html( $rlje_logo['title'] ); ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">Review Title:</th>
					<td>
						<input class="lnwsText" name="rlje_news_and_reviews_reviews[<?php echo esc_attr( $key ); ?>][title]" type="text" size="70" placeholder="Title..." value="<?php echo esc_attr( $value_title ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">Review Link:</th>
					<td>
						<input class="lnwsLink" name="rlje_news_and_reviews_reviews[<?php echo esc_attr( $key ); ?>][link]" type="text" size="70" placeholder="http://[external-link]" value="<?php echo esc_attr( $value_link ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}

$rlje_reviews_tab = new RLJE_Reviews_Tab();
