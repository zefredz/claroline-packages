<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.5.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLQPOLL';
$conf_def['config_file'] = 'CLQPOLL.conf.php';
$conf_def['config_name'] = 'Quick poll for Claroline';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'CLQPOLL_pagerLineNb' );

$conf_def_property_list[ 'CLQPOLL_pagerLineNb' ] = 
 array ( 'label'       => 'Lines numbers per page in result'
       , 'description' => ''
       , 'default'     => '10'
       , 'type'        => 'integer'
        ,'acceptedValue' => array ( 'min'=> 5
                                  , 'max'=> 50)
       );