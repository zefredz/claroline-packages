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

$conf_def['section']['display']['label']      = 'Display';
$conf_def['section']['display']['description']= '';
$conf_def['section']['display']['properties'] =
array ( 'showUserId'
      , 'showEmail'
      , 'showStatus'
      , 'usersPerPage'
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

$conf_def_property_list['usersPerPage'] =
array ( 'label'   => 'Number of users per page'
      , 'default' => '25'
      , 'unit'    => 'users'
      , 'type'    => 'integer'
      , 'display'     => TRUE      
      , 'readonly'    => FALSE      
      , 'acceptedValue' => array ('min'=>'5')
      );                     

?>
