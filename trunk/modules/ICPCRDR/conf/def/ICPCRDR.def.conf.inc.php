<?php //$Id$
// CONFIG HEADER

$conf_def['config_code'] = 'ICPCRDR';
$conf_def['config_file'] = 'ICPCRDR.conf.php';
$conf_def['config_name'] = 'Podcast reader';

// CONFIG SECTIONS
$conf_def['section']['main']['label'] = 'Main';
$conf_def['section']['main']['description'] = '';
$conf_def['section']['main']['properties'] = array (
        'flowplayer_autoPlay',
        'flowplayer_autoBuffering',
        'displaySizeSelector'
);

// CONFIG PROPERTIES
$conf_def_property_list['flowplayer_autoPlay'] = array (
    'label' => 'Autoplay video in flowplayer',
    'description' => '',
    'default'     => FALSE,
    'type'        => 'boolean',
    'acceptedValue' => array (
        'TRUE'=>'Yes',
        'FALSE'=>'No'
    ),
    'display'     => TRUE,
    'readonly'    => FALSE
);

$conf_def_property_list['flowplayer_autoBuffering'] = array (
    'label' => 'Autoload video in flowplayer',
    'description' => '',
    'default'     => FALSE,
    'type'        => 'boolean',
    'acceptedValue' => array (
        'TRUE'=>'Yes',
        'FALSE'=>'No'
    ),
    'display'     => TRUE,
    'readonly'    => FALSE
);

$conf_def_property_list['displaySizeSelector'] = array (
    'label' => 'Display size selector',
    'description' => '',
    'default'     => FALSE,
    'type'        => 'boolean',
    'acceptedValue' => array (
        'TRUE'=>'Yes',
        'FALSE'=>'No'
    ),
    'display'     => TRUE,
    'readonly'    => FALSE
);
