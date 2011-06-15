<?php // $Id$

/**
 * Server Time
 *
 * @version     UCSTIME 1.1.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCSTIME
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'UCSTIME';
$conf_def['config_file'] = 'UCSTIME.conf.php';
$conf_def['config_name'] = 'Server Time';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'UCSTIME_displaySeconds' , 'UCSTIME_displayDate' );

$conf_def_property_list[ 'UCSTIME_displaySeconds' ] = 
 array ( 'label'       => 'Displays seconds'
       , 'description' => 'Do you want seconds to be shown?'
       , 'default'     => FALSE
       , 'type'        => 'boolean'
       ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
       );

$conf_def_property_list[ 'UCSTIME_displayDate' ] = 
 array ( 'label'       => 'Displays date'
       , 'description' => 'Do you want the date to be shown?'
       , 'default'     => FALSE
       , 'type'        => 'boolean'
       ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
       );
