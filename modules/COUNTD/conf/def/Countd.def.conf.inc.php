<?php // $Id$
/**
 * CLAROLINE
 *
 * This file describe the parameters for Countdown module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Marc Lavergne <marc86.lavergne@gmail.com>
 *
 * @package Countd
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'Countd';
$conf_def['config_file'] = 'Countd.conf.php';
$conf_def['config_name'] = 'Countdown';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'Year'
      , 'Month'
      , 'Day'
      , 'Hour'
      , 'Minute'
      , 'Second'
      , 'Display'
      , 'Alert'
      , 'Passed'
      , 'bgcolor'
      );

// PROPERTIES

$conf_def_property_list['Year'] =
array ( 'label'       => 'Year'
      , 'description' => ''
      , 'default'     => '2010'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
      
$conf_def_property_list['Month'] =
array ( 'label'       => 'Month'
      , 'description' => ''
      , 'default'     => '1'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['Day'] =
array ( 'label'       => 'Day'
      , 'description' => ''
      , 'default'     => '1'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
            
$conf_def_property_list['Hour'] =
array ( 'label'       => 'Hour'
      , 'description' => ''
      , 'default'     => '1'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE        
      );    

$conf_def_property_list['Minute'] =
array ( 'label'       => 'Minute'
      , 'description' => ''
      , 'default'     => '1'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE     
      );   

$conf_def_property_list['Second'] =
array ( 'label'       => 'Second'
      , 'description' => ''
      , 'default'     => '1'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE     
      );   

$conf_def_property_list['Display'] =
array ( 'label'       => 'Display'
      , 'description' => 'Message displayed'
      , 'default'     => 'un évènement spécial'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE     
      );   

$conf_def_property_list['Alert'] =
array ( 'label'       => 'Alert'
      , 'description' => 'Message to be displayed at the end'
      , 'default'     => 'C\'est aujourd\'hui !'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE     
      );  

$conf_def_property_list['Passed'] =
array ( 'label'       => 'Passed'
      , 'description' => 'Message to be displayed when the day of the occasion has passed'
      , 'default'     => 'L\'évènement est déjà arrivé ! '
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE 
      );  

$conf_def_property_list['bgcolor'] =
array ( 'label'       => 'bgcolor'
      , 'description' => 'back ground color'
      , 'default'     => 'transparent'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE 
      );  
?>
