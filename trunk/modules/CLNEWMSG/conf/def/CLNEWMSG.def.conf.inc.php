<?php // $Id$
/**
 * New message notifier
 *
 * @version     CLNEWMSG 0.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLNEWMSG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLNEWMSG';
$conf_def['config_file'] = 'CLNEWMSG.conf.php';
$conf_def['config_name'] = 'New message notifier';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'refreshTime' , 'displayTime' );

$conf_def_property_list[ 'refreshTime' ] = 
 array ( 'label'       => 'Refresh time'
       , 'description' => 'Sets the frequency which the system seeks for new messages'
       , 'default'     => '10'
       , 'unit'        => 'seconds'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 3600)
       );

$conf_def_property_list[ 'displayTime' ] = 
 array ( 'label'       => 'Popup display time'
       , 'description' => 'Sets the time within the notifying popup is displayed'
       , 'default'     => '20'
       , 'unit'        => 'seconds'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 1
                                  , 'max'=> 60)
       );

