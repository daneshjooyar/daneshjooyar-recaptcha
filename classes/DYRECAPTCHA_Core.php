<?php

/**
* Core of Plugin
*/
class DYRECAPTCHA_Core
{

	/**
	 * Plugin version
	 * @var string
	 */
	protected $version 	= '1.0.0';

	/**
	 * Settings object
	 * @var DYRECAPTCHA_Settings
	 */
	protected $settings;

	/**
	 * Recaptcha js file from google server
	 * @var String
	 */
	protected $recaptcha_script;
	
	function __construct( )
	{
		//Instatiation setting object
		$this->settings = new DYRECAPTCHA_Settings();

		//set google recaptcha script with custom language from settings
		$this->recaptcha_script = 'https://www.google.com/recaptcha/api.js?hl=' . $this->settings->get_language();
	}

	/**
	 * Run core of plugin
	 * @return Void
	 */
	public function run() {

		//Active Setting page in admin area
		$this->settings->active();

		//Check for active recaptcha
		if( $this->settings->is_active() && ! $this->settings->is_in_whitelist( $this->get_real_user_ip() ) ){

			if( $this->settings->is_active_comment_form() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'add_recaptcha_script' ) );
				add_action( 'comment_form_after_fields', array( $this, 'add_recaptcha' ) );
				add_action( 'pre_comment_on_post', array( $this, 'authentication_recaptcha' ) );
				return;
			}

			//If not comment or not in front-end enqueue script and style in login page 
			add_action( 'login_enqueue_scripts', array( $this, 'add_recaptcha_script' ) );
			add_action( 'login_enqueue_scripts', array( $this, 'add_recaptcha_style' ));
			
			if( $this->settings->is_active_login() ) {
				add_action( 'login_form', array( $this, 'add_recaptcha' ) );
				add_filter( 'authenticate', array( $this, 'authentication_recaptcha' ), 50 );
			}

			if( $this->settings->is_active_registration() ) {
				add_action( 'register_form', array( $this, 'add_recaptcha' ));
    			add_filter( 'registration_errors', array( $this, 'authentication_recaptcha' ), 50 );
			}

			if( $this->settings->is_active_reset_password() ) {
				add_action( 'lostpassword_form', array( $this, 'add_recaptcha' ) );
				add_filter( 'allow_password_reset', array( $this, 'authentication_recaptcha') );
			}

		}

	}

	/**
	 * Get real user ip
	 * @return String remote ip of user or visitor
	 */
	public function get_real_user_ip(){
	    switch(true){
	      case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
	      case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
	      case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
	      default : return $_SERVER['REMOTE_ADDR'];
	    }
	 }

	/**
	 * Add recaptcha field in specific places
	 */
	public function add_recaptcha() {
		if( $this->settings->is_active() ) {
			echo '<div class="g-recaptcha" data-sitekey="' . $this->settings->get_sitekey() . '"></div>';
		}
	}

	/**
	 * Add few style for recaptcha iframe
	 */
	public function add_recaptcha_style() {
		?>
		<style type="text/css">
			.g-recaptcha{
				margin-right: -15px !important;
				margin-bottom: 5px !important;
			}
		</style>
		<?php
	}

	/**
	 * Enqueue require js file for recaptcha
	 */
	public function add_recaptcha_script() {
		wp_enqueue_script( 'g-recaptcha', $this->recaptcha_script, array(), $this->version, false );
	}

	/**
	 * Check recaptcha authentication
	 * @param  null|WP_User|WP_Error|Boolean 	$user 	just boolean is 'allow_password_reset' filter
	 * @return null|WP_User|WP_Error|Boolean 			just boolean is 'allow_password_reset' filter
	 */
	public function authentication_recaptcha( $user ) {
		
		//Check for if is login authenticate process and submit from login form
		if( current_filter() === 'authenticate' && ( ! isset( $_POST['log'] ) || ! isset( $_POST['pwd'] ) ) ) {
			return $user;
		}

		if( ! $this->settings->is_active() ){
			return $user;
		}

		$google_site_verify_url = 'https://www.google.com/recaptcha/api/siteverify';

		$params = array(
			'secret' 	=> $this->settings->get_secretkey(),
			'response'	=> $_POST['g-recaptcha-response'],
			'remoteip'	=> $this->get_real_user_ip()
		);

		$response = wp_remote_get( add_query_arg( $params, $google_site_verify_url ) );

		if( ! is_wp_error( $response ) ){
			if( wp_remote_retrieve_response_code( $response ) == 200 ){
				$result = json_decode( wp_remote_retrieve_body( $response ) );
				if( $result->success == true ){
					return $user;
				}else{

					//Specific wp_die for comment form
					if( current_filter() === 'pre_comment_on_post' ) {
						wp_die( __( 'ERROR', 'daneshjooyar-recaptcha' ) . ':&nbsp;' . __( 'You have entered an incorrect reCAPTCHA value. Click the BACK button on your browser, and try again.', 'daneshjooyar-recaptcha' ) );
					}

					//Specific return boolean for 'allow_password_reset' filter
					if( current_filter() === 'allow_password_reset' ){
						return false;
					}

					if( is_wp_error( $user ) ){
						$user->add( 'recaptcha_error', __( '<strong>Erro</strong>:Recaptcha response is not correct, Please try again.', 'daneshjooyar-recaptcha' ) );
						return $user;
					}else{
						return new WP_Error( 'recaptcha_error', __( '<strong>Erro</strong>:Recaptcha response is not correct, Please try again.', 'daneshjooyar-recaptcha' ) );
					}
				}
			}
		}
		return $user;
	}

}