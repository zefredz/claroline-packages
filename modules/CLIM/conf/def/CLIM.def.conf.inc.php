<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Yassine Hassaine <yassinehassaine@gmail.com>
 *
 * @package CLIM
 *
 */
$conf_def['config_code'] = 'CLIM';
$conf_def['config_file'] = 'CLIM.conf.php';
$conf_def['config_name'] = 'Who is online';

 $conf_def['section']['main']['label']      = 'Main'; 	 
 $conf_def['section']['main']['description']= ''; 	 
 $conf_def['section']['main']['properties'] = 	 
 array ( 'CLIM_refreshTime' 	 
       );

$conf_def['section']['display']['label']      = 'Display';
$conf_def['section']['display']['description']= '';
$conf_def['section']['display']['properties'] =
array ( 'showUserId'
      , 'showEmail'
      , 'showStatus'
      , 'usersPerPage'
      );

// MAIN 	 
$conf_def_property_list['CLIM_refreshTime'] = 	 
 array ( 'label'       => 'Refresh time'
       , 'description' => '5 is a good value for this. (Minimum 1 minute; Maximum 60 minutes)'
       , 'default'     => '5' 	 
       , 'unit'        => 'minutes' 	 
       , 'type'        => 'integer' 	 
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 60)       
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
