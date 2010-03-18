<?php
if (count(get_included_files()) == 1)
    die('---');
/**
 * CLAROLINE
 *
 * This file describe the parameters for the podcast tool
 *
 * @author Jérôme Lambert <lambertjer@gmail.com>
 *
 * @package CLPRES
 *
 */
$conf_def['config_code'] = 'CLPRES';
$conf_def['config_file'] = 'CLPRES.conf.php';
$conf_def['config_name'] = 'Presence';
$conf_def['section']['main']['label'] = 'Main';
$conf_def['section']['main']['properties'] = array('allow_users_to_see' , 'allow_export_csv' , 'nbUsersPerPage');
// GENERAL PROPERTIES
$conf_def_property_list['allow_users_to_see'] = array('label' => get_lang('Allow users to see their attendance') , 'description' => '' , 'default' => TRUE , 'type' => 'boolean' , 'display' => TRUE , 'readonly' => FALSE , 'acceptedValue' => array('TRUE' => 'Yes' , 'FALSE' => 'No'));
$conf_def_property_list['allow_export_csv'] = array('label' => get_lang('Allow to export attendance list') , 'description' => '' , 'default' => TRUE , 'type' => 'boolean' , 'display' => TRUE , 'readonly' => FALSE , 'acceptedValue' => array('TRUE' => 'Yes' , 'FALSE' => 'No'));
$conf_def_property_list['nbUsersPerPage'] = array('label' => 'Number of user per page' , 'default' => '25' , 'unit' => 'users' , 'type' => 'integer' , 'acceptedValue' => array('Min' => '5'));
// $conf_def_property_list['export_csv_fields'] =
// array ( 'label'        => 'Fields for the CSV export'
// , 'description'  => ''
// ,'default'   	  => array('officialCode','username','name','firstname','subscription_date','email','phoneNumber')
// ,'type'      	  => 'multi'
// ,'display'       => TRUE
// ,'readonly'      => FALSE
// ,'acceptedValue' => array ( 'officialCode'=> get_lang('officialCode'),
// 'username' => get_lang('username'),
// 'name' => get_lang('name'),
// 'firstname' => get_lang('firstname'),
// 'subscription_date' => get_lang('subscription date'),
// 'email' => get_lang('email'),
// 'phoneNumber' => get_lang('phoneNumber')
// )
// );
// $conf_def_property_list['session_incompatibility'] =
// array ( 'label'       => 'Session incompatibility'
// , 'description' => 'The subscription to a session can be incompatible with other sessions'
// , 'default'     => 'true'
// , 'type'        => 'boolean'
// , 'display'     => TRUE
// , 'readonly'    => FALSE
// );
?>
