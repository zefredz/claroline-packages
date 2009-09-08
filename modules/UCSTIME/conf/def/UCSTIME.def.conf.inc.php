<?php // $Id$

/**
 * Server Time?
 *
 * @version     UCSTIME 0.1 $Revision$ - Claroline 1.9
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
$conf_def['section']['main']['properties'] = array ( 'refreshTime' , 'displaySeconds' );

$conf_def_property_list[ 'refreshTime' ] = 
 array ( 'label'       => 'Refresh time'
       , 'description' => 'Sets the frequency which the client synchronizes with the server'
       , 'default'     => '30'
       , 'unit'        => 'minutes'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 60)
       );

$conf_def_property_list[ 'displaySeconds' ] = 
 array ( 'label'       => 'Displays seconds'
       , 'description' => 'Do you want seconds to be shown?'
       , 'default'     => FALSE
       , 'type'        => 'boolean'
       ,'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
       );
