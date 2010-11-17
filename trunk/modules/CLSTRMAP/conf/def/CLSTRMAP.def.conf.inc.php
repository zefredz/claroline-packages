<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLSTRMAP 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSTRMAP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLSTRMAP';
$conf_def['config_file'] = 'CLSTRMAP.conf.php';
$conf_def['config_name'] = 'ClustrMaps';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'clustrMapsUrl' );

$conf_def_property_list[ 'clustrMapsUrl' ] = 
 array ( 'label'       => 'Url on which the site is registered'
       , 'description' => ''
       , 'default'     => get_path( 'rootWeb' )
       , 'type'        => 'string'
       );