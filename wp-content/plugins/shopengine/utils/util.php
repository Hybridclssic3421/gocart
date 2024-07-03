<?php
namespace ShopEngine\Utils;

defined( 'ABSPATH' ) || exit;
/**
 * Global helper class.
 *
 * @since 1.0.0
 */

class Util{

	public static $instance = null;
	private static $key     = 'shopengine_options';
	
	public static function get_option( $key, $default = '' ) {
		$data_all = get_option( self::$key );
		return ( isset( $data_all[ $key ] ) && $data_all[ $key ] != '' ) ? $data_all[ $key ] : $default;
	}

	public static function save_option( $key, $value = '' ) {
		$data_all         = get_option( self::$key );

		$data_all[ $key ] = $value;
		return update_option(  self::$key, $data_all );
	}

	public static function get_settings( $key, $default = '' ) {
		$data_all = self::get_option( 'settings', array() );
		return ( isset( $data_all[ $key ] ) && $data_all[ $key ] != '' ) ? $data_all[ $key ] : $default;
	}

	public static function save_settings( $new_data = '' ) {
		$data_old = self::get_option( 'settings', array() );
		$data     = array_merge( $data_old, $new_data );
		return self::save_option( 'settings', $data );
	}

	public static function shopengine_admin_action() {
		
		$status = '';
		
		// Check for nonce security
		if (!isset($_POST['nonce']) || ! wp_verify_nonce(  sanitize_text_field( wp_unslash($_POST['nonce']) ) ) ) {
			return;
		}
		
		// manage capability check
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	
		if ( isset( $_POST['settings'] ) ) {
			$status = self::save_settings( empty( $_POST['settings'] ) ? array() : map_deep( wp_unslash( $_POST['settings'] ) , 'sanitize_text_field' )  ); 
		}
		
		if(trim($status)){
			wp_send_json_success();
		}else{
			wp_send_json_error();
		}

		exit; 
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {

			// Fire the class instance
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function banner_consent(){
		include_once "user-consent-banner/consent-check-view.php";
	}
	
}