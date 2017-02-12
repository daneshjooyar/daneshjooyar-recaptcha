<?php

/**
* Manage Plugin Settings
*/
class DYRECAPTCHA_Settings
{

	/**
	 * Page id for settings page
	 * @var String
	 */
	private $page_id;

	/**
	 * General section id
	 * @var String
	 */
	private $section_general;

	/**
	 * Site key setting option name
	 * @var String
	 */
	private $setting_sitekey;

	/**
	 * Secret key setting option name
	 * @var String
	 */
	private $setting_secretkey;

	/**
	 * Is_active setting checkbox option name
	 * @var boolean
	 */
	private $setting_is_active;

	/**
	 * Check if active recaptcha for login page option name
	 * @var String
	 */
	private $setting_active_login;

	/**
	 * Check if active recaptcha for registration page option name
	 * @var String
	 */
	private $setting_active_registration;

	/**
	 * Check if active recaptcha for reset_password page option name
	 * @var String
	 */
	private $setting_active_reset_password;

	/**
	 * Check if active recaptcha for comment_form option name
	 * @var String
	 */
	private $setting_active_comment_form;

	/**
	 * Language code option name
	 * @var String
	 */
	private $languages;

	/**
	 * White List ip that dont use recaptcha option name
	 * @var String
	 */
	private $setting_whitelist;
	
	
	function __construct( )
	{

		$this->page_id 							= 'daneshjooyar-recaptcha-settings';
		$this->section_general 					= $this->page_id . 'general-section';

		$this->setting_sitekey 					= $this->page_id . '_sitekey';
		$this->setting_secretkey 				= $this->page_id . '_secretkey';
		$this->setting_language 				= $this->page_id . '_language';
		$this->setting_whitelist 				= $this->page_id . '_whitelist';
		$this->setting_is_active 				= $this->page_id . '_is_active';
		$this->setting_active_login				= $this->page_id . '_is_active_login';
		$this->setting_active_registration		= $this->page_id . '_is_active_registration';
		$this->setting_active_reset_password	= $this->page_id . '_is_active_reset_password';
		$this->setting_active_comment_form		= $this->page_id . '_is_active_comment_form';

		$this->languages = array(
			'fa'	=> __( 'Persian', 'daneshjooyar-recaptcha' ),
			'ar'	=> __( 'Arabic', 'daneshjooyar-recaptcha' ),
			'en'	=> __( 'English(US)', 'daneshjooyar-recaptcha' ),
			'en-GB'	=> __( 'English(UK)', 'daneshjooyar-recaptcha' ),
		);

	}

	/**
	 * Get Sitekey from settings
	 * @return String sitekey for recaptcha
	 */
	public function get_sitekey() {
		return get_option( $this->setting_sitekey );
	}

	/**
	 * Get Secretkey from settings
	 * @return String Secret key for recaptcha validation
	 */
	public function get_secretkey() {
		return get_option( $this->setting_secretkey );
	}

	/**
	 * Get Recaptcha language from settings
	 * @return String Define recaptcha language
	 */
	public function get_language() {
		return get_option( $this->setting_language, 'en' );
	}

	/**
	 * Check for is active recaptcha or not
	 * @return boolean if recaptcha is active
	 */
	public function is_active() {
		return get_option( $this->setting_is_active, 0 );
	}

	/**
	 * Check for is active recaptcha for login page or not
	 * @return boolean if recaptcha is active for login page
	 */
	public function is_active_login() {
		return get_option( $this->setting_active_login, 0 );
	}

	/**
	 * Check for is active recaptcha for registration page or not
	 * @return boolean if recaptcha is active for registration page
	 */
	public function is_active_registration() {
		return get_option( $this->setting_active_registration, 0 );
	}

	/**
	 * Check for is active recaptcha for reset_password page or not
	 * @return boolean if recaptcha is active for reset_password page
	 */
	public function is_active_reset_password() {
		return get_option( $this->setting_active_reset_password, 0 );
	}

	/**
	 * Check for is active recaptcha for reset_password page or not
	 * @return boolean if recaptcha is active for reset_password page
	 */
	public function is_active_comment_form() {
		return get_option( $this->setting_active_comment_form, 0 );
	}

	/**
	 * Check ip that is in whitelist or not
	 * @param  String  $userip ip that must check
	 * @return boolean         True if ip is in whitelist
	 */
	public function is_in_whitelist( $userip ) {
		$whitelist = get_option( $this->setting_whitelist );
		foreach ( explode( PHP_EOL , $whitelist ) as $line => $ip) {
			if( $userip == trim( $ip ) ){
				return true;
			}
		}
		return false;
	}



	/**
	 * Run and implement automatic settings page
	 * @return Void
	 */
	public function active() {

		add_action( 'admin_menu', array( $this, 'add_option_page' ) );

		add_action( 'admin_init', array( $this, 'add_options_fields' ));

	}

	/**
	 * Add Options page by usign add_options_page() function in wordpress
	 * that add menu under Settings in wordpress menu
	 */
	public function add_option_page() {
		add_options_page(
			__( 'Recaptcha Settings', 'daneshjooyar-recaptcha' ),
			__( 'Recaptcha Settings', 'daneshjooyar-recaptcha' ),
			'manage_options',
			'daneshjooyar-recaptcha-settings',
			array( $this, 'recaptcha_settings_callback' )
		);
	}

	/**
	 * Callback that echo settings UI in plugin settings page
	 * @return Void Render UI settings page
	 */
	public function recaptcha_settings_callback() {
		?>
		<form action='options.php' method='post'>

			<h2><?php esc_html_e( 'Daneshjooyar Recaptcha Settings Page', 'daneshjooyar-recaptcha' );?></h2>

			<?php
			settings_fields( $this->page_id );
			do_settings_sections( $this->page_id );
			submit_button();
			?>

		</form>
		<?php
	}

	/**
	 * Add Options fields and section in plugin settings page
	 */
	public function add_options_fields() {

		add_settings_section(
			$this->section_general,
			__( 'General Settings', 'daneshjooyar-recaptcha' ),
			null,
			$this->page_id
		);

		add_settings_field(
			$this->setting_sitekey,
			__( 'Site Key', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_sitekey_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_sitekey );

		add_settings_field(
			$this->setting_secretkey,
			__( 'secret Key', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_secretkey_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_secretkey );

		add_settings_field(
			$this->setting_language,
			__( 'Language', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_language_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_language );

		add_settings_field(
			$this->setting_is_active,
			__( 'Active', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_is_active_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_is_active );

		add_settings_field(
			$this->setting_whitelist,
			__( 'Whitelist IPs', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_whitelist_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_whitelist );

		add_settings_field(
			$this->setting_active_login,
			__( 'Is active in login page', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_active_login_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_active_login );

		add_settings_field(
			$this->setting_active_registration,
			__( 'Is active in registration page', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_active_registration_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_active_registration );

		add_settings_field(
			$this->setting_active_reset_password,
			__( 'Is active in reset password page', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_active_reset_password_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_active_reset_password );

		add_settings_field(
			$this->setting_active_comment_form,
			__( 'Is active in comment form', 'daneshjooyar-recaptcha' ),
			array( $this, 'add_active_comment_form_field' ),
			$this->page_id,
			$this->section_general,
			array()
		);
		register_setting( $this->page_id, $this->setting_active_comment_form );

	}


	/**
	 * Add SiteKey setting field
	 */
	public function add_sitekey_field() {
		$sitekey = get_option( $this->setting_sitekey );
		?>
		<input type='text' name='<?php echo esc_attr( $this->setting_sitekey ); ?>' value='<?php echo esc_attr( $sitekey ); ?>' class='regular-text code'>
		<?php
	}

	/**
	 * add SecretKey setting field
	 */
	public function add_secretkey_field() {
		$secretkey = get_option( $this->setting_secretkey );
		?>
		<input type='text' name='<?php echo esc_attr( $this->setting_secretkey ); ?>' value='<?php echo esc_attr( $secretkey ); ?>' class='regular-text code'>
		<?php
	}

	/**
	 * add is_active setting field
	 */
	public function add_is_active_field() {
		$is_active = get_option( $this->setting_is_active, 0 );
		?>
		<input type='checkbox' <?php checked( $is_active );?> name='<?php echo esc_attr( $this->setting_is_active ); ?>' value='1' >
		<?php
	}

	/**
	 * add active_login setting field
	 */
	public function add_active_login_field() {
		$active_login = get_option( $this->setting_active_login, 0 );
		?>
		<input type='checkbox' <?php checked( $active_login );?> name='<?php echo esc_attr( $this->setting_active_login ); ?>' value='1' >
		<?php
	}

	/**
	 * add active_registration setting field
	 */
	public function add_active_registration_field() {
		$active_registration = get_option( $this->setting_active_registration, 0 );
		?>
		<input type='checkbox' <?php checked( $active_registration );?> name='<?php echo esc_attr( $this->setting_active_registration ); ?>' value='1' >
		<?php
	}

	/**
	 * add active_reset_password setting field
	 */
	public function add_active_reset_password_field() {
		$active_reset_password = get_option( $this->setting_active_reset_password, 0 );
		?>
		<input type='checkbox' <?php checked( $active_reset_password );?> name='<?php echo esc_attr( $this->setting_active_reset_password ); ?>' value='1' >
		<?php
	}

	/**
	 * add active_comment_form setting field
	 */
	public function add_active_comment_form_field() {
		$active_comment_form = get_option( $this->setting_active_comment_form, 0 );
		?>
		<input type='checkbox' <?php checked( $active_comment_form );?> name='<?php echo esc_attr( $this->setting_active_comment_form ); ?>' value='1' >
		<?php
	}

	/**
	 * add whitelist setting textarea field
	 */
	public function add_whitelist_field() {
		$whitelist = get_option( $this->setting_whitelist );
		?>
		<textarea rows="6" name='<?php echo esc_attr( $this->setting_whitelist ); ?>' class='regular-text code'><?php echo esc_textarea( $whitelist );?></textarea>
		<?php
	}

	/**
	 * add Language setting field
	 */
	public function add_language_field() {
		$language = get_option( $this->setting_language );
		?>
		<select name="<?php echo esc_attr( $this->setting_language ); ?>">
			<?php foreach( $this->languages as $id => $label ):?>
				<option <?php selected( $id, $language );?> value="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?></option>
			<?php endforeach;?>
		</select>
		<?php
	}

}