<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameters for SPREADOO module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package SPREADOO
 */
$conf_def['config_code'] = 'SPREADOO';
$conf_def['config_file'] = 'SPREADOO.conf.php';
$conf_def['config_name'] = 'Spread Open Office';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'spreadoo_img_url'
      , 'spreadoo_link'
      );

// PROPERTIES

$conf_def_property_list['spreadoo_img_url'] =
array ( 'label'       => 'Image url'
      , 'description' => ''
      , 'default'     => 'http://marketing.openoffice.org/art/galleries/marketing/web_buttons/nicu/80x15_1.png'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['spreadoo_link'] =
array ( 'label'       => 'Link'
      , 'description' => ''
      , 'default'     => 'http://www.openoffice.org/'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

                
?>
