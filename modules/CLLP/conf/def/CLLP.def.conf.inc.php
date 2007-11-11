<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for CLJCHAT config file
 *
 * @version 1.8 $Revision: 130 $
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 * @see http://www.claroline.net/wiki/index.php/CLCHT
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package CLLP
 */

$conf_def['config_file'] = 'CLLP.conf.php';
$conf_def['config_code'] = 'CLLP';
$conf_def['config_name'] = 'Learning path';
$conf_def['config_class'] = 'tool';


$conf_def['section']['display']['label']='Display Settings';
$conf_def['section']['display']['properties'] =
array ( 'scorm_api_debug' );

$conf_def_property_list['scorm_api_debug'] =
array ( 'label'       => 'SCORM API debug mode'
      , 'description' => 'If setted debug messages from the SCORM API will be shown to course administrator in the learning path viewer'
      , 'default'       => 0
      , 'type'          => 'enum'
      , 'display'       => TRUE
      , 'readonly'      => FALSE
      , 'acceptedValue' => array ( 0 => 'No debug messages'
                               , 1 => 'Messages send by content'
                               , 2 => 'All messages'
                               )
      );


?>
