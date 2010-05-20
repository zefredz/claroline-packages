<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for the podcast tool
 *
 * @author Jérôme Lambert <lambertjer@gmail.com>
 *
 * @package CLATT
 *
 */
 
$conf_def['config_code'] = 'CLATT';
$conf_def['config_file'] = 'CLATT.conf.php';
$conf_def['config_name'] = 'Attendance';

$conf_def['section']['main']['label']      = 'Main';

$conf_def['section']['main']['properties'] =
array ( 'allow_users_to_see','allow_export_csv','nbUsersPerPage','enable_entitled' );
      


// GENERAL PROPERTIES
$conf_def_property_list['allow_users_to_see'] =
array ( 'label'       => ucfirst(get_lang('allow users to see their attendance'))
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
	  , 'acceptedValue' => array ('TRUE'=>'Yes'
                              ,'FALSE'=>'No'
                              )
      );
	  
$conf_def_property_list['allow_export_csv'] =
array ( 'label'       => ucfirst(get_lang('allow to export attendance list'))
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
	  , 'acceptedValue' => array ('TRUE'=>'Yes'
                              ,'FALSE'=>'No'
                              )
      );
      
$conf_def_property_list['nbUsersPerPage'] =
array ( 'label'   => ucfirst(get_lang('number of user per page'))
      , 'default' => '25'
      , 'unit'    => 'users'
      ,  'type'    => 'integer'
      ,'acceptedValue' => array ('Min'=>'5')
      );	  

?>
