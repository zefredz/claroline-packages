<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for Google AdSense module
 *
 * @copyright 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 * @package W3CVALID
 *
 * TODO : add support of multiple selected colors ["0000FF","0000FF","0000CC","0000CC"]
 */
$conf_def['config_code'] = 'W3CVALID';
$conf_def['config_file'] = 'W3CVALID.conf.php';
$conf_def['config_name'] = 'W3C validator';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'w3cxhml_text'
      , 'w3cxhml_borderColor'
      , 'w3cxhml_bgColor'
      , 'w3cxhml_textColor'
      );

// PROPERTIES

$conf_def_property_list['w3cxhml_text'] =
array ( 'label'       => 'Text to show'
      , 'description' => 'ex : check xhtml'
      , 'default'     => 'Check xhtml'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

/* Colors scheme : use a regexp to check that string is an hexadecimal value */

$conf_def_property_list['w3cxhml_borderColor'] =
array ( 'label'       => 'Border color'
      , 'description' => 'ex : 669933'
      , 'default'     => '669933'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'
      );

$conf_def_property_list['w3cxhml_bgColor'] =
array ( 'label'       => 'Background color'
      , 'description' => 'ex : FFFFFF'
      , 'default'     => 'FFFFFF'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'
      );


$conf_def_property_list['w3cxhml_textColor'] =
array ( 'label'       => 'Text color'
      , 'description' => 'ex : 444444'
      , 'default'     => '444444'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'
      );
?>