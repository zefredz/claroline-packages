<?php

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'ICSURVEW';
$conf_def['config_file'] = 'ICSURVEW.conf.php';
$conf_def['config_name'] = 'iCampus course survey';

$conf_def['section']['main']['label']       = 'Main';
$conf_def['section']['main']['description'] = '';
$conf_def['section']['main']['properties']  = array ( 'ICSURVEW_postpone_allowed' );

$conf_def_property_list[ 'ICSURVEW_postpone_allowed' ] =
array ( 'label'       => get_lang( 'Users can delay their answer' )
      , 'description' => ''
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );