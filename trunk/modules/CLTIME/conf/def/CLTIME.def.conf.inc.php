<?php // $Id$
/**
 * Server Time
 *
 * @version     CLTIME-1.0alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLTIME
 * @author      Frédéric Minne <frederic.minne@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLTIME';
$conf_def['config_file'] = 'CLTIME.conf.php';
$conf_def['config_name'] = 'Server time';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= 'Displays the time on the server';
$conf_def['section']['main']['properties'] = array ( 'refreshTime' );

$conf_def_property_list[ 'refreshTime' ] = 
 array ( 'label'       => 'Refresh time'
       , 'description' => 'Refresh rate which the server time is displayed'
       , 'default'     => '10'
       , 'unit'        => 'seconds'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 60)
       );