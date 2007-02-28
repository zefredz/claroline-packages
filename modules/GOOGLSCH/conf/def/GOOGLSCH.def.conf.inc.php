<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for Google Search module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package GOOGLSCH
 */
$conf_def['config_code'] = 'GOOGLSCH';
$conf_def['config_file'] = 'GOOGLSCH.conf.php';
$conf_def['config_name'] = 'Google Search';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'google_sch_on_this_campus'
      , 'google_sch_on_other_site'
      );


// PROPERTIES

$conf_def_property_list['google_sch_on_this_campus'] =
array ( 'label'       => 'Allow search on this campus'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list['google_sch_on_other_site'] =
array ( 'label'       => 'URL of another site to search on'
      , 'description' => 'Example : http://www.anotherdomain.tld/ Leave empty to ignore'
      , 'default'     => ''
      , 'type'        => 'url'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

?>