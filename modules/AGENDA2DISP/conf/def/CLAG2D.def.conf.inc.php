<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLAG2D';
$conf_def['config_file'] = 'CLAG2D.conf.php';
$conf_def['config_name'] = 'CLAG2D';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'activate_personal'
      );

// PROPERTIES

$conf_def_property_list['activate_personal'] =
array ( 'label'       => 'Activate personal events function'
      , 'description' => 'This function allows a logged user to create personal events'
      , 'default'     => TRUE
      , 'type'        => 'boolean'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
      );
?>
