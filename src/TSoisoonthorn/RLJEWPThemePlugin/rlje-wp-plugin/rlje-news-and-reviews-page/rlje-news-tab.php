<?php

class RLJE_News_Tab extends RLJE_News_And_Reviews {

	protected $transient_key = 'rlje_news_and_review_';
	protected $rlje_news;
	protected $rlje_news_defaults;

	public function __construct() {
		$this->rlje_news_defaults = [
			1 => [
				[
					'type'        => 'image',
					'franchiseId' => 'jackirish',
					'src'         => plugins_url( 'img/upgradeannual515.png', __FILE__ ),
				],
				[
					'type'        => 'video',
					'franchiseId' => '',
					'src'         => '4328731797001',
				],
			],
			2 => [
				[
					'type'        => 'image',
					'franchiseId' => 'richandruthless',
					'src'         => plugins_url( 'img/web_devices.jpg', __FILE__ ),
				],
				[
					'type'        => 'video',
					'franchiseId' => '',
					'src'         => 'UMC1028300_T01',
				],
			],
		];

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'display_options' ) );
	}

	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_rlje-news-and-reviews' === $hook ) {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_media();

			// Versioning for cachebuster.
			$news_js_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/admin-news-and-reviews.js' ) );
			$news_css_verion = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/admin-news-and-reviews.css' ) );
			wp_enqueue_style( 'rlje-admin-news-and-reviews', plugins_url( 'css/admin-news-and-reviews.css', __FILE__ ), array(), $news_css_verion );
			wp_enqueue_script( 'rlje-admin-news-and-reviews', plugins_url( 'js/admin-news-and-reviews.js', __FILE__ ), array( 'jquery-ui-core', 'brightcove-public-player' ), $news_js_version, true );
		}
	}

	public function display_options() {
		register_setting( 'rlje_news_and_reviews_news_section', 'rlje_news_and_reviews_news_group' );
		register_setting( 'rlje_news_and_reviews_news_section', 'rlje_news_and_reviews_news', array( $this, 'rlje_news_sanitize_callback' ) );

		// Here we display the sections and options in the settings page based on the active tab.
		$tab = ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : '';
		if ( empty( $tab ) || ( 'news-section' === $tab ) ) {

			add_settings_section( 'rlje_news_and_reviews_news_group_section', 'News Group', array( $this, 'display_news_group_section' ), 'rlje-news-and-reviews' );
			add_settings_field( 'rlje_news_group', 'Currently Display Group', array( $this, 'display_rlje_news_group_fields' ), 'rlje-news-and-reviews', 'rlje_news_and_reviews_news_group_section' );

			add_settings_section( 'rlje_news_and_reviews_news_section', 'News', array( $this, 'display_news_section' ), 'rlje-news-and-reviews' );

			$reviews_items = array(
				'1st',
				'2nd',
				'3rd',
				'4th',
				'5th',
			);

			for ( $i = 0; $i < 5; $i++ ) {
				add_settings_field(
					'rlje_news_' . $i,
					$reviews_items[ $i ] . ' News:',
					array( $this, 'display_rlje_news_fields' ),
					'rlje-news-and-reviews',
					'rlje_news_and_reviews_news_section',
					array( $i )
				);
			}
		}
	}

	public function display_news_group_section() {
		$this->rlje_news_group = get_option( 'rlje_news_and_reviews_news_group', 1 );
		$this->rlje_news_group = ( false !== $this->rlje_news_group ) ? intval( $this->rlje_news_group ) : 1;
		var_dump( $this->rlje_news_group );
		print '<p>News group to display on homepage</p>';
	}

	public function display_rlje_news_group_fields() {
		$news_group = ( intval( $this->rlje_news_group ) ) ? intval( $this->rlje_news_group ) : 1
		?>
		<select name="rlje_news_and_reviews_news_group" id="news-group" class="regular-text">
			<option value="1" <?php selected( $news_group, 1 ); ?>>Group 1</option>
			<option value="2" <?php selected( $news_group, 2 ); ?>>Group 2</option>
		</select>
		<?php
		submit_button( 'Display This Group Data', 'primary', 'submit', false );
	}

	public function display_news_section() {
		$rlje_news_and_reviews_news = get_option( 'rlje_news_and_reviews_news' );
		var_dump( $rlje_news_and_reviews_news, $this->rlje_news_group, $this->rlje_news );
		$this->rlje_news = ( ! empty( $rlje_news_and_reviews_news[ $this->rlje_news_group ] ) ) ? $rlje_news_and_reviews_news[ $this->rlje_news_group ] : $this->rlje_news_defaults[ $this->rlje_news_group ];
		var_dump( $this->rlje_news );
		print '<p>Load the News images (marketing) or video trailers to show in homepage.</p>';
	}

	public function display_rlje_news_fields( $key_param ) {
		$key           = absint( $key_param[0] );
		$value         = ( ! empty( $this->rlje_news[ $key ]['src'] ) ) ? esc_attr( $this->rlje_news[ $key ]['src'] ) : '';
		$franchise_id  = ( ! empty( $this->rlje_news[ $key ]['franchiseId'] ) ) ? esc_attr( $this->rlje_news[ $key ]['franchiseId'] ) : '';
		$external_link = ( ! empty( $this->rlje_news[ $key ]['externalLink'] ) ) ? esc_attr( $this->rlje_news[ $key ]['externalLink'] ) : '';

		$default_image_src_size        = 70;
		$default_ext_image_src_size    = 45;
		$default_video_src_size        = 20;
		$src_size                      = $default_image_src_size;
		$default_image_src_placeholder = 'http://[image-url]';
		$default_video_src_placeholder = 'ID Number';
		$src_placeholder               = $default_image_src_placeholder;
		$type                          = 'image';
		$type_text                     = 'Image URL';
		if ( ! empty( $this->rlje_news[ $key ]['type'] ) ) {
			$type = $this->rlje_news[ $key ]['type'];
			switch ( $type ) {
				case 'video':
					$src_placeholder = $default_video_src_placeholder;
					$type_text       = 'Video Embed ID';
					break;
				case 'extImage':
					$src_size  = $default_ext_image_src_size;
					$type_text = 'Image URL';
					break;
				case 'image':
				default:
					$type_text = 'Image URL';
			}
		}
		?>
		<input type="hidden" name="rlje_news_and_reviews_news[<?php echo esc_attr( $key ); ?>][group]" value="<?php echo esc_attr( $this->rlje_news_group ); ?>">
		<div id="news-carousel-<?php echo esc_attr( $key + 1 ); ?>" class="news-carousel" data-id="<?php echo esc_attr( $key + 1 ); ?>">
			<p>
				<label for="mkgType">Carousel Type</label>
				<select class="mkgType regular-text" name="rlje_news_and_reviews_news[<?php echo esc_attr( $key ); ?>][type]">
					<option value="image" <?php echo selected( $type, 'image' ); ?>>Franchise ID & Image</option>
					<option value="extImage" <?php echo selected( $type, 'extImage' ); ?>>External Link & Image</option>
					<option value="video" <?php echo selected( $type, 'video' ); ?>>Trailer ID</option>
				</select>
			</p>

			<p class="mkgFranchiseId">
				<label for="mkgFranchiseId">Franchise ID</label>
				<input id="mkgFranchiseId" class="regular-text" name="rlje_news_and_reviews_news[<?php echo esc_attr( $key ); ?>][franchiseId]" type="text" size="20" placeholder="Franchise ID" value="<?php echo esc_attr( $franchise_id ); ?>">
				<span class="help">(e.g. <?php echo esc_url( trailingslashit( home_url( $franchise_id ) ) ); ?>)</span>
			</p>


			<p class="mkgExternalLink">
				<label for="mkgExternalLink">External URL Link</label>
				<input id="mkgExternalLink" class="regular-text" name="rlje_news_and_reviews_news[<?php echo esc_attr( $key ); ?>][externalLink]" type="text" size="45" placeholder="http://[external-link]" value="<?php echo esc_url( $external_link ); ?>">
			</p>

			<p class="mkgSrc">
				<label for="uploadImage"><?php echo esc_html( $type_text ); ?></label>
				<input id="uploadImage" class="uploadImage regular-text" name="rlje_news_and_reviews_news[<?php echo esc_attr( $key ); ?>][src]" type="text" size="<?php echo esc_attr( $src_size ); ?>" placeholder="http://[image-url]" value="<?php echo esc_attr( $value ); ?>"
					data-videoSrcSize="<?php echo esc_attr( $default_video_src_size ); ?>"
					data-imageSrcSize="<?php echo esc_attr( $default_image_src_size ); ?>"
					data-extImageSrcSize="<?php echo esc_attr( $default_ext_image_src_size ); ?>"
					data-imagePlaceholder="<?php echo esc_attr( $default_image_src_placeholder ); ?>"
					data-videoPlaceholder="<?php echo esc_attr( $default_video_src_placeholder ); ?>">
				<!-- Can't upload image here because of how we set up our infrastructure -->
				<!-- <button class="uploadBtn">Upload Image</button> -->
			</p>

			<?php if ( ! empty( $value ) ) : ?>
			<p>
				<label for="preview">Preview</label>
				<?php if ( 'video' === $type ) : ?>
				<div class="video-wrapper">
					<div class="video-preview">
						<video preload
							class="brightcove-public-player"
							data-account="<?php echo esc_attr( $this->brightcove['bc_account_id'] ); ?>"
							data-player="<?php echo esc_attr( $this->brightcove['bc_player_id'] ); ?>"
							data-embed="default"
							data-video-id="<?php echo esc_attr( $value ); ?>"
							class="video-js embed-responsive embed-responsive-16by9"
							controls></video>
					</div>
				</div>
				<?php else : ?>
				<img class="image-preview" src="<?php echo esc_attr( $value ); ?>" alt="Preview Image">
				<?php endif; ?>
			</p>
			<?php endif; ?>
		</div>
		<?php
	}

	public function rlje_news_sanitize_callback( $data ) {
		// Detect multiple sanitizing passes.
		// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
		static $pass_count = 0;
		$pass_count++;

		if ( $pass_count <= 1 ) {
			// Handle any single-time / performane sensitive actions.
			$new_data = [];
			foreach ( $data as $carousel ) {
				if ( ! empty( $carousel['src'] ) ) {
					$news_group = $carousel['group'];
					unset( $carousel['group'] );
					$new_data[ $news_group ][] = $carousel;
				}
			}
			$old_data = get_option( 'rlje_news_and_reviews_news' );
			$new_data = array_replace( (array) array_filter( $old_data ), (array) array_filter( $new_data ) );
		}

		$country_code  = ( ! empty( rljeApiWP_getCountryCode() ) ) ? rljeApiWP_getCountryCode() : 'US';
		$transient_key = $this->transient_key . strtolower( $country_code );
		delete_transient( $transient_key );

		add_settings_error( 'rlje-news-and-reviews', 'settings_updated', 'Successfully updated', 'updated' );

		return $new_data;
	}
}

$rlje_news_tab = new RLJE_News_Tab();
