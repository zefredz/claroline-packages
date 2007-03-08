<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package CLONLINE
 *
 */
$conf_def['config_code'] = 'CLONLINE';
$conf_def['config_file'] = 'CLONLINE.conf.php';
$conf_def['config_name'] = 'Who is online';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] =
array ( 'refreshTime'
      );

$conf_def['section']['display']['label']      = 'Display';
$conf_def['section']['display']['description']= '';
$conf_def['section']['display']['properties'] =
array ( 'showUserId'
      , 'showEmail'
      , 'showStatus'
      );

// MAIN
$conf_def_property_list['refreshTime'] =
array ( 'label'       => 'Refresh time'
      , 'description' => '15 is a good value for this. Will not work if bigger than php session lifetime (see server configuration)'
      , 'default'     => '15'
      , 'unit'        => 'minutes'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

// DISPLAY
$conf_def_property_list['showUserId'] =
array ( 'label'       => 'Show user id'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );
  
$conf_def_property_list['showEmail'] =
array ( 'label'       => 'Show user email'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );         

$conf_def_property_list['showStatus'] =
array ( 'label'       => 'Show user status'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );  
                     
?>
