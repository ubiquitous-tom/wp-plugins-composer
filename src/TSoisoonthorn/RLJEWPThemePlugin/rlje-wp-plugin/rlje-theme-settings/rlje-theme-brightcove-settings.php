<?php

class RLJE_Theme_Brightcove_Settings {

	protected $theme_brightcove_restricted_settings = array();

	public function __construct() {
		add_action( 'admin_init', array( $this, 'display_options' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_brightcove_settings_submenu' ) );
	}

	public function display_options() {
		register_setting( 'rlje_theme_brightcove_section', 'rlje_theme_brightcove_restricted_settings' );
		register_setting( 'rlje_theme_brightcove_section', 'rlje_theme_brightcove_shared_settings' );

		add_settings_section( 'rlje_theme_brightcove_restricted_section', 'Restricted Account', array( $this, 'display_rlje_brightcove_restricted_options_content' ), 'rlje-theme-brightcove-settings' );
		add_settings_field( 'restricted_account_id', 'Restricted Account ID', array( $this, 'display_brightcove_restricted_account_id' ), 'rlje-theme-brightcove-settings', 'rlje_theme_brightcove_restricted_section' );
		add_settings_field( 'restricted_player_id', 'Restricted Player ID', array( $this, 'display_brightcove_restricted_player_id' ), 'rlje-theme-brightcove-settings', 'rlje_theme_brightcove_restricted_section' );

		add_settings_section( 'rlje_theme_brightcove_shared_section', 'Shared Account', array( $this, 'display_rlje_brightcove_shared_options_content' ), 'rlje-theme-brightcove-settings' );
		add_settings_field( 'shared_account_id', 'Shared Account ID', array( $this, 'display_brightcove_shared_account_id' ), 'rlje-theme-brightcove-settings', 'rlje_theme_brightcove_shared_section' );
		add_settings_field( 'shared_player_id', 'Shared Player ID', array( $this, 'display_brightcove_shared_player_id' ), 'rlje-theme-brightcove-settings', 'rlje_theme_brightcove_shared_section' );
	}

	public function add_theme_brightcove_settings_submenu() {
		add_submenu_page(
			'rlje-theme-settings',
			'Brightcove Settings',
			'Brightcove Settings',
			'manage_sites',
			'rlje-theme-brightcove-settings',
			array( $this, 'rlje_theme_brightcove_settings_page' )
		);
	}

	public function rlje_theme_brightcove_settings_page() {
		$active_fields = 'rlje_theme_brightcove_section';
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1>Brightcove Options</h1>
			<form method="post" action="options.php">
				<?php
					// Add_settings_section callback is displayed here. For every new section we need to call settings_fields.
					settings_fields( $active_fields );

					// all the add_settings_field callbacks is displayed here.
					do_settings_sections( 'rlje-theme-brightcove-settings' );

					// Add the submit button to serialize the options.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function display_rlje_brightcove_restricted_options_content() {
		echo 'Brightcove account for videos behind paywall';
		$this->theme_brightcove_restricted_settings = get_option( 'rlje_theme_brightcove_restricted_settings' );
		var_dump( $this->theme_brightcove_restricted_settings );
	}

	public function display_brightcove_restricted_account_id() {
		$restricted_account_id = ( ! empty( $this->theme_brightcove_restricted_settings['restricted_account_id'] ) ) ? $this->theme_brightcove_restricted_settings['restricted_account_id'] : '';
		?>
		<input type="text" name="rlje_theme_brightcove_restricted_settings[restricted_account_id]" id="restricted-account-id" class="regular-text" value="<?php echo esc_attr( $restricted_account_id ); ?>" placeholder="Restricted Account ID" required>
		<p class="description">Brightcove restricted account ID for paywall customers such as video on Episode page. (e.g. 3392051362001)</p>
		<?php
	}

	public function display_brightcove_restricted_player_id() {
		$restricted_player_id = ( ! empty( $this->theme_brightcove_restricted_settings['restricted_player_id'] ) ) ? $this->theme_brightcove_restricted_settings['restricted_player_id'] : '';
		?>
		<input type="text" name="rlje_theme_brightcove_restricted_settings[restricted_player_id]" id="restricted-player-id" class="regular-text" value="<?php echo esc_attr( $restricted_player_id ); ?>" placeholder="Restricted Player ID" required>
		<p class="description">Brightcove restricted player ID for paywall customers such as video on Episode page. (e.g. e148573c-29cd-4ede-a267-a3947918ea4a)</p>
		<?php
	}

	public function display_rlje_brightcove_shared_options_content() {
		echo 'Brightcove account for trailer videos';
		$this->theme_brightcove_shared_settings = get_option( 'rlje_theme_brightcove_shared_settings' );
		var_dump( $this->theme_brightcove_shared_settings );
	}

	public function display_brightcove_shared_account_id() {
		$shared_account_id = ( ! empty( $this->theme_brightcove_shared_settings['shared_account_id'] ) ) ? $this->theme_brightcove_shared_settings['shared_account_id'] : '';
		?>
		<input type="text" name="rlje_theme_brightcove_shared_settings[shared_account_id]" id="shared-account-id" class="regular-text" value="<?php echo esc_attr( $shared_account_id ); ?>" placeholder="Share Account ID" required>
		<p class="description">Brightcove share account ID for all customers such as video on Trailer page. (e.g. 3392051363001)</p>
		<?php
	}

	public function display_brightcove_shared_player_id() {
		$shared_player_id = ( ! empty( $this->theme_brightcove_shared_settings['shared_player_id'] ) ) ? $this->theme_brightcove_shared_settings['shared_player_id'] : '';
		?>
		<input type="text" name="rlje_theme_brightcove_shared_settings[shared_player_id]" id="shared-player-id" class="regular-text" value="<?php echo esc_attr( $shared_player_id ); ?>" placeholder="Share Player ID" required>
		<p class="description">Brightcove restricted player ID for paywall customers such as video on Episode page. (e.g. 0066661d-8f08-4e7b-a5b4-8d48755a3057)</p>
		<?php
	}
}

$rjle_theme_brightcove_settings = new RLJE_Theme_Brightcove_Settings();
