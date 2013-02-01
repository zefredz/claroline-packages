<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'UCREPORT';
$conf_def['config_file'] = 'UCREPORT.conf.php';
$conf_def['config_name'] = 'Student Report';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'UCREPORT_public_allowed' );

$conf_def_property_list[ 'UCREPORT_public_allowed' ] = array (
          'label'       => get_lang( 'Public report allowed' )
        , 'description' => get_lang( 'Course managers have the right to publish reports where students can see each other scores?' )
        , 'type'        => 'boolean'
        , 'display'     => TRUE
        , 'readonly'    => FALSE
        , 'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
        , 'default'     => FALSE
       );