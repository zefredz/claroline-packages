<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * @version 1.9 $Revision$
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
 * @package DIMDIM
 */

$conf_def['config_file'] = 'DIMDIM.conf.php';
$conf_def['config_code'] = 'DIMDIM';
$conf_def['config_name'] = 'Online conference';
$conf_def['config_class'] = 'tool';


$conf_def['section']['display']['label']='Connection settings';
$conf_def['section']['display']['properties'] =
array ( 'dimdim_server_url', 'dimdim_server_port' );

$conf_def_property_list['dimdim_server_url'] =
array ( 'label'       => 'Server url'
      , 'description' => ''
      , 'default'       => 'http://webmeeting.dimdim.com'
      , 'type'          => 'string'
      , 'display'       => TRUE
      , 'readonly'      => FALSE
      );

$conf_def_property_list['dimdim_server_port'] =
array ( 'label'       => 'Server port'
      , 'description' => ''
      , 'default'       => '80'
      , 'type'          => 'int'
      , 'display'       => TRUE
      , 'readonly'      => FALSE
      ,'acceptedValue' => array ( 'min' => '0' )
      );

?>