<?php // $Id$

/**
 * This file describe the configuration for ICTERMS module
 *
 * @version 1.0 $Revision$
 *
 * @copyright 2001-2010 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author Claro Team <cvs@claroline.net>
 * @package icterms
 */

$conf_def['config_file'] = 'ICTERMS.conf.php';
$conf_def['config_code'] = 'ICTERMS';
$conf_def['config_name'] = 'Terms Of Use Module';
$conf_def['config_class'] = 'tool';


$conf_def['section']['main']['label']='Main Settings';
$conf_def['section']['main']['properties'] =
array (
    'icterms_forceTermsAcceptance',
    'icterms_useAccountCreationAgreement'
);

$conf_def_property_list['icterms_forceTermsAcceptance'] = array (
    'label'         => 'Force user to accept terms of use on login',
    'default'       => TRUE,
    'type'          => 'boolean',
    'display'       => TRUE,
    'readonly'      => FALSE,
    'acceptedValue' => array (
        'TRUE' => 'Yes',
        'FALSE' => 'No'
    )
);
