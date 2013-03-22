<?php

// $Id$

$conf_def[ 'config_file' ]  = 'ICEPC.conf.php';
$conf_def[ 'config_code' ]  = 'ICEPC';
$conf_def[ 'config_name' ]  = 'EPC';
$conf_def[ 'config_class' ] = 'tool';


$conf_def[ 'section' ][ 'display' ][ 'label' ]      = 'Settings';
$conf_def[ 'section' ][ 'display' ][ 'properties' ] =
    array (
        'epcServiceUrl',
        'epcServiceUser',
        'epcServicePassword'
);

$conf_def_property_list[ 'epcServiceUrl' ] =
    array ( 'label'         => 'EPC service URL'
        , 'description'   => 'URL of the EPC web service'
        , 'default'       => ''
        , 'type'          => 'string'
        , 'display'       => TRUE
        , 'readonly'      => FALSE
        , 'technicalInfo' => 'EPC webservice URL'
);

$conf_def_property_list[ 'epcServiceUser' ] =
    array ( 'label'         => 'EPC user'
        , 'description'   => 'Name of the EPC web service user'
        , 'default'       => ''
        , 'type'          => 'string'
        , 'display'       => TRUE
        , 'readonly'      => FALSE
        , 'technicalInfo' => 'EPC webservice login'
);

$conf_def_property_list[ 'epcServicePassword' ] =
    array ( 'label'         => 'EPC password'
        , 'description'   => 'Password for the EPC web service user'
        , 'default'       => ''
        , 'type'          => 'string'
        , 'display'       => TRUE
        , 'readonly'      => FALSE
        , 'technicalInfo' => 'EPC webservice password'
);
