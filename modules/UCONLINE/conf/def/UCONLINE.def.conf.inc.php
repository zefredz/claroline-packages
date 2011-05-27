<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 1.2.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'UCONLINE';
$conf_def['config_file'] = 'UCONLINE.conf.php';
$conf_def['config_name'] = 'Who is online';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'UCONLINE_refreshTime' ,'UCONLINE_displayRefreshTime' );

$conf_def['section']['display']['label']      = 'Display';
$conf_def['section']['display']['description']= '';
$conf_def['section']['display']['properties'] = array ( 'UCONLINE_showUserId'
                                                      , 'UCONLINE_showEmail'
                                                      , 'UCONLINE_showSendMessage'
                                                      , 'UCONLINE_showStatus'
                                                      , 'UCONLINE_showSkypeStatus'
                                                      , 'UCONLINE_showUserPicture'
                                                      , 'UCONLINE_showLocalTime'
                                                      , 'UCONLINE_privacy'
                                                      , 'UCONLINE_usersPerPage' );

// MAIN
$conf_def_property_list[ 'UCONLINE_refreshTime' ] = 
 array ( 'label'       => 'Refresh time'
       , 'description' => '5 is a good value for this. (Minimum 1 minute; Maximum 60 minutes)'
       , 'default'     => '5'
       , 'unit'        => 'minutes'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 60)
       );

$conf_def_property_list[ 'UCONLINE_displayRefreshTime' ] = 
 array ( 'label'       => 'Display refresh time'
       , 'description' => 'The refresh time for displaying the amount of online users'
       , 'default'     => '60'
       , 'unit'        => 'seconds'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 10
                                  , 'max'=> 3600 )
       );

// DISPLAY
$conf_def_property_list[ 'UCONLINE_showUserId' ] =
array ( 'label'       => 'Show user id'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showSendMessage' ] =
array ( 'label'       => 'Show a link to send a message'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showEmail' ] =
array ( 'label'       => 'Show user email'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showStatus' ] =
array ( 'label'       => 'Show user status'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showSkypeStatus' ] =
array ( 'label'       => 'Show user\'s Skype account status'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showUserPicture' ] =
array ( 'label'       => 'Show user\'s picture'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_showLocalTime' ] =
array ( 'label'       => 'Show the local time of each user'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list[ 'UCONLINE_privacy' ] =
array ( 'label'       => 'privacy/security level'
      , 'description' => 'The users can see...'
      , 'default'     => '0'
      , 'type'        => 'enum'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array( '0' => 'all the other online users - security level : none'
                               , '1' => 'only the users registered in their own courses - security level : medium'
                               , '2' => '... except the courses with open registration - security level : high' )
      );

$conf_def_property_list[ 'UCONLINE_usersPerPage' ] =
array ( 'label'   => 'Number of users per page'
      , 'default' => '25'
      , 'unit'    => 'users'
      , 'type'    => 'integer'
      , 'display'     => TRUE      
      , 'readonly'    => FALSE      
      , 'acceptedValue' => array ('min'=>'5')
      );
