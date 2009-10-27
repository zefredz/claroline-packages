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
      ,'acceptedValue' => array('TRUE' => 'TRUE', 'FALSE' => 'FALSE')
      );
      
$conf_def_property_list['export_csv_fields'] =
array ( 'label'        => 'Fields for the CSV export'
      , 'description'  => 'Which fields in the export file ?'
		,'default'   	  => array('officialCode','username','name','firstname','subscription_date','email','phoneNumber')
      ,'type'      	  => 'multi'
      ,'display'       => TRUE
      ,'readonly'      => FALSE
      ,'acceptedValue' => array ( 'officialCode'=> 'officialCode',
                                  'username' => 'username',
                                  'name' => 'name',
                                  'firstname' => 'firstname',
                                  'subscription_date' => 'subscription date',
				  				  'email' => 'email',
				  				  'phoneNumber' => 'phoneNumber'
                                )
      );
      
$conf_def_property_list['session_incompatibility'] =
array ( 'label'       => 'Session incompatibility'
      , 'description' => 'The subscription to a session can be incompatible with other sessions'
      , 'default'     => 'true'
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'TRUE', 'FALSE' => 'FALSE')
      );
?>
