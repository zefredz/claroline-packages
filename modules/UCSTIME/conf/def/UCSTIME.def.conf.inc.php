<?php // $Id$

/**
 * Server Time?
 *
 * @version     UCSTIME 1.1.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
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
$conf_def['section']['main']['properties'] = array ( 'displaySeconds' );

$conf_def_property_list[ 'displaySeconds' ] = 
 array ( 'label'       => 'Displays seconds'
       , 'description' => 'Do you want seconds to be shown?'
       , 'default'     => FALSE
       , 'type'        => 'boolean'
       ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
       );
