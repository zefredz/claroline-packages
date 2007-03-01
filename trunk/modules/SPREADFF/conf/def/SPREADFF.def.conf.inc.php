<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for SPREADFF module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package SPREADFF
 */
$conf_def['config_code'] = 'SPREADFF';
$conf_def['config_file'] = 'SPREADFF.conf.php';
$conf_def['config_name'] = 'Spread Firefox';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'spreadff_img_url'
      , 'spreadff_link'
      , 'spreadff_show_in_all_browsers'      
      );

// PROPERTIES

$conf_def_property_list['spreadff_img_url'] =
array ( 'label'       => 'Image url'
      , 'description' => ''
      , 'default'     => 'http://sfx-images.mozilla.org/affiliates/Buttons/firefox2/ff2b80x15.gif'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['spreadff_link'] =
array ( 'label'       => 'Link'
      , 'description' => ''
      , 'default'     => 'http://www.mozilla.com/en-US/firefox/'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['spreadff_show_in_all_browsers'] =
array ( 'label'       => 'Show in all browsers'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );      
                
?>
