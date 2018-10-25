<?php

class RLJE_Landing_page {

	protected $post_type = 'atv_landing_page';

	public function __construct() {
		add_action( 'init', array( $this, 'acorntv_create_landing_pages_post_type' ) );
		add_action( 'save_post', array( $this, 'acorntv_landing_page_save_meta' ), 10, 2 );
		add_action( 'wp_restore_post_revision', array( $this, 'acorntv_landing_page_restore_revision' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'rlje_description_meta_tag_content', array( $this, 'add_landing_page_description_meta_tag_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_filter( 'document_title_parts', array( $this, 'landing_pages_title_parts' ), 11 );
		add_filter( '_wp_post_revision_fields', array( $this, 'acorntv_landing_page_revision_fields' ) );
		add_filter( 'wp_save_post_revision_check_for_changes', array( $this, 'force_save_revision' ), 10, 3 );
		add_filter( 'template_include', array( $this, 'landing_page_template' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		// Versioning for cachebuster.
		if ( $this->post_type === get_post_type() ) {
			$js_version  = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'js/script.js' ) );
			$css_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' ) );

			wp_enqueue_style( 'rlje-landing-page', plugins_url( 'css/style.css', __FILE__ ), array(), $css_version );

			wp_enqueue_script( 'rlje-brightcove', '//players.brightcove.net/3392051363001/default_default/index.min.js', array(), '3392051363001', true );
			wp_enqueue_script( 'rlje-landing-page', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ), $js_version, true );
		}
	}

	public function wp_enqueue_scripts() {
		$post_type = get_post_type();
		if ( $this->post_type === $post_type ) {
			$css_version = date( 'ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'css/landing.css' ) );
			wp_enqueue_style( 'rlje-landing-page', plugins_url( 'css/landing.css', __FILE__ ), array(), $css_version );
		}
	}

	public function landing_page_template( $template ) {
		$post_type = get_post_type();
		if ( $this->post_type === $post_type ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/landing.php';
		}

		return $template;
	}

	public function acorntv_create_landing_pages_post_type() {
		register_post_type(
			$this->post_type,
			array(
				'labels'               => array(
					'name'          => __( 'Landing Pages' ),
					'singular_name' => __( 'Landing Page' ),
				),
				'public'               => true,
				'has_archive'          => true,
				'rewrite'              => array( 'slug' => 'landing' ),
				'capability_type'      => 'page',
				'menu_position'        => 6,
				'menu_icon'            => 'dashicons-format-aside',
				'supports'             => array(
					'title',
					'editor',
					'excerpt',
					// 'thumbnail',
					// 'author',
					// 'trackbacks',
					// 'custom-fields',
					// 'comments',
					'revisions',
					// 'page-attributes', // (menu order, hierarchical must be true to show Parent option)
					// 'post-formats',
				),
				'register_meta_box_cb' => array( $this, 'acorntv_add_landing_page_metaboxs' ),
			)
		);
	}

	public function acorntv_add_landing_page_metaboxs( $post ) {
		add_meta_box( 'atv_trailer_metabox', 'Trailer', array( $this, 'acorntv_trailer_metabox' ), $this->post_type, 'normal' );
		add_meta_box( 'atv_quote_metabox', 'Quote', array( $this, 'acorntv_quote_metabox' ), $this->post_type, 'normal' );
		add_meta_box( 'atv_franchiseId_metabox', 'Franchise', array( $this, 'acorntv_franchise_id_metabox' ), $this->post_type, 'side' );
		add_meta_box( 'atv_featuredImageUrl_metabox', 'Featured Image', array( $this, 'acorntv_featured_image_url_metabox' ), $this->post_type, 'side' );
	}

	public function acorntv_trailer_metabox( $post ) {
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="atv_trailer_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		// Get the data if it's already been entered
		$trailer_id = get_post_meta( $post->ID, '_atv_trailer_id', true );
		?>
		<table class="form-table">
			<tr>
				<th>
					<label>Trailer ID</label>
				</th>
				<td>
					<input type="text" name="_atv_trailer_id" id="_atv_trailer_id" placeholder="3566879545470" class="regular-text" value="<?php echo $trailer_id; ?>">
					<!-- classes: .small-text .regular-text .large-text -->
				</td>
			</tr>
			<tr>
				<th>
					<label>Preview</label>
				</th>
				<td>
					<div id="preview-container">
						<video id="preview-trailer"
						<?php if ( ! empty( $trailer_id ) ) : ?>
							data-video-id="<?php echo esc_html( $trailer_id ); ?>"
						<?php endif; ?>
							data-account="3392051363001"
							data-player="default"
							data-embed="default"
							class="video-js"
							controls></video>
					</div>
				</td>
			</tr>
		</table>
		<?php
	}

	public function acorntv_quote_metabox( $post ) {
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="atv_quote_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		// Get the data if it's already been entered
		$quote_auth = get_post_meta( $post->ID, '_atv_quote_auth', true );
		$quote_desc = get_post_meta( $post->ID, '_atv_quote_desc', true );
		?>
		<table class="form-table">
			<tr>
				<th>
					<label>Description:</label>
				</th>
				<td>
					<textarea name="_atv_quote_desc" id="_atv_quote_desc" placeholder="Quote Description" class="large-text"><?php echo $quote_desc; ?></textarea>
				</td>
			</tr>
			<tr>
				<th>
					<label>Author:</label>
				</th>
				<td>
					<input type="text" name="_atv_quote_auth" class="regular-text" placeholder="Quote Author" value="<?php echo $quote_auth; ?>">
				</td>
			</tr>
		</table>
		<?php
	}
	public function acorntv_franchise_id_metabox( $post ) {
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="atv_franchiseId_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		// Get the data if it's already been entered
		$franchise_id = get_post_meta( $post->ID, '_atv_franchiseId', true );
		?>

		<table class="form-table">
			<tr>
				<th>
					<label>Franchise ID</label>
				</th>
				<td>
					<input type="text" name="_atv_franchiseId"  placeholder="franchiseid" class="medium-text" value="<?php echo $franchise_id; ?>">
				</td>
			</tr>
		</table>
		<?php
	}
	public function acorntv_featured_image_url_metabox( $post ) {
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="atv_featuredImageUrl_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		// Get the data if it's already been entered
		$default_image_url  = 'https://placeholdit.imgix.net/~text?txtsize=33&txt=No%20Image&w=250&h=150';
		$featured_image_url = get_post_meta( $post->ID, '_atv_featuredImageUrl', true );
		$featured_image_url = ( ! empty( $featured_image_url ) ) ? $featured_image_url : $default_image_url;
		?>
		<table class="form-table">
			<tr>
				<th>
					<label>Image URL</label>
				</th>
				<td>
					<input type="text" name="_atv_featuredImageUrl" id="_atv_featuredImageUrl" class="medium-text" placeholder="http://domain.com/img/my-image.jpg" value="<?php echo esc_url( $featured_image_url ); ?>">
				</td>
			</tr>
			<tr>
				<th>
					<label>Preview</label>
				</th>
				<td>
					<img class="featureImg-loading" src="/wp-admin/images/spinner-2x.gif" alt="loading..." />
				</td>
			</tr>
			<tr>
				<td class="featureImg-preview-td" colspan="2">
					<div id="featuredImg-preview-container">
						<img class="featuredImg-preview" src="<?php echo esc_url( $featured_image_url ); ?>" alt="Featured Image"/>
					</div>
				</td>
			</tr>
		</table>
		<?php
	}


	// Save the acorntv landing page data
	public function acorntv_landing_page_save_meta( $post_id, $post ) {
		// save the data
		$this->acorntv_save_metabox_data( $post, 'atv_trailer_noncename', array( '_atv_trailer_id' ) );
		$this->acorntv_save_metabox_data( $post, 'atv_quote_noncename', array( '_atv_quote_desc', '_atv_quote_auth' ) );
		$this->acorntv_save_metabox_data( $post, 'atv_franchiseId_noncename', array( '_atv_franchiseId' ) );
		$this->acorntv_save_metabox_data( $post, 'atv_featuredImageUrl_noncename', array( '_atv_featuredImageUrl' ) );
	}

	/**
	 * Save the metabox data in db.
	 *
	 * @param string $noncename
	 * @param array  $fieldsName
	 * @return null
	 */
	public function acorntv_save_metabox_data( $post, $nonceName, $fieldsName ) {
		/**
		 * Verify if this came from our screen and with the proper authorization,
		 * because the save_post action can be triggered at other times,
		 * and check if the user is allowed to edit the post or page.
		 */
		if ( ! empty( $_POST[ $nonceName ] ) && wp_verify_nonce( $_POST[ $nonceName ], plugin_basename( __FILE__ ) ) && current_user_can( 'edit_post', $post->ID ) ) {
			// Find and save the data even if it is an auto-save (preview), draft or review.
			foreach ( $fieldsName as $fieldName ) {
				$fieldValue  = $_POST[ $fieldName ]; // if $_POST[$fieldName] is an array change for implode(',', (array)$_POST[$fieldName]) to make it a CSV (unlikely)
				$getMetaData = get_post_meta( $post->ID, $fieldName, true );

				if ( is_string( $getMetaData ) ) {
					update_metadata( 'post', $post->ID, $fieldName, $fieldValue );
				} else { // if the custom field doesn't have a value
					add_metadata( 'post', $post->ID, $fieldName, $fieldValue );
				}
			}
		}
	}

	// Get Fields created.
	public function acorntv_get_fields( $fields = array() ) {
		// Add here the custom fields added to show it in the wp revision screen.
		$fields['_atv_trailer_id']       = 'Trailer Id';
		$fields['_atv_quote_desc']       = 'Quote Description';
		$fields['_atv_quote_auth']       = 'Quote Author';
		$fields['_atv_franchiseId']      = 'Franchise Id';
		$fields['_atv_featuredImageUrl'] = 'Featured Image URL';
		foreach ( $fields as $fieldKey => $fieldValue ) {
			// Add the values to each meta fields in the revision screen.
			add_filter( '_wp_post_revision_field_' . $fieldKey, array( $this, 'acorntv_landing_page_revision_field' ), 10, 2 );
		}
		return $fields;
	}

	public function acorntv_landing_page_revision_field( $value, $field ) {
		return $value;
	}


	// Indicate to WordPress which fields to show in the revision screen.
	public function acorntv_landing_page_revision_fields( $fields ) {
		return $this->acorntv_get_fields( $fields );
	}

	// Check changes in the custom metadata to create a revision.
	public function force_save_revision( $return, $last_revision, $post ) {
		// Force to save a revision only if it is an atv_landing_page and it has a change.
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == $this->post_type ) {
			$metaDatas = $this->acorntv_get_metadatas( $_POST['post_ID'] );
			$isChanged = false;
			foreach ( $metaDatas as $metaDataKey => $metaDatasValue ) {
				// Only save a revision if the any of the meta data has a change.
				if ( is_string( $metaDatasValue ) && trim( $_POST[ $metaDataKey ] ) != trim( $metaDatasValue ) ) {
					$isChanged = true;
					break;
				}
			}
			if ( $isChanged ) {
				$return = false;
			}
		}

		return $return;
	}

	// Add metabox data to post revision
	public function acorntv_landing_page_restore_revision( $post_id, $revision_id ) {
		$revision  = get_post( $revision_id );
		$metaDatas = $this->acorntv_get_metadatas( $revision->ID );
		foreach ( $metaDatas as $metaName => $metaValue ) {
			if ( false !== $metaValue ) {
				update_post_meta( $post_id, $metaName, $metaValue );
			}
		}
	}

	// Get ALL the MetaData values from each meta field created.
	protected function acorntv_get_metadatas( $post_id ) {
		$fields = $this->acorntv_get_fields();
		foreach ( $fields as $fieldKey => $fieldValue ) {
			$metaDatas[ $fieldKey ] = get_metadata( 'post', $post_id, $fieldKey, true );
		}
		return $metaDatas;
	}

	public function add_landing_page_description_meta_tag_content( $description_content ) {
		if ( $this->post_type === get_query_var( 'post_type' ) ) {
			$meta_title = htmlentities( get_the_title() );
			$meta_descr = htmlentities( get_the_excerpt() );

			$description_content = $meta_descr;
		}

		return $description_content;
	}

	public function landing_pages_title_parts( $title ) {
		if ( $this->post_type === get_query_var( 'post_type' ) ) {
			$meta_title = htmlentities( get_the_title() );
			$meta_descr = htmlentities( get_the_excerpt() );

			$title['tagline'] = $meta_title;
		}

		return $title;
	}
}

$rlje_landing_page = new RLJE_Landing_page();
