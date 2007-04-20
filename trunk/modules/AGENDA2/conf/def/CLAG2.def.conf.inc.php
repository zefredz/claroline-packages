<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for Agenda2 module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package CLAG2
 *
 */
$conf_def['config_code'] = 'CLAG2';
$conf_def['config_file'] = 'CLAG2.conf.php';
$conf_def['config_name'] = 'Agend2';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'activate_shared_event',
		'activate_auto_delete',
		'auto_delete_timestamp'
      );


// PROPERTIES

$conf_def_property_list['activate_shared_event'] =
array ( 'label'       => 'Activate shared events function'
      , 'description' => 'This function allows a teacher to create an event for some of his users or a user to create an event for a group'
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list['activate_auto_delete'] =
array ( 'label'       => 'Activate automatic delete function'
      , 'description' => 'This function will delete all events older than a certain number of days'
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list['auto_delete_timestamp'] =
array ( 'label'       => 'Number of days before deletion'
      , 'description' => 'This function allows a teacher to create an event for some of his users or a user to create an event for a group'
      , 'default'     => '30'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
?>