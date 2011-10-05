<?php
/*
Module COMPILATIO v1.6 pour Claroline 
Compilatio - www.compilatio.net
*/

/*
script contenant les éléments de paramétrage du module compilatio pour claroline
 */

if ( count( get_included_files() ) == 1 ) die( '---' );
 
$conf_def['config_code'] = 'COMPILAT';
$conf_def['config_file'] = 'COMPILAT.conf.php';
$conf_def['config_name'] = 'compilatio';

$conf_def['section']['main']['label']      = 'Main';

$conf_def['section']['main']['properties'] =
array ( 'clef_compilatio',
    'using_SSL',
    'soap_proxy_host',
    'soap_proxy_port',
    'mode_transport',
    'wget_uri',
    'wget_login',
    'wget_password',
		'using_CAS',
		'host_CAS',
		'port_CAS',
		'uri_CAS',
		'version_CAS',
		'using_debug'
		);

/* GENERAL PROPERTIES*/
$conf_def_property_list['clef_compilatio'] =
array ( 'label'   => 'Votre clef d\'identification Compilatio'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );


$conf_def_property_list['soap_proxy_host'] =
array ( 'label'   => 'Adresse du proxy pour SOAP'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );
	  
$conf_def_property_list['soap_proxy_port'] =
array ( 'label'   => 'Port du proxy pour SOAP'
      , 'default' => ''
      , 'type'    => 'string'
      ,'acceptedValue' => array ('Min'=>'1')
      );


$conf_def_property_list['using_SSL'] =
array ( 'label'       => 'Utilisation en SSL (https)'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );


$conf_def_property_list['mode_transport'] =
array ( 'label'       => 'Mode de transport des données'
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Envoi des fichiers par Soap', 'FALSE' => 'Récupération des fichiers par téléchargement')
      );

	  
$conf_def_property_list['wget_uri'] =
array ( 'label'   => 'Si transport par récupération, URL spéciales de transport (URL normale de Claroline par défaut)'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );	  

$conf_def_property_list['wget_login'] =
array ( 'label'   => 'Login si URL protégée par mot de passe'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );	
      
$conf_def_property_list['wget_password'] =
array ( 'label'   => 'Mot de passe'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );	     
  
	  
$conf_def_property_list['using_CAS'] =
array ( 'label'       => 'Utilisation de l\'authentification CAS'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );

$conf_def_property_list['host_CAS'] =
array ( 'label'   => 'Adresse du serveur CAS'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );
	  
$conf_def_property_list['port_CAS'] =
array ( 'label'   => 'Port utilisé par le serveur CAS'
      , 'default' => 0
      , 'type'    => 'integer'
      , 'acceptedValue' => array ('Min'=>'2')
      );

$conf_def_property_list['uri_CAS'] =
array ( 'label'   => 'Uri du serveur CAS'
      , 'default' => ''
      , 'type'    => 'string'
      , 'acceptedValue' => array ('Min'=>'5')
      );

$conf_def_property_list['version_CAS'] =
array ( 'label'   => 'Version du serveur CAS (1 ou 2) '
      , 'default' => 2
      ,  'type'    => 'integer'
      ,'acceptedValue' => array ('Min'=>'1')
      );
      
$conf_def_property_list['using_debug'] =
array ( 'label'       => 'Mode debug'
      , 'description' => ''
      , 'default'     => FALSE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );
      
?>
