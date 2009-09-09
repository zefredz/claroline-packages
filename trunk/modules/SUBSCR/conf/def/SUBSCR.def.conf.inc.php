<?php
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for the podcast tool
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */
 
$conf_def['config_code'] = 'SUBSCR';
$conf_def['config_file'] = 'SUBSCR.conf.php';
$conf_def['config_name'] = 'Subscription';

$conf_def['section']['main']['label']      = 'Main';

$conf_def['section']['main']['properties'] =
array ( 'allow_users_to_modify','export_csv_fields','session_incompatibility');
      


// GENERAL PROPERTIES
$conf_def_property_list['allow_users_to_modify'] =
array ( 'label'       => 'Allow users to modify their initial choice - default value'
      , 'description' => ''
      , 'default'     => 'true'
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
      
$conf_def_property_list['export_csv_fields'] =
array ( 'label'        => 'Fields for the CSV export'
      , 'description'  => ''
		,'default'   	  => array('officialCode','username','name','firstname','subscription_date','email','phoneNumber')
      ,'type'      	  => 'multi'
      ,'display'       => TRUE
      ,'readonly'      => FALSE
      ,'acceptedValue' => array ( 'officialCode'=> get_lang('officialCode'),
                                  'username' => get_lang('username'),
                                  'name' => get_lang('name'),
                                  'firstname' => get_lang('firstname'),
                                  'subscription_date' => get_lang('subscription date'),
				  'email' => get_lang('email'),
				  'phoneNumber' => get_lang('phoneNumber')
                                )
      );
      
$conf_def_property_list['session_incompatibility'] =
array ( 'label'       => 'Session incompatibility'
      , 'description' => 'The subscription to a session can be incompatible with other sessions'
      , 'default'     => 'true'
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
?>
