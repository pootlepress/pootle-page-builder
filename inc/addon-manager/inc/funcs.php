<?php
/**
 * Created by shramee
 * At: 9:04 PM 14/8/15
 */

/**
 * Output the error info from code
 * @param int $code The error code
 * @return array|bool Error info or false
 */
function ppb_am_error_info( $code ) {
	switch ( $code ) {
		case '100':
			return array( 'api_email_text', 'api_email_error' );
		case '101':
			return array( 'api_key_text', 'api_key_error' );
		case '102':
			return array( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error' );
		case '103':
			return array( 'api_key_exceeded_text', 'api_key_exceeded_error' );
		case '104':
			return array( 'api_key_not_activated_text', 'api_key_not_activated_error' );
		case '105':
			return array( 'api_key_invalid_text', 'api_key_invalid_error' );
		case '106':
			return array( 'sub_not_active_text', 'sub_not_active_error' );
		default:
			return false;
	}
}

/**
 * Displays an admin error message in the WordPress dashboard
 * @param  object $response
 * @param Pootle_Page_Builder_Addon_Update_Check $this Instance
 * @return string
 */
function ppb_am_check_response_for_errors( $response, $this ) {

	if ( ! empty( $response ) ) {

		if ( isset( $response->errors['no_key'] ) && $response->errors['no_key'] == 'no_key' && isset( $response->errors['no_subscription'] ) && $response->errors['no_subscription'] == 'no_subscription' ) {

			add_action('admin_notices', array( $this, 'no_key_error_notice') );
			add_action('admin_notices', array( $this, 'no_subscription_error_notice') );

		} else if ( isset( $response->errors['exp_license'] ) && $response->errors['exp_license'] == 'exp_license' ) {

			add_action('admin_notices', array( $this, 'expired_license_error_notice') );

		}  else if ( isset( $response->errors['hold_subscription'] ) && $response->errors['hold_subscription'] == 'hold_subscription' ) {

			add_action('admin_notices', array( $this, 'on_hold_subscription_error_notice') );

		} else if ( isset( $response->errors['cancelled_subscription'] ) && $response->errors['cancelled_subscription'] == 'cancelled_subscription' ) {

			add_action('admin_notices', array( $this, 'cancelled_subscription_error_notice') );

		} else if ( isset( $response->errors['exp_subscription'] ) && $response->errors['exp_subscription'] == 'exp_subscription' ) {

			add_action('admin_notices', array( $this, 'expired_subscription_error_notice') );

		} else if ( isset( $response->errors['suspended_subscription'] ) && $response->errors['suspended_subscription'] == 'suspended_subscription' ) {

			add_action('admin_notices', array( $this, 'suspended_subscription_error_notice') );

		} else if ( isset( $response->errors['pending_subscription'] ) && $response->errors['pending_subscription'] == 'pending_subscription' ) {

			add_action('admin_notices', array( $this, 'pending_subscription_error_notice') );

		} else if ( isset( $response->errors['trash_subscription'] ) && $response->errors['trash_subscription'] == 'trash_subscription' ) {

			add_action('admin_notices', array( $this, 'trash_subscription_error_notice') );

		} else if ( isset( $response->errors['no_subscription'] ) && $response->errors['no_subscription'] == 'no_subscription' ) {

			add_action('admin_notices', array( $this, 'no_subscription_error_notice') );

		} else if ( isset( $response->errors['no_activation'] ) && $response->errors['no_activation'] == 'no_activation' ) {

			add_action('admin_notices', array( $this, 'no_activation_error_notice') );

		} else if ( isset( $response->errors['no_key'] ) && $response->errors['no_key'] == 'no_key' ) {

			add_action('admin_notices', array( $this, 'no_key_error_notice') );

		} else if ( isset( $response->errors['download_revoked'] ) && $response->errors['download_revoked'] == 'download_revoked' ) {

			add_action('admin_notices', array( $this, 'download_revoked_error_notice') );

		} else if ( isset( $response->errors['switched_subscription'] ) && $response->errors['switched_subscription'] == 'switched_subscription' ) {

			add_action('admin_notices', array( $this, 'switched_subscription_error_notice') );
		}
	}
}

