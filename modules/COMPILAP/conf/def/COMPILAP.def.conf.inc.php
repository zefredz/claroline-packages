<?php
if ( count( get_included_files() ) == 1 ) die( '---' );
/*
Applet COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/
 
$conf_def['config_code'] = 'COMPILAP';
$conf_def['config_file'] = 'COMPILAP.conf.php';
$conf_def['config_name'] = 'Applet pour Compilatio';

$conf_def['section']['main']['label'] = 'Main';

$conf_def['section']['main']['properties'] =
array ('compi_active','clef_compilatio');
      


$conf_def_property_list['compi_active'] =
array ( 'label'       => 'Voir le lien vers Compilatio dans les travaux'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );
      
      
      
      /* GENERAL PROPERTIES*/
$conf_def_property_list['clef_compilatio'] =
array ( 'label'   => 'Votre clef d\'identification Compilatio'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );
?>
