<?php
/*
Simple:Press Admin
Ajax form license-check - Toolbox
$LastChangedDate: 2018-11-02 13:02:17 -0500 (Fri, 02 Nov 2018) $
$Rev: 15795 $
*/


if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

require_once SP_PLUGIN_DIR.'/admin/panel-toolbox/spa-toolbox-display.php';
include_once SP_PLUGIN_DIR.'/admin/panel-toolbox/support/spa-toolbox-prepare.php';
include_once SP_PLUGIN_DIR.'/admin/panel-toolbox/support/spa-toolbox-save.php';
include_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

global $adminhelpfile;

$adminhelpfile = 'admin-toolbox';

# --------------------------------------------------------------------

# ----------------------------------
# Check Whether User Can Manage Options
if (!SP()->auths->current_user_can('SPF Manage Toolbox')) die();


if($_POST['sp_action'] == 'activate_license' || $_POST['sp_action'] == 'deactivate_license'){

	$license_key = trim( $_POST['licence_key'] );
	
	$item_name = trim( $_POST['item_name'] );
	
	if($_POST['sp_item'] == 'sp_check_pugin'){
		
		$update_key_option 		= 'plugin_'.str_replace(' ', '-', strtolower($item_name));
		$update_status_option 	= 'spl_plugin_stats_'.str_replace(' ', '-', strtolower($item_name));
		$update_info_option 	= 'spl_plugin_info_'.str_replace(' ', '-', strtolower($item_name));
		
	}else{
		
		$update_key_option 		= 'theme_'.str_replace(' ', '-', strtolower($item_name));
		$update_status_option 	= 'spl_theme_stats_'.str_replace(' ', '-', strtolower($item_name));
		$update_info_option 	= 'spl_theme_info_'.str_replace(' ', '-', strtolower($item_name));
	}
	
	/* in SP_Addon_STORE Apis
	 * 
	 * check_license - Used to remotely check if a license key is activated, valid, and not expired
	 * 
	 * activate_license - Used to remotely activate a license key
	 * 
	 * deactivate_license - Used to remotely deactivate a license key
	 * 
	 * get_version - Used to remotely retrieve the latest version information for a product
	 * 
	 */
	 
	// save key to option table
	SP()->options->update($update_key_option, $license_key);
	
	if($_POST['sp_action'] == 'activate_license'){
		
		// data to send in our API request
		$api_params = array(
		
			'edd_action'=> 'activate_license',
			'license' 	=> $license_key,
			'url'       => home_url()
		);
		
		
		
	}elseif($_POST['sp_action'] == 'deactivate_license'){
		
		// data to send in our API request
		$api_params = array(
		
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license_key,
			'url'       => home_url()
		);
	}
	
	if($_POST['sp_itemn_id'] == ''){
					
		$api_params['item_name'] = urlencode($item_name);  // the name of our product in SP_Addon_STORE
		
	}else{
		
		$api_params['item_id'] = $_POST['sp_itemn_id'];  // id of this plugin in SP_Addon_STORE
	}
	
	
	$response = wp_remote_post( SP_Addon_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
	
	// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
	
		if ( is_wp_error( $response ) ) {
			
			$message = $response->get_error_message();
			
		} else {
			
			$message = 'An error occurred, please try again.';
		}
	
	} else {
	
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	
		if(isset($license_data->license)) {
			
			if( $license_data->license == 'deactivated' ) {
				
				// delete status from option table
				SP()->options->delete( $update_status_option );
				
				// delete info from option table
				SP()->options->delete( $update_info_option );
				
				$message = __( 'Your license key is deactivated.' );
				
			}else{
				
				// save status to option table
				SP()->options->update( $update_status_option, $license_data->license );
				
				// save info to option table
				SP()->options->update( $update_info_option, wp_remote_retrieve_body( $response ) );
			}
		}
	
		if ( false === $license_data->success ) {
	
			switch( $license_data->error ) {
	
				case 'expired' :
	
					$message = sprintf(
						__( 'Your license key expired on %s.' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;
	
				case 'revoked' :
	
					$message = __( 'Your license key has been disabled.' );
					break;
	
				case 'missing' :
	
					$message = __( 'Plugin license is invalid. Please be sure you have entered right plugin license key.' );
					break;
	
				case 'invalid' :
				case 'site_inactive' :
	
					$message = __( 'Your license is not active for this URL.' );
					break;
	
				case 'item_name_mismatch' :
	
					$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $item_name );
					break;
	
				case 'no_activations_left':
	
					$message = __( 'Your license key has reached its activation limit.' );
					break;
	
				default :
	
					$message = __( 'An error occurred, please try again.' );
					break;
			}
		}
		
	}
	
	if($message == '' && $_POST['sp_action'] == 'activate_license'){
		
		$message = 'License successfully Activated. Thank you.';
	}
	
	$result = array('message'=>$message, 'sp_item' => $_POST['sp_item']);
	
	echo json_encode($result);
	
}elseif($_POST['sp_action'] == 'save_store_url'){
	
	SP()->options->update('sp_addon_store_url', $_POST['sp_sample_store_url']);
	
	$message = 'Updated option Successfully.';
	
	$result = array('message'=>$message);
	
	echo json_encode($result);
}

die();

?>