<?php
// CONFIG HEADER

$conf_def['config_code'] = 'LPUTRACK';
$conf_def['config_file'] = 'LPUTRACK.conf.php';
$conf_def['config_name'] = 'Learnpath user tracking export';

// CONFIG SECTIONS
$conf_def['section']['main']['label'] = 'Main';
$conf_def['section']['main']['description'] = '';
$conf_def['section']['main']['properties'] = array (
        'LPUTRACK_display_progress',
        'LPUTRACK_display_progress_widget'
);

// CONFIG PROPERTIES
$conf_def_property_list['LPUTRACK_display_progress'] = array (
    'label' => 'Display progress',
    'description' => '',
    'default'     => TRUE,
    'type'        => 'boolean',
    'acceptedValue' => array (
        'TRUE'=>'Yes',
        'FALSE'=>'No'
    ),
    'display'     => TRUE,
    'readonly'    => FALSE
);
$conf_def_property_list['LPUTRACK_display_progress_widget'] = array (
    'label' => 'Display progress in Widget',
    'description' => '',
    'default'     => TRUE,
    'type'        => 'boolean',
    'acceptedValue' => array (
        'TRUE'=>'Yes',
        'FALSE'=>'No'
    ),
    'display'     => TRUE,
    'readonly'    => FALSE
);