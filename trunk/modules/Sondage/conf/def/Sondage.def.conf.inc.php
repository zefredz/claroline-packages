<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for showip module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package SONDAGE
 *
 * TODO : add support of multiple selected colors ["0000FF","0000FF","0000CC","0000CC"]
 */
$conf_def['config_code'] = 'Sondage';
$conf_def['config_file'] = 'Sondage.conf.php';
$conf_def['config_name'] = 'Sondage';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'Message'
	   ,'Error_user'
	   ,'Error_input'
	   ,'User_select'
      );


// PROPERTIES

$conf_def_property_list['Message'] =
array ( 'label'       => 'Text to show'
      , 'description' => 'Message shown to the user'
      , 'default'     => 'Aidez nous à développer le site. Postez vos remarques.'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['Error_user'] =
array ( 'label'       => 'Unregistred user'
      , 'description' => ''
      , 'default'     => 'Il faut être logué'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['Error_input'] =
array ( 'label'       => 'No input'
      , 'description' => ''
      , 'default'     => 'Introduisez quelque chose'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['User_select'] =
array ( 'label'       => 'Allow unregistred users'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );
?>
