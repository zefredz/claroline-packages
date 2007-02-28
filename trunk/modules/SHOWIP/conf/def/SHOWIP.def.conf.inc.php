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
 * @package SHOWIP
 *
 * TODO : add support of multiple selected colors ["0000FF","0000FF","0000CC","0000CC"]
 */
$conf_def['config_code'] = 'SHOWIP';
$conf_def['config_file'] = 'SHOWIP.conf.php';
$conf_def['config_name'] = 'Show ip';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'showip_text'
      , 'showip_borderColor'
      , 'showip_bgColor'
      , 'showip_textColor' 
      );

// PROPERTIES

$conf_def_property_list['showip_text'] =
array ( 'label'       => 'Text to show'
      , 'description' => 'ex : "Your ip address is %ip". Use %ip as a keyword to display the address at the good place.'
      , 'default'     => '%ip'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

/* Colors scheme : use a regexp to check that string is an hexadecimal value */     
 
$conf_def_property_list['showip_borderColor'] =
array ( 'label'       => 'Border color'
      , 'description' => 'ex : 669933'
      , 'default'     => '669933'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'         
      );
      
$conf_def_property_list['showip_bgColor'] =
array ( 'label'       => 'Background color'
      , 'description' => 'ex : FFFFFF'
      , 'default'     => 'FFFFFF'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'       
      );      


$conf_def_property_list['showip_textColor'] =
array ( 'label'       => 'Text color'
      , 'description' => 'ex : 444444'
      , 'default'     => '444444'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE 
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'        
      );                   
?>
